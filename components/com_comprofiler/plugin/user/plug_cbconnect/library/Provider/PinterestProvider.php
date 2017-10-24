<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Connect\Provider;

use CBLib\Application\Application;
use CB\Plugin\Connect\Provider;
use CB\Plugin\Connect\Profile;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Xml\SimpleXMLElement;
use GuzzleHttp\Client;
use CBLib\Language\CBTxt;
use GuzzleHttp\Exception\ClientException;
use Exception;

defined('CBLIB') or die();

class PinterestProvider extends Provider
{
	/**
	 * https://developers.pinterest.com/docs/api/overview/#permission-scopes
	 *
	 * @var string
	 */
	protected $scope		=	'read_public';
	/** @var string  */
	protected $fields		=	'id,username,first_name,last_name,image';
	/** @var array  */
	protected $urls			=	array(	'base'		=>	'https://api.pinterest.com/v1',
										'authorize'	=>	'https://api.pinterest.com/oauth/',
										'access'	=>	'https://api.pinterest.com/v1/oauth/token'
									);

	/**
	 * Authenticates a Pinterest user (redirect and token exchange)
	 * https://developers.pinterest.com/docs/api/overview/#authentication
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'pinterest.state' ) ) || ( $this->session()->get( 'pinterest.state' ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'pinterest.code', $code );

			$client				=	new Client();

			$options			=	array(	'body'	=>	array(	'grant_type'	=>	'authorization_code',
																'client_id'		=>	$this->clientId,
																'client_secret'	=>	$this->clientSecret,
																'code'			=>	$code
															)
									);

			try {
				$result			=	$client->post( $this->urls['access'], $options );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof Registry ) && $response->get( 'error' ) ) {
					$error		=	CBTxt::T( 'FAILED_EXCHANGE_CODE_ERROR', 'Failed to exchange code. Error: [error]', array( '[error]' => $response->get( 'error' ) ) );
				} else {
					$error		=	$e->getMessage();
				}

				$this->debug( $e );

				throw new Exception( $error );
			}

			$response			=	$this->response( $result );

			$this->debug( $result, $response );

			if ( ( $response instanceof Registry ) && $response->get( 'access_token' ) ) {
				$this->session()->set( 'pinterest.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'pinterest.state', $state );

			$url				=	$this->urls['authorize']
								.	'?response_type=code'
								.	'&client_id=' . urlencode( $this->clientId )
								.	'&state=' . urlencode( $state )
								.	( $this->scope ? '&scope=' . urlencode( $this->scope ) : null )
								.	'&redirect_uri=' . urlencode( $this->callback );

			cbRedirect( $url );
		}
	}

	/**
	 * Checks if access token exists and ensures it's not expired
	 *
	 * @return bool
	 */
	public function authorized()
	{
		return ( $this->session()->get( 'pinterest.access_token' ) != '' );
	}

	/**
	 * Request current users Pinterest profile
	 * https://developers.pinterest.com/docs/api/users/
	 *
	 * @param null|string|array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = null )
	{
		$profile				=	new Profile();

		if ( ! $fields ) {
			$fields				=	$this->fields;
		} else {
			if ( is_array( $fields ) ) {
				$fields			=	implode( $this->fieldsSeparator, $fields );
			}
		}

		$params					=	array();

		if ( $fields ) {
			$params['fields']	=	$fields;
		}

		$response				=	$this->api( '/me', 'GET', $params );

		if ( $response instanceof Registry ) {
			$response			=	$response->subTree( 'data' );

			$fieldMap			=	array(	'id'			=>	'id',
											'username'		=>	'username',
											'firstname'		=>	'first_name',
											'lastname'		=>	'last_name'
										);

			foreach ( $fieldMap as $cbField => $fbField ) {
				$profile->set( $cbField, $response->get( $fbField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id' ) ) {
				$this->session()->set( 'pinterest.id', $profile->get( 'id' ) );
			}

			if ( $response->get( 'image' ) ) {
				foreach ( $response->subTree( 'image' ) as $image ) {
					/** @var Registry $image */
					$profile->set( 'avatar', str_replace( '_' . $image->get( 'width', 0, GetterInterface::INT ) . '.', '.', $image->get( 'url', null, GetterInterface::STRING ) ) );
					break;
				}
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Pinterest API request
	 * https://developers.pinterest.com/docs/api/overview/
	 *
	 * @param string $api
	 * @param string $type
	 * @param array  $params
	 * @param array  $headers
	 * @return string|Registry|SimpleXMLElement
	 * @throws Exception
	 */
	public function api( $api, $type = 'GET', $params = array(), $headers = array() )
	{
		$client							=	new Client();

		if ( $this->session()->get( 'pinterest.access_token' ) ) {
			$params['access_token']		=	$this->session()->get( 'pinterest.access_token' );
		}

		$options						=	array();

		if ( $headers ) {
			$options['headers']			=	$headers;
		}

		if ( $params ) {
			if ( $type == 'POST' ) {
				$options['body']		=	$params;
			} else {
				$options['query']		=	$params;
			}
		}

		try {
			if ( $type == 'POST' ) {
				$result					=	$client->post( $this->urls['base'] . $api, $options );
			} else {
				$result					=	$client->get( $this->urls['base'] . $api, $options );
			}
		} catch( ClientException $e ) {
			$response					=	$this->response( $e->getResponse() );

			if ( ( $response instanceof Registry ) && $response->get( 'message' ) ) {
				$error					=	CBTxt::T( 'FAILED_API_REQUEST_ERROR', 'Failed API request [api]. Error: [error]', array( '[api]' => $api, '[error]' => $response->get( 'message' ) ) );
			} else {
				$error					=	$e->getMessage();
			}

			$this->debug( $e );

			throw new Exception( $error );
		}

		$response						=	$this->response( $result );

		$this->debug( $result, $response );

		return $response;
	}
}
