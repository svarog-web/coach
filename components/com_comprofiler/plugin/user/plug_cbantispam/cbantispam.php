<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CBLib\Database\Table\Table;
use CB\Database\Table\UserTable;
use CB\Database\Table\PluginTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CBLib\Language\CBTxt;
use CBLib\Session\Session;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onDuringLogin', 'onDuringLogin', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onDoLoginNow', 'onDoLoginNow', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onAfterLogin', 'onAfterLogin', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onBeforeLoginFormDisplay', 'onBeforeLoginFormDisplay', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onBeforeUsernameReminder', 'onBeforeForgotLogin', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onBeforeNewPassword', 'onBeforeForgotLogin', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onBeforeUserRegistration', 'onBeforeUserRegistration', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onSaveUserError', 'onSaveUserError', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onAfterUserRegistration', 'onAfterUserRegistration', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onPrepareMenus', 'getMenu','cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onStartNewPassword', 'legacyValidateCaptchaForgot', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onBeforeEmailUser', 'legacyValidateCaptchaEmail', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onBeforeLogin', 'legacyValidateCaptchaLogin', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onGetCaptchaHtmlElements', 'legacyGetCaptchaHTML', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onCheckCaptchaHtmlElements', 'legacyValidateCaptcha', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onLostPassForm', 'legacyCaptchaForgot', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onAfterEmailUserForm', 'legacyCaptchaEmail', 'cbantispamPlugin' );
$_PLUGINS->registerFunction( 'onAfterLoginForm', 'legacyCaptchaLogin', 'cbantispamPlugin' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'antispam_ipaddress' => 'cbantispamIPAddressField' ) );
$_PLUGINS->registerUserFieldTypes( array( 'antispam_captcha' => 'cbantispamCaptchaField' ) );

class cbantispamClass
{

	/**
	 * Safely splits a utf8 string
	 *
	 * @param $string
	 * @return array
	 */
	static public function UTF8_str_split( $string )
	{
		global $_CB_framework;

		if ( $_CB_framework->outputCharset() == 'UTF-8' ) {
			return preg_split( '//u', $string, -1, PREG_SPLIT_NO_EMPTY );
		} else {
			return str_split( $string, 1 );
		}
	}

	/**
	 * @param null|array $files
	 * @param bool       $loadGlobal
	 * @param bool       $loadHeader
	 */
	static public function getTemplate( $files = null, $loadGlobal = true, $loadHeader = true )
	{
		global $_CB_framework, $_PLUGINS;

		static $tmpl							=	array();

		if ( ! $files ) {
			$files								=	array();
		} elseif ( ! is_array( $files ) ) {
			$files								=	array( $files );
		}

		$id										=	md5( serialize( array( $files, $loadGlobal, $loadHeader ) ) );

		if ( ! isset( $tmpl[$id] ) ) {
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );

			if ( ! $plugin ) {
				return;
			}

			$livePath							=	$_PLUGINS->getPluginLivePath( $plugin );
			$absPath							=	$_PLUGINS->getPluginPath( $plugin );
			$params								=	$_PLUGINS->getPluginParams( $plugin );

			$template							=	$params->get( 'general_template', 'default' );
			$paths								=	array( 'global_css' => null, 'php' => null, 'css' => null, 'js' => null, 'override_css' => null );

			foreach ( $files as $file ) {
				$file							=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $file );
				$globalCss						=	'/templates/' . $template . '/template.css';
				$overrideCss					=	'/templates/' . $template . '/override.css';

				if ( $file ) {
					$php						=	$absPath . '/templates/' . $template . '/' . $file . '.php';
					$css						=	'/templates/' . $template . '/' . $file . '.css';
					$js							=	'/templates/' . $template . '/' . $file . '.js';
				} else {
					$php						=	null;
					$css						=	null;
					$js							=	null;
				}

				if ( $loadGlobal && $loadHeader ) {
					if ( ! file_exists( $absPath . $globalCss ) ) {
						$globalCss				=	'/templates/default/template.css';
					}

					if ( file_exists( $absPath . $globalCss ) ) {
						$_CB_framework->document->addHeadStyleSheet( $livePath . $globalCss );

						$paths['global_css']	=	$livePath . $globalCss;
					}
				}

				if ( $file ) {
					if ( ! file_exists( $php ) ) {
						$php					=	$absPath . '/templates/default/' . $file . '.php';
					}

					if ( file_exists( $php ) ) {
						require_once( $php );

						$paths['php']			=	$php;
					}

					if ( $loadHeader ) {
						if ( ! file_exists( $absPath . $css ) ) {
							$css				=	'/templates/default/' . $file . '.css';
						}

						if ( file_exists( $absPath . $css ) ) {
							$_CB_framework->document->addHeadStyleSheet( $livePath . $css );

							$paths['css']		=	$livePath . $css;
						}

						if ( ! file_exists( $absPath . $js ) ) {
							$js					=	'/templates/default/' . $file . '.js';
						}

						if ( file_exists( $absPath . $js ) ) {
							$_CB_framework->document->addHeadScriptUrl( $livePath . $js );

							$paths['js']		=	$livePath . $js;
						}
					}
				}

				if ( $loadGlobal && $loadHeader ) {
					if ( file_exists( $absPath . $overrideCss ) ) {
						$_CB_framework->document->addHeadStyleSheet( $livePath . $overrideCss );

						$paths['override_css']	=	$livePath . $overrideCss;
					}
				}
			}

			$tmpl[$id]							=	$paths;
		}
	}

	/**
	 * Returns array of user blocks
	 *
	 * @param int|string|UserTable $user
	 * @param null|string          $ipAddress
	 * @return cbantispamBlockTable[]
	 */
	static public function getUserBlocks( $user = null, $ipAddress = null )
	{
		global $_CB_database;

		static $cache						=	array();

		if ( $user === null ) {
			$userId							=	(int) Application::MyUser()->getUserId();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$userId							=	(int) $user;
		} else {
			$userId							=	(int) $user->get( 'id' );
		}

		if ( ( ! $userId ) || ( ! isset( $cache[$userId][$ipAddress] ) ) ) {
			if ( ! ( $user instanceof UserTable ) ) {
				$user						=	CBuser::getUserDataInstance( $userId );
			}

			if ( $ipAddress === null ) {
				$ip							=	self::getUserIP( $user );
			} else {
				$ip							=	$ipAddress;
			}

			$emailParts						=	explode( '@', $user->get( 'email' ) );
			$emailDomain					=	null;

			if ( count( $emailParts ) > 1 ) {
				$emailDomain				=	array_pop( $emailParts );
			}

			$query							=	'SELECT *'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' )
											.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
											.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . (int) $user->get( 'id' ) . ' )'
											.	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'email' )
											.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) ) . ' )';
			if ( $ip ) {
				$query						.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'ip' )
											.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $ip ) . ' )';
			}
			if ( $emailDomain ) {
				$query						.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'domain' )
											.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $emailDomain ) . ' )';
			}
			$_CB_database->setQuery( $query );
			$blocks							=	$_CB_database->loadObjectList( null, 'cbantispamBlockTable', array( $_CB_database ) );

			$cache[$userId][$ipAddress]		=	$blocks;
		}

		return $cache[$userId][$ipAddress];
	}

	/**
	 * Returns array of user whitelists
	 *
	 * @param int|string|UserTable $user
	 * @param null|string          $ipAddress
	 * @return cbantispamWhitelistTable[]
	 */
	static public function getUserWhitelists( $user = null, $ipAddress = null )
	{
		global $_CB_database;

		static $cache						=	array();

		if ( $user === null ) {
			$userId							=	(int) Application::MyUser()->getUserId();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$userId							=	(int) $user;
		} else {
			$userId							=	(int) $user->get( 'id' );
		}

		if ( ( ! $userId ) || ( ! isset( $cache[$userId][$ipAddress] ) ) ) {
			if ( ! ( $user instanceof UserTable ) ) {
				$user						=	CBuser::getUserDataInstance( $userId );
			}

			if ( $ipAddress === null ) {
				$ip							=	self::getUserIP( $user );
			} else {
				$ip							=	$ipAddress;
			}

			$emailParts						=	explode( '@', $user->get( 'email' ) );
			$emailDomain					=	null;

			if ( count( $emailParts ) > 1 ) {
				$emailDomain				=	array_pop( $emailParts );
			}

			$query							=	'SELECT *'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
											.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
											.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . (int) $user->get( 'id' ) . ' )'
											.	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'email' )
											.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) ) . ' )';
			if ( $ip ) {
				$query						.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'ip' )
											.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $ip ) . ' )';
			}
			if ( $emailDomain ) {
				$query						.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'domain' )
											.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $emailDomain ) . ' )';
			}
			$_CB_database->setQuery( $query );
			$whitelists						=	$_CB_database->loadObjectList( null, 'cbantispamWhitelistTable', array( $_CB_database ) );

			$cache[$userId][$ipAddress]		=	$whitelists;
		}

		return $cache[$userId][$ipAddress];
	}

	/**
	 * Returns the viewers current ip address
	 *
	 * @return string
	 */
	static public function getCurrentIP()
	{
		$ipAddresses	=	cbGetIParray();

		return trim( array_shift( $ipAddresses ) );
	}

	/**
	 * Returns a users current ip address
	 *
	 * @param UserTable $user
	 * @return null|string
	 */
	static public function getUserIP( $user = null )
	{
		global $_CB_database;

		static $cache			=	array();

		if ( $user === null ) {
			$userId				=	(int) Application::MyUser()->getUserId();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$userId				=	(int) $user;
		} else {
			$userId				=	(int) $user->get( 'id' );
		}

		if ( ! $userId ) {
			return self::getCurrentIP();
		}

		if ( ! isset( $cache[$userId] ) ) {
			if ( ! ( $user instanceof UserTable ) ) {
				$user			=	CBuser::getUserDataInstance( $userId );
			}

			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
			$_CB_database->setQuery( $query, 0, 1 );
			$log				=	new cbantispamLogTable();
			$_CB_database->loadObject( $log );

			if ( ! $log->get( 'id' ) ) {
				$ipAddress		=	$user->get( 'registeripaddr' );
			} else {
				$ipAddress		=	$log->get( 'ip_address' );
			}

			$cache[$userId]		=	$ipAddress;
		}

		return $cache[$userId];
	}

	/**
	 * Returns if the user can be blocked
	 *
	 * @param null|int|UserTable $user
	 * @param null|string        $ipAddress
	 * @return bool
	 */
	static public function isUserBlockable( $user = null, $ipAddress = null )
	{
		global $_PLUGINS;

		static $cache						=	array();

		$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );

		if ( ! $plugin ) {
			return false;
		}

		$params								=	$_PLUGINS->getPluginParams( $plugin );

		if ( ! $plugin->get( 'general_block', 1 ) ) {
			return false;
		}

		if ( $user === null ) {
			$userId							=	(int) Application::MyUser()->getUserId();
		} elseif ( ! $user instanceof UserTable ) {
			$userId							=	(int) $user;
		} else {
			$userId							=	(int) $user->get( 'id' );
		}

		if ( Application::User( $userId )->isGlobalModerator() ) {
			return false;
		}

		if ( ( ! $userId ) || ( ! isset( $cache[$userId][$ipAddress] ) ) ) {
			if ( ! ( $user instanceof UserTable ) ) {
				$user						=	CBuser::getUserDataInstance( $userId );
			}

			$blockable						=	true;

			if ( $ipAddress === null ) {
				$ipAddress					=	self::getUserIP();
			}

			$whitelisted					=	false;

			if ( $params->get( 'general_whitelist', 1 ) ) {
				$whitelisted				=	( count( self::getUserWhitelists( $user, $ipAddress ) ) > 0 );
			}

			if ( $whitelisted ) {
				$blockable					=	false;
			}

			$cache[$userId][$ipAddress]		=	$blockable;
		}

		return $cache[$userId][$ipAddress];
	}

	/**
	 * Returns the users most recent block
	 *
	 * @param null|int|UserTable $user
	 * @param null|string        $ipAddress
	 * @return cbantispamBlockTable|null
	 */
	static public function getUserBlock( $user = null, $ipAddress = null )
	{
		static $cache						=	array();

		if ( $user === null ) {
			$userId							=	(int) Application::MyUser()->getUserId();
		} elseif ( ! $user instanceof UserTable ) {
			$userId							=	(int) $user;
		} else {
			$userId							=	(int) $user->get( 'id' );
		}

		if ( ( ! $userId ) || ( ! isset( $cache[$userId][$ipAddress] ) ) ) {
			if ( ! ( $user instanceof UserTable ) ) {
				$user						=	CBuser::getUserDataInstance( $userId );
			}

			$blocked						=	null;

			if ( $ipAddress === null ) {
				$ipAddress					=	self::getCurrentIP();
			}

			if ( self::isUserBlockable( $user, $ipAddress ) ) {
				$blocks						=	self::getUserBlocks( $user, $ipAddress );

				foreach ( $blocks as $block ) {
					if ( $block->isBlocked() ) {
						$blocked			=	$block;
						break;
					}
				}
			}

			$cache[$userId][$ipAddress]		=	$blocked;
		}

		return $cache[$userId][$ipAddress];
	}

	/**
	 * Returns internal clean up urls
	 *
	 * @param string $name
	 * @return string
	 */
	public function loadCleanUpURL( $name )
	{
		global $_CB_framework, $_PLUGINS;

		$plugin					=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );

		switch( $name ) {
			case 'cleanup_attempts':
				$function		=	'attempts';
				break;
			case 'cleanup_log':
				$function		=	'log';
				break;
			case 'cleanup_block':
				$function		=	'block';
				break;
			case 'cleanup_all':
			default:
				$function		=	'all';
				break;
		}

		return '<a href="' . $_CB_framework->pluginClassUrl( $plugin->get( 'element' ), true, array( 'action' => 'prune', 'func' => $function, 'token' => md5( $_CB_framework->getCfg( 'secret' ) ) ), 'raw', 0, true ) . '" target="_blank">' . CBTxt::T( 'Click to Process' ) . '</a>';
	}
}

