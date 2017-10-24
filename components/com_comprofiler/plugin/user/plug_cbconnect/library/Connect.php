<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Connect;

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;

defined('CBLIB') or die();

class Connect
{
	/** @var string  */
	public $id					=	null;
	/** @var array  */
	public $providers			=	array();

	/**
	 * Connect constructor.
	 *
	 * @param string $providerId
	 */
	public function __construct( $providerId = null )
	{
		$this->providers	=	CBConnect::getProviders();

		if ( isset( $this->providers[$providerId] ) ) {
			$this->id		=	$providerId;
		}
	}

	/**
	 * Returns a provider object
	 *
	 * @return Provider|null
	 */
	public function provider()
	{
		$return						=	false;

		if ( ( ! $this->id ) || ( ! isset( $this->providers[$this->id] ) ) || ( ! $this->params()->get( 'enabled', false, GetterInterface::BOOLEAN ) ) ) {
			return $return;
		}

		$permissions				=	array();
		$fields						=	array();
		$clientId					=	$this->params()->get( 'client_id', null, GetterInterface::STRING );
		$clientSecret				=	$this->params()->get( 'client_secret', null, GetterInterface::STRING );
		$provider					=	null;

		switch ( $this->id ) {
			case 'facebook':
				$permissions		=	array( 'public_profile', 'email' );
				$fields				=	array( 'id', 'name', 'first_name', 'middle_name', 'last_name', 'cover', 'email' );
				$clientId			=	$this->params()->get( 'application_id', null, GetterInterface::STRING );
				$clientSecret		=	$this->params()->get( 'application_secret', null, GetterInterface::STRING );
				$provider			=	'\CB\Plugin\Connect\Provider\FacebookProvider';
				break;
			case 'twitter':
				$clientId			=	$this->params()->get( 'consumer_key', null, GetterInterface::STRING );
				$clientSecret		=	$this->params()->get( 'consumer_secret', null, GetterInterface::STRING );
				$provider			=	'\CB\Plugin\Connect\Provider\TwitterProvider';
				break;
			case 'google':
				$permissions		=	array( 'profile', 'email' );
				$fields				=	array( 'id', 'displayName', 'nickname', 'image', 'cover', 'emails' );
				$provider			=	'\CB\Plugin\Connect\Provider\GoogleProvider';
				break;
			case 'linkedin':
				$permissions		=	array( 'r_basicprofile', 'r_emailaddress' );
				$fields				=	array( 'id', 'first-name', 'last-name', 'formatted-name', 'email-address', 'picture-urls::(original)' );
				$clientId			=	$this->params()->get( 'api_key', null, GetterInterface::STRING );
				$clientSecret		=	$this->params()->get( 'secret_key', null, GetterInterface::STRING );
				$provider			=	'\CB\Plugin\Connect\Provider\LinkedInProvider';
				break;
			case 'windowslive':
				$permissions		=	array( 'wl.basic', 'wl.emails', 'wl.signin' );
				$provider			=	'\CB\Plugin\Connect\Provider\WindowsLiveProvider';
				break;
			case 'instagram':
				$permissions		=	array( 'basic' );
				$provider			=	'\CB\Plugin\Connect\Provider\InstagramProvider';
				break;
			case 'foursquare':
				$provider			=	'\CB\Plugin\Connect\Provider\FoursquareProvider';
				break;
			case 'github':
				$permissions		=	array( 'user' );
				$provider			=	'\CB\Plugin\Connect\Provider\GitHubProvider';
				break;
			case 'vkontakte':
				$permissions		=	array( 'email' );
				$fields				=	array( 'uid', 'nickname', 'first_name', 'last_name', 'photo_max_orig' );
				$clientId			=	$this->params()->get( 'application_id', null, GetterInterface::STRING );
				$clientSecret		=	$this->params()->get( 'secret_key', null, GetterInterface::STRING );
				$provider			=	'\CB\Plugin\Connect\Provider\VkontakteProvider';
				break;
			case 'steam':
				$provider			=	'\CB\Plugin\Connect\Provider\SteamProvider';
				break;
			case 'reddit':
				$permissions		=	array( 'identity' );
				$provider			=	'\CB\Plugin\Connect\Provider\RedditProvider';
				break;
			case 'twitch':
				$permissions		=	array( 'user_read' );
				$provider			=	'\CB\Plugin\Connect\Provider\TwitchProvider';
				break;
			case 'stackexchange':
				$provider			=	'\CB\Plugin\Connect\Provider\StackExchangeProvider';
				break;
			case 'pinterest':
				$permissions		=	array( 'read_public' );
				$fields				=	array( 'id', 'username', 'first_name', 'last_name', 'image' );
				$provider			=	'\CB\Plugin\Connect\Provider\PinterestProvider';
				break;
			case 'amazon':
				$permissions		=	array( 'profile' );
				$provider			=	'\CB\Plugin\Connect\Provider\AmazonProvider';
				break;
			case 'yahoo':
				$provider			=	'\CB\Plugin\Connect\Provider\YahooProvider';
				break;
			case 'paypal':
				$permissions		=	array( 'openid', 'profile', 'email' );
				$provider			=	'\CB\Plugin\Connect\Provider\PayPalProvider';
				break;
			case 'disqus':
				$permissions		=	array( 'read', 'email' );
				$provider			=	'\CB\Plugin\Connect\Provider\DisqusProvider';
				break;
			case 'wordpress':
				$permissions		=	array( 'auth' );
				$fields				=	array( 'ID', 'display_name', 'username', 'email', 'avatar_URL' );
				$provider			=	'\CB\Plugin\Connect\Provider\WordPressProvider';
				break;
			case 'meetup':
				$permissions		=	array( 'basic' );
				$fields				=	array( 'id', 'name', 'photo' );
				$provider			=	'\CB\Plugin\Connect\Provider\MeetupProvider';
				break;
		}

		if ( ! $provider ) {
			return false;
		}

		if ( $permissions !== false ) {
			$allowedPermissions		=	$this->providers[$this->id]['permissions'];

			foreach ( explode( '|*|', $this->params()->get( 'permissions' ) ) as $permission ) {
				if ( ( ! $permission ) || in_array( $permission, $permissions ) || ( ! in_array( $permission, $allowedPermissions ) ) ) {
					continue;
				}

				$permissions[]		=	$permission;
			}
		}

		if ( $fields !== false ) {
			$allowedFields			=	$this->providers[$this->id]['fields'];

			foreach ( $this->params()->subTree( 'fields' ) as $field ) {
				/** @var ParamsInterface $field */
				$fromField			=	$field->get( 'from', null, GetterInterface::STRING );
				$toField			=	$field->get( 'to', null, GetterInterface::STRING );

				if ( ( ! $fromField ) || ( ! $toField ) || ( ! in_array( $fromField, $allowedFields ) ) ) {
					continue;
				}

				$fromField			=	explode( '.', $fromField );

				if ( ! $fromField ) {
					continue;
				}

				$fromField			=	$fromField[0];

				if ( in_array( $fromField, $fields ) ) {
					continue;
				}

				$fields[]			=	$fromField;
			}
		}

		$provider					=	new $provider( $clientId, $clientSecret, $this->callback(), $permissions, $fields, $this->params()->get( 'debug', 0, GetterInterface::INT ) );

		/** @var Provider $provider */
		switch ( $this->id ) {
			case 'steam':
				$provider->set( 'key', $this->params()->get( 'api_key', null, GetterInterface::STRING ) );
				break;
			case 'stackexchange':
				$provider->set( 'key', $this->params()->get( 'key', null, GetterInterface::STRING ) );
				break;
			case 'paypal':
				$provider->set( 'sandbox', $this->params()->get( 'sandbox', 0, GetterInterface::INT ) );
				break;
		}

		return $provider;
	}

