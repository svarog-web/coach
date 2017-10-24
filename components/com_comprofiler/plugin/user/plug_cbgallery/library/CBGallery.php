<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery;

use CBLib\Application\Application;
use CBLib\Input\Get;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use GuzzleHttp\Client;
use CB\Plugin\Gallery\Table\ItemTable;
use Exception;

defined('CBLIB') or die();

class CBGallery
{

	/**
	 * Utility function for grabbing a field while also ensuring proper display access to it
	 *
	 * @param int $fieldId
	 * @param int $profileId
	 * @return FieldTable|null
	 */
	static public function getField( $fieldId, $profileId )
	{
		if ( ! $fieldId ) {
			return null;
		}

		$userId								=	Application::MyUser()->getUserId();

		static $fields						=	array();

		if ( ! isset( $fields[$profileId][$userId] ) ) {
			$profileUser					=	\CBuser::getInstance( $profileId, false );

			$fields[$profileId][$userId]	=	$profileUser->_getCbTabs( false )->_getTabFieldsDb( null, $profileUser->getUserData(), 'profile' );
		}

		if ( ! isset( $fields[$profileId][$userId][$fieldId] ) ) {
			return null;
		}

		$field								=	$fields[$profileId][$userId][$fieldId];

		if ( ! ( $field->params instanceof ParamsInterface ) ) {
			$field->params					=	new Registry( $field->params );
		}

		return $field;
	}

	/**
	 * Utility function for grabbing the gallery tab while also ensuring proper display access to it
	 *
	 * @param int $profileId
	 * @return TabTable|null
	 */
	static public function getTab( $profileId )
	{
		static $tab							=	null;

		if ( $tab === null ) {
			$tab							=	new TabTable();

			$tab->load( array( 'pluginclass' => 'cbgalleryTab' ) );

			if ( ! ( $tab->params instanceof ParamsInterface ) ) {
				$tab->params				=	new Registry( $tab->params );
			}
		}

		$tabid								=	$tab->get( 'tabid', 0, GetterInterface::INT );

		if ( ! $tabid ) {
			return null;
		}

		$userId								=	Application::MyUser()->getUserId();

		static $tabs						=	array();

		if ( ! isset( $tabs[$profileId][$userId] ) ) {
			$profileUser					=	\CBuser::getInstance( $profileId, false );

			$tabs[$profileId][$userId]		=	array_keys( $profileUser->_getCbTabs( false )->_getTabsDb( $profileUser->getUserData(), 'profile' ) );
		}

		if ( ! in_array( $tabid, $tabs[$profileId][$userId] ) ) {
			return null;
		}

		return $tab;
	}

	/**
	 * Returns an array of users connections
	 *
	 * @param int $profileId
	 * @return array
	 */
	static public function getConnections( $profileId )
	{
		if ( ! $profileId ) {
			return array();
		}

		static $cache				=	array();

		if ( ! isset( $cache[$profileId] ) ) {
			$cbConnection			=	new \cbConnection( $profileId );

			$cache[$profileId]		=	$cbConnection->getConnectedToMe( $profileId );
		}

		return $cache[$profileId];
	}

	/**
	 * Checks if a user can create folders in the supplied gallery
	 *
	 * @param Gallery $gallery
	 * @return bool
	 */
	static public function canCreateFolders( $gallery )
	{
		global $_PLUGINS;

		static $cache								=	array();

		if ( ( ! $gallery->get( 'folders', true, GetterInterface::BOOLEAN ) ) || ( ! $gallery->get( 'folders_create', true, GetterInterface::BOOLEAN ) ) ) {
			return false;
		}

		$user										=	\CBuser::getMyUserDataInstance();
		$userId										=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ! $userId ) {
			return false;
		}

		$galleryId									=	$gallery->id();

		if ( ! isset( $cache[$userId][$galleryId] ) ) {
			if ( Application::User( $userId )->isGlobalModerator() ) {
				$cache[$userId][$galleryId]			=	true;

				return true;
			}

			if ( preg_match( '/^profile\.(\d+)/', $gallery->asset(), $matches ) ) {
				if ( (int) $matches[1] !== $userId ) {
					$cache[$userId][$galleryId]		=	false;

					return false;
				}
			}

			$access									=	false;

			if ( Application::User( (int) $userId )->canViewAccessLevel( $gallery->get( 'folders_create_access', 2, GetterInterface::INT ) ) && ( ! self::createLimitedFolders( $gallery ) ) ) {
				$access								=	true;
			}

			$_PLUGINS->trigger( 'gallery_onGalleryFoldersCreateAccess', array( &$access, $user, $gallery ) );

			$cache[$userId][$galleryId]				=	$access;
		}