class cbantispamCaptcha
{
	/** @var string  */
	public $id			=	null;
	/** @var string  */
	public $code		=	null;
	/** @var string  */
	public $mode		=	null;
	/** @var string  */
	public $error		=	null;

	/**
	 * @param string $id
	 * @param string $mode
	 */
	public function __construct( $id = null, $mode = null )
	{
		global $_PLUGINS;

		static $params		=	null;

		if ( ! $params ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );
			$params			=	$_PLUGINS->getPluginParams( $plugin );
		}

		if ( ! $id ) {
			$id				=	uniqid( 'cbantispamCaptcha' );
		}

		$this->id			=	$id;

		$defaultMode		=	$params->get( 'captcha_mode', 'internal' );

		if ( $mode && ( $mode != $defaultMode ) ) {
			$this->mode		=	$mode;
		} else {
			$this->mode		=	$defaultMode;
		}

		$this->error		=	null;
	}

	/**
	 * @param null|string $id
	 * @param null|string $mode
	 * @return cbantispamCaptcha
	 */
	static public function getInstance( $id = null, $mode = null )
	{
		static $cache		=	array();

		if ( ! $id ) {
			$id				=	uniqid( 'cbantispamCaptcha' );
		}

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	new cbantispamCaptcha( $id, $mode );
		}

		return $cache[$id];
	}

	/**
	 * Returns preview HTML for the current captcha configuration
	 *
	 * @return string
	 */
	static public function preview()
	{
		return self::getInstance( 'preview' )->getCaptchaHTMLImage( false, false );
	}

	/**
	 * Generates new captcha code and stores to session
	 *
	 * @param int    $length
	 * @param string $characters
	 * @return string
	 */
	public function generateCode( $length = null, $characters = null )
	{
		global $_PLUGINS;

		static $params					=	null;

		if ( ! $params ) {
			$plugin						=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );
			$params						=	$_PLUGINS->getPluginParams( $plugin );
		}

		$code							=	null;

		switch( $this->mode ) {
			case 'recaptcha':
				$code					=	null;
				break;
			case 'question':
				$captchaQuestions		=	"What is 2 plus 2?=4\n"
										.	"What is 1 times 6?=6\n"
										.	"What is 9 divide 3?=3\n"
										.	"Are you a Human?=Yes\n"
										.	"Are you a Bot?=No\n"
										.	"How many words is this?=5\n"
										.	"How many fingers on a hand?=5\n"
										.	"How many toes on a foot?=5\n"
										.	"What is 10 add 10?=20\n"
										.	"What is 0 multiply 100?=0\n"
										.	"What is 5 minus 1?=4\n"
										.	"What is 2 add 2?=4\n"
										.	"4th letter of Test is?=t\n"
										.	"20, 81, 3; which is smallest?=3\n"
										.	"12, 31, 9; which is greatest?=31\n"
										.	"Purple, car, dog; which is a color?=Purple\n"
										.	"Cat, plane, rock; which is an animal?=Cat\n"
										.	"If tomorrow is Monday; what day is today?=Sunday\n"
										.	"Tim, cat, dog; which is human?=Tim";

				$questions				=	$params->get( 'captcha_internal_questions', $captchaQuestions );

				if ( ! $questions ) {
					$questions			=	$captchaQuestions;
				}

				$questions				=	explode( "\n", $questions );
				$codes					=	array();

				foreach ( $questions as $question ) {
					$question			=	explode( '=', $question );
					$key				=	( isset( $question[0] ) ? trim( CBTxt::T( $question[0] ) ) : null );
					$value				=	( isset( $question[1] ) ? trim( CBTxt::T( $question[1] ) ) : null );

					if ( $key && $value ) {
						$codes[$key]	=	$value;
					}
				}

				if ( $codes ) {
					$code				=	array_rand( $codes, 1 );
				}
				break;
			case 'internal':
			default:
				if ( ! $length ) {
					$length				=	(int) $params->get( 'captcha_internal_length', 6 );

					if ( ! $length ) {
						$length			=	6;
					}
				}

				$length					=	(int) $length;

				if ( ! $characters ) {
					$characters			=	$params->get( 'captcha_internal_characters', 'abcdefhijklmnopqrstuvwxyz' );

					if ( ! $characters ) {
						$characters		=	'abcdefhijklmnopqrstuvwxyz';
					}
				}

				for ( $i = 0, $n = (int) $length; $i < $n; $i++ ) {
					$code				.=	cbIsoUtf_substr( $characters, mt_rand( 0, cbIsoUtf_strlen( $characters ) -1 ), 1 );
				}
				break;
		}

		$this->code						=	$code;

		Application::Session()->set( $this->id, array( 'code' => $this->code ) );

		return $this->code;
	}

	/**
	 * Returns session captcha code or generates a new one
	 *
	 * @return string
	 */
	public function getCaptchaCode()
	{
		if ( ! $this->code ) {
			$this->code		=	Application::Session()->subTree( $this->id )->get( 'code', null, GetterInterface::STRING );

			if ( ! $this->code ) {
				$this->generateCode();
			}
		}

		return $this->code;
	}

	/**
	 * Outputs captcha image
	 *
	 * @param null|int    $height
	 * @param null|string $font
	 * @return null|string
	 */
	public function getCaptchaImage( $height = null, $font = null )
	{
		global $_CB_framework, $_PLUGINS;

		static $params				=	null;
		static $absPath				=	null;

		if ( ! $params ) {
			$plugin					=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );
			$params					=	$_PLUGINS->getPluginParams( $plugin );
			$absPath				=	$_PLUGINS->getPluginPath( $plugin );
		}

		$image						=	null;

		switch( $this->mode ) {
			case 'recaptcha':
				$image				=	null;
				break;
			case 'internal':
			case 'question':
			default:
				if ( ! $height ) {
					$height			=	(int) $params->get( 'captcha_internal_height', 40 );

					if ( ! $height ) {
						$height		=	40;
					}
				}

				if ( ! $font ) {
					$font			=	$params->get( 'captcha_internal_font', 'monofont.ttf' );

					if ( ! $font ) {
						$font		=	'monofont.ttf';
					}
				}

				if ( strpos( $font, '/' ) === false ) {
					if ( ! file_exists( $absPath . '/fonts/' . $font ) ) {
						$imgFont	=	$absPath . '/fonts/monofont.ttf';
					} else {
						$imgFont	=	$absPath . '/fonts/' . $font;
					}
				} else {
					$imgFont		=	$font;
				}

				$fontSize			=	( $height * 0.75 );
				$textBox			=	imagettfbbox( $fontSize, 0, $imgFont, $this->code );

				if ( ! $textBox ) {
					$width			=	( $height * 2.75 );
				} else {
					$width			=	( $textBox[4] + 20 );
				}

				$image				=	'<img src="' . $_CB_framework->pluginClassUrl( 'cbantispam', true, array( 'action' => 'captcha', 'func' => $this->mode, 'id' => $this->id ), 'raw', 0, true ) . '" id="' . htmlspecialchars( $this->id ) . 'Image" alt="' . htmlspecialchars( CBTxt::T( 'Captcha' ) ) . '"' . ( $height || $width ? ' style="' . ( $height ? 'height: ' . (int) $height . 'px;' : null ) . ( $width ? 'width: ' . (int) $width . 'px;' : null ) . '"' : null ) . ' class="cbantispamCaptchaImage" />';
				break;
		}

		return $image;
	}

	/**
	 * Outputs captcha audio
	 *
	 * @param bool $hidden
	 * @return null|string
	 */
	public function getCaptchaAudio( $hidden = false )
	{
		global $_CB_framework;

		$audio				=	null;

		switch( $this->mode ) {
			case 'recaptcha':
				$audio		=	null;
				break;
			case 'internal':
			case 'question':
			default:
				$audio		=	'<audio src="' . $_CB_framework->pluginClassUrl( 'cbantispam', true, array( 'action' => 'captcha', 'func' => 'audio', 'id' => $this->id ), 'raw', 0, true ) . '" id="' . htmlspecialchars( $this->id ) . 'AudioFile" type="audio/mpeg" class="cbantispamCaptchaAudioFile"' . ( $hidden ? ' style="display: none !important;"' : null ) . '></audio>';
				break;
		}

		return $audio;
	}

	/**
	 * Outputs captcha input
	 *
	 * @param null|int    $size
	 * @param null|string $attributes
	 * @return null|string
	 */
	public function getCaptchaInput( $size = null, $attributes = null )
	{
		global $_PLUGINS;

		static $params			=	null;

		if ( ! $params ) {
			$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );
			$params				=	$_PLUGINS->getPluginParams( $plugin );
		}

		switch( $this->mode ) {
			case 'recaptcha':
				$input			=	null;
				break;
			case 'internal':
			case 'question':
			default:
				if ( ! $size ) {
					$size		=	(int) $params->get( 'captcha_internal_size', 20 );

					if ( ! $size ) {
						$size	=	20;
					}
				}

				$input			=	'<input type="text" name="' . htmlspecialchars( $this->id ) . '" id="' . htmlspecialchars( $this->id ) . '" value=""' . ( $size ? ' size="' . (int) $size . '"' : null ) . ' class="required form-control"' . $attributes . '>';
				break;
		}

		if ( $params->get( 'captcha_honeypot', 1 ) ) {
			$honeyPot			=	$params->get( 'captcha_honeypot_name', 'full_address' );

			if ( ! $honeyPot ) {
				$honeyPot		=	'full_address';
			}

			$input				.=	'<div style="display: none !important;">'
								.		'<input type="text" name="' . htmlspecialchars( $honeyPot ) . '" value="" />'
								.	'</div>';
		}

		return $input;
	}

	/**
	 * Generates and outputs captcha image
	 *
	 * @param null|bool   $audio
	 * @param null|bool   $refresh
	 * @param null|int    $height
	 * @param null|string $font
	 * @return string
	 */
	public function getCaptchaHTMLImage( $audio = null, $refresh = null, $height = null, $font = null )
	{
		global $_CB_framework, $_PLUGINS;

		static $params				=	null;

		if ( ! $params ) {
			$plugin					=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );
			$params					=	$_PLUGINS->getPluginParams( $plugin );
		}

		$this->generateCode();

		switch( $this->mode ) {
			case 'recaptcha':
				static $JS_loaded	=	0;

				if ( ! $JS_loaded++ ) {
					$language		=	$params->get( 'captcha_recaptcha_lang', 'en' );
					$scheme			=	( ( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) ) ? 'https' : 'http' );

					$js				=	"var loadCBAntiSpamRecaptcha = function() {"
									.		"var captchas = document.getElementsByClassName( 'cbantispamRecaptcha' );"
									.		"for ( var i = 0; i < captchas.length; i++ ) {"
									.			"grecaptcha.render( captchas[i], {"
									.				"sitekey: '" . addslashes( $params->get( 'captcha_recaptcha_site_key' ) ) . "',"
									.				"theme: '" . addslashes( $params->get( 'captcha_recaptcha_theme', 'light' ) ) . "',"
									.				"size: '" . addslashes( $params->get( 'captcha_recaptcha_size', 'normal' ) ) . "'"
									.			"});"
									.		"}"
									.	"};";

					$_CB_framework->document->addHeadScriptUrl( $scheme . '://www.google.com/recaptcha/api.js?onload=loadCBAntiSpamRecaptcha&render=explicit' . ( $language ? '&hl=' . $language : null ), false, $js );
				}

				$html				=	'<div id="' . htmlspecialchars( $this->id ) . '" class="cbantispamRecaptcha"></div>';
				break;
			case 'internal':
			case 'question':
			default:
				static $JS_loaded	=	0;

				if ( ! $JS_loaded++ ) {
					$js				=	"$( '.cbantispamCaptchaRefresh' ).click( function() {"
									.		"var id = $( this ).attr( 'id' ).replace( /Refresh/, '' );"
									.		"var url = $( '#' + id + 'Image' ).attr( 'src' ).replace( /(&ver=[0-9]+)*/ig, '' );"
									.		"var ver = Math.floor( Math.random() * 10000 );"
									.		"$( '#' + id + 'Image' ).css( 'width', 'auto' );"
									.		"$( '#' + id + 'Image' ).attr( 'src', url + '&ver=' + ver );"
									.		"$( '#' + id + 'Image' ).load( function() {"
									.			"var width = $( this )[0].naturalWidth;"
									.			"if ( width != 'undefined' ) {"
									.				"$( this ).css( 'width', width );"
									.			"} else {"
									.				"$( this ).css( 'width', $( this ).width() );"
									.			"}"
									.			"var audioFile = $( '#' + id + 'AudioFile' );"
									.			"if ( audioFile.length ) {"
									.				"audioFile.attr( 'src', audioFile.attr( 'src' ).replace( /(&ver=[0-9]+)*/ig, '' ) + '&ver=' + ver );"
									.			"}"
									.		"});"
									.	"});"
									.	"$( '.cbantispamCaptchaAudio' ).click( function() {"
									.		"new MediaElement( $( this ).attr( 'id' ) + 'File', {"
									.			"success: function( media ) {"
									.				"media.play();"
									.			"}"
									.		"});"
									.	"});";

					$_CB_framework->outputCbJQuery( $js, 'media' );
				}

				$audio				=	( $this->mode == 'question' ? false : ( $audio === null ? $params->get( 'captcha_internal_audio', 1 ) : $audio ) );
				$refresh			=	( $refresh === null ? $params->get( 'captcha_internal_refresh', 1 ) : $refresh );
				$html				=	$this->getCaptchaImage( $height, $font );

				if ( $audio || $refresh ) {
					$html			=	'<div>'
									.		'<div style="float: left; margin-right: 5px; margin-bottom: 5px;">' . $html . '</div>'
									.		'<div>';

					if ( $refresh ) {
						$html		.=			'<div>'
									.				'<a href="javascript:void(0);" id="' . htmlspecialchars( $this->id ) . 'Refresh" class="cbantispamCaptchaRefresh fa fa-refresh" style="vertical-align: top; margin-bottom: 3px; cursor: pointer;" title="' . htmlspecialchars( CBTxt::T( 'Refresh Captcha' ) ) . '"></a>'
									.			'</div>';
					}

					if ( $audio ) {
						$html		.=			'<div>'
									.				'<a href="javascript:void(0);"  id="' . htmlspecialchars( $this->id ) . 'Audio" class="cbantispamCaptchaAudio fa fa-volume-up" style="vertical-align: top; cursor: pointer;" title="' . htmlspecialchars( CBTxt::T( 'Listen to Captcha' ) ) . '"></a>'
									.				$this->getCaptchaAudio( true )
									.			'</div>';
					}

					$html			.=		'</div>'
									.		'<div style="clear: both;"></div>'
									.	'</div>';
				}
				break;
		}

		return $html;
	}

	/**
	 * Returns captcha formatted HTML
	 *
	 * @param null|bool   $audio
	 * @param null|bool   $refresh
	 * @param null|int    $height
	 * @param null|string $font
	 * @param null|int    $size
	 * @param null|string $attributes
	 * @return string
	 */
	public function getCaptchaHTML( $audio = null, $refresh = null, $height = null, $font = null, $size = null, $attributes = null )
	{
		$html	=	$this->getCaptchaHTMLImage( $audio, $refresh, $height, $font )
				.	$this->getCaptchaInput( $size, $attributes );

		return $html;
	}

	/**
	 * Returns the captcha input value
	 *
	 * @return string
	 */
	public function getCaptchaInputValue()
	{
		switch( $this->mode ) {
			case 'recaptcha':
				$value	=	Application::Input()->get( 'g-recaptcha-response', null, GetterInterface::STRING );
				break;
			case 'internal':
			case 'question':
			default:
				$value	=	Application::Input()->get( $this->id, null, GetterInterface::STRING );
				break;
		}

		return $value;
	}

	/**
	 * Valiadtes a captcha code
	 *
	 * @param null|string $code
	 * @param bool        $reset
	 * @return bool
	 */
	public function validateCaptcha( $code = null, $reset = true )
	{
		global $_CB_database, $_PLUGINS;

		static $params								=	null;

		if ( ! $params ) {
			$plugin									=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );
			$params									=	$_PLUGINS->getPluginParams( $plugin );
		}

		if ( ! $code ) {
			$code									=	$this->getCaptchaInputValue();
		}

		$valid										=	false;
		$ipAddress									=	cbantispamClass::getCurrentIP();

		if ( $code ) switch( $this->mode ) {
			case 'recaptcha':
				$client								=	new GuzzleHttp\Client();

				try {
					$body							=	array(	'secret'	=>	$params->get( 'captcha_recaptcha_secret_key', null ),
																'response'	=>	$code
															);

					if ( $ipAddress ) {
						$body['remoteip']			=	$ipAddress;
					}

					$result							=	$client->get( 'https://www.google.com/recaptcha/api/siteverify', array( 'query' => $body ) );

					if ( $result->getStatusCode() == 200 ) {
						$response					=	$result->json();

						if ( isset( $response['success'] ) && ( $response['success'] == true ) ) {
							$valid					=	true;
						} elseif ( isset( $response['error-codes'] ) ) {
							$this->error			=	implode( ', ', $response['error-codes'] );
						}
					} else {
						$this->error				=	CBTxt::T( 'Failed to reach Google reCaptcha verify server.' );
					}
				} catch ( Exception $e ) {
					$this->error					=	$e->getMessage();
				}
				break;
			case 'question':
				$captchaQuestions					=	"What is 2 plus 2?=4\n"
													.	"What is 1 times 6?=6\n"
													.	"What is 9 divide 3?=3\n"
													.	"Are you a Human?=Yes\n"
													.	"Are you a Bot?=No\n"
													.	"How many words is this?=5\n"
													.	"How many fingers on a hand?=5\n"
													.	"How many toes on a foot?=5\n"
													.	"What is 10 add 10?=20\n"
													.	"What is 0 multiply 100?=0\n"
													.	"What is 5 minus 1?=4\n"
													.	"What is 2 add 2?=4\n"
													.	"4th letter of Test is?=t\n"
													.	"20, 81, 3; which is smallest?=3\n"
													.	"12, 31, 9; which is greatest?=31\n"
													.	"Purple, car, dog; which is a color?=Purple\n"
													.	"Cat, plane, rock; which is an animal?=Cat\n"
													.	"If tomorrow is Monday; what day is today?=Sunday\n"
													.	"Tim, cat, dog; which is human?=Tim";

				$questions							=	$params->get( 'captcha_internal_questions', $captchaQuestions );

				if ( ! $questions ) {
					$questions						=	$captchaQuestions;
				}

				$questions							=	explode( "\n", $questions );
				$codes								=	array();

				foreach ( $questions as $question ) {
					$question						=	explode( '=', $question );
					$key							=	( isset( $question[0] ) ? trim( CBTxt::T( $question[0] ) ) : null );
					$value							=	( isset( $question[1] ) ? trim( CBTxt::T( $question[1] ) ) : null );

					if ( $key && $value ) {
						$codes[$key]				=	$value;
					}
				}

				$captchaCode						=	$this->getCaptchaCode();

				if ( $captchaCode && isset( $codes[$captchaCode] ) && ( strtolower( $codes[$captchaCode] ) == strtolower( $code ) ) ) {
					$valid							=	true;
				}
				break;
			case 'internal':
			default:
				$captchaCode						=	$this->getCaptchaCode();

				if ( $captchaCode && ( $captchaCode == $code ) ) {
					$valid							=	true;
				}
				break;
		}

		if ( $valid && $reset ) {
			Application::Session()->set( $this->id, null );
		}

		if ( $params->get( 'captcha_honeypot', 1 ) ) {
			$honeyPot								=	$params->get( 'captcha_honeypot_name', 'full_address' );

			if ( ! $honeyPot ) {
				$honeyPot							=	'full_address';
			}

			if ( Application::Input()->get( $honeyPot, null, GetterInterface::STRING ) ) {
				$valid								=	false;
			}
		}

		if ( $reset ) {
			$blocked								=	cbantispamClass::getUserBlock( null, $ipAddress );
			$message								=	$params->get( 'captcha_autoblock_msg', 'Your captcha attempt has been blocked. Reason: [reason]' );

			if ( $blocked ) {
				if ( $message ) {
					$extras							=	array(	'[duration]'	=>	ucwords( strtolower( str_replace( array( '+', '-' ), '', $blocked->get( 'duration' ) ) ) ),
																'[date]'		=>	$blocked->get( 'date' ) . ' UTC',
																'[expire]'		=>	$blocked->getExpire() . ( $blocked->get( 'duration' ) ? ' UTC' : null )
															);

					$extras							=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'CAPTCHA_BLOCK_REASON', ( $blocked->get( 'reason' ) ? $blocked->get( 'reason' ) : 'Spam.' ), $extras ) ) );

					$this->error					=	CBTxt::T( 'CAPTCHA_BLOCK_MESSAGE', $message, $extras );
				}

				$valid								=	false;
			} elseif ( $params->get( 'general_attempts', 1 ) ) {
				if ( ! $valid ) {
					$timeframe						=	$params->get( 'captcha_autoblock_timeframe', '-1 DAY' );

					$query							=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
													.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress )
													.	"\n AND " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'captcha' )
													.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
					$_CB_database->setQuery( $query, 0, 1 );
					$attempt						=	new cbantispamAttemptsTable();
					$_CB_database->loadObject( $attempt );

					if ( ! $attempt->get( 'id' ) ) {
						$attempt->set( 'ip_address', $ipAddress );
						$attempt->set( 'type', 'captcha' );
						$attempt->set( 'count', 1 );
					} elseif ( ( ! $timeframe ) || ( Application::Date( $attempt->get( 'date' ), 'UTC' )->getTimestamp() >= Application::Date( 'now', 'UTC' )->modify( strtoupper( $timeframe ) )->getTimestamp() ) ) {
						$attempt->set( 'count', ( (int) $attempt->get( 'count' ) + 1 ) );
					}

					$attempt->set( 'date', Application::Database()->getUtcDateTime() );

					$attempt->store();

					if ( $params->get( 'captcha_autoblock', 1 ) && cbantispamClass::isUserBlockable( null, $ipAddress ) ) {
						$count						=	(int) $params->get( 'captcha_autoblock_count', 20 );

						if ( ! $count ) {
							$count					=	20;
						}

						if ( (int) $attempt->get( 'count' ) >= $count ) {
							$reason					=	$params->get( 'captcha_autoblock_reason', 'Too many failed captcha attempts.' );

							if ( $params->get( 'captcha_autoblock_method', 0 ) ) {
								$row				=	new cbantispamBlockTable();

								$row->set( 'type', 'ip' );
								$row->set( 'value', $ipAddress );
								$row->set( 'date', Application::Database()->getUtcDateTime() );
								$row->set( 'duration', $params->get( 'captcha_autoblock_dur', '+1 HOUR' ) );
								$row->set( 'reason', $reason );

								$row->store();

								if ( $message ) {
									$extras			=	array(	'[duration]' => ucwords( strtolower( str_replace( array( '+', '-' ), '', $row->get( 'duration' ) ) ) ),
																'[date]' => $row->get( 'date' ) . ' UTC',
																'[expire]' => $row->getExpire() . ( $row->get( 'duration' ) ? ' UTC' : null )
															);

									$extras			=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'CAPTCHA_BLOCK_REASON', ( $row->get( 'reason' ) ? $row->get( 'reason' ) : 'Spam.' ), $extras ) ) );

									$this->error	=	CBTxt::T( 'CAPTCHA_BLOCK_MESSAGE', $message, $extras );
								}
							} elseif ( $message ) {
								$extras				=	array(	'[duration]' => null,
																'[date]' => null,
																'[expire]' => null
															);

								$extras				=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'CAPTCHA_BLOCK_REASON', ( $reason ? $reason : 'Spam.' ), $extras ) ) );

								$this->error		=	CBTxt::T( 'CAPTCHA_BLOCK_MESSAGE', $message, $extras );
							}
						}
					}
				} else {
					$query							=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
													.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress )
													.	"\n AND " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'captcha' )
													.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
					$_CB_database->setQuery( $query );
					$attempts						=	$_CB_database->loadObjectList( null, 'cbantispamAttemptsTable', array( $_CB_database ) );

					/** @var cbantispamAttemptsTable[] $attempts */
					foreach ( $attempts as $attempt ) {
						$attempt->delete();
					}
				}
			}
		}

		return $valid;
	}
}