	/**
	 * Returns the provider callback url
	 *
	 * @return string|null
	 */
	public function callback()
	{
		global $_CB_framework;

		if ( ( ! $this->id ) || ( ! isset( $this->providers[$this->id] ) ) ) {
			return null;
		}

		$liveSite		=	$_CB_framework->getCfg( 'live_site' );

		if ( $this->providers[$this->id]['ssl'] ) {
			$liveSite	=	str_replace( 'http://', 'https://', $liveSite );
		}

		return $liveSite . '/components/com_comprofiler/plugin/user/plug_cbconnect/endpoint.php?provider=' . urlencode( $this->id );
	}

	/**
	 * Returns a translated user friendly provider name
	 *
	 * @return string|null
	 */
	public function name()
	{
		if ( ( ! $this->id ) || ( ! isset( $this->providers[$this->id] ) ) ) {
			return null;
		}

		return $this->providers[$this->id]['name'];
	}

	/**
	 * Returns a provider field name
	 *
	 * @return string|null
	 */
	public function field()
	{
		if ( ( ! $this->id ) || ( ! isset( $this->providers[$this->id] ) ) ) {
			return null;
		}

		return $this->providers[$this->id]['field'];
	}

	/**
	 * Returns a provider profile url
	 *
	 * @param string $id
	 * @return string|null
	 */
	public function profileUrl( $id )
	{
		if ( ( ! $this->id ) || ( ! isset( $this->providers[$this->id] ) ) ) {
			return null;
		}

		return str_replace( '[id]', $id, $this->providers[$this->id]['profile'] );
	}

