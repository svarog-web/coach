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

class FacebookProvider extends Provider
{
	/**
	 * https://developers.facebook.com/docs/facebook-login/permissions
	 *
	 * @var string
	 */
	protected $scope		=	'email,public_profile';
	/** @var string  */
	protected $fields		=	'id,name,first_name,middle_name,last_name,email,cover';
	/** @var array  */
	protected $urls			=	array(	'base'		=>	'https://graph.facebook.com/v2.5',
										'authorize'	=>	'https://www.facebook.com/dialog/oauth',
										'access'	=>	'https://graph.facebook.com/v2.3/oauth/access_token'
									);

	/**
	 * Authenticates a Facebook user (redirect and token exchange)
	 * https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'facebook.state' ) ) || ( $this->session()->get( 'facebook.state' ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'facebook.code', $code );

			$client				=	new Client();

			$options			=	array(	'query'	=>	array(	'client_id'		=>	$this->clientId,
																'redirect_uri'	=>	$this->callback,
																'client_secret'	=>	$this->clientSecret,
																'code'			=>	$code
															)
									);

			try {
				$result			=	$client->get( $this->urls['access'], $options );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof Registry ) && $response->get( 'error.message' ) ) {
					$error		=	CBTxt::T( 'FAILED_EXCHANGE_CODE_ERROR', 'Failed to exchange code. Error: [error]', array( '[error]' => $response->get( 'error.message' ) ) );
				} else {
					$error		=	$e->getMessage();
				}

				$this->debug( $e );

				throw new Exception( $error );
			}

			$response			=	$this->response( $result );

			$this->debug( $result, $response );

			if ( ( $response instanceof Registry ) && $response->get( 'access_token' ) ) {
				$this->session()->set( 'facebook.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'facebook.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'facebook.state', $state );

			$url				=	$this->urls['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
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
		$expired			=	true;

		if ( $this->session()->get( 'facebook.access_token' ) ) {
			$expires		=	$this->session()->get( 'facebook.expires' );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users Facebook profile
	 * https://developers.facebook.com/docs/graph-api/reference/user/
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
			$fieldMap			=	array(	'id'			=>	'id',
											'name'			=>	'name',
											'firstname'		=>	'first_name',
											'middlename'	=>	'middle_name',
											'lastname'		=>	'last_name',
											'email'			=>	'email',
											'canvas'		=>	'cover.source'
										);

			foreach ( $fieldMap as $cbField => $fbField ) {
				$profile->set( $cbField, $response->get( $fbField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id' ) ) {
				$this->session()->set( 'facebook.id', $profile->get( 'id' ) );

				$profile->set( 'avatar', $this->urls['base'] . '/' . $profile->get( 'id' ) . '/picture?height=800&width=800&type=large' );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Facebook API request
	 * https://developers.facebook.com/docs/graph-api/reference
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

		if ( $this->session()->get( 'facebook.access_token' ) ) {
			$params['access_token']		=	$this->session()->get( 'facebook.access_token' );
			$params['appsecret_proof']	=	hash_hmac( 'sha256', $params['access_token'], $this->clientSecret );
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

			if ( ( $response instanceof Registry ) && $response->get( 'error.message' ) ) {
				$error					=	CBTxt::T( 'FAILED_API_REQUEST_ERROR', 'Failed API request [api]. Error: [error]', array( '[api]' => $api, '[error]' => $response->get( 'error.message' ) ) );
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