class cbantispamBlockTable extends Table
{
	var $id					=	null;
	var $type				=	null;
	var $value				=	null;
	var $date				=	null;
	var $duration			=	null;
	var $reason				=	null;
	var $params				=	null;

	var $_custom_duration	=	null;

	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl		=	'#__comprofiler_plugin_antispam_block';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key	=	'id';

	/**
	 * Copy the named array or object content into this object as vars
	 * only existing vars of object are filled.
	 * When undefined in array, object variables are kept.
	 *
	 * WARNING: DOES addslashes / escape BY DEFAULT
	 *
	 * Can be overridden or overloaded.
	 *
	 * @param  array|object  $array         The input array or object
	 * @param  string        $ignore        Fields to ignore
	 * @param  string        $prefix        Prefix for the array keys
	 * @return boolean                      TRUE: ok, FALSE: error on array binding
	 */
	public function bind( $array, $ignore = '', $prefix = null )
	{
		$bind								=	parent::bind( $array, $ignore, $prefix );

		// Bind the custom duration to duration if it exists
		if ( $bind ) {
			if ( is_array( $array ) && isset( $array['_custom_duration'] ) ) {
				$this->_custom_duration		=	$array['_custom_duration'];
			} elseif ( isset( $array->_custom_duration ) ) {
				$this->_custom_duration		=	$array->_custom_duration;
			}

			if ( $this->_custom_duration != '' ) {
				$this->duration				=	$this->_custom_duration;
				$this->_custom_duration		=	null;
			}
		}

		return $bind;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'antispam_onBeforeUpdateBlock', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onBeforeCreateBlock', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'antispam_onAfterUpdateBlock', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onAfterCreateBlock', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'antispam_onBeforeDeleteBlock', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'antispam_onAfterDeleteBlock', array( $this ) );