	/**
	 * Returns a provider button
	 *
	 * @param int    $horizontal
	 * @param bool   $registration
	 * @return null|string
	 */
	public function button( $horizontal = 1, $registration = false )
	{
		global $_CB_framework;

		if ( ( ! $this->id ) || ( ! isset( $this->providers[$this->id] ) ) || ( ! $this->params()->get( 'enabled', false, GetterInterface::BOOLEAN ) ) || ( $registration && ( ! $this->params()->get( 'register', false, GetterInterface::BOOLEAN ) ) ) ) {
			return null;
		}

		$user						=	\CBuser::getMyUserDataInstance();

		if ( $user->get( 'id' ) ) {
			$style					=	(int) $this->params()->get( 'link_button_style', 2, GetterInterface::INT );
		} else {
			if ( $registration ) {
				$style				=	(int) $this->params()->get( 'reg_button_style', 0, GetterInterface::INT );
			} else {
				$style				=	(int) $this->params()->get( 'button_style', 2, GetterInterface::INT );
			}
		}

		if ( $style == 0 ) {
			return null;
		} elseif ( $style == 1 ) {
			$horizontal				=	1;
		}

		$providerName				=	$this->name();
		$styleClass					=	'cbConnectButton' . ( $style == 1 ? 'IconOnly' : ( $style == 2 ? 'IconText' : 'TextOnly' ) );
		$iconClass					=	$this->providers[$this->id]['icon'];
		$buttonClass				=	$this->providers[$this->id]['button'];
		$return						=	null;

		if ( $user->get( 'id' ) ) {
			if ( $this->params()->get( 'link', true, GetterInterface::BOOLEAN ) && ( ! $user->get( $this->field() ) ) ) {
				// CBTxt::T( 'LINK_YOUR_PROVIDER_ACCOUNT', 'Link your [provider] account', array( '[provider]' => $providerName ) )
				$return				=	'<button type="button" class="cbConnectButton ' . $styleClass . ' cbConnectButton' . ucfirst( $this->id ) . ' btn btn-' . $buttonClass . ' btn-sm' . ( ! $horizontal ? ' btn-block' : null ) . '" onclick="window.location=\'' . $_CB_framework->pluginClassUrl( 'cbconnect', false, array( 'provider' => $this->id, 'return' => CBConnect::getReturn() ) ) . '\'; return false;" title="' . htmlspecialchars( CBTxt::T( 'LINK_YOUR_PROVIDER_ACCOUNT LINK_YOUR_' . strtoupper( $this->id ) . '_ACCOUNT', 'Link your [provider] account', array( '[provider]' => $providerName ) ) ) . '">'
									.		( in_array( $style, array( 1, 2 ) ) ? '<span class="fa fa-' . $iconClass . ' fa-lg' . ( $style != 1 ? ' cbConnectButtonPrefix' : null ) . '"></span>' : null )
											// CBTxt::T( 'LINK_WITH_PROVIDER', 'Link with [provider]', array( '[provider]' => $providerName ) )
									.		( in_array( $style, array( 2, 3 ) ) ? CBTxt::T( 'LINK_WITH_PROVIDER LINK_WITH_' . strtoupper( $this->id ), 'Link with [provider]', array( '[provider]' => $providerName ) ) : null )
									.	'</button>'
									.	( $horizontal ? ' ' : null );
			}
		} else {
			if ( $registration ) {
				// CBTxt::T( 'SIGN_UP_WITH_PROVIDER', 'Sign up with [provider]', array( '[provider]' => $providerName ) );
				$buttonLabel		=	CBTxt::T( 'SIGN_UP_WITH_PROVIDER SIGN_UP_WITH_' . strtoupper( $this->id ), 'Sign up with [provider]', array( '[provider]' => $providerName ) );
				// CBTxt::T( 'SIGN_UP_WITH_YOUR_PROVIDER_ACCOUNT', 'Sign up with your [provider] account', array( '[provider]' => $providerName ) );
				$buttonDesc			=	CBTxt::T( 'SIGN_UP_WITH_YOUR_PROVIDER_ACCOUNT SIGN_UP_WITH_YOUR_' . strtoupper( $this->id ) . '_ACCOUNT', 'Sign up with your [provider] account', array( '[provider]' => $providerName ) );
			} else {
				// CBTxt::T( 'SIGN_IN_WITH_PROVIDER', 'Sign in with [provider]', array( '[provider]' => $providerName ) );
				$buttonLabel		=	CBTxt::T( 'SIGN_IN_WITH_PROVIDER SIGN_IN_WITH_' . strtoupper( $this->id ), 'Sign in with [provider]', array( '[provider]' => $providerName ) );
				// CBTxt::T( 'SIGN_IN_WITH_YOUR_PROVIDER_ACCOUNT', 'Sign in with your [provider] account', array( '[provider]' => $providerName ) );
				$buttonDesc			=	CBTxt::T( 'SIGN_IN_WITH_YOUR_PROVIDER_ACCOUNT SIGN_IN_WITH_YOUR_' . strtoupper( $this->id ) . '_ACCOUNT', 'Sign in with your [provider] account', array( '[provider]' => $providerName ) );
			}

			$return					=	'<button type="button" class="cbConnectButton ' . $styleClass . ' cbConnectButton' . ucfirst( $this->id ) . ' btn btn-' . $buttonClass . ' btn-sm' . ( ! $horizontal ? ' btn-block' : null ) . '" onclick="window.location=\'' . $_CB_framework->pluginClassUrl( 'cbconnect', false, array( 'provider' => $this->id, 'return' => CBConnect::getReturn() ) ) . '\'; return false;" title="' . htmlspecialchars( $buttonDesc ) . '">'
									.		( in_array( $style, array( 1, 2 ) ) ? '<span class="fa fa-' . $iconClass . ' fa-lg' . ( $style != 1 ? ' cbConnectButtonPrefix' : null ) . '"></span>' : null )
									.		( in_array( $style, array( 2, 3 ) ) ? $buttonLabel : null )
									.	'</button>'
									.	( $horizontal ? ' ' : null );
		}

		return $return;
	}

	/**
	 * Returns provider specific parameters
	 *
	 * @return Registry
	 */
	public function params()
	{
		global $_PLUGINS;

		/** @var Registry[] $cache */
		static $cache			=	array();

		if ( ( ! $this->id ) || ( ! isset( $this->providers[$this->id] ) ) ) {
			return new Registry();
		}

		static $pluginParams	=	null;

		if ( ! $pluginParams ) {
			$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbconnect' );
			$pluginParams		=	$_PLUGINS->getPluginParams( $plugin );
		}

		if ( ! isset( $cache[$this->id] ) ) {
			$cache[$this->id]	=	new Registry();

			foreach ( $pluginParams->asArray() as $k => $v ) {
				if ( strpos( $k, $this->id . '_' ) !== false ) {
					$cache[$this->id]->set( str_replace( $this->id . '_', '', $k ), $v );
				}
			}
		}

		return $cache[$this->id];
	}
}
