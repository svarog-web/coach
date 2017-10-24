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

class PayPalProvider extends Provider
{
	/**
	 * https://developer.paypal.com/docs/integration/direct/identity/attributes/
	 *
	 * @var string
	 */
	protected $scope			=	'openid profile email';
	/** @var string  */
	protected $scopeSeparator	=	' ';
	/** @var bool  */
	protected $sandbox			=	0;
	/** @var array  */
	protected $urls				=	array(	array(	'base'		=>	'https://api.paypal.com/v1',
													'authorize'	=>	'https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize',
													'access'	=>	'https://api.paypal.com/v1/identity/openidconnect/tokenservice'
											),
											array(	'base'		=>	'https://api.sandbox.paypal.com/v1',
													'authorize'	=>	'https://www.sandbox.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize',
													'access'	=>	'https://api.sandbox.paypal.com/v1/identity/openidconnect/tokenservice'
											)
										);

	/**
	 * Authenticates a PayPal user (redirect and token exchange)
	 * https://developer.paypal.com/docs/api/#obtain-users-consent
	 * https://developer.paypal.com/docs/api/#grant-token-from-authorization-code
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'paypal.state' ) ) || ( $this->session()->get( 'paypal.state' ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'paypal.code', $code );

			$client				=	new Client( array( 'defaults' => array( 'auth' => array( $this->clientId, $this->clientSecret ) ) ) );

			$options			=	array(	'body'	=>	array(	'grant_type'	=>	'authorization_code',
																'code'			=>	$code,
																'redirect_uri'	=>	$this->callback
															)
									);

			try {
				$result			=	$client->post( $this->urls[$this->sandbox]['access'], $options );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof Registry ) && $response->get( 'error_description' ) ) {
					$error		=	CBTxt::T( 'FAILED_EXCHANGE_CODE_ERROR', 'Failed to exchange code. Error: [error]', array( '[error]' => $response->get( 'error_description' ) ) );
				} else {
					$error		=	$e->getMessage();
				}

				$this->debug( $e );

				throw new Exception( $error );
			}

			$response			=	$this->response( $result );

			$this->debug( $result, $response );

			if ( ( $response instanceof Registry ) && $response->get( 'access_token' ) ) {
				$this->session()->set( 'paypal.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'paypal.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'paypal.state', $state );

			$url				=	$this->urls[$this->sandbox]['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
								.	'&response_type=code'
								.	( $this->scope ? '&scope=' . urlencode( $this->scope ) : null )
								.	'&redirect_uri=' . urlencode( $this->callback )
								.	'&nonce=' . urlencode( sha1( uniqid( '', true ) . $this->urls[$this->sandbox]['authorize'] ) )
								.	'&state=' . urlencode( $state );

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

		if ( $this->session()->get( 'paypal.access_token' ) ) {
			$expires		=	$this->session()->get( 'paypal.expires' );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users PayPal profile
	 * https://developer.paypal.com/docs/rest/api/identity/#openidconnect
	 *
	 * @param null|string|array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = null )
	{
		$profile				=	new Profile();

		$response				=	$this->api( '/identity/openidconnect/userinfo', 'GET', array( 'schema' => 'openid' ) );

		if ( $response instanceof Registry ) {
			$fieldMap			=	array(	'name'			=>	'name',
											'firstname'		=>	'given_name',
											'middlename'	=>	'middle_name',
											'lastname'		=>	'family_name',
											'email'			=>	'email',
											'avatar'		=>	'picture'
										);

			foreach ( $fieldMap as $cbField => $fbField ) {
				$profile->set( $cbField, $response->get( $fbField, null, GetterInterface::STRING ) );
			}

			if ( $response->get( 'user_id' ) ) {
				$profile->set( 'id', preg_replace( '%^.+/(.+)%', '$1', $response->get( 'user_id', null, GetterInterface::STRING ) ) );
			}

			if ( $profile->get( 'id' ) ) {
				$this->session()->set( 'paypal.id', $profile->get( 'id' ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom PayPal API request
	 * https://developer.paypal.com/docs/api/overview/
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

		if ( $this->session()->get( 'paypal.access_token' ) ) {
			$headers['Authorization']	=	'Bearer ' . $this->session()->get( 'paypal.access_token' );
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
				$result					=	$client->post( $this->urls[$this->sandbox]['base'] . $api, $options );
			} else {
				$result					=	$client->get( $this->urls[$this->sandbox]['base'] . $api, $options );
			}
		} catch( ClientException $e ) {
			$response					=	$this->response( $e->getResponse() );

			if ( ( $response instanceof Registry ) && $response->get( 'error_description' ) ) {
				$error					=	CBTxt::T( 'FAILED_API_REQUEST_ERROR', 'Failed API request [api]. Error: [error]', array( '[api]' => $api, '[error]' => $response->get( 'error_description' ) ) );
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