		return true;
	}

	/**
	 * Returns the expiration date incremented by duration
	 *
	 * @return string
	 */
	public function getExpire()
	{
		static $cache		=	array();

		$id					=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			if ( $this->get( 'duration' ) ) {
				$expire		=	Application::Date( $this->get( 'date' ), 'UTC' )->modify( strtoupper( $this->get( 'duration' ) ) )->format( 'Y-m-d H:i:s' );
			} else {
				$expire		=	'0000-00-00 00:00:00';
			}

			$cache[$id]		=	$expire;
		}

		return $cache[$id];
	}

	/**
	 * Returns true or false if the block is expired
	 *
	 * @return bool
	 */
	public function isExpired()
	{
		static $cache	=	array();

		$id				=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]	=	( $this->get( 'duration' ) && ( Application::Date( 'now', 'UTC' )->getTimestamp() >= Application::Date( $this->get( 'date' ), 'UTC' )->modify( strtoupper( $this->get( 'duration' ) ) )->getTimestamp() ) ? true : false );
		}

		return $cache[$id];
	}

	/**
	 * Returns true or false if this block has been whitelisted
	 *
	 * @return bool
	 */
	public function isWhitelisted()
	{
		global $_CB_database;

		static $cache				=	array();

		$type						=	$this->get( 'type' );
		$value						=	$this->get( 'value' );

		if ( ! isset( $cache[$type][$value] ) ) {
			$query					=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type )
									.	"\n AND " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $value );
			$_CB_database->setQuery( $query );
			$whitelists				=	(int) $_CB_database->loadResult();

			$cache[$type][$value]	=	( $whitelists > 0 ? true : false );
		}

		return $cache[$type][$value];
	}

	/**
	 * Returns true or false if this block is active
	 *
	 * @return bool
	 */
	public function isBlocked()
	{
		static $cache	=	array();

		$id				=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]	=	( ! ( $this->isExpired() || $this->isWhitelisted() ) ? true : false );
		}

		return $cache[$id];
	}
}

class cbantispamWhitelistTable extends Table
{
	var $id				=	null;
	var $type			=	null;
	var $value			=	null;
	var $reason			=	null;
	var $params			=	null;

	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl		=	'#__comprofiler_plugin_antispam_whitelist';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key	=	'id';

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global  $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'antispam_onBeforeUpdateWhitelist', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onBeforeCreateWhitelist', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'antispam_onAfterUpdateWhitelist', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onAfterCreateWhitelist', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'antispam_onBeforeDeleteWhitelist', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'antispam_onAfterDeleteWhitelist', array( $this ) );

		return true;
	}
}