		return $cache[$userId][$galleryId];
	}

	/**
	 * Checks if a user is create limited for folders in a gallery
	 *
	 * @param Gallery $gallery
	 * @return bool
	 */
	static public function createLimitedFolders( $gallery )
	{
		static $cache						=	array();

		$user								=	\CBuser::getMyUserDataInstance();
		$userId								=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ! $userId ) {
			return false;
		}

		$galleryId							=	$gallery->id();

		if ( ! isset( $cache[$userId][$galleryId] ) ) {
			$createLimit					=	$gallery->get( 'folders_create_limit', 'custom', GetterInterface::STRING );

			if ( $createLimit && ( $createLimit != 'custom' ) ) {
				$limitField					=	\CBuser::getInstance( (int) $userId, false )->getField( $createLimit, null, 'php', 'none', 'profile', 0, true );

				if ( is_array( $limitField ) ) {
					$createLimit			=	array_shift( $limitField );

					if ( is_array( $createLimit ) ) {
						$createLimit		=	implode( '|*|', $createLimit );
					}
				} else {
					$createLimit			=	$user->get( $limitField, 0, GetterInterface::INT );
				}

				$createLimit				=	(int) $createLimit;
			} else {
				$createLimit				=	$gallery->get( 'folders_create_limit_custom', 0, GetterInterface::INT );
			}

			if ( $createLimit ) {
				/** @var Gallery $gallery */
				$gallery					=	$gallery->reset()->setUserId( $userId );

				if ( $gallery->folders( true ) >= $createLimit ) {
					$createLimited			=	true;
				} else {
					$createLimited			=	false;
				}
			} else {
				$createLimited				=	false;
			}

			$cache[$userId][$galleryId]		=	$createLimited;
		}

		return $cache[$userId][$galleryId];
	}

	/**
	 * Checks if a user can create items in the supplied gallery
	 *
	 * @param string  $type
	 * @param string  $method
	 * @param Gallery $gallery
	 * @return bool
	 */
	static public function canCreateItems( $type, $method, $gallery )
	{
		global $_PLUGINS;

		static $cache												=	array();

		if ( ! $gallery->get( 'items_create', true, GetterInterface::BOOLEAN ) ) {
			return false;
		}

		$user														=	\CBuser::getMyUserDataInstance();
		$userId														=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ! $userId ) {
			return false;
		}

		$galleryId													=	$gallery->id();

		if ( ! isset( $cache[$userId][$type][$method][$galleryId] ) ) {
			$folderId												=	$gallery->get( 'folder', 0, GetterInterface::INT );

			if ( $folderId ) {
				if ( ! $gallery->get( 'folders', true, GetterInterface::BOOLEAN ) ) {
					$cache[$userId][$type][$method][$galleryId]		=	false;

					return false;
				} else {
					$folder											=	$gallery->folder( $folderId );

					if ( ( ! $folder->get( 'id', 0, GetterInterface::INT ) ) || ( ( ! $folder->get( 'published', 1, GetterInterface::INT ) ) && ( $userId != $folder->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! self::canModerate( $gallery ) ) ) || ( ( $folder->get( 'published', 1, GetterInterface::INT ) == -1 ) && $gallery->get( 'folders_create_approval', false, GetterInterface::BOOLEAN ) ) && ( ! self::canModerate( $gallery ) ) ) {
						$cache[$userId][$type][$method][$galleryId]	=	false;

						return false;
					}
				}
			}

			$create													=	false;

			foreach ( $gallery->types() as $galleryType ) {
				if ( in_array( $type, array( 'all', $galleryType ) ) ) {
					if ( $create ) {
						break;
					}

					if ( $gallery->get( $galleryType . '_create', true, GetterInterface::BOOLEAN ) ) {
						$create										=	true;
					}
				}
			}

			if ( ! $create ) {
				$cache[$userId][$type][$method][$galleryId]			=	false;

				return false;
			}

			if ( Application::User( $userId )->isGlobalModerator() ) {
				$cache[$userId][$type][$method][$galleryId]			=	true;

				return true;
			}

			if ( preg_match( '/^profile\.(\d+)/', $gallery->asset(), $matches ) ) {
				if ( (int) $matches[1] !== $userId ) {
					$cache[$userId][$type][$method][$galleryId]		=	false;

					return false;
				}
			}

			$access													=	false;

			foreach ( $gallery->types() as $galleryType ) {
				if ( in_array( $type, array( 'all', $galleryType ) ) ) {
					if ( $access ) {
						break;
					}

					$upload											=	$gallery->get( $galleryType . '_upload', true, GetterInterface::BOOLEAN );
					$link											=	$gallery->get( $galleryType . '_link', true, GetterInterface::BOOLEAN );

					if ( ! $gallery->get( $galleryType . '_create', true, GetterInterface::BOOLEAN ) ) {
						$access										=	false;
						continue;
					} elseif ( $method == 'upload' ) {
						if ( ! $upload ) {
							$access									=	false;
							continue;
						}
					} elseif ( $method == 'link' ) {
						if ( ! $link ) {
							$access									=	false;
							continue;
						}
					} elseif ( ( ! $upload ) && ( ! $link ) ) {
						$access										=	false;
						continue;
					}

					if ( ! Application::User( (int) $userId )->canViewAccessLevel( $gallery->get( $galleryType . '_create_access', 2, GetterInterface::INT ) ) ) {
						$access										=	false;
						continue;
					}

					if ( self::createLimitedItems( $type, $gallery ) ) {
						$access										=	false;
						continue;
					}

					$access											=	true;
				}
			}

			$_PLUGINS->trigger( 'gallery_onGalleryItemsCreateAccess', array( &$access, $type, $method, $user, $gallery ) );

			$cache[$userId][$type][$method][$galleryId]				=	$access;
		}

		return $cache[$userId][$type][$method][$galleryId];
	}

	/**
	 * Checks if a user is create limited for items in a gallery
	 *
	 * @param string  $type
	 * @param Gallery $gallery
	 * @return bool
	 */
	static public function createLimitedItems( $type, $gallery )
	{
		static $cache								=	array();

		$user										=	\CBuser::getMyUserDataInstance();
		$userId										=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ( ! $userId ) || ( ! in_array( $type, $gallery->types() ) ) ) {
			return false;
		}

		$galleryId									=	$gallery->id();

		if ( ! isset( $cache[$userId][$type][$galleryId] ) ) {
			$createLimit							=	$gallery->get( $type . '_create_limit', 'custom', GetterInterface::STRING );

			if ( $createLimit && ( $createLimit != 'custom' ) ) {
				$limitField							=	\CBuser::getInstance( (int) $userId, false )->getField( $createLimit, null, 'php', 'none', 'profile', 0, true );

				if ( is_array( $limitField ) ) {
					$createLimit					=	array_shift( $limitField );

					if ( is_array( $createLimit ) ) {
						$createLimit				=	implode( '|*|', $createLimit );
					}
				} else {
					$createLimit					=	$user->get( $limitField, 0, GetterInterface::INT );
				}

				$createLimit						=	(int) $createLimit;
			} else {
				$createLimit						=	$gallery->get( $type . '_create_limit_custom', 0, GetterInterface::INT );
			}

			if ( $createLimit ) {
				/** @var Gallery $gallery */
				$gallery							=	$gallery->reset()->setUserId( $userId )->setType( $type );

				if ( $gallery->items( true ) >= $createLimit ) {
					$createLimited					=	true;
				} else {
					$createLimited					=	false;
				}

				$gallery->set( 'type', null );
			} else {
				$createLimited						=	false;
			}

			$cache[$userId][$type][$galleryId]		=	$createLimited;
		}

		return $cache[$userId][$type][$galleryId];
	}

	/**
	 * Checks if a user can moderate the gallery
	 *
	 * @param Gallery $gallery
	 * @return bool
	 */
	static public function canModerate( $gallery )
	{
		global $_PLUGINS;

		static $cache						=	array();

		$user								=	\CBuser::getMyUserDataInstance();
		$userId								=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ! $userId ) {
			return false;
		}

		if ( Application::User( $userId )->isGlobalModerator() ) {
			return true;
		}

		if ( in_array( $userId, $gallery->get( 'moderators', array(), GetterInterface::RAW ) ) ) {
			return true;
		}

		$galleryId							=	$gallery->id();

		if ( ! isset( $cache[$userId][$galleryId] ) ) {
			/** @var Gallery $gallery */
			$gallery						=	$gallery->reset()->setUserId( $userId );

			$access							=	false;

			$_PLUGINS->trigger( 'gallery_onGalleryModerateAccess', array( &$access, $user, $gallery ) );

			$cache[$userId][$galleryId]		=	$access;
		}

		return $cache[$userId][$galleryId];
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
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );

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
	 * Returns the current return url or generates one from current page
	 *
	 * @param bool|false $current
	 * @param bool|false $raw
	 * @return null|string
	 */
	static public function getReturn( $current = false, $raw = false )
	{
		static $cache				=	array();

		if ( ! isset( $cache[$current] ) ) {
			$url					=	null;

			if ( $current ) {
				$returnUrl			=	Application::Input()->get( 'get/return', '', GetterInterface::BASE64 );

				if ( $returnUrl ) {
					$returnUrl		=	base64_decode( $returnUrl );

					if ( \JUri::isInternal( $returnUrl ) || ( $returnUrl[0] == '/' ) ) {
						$url		=	$returnUrl;
					}
				}
			} else {
				$isHttps			=	( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) );
				$returnUrl			=	'http' . ( $isHttps ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'];

				if ( ( ! empty( $_SERVER['PHP_SELF'] ) ) && ( ! empty( $_SERVER['REQUEST_URI'] ) ) ) {
					$returnUrl		.=	$_SERVER['REQUEST_URI'];
				} else {
					$returnUrl		.=	$_SERVER['SCRIPT_NAME'];

					if ( isset( $_SERVER['QUERY_STRING'] ) && ( ! empty( $_SERVER['QUERY_STRING'] ) ) ) {
						$returnUrl	.=	'?' . $_SERVER['QUERY_STRING'];
					}
				}

				$url				=	cbUnHtmlspecialchars( preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', preg_replace( '/eval\((.*)\)/', '', htmlspecialchars( urldecode( $returnUrl ) ) ) ) );
			}

			$cache[$current]		=	$url;
		}

		$return						=	$cache[$current];

		if ( ( ! $raw ) && $return ) {
			$return					=	base64_encode( $return );
		}

		return $return;
	}

	/**
	 * Redirects to the return url if available otherwise to the url specified
	 *
	 * @param string      $url
	 * @param null|string $message
	 * @param string      $messageType
	 */
	static public function returnRedirect( $url, $message = null, $messageType = 'message' )
	{
		$returnUrl		=	self::getReturn( true, true );

		cbRedirect( ( $returnUrl ? $returnUrl : $url ), $message, $messageType );
	}

	/**
	 * Returns file size formatted from bytes
	 *
	 * @param int $bytes
	 * @return string
	 */
	static public function getFormattedFileSize( $bytes )
	{
		if ( $bytes >= 1099511627776 ) {
			return CBTxt::T( 'FILESIZE_FORMATTED_TB', '%%COUNT%% TB|%%COUNT%% TBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1099511627776, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1073741824 ) {
			return CBTxt::T( 'FILESIZE_FORMATTED_GB', '%%COUNT%% GB|%%COUNT%% GBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1073741824, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1048576 ) {
			return CBTxt::T( 'FILESIZE_FORMATTED_MB', '%%COUNT%% MB|%%COUNT%% MBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1048576, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1024 ) {
			return CBTxt::T( 'FILESIZE_FORMATTED_KB', '%%COUNT%% KB|%%COUNT%% KBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1024, 2, '.', '' ) ) );
		}

		return CBTxt::T( 'FILESIZE_FORMATTED_B', '%%COUNT%% B|%%COUNT%% Bs', array( '%%COUNT%%' => (float) number_format( $bytes, 2, '.', '' ) ) );
	}

	/**
	 * Returns the type based off extension
	 *
	 * @param string|ItemTable $extension
	 * @param null|Gallery     $gallery
	 * @param null|string      $method
	 * @param bool             $access
	 * @return string
	 */
	static public function getExtensionType( $extension, $gallery = null, $method = null, $access = true )
	{
		if ( $extension instanceof ItemTable ) {
			$extension		=	$extension->extension();
		}

		if ( in_array( $extension, self::getExtensions( 'photos', $gallery, $method, $access ) ) ) {
			return 'photos';
		} elseif ( in_array( $extension, self::getExtensions( 'videos', $gallery, $method, $access ) ) ) {
			return 'videos';
		} elseif ( in_array( $extension, self::getExtensions( 'music', $gallery, $method, $access ) ) ) {
			return 'music';
		} elseif ( in_array( $extension, self::getExtensions( 'files', $gallery, $method, $access ) ) ) {
			return 'files';
		}

		return null;
	}

	/**
	 * Returns a list of extensions supported by the provided gallery type
	 *
	 * @param string|ItemTable $type
	 * @param null|Gallery     $gallery
	 * @param null|string      $method
	 * @param bool             $access
	 * @return array
	 */
	static public function getExtensions( $type, $gallery = null, $method = null, $access = true )
	{
		global $_PLUGINS;

		static $params				=	null;

		if ( $params == null ) {
			$plugin					=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );
			$params					=	$_PLUGINS->getPluginParams( $plugin );
		}

		if ( $type instanceof ItemTable ) {
			$type					=	$type->get( 'type', null, GetterInterface::STRING );
		}

		$photos						=	array( 'jpg', 'jpeg', 'gif', 'png' );

		if ( $gallery ) {
			$extensions				=	$gallery->get( 'files_extensions', 'zip,rar,doc,pdf,txt,xls' );
		} else {
			$extensions				=	$params->get( 'files_extensions', 'zip,rar,doc,pdf,txt,xls' );
		}

		$files						=	explode( ',', $extensions );

		$videos						=	array( 'mp4', 'ogv', 'ogg', 'webm', 'm4v' );

		if ( $method == 'link' ) {
			$videos[]				=	'youtube';
		}

		$music						=	array( 'mp3', 'oga', 'weba', 'wav', 'm4a' );

		if ( $gallery && $access ) {
			if ( ! self::canCreateItems( 'photos', $method, $gallery ) ) {
				$photos				=	array();
			}

			if ( ! self::canCreateItems( 'files', $method, $gallery ) ) {
				$files				=	array();
			}

			if ( ! self::canCreateItems( 'videos', $method, $gallery ) ) {
				$videos				=	array();
			}

			if ( ! self::canCreateItems( 'music', $method, $gallery ) ) {
				$music				=	array();
			}
		}

		switch( $type ) {
			case 'photos':
				return $photos;
				break;
			case 'files':
				return $files;
				break;
			case 'videos':
				return $videos;
				break;
			case 'music':
				return $music;
				break;
			case 'all':
				return array_unique( array_merge( $photos, $files, $videos, $music ) );
				break;
		}

		return array();
	}

	/**
	 * Returns a list of mimetypes based off extension
	 *
	 * @param array|string|ItemTable $extensions
	 * @return array|string
	 */
	static public function getMimeTypes( $extensions )
	{
		if ( $extensions instanceof ItemTable ) {
			$extensions			=	$extensions->extension();
		}

		$mimeTypes				=	cbGetMimeFromExt( $extensions );

		if ( is_array( $extensions ) ) {
			if ( in_array( 'm4v', $extensions ) ) {
				$mimeTypes[]	=	'video/mp4';
			}

			if ( in_array( 'youtube', $extensions ) ) {
				$mimeTypes[]	=	'video/x-youtube';
			}
		} else {
			if ( $extensions == 'm4v' ) {
				$mimeTypes		=	'video/mp4';
			} elseif ( $extensions == 'youtube' ) {
				$mimeTypes		=	'video/x-youtube';
			}
		}

		if ( is_array( $extensions ) ) {
			if ( in_array( 'mp3', $extensions ) ) {
				$mimeTypes[]	=	'audio/mp3';
			}
		} else {
			if ( $extensions == 'mp3' ) {
				$mimeTypes		=	'audio/mp3';
			}
		}

		if ( is_array( $extensions ) ) {
			if ( in_array( 'm4a', $extensions ) ) {
				$mimeTypes[]	=	'audio/mp4';
			}
		} else {
			if ( $extensions == 'm4a' ) {
				$mimeTypes		=	'audio/mp4';
			}
		}

		if ( is_array( $mimeTypes ) ) {
			$mimeTypes			=	array_unique( $mimeTypes );
		}

		return $mimeTypes;
	}

	/**
	 * Try to find the extension from an upload object
	 *
	 * @param ParamsInterface $upload
	 * @return null|string
	 */
	static public function getUploadExtension( $upload )
	{
		$name									=	$upload->get( 'name', null, GetterInterface::STRING );
		$extension								=	null;

		if ( $name ) {
			$extension							=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $name, PATHINFO_EXTENSION ) ) );
		}

		if ( ! $extension ) {
			$mimeType							=	$upload->get( 'type', null, GetterInterface::STRING );

			if ( $mimeType == 'video/mp4' ) {
				$extension						=	'm4v';
			} elseif ( $mimeType == 'video/x-youtube' ) {
				$extension						=	'youtube';
			} elseif ( $mimeType == 'audio/mp3' ) {
				$extension						=	'mp3';
			} elseif ( $mimeType == 'audio/mp4' ) {
				$extension						=	'm4a';
			} else {
				foreach ( cbGetMimeMap() as $ext => $type ) {
					if ( is_array( $type ) ) {
						foreach ( $type as $subExt => $subType ) {
							if ( $mimeType == $subType ) {
								$extension		=	$subExt;

								break 2;
							}
						}
					} elseif ( $mimeType == $type ) {
						$extension				=	$ext;

						break;
					}
				}
			}
		}

		return $extension;
	}

	/**
	 * Creates a directory path from base to user and type
	 *
	 * @param string      $basePath
	 * @param int|null    $userId
	 * @param string|null $type
	 */
	static public function createDirectory( $basePath, $userId = null, $type = null )
	{
		global $_CB_framework;

		if ( ! $basePath ) {
			return;
		}

		$indexPath					=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbgallery/index.html';

		if ( ! is_dir( $basePath ) ) {
			$oldMask				=	@umask( 0 );

			if ( @mkdir( $basePath, 0755, true ) ) {
				@umask( $oldMask );
				@chmod( $basePath, 0755 );

				if ( ! file_exists( $basePath . '/index.html' ) ) {
					@copy( $indexPath, $basePath . '/index.html' );
					@chmod( $basePath . '/index.html', 0755 );
				}
			} else {
				@umask( $oldMask );
			}
		}

		if ( ! file_exists( $basePath . '/.htaccess' ) ) {
			file_put_contents( $basePath . '/.htaccess', 'deny from all' );
		}

		if ( $userId !== null ) {
			$userPath				=	$basePath . '/' . (int) $userId;

			if ( ! is_dir( $userPath ) ) {
				$oldMask			=	@umask( 0 );

				if ( @mkdir( $userPath, 0755, true ) ) {
					@umask( $oldMask );
					@chmod( $userPath, 0755 );

					if ( ! file_exists( $userPath . '/index.html' ) ) {
						@copy( $indexPath, $userPath . '/index.html' );
						@chmod( $userPath . '/index.html', 0755 );
					}
				} else {
					@umask( $oldMask );
				}
			}

			if ( $type !== null ) {
				$typePath			=	$userPath . '/' . $type;

				if ( ! is_dir( $typePath ) ) {
					$oldMask		=	@umask( 0 );

					if ( @mkdir( $typePath, 0755, true ) ) {
						@umask( $oldMask );
						@chmod( $typePath, 0755 );

						if ( ! file_exists( $typePath . '/index.html' ) ) {
							@copy( $indexPath, $typePath . '/index.html' );
							@chmod( $typePath . '/index.html', 0755 );
						}
					} else {
						@umask( $oldMask );
					}
				}
			}
		}
	}

	/**
	 * Reloads page headers for ajax responses
	 *
	 * @return null|string
	 */
	static public function reloadHeaders()
	{
		global $_CB_framework;

		if ( Application::Input()->get( 'format', null, GetterInterface::STRING ) != 'raw' ) {
			return null;
		}

		$_CB_framework->getAllJsPageCodes();

		// Reset meta headers as they can't be used inline anyway:
		$_CB_framework->document->_head['metaTags']		=	array();

		// Remove all non-jQuery scripts as they'll likely just cause errors due to redeclaration:
		foreach( $_CB_framework->document->_head['scriptsUrl'] as $url => $script ) {
			if ( ( strpos( $url, 'jquery.' ) === false ) || ( strpos( $url, 'migrate' ) !== false ) ) {
				unset( $_CB_framework->document->_head['scriptsUrl'][$url] );
			}
		}

		$header				=	$_CB_framework->document->outputToHead();

		if ( ! $header ) {
			return null;
		}

		$return				=	'<div class="galleryHeaders" style="position: absolute; display: none; height: 0; width: 0; z-index: -999;">'
							.		'<script type="text/javascript">window.jQuery = cbjQuery; window.$ = cbjQuery;</script>'
							.		$header
							.	'</div>';

		return $return;
	}

	/**
	 * Returns an items type translated
	 *
	 * @param string|ItemTable $type
	 * @param bool             $plural
	 * @param bool             $lowercase
	 * @return string
	 */
	static public function translateType( $type, $plural = false, $lowercase = false )
	{
		if ( $type instanceof ItemTable ) {
			$type			=	$type->get( 'type', null, GetterInterface::STRING );
		}

		switch( $type ) {
			case 'photos':
				$string		=	( $plural ? CBTxt::T( 'Photos' ) : CBTxt::T( 'Photo' ) );
				break;
			case 'videos':
				$string		=	( $plural ? CBTxt::T( 'Videos' ) : CBTxt::T( 'Video' ) );
				break;
			case 'music':
				$string		=	CBTxt::T( 'Music' );
				break;
			default:
				$string		=	( $plural ? CBTxt::T( 'Files' ) : CBTxt::T( 'File' ) );
				break;
		}

		if ( $lowercase ) {
			$string			=	cbutf8_strtolower( $string );
		}

		return $string;
	}

	/**
	 * Returns the fontawesome icon based off type
	 *
	 * @param string|ItemTable $type
	 * @return string
	 */
	static public function getTypeIcon( $type )
	{
		if ( $type instanceof ItemTable ) {
			$type	=	$type->get( 'type', null, GetterInterface::STRING );
		}

		switch ( $type ) {
			case 'photos':
				return 'fa-picture-o';
				break;
			case 'videos':
				return 'fa-play';
				break;
			case 'music':
				return 'fa-volume-up';
				break;
		}

		return 'fa-file-o';
	}

	/**
	 * Returns the fontawesome icon based off extension and that extensions mimetype
	 *
	 * @param string|ItemTable $extension
	 * @return string
	 */
	static public function getExtensionIcon( $extension )
	{
		if ( $extension instanceof ItemTable ) {
			$extension					=	$extension->extension();
		}

		$type							=	'fa-file-o';

		if ( ! $extension ) {
			return $type;
		} elseif ( $extension == 'youtube' ) {
			return 'fa-youtube';
		}

		static $cache					=	array();

		if ( ! isset( $cache[$extension] ) ) {
			$mimeParts					=	explode( '/', CBGallery::getMimeTypes( $extension ) );

			switch ( $mimeParts[0] ) {
				case 'text':
					switch ( $extension ) {
						case 'csv':
							$type		=	'fa-file-excel-o';
							break;
						case 'css':
						case 'html':
						case 'htm':
							$type		=	'fa-file-code-o';
							break;
						default:
							$type		=	'fa-file-text-o';
							break;
					}
					break;
				case 'video':
					$type				=	'fa-file-video-o';
					break;
				case 'audio':
					$type				=	'fa-file-audio-o';
					break;
				case 'image':
					$type				=	'fa-file-image-o';
					break;
				default:
					switch ( $extension ) {
						case 'pdf':
							$type		=	'fa-file-pdf-o';
							break;
						case 'zip':
						case '7z':
						case 'rar':
						case 'tar':
						case 'iso':
							$type		=	'fa-file-archive-o';
							break;
						case 'js':
						case 'php':
						case 'xml':
						case 'java':
							$type		=	'fa-file-code-o';
							break;
						case 'ods':
						case 'xls':
						case 'xlsx':
						case 'xlt':
							$type		=	'fa-file-excel-o';
							break;
						case 'doc':
						case 'docx':
						case 'odt':
						case 'dot':
							$type		=	'fa-file-word-o';
							break;
					}
					break;
			}

			$cache[$extension]			=	$type;
		}

		return $cache[$extension];
	}

	/**
	 * Parses a url for media
	 *
	 * @param string $url
	 * @return array
	 */
	static public function parseUrl( $url )
	{
		static $cache												=	array();

		$paths														=	array(	'title'			=>	array(	'//meta[@name="og:title"]/@content',
																											'//meta[@name="twitter:title"]/@content',
																											'//meta[@name="title"]/@content',
																											'//meta[@property="og:title"]/@content',
																											'//meta[@property="twitter:title"]/@content',
																											'//meta[@property="title"]/@content',
																											'//title'
																										),
																				'description'	=>	array(	'//meta[@name="og:description"]/@content',
																											'//meta[@name="twitter:description"]/@content',
																											'//meta[@name="description"]/@content',
																											'//meta[@property="og:description"]/@content',
																											'//meta[@property="twitter:description"]/@content',
																											'//meta[@property="description"]/@content'
																										),
																				'media'			=>	array(	'video'	=>	array(	'//meta[@name="og:video"]/@content',
																																'//meta[@name="og:video:url"]/@content',
																																'//meta[@name="twitter:player"]/@content',
																																'//meta[@property="og:video"]/@content',
																																'//meta[@property="og:video:url"]/@content',
																																'//meta[@property="twitter:player"]/@content',
																																'//video/@src'
																															),
																											'audio'	=>	array(	'//meta[@name="og:audio"]/@content',
																																'//meta[@name="og:audio:url"]/@content',
																																'//meta[@property="og:audio"]/@content',
																																'//meta[@property="og:audio:url"]/@content',
																																'//audio/@src'
																															),
																											'image'	=>	array(	'//meta[@name="og:image"]/@content',
																																'//meta[@name="og:image:url"]/@content',
																																'//meta[@name="twitter:image"]/@content',
																																'//meta[@name="image"]/@content',
																																'//meta[@property="og:image"]/@content',
																																'//meta[@property="og:image:url"]/@content',
																																'//meta[@property="twitter:image"]/@content',
																																'//meta[@property="image"]/@content',
																																'//img/@src'
																															)
																										)
																			);

		if ( ! isset( $cache[$url] ) ) {
			$media													=	array(	'title'			=>	null,
																				'description'	=>	null,
																				'mimetype'		=>	null,
																				'extension'		=>	strtolower( pathinfo( $url, PATHINFO_EXTENSION ) ),
																				'url'			=>	$url,
																				'exists'		=>	false
																			);

			$domain													=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $url, PHP_URL_HOST ) );

			if ( ! $domain ) {
				return $media;
			}

			try {
				$request											=	new Client();

				$result												=	$request->get( $url );

				if ( ( $result !== false ) && ( $result->getStatusCode() == 200 ) ) {
					$media['exists']								=	true;

					$extension										=	$media['extension'];

					if ( ! $extension ) {
						$document									=	@new \DOMDocument();

						$body										=	(string) $result->getBody();

						if ( function_exists( 'mb_convert_encoding' ) ) {
							$body									=	mb_convert_encoding( $body, 'HTML-ENTITIES', 'UTF-8' );
						} else {
							$body									=	'<?xml encoding="UTF-8">' . $body;
						}

						@$document->loadHTML( $body );

						$xpath										=	@new \DOMXPath( $document );

						foreach ( $paths['title'] as $titlePath ) {
							$nodes									=	@$xpath->query( $titlePath );

							if ( ( $nodes !== false ) && $nodes->length ) {
								foreach ( $nodes as $node ) {
									$media['title']					=	Get::clean( $node->nodeValue, GetterInterface::STRING );
									break 2;
								}
							}
						}

						foreach ( $paths['description'] as $titlePath ) {
							$nodes									=	@$xpath->query( $titlePath );

							if ( ( $nodes !== false ) && $nodes->length ) {
								foreach ( $nodes as $node ) {
									$media['description']			=	Get::clean( $node->nodeValue, GetterInterface::STRING );
									break 2;
								}
							}
						}

						if ( in_array( $domain, array( 'youtube', 'youtu' ) ) ) {
							$extension								=	'youtube';

							$media['extension']						=	$extension;
						} else {
							foreach ( $paths['media'] as $mediaType ) {
								foreach ( $mediaType as $mediaPath ) {
									$nodes							=	@$xpath->query( $mediaPath );

									if ( ( $nodes !== false ) && $nodes->length ) {
										foreach ( $nodes as $node ) {
											$media['url']			=	Get::clean( $node->nodeValue, GetterInterface::STRING );

											$extension				=	strtolower( pathinfo( $node->nodeValue, PATHINFO_EXTENSION ) );

											$media['extension']		=	$extension;
											break 3;
										}
									}
								}
							}
						}
					}

					if ( $extension ) {
						$media['mimetype']							=	CBGallery::getMimeTypes( $extension );
					}
				}
			} catch( Exception $e ) {}

			$cache[$url]											=	$media;
		}

		return $cache[$url];
	}
}
