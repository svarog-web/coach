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

class SteamProvider extends Provider
{
	/** @var string  */
	protected $key			=	null;
	/** @var array  */
	protected $urls			=	array(	'base'			=>	'http://api.steampowered.com',
										'identifier'	=>	'http://steamcommunity.com/openid'
									);

	/**
	 * Authenticates a Steam user (redirect and token exchange)
	 * https://openid.net/specs/openid-authentication-2_0.html
	 * http://forums.steampowered.com/forums/showthread.php?t=1430511
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		global $_CB_framework;

		if ( Application::Input()->get( 'openid_mode', null, GetterInterface::STRING ) ) {
			$client				=	new Client();

			$params				=	array(	'openid.mode'			=>	'check_authentication',
											'openid.assoc_handle'	=>	Application::Input()->get( 'openid_assoc_handle', null, GetterInterface::STRING ),
											'openid.signed'			=>	Application::Input()->get( 'openid_signed', null, GetterInterface::STRING ),
											'openid.sig'			=>	Application::Input()->get( 'openid_sig', null, GetterInterface::STRING ),
											'openid.ns'				=>	'http://specs.openid.net/auth/2.0'
										);

			foreach ( explode( ',', Application::Input()->get( 'openid_signed', null, GetterInterface::STRING ) ) as $signed ) {
				$params['openid.' . $signed]	=	Application::Input()->get( 'openid_' . $signed, null, GetterInterface::STRING );
			}

			try {
				$result			=	$client->post( $this->urls['identifier'] . '/login', array( 'body' => $params ) );
			} catch( ClientException $e ) {
				$this->debug( $e );

				throw new Exception( $e->getMessage() );
			}

			$response			=	(string) $result->getBody();

			$this->debug( $result, $response );

			if ( preg_match( '/is_valid\s*:\s*true/i', $response ) ) {
				$this->session()->set( 'steam.id', str_replace( $this->urls['identifier'] . '/id/', '', Application::Input()->get( 'openid_claimed_id', null, GetterInterface::STRING ) ) );
			} else {
				throw new Exception( CBTxt::T( 'Failed to authenticate identity.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'steam.state', $state );

			$url				=	$this->urls['identifier'] . '/login'
								.	'?openid.ns=' . urlencode( 'http://specs.openid.net/auth/2.0' )
								.	'&openid.mode=checkid_setup'
								.	'&openid.return_to=' . urlencode( $this->callback )
								.	'&openid.realm=' . urlencode( $_CB_framework->getCfg( 'live_site' ) )
								.	'&openid.identity=' . urlencode( 'http://specs.openid.net/auth/2.0/identifier_select' )
								.	'&openid.claimed_id=' . urlencode( 'http://specs.openid.net/auth/2.0/identifier_select' );

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
		return ( $this->session()->get( 'steam.id' ) != '' );
	}

	/**
	 * Request current users Steam profile
	 * https://developer.valvesoftware.com/wiki/Steam_Web_API#GetPlayerSummaries_.28v0001.29
	 *
	 * @param null|string|array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = null )
	{
		$profile				=	new Profile();
		$params					=	array();

		if ( $this->session()->get( 'steam.id' ) ) {
			$params['steamids']	=	$this->session()->get( 'steam.id' );
		}

		$response				=	$this->api( '/ISteamUser/GetPlayerSummaries/v0002', 'GET', $params );

		if ( $response instanceof Registry ) {
			$response			=	$response->subTree( 'response.players.0' );

			$fieldMap			=	array(	'id'			=>	'steamid',
											'username'		=>	'personaname',
											'name'			=>	'realname',
											'avatar'		=>	'avatarfull'
										);

			foreach ( $fieldMap as $cbField => $fbField ) {
				$profile->set( $cbField, $response->get( $fbField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id' ) ) {
				$this->session()->set( 'steam.id', $profile->get( 'id' ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Steam API request
	 * https://developer.valvesoftware.com/wiki/Steam_Web_API
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

		if ( $this->key ) {
			$params['key']				=	$this->key;
		}

		$params['format']				=	'json';

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