class cbantispamLogTable extends Table
{
	var $id				=	null;
	var $user_id		=	null;
	var $ip_address		=	null;
	var $date			=	null;
	var $count			=	null;
	var $params			=	null;

	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl		=	'#__comprofiler_plugin_antispam_log';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key	=	'id';

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'antispam_onBeforeUpdateLog', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onBeforeCreateLog', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'antispam_onAfterUpdateLog', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onAfterCreateLog', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'antispam_onBeforeDeleteLog', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'antispam_onAfterDeleteLog', array( $this ) );

		return true;
	}

	/**
	 * Blocks this attempt by user id or ip address
	 *
	 * @return bool
	 */
	public function block()
	{
		global $_CB_framework;

		if ( $this->get( 'user_id' ) || $this->get( 'ip_address' ) ) {
			$block		=	new cbantispamBlockTable();

			if ( $this->get( 'user_id' ) ) {
				$block->load( array( 'value' => $this->get( 'user_id' ), 'type' => 'user' ) );
			} else {
				$block->load( array( 'value' => $this->get( 'ip_address' ), 'type' => 'ip' ) );
			}

			if ( ! $block->get( 'id' ) ) {
				if ( $this->get( 'user_id' ) ) {
					$block->set( 'type', 'user' );
					$block->set( 'value', $this->get( 'user_id' ) );
				} else {
					$block->set( 'type', 'ip' );
					$block->set( 'value', $this->get( 'ip_address' ) );
				}

				$block->set( 'date', Application::Database()->getUtcDateTime() );

				if ( ! $block->store() ) {
					$this->setError( $block->getError() );

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Whitelists this attempt by user id or ip address
	 *
	 * @return bool
	 */
	public function whitelist()
	{
		if ( $this->get( 'user_id' ) || $this->get( 'ip_address' ) ) {
			$whitelist		=	new cbantispamWhitelistTable();

			if ( $this->get( 'user_id' ) ) {
				$whitelist->load( array( 'value' => $this->get( 'user_id' ), 'type' => 'user' ) );
			} else {
				$whitelist->load( array( 'value' => $this->get( 'ip_address' ), 'type' => 'ip' ) );
			}

			if ( ! $whitelist->get( 'id' ) ) {
				if ( $this->get( 'user_id' ) ) {
					$whitelist->set( 'type', 'user' );
					$whitelist->set( 'value', $this->get( 'user_id' ) );
				} else {
					$whitelist->set( 'type', 'ip' );
					$whitelist->set( 'value', $this->get( 'ip_address' ) );
				}

				if ( ! $whitelist->store() ) {
					$this->setError( $whitelist->getError() );

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Returns true or false if this log has been whitelisted
	 *
	 * @return bool
	 */
	public function isWhitelisted()
	{
		global $_CB_database;

		static $cache			=	array();

		$user					=	(int) $this->get( 'user_id' );
		$ip						=	$this->get( 'ip_address' );

		if ( ! isset( $cache[$user][$ip] ) ) {
			$query				=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
								.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
								.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $ip ) . ' )';
			if ( $user ) {
				$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'user' )
								.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $user ) . ' )';
			}
			$_CB_database->setQuery( $query );
			$whitelists			=	(int) $_CB_database->loadResult();

			$cache[$user][$ip]	=	( $whitelists > 0 ? true : false );
		}

		return $cache[$user][$ip];
	}

	/**
	 * Returns true or false if this log has a block
	 *
	 * @return bool
	 */
	public function isBlocked()
	{
		global $_CB_database;

		static $cache				=	array();

		$user					=	(int) $this->get( 'user_id' );
		$ip						=	$this->get( 'ip_address' );

		if ( ! isset( $cache[$user][$ip] ) ) {
			$blocked				=	false;

			if ( ! $this->isWhitelisted() ) {
				$query				=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' )
									.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $ip ) . ' )';
				if ( $user ) {
					$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'user' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $user ) . ' )';
				}
				$_CB_database->setQuery( $query );
				$blocks				=	$_CB_database->loadObjectList( null, 'cbantispamBlockTable', array( $_CB_database ) );

				/** @var cbantispamBlockTable[] $blocks */
				foreach ( $blocks as $block ) {
					if ( $block->isBlocked() ) {
						$blocked	=	true;
						break;
					}
				}
			}

			$cache[$user][$ip]		=	$blocked;
		}

		return $cache[$user][$ip];
	}
}

class cbantispamAttemptsTable extends Table
{
	var $id				=	null;
	var $ip_address		=	null;
	var $date			=	null;
	var $count			=	null;
	var $type			=	null;
	var $params			=	null;

	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl		=	'#__comprofiler_plugin_antispam_attempts';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key	=	'id';

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'antispam_onBeforeUpdateAttempts', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onBeforeCreateAttempts', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'antispam_onAfterUpdateAttempts', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'antispam_onAfterCreateAttempts', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'antispam_onBeforeDeleteAttempts', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'antispam_onAfterDeleteAttempts', array( $this ) );

		return true;
	}

	/**
	 * Blocks this attempt by ip address
	 *
	 * @return bool
	 */
	public function block()
	{
		global $_CB_framework;

		$ip				=	$this->get( 'ip_address' );

		if ( $ip ) {
			$block		=	new cbantispamBlockTable();

			$block->load( array( 'value' => $ip, 'type' => 'ip' ) );

			if ( ! $block->get( 'id' ) ) {
				$block->set( 'type', 'ip' );
				$block->set( 'value', $ip );
				$block->set( 'date', Application::Database()->getUtcDateTime() );

				if ( ! $block->store() ) {
					$this->setError( $block->getError() );

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Whitelists this attempt by ip address
	 *
	 * @return bool
	 */
	public function whitelist()
	{
		$ip					=	$this->get( 'ip_address' );

		if ( $ip ) {
			$whitelist		=	new cbantispamWhitelistTable();

			$whitelist->load( array( 'value' => $ip, 'type' => 'ip' ) );

			if ( ! $whitelist->get( 'id' ) ) {
				$whitelist->set( 'type', 'ip' );
				$whitelist->set( 'value', $ip );

				if ( ! $whitelist->store() ) {
					$this->setError( $whitelist->getError() );

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Returns true or false if this attempt has been whitelisted
	 *
	 * @return bool
	 */
	public function isWhitelisted()
	{
		global $_CB_database;

		static $cache		=	array();

		$id					=	$this->get( 'ip_address' );

		if ( ! isset( $cache[$id] ) ) {
			$query			=	'SELECT COUNT(*)'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
							.	"\n AND " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $id );
			$_CB_database->setQuery( $query );
			$whitelists		=	(int) $_CB_database->loadResult();

			$cache[$id]		=	( $whitelists > 0 ? true : false );
		}

		return $cache[$id];
	}

	/**
	 * Returns true or false if this attempt has a block
	 *
	 * @return bool
	 */
	public function isBlocked()
	{
		global $_CB_database;

		static $cache				=	array();

		$id							=	$this->get( 'ip_address' );

		if ( ! isset( $cache[$id] ) ) {
			$blocked				=	false;

			if ( $this->isWhitelisted() ) {
				$query				=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
									.	"\n AND " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $id );
				$_CB_database->setQuery( $query );
				$blocks				=	$_CB_database->loadObjectList( null, 'cbantispamBlockTable', array( $_CB_database ) );

				/** @var cbantispamBlockTable[] $blocks */
				foreach ( $blocks as $block ) {
					if ( $block->isBlocked() ) {
						$blocked	=	true;
						break;
					}
				}
			}

			$cache[$id]				=	$blocked;
		}

		return $cache[$id];
	}
}

class cbantispamPlugin extends cbPluginHandler
{

	/**
	 * Returns the users sessions
	 *
	 * @param int $userId
	 * @return mixed
	 */
	private function getUserSessions( $userId )
	{
		global $_CB_database;

		if ( ! $userId ) {
			return 0;
		}

		static $cache			=	array();

		if ( ! isset( $cache[$userId] ) ) {
			$query				=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__session' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'userid' ) . " = " . (int) $userId;
			$_CB_database->setQuery( $query );
			$cache[$userId]		=	$_CB_database->loadResult();
		}

		return $cache[$userId];
	}

	/**
	 * Blocks a login attempt
	 *
	 * @param string $reason
	 * @param string $duration
	 * @param string $date
	 * @param string $expire
	 */
	private function blockLogin( $reason = null, $duration = null, $date = null, $expire = null )
	{
		global $_PLUGINS;

		if ( ! $reason ) {
			$reason		=	'Spam.';
		}

		$extras			=	array(	'[duration]' => ucwords( strtolower( str_replace( array( '+', '-' ), '', $duration ) ) ),
									'[date]' => $date . ' UTC',
									'[expire]' => $expire . ( $duration ? ' UTC' : null )
								);

		$extras			=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'LOGIN_BLOCK_REDIRECT_REASON', $reason, $extras ) ) );

		$redirect		=	$this->params->get( 'login_block_redirect', null );
		$redirectMsg	=	CBTxt::T( 'LOGIN_BLOCK_REDIRECT_MSG', $this->params->get( 'login_block_redirect_msg', 'Your login attempt has been blocked. Reason: [reason]' ), $extras );
		$redirectType	=	$this->params->get( 'login_block_redirect_type', 'error' );

		if ( ! $redirect ) {
			$redirect	=	'index.php';
		}

		cbRedirect( $redirect, $redirectMsg, $redirectType );

		$_PLUGINS->_setErrorMSG( $redirectMsg );
		$_PLUGINS->raiseError();
	}

	/**
	 * Blocks a forgot login attempt
	 *
	 * @param string $reason
	 * @param string $duration
	 * @param string $date
	 * @param string $expire
	 */
	private function blockForgotLogin( $reason = null, $duration = null, $date = null, $expire = null )
	{
		global $_PLUGINS;

		if ( ! $reason ) {
			$reason		=	'Spam.';
		}

		$extras			=	array(	'[duration]' => ucwords( strtolower( str_replace( array( '+', '-' ), '', $duration ) ) ),
									'[date]' => $date . ' UTC',
									'[expire]' => $expire . ( $duration ? ' UTC' : null )
								);

		$extras			=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'FORGOT_BLOCK_REDIRECT_REASON', $reason, $extras ) ) );

		$redirect		=	$this->params->get( 'forgot_block_redirect', null );
		$redirectMsg	=	CBTxt::T( 'FORGOT_BLOCK_REDIRECT_MSG', $this->params->get( 'forgot_block_redirect_msg', 'Your forgot login attempt has been blocked. Reason: [reason]' ), $extras );
		$redirectType	=	$this->params->get( 'forgot_block_redirect_type', 'error' );

		if ( ! $redirect ) {
			$redirect	=	'index.php';
		}

		cbRedirect( $redirect, $redirectMsg, $redirectType );

		$_PLUGINS->_setErrorMSG( $redirectMsg );
		$_PLUGINS->raiseError( 1 );
	}

	/**
	 * Blocks a registration attempt
	 *
	 * @param string $reason
	 * @param string $duration
	 * @param string $date
	 * @param string $expire
	 */
	private function blockRegistration( $reason = null, $duration = null, $date = null, $expire = null )
	{
		global $_PLUGINS;

		if ( ! $reason ) {
			$reason		=	'Spam.';
		}

		$extras			=	array(	'[duration]' => ucwords( strtolower( str_replace( array( '+', '-' ), '', $duration ) ) ),
									'[date]' => $date . ' UTC',
									'[expire]' => $expire . ( $duration ? ' UTC' : null )
								);

		$extras			=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'REG_BLOCK_REDIRECT_REASON', $reason, $extras ) ) );

		$redirect		=	$this->params->get( 'reg_block_redirect', null );
		$redirectMsg	=	CBTxt::T( 'REG_BLOCK_REDIRECT_MSG', $this->params->get( 'reg_block_redirect_msg', 'Your registration attempt has been blocked. Reason: [reason]' ), $extras );
		$redirectType	=	$this->params->get( 'reg_block_redirect_type', 'error' );

		if ( ! $redirect ) {
			$redirect	=	'index.php';
		}

		cbRedirect( $redirect, $redirectMsg, $redirectType );

		$_PLUGINS->_setErrorMSG( $redirectMsg );
		$_PLUGINS->raiseError( 1 );
	}

	/**
	 * Stores a log
	 *
	 * @param int    $userId
	 * @param string $type
	 */
	private function storeLog( $userId, $type = 'login' )
	{
		global $_CB_framework, $_CB_database;

		if ( ! $this->params->get( 'general_log', 1 ) ) {
			return;
		}

		$ipAddress			=	cbantispamClass::getCurrentIP();

		if ( ! $ipAddress ) {
			return;
		}

		$query				=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
		if ( $type ) {
			$query			.=	"\n AND " . $_CB_database->NameQuote( 'type' ) . ( is_array( $type ) ? " IN " . $_CB_database->safeArrayOfStrings( $type ) : " = " . $_CB_database->Quote( $type ) );
		}
		$query				.=	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
		$_CB_database->setQuery( $query );
		$attempts			=	$_CB_database->loadObjectList( null, 'cbantispamAttemptsTable', array( $_CB_database ) );

		/** @var cbantispamAttemptsTable[] $attempts */
		foreach ( $attempts as $attempt ) {
			$attempt->delete();
		}

		$query				=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId
							.	"\n AND " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress )
							.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
		$_CB_database->setQuery( $query, 0, 1 );
		$row				=	new cbantispamLogTable();
		$_CB_database->loadObject( $log );

		if ( ! $row->get( 'id' ) ) {
			$row->set( 'user_id', (int) $userId );
			$row->set( 'ip_address', $ipAddress );
			$row->set( 'count', 1 );
		} else {
			$row->set( 'count', ( (int) $row->get( 'count' ) + 1 ) );
		}

		$row->set( 'date', Application::Database()->getUtcDateTime() );

		$row->store();
	}

	/**
	 * Stores an attempt
	 *
	 * @param int    $userId
	 * @param string $type
	 */
	private function storeAttempt( $userId = null, $type = 'login' )
	{
		global $_CB_database;

		if ( ! $this->params->get( 'general_attempts', 1 ) ) {
			return;
		}

		$ipAddress				=	cbantispamClass::getCurrentIP();

		if ( ! $ipAddress ) {
			return;
		}

		switch ( $type ) {
			case 'login':
				$timeframe		=	$this->params->get( 'login_autoblock_timeframe', '-1 MONTH' );
				break;
			case 'forgot':
				$timeframe		=	$this->params->get( 'forgot_autoblock_timeframe', '-1 WEEK' );
				break;
			case 'reg':
				$timeframe		=	$this->params->get( 'reg_autoblock_timeframe', '-1 MONTH' );
				break;
			default:
				$timeframe		=	null;
				break;
		}

		$query					=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
		if ( $type ) {
			$query				.=	"\n AND " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type );
		}
		$query					.=	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
		$_CB_database->setQuery( $query, 0, 1 );
		$attempt				=	new cbantispamAttemptsTable();
		$_CB_database->loadObject( $attempt );

		if ( ! $attempt->get( 'id' ) ) {
			$attempt->set( 'ip_address', $ipAddress );
			$attempt->set( 'type', $type );
			$attempt->set( 'count', 1 );
		} elseif ( ( ! $timeframe ) || ( Application::Date( $attempt->get( 'date' ), 'UTC' )->getTimestamp() >= Application::Date( 'now', 'UTC' )->modify( strtoupper( $timeframe ) )->getTimestamp() ) ) {
			$attempt->set( 'count', ( (int) $attempt->get( 'count' ) + 1 ) );
		}

		$attempt->set( 'date', Application::Database()->getUtcDateTime() );

		$attempt->store();

		if ( ! cbantispamClass::isUserBlockable( $userId, $ipAddress ) ) {
			return;
		}

		$block					=	false;
		$duration				=	'+1 HOUR';
		$reason					=	null;

		switch ( $type ) {
			case 'login':
				$duration		=	$this->params->get( 'login_autoblock_dur', '+1 HOUR' );
				$reason			=	$this->params->get( 'login_autoblock_reason', 'Too many failed login attempts.' );

				if ( $this->params->get( 'login_autoblock', 0 ) ) {
					$count		=	(int) $this->params->get( 'login_autoblock_count', 5 );

					if ( ! $count ) {
						$count	=	5;
					}

					if ( (int) $attempt->get( 'count' ) >= $count ) {
						$block	=	true;
					}
				}
				break;
			case 'forgot':
				$duration		=	$this->params->get( 'forgot_autoblock_dur', '+1 HOUR' );
				$reason			=	$this->params->get( 'forgot_autoblock_reason', 'Too many forgot login attempts.' );

				if ( $this->params->get( 'forgot_autoblock', 0 ) ) {
					$count		=	(int) $this->params->get( 'forgot_autoblock_count', 5 );

					if ( ! $count ) {
						$count	=	5;
					}

					if ( (int) $attempt->get( 'count' ) >= $count ) {
						$block	=	true;
					}
				}
				break;
			case 'reg':
				$duration		=	$this->params->get( 'reg_autoblock_dur', '+1 HOUR' );
				$reason			=	$this->params->get( 'reg_autoblock_reason', 'Too many failed registration attempts.' );

				if ( $this->params->get( 'reg_autoblock', 0 ) ) {
					$count		=	(int) $this->params->get( 'reg_autoblock_count', 5 );

					if ( ! $count ) {
						$count	=	5;
					}

					if ( (int) $attempt->get( 'count' ) >= $count ) {
						$block	=	true;
					}
				}
				break;
		}

		if ( ! $block ) {
			return;
		}

		$row					=	new cbantispamBlockTable();

		$row->set( 'type', 'ip' );
		$row->set( 'value', $ipAddress );
		$row->set( 'date', Application::Database()->getUtcDateTime() );
		$row->set( 'duration', $duration );
		$row->set( 'reason', $reason );

		$row->store();

		switch ( $type ) {
			case 'login':
				$this->blockLogin( $row->get( 'reason' ), $row->get( 'duration' ), $row->get( 'date' ), $row->getExpire() );
				break;
			case 'forgot':
				$this->blockForgotLogin( $row->get( 'reason' ), $row->get( 'duration' ), $row->get( 'date' ), $row->getExpire() );
				break;
			case 'reg':
				$this->blockRegistration( $row->get( 'reason' ), $row->get( 'duration' ), $row->get( 'date' ), $row->getExpire() );
				break;
		}
	}

	/**
	 * Handles login blocking
	 *
	 * @param UserTable $user
	 */
	public function onDuringLogin( &$user )
	{
		global $_CB_database, $_PLUGINS;

		$ipAddress							=	cbantispamClass::getCurrentIP();
		$blocked							=	cbantispamClass::getUserBlock( $user, $ipAddress );

		if ( $blocked ) {
			$this->blockLogin( $blocked->get( 'reason' ), $blocked->get( 'duration' ), $blocked->get( 'date' ), $blocked->getExpire() );
		} else {
			if ( $_PLUGINS->is_errors() || $user->getError() ) {
				$this->storeAttempt( (int) $user->get( 'id' ) );
			} else {
				if ( ! cbantispamClass::isUserBlockable( $user, $ipAddress ) ) {
					return;
				}

				if ( $this->params->get( 'login_duplicate', 0 ) ) {
					$sessions				=	$this->getUserSessions( (int) $user->get( 'id' ) );
					$count					=	(int) $this->params->get( 'login_duplicate_count', 1 );

					if ( ! $count ) {
						$count				=	1;
					}

					if ( $sessions >= $count ) {
						$method				=	(int) $this->params->get( 'login_duplicate_method', 1 );

						if ( $method > 0 ) {
							$reason			=	$this->params->get( 'login_duplicate_reason', 'Already logged in.' );

							if ( $method > 1 ) {
								$row		=	new cbantispamBlockTable();

								if ( $method == 1 ) {
									$row->set( 'type', 'ip' );
									$row->set( 'value', $ipAddress );
								} else {
									$row->set( 'type', 'user' );
									$row->set( 'value', (int) $user->get( 'id' ) );
								}

								$row->set( 'date', Application::Database()->getUtcDateTime() );
								$row->set( 'duration', $this->params->get( 'login_duplicate_dur', '+1 HOUR' ) );
								$row->set( 'reason', $reason );

								$row->store();

								$this->blockLogin( $row->get( 'reason' ), $row->get( 'duration' ), $row->get( 'date' ), $row->getExpire() );
							} else {
								$this->blockLogin( $reason );
							}
						} else {
							$query			=	'DELETE'
											.	"\n FROM " . $_CB_database->NameQuote( '#__session' )
											.	"\n WHERE " . $_CB_database->NameQuote( 'userid' ) . " = " . (int) $user->get( 'id' );
							$_CB_database->setQuery( $query );
							$_CB_database->query();
						}
					}
				}

				if ( $this->params->get( 'login_share', 0 ) ) {
					$timeframe				=	$this->params->get( 'login_share_timeframe', '-1 MONTH' );

					$query					=	'SELECT COUNT(*)'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
											.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );
					if ( $timeframe ) {
						$query				.=	"\n AND " . $_CB_database->NameQuote( 'date' ) . " >= " . $_CB_database->Quote( Application::Date( 'now', 'UTC' )->modify( strtoupper( $timeframe ) )->format( 'Y-m-d H:i:s' ) );
					}
					$_CB_database->setQuery( $query );
					$logins					=	$_CB_database->loadResult();

					$count					=	(int) $this->params->get( 'login_share_count', 10 );

					if ( ! $count ) {
						$count				=	10;
					}

					if ( $logins > $count ) {
						$method				=	(int) $this->params->get( 'login_share_method', 0 );
						$reason				=	$this->params->get( 'login_share_reason', 'Login sharing.' );

						if ( $method > 0 ) {
							$row			=	new cbantispamBlockTable();

							if ( $method == 1 ) {
								$row->set( 'type', 'ip' );
								$row->set( 'value', $ipAddress );
							} else {
								$row->set( 'type', 'user' );
								$row->set( 'value', (int) $user->get( 'id' ) );
							}

							$row->set( 'date', Application::Database()->getUtcDateTime() );
							$row->set( 'duration', $this->params->get( 'login_share_dur', '+1 HOUR' ) );
							$row->set( 'reason', $reason );

							$row->store();

							$this->blockLogin( $row->get( 'reason' ), $row->get( 'duration' ), $row->get( 'date' ), $row->getExpire() );
						} else {
							$this->blockLogin( $reason );
						}
					}
				}
			}
		}
	}

	/**
	 * Stores user login attempt
	 *
	 * @param string    $username
	 * @param string    $password
	 * @param bool      $rememberMe
	 * @param UserTable $row
	 * @param bool      $loggedIn
	 * @param string    $resultError
	 * @param array     $messagesToUser
	 * @param array     $alertmessages
	 * @param string    $return
	 */
	public function onDoLoginNow( $username, $password, $rememberMe, &$row, &$loggedIn, &$resultError, &$messagesToUser, &$alertmessages, &$return )
	{
		global $_PLUGINS;

		if ( $resultError || $_PLUGINS->is_errors() || $row->getError() ) {
			$this->storeAttempt( (int) $row->get( 'id' ) );
		}
	}

	/**
	 * Stores user login log and attempts
	 *
	 * @param UserTable $user
	 * @param bool      $loggedIn
	 */
	public function onAfterLogin( &$user, $loggedIn )
	{
		global $_PLUGINS;

		if ( ( ! $loggedIn ) || $_PLUGINS->is_errors() || $user->getError() ) {
			$this->storeAttempt( (int) $user->get( 'id' ) );
		} else {
			$this->storeLog( (int) $user->get( 'id' ), array( 'login', 'forgot' ) );
		}
	}

	/**
	 * Handles login form blocking
	 *
	 * @param array  $post
	 * @param string $error
	 */
	public function onBeforeLoginFormDisplay( &$post, $error )
	{
		global $_PLUGINS;

		if ( $post ) {
			$blocked	=	cbantispamClass::getUserBlock();

			if ( $blocked ) {
				$this->blockLogin( $blocked->get( 'reason' ), $blocked->get( 'duration' ), $blocked->get( 'date' ), $blocked->getExpire() );
			} else {
				if ( $error || $_PLUGINS->is_errors() ) {
					$this->storeAttempt();
				}
			}
		}
	}

	/**
	 * Handles forgot login blocking
	 */
	public function onBeforeForgotLogin()
	{
		$myId		=	Application::MyUser()->getUserId();
		$blocked	=	cbantispamClass::getUserBlock( $myId );

		if ( $blocked ) {
			$this->blockForgotLogin( $blocked->get( 'reason' ), $blocked->get( 'duration' ), $blocked->get( 'date' ), $blocked->getExpire() );
		} else {
			$this->storeAttempt( $myId, 'forgot' );
		}
	}

	/**
	 * Handles registration blocking
	 *
	 * @param UserTable $user
	 * @param UserTable $userDuplicate
	 */
	public function onBeforeUserRegistration( &$user, &$userDuplicate )
	{
		global $_CB_database, $_PLUGINS;

		$ipAddress						=	cbantispamClass::getCurrentIP();
		$blocked						=	cbantispamClass::getUserBlock( $user, $ipAddress );

		if ( $blocked ) {
			$this->blockRegistration( $blocked->get( 'reason' ), $blocked->get( 'duration' ), $blocked->get( 'date' ), $blocked->getExpire() );
		} elseif ( ( ( ! $_PLUGINS->is_errors() ) && ( ! $user->getError() ) ) ) {
			if ( $this->params->get( 'reg_duplicate', 0 ) ) {
				if ( ! cbantispamClass::isUserBlockable( $user, $ipAddress ) ) {
					return;
				}

				$timeframe				=	$this->params->get( 'reg_duplicate_timeframe', '-1 YEAR' );

				$query					=	'SELECT COUNT(*)'
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' ) . " AS l"
										.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
										.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = l.' . $_CB_database->NameQuote( 'user_id' )
										.	"\n WHERE l." . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
				if ( $timeframe ) {
					$query				.=	"\n AND l." . $_CB_database->NameQuote( 'date' ) . " >= " . $_CB_database->Quote( Application::Date( 'now', 'UTC' )->modify( strtoupper( $timeframe ) )->format( 'Y-m-d H:i:s' ) );
				}
				$_CB_database->setQuery( $query );
				$accounts				=	$_CB_database->loadResult();

				$count					=	(int) $this->params->get( 'reg_duplicate_count', 1 );

				if ( ! $count ) {
					$count				=	1;
				}

				if ( $accounts >= $count ) {
					$method				=	(int) $this->params->get( 'reg_duplicate_method', 0 );
					$reason				=	$this->params->get( 'reg_duplicate_reason', 'Already registered.' );

					if ( $method == 1 ) {
						$row			=	new cbantispamBlockTable();

						$row->set( 'type', 'ip' );
						$row->set( 'value', $ipAddress );
						$row->set( 'date', Application::Database()->getUtcDateTime() );
						$row->set( 'duration', $this->params->get( 'reg_duplicate_dur', '+1 HOUR' ) );
						$row->set( 'reason', $reason );

						$row->store();

						$this->blockRegistration( $row->get( 'reason' ), $row->get( 'duration' ), $row->get( 'date' ), $row->getExpire() );
					} else {
						$this->blockRegistration( $reason );
					}
				}
			}
		}
	}

	/**
	 * Stores user save attempt
	 *
	 * @param UserTable $user
	 * @param string    $error
	 * @param string    $reason
	 */
	public function onSaveUserError( &$user, $error, $reason )
	{
		global $_PLUGINS;

		if ( $error || $_PLUGINS->is_errors() || $user->getError() ) {
			$this->storeAttempt( (int) $user->get( 'id' ), 'reg' );
		}
	}

	/**
	 * Stores user registration log
	 *
	 * @param UserTable $user
	 * @param UserTable $userDuplicate
	 * @param bool      $state
	 */
	public function onAfterUserRegistration( &$user, &$userDuplicate, $state )
	{
		$this->storeLog( (int) $user->get( 'id' ), 'reg' );
	}

	/**
	 * @param UserTable $user
	 */
	public function getMenu( $user )
	{
		global $_CB_framework;

		if ( ( ! cbantispamClass::isUserBlockable( $user ) ) ||( ! Application::MyUser()->isGlobalModerator() ) ) {
			return;
		}

		if ( $this->params->get( 'general_block', 1 ) ) {
			if ( $this->params->get( 'menu_block_user', 1 ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_BLOCKUSER' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Block User' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'block', 'func' => 'user', 'usr' => (int) $user->get( 'id' ) ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-ban"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Block this users account' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_block_ip', 1 ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_BLOCKIP' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Block IP Address' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'block', 'func' => 'ip', 'usr' => (int) $user->get( 'id' ) ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-ban"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Block this users IP Address' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_block_email', 0 ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_BLOCKEMAIL' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Block Email Address' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'block', 'func' => 'email', 'usr' => (int) $user->get( 'id' ) ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-ban"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Block this users Email Address' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_block_domain', 0 ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_BLOCKDOMAIN' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Block Email Domain' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'block', 'func' => 'domain', 'usr' => (int) $user->get( 'id' ) ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-ban"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Block this users Email Domain' ) );

				$this->addMenu( $menu );
			}
		}

		if ( $this->params->get( 'general_whitelist', 1 ) ) {
			if ( $this->params->get( 'menu_whitelist_user', 1 ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_WHITELISTUSER' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Whitelist User' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'whitelist', 'func' => 'user', 'usr' => (int) $user->get( 'id' ) ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-shield"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Whitelist this users account' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_whitelist_ip', 1 ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_WHITELISTIP' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Whitelist IP Address' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'whitelist', 'func' => 'ip', 'usr' => (int) $user->get( 'id' ) ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-shield"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Whitelist this users IP Address' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_whitelist_email', 0 ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_WHITELISTEMAIL' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Whitelist Email Address' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'whitelist', 'func' => 'email', 'usr' => (int) $user->get( 'id' ) ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-shield"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Whitelist this users Email Address' ) );

				$this->addMenu( $menu );
			}

			if ( $this->params->get( 'menu_whitelist_domain', 0 ) ) {
				$menu					=	array();
				$menu['arrayPos']		=	array( '_UE_MENU_MODERATE' => array( '_UE_MENU_ANTISPAM_WHITELISTDOMAIN' => null ) );
				$menu['position']		=	'menuBar';
				$menu['caption']		=	htmlspecialchars( CBTxt::T( 'Whitelist Email Domain' ) );
				$menu['url']			=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'whitelist', 'func' => 'domain', 'usr' => (int) $user->get( 'id' ) ) );
				$menu['target']			=	'';
				$menu['img']			=	'<span class="fa fa-shield"></span> ';
				$menu['tooltip']		=	htmlspecialchars( CBTxt::T( 'Whitelist this users Email Domain' ) );

				$this->addMenu( $menu );
			}
		}
	}

	/**
	 * Displays legacy forgot login captcha
	 *
	 * @return array|null
	 */
	public function legacyCaptchaForgot()
	{
		$return			=	null;

		if ( $this->params->get( 'captcha_legacy_forgot', 0 ) ) {
			$return		=	array( null, cbantispamCaptcha::getInstance( 'legacyCaptchaForgotLogin', $this->params->get( 'captcha_legacy_forgot_mode', null ) )->getCaptchaHTML() );
		}

		return $return;
	}

	/**
	 * Validates legacy forgot login captcha
	 */
	public function legacyValidateCaptchaForgot()
	{
		global $_PLUGINS;

		if ( $this->params->get( 'captcha_legacy_forgot', 0 ) ) {
			$captcha	=	cbantispamCaptcha::getInstance( 'legacyCaptchaForgotLogin', $this->params->get( 'captcha_legacy_forgot_mode', null ) );

			if ( ! $captcha->validateCaptcha() ) {
				$_PLUGINS->_setErrorMSG( ( $captcha->error ? $captcha->error : CBTxt::T( 'Invalid Captcha Code' ) ) );
				$_PLUGINS->raiseError();
			}
		}
	}

	/**
	 * Displays legacy email form captcha
	 *
	 * @return null|string
	 */
	public function legacyCaptchaEmail()
	{
		$return			=	null;

		if ( $this->params->get( 'captcha_legacy_email', 0 ) ) {
			$return		=	cbantispamCaptcha::getInstance( 'legacyCaptchaEmailForm', $this->params->get( 'captcha_legacy_email_mode', null ) )->getCaptchaHTML();
		}

		return $return;
	}

	/**
	 * Validates legacy email form captcha
	 */
	public function legacyValidateCaptchaEmail()
	{
		global $_PLUGINS;

		if ( $this->params->get( 'captcha_legacy_email', 0 ) ) {
			$captcha	=	cbantispamCaptcha::getInstance( 'legacyCaptchaEmailForm', $this->params->get( 'captcha_legacy_email_mode', null ) );

			if ( ! $captcha->validateCaptcha() ) {
				$_PLUGINS->_setErrorMSG( ( $captcha->error ? $captcha->error : CBTxt::T( 'Invalid Captcha Code' ) ) );
				$_PLUGINS->raiseError();
			}
		}
	}

	/**
	 * Displays legacy login captcha
	 *
	 * @return null|string
	 */
	public function legacyCaptchaLogin()
	{
		$return			=	null;

		if ( $this->params->get( 'captcha_legacy_login', 0 ) ) {
			$return		=	'<div class="cbLegacyLoginCaptcha cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
						.		cbantispamCaptcha::getInstance( uniqid( 'legacyCaptchaLogin' ), $this->params->get( 'captcha_legacy_login_mode', null ) )->getCaptchaHTML()
						.	'</div>';
		}

		return $return;
	}

	/**
	 * Validates legacy login captcha
	 *
	 * @param string      $username
	 * @param string|bool $password
	 */
	public function legacyValidateCaptchaLogin( $username, $password )
	{
		global $_PLUGINS;

		if ( $username && $password && $this->params->get( 'captcha_legacy_login', 0 ) ) {
			$post						=	$this->getInput()->getNamespaceRegistry( 'post' );
			$cookieName					=	null;

			if ( $post ) {
				foreach ( $post->asArray() as $k => $v ) {
					if ( strpos( $k, 'legacyCaptchaLogin' ) !== false ) {
						$cookieName		=	$k;
					}
				}
			}

			$captcha					=	cbantispamCaptcha::getInstance( $cookieName, $this->params->get( 'captcha_legacy_login_mode', null ) );

			if ( ! $captcha->validateCaptcha() ) {
				$_PLUGINS->_setErrorMSG( ( $captcha->error ? $captcha->error : CBTxt::T( 'Invalid Captcha Code' ) ) );
				$_PLUGINS->raiseError();
			}
		}
	}

	/**
	 * Legacy captcha api call to return captcha html
	 *
	 * @param bool $generateFullHtml
	 * @return array|string
	 */
	public function legacyGetCaptchaHTML( $generateFullHtml = true )
	{
		$captcha	=	cbantispamCaptcha::getInstance( uniqid( 'legacyCaptcha' ) );

		if ( $generateFullHtml ) {
			$html	=	$captcha->getCaptchaHTML();
		} else {
			$html	=	array( $captcha->getCaptchaHTMLImage(), $captcha->getCaptchaInput() );
		}

		return $html;
	}

	/**
	 * Legacy captcha api call to validate captcha
	 *
	 * @return bool
	 */
	public function legacyValidateCaptcha()
	{
		global $_PLUGINS;

		$post						=	$this->getInput()->getNamespaceRegistry( 'post' );
		$cookieName					=	null;

		if ( $post ) {
			foreach ( $post->asArray() as $k => $v ) {
				if ( strpos( $k, 'legacyCaptcha' ) !== false ) {
					$cookieName		=	$k;
				}
			}
		}

		$captcha					=	cbantispamCaptcha::getInstance( $cookieName );

		if ( ! $captcha->validateCaptcha() ) {
			$_PLUGINS->_setErrorMSG( ( $captcha->error ? $captcha->error : CBTxt::T( 'Invalid Captcha Code' ) ) );
			$_PLUGINS->raiseError();

			return false;
		}

		return true;
	}
}

