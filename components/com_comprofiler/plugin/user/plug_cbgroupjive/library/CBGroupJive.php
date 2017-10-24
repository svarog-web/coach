<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive;

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Database\Table\TableInterface;
use CB\Database\Table\UserTable;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\Table\NotificationTable;
use CB\Plugin\GroupJive\Table\InviteTable;

defined('CBLIB') or die();

class CBGroupJive
{

	/**
	 * @param null|array $files
	 * @param bool       $loadGlobal
	 * @param bool       $loadHeader
	 * @param string     $integration
	 */
	static public function getTemplate( $files = null, $loadGlobal = true, $loadHeader = true, $integration = null )
	{
		global $_CB_framework, $_PLUGINS;

		static $tmpl							=	array();

		if ( ! $files ) {
			$files								=	array();
		} elseif ( ! is_array( $files ) ) {
			$files								=	array( $files );
		}

		$id										=	md5( serialize( array( $files, $loadGlobal, $loadHeader, $integration ) ) );

		if ( ! isset( $tmpl[$id] ) ) {
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );

			if ( ! $plugin ) {
				return;
			}

			if ( $integration ) {
				self::getTemplate();

				$integrationPlugin				=	$_PLUGINS->getLoadedPlugin( 'user/plug_cbgroupjive/plugins', $integration );

				if ( ! $integrationPlugin ) {
					return;
				}

				$livePath						=	$_PLUGINS->getPluginLivePath( $integrationPlugin );
				$absPath						=	$_PLUGINS->getPluginPath( $integrationPlugin );
			} else {
				$livePath						=	$_PLUGINS->getPluginLivePath( $plugin );
				$absPath						=	$_PLUGINS->getPluginPath( $plugin );
			}

			$params								=	$_PLUGINS->getPluginParams( $plugin );

			$template							=	$params->get( 'general_template', 'default' );
			$globalCss							=	'/templates/' . $template . '/template.css';
			$overrideCss						=	'/templates/' . $template . '/override.css';

			if ( $loadGlobal && $loadHeader ) {
				if ( ! file_exists( $absPath . $globalCss ) ) {
					$globalCss					=	'/templates/default/template.css';
				}

				if ( file_exists( $absPath . $globalCss ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $globalCss );

					$paths['global_css']		=	$livePath . $globalCss;
				}
			}

			$paths								=	array( 'global_css' => null, 'php' => null, 'css' => null, 'js' => null, 'override_css' => null );

			foreach ( $files as $file ) {
				$file							=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $file );

				if ( $file ) {
					$php						=	$absPath . '/templates/' . $template . '/' . $file . '.php';
					$css						=	'/templates/' . $template . '/' . $file . '.css';
					$js							=	'/templates/' . $template . '/' . $file . '.js';
				} else {
					$php						=	null;
					$css						=	null;
					$js							=	null;
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
			}

			if ( $loadGlobal && $loadHeader ) {
				if ( file_exists( $absPath . $overrideCss ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $overrideCss );

					$paths['override_css']		=	$livePath . $overrideCss;
				}
			}

			$tmpl[$id]							=	$paths;
		}
	}

	/**
	 * Sends a PM or Email notification with substitutions based off configuration
	 *
	 * @param int                  $type 1: Email, 2: PM, 3: Moderators, 4: Auto
	 * @param UserTable|int|null   $from
	 * @param UserTable|int|string $to
	 * @param string               $subject
	 * @param string               $body
	 * @param GroupTable           $group
	 * @param array                $extra
	 * @return bool
	 */
	static public function sendNotification( $type, $from, $to, $subject, $body, $group, $extra = array() )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ( ! $subject ) || ( ! $body ) || ( ! $group->get( 'id' ) ) || ( ! $group->category()->get( 'published', 1 ) ) || ( ( $type != 3 ) && ( ( $group->get( 'published', 1 ) != 1 ) || ( ! $to ) ) ) ) {
			return false;
		}

		if ( $from instanceof UserTable ) {
			$fromUser			=	$from;
		} elseif ( is_int( $from ) ) {
			$fromUser			=	\CBuser::getUserDataInstance( $from );
		} else {
			$fromUser			=	null;
		}

		if ( $to instanceof UserTable ) {
			$toUser				=	$to;
		} elseif ( is_int( $to ) ) {
			$toUser				=	\CBuser::getUserDataInstance( $to );
		} else {
			$toUser				=	null;
		}

		if ( $fromUser && $toUser && ( $fromUser->get( 'id' ) == $toUser->get( 'id' ) ) ) {
			return false;
		}

		static $plugin			=	null;
		static $params			=	null;

		if ( ! $params ) {
			$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params				=	$_PLUGINS->getPluginParams( $plugin );
		}

		$notifyBy				=	(int) $params->get( 'notifications_notifyby', 1 );
		$fromName				=	$params->get( 'notifications_from_name', null );
		$fromEmail				=	$params->get( 'notifications_from_address', null );
		$cbUser					=	\CBuser::getInstance( ( $fromUser ? (int) $fromUser->get( 'id' ) : ( $toUser ? (int) $toUser->get( 'id' ) : 0 ) ), false );
		$user					=	$cbUser->getUserData();

		$extras					=	array(	'category_id'			=>	(int) $group->category()->get( 'id' ),
											'category_name'			=>	( $group->category()->get( 'id' ) ? CBTxt::T( $group->category()->get( 'name' ) ) : CBTxt::T( 'Uncategorized' ) ),
											'category_description'	=>	( $group->category()->get( 'id' ) ? htmlspecialchars( CBTxt::T( $group->category()->get( 'description' ) ) ) : null ),
											'category'				=>	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $group->get( 'category' ) ) ) . '">' . ( $group->category()->get( 'id' ) ? CBTxt::T( $group->category()->get( 'name' ) ) : CBTxt::T( 'Uncategorized' ) ) . '</a>',
											'group_id'				=>	(int) $group->get( 'id' ),
											'group_name'			=>	htmlspecialchars( CBTxt::T( $group->get( 'name' ) ) ),
											'group_description'		=>	htmlspecialchars( CBTxt::T( $group->get( 'description' ) ) ),
											'group'					=>	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) . '">' . htmlspecialchars( CBTxt::T( $group->get( 'name' ) ) ) . '</a>',
											'user'					=>	'<a href="' . $_CB_framework->viewUrl( 'userprofile', false, array( 'user' => (int) $user->get( 'id' ) ) ) . '">' . getNameFormat( $user->get( 'name' ), $user->get( 'username' ), Application::Config()->get( 'name_format', 3 ) ) . '</a>'
										);

		if ( ! $toUser ) {
			$extras['email']	=	$to;
			$extras['name']		=	$to;
			$extras['username']	=	$to;
		}

		$extras					=	array_merge( $extras, $extra );
		$subject				=	$cbUser->replaceUserVars( $subject, true, false, $extras, false );
		$body					=	$cbUser->replaceUserVars( $body, false, false, $extras, false );

		if ( $type == 4 ) {
			$type				=	( $notifyBy == 2 ? 2 : 1 );
		}

		$notification			=	new \cbNotification();

		if ( $type == 3 ) {
			// Moderator Notification:
			$notification->sendToModerators( $subject, $body, false, 1 );
		} elseif ( ( $type == 2 ) && $toUser ) {
			// PM Notification:
			if ( ! $toUser->get( 'id' ) ) {
				return false;
			}

			$notification->sendUserPMSmsg( $toUser, 0, $subject, $body, true, false, 1, $extras );
		} else {
			// Email Notification:
			if ( $toUser ) {
				if ( ! $toUser->get( 'id' ) ) {
					return false;
				}

				$notification->sendFromSystem( $toUser, $subject, $body, 1, 1, null, null, null, $extras, true, $fromName, $fromEmail );
			} else {
				$userTo			=	new UserTable();

				$userTo->set( 'email', $to );
				$userTo->set( 'name', $to );
				$userTo->set( 'username', $to );

				$notification->sendFromSystem( $userTo, $subject, $body, 1, 1, null, null, null, $extras, true, $fromName, $fromEmail );
			}
		}

		return true;
	}

	/**
	 * Parses for users set to receive a notification and sends it to them
	 *
	 * @param string             $notification The notification to send
	 * @param string             $subject
	 * @param string             $body
	 * @param GroupTable         $group        Group for this notification
	 * @param UserTable|int|null $from         UserTable|int: Specific user to notify from (used for substitutions), Null: Notify from self
	 * @param UserTable|int|null $to           UserTable|int: Specific user to notify, Null: Notify everyone elegible
	 * @param array              $skip         Array of user ids to skip
	 * @param int                $status       Group status restriction for notifications (e.g. 2: Group Moderators and above)
	 * @param array              $extra
	 * @return bool
	 */
	static public function sendNotifications( $notification, $subject, $body, $group, $from = null, $to = null, $skip = array(), $status = 1, $extra = array() )
	{
		global $_CB_database, $_PLUGINS;

		if ( is_int( $from ) ) {
			$from					=	\CBuser::getUserDataInstance( $from );
		}

		if ( is_int( $to ) ) {
			$to						=	\CBuser::getUserDataInstance( $to );
		}

		$myId						=	Application::MyUser()->getUserId();

		if ( ( ! $notification ) || ( ! $subject ) || ( ! $body ) ) {
			return false;
		} elseif ( $to && ( $to->get( 'id' ) == $myId ) ) {
			return false;
		} elseif ( $from && $to && ( $from->get( 'id' ) == $to->get( 'id' ) ) ) {
			return false;
		} elseif ( ( ! $group->get( 'id' ) ) || ( $group->get( 'published' ) != 1 ) ) {
			return false;
		} elseif ( $group->category()->get( 'id' ) && ( ! $group->category()->get( 'published' ) ) ) {
			return false;
		}

		static $params				=	null;

		if ( ! $params ) {
			$plugin					=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params					=	$_PLUGINS->getPluginParams( $plugin );
		}

		if ( ( ! $group->category()->get( 'id' ) ) && ( ! $params->get( 'groups_uncategorized', 1 ) ) ) {
			return false;
		} elseif ( ! $params->get( 'notifications', 1 ) ) {
			return false;
		}

		if ( ! $status ) {
			$status					=	1;
		}

		if ( ! is_array( $skip ) ) {
			$skip					=	array( $skip );
		}

		if ( $from ) {
			$skip[]					=	$from->get( 'id' );
		}

		$moderators					=	Application::CmsPermissions()->getGroupsOfViewAccessLevel( Application::Config()->get( 'moderator_viewaccesslevel', 3, GetterInterface::INT ), true );

		$query						=	'SELECT DISTINCT n.*'
									.	', u.' . $_CB_database->NameQuote( 'status' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_notifications' ) . " AS n"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
									.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = n.' . $_CB_database->NameQuote( 'user_id' )
									.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = n.' . $_CB_database->NameQuote( 'group' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
									.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = u.' . $_CB_database->NameQuote( 'user_id' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
									.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__user_usergroup_map' ) . " AS g"
									.	' ON g.' . $_CB_database->NameQuote( 'user_id' ) . ' = j.' . $_CB_database->NameQuote( 'id' )
									.	"\n WHERE n." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' );

		if ( $to ) {
			$query					.=	"\n AND n." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $to->get( 'id' );
		} else {
			$query					.=	"\n AND n." . $_CB_database->NameQuote( 'user_id' ) . " != " . (int) $myId;
		}

		if ( $skip ) {
			$query					.=	"\n AND n." . $_CB_database->NameQuote( 'user_id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $skip );
		}

		$query						.=	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
									.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
									.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
									.	"\n AND ( n." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $group->get( 'user_id' )
									.		' OR u.' . $_CB_database->NameQuote( 'status' ) . " >= " . (int) $status
									.		( $moderators ? ' OR g.' . $_CB_database->NameQuote( 'group_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $moderators ) : null ) . ' )';
		$_CB_database->setQuery( $query );
		$rows						=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\NotificationTable', array( $_CB_database ) );

		self::preFetchUsers( $rows );

		/** @var NotificationTable[] $rows */
		foreach ( $rows as $row ) {
			if ( ! $row->params()->get( $notification, 0 ) ) {
				continue;
			}

			if ( $to ) {
				$notifyUser			=	$to;
			} else {
				$notifyUser			=	\CBuser::getUserDataInstance( (int) $row->get( 'user_id' ) );
			}

			$group->set( '_user_status', $row->get( 'status' ) );

			if ( ! self::canAccessGroup( $group, $notifyUser ) ) {
				continue;
			}

			self::sendNotification( 4, $from, $notifyUser, $subject, $body, $group, $extra );
		}

		return true;
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

					if ( \JUri::isInternal( $returnUrl ) ) {
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
	 * Creates a directory path from base to category or group
	 *
	 * @param string   $basePath
	 * @param int|null $category
	 * @param int|null $group
	 */
	static public function createDirectory( $basePath, $category = null, $group = null )
	{
		global $_CB_framework;

		if ( ! $basePath ) {
			return;
		}

		$indexPath					=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbgroupjive/index.html';

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

		if ( $category !== null ) {
			$categoryPath			=	$basePath . '/' . (int) $category;

			if ( ! is_dir( $categoryPath ) ) {
				$oldMask			=	@umask( 0 );

				if ( @mkdir( $categoryPath, 0755, true ) ) {
					@umask( $oldMask );
					@chmod( $categoryPath, 0755 );

					if ( ! file_exists( $categoryPath . '/index.html' ) ) {
						@copy( $indexPath, $categoryPath . '/index.html' );
						@chmod( $categoryPath . '/index.html', 0755 );
					}
				} else {
					@umask( $oldMask );
				}
			}

			if ( $group !== null ) {
				$groupPath			=	$categoryPath . '/' . (int) $group;

				if ( ! is_dir( $groupPath ) ) {
					$oldMask		=	@umask( 0 );

					if ( @mkdir( $groupPath, 0755, true ) ) {
						@umask( $oldMask );
						@chmod( $groupPath, 0755 );

						if ( ! file_exists( $groupPath . '/index.html' ) ) {
							@copy( $indexPath, $groupPath . '/index.html' );
							@chmod( $groupPath . '/index.html', 0755 );
						}
					} else {
						@umask( $oldMask );
					}
				}
			}
		}
	}

	/**
	 * Recursively delete a folder and its contents
	 *
	 * @param string $source
	 */
	static public function deleteDirectory( $source )
	{
		if ( is_dir( $source ) ) {
			$source			=	str_replace( '\\', '/', realpath( $source ) );

			if ( is_dir( $source ) ) {
				$files		=	new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $source ), \RecursiveIteratorIterator::CHILD_FIRST );

				if ( $files ) foreach ( $files as $file ) {
					$file	=	str_replace( '\\', '/', realpath( $file ) );

					if ( is_dir( $file ) ) {
						@rmdir( $file );
					} elseif ( is_file( $file ) ) {
						@unlink( $file );
					}
				}

				@rmdir( $source );
			}
		}
	}

	/**
	 * recursively copy a folder and its contents
	 *
	 * @param string $source
	 * @param string $destination
	 * @param bool   $deleteSource
	 */
	static public function copyDirectory( $source, $destination, $deleteSource = false )
	{
		if ( is_dir( $source ) ) {
			$source					=	str_replace( '\\', '/', realpath( $source ) );
			$oldmask				=	@umask( 0 );

			if ( ! file_exists( $destination ) ) {
				@mkdir( $destination, 0755 );
			}

			$destination			=	str_replace( '\\', '/', realpath( $destination ) );

			if ( is_dir( $destination ) ) {
				$files				=	new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $source ), \RecursiveIteratorIterator::SELF_FIRST );

				if ( $files ) {
					foreach ( $files as $file ) {
						$file		=	str_replace( '\\', '/', realpath( $file ) );

						if ( is_dir( $file ) ) {
							@mkdir( str_replace( $source . '/', $destination . '/', $file . '/' ), 0755 );
						} elseif ( is_file( $file ) ) {
							$copy	=	str_replace( $source . '/', $destination . '/', $file );

							@copy( $file, $copy );
							@chmod( $copy, 0755 );
						}
					}
				}
			}

			@umask( $oldmask );

			if ( $deleteSource ) {
				self::deleteDirectory( $source );
			}
		}
	}

	/**
	 * Uploads category or group canvas or logo
	 *
	 * @param string                   $type
	 * @param CategoryTable|GroupTable $row
	 * @return bool
	 */
	static public function uploadImage( $type = 'canvas', &$row )
	{
		global $_CB_framework, $_PLUGINS;

		if ( Application::Cms()->getClientId() ) {
			$input						=	Application::Input();
			$files						=	$input->getNamespaceRegistry( 'files' );
		} else {
			$input						=	$row->get( '_input', new Registry(), GetterInterface::RAW );
			$files						=	$row->get( '_files', new Registry(), GetterInterface::RAW );
		}

		if ( ( ! $type ) || ( ! in_array( $type, array( 'canvas', 'logo' ) ) ) ) {
			return false;
		}

		$method							=	$input->get( 'post/' . $type . '_method', null, GetterInterface::INT );

		if ( $method === 0 ) {
			return true;
		}

		static $params					=	null;

		if ( ! $params ) {
			$plugin						=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params						=	$_PLUGINS->getPluginParams( $plugin );
		}

		$basePath						=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgroupjive';

		if ( $row instanceof GroupTable ) {
			$imagePath					=	$basePath . '/' . (int) $row->get( 'category' ) . '/' . (int) $row->get( 'id' );
		} else {
			$imagePath					=	$basePath . '/' . (int) $row->get( 'id' );
		}

		$upload							=	$files->subTree( $type );
		$uploadFile						=	$upload->get( 'tmp_name', null, GetterInterface::STRING );

		if ( ( ( $method === null ) || ( $method === 1 ) ) && $uploadFile ) {
			if ( $row instanceof GroupTable ) {
				self::createDirectory( $basePath, $row->get( 'category' ), $row->get( 'id' ) );
			} else {
				self::createDirectory( $basePath, $row->get( 'id' ) );
			}

			$resample					=	$params->get( $type . '_resample', 1 );
			$aspectRatio				=	$params->get( $type . '_maintain_aspect_ratio', 1 );
			$imageHeight				=	(int) $params->get( $type . '_image_height', 640 );

			if ( ! $imageHeight ) {
				$imageHeight			=	640;
			}

			$imageWidth					=	(int) $params->get( $type . '_image_width', 1280 );

			if ( ! $imageWidth ) {
				$imageWidth				=	1280;
			}

			$thumbHeight				=	(int) $params->get( $type . '_thumbnail_height', 320 );

			if ( ! $thumbHeight ) {
				$thumbHeight			=	320;
			}

			$thumbWidth					=	(int) $params->get( $type . '_thumbnail_width', 640 );

			if ( ! $thumbWidth ) {
				$thumbWidth				=	640;
			}

			$conversionType				=	(int) Application::Config()->get( 'conversiontype', 0 );
			$imageSoftware				=	( $conversionType == 5 ? 'gmagick' : ( $conversionType == 1 ? 'imagick' : ( $conversionType == 4 ? 'gd' : 'auto' ) ) );
			$imageId					=	uniqid();

			try {
				$image					=	new \CBLib\Image\Image( $imageSoftware, $resample, $aspectRatio );

				$image->setName( $imageId );
				$image->setSource( $upload->asArray() );
				$image->setDestination( $imagePath . '/' );

				$image->processImage( $imageWidth, $imageHeight );

				$newFileName			=	$image->getCleanFilename();

				$image->setName( 'tn' . $imageId );

				$image->processImage( $thumbWidth, $thumbHeight );

				if ( $row->get( $type ) ) {
					$oldImage			=	$imagePath . '/' . $row->get( $type );

					if ( file_exists( $oldImage ) ) {
						@unlink( $oldImage );
					}

					$oldThumbnail		=	$imagePath . '/tn' . $row->get( $type );

					if ( file_exists( $oldThumbnail ) ) {
						@unlink( $oldThumbnail );
					}
				}

				$row->set( $type, $newFileName );
			} catch ( \Exception $e ) {
				$row->setError( $e->getMessage() );

				return false;
			}
		} elseif ( ( $method === 2 ) && $row->get( $type ) ) {
			$image						=	$imagePath . '/' . $row->get( $type );

			if ( file_exists( $image ) ) {
				@unlink( $image );
			}

			$thumbnail					=	$imagePath . '/tn' . $row->get( $type );

			if ( file_exists( $thumbnail ) ) {
				@unlink( $thumbnail );
			}

			$row->set( $type, '' );
		}

		return true;
	}

	/**
	 * @param UserTable          $user
	 * @param CategoryTable|null $category
	 * @return bool
	 */
	static public function canCreateGroup( $user, $category = null )
	{
		global $_CB_database, $_PLUGINS;

		if ( ! $user->get( 'id' ) ) {
			return false;
		} elseif ( self::isModerator( (int) $user->get( 'id' ) ) ) {
			return true;
		}

		static $params							=	null;

		if ( ! $params ) {
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params								=	$_PLUGINS->getPluginParams( $plugin );
		}

		static $cache							=	array();

		$id										=	md5( $user->get( 'id' ) . ( $category ? $category->get( 'id' ) : 0 ) );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]							=	false;

			if ( $category !== null ) {
				if ( ! $category->get( 'id' ) ) {
					if ( ! $params->get( 'groups_uncategorized', 1 ) ) {
						$cache[$id]				=	false;

						return false;
					}
				} else {
					if ( ( ! $category->get( 'published' ) ) || ( ! self::canAccess( (int) $category->get( 'access' ), (int) $user->get( 'id' ) ) ) ) {
						$cache[$id]				=	false;

						return false;
					}

					$createAccess				=	(int) $category->get( 'create_access', 0 );

					if ( ( $createAccess != 0 ) && ( ( $createAccess == -1 ) || ( ! self::canAccess( $createAccess, (int) $user->get( 'id' ) ) ) ) ) {
						$cache[$id]				=	false;

						return false;
					}
				}
			}

			$createAccess						=	(int) $params->get( 'groups_create_access', 2 );

			if ( $createAccess == -1 ) {
				$cache[$id]						=	false;

				return false;
			} elseif ( self::canAccess( $createAccess, (int) $user->get( 'id' ) ) ) {
				$createLimit					=	(int) $params->get( 'groups_create_limit', 0 );

				if ( $createLimit ) {
					static $count				=	array();

					$countId					=	(int) $user->get( 'id' );

					if ( ! isset( $count[$countId] ) ) {
						$query					=	'SELECT COUNT(*)'
												.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $countId;
						$_CB_database->setQuery( $query );
						$count[$countId]		=	(int) $_CB_database->loadResult();
					}

					if ( $count[$countId] >= $createLimit ) {
						$cache[$id]				=	false;

						return false;
					}
				}

				$access							=	true;

				$_PLUGINS->trigger( 'gj_onCanCreateGroup', array( &$access, $category, $user ) );

				$cache[$id]						=	$access;

				return $access;
			}
		}

		return $cache[$id];
	}

	/**
	 * returns a cached category object or adds existing categories to the cache
	 *
	 * @param int|CategoryTable[] $id
	 * @return CategoryTable|null
	 */
	static public function getCategory( $id )
	{
		static $cache				=	array();

		if ( is_array( $id ) ) {
			foreach ( $id as $row ) {
				/** @var CategoryTable $row */
				$rowId				=	(int) $row->get( 'id' );

				if ( ! $rowId ) {
					continue;
				}

				$cache[$rowId]		=	$row;
			}

			return null;
		} elseif ( ! $id ) {
			return new CategoryTable();
		} elseif ( ! isset( $cache[$id] ) ) {
			$row					=	new CategoryTable();

			$row->load( (int) $id );

			$cache[$id]				=	$row;
		}

		return $cache[$id];
	}

	/**
	 * returns a cached group object or adds existing groups to the cache
	 *
	 * @param int|GroupTable[] $id
	 * @return GroupTable|null
	 */
	static public function getGroup( $id )
	{
		static $cache			=	array();

		if ( is_array( $id ) ) {
			foreach ( $id as $row ) {
				/** @var GroupTable $row */
				$rowId			=	(int) $row->get( 'id' );

				if ( ! $rowId ) {
					continue;
				}

				$cache[$rowId]	=	$row;
			}

			return null;
		} elseif ( ! $id ) {
			return new GroupTable();
		} elseif ( ! isset( $cache[$id] ) ) {
			$row				=	new GroupTable();

			$row->load( (int) $id );

			$cache[$id]			=	$row;
		}

		return $cache[$id];
	}

	/**
	 * returns a cached group user object or adds existing group users to the cache
	 *
	 * @param int|\CB\Plugin\GroupJive\Table\UserTable[] $id
	 * @return \CB\Plugin\GroupJive\Table\UserTable|null
	 */
	static public function getUser( $id )
	{
		static $cache			=	array();

		if ( is_array( $id ) ) {
			foreach ( $id as $row ) {
				/** @var GroupTable $row */
				$rowId			=	(int) $row->get( 'id' );

				if ( ! $rowId ) {
					continue;
				}

				$cache[$rowId]	=	$row;
			}

			return null;
		} elseif ( ! $id ) {
			return new \CB\Plugin\GroupJive\Table\UserTable();
		} elseif ( ! isset( $cache[$id] ) ) {
			$row				=	new \CB\Plugin\GroupJive\Table\UserTable();

			$row->load( (int) $id );

			$cache[$id]			=	$row;
		}

		return $cache[$id];
	}

	/**
	 * returns a cached group invite object or adds existing group invites to the cache
	 *
	 * @param int|InviteTable[] $id
	 * @return InviteTable|null
	 */
	static public function getInvite( $id )
	{
		static $cache			=	array();

		if ( is_array( $id ) ) {
			foreach ( $id as $row ) {
				/** @var GroupTable $row */
				$rowId			=	(int) $row->get( 'id' );

				if ( ! $rowId ) {
					continue;
				}

				$cache[$rowId]	=	$row;
			}

			return null;
		} elseif ( ! $id ) {
			return new InviteTable();
		} elseif ( ! isset( $cache[$id] ) ) {
			$row				=	new InviteTable();

			$row->load( (int) $id );

			$cache[$id]			=	$row;
		}

		return $cache[$id];
	}

	/**
	 * @param UserTable  $user
	 * @param GroupTable $group
	 * @return null|int|bool    -1: Banned, 0: Pending, 1: Active, 2: Moderator, 3: Admin, 4: Owner
	 */
	static public function getGroupStatus( $user, $group )
	{
		global $_CB_database;

		if ( ( ! $user->get( 'id' ) ) || ( ! $group->get( 'id' ) ) ) {
			return false;
		} elseif ( $user->get( 'id' ) == $group->get( 'user_id' ) ) {
			return 4;
		} elseif ( isset( $group->_user_status ) ) {
			$status				=	$group->get( '_user_status' );

			return ( $status !== null ? (int) $status : null );
		}

		static $users			=	array();

		$groupId				=	(int) $group->get( 'id' );

		if ( ! isset( $users[$groupId] ) ) {
			$query				=	'SELECT ' . $_CB_database->NameQuote( 'user_id' )
								.	', ' . $_CB_database->NameQuote( 'status' )
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $groupId;
			$_CB_database->setQuery( $query );
			$users[$groupId]	=	$_CB_database->loadAssocList( 'user_id', 'status' );
		}

		$userId					=	(int) $user->get( 'id' );

		return ( isset( $users[$groupId][$userId] ) ? $users[$groupId][$userId] : null );
	}

	/**
	 * @param UserTable   $user
	 * @param GroupTable  $group
	 * @return null|int
	 */
	static public function getGroupInvited( $user, $group )
	{
		global $_CB_database;

		if ( ( ! $user->get( 'id' ) ) || ( ! $group->get( 'id' ) ) || ( $user->get( 'id' ) == $group->get( 'user_id' ) ) ) {
			return null;
		} elseif ( isset( $group->_invite_id ) ) {
			return $group->get( '_invite_id' );
		}

		static $users					=	array();

		$userId							=	(int) $user->get( 'id' );
		$groupId						=	(int) $group->get( 'id' );

		if ( ! isset( $users[$userId][$groupId] ) ) {
			$query						=	'SELECT ' . $_CB_database->NameQuote( 'id' )
										.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_invites' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
										.	"\n AND " . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
										.	"\n AND ( ( " . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) )
										.	' AND ' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
										.	' OR ( ' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' )
										.	' AND ' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';
			$_CB_database->setQuery( $query, 0, 1 );
			$users[$userId][$groupId]	=	(int) $_CB_database->loadResult();
		}

		return $users[$userId][$groupId];
	}

	/**
	 * @param UserTable   $user
	 * @param GroupTable  $group
	 * @param null|string $param
	 * @param int         $status
	 * @return bool
	 */
	static public function canCreateGroupContent( $user, $group, $param = null, $status = 1 )
	{
		global $_CB_database, $_PLUGINS;

		if ( ( ! $user->get( 'id' ) ) || ( ! $group->get( 'id' ) ) ) {
			return false;
		} elseif ( self::isModerator( (int) $user->get( 'id' ) ) ) {
			return true;
		}

		static $params							=	null;

		if ( ! $params ) {
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params								=	$_PLUGINS->getPluginParams( $plugin );
		}

		static $cache							=	array();

		$id										=	md5( $user->get( 'id' ) . $group->get( 'id' ) . $param . $status );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]							=	false;

			if ( ! self::canAccessGroup( $group, $user ) ) {
				$cache[$id]						=	false;

				return false;
			}

			if ( ( $group->get( 'published' ) == -1 ) && $params->get( 'groups_create_approval', 0 ) ) {
				$cache[$id]						=	false;

				return false;
			} elseif ( $user->get( 'id' ) == $group->get( 'user_id' ) ) {
				$cache[$id]						=	true;

				return true;
			}

			$userStatus							=	self::getGroupStatus( $user, $group );

			if ( $userStatus >= $status ) {
				if ( $param && ( ! $group->params()->get( $param, 1 ) ) && ( $userStatus < 3 ) ) {
					$cache[$id]					=	false;

					return false;
				} elseif ( $param == 'invites' ) {
					$createLimit				=	(int) $params->get( 'groups_invites_create_limit', 0 );

					if ( $createLimit ) {
						static $count			=	array();

						$countId				=	md5( $user->get( 'id' ) . $group->get( 'id' ) );

						if ( ! isset( $count[$countId] ) ) {
							$query				=	'SELECT COUNT(*)'
												.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_invites' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
												.	"\n AND " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
												.	"\n AND " . $_CB_database->NameQuote( 'accepted' ) . " = " . $_CB_database->Quote( '0000-00-00 00:00:00' );
							$_CB_database->setQuery( $query );
							$count[$countId]	=	(int) $_CB_database->loadResult();
						}

						if ( $count[$countId] >= $createLimit ) {
							$cache[$id]			=	false;

							return false;
						}
					}
				}

				$access							=	true;

				$_PLUGINS->trigger( 'gj_onCanCreateGroupContent', array( &$access, $param, $group, $user ) );

				$cache[$id]						=	$access;

				return $access;
			}
		}

		return $cache[$id];
	}

	/**
	 * @param GroupTable  $group
	 * @param UserTable   $user
	 * @return bool
	 */
	static public function canAccessGroup( $group, $user )
	{
		global $_PLUGINS;

		if ( ! $group->get( 'id' ) ) {
			return false;
		} elseif ( CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
			return true;
		}

		static $params					=	null;

		if ( ! $params ) {
			$plugin						=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params						=	$_PLUGINS->getPluginParams( $plugin );
		}

		static $cache					=	array();

		$groupId						=	(int) $group->get( 'id' );
		$userId							=	(int) $user->get( 'id' );

		if ( ! isset( $cache[$userId][$groupId] ) ) {
			$access						=	true;

			if ( ( ! $group->category()->get( 'id' ) ) && ( ! $params->get( 'groups_uncategorized', 1 ) ) ) {
				$access					=	false;
			} elseif ( $group->category()->get( 'id' ) && ( ( ! $group->category()->get( 'published' ) ) || ( ! CBGroupJive::canAccess( (int) $group->category()->get( 'access' ), (int) $user->get( 'id' ) ) ) ) ) {
				$access					=	false;
			} elseif ( ( $group->get( 'published' ) != 1 ) && ( $user->get( 'id' ) != $group->get( 'user_id' ) ) ) {
				$access					=	false;
			} elseif ( $user->get( 'id' ) != $group->get( 'user_id' ) ) {
				$userStatus				=	CBGroupJive::getGroupStatus( $user, $group );

				if ( $userStatus == -1 ) {
					$access				=	false;
				} elseif ( ( $group->get( 'type' ) == 3 ) && ( ( $userStatus === false ) || ( $userStatus === null ) ) && ( ! CBGroupJive::getGroupInvited( $user, $group ) ) ) {
					$access				=	false;
				}
			}

			$cache[$userId][$groupId]	=	$access;
		}

		return $cache[$userId][$groupId];
	}

	/**
	 * @param null|int $userId
	 * @return array
	 */
	static public function getAccess( $userId = null )
	{
		static $cache			=	array();

		if ( $userId === null ) {
			$userId				=	Application::MyUser()->getUserId();
		}

		if ( ! isset( $cache[$userId] ) ) {
			$cache[$userId]		=	Application::User( (int) $userId )->getAuthorisedViewLevels();
		}

		return $cache[$userId];
	}

	/**
	 * @param int      $viewAccessLevel
	 * @param null|int $userId
	 * @return bool
	 */
	static public function canAccess( $viewAccessLevel, $userId = null )
	{
		static $cache							=	array();

		if ( $userId === null ) {
			$userId								=	Application::MyUser()->getUserId();
		}

		if ( ! isset( $cache[$userId][$viewAccessLevel] ) ) {
			$cache[$userId][$viewAccessLevel]	=	Application::User( (int) $userId )->canViewAccessLevel( (int) $viewAccessLevel );
		}

		return $cache[$userId][$viewAccessLevel];
	}

	/**
	 * @param null|int $userId
	 * @return bool
	 */
	static public function isModerator( $userId = null )
	{
		static $cache			=	array();

		if ( $userId === null ) {
			$userId				=	Application::MyUser()->getUserId();
		}

		if ( ! isset( $cache[$userId] ) ) {
			$cache[$userId]		=	Application::User( (int) $userId )->isGlobalModerator();
		}

		return $cache[$userId];
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
			$size							=	CBTxt::T( 'FILESIZE_FORMATTED_TB', '%%COUNT%% TB|%%COUNT%% TBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1099511627776, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1073741824 ) {
			$size							=	CBTxt::T( 'FILESIZE_FORMATTED_GB', '%%COUNT%% GB|%%COUNT%% GBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1073741824, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1048576 ) {
			$size							=	CBTxt::T( 'FILESIZE_FORMATTED_MB', '%%COUNT%% MB|%%COUNT%% MBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1048576, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1024 ) {
			$size							=	CBTxt::T( 'FILESIZE_FORMATTED_KB', '%%COUNT%% KB|%%COUNT%% KBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1024, 2, '.', '' ) ) );
		} else {
			$size							=	CBTxt::T( 'FILESIZE_FORMATTED_B', '%%COUNT%% B|%%COUNT%% Bs', array( '%%COUNT%%' => (float) number_format( $bytes, 2, '.', '' ) ) );
		}

		return $size;
	}

	/**
	 * Prefetches users
	 *
	 * @param TableInterface[] $rows
	 */
	static public function preFetchUsers( $rows )
	{
		if ( ! $rows ) {
			return;
		}

		$users					=	array();

		/** @var TableInterface[] $rows */
		foreach ( $rows as $row ) {
			if ( $row instanceof UserTable ) {
				$userId			=	(int) $row->get( 'id' );

				if ( $userId && ( ! in_array( $userId, $users ) ) ) {
					$users[]	=	$userId;
				}
			} else {
				$userId			=	(int) $row->get( 'user_id' );

				if ( $userId && ( ! in_array( $userId, $users ) ) ) {
					$users[]	=	$userId;
				}

				$userId			=	(int) $row->get( 'user' );

				if ( $userId && ( ! in_array( $userId, $users ) ) ) {
					$users[]	=	$userId;
				}
			}
		}

		if ( $users ) {
			\CBuser::advanceNoticeOfUsersNeeded( $users );
		}
	}

	/**
	 * Returns an options array of available categories
	 *
	 * @param bool $raw
	 * @return array|\stdClass[]
	 */
	static public function getCategoryOptions( $raw = false )
	{
		global $_CB_database, $_PLUGINS;

		if ( Application::Cms()->getClientId() ) {
			$raw				=	false;
		}

		static $params			=	null;

		if ( ! $params ) {
			$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params				=	$_PLUGINS->getPluginParams( $plugin );
		}

		static $cache			=	array();

		$userId					=	Application::MyUser()->getUserId();

		if ( ! isset( $cache[$userId] ) ) {
			$query				=	'SELECT ' . $_CB_database->NameQuote( 'id' ) . ' AS value'
								.	', ' . $_CB_database->NameQuote( 'name' ) . ' AS text'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_categories' );

			if ( ( ! self::isModerator( $userId ) ) && ( ! Application::Cms()->getClientId() ) ) {
				$query			.=	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
								.	"\n AND " . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( self::getAccess( $userId ) )
								.	"\n AND ( " . $_CB_database->NameQuote( 'create_access' ) . " = 0"
								.		' OR ' . $_CB_database->NameQuote( 'create_access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( self::getAccess( $userId ) ) . ' )';
			}

			$query				.=	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$cache[$userId]		=	$_CB_database->loadObjectList();
		}

		if ( $raw === true ) {
			return $cache[$userId];
		}

		$options				=	array();

		if ( Application::Cms()->getClientId() ) {
			$options[]			=	\moscomprofilerHTML::makeOption( 0, CBTxt::T( 'Uncategorized' ) );
		}

		foreach ( $cache[$userId] as $category ) {
			$options[]			=	\moscomprofilerHTML::makeOption( (int) $category->value, CBTxt::T( $category->text ) );
		}

		return $options;
	}

	/**
	 * Returns an options array of available groups
	 *
	 * @param bool  $raw
	 * @param array $excludeCategories
	 * @param array $excludeGroups
	 * @return array|\stdClass[]
	 */
	static public function getGroupOptions( $raw = false, $excludeCategories = array(), $excludeGroups = array() )
	{
		global $_CB_database;

		if ( Application::Cms()->getClientId() ) {
			$raw					=	false;
			$excludeCategories		=	array();
			$excludeGroups			=	array();
		}

		static $cache				=	array();

		$userId						=	Application::MyUser()->getUserId();

		if ( ! isset( $cache[$userId] ) ) {
			$query					=	'SELECT g.' . $_CB_database->NameQuote( 'id' ) . ' AS value'
									.	', g.' . $_CB_database->NameQuote( 'name' ) . ' AS text'
									.	', g.' . $_CB_database->NameQuote( 'category' )
									.	', c.' . $_CB_database->NameQuote( 'name' ) . ' AS category_name'
									.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c"
									.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'category' );

			if ( ( ! self::isModerator( $userId ) ) && ( ! Application::Cms()->getClientId() ) ) {
				$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
									.	' ON u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
									.	' AND u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $userId
									.	' AND u.' . $_CB_database->NameQuote( 'status' ) . ' >= 1'
									.	"\n WHERE c." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND c." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( self::getAccess( $userId ) )
									.	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId
									.		' OR ( ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )'
									.		' AND ( ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 ) )'
									.		' OR ( u.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) ) )'
									.	( $excludeCategories ? "\n AND c." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeCategories ) : null )
									.	( $excludeGroups ? "\n AND g." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeGroups ) : null );
			} else {
				$query				.=	( $excludeCategories ? "\n WHERE c." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeCategories ) : null )
									.	( $excludeGroups ? "\n " . ( $excludeCategories ? 'AND' : 'WHERE' ) . " g." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeGroups ) : null );
			}

			$query					.=	"\n ORDER BY c." . $_CB_database->NameQuote( 'ordering' ) . ", g." . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$cache[$userId]			=	$_CB_database->loadObjectList();
		}

		if ( $raw === true ) {
			return $cache[$userId];
		}

		$optGroups					=	array();
		$options					=	array();

		foreach ( $cache[$userId] as $group ) {
			$category				=	(int) $group->category;

			if ( ! in_array( $category, $optGroups ) ) {
				$options[]			=	\moscomprofilerHTML::makeOptGroup( ( $category ? CBTxt::T( $group->category_name ) : CBTxt::T( 'Uncategorized' ) ) );

				$optGroups[]		=	$category;
			}

			$options[]				=	\moscomprofilerHTML::makeOption( (int) $group->value, CBTxt::T( $group->text ) );
		}

		return $options;
	}
}