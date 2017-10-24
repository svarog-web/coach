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

class GitHubProvider extends Provider
{
	/**
	 * https://developer.github.com/v3/oauth/#scopes
	 *
	 * @var string
	 */
	protected $scope		=	'user';
	/** @var array  */
	protected $urls			=	array(	'base'		=>	'https://api.github.com',
										'authorize'	=>	'https://github.com/login/oauth/authorize',
										'access'	=>	'https://github.com/login/oauth/access_token'
									);

	/**
	 * Authenticates a GitHub user (redirect and token exchange)
	 * https://developer.github.com/v3/oauth/
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'github.state' ) ) || ( $this->session()->get( 'github.state' ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'github.code', $code );

			$client				=	new Client();

			$options			=	array(	'headers'	=>	array(	'Accept'		=>	'application/json' ),
											'body'		=>	array(	'client_id'		=>	$this->clientId,
																	'client_secret'	=>	$this->clientSecret,
																	'code'			=>	$code,
																	'redirect_uri'	=>	$this->callback,
																	'state'			=>	$this->session()->get( 'github.state' )
																)
									);

			try {
				$result			=	$client->post( $this->urls['access'], $options );
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

			if ( $response instanceof Registry ) {
				if ( $response->get( 'access_token' ) ) {
					$this->session()->set( 'github.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				} elseif ( $response->get( 'error_description' ) ) {
					throw new Exception( CBTxt::T( 'FAILED_EXCHANGE_CODE_ERROR', 'Failed to exchange code. Error: [error]', array( '[error]' => $response->get( 'error_description' ) ) ) );
				} else {
					throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
				}
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'github.state', $state );

			$url				=	$this->urls['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
								.	'&redirect_uri=' . urlencode( $this->callback )
								.	( $this->scope ? '&scope=' . urlencode( $this->scope ) : null )
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
		return ( $this->session()->get( 'github.access_token' ) != '' );
	}

	/**
	 * Request current users GitHub profile
	 * https://developer.github.com/v3/users/
	 *
	 * @param null|string|array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = null )
	{
		$profile				=	new Profile();

		$response				=	$this->api( '/user' );

		if ( $response instanceof Registry ) {
			$fieldMap			=	array(	'id'			=>	'id',
											'username'		=>	'login',
											'name'			=>	'name',
											'email'			=>	'email',
											'avatar'		=>	'avatar_url'
										);

			foreach ( $fieldMap as $cbField => $fbField ) {
				$profile->set( $cbField, $response->get( $fbField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id' ) ) {
				$this->session()->set( 'github.id', $profile->get( 'id' ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom GitHub API request
	 * https://developer.github.com/v3/
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

		if ( $this->session()->get( 'github.access_token' ) ) {
			$params['access_token']		=	$this->session()->get( 'github.access_token' );
		}

		$options						=	array();

		$headers['Accept']				=	'application/json';

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

			if ( ( $response instanceof Registry ) && $response->get( 'meta.error_message' ) ) {
				$error					=	CBTxt::T( 'FAILED_API_REQUEST_ERROR', 'Failed API request [api]. Error: [error]', array( '[api]' => $api, '[error]' => $response->get( 'meta.error_message' ) ) );
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