class cbantispamTab extends cbTabHandler
{
	protected $tabBlock			=	0;
	protected $tabWhitelist		=	0;
	protected $tabAttempts		=	0;
	protected $tabLogs			=	0;

	/**
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_framework, $_CB_database;

		if ( ( ! Application::MyUser()->isGlobalModerator() ) || Application::User( (int) $user->get( 'id' ) )->isGlobalModerator() ) {
			return null;
		}

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params			=	new Registry( $tab->params );
		}

		$blocksEnabled				=	( $this->params->get( 'general_block', 1 ) && $tab->params->get( 'tab_block', $this->tabBlock ) );
		$whitelistsEnabled			=	( $this->params->get( 'general_whitelist', 1 ) && $tab->params->get( 'tab_whitelist', $this->tabWhitelist ) );
		$attemptsEnabled			=	( $this->params->get( 'general_attempts', 1 ) && $tab->params->get( 'tab_attempts', $this->tabAttempts ) );
		$logsEnabled				=	( $this->params->get( 'general_log', 1 ) && $tab->params->get( 'tab_logs', $this->tabLogs ) );
		$return						=	null;

		if ( $blocksEnabled || $whitelistsEnabled || $attemptsEnabled || $logsEnabled ) {
			$tabPrefix				=	'tab_' . (int) $tab->get( 'tabid' ) . '_';
			$viewer					=	CBuser::getMyUserDataInstance();

			outputCbJs();
			outputCbTemplate();
			cbimport( 'cb.pagination' );

			cbantispamClass::getTemplate( 'tab' );

			$ipAddress				=	cbantispamClass::getUserIP( $user );
			$emailParts				=	explode( '@', $user->get( 'email' ) );
			$emailDomain			=	null;

			if ( count( $emailParts ) > 1 ) {
				$emailDomain		=	array_pop( $emailParts );
			}

			$blocks					=	null;

			if ( $blocksEnabled ) {
				cbantispamClass::getTemplate( 'blocks' );

				$blocksPrefix		=	$tabPrefix . 'blocks_';
				$limit				=	(int) $tab->params->get( 'tab_limit', 15 );
				$limitstart			=	$_CB_framework->getUserStateFromRequest( $blocksPrefix . 'limitstart{com_comprofiler}', $blocksPrefix . 'limitstart' );

				$query				=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' )
									.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . (int) $user->get( 'id' ) . ' )'
									.	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'email' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) ) . ' )';
				if ( $ipAddress ) {
					$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'ip' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $ipAddress ) . ' )';
				}
				if ( $emailDomain ) {
					$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'domain' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $emailDomain ) . ' )';
				}
				$_CB_database->setQuery( $query );
				$total				=	$_CB_database->loadResult();

				if ( $total <= $limitstart ) {
					$limitstart		=	0;
				}

				$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

				$pageNav->setInputNamePrefix( $blocksPrefix );

				$query				=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' )
									.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . (int) $user->get( 'id' ) . ' )'
									.	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'email' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) ) . ' )';
				if ( $ipAddress ) {
					$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'ip' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $ipAddress ) . ' )';
				}
				if ( $emailDomain ) {
					$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'domain' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $emailDomain ) . ' )';
				}
				$query				.=	"\n ORDER BY " . $_CB_database->NameQuote( 'id' ) . " ASC";
				if ( $tab->params->get( 'tab_paging', 1 ) ) {
					$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
				} else {
					$_CB_database->setQuery( $query );
				}
				$rows				=	$_CB_database->loadObjectList( null, 'cbantispamBlockTable', array( $_CB_database ) );

				$blocks				=	HTML_cbantispamBlocks::showBlocks( $rows, $pageNav, $viewer, $user, $tab, $this );
			}

			$whitelists				=	null;

			if ( $whitelistsEnabled ) {
				cbantispamClass::getTemplate( 'whitelists' );

				$whitelistsPrefix	=	$tabPrefix . 'whitelists_';
				$limit				=	(int) $tab->params->get( 'tab_limit', 15 );
				$limitstart			=	$_CB_framework->getUserStateFromRequest( $whitelistsPrefix . 'limitstart{com_comprofiler}', $whitelistsPrefix . 'limitstart' );

				$query				=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
									.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . (int) $user->get( 'id' ) . ' )'
									.	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'email' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) ) . ' )';
				if ( $ipAddress ) {
					$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'ip' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $ipAddress ) . ' )';
				}
				if ( $emailDomain ) {
					$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'domain' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $emailDomain ) . ' )';
				}
				$_CB_database->setQuery( $query );
				$total				=	$_CB_database->loadResult();

				if ( $total <= $limitstart ) {
					$limitstart		=	0;
				}

				$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

				$pageNav->setInputNamePrefix( $whitelistsPrefix );

				$query				=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
									.	"\n WHERE ( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . (int) $user->get( 'id' ) . ' )'
									.	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'email' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) ) . ' )';
				if ( $ipAddress ) {
					$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'ip' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $ipAddress ) . ' )';
				}
				if ( $emailDomain ) {
					$query			.=	' OR ( ' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'domain' )
									.	' AND ' . $_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $emailDomain ) . ' )';
				}
				$query				.=	"\n ORDER BY " . $_CB_database->NameQuote( 'id' ) . " ASC";
				if ( $tab->params->get( 'tab_paging', 1 ) ) {
					$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
				} else {
					$_CB_database->setQuery( $query );
				}
				$rows				=	$_CB_database->loadObjectList( null, 'cbantispamWhitelistTable', array( $_CB_database ) );

				$whitelists			=	HTML_cbantispamWhitelists::showWhitelists( $rows, $pageNav, $viewer, $user, $tab, $this );
			}

			$attempts				=	null;

			if ( $attemptsEnabled ) {
				cbantispamClass::getTemplate( 'attempts' );

				$attemptsPrefix		=	$tabPrefix . 'attempts_';
				$limit				=	(int) $tab->params->get( 'tab_limit', 15 );
				$limitstart			=	$_CB_framework->getUserStateFromRequest( $attemptsPrefix . 'limitstart{com_comprofiler}', $attemptsPrefix . 'limitstart' );

				if ( $ipAddress ) {
					$query			=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
					$_CB_database->setQuery( $query );
					$total			=	$_CB_database->loadResult();
				} else {
					$total			=	0;
				}

				if ( $total <= $limitstart ) {
					$limitstart		=	0;
				}

				$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

				$pageNav->setInputNamePrefix( $attemptsPrefix );

				if ( $ipAddress ) {
					$query			=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress )
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
					if ( $tab->params->get( 'tab_paging', 1 ) ) {
						$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
					} else {
						$_CB_database->setQuery( $query );
					}
					$rows			=	$_CB_database->loadObjectList( null, 'cbantispamAttemptsTable', array( $_CB_database ) );
				} else {
					$rows			=	array();
				}

				$attempts			=	HTML_cbantispamAttempts::showAttempts( $rows, $pageNav, $viewer, $user, $tab, $this );
			}

			$logs					=	null;

			if ( $logsEnabled ) {
				cbantispamClass::getTemplate( 'logs' );

				$logsPrefix			=	$tabPrefix . 'logs_';
				$limit				=	(int) $tab->params->get( 'tab_limit', 15 );
				$limitstart			=	$_CB_framework->getUserStateFromRequest( $logsPrefix . 'limitstart{com_comprofiler}', $logsPrefix . 'limitstart' );

				$query				=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );
				$_CB_database->setQuery( $query );
				$total				=	$_CB_database->loadResult();

				if ( $total <= $limitstart ) {
					$limitstart		=	0;
				}

				$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

				$pageNav->setInputNamePrefix( $logsPrefix );

				$query				=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
				if ( $tab->params->get( 'tab_paging', 1 ) ) {
					$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
				} else {
					$_CB_database->setQuery( $query );
				}
				$rows				=	$_CB_database->loadObjectList( null, 'cbantispamLogTable', array( $_CB_database ) );

				$logs				=	HTML_cbantispamLogs::showLogs( $rows, $pageNav, $viewer, $user, $tab, $this );
			}

			$class					=	$this->params->get( 'general_class', null );

			$return					=	'<div id="cbAntiSpam" class="cbAntiSpam' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
									.		'<div id="cbAntiSpamInner" class="cbAntiSpamInner">'
									.			HTML_cbantispamTab::showTab( $blocks, $whitelists, $attempts, $logs, $viewer, $user, $tab, $this )
									.		'</div>'
									.	'</div>';
		}

		return $return;
	}
}

class cbantispamTabBlocks extends cbantispamTab
{

	/**
	 * Constructor to set the default display mode
	 */
	public function __construct()
	{
		parent::__construct();

		$this->tabBlock	=	1;
	}
}

