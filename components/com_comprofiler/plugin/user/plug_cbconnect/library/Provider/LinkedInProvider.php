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

class LinkedInProvider extends Provider
{
	/**
	 * https://developer.linkedin.com/docs/oauth2#permissions
	 *
	 * @var string
	 */
	protected $scope			=	'r_basicprofile r_emailaddress';
	/** @var string  */
	protected $scopeSeparator	=	' ';
	/** @var string  */
	protected $fields			=	'id,first-name,last-name,formatted-name,email-address,picture-urls::(original)';
	/** @var array  */
	protected $urls				=	array(	'base'		=>	'https://api.linkedin.com/v1',
											'authorize'	=>	'https://www.linkedin.com/uas/oauth2/authorization',
											'access'	=>	'https://www.linkedin.com/uas/oauth2/accessToken'
										);

	/**
	 * Authenticates a LinkedIn user (redirect and token exchange)
	 * https://developer.linkedin.com/docs/oauth2
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'linkedin.state' ) ) || ( $this->session()->get( 'linkedin.state' ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'linkedin.code', $code );

			$client				=	new Client();

			$options			=	array(	'body'	=>	array(	'grant_type'	=>	'authorization_code',
																'code'			=>	$code,
																'redirect_uri'	=>	$this->callback,
																'client_id'		=>	$this->clientId,
																'client_secret'	=>	$this->clientSecret
															)
									);

			try {
				$result			=	$client->post( $this->urls['access'], $options );
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
				$this->session()->set( 'linkedin.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'linkedin.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'linkedin.state', $state );

			$url				=	$this->urls['authorize']
								.	'?response_type=code'
								.	'&client_id=' . urlencode( $this->clientId )
								.	'&redirect_uri=' . urlencode( $this->callback )
								.	'&state=' . urlencode( $state )
								.	( $this->scope ? '&scope=' . urlencode( $this->scope ) : null );

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

		if ( $this->session()->get( 'linkedin.access_token' ) ) {
			$expires		=	$this->session()->get( 'linkedin.expires' );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users LinkedIn profile
	 * https://developer.linkedin.com/docs/fields/basic-profile
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

		$response				=	$this->api( '/people/~' . ( $fields ? ':(' . $fields . ')' : null ) . '?format=json' );

		if ( $response instanceof Registry ) {
			$fieldMap			=	array(	'id'			=>	'id',
											'name'			=>	'formattedName',
											'firstname'		=>	'firstName',
											'lastname'		=>	'lastName',
											'email'			=>	'emailAddress',
											'avatar'		=>	'pictureUrls.values.0'
										);

			foreach ( $fieldMap as $cbField => $fbField ) {
				$profile->set( $cbField, $response->get( $fbField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id' ) ) {
				$this->session()->set( 'linkedin.id', $profile->get( 'id' ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom LinkedIn API request
	 * https://developer.linkedin.com/docs/rest-api
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

		if ( $this->session()->get( 'linkedin.access_token' ) ) {
			$headers['Authorization']	=	'Bearer ' . $this->session()->get( 'linkedin.access_token' );
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