class cbantispamTabWhitelists extends cbantispamTab
{

	/**
	 * Constructor to set the default display mode
	 */
	public function __construct()
	{
		parent::__construct();

		$this->tabWhitelist	=	1;
	}
}

class cbantispamTabAttempts extends cbantispamTab
{

	/**
	 * Constructor to set the default display mode
	 */
	public function __construct()
	{
		parent::__construct();

		$this->tabAttempts	=	1;
	}
}

class cbantispamTabLog extends cbantispamTab
{

	/**
	 * Constructor to set the default display mode
	 */
	public function __construct()
	{
		parent::__construct();

		$this->tabLogs	=	1;
	}
}

class cbantispamIPAddressField extends cbFieldHandler
{

	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		$modOnly				=	$field->params->get( 'cbantispam_ipaddress_display', 1 );
		$return					=	null;

		if ( ( Application::MyUser()->isGlobalModerator() && $modOnly ) || ( ! $modOnly ) ) {
			$ipAddress			=	cbantispamClass::getUserIP( $user );

			switch ( $output ) {
				case 'html':
				case 'rss':
					$return		=	$this->_formatFieldOutput( $field->get( 'name' ), $ipAddress, $output, true );
					break;
				case 'htmledit':
					$return		=	null;
					break;
				default:
					$return		=	$this->_formatFieldOutput( $field->get( 'name' ), $ipAddress, $output, false );
					break;
			}
		}

		return $return;
	}
}

class cbantispamCaptchaField extends cbFieldHandler
{

	/**
	 * formats variable array into data attribute string
	 *
	 * @param  FieldTable $field
	 * @param  UserTable  $user
	 * @param  string     $output
	 * @param  string     $reason
	 * @param  array      $attributeArray
	 * @return null|string
	 */
	protected function getDataAttributes( $field, $user, $output, $reason, $attributeArray = array() )
	{
		if ( $field->params->get( 'cbantispam_captcha_ajax_valid', 0 ) ) {
			$attributeArray[]	=	cbValidator::getRuleHtmlAttributes( 'cbfield', array( 'user' => (int) $user->id, 'field' => htmlspecialchars( $field->name ), 'reason' => htmlspecialchars( $reason ) ) );
		}

		return parent::getDataAttributes( $field, $user, $output, $reason, $attributeArray );
	}

	/**
	 * Formatter:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $formatting           'tr', 'td', 'div', 'span', 'none',   'table'??
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$return			=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && ( $output == 'htmledit' ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			$field->set( 'searchable', 0 );
			$field->set( 'profile', 0 );
			$field->set( 'readonly', 0 );
			$field->set( 'required', 1 );

			$return		=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}

		return $return;
	}

	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		$return			=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && ( $output == 'htmledit' ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			$return		=	cbantispamCaptcha::getInstance( $field->get( 'name' ), $field->params->get( 'cbantispam_captcha_mode', null ) )->getCaptchaHTML( null, null, null, null, $field->get( 'size' ), $this->getDataAttributes( $field, $user, $output, $reason ) )
						.	getFieldIcons( null, $this->_isRequired( $field, $user, $reason ), null, $this->getFieldDescription( $field, $user, $output, $reason ), $this->getFieldTitle( $field, $user, $output, $reason ) );
		}

		return $return;
	}

	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check well for the $reason ...
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable    $user
	 * @param  array                 $postdata
	 * @param  string                $reason     'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @return string                            Expected output.
	 */
	public function fieldClass( &$field, &$user, &$postdata, $reason )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			parent::fieldClass( $field, $user, $postdata, $reason );

			$function			=	cbGetParam( $_GET, 'function', null );

			if ( $function == 'checkvalue' ) {
				$value			=	stripslashes( cbGetParam( $postdata, 'value', null ) );

				if ( ! cbantispamCaptcha::getInstance( $field->get( 'name' ), $field->params->get( 'cbantispam_captcha_mode', null ) )->validateCaptcha( $value, false ) ) {
					$valid		=	false;
					$message	=	CBTxt::T( 'Captcha code not valid.' );
				} else {
					$valid		=	true;
					$message	=	CBTxt::T( 'Captcha code is valid.' );
				}

				return json_encode( array( 'valid' => $valid, 'message' => $message ) );
			}
		}

		return null;
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			$value		=	cbantispamCaptcha::getInstance( $field->get( 'name' ), $field->params->get( 'cbantispam_captcha_mode', null ) )->getCaptchaInputValue();

			$this->validate( $field, $user, null, $value, $postdata, $reason );
		}
	}

	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string      $columnName  Column to validate
	 * @param  string      $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array       $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason      'edit' for save user edit, 'register' for save registration
	 * @return boolean                  True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && in_array( $reason, array( 'register', 'edit' ) ) ) {
			if ( parent::validate( $field, $user, $columnName, $value, $postdata, $reason ) ) {
				$captcha	=	cbantispamCaptcha::getInstance( $field->get( 'name' ), $field->params->get( 'cbantispam_captcha_mode', null ) );

				if ( ! $captcha->validateCaptcha() ) {
					$this->_setValidationError( $field, $user, $reason, ( $captcha->error ? $captcha->error : CBTxt::T( 'Invalid Captcha Code' ) ) );

					return false;
				}
			}
		}

		return true;
	}
}