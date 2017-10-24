<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CBLib\Application\Application;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Table\ActionTable;
use CB\Plugin\Activity\Table\LocationTable;
use CB\Plugin\Activity\Table\EmoteTable;
use CBLib\Database\Table\TableInterface;
use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class CBActivity
{

	/**
	 * @param null|array $files
	 * @param bool       $loadGlobal
	 * @param bool       $loadHeader
	 * @param bool       $loadPHP
	 */
	static public function getTemplate( $files = null, $loadGlobal = true, $loadHeader = true, $loadPHP = true )
	{
		global $_CB_framework, $_PLUGINS;

		static $tmpl							=	array();

		if ( ! $files ) {
			$files								=	array();
		} elseif ( ! is_array( $files ) ) {
			$files								=	array( $files );
		}

		$id										=	md5( serialize( array( $files, $loadGlobal, $loadHeader, $loadPHP ) ) );

		if ( ! isset( $tmpl[$id] ) ) {
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );

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
					if ( $loadPHP ) {
						if ( ! file_exists( $php ) ) {
							$php				=	$absPath . '/templates/default/' . $file . '.php';
						}

						if ( file_exists( $php ) ) {
							require_once( $php );

							$paths['php']		=	$php;
						}
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
	 * Loads and caches headers for initial output
	 *
	 * @param int $output 0: Normal, 1: Raw, 2: Inline, 3: Load , 4: Save
	 */
	static public function loadHeaders( $output )
	{
		global $_CB_framework;

		if ( $output != 0 ) {
			return;
		}

		static $loaded	=	0;

		if ( ! $loaded++ ) {
			self::getTemplate( array( 'activity', 'comments', 'tags', 'twemoji' ), true, true, false );

			initToolTip();

			$_CB_framework->addJQueryPlugin( 'cbactivity', '/components/com_comprofiler/plugin/user/plug_cbactivity/js/jquery.cbactivity.js' );

			$_CB_framework->outputCbJQuery( null, array( 'form', 'cbmoreless', 'cbrepeat', 'cbselect', 'autosize', 'cbtimeago', 'cbactivity' ) );
		}
	}

	/**
	 * Reloads page headers for ajax responses
	 *
	 * @param int $output 0: Normal, 1: Raw, 2: Inline, 3: Load , 4: Save
	 * @return null|string
	 */
	static public function reloadHeaders( $output )
	{
		global $_CB_framework;

		if ( ! in_array( $output, array( 1, 3, 4 ) ) ) {
			return null;
		}

		// Inform the header of core jQuery plugins already loaded by loadHeaders:
		$preLoaded											=	array(	'ui-all', 'form', 'cbmoreless',
																		'cbrepeat', 'cbselect', 'select2',
																		'multiple.select', 'autosize',
																		'qtip', 'cbtooltip', 'livestamp',
																		'cbtimeago', 'cbactivity', '/components/com_comprofiler/plugin/templates/default/jquery/qtip/qtip.css'
																	);

		foreach ( $preLoaded as $loaded ) {
			$_CB_framework->_jQueryPluginsSent[$loaded]		=	true;
		}

		$_CB_framework->getAllJsPageCodes();

		// Reset meta headers as they can't be used inline anyway:
		$_CB_framework->document->_head['metaTags']			=	array();

		// Remove all non-jQuery scripts as they'll likely just cause errors due to redeclaration:
		foreach( $_CB_framework->document->_head['scriptsUrl'] as $url => $script ) {
			if ( ( strpos( $url, 'jquery.' ) === false ) || ( strpos( $url, 'migrate' ) !== false ) ) {
				unset( $_CB_framework->document->_head['scriptsUrl'][$url] );
			}
		}

		$return				=	null;

		if ( $output == 4 ) {
			$return			.=	'<div class="streamItemHeaders">';
		}

		$return				.=	$_CB_framework->document->outputToHead();

		if ( $output == 4 ) {
			$return			.=	'</div>';
		}

		return $return;
	}

	/**
	 * @param UserTable       $user
	 * @param UserTable       $viewer
	 * @param StreamInterface $stream
	 * @return bool
	 */
	static public function canCreate( $user, $viewer, $stream )
	{
		global $_PLUGINS;

		if ( ! $viewer->get( 'id' ) ) {
			return false;
		}

		$createAccess			=	(int) $stream->get( 'create_access', 2 );

		if ( $createAccess == -1 ) {
			return false;
		}

		if ( self::isModerator( (int) $viewer->get( 'id' ) ) ) {
			return true;
		}

		$access					=	false;

		if ( self::canAccess( $createAccess, (int) $viewer->get( 'id' ) ) ) {
			if ( ( $viewer->get( 'id' ) != $user->get( 'id' ) ) && Application::Config()->get( 'allowConnections' ) ) {
				if ( self::isConnected( $viewer->get( 'id' ), $user->get( 'id' ) ) ) {
					$access		=	true;
				}
			} else {
				$access			=	true;
			}
		}

		$_PLUGINS->trigger( 'activity_onStreamCreateAccess', array( &$access, $user, $viewer, $stream ) );

		return $access;
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
	 * Checks if two users are completely conntected (accepted and not pending)
	 *
	 * @param int $fromUser
	 * @param int $toUser
	 *
	 * @return bool
	 */
	static public function isConnected( $fromUser, $toUser )
	{
		static $cache				=	array();

		if ( ! isset( $cache[$fromUser][$toUser] ) ) {
			if ( Application::Config()->get( 'allowConnections' ) ) {
				$cbConnection			=	new \cbConnection( $fromUser );
				$details				=	$cbConnection->getConnectionDetails( $fromUser, $toUser );

				$connected				=	( ( $details !== false ) && ( $details->get( 'pending' ) == 0 ) && ( $details->get( 'accepted' ) == 1 ) ? true : false );
			} else {
				$connected				=	false;
			}

			$cache[$fromUser][$toUser]	=	$connected;
		}

		return $cache[$fromUser][$toUser];
	}

	/**
	 * Loads stream default params (from a standardized format) from a params object to override globals
	 *
	 * @param StreamInterface $stream
	 * @param ParamsInterface $params
	 * @param string          $prefix
	 */
	static public function loadStreamDefaults( &$stream, $params, $prefix = null )
	{
		if ( $stream instanceof ActivityInterface ) {
			// Activity
			$paging								=	(string) $params->get( $prefix . 'paging', '' );

			if ( $paging != '' ) {
				$stream->set( 'paging', (int) $paging );
			}

			$limit								=	(string) $params->get( $prefix . 'limit', '' );

			if ( $limit != '' ) {
				$stream->set( 'limit', (int) $limit );
			}

			$createAccess						=	(string) $params->get( $prefix . 'create_access', '' );

			if ( $createAccess != '' ) {
				$stream->set( 'create_access', (int) $createAccess );
			}

			$messageLimit						=	(string) $params->get( $prefix . 'message_limit', '' );

			if ( $messageLimit != '' ) {
				$stream->set( 'message_limit', (int) $messageLimit );
			}

			// Actions
			$actions							=	(string) $params->get( $prefix . 'actions', '' );

			if ( $actions != '' ) {
				$stream->set( 'actions', (int) $actions );
			}

			$actionsMessageLimit				=	(string) $params->get( $prefix . 'actions_message_limit', '' );

			if ( $actionsMessageLimit != '' ) {
				$stream->set( 'actions_message_limit', (int) $actionsMessageLimit );
			}

			// Locations
			$locations							=	(string) $params->get( $prefix . 'locations', '' );

			if ( $locations != '' ) {
				$stream->set( 'locations', (int) $locations );
			}

			$locationsAddressLimit				=	(string) $params->get( $prefix . 'locations_address_limit', '' );

			if ( $locationsAddressLimit != '' ) {
				$stream->set( 'locations_address_limit', (int) $locationsAddressLimit );
			}

			// Links
			$links								=	(string) $params->get( $prefix . 'links', '' );

			if ( $links != '' ) {
				$stream->set( 'links', (int) $links );
			}

			$linksLinkLimit						=	(string) $params->get( $prefix . 'links_link_limit', '' );

			if ( $linksLinkLimit != '' ) {
				$stream->set( 'links_link_limit', (int) $linksLinkLimit );
			}

			// Tags
			$tags								=	(string) $params->get( $prefix . 'tags', '' );

			if ( $tags != '' ) {
				$stream->set( 'tags', (int) $tags );
			}

			// Comments
			$comments							=	(string) $params->get( $prefix . 'comments', '' );

			if ( $comments != '' ) {
				$stream->set( 'comments', (int) $comments );
			}

			$commentsPaging						=	(string) $params->get( $prefix . 'comments_paging', '' );

			if ( $commentsPaging != '' ) {
				$stream->set( 'comments_paging', (int) $commentsPaging );
			}

			$commentsLimit						=	(string) $params->get( $prefix . 'comments_limit', '' );

			if ( $commentsLimit != '' ) {
				$stream->set( 'comments_limit', (int) $commentsLimit );
			}

			$commentsCreateAccess				=	(string) $params->get( $prefix . 'comments_create_access', '' );

			if ( $commentsCreateAccess != '' ) {
				$stream->set( 'comments_create_access', (int) $commentsCreateAccess );
			}

			$commentsMessageLimit				=	(string) $params->get( $prefix . 'comments_message_limit', '' );

			if ( $commentsMessageLimit != '' ) {
				$stream->set( 'comments_message_limit', (int) $commentsMessageLimit );
			}

			// Comment Replies
			$commentsReplies					=	(string) $params->get( $prefix . 'comments_replies', '' );

			if ( $commentsReplies != '' ) {
				$stream->set( 'comments_replies', (int) $commentsReplies );
			}

			$commentsRepliesPaging				=	(string) $params->get( $prefix . 'comments_replies_paging', '' );

			if ( $commentsRepliesPaging != '' ) {
				$stream->set( 'comments_replies_paging', (int) $commentsRepliesPaging );
			}

			$commentsRepliesLimit				=	(string) $params->get( $prefix . 'comments_replies_limit', '' );

			if ( $commentsRepliesLimit != '' ) {
				$stream->set( 'comments_replies_limit', (int) $commentsRepliesLimit );
			}

			$commentsRepliesCreateAccess		=	(string) $params->get( $prefix . 'comments_replies_create_access', '' );

			if ( $commentsRepliesCreateAccess != '' ) {
				$stream->set( 'comments_replies_create_access', (int) $commentsRepliesCreateAccess );
			}

			$commentsRepliesMessageLimit		=	(string) $params->get( $prefix . 'comments_replies_message_limit', '' );

			if ( $commentsRepliesMessageLimit != '' ) {
				$stream->set( 'comments_replies_message_limit', (int) $commentsRepliesMessageLimit );
			}
		} elseif ( $stream instanceof CommentsInterface ) {
			// Comments
			$paging								=	(string) $params->get( $prefix . 'paging', '' );

			if ( $paging != '' ) {
				$stream->set( 'paging', (int) $paging );
			}

			$limit								=	(string) $params->get( $prefix . 'limit', '' );

			if ( $limit != '' ) {
				$stream->set( 'limit', (int) $limit );
			}

			$createAccess						=	(string) $params->get( $prefix . 'create_access', '' );

			if ( $createAccess != '' ) {
				$stream->set( 'create_access', (int) $createAccess );
			}

			$messageLimit						=	(string) $params->get( $prefix . 'message_limit', '' );

			if ( $messageLimit != '' ) {
				$stream->set( 'message_limit', (int) $messageLimit );
			}

			// Comment Replies
			$replies							=	(string) $params->get( $prefix . 'replies', '' );

			if ( $replies != '' ) {
				$stream->set( 'replies', (int) $replies );
			}

			$repliesPaging						=	(string) $params->get( $prefix . 'replies_paging', '' );

			if ( $repliesPaging != '' ) {
				$stream->set( 'replies_paging', (int) $repliesPaging );
			}

			$repliesLimit						=	(string) $params->get( $prefix . 'replies_limit', '' );

			if ( $repliesLimit != '' ) {
				$stream->set( 'replies_limit', (int) $repliesLimit );
			}

			$repliesCreateAccess				=	(string) $params->get( $prefix . 'replies_create_access', '' );

			if ( $repliesCreateAccess != '' ) {
				$stream->set( 'replies_create_access', (int) $repliesCreateAccess );
			}

			$repliesMessageLimit				=	(string) $params->get( $prefix . 'replies_message_limit', '' );

			if ( $repliesMessageLimit != '' ) {
				$stream->set( 'replies_message_limit', (int) $repliesMessageLimit );
			}
		}
	}

	/**
	 * Returns internal clean up urls
	 *
	 * @return string
	 */
	static public function loadCleanUpURL()
	{
		global $_CB_framework;

		return '<a href="' . $_CB_framework->pluginClassUrl( 'cbactivity', true, array( 'action' => 'cleanup', 'token' => md5( $_CB_framework->getCfg( 'secret' ) ) ), 'raw', 0, true ) . '" target="_blank">' . CBTxt::T( 'Click to Process' ) . '</a>';
	}

	/**
	 * Returns an options array of types for exclude
	 *
	 * @return array
	 */
	static public function loadExcludeOptions()
	{
		global $_CB_database;

		static $options			=	null;

		if ( $options === null ) {
			$options			=	array();

			$query				=	'SELECT DISTINCT ' . $_CB_database->NameQuote( 'type' )
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " != " . $_CB_database->Quote( '' )
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'type' );
			$_CB_database->setQuery( $query );
			$types				=	$_CB_database->loadResultArray();

			foreach ( $types as $type ) {
				$options[]		=	\moscomprofilerHTML::makeOption( $type, $type );

				$query			=	'SELECT DISTINCT ' . $_CB_database->NameQuote( 'subtype' )
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type )
								.	"\n AND " . $_CB_database->NameQuote( 'type' ) . " != " . $_CB_database->Quote( '' )
								.	"\n AND " . $_CB_database->NameQuote( 'subtype' ) . " != " . $_CB_database->Quote( '' )
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'type' );
				$_CB_database->setQuery( $query );
				$subTypes		=	$_CB_database->loadResultArray();

				foreach ( $subTypes as $subType ) {
					$options[]	=	\moscomprofilerHTML::makeOption( $type . ',' . $subType, ' - ' . $subType );
				}
			}
		}

		return $options;
	}

	/**
	 * Returns an options array of available actions
	 *
	 * @param bool $raw
	 * @return array|ActionTable[]
	 */
	static public function loadActionOptions( $raw = false )
	{
		global $_CB_database;

		static $options			=	null;
		static $actions			=	null;

		if ( $actions === null ) {
			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_actions' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$actions			=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\ActionTable', array( $_CB_database ) );
		}

		if ( $raw ) {
			return $actions;
		}

		if ( $options === null ) {
			$options			=	array();
			$options[]			=	\moscomprofilerHTML::makeOption( 0, '&nbsp;', 'value', 'text', null, null, 'data-cbactivity-option-icon="' . htmlspecialchars( '<span class="fa fa-times"></span>' ) . '"' );

			/** @var ActionTable[] $actions */
			foreach ( $actions as $action ) {
				$options[]		=	\moscomprofilerHTML::makeOption( (int) $action->get( 'id' ), CBTxt::T( $action->get( 'value' ) ), 'value', 'text', null, null, ( $action->icon() ? ' data-cbactivity-option-icon="' . htmlspecialchars( $action->icon() ) . '"' : null ) . ( $action->get( 'description' ) ? ' data-cbactivity-toggle-placeholder="' . htmlspecialchars( $action->get( 'description' ) ) . '"' : null ) );
			}
		}

		return $options;
	}

	/**
	 * Returns an options array of available locations
	 *
	 * @param bool $raw
	 * @return array|ActionTable[]
	 */
	static public function loadLocationOptions( $raw = false )
	{
		global $_CB_database;

		static $options			=	null;
		static $locations		=	null;

		if ( $locations === null ) {
			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_locations' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$locations			=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\LocationTable', array( $_CB_database ) );
		}

		if ( $raw ) {
			return $locations;
		}

		if ( $options === null ) {
			$options			=	array();
			$options[]			=	\moscomprofilerHTML::makeOption( 0, '&nbsp;', 'value', 'text', null, null, 'data-cbactivity-option-icon="' . htmlspecialchars( '<span class="fa fa-times"></span>' ) . '"' );

			/** @var LocationTable[] $locations */
			foreach ( $locations as $location ) {
				$options[]		=	\moscomprofilerHTML::makeOption( (int) $location->get( 'id' ), CBTxt::T( $location->get( 'value' ) ) );
			}
		}

		return $options;
	}

	/**
	 * Returns an options array of available user tags with optional activity specific
	 *
	 * @param null|int|ActivityTable $activityId
	 * @param null|int               $userId
	 * @return array
	 */
	static public function loadTagOptions( $activityId = null, $userId = null )
	{
		global $_CB_database;

		/** @var ActivityTable[] $cache */
		static $cache							=	array();

		if ( $activityId && ( $userId === null ) ) {
			if ( $activityId instanceof ActivityTable ) {
				$activity						=	$activityId;
				$activityId						=	(int) $activity->get( 'id' );
			} else {
				if ( ! isset( $cache[$activityId] ) ) {
					$activity					=	new ActivityTable();

					$activity->load( (int) $activityId );

					$cache[$activityId]			=	$activity;
				}

				$activity						=	$cache[$activityId];
			}

			$userId								=	(int) $activity->get( 'user_id' );
		} elseif ( $userId === null ) {
			$userId								=	Application::MyUser()->getUserId();
		}

		static $connections						=	array();
		static $custom							=	array();
		static $options							=	array();

		if ( ! isset( $options[$userId][$activityId] ) ) {
			if ( ! isset( $connections[$userId] ) ) {
				$connectionOptions				=	array();

				if ( Application::Config()->get( 'allowConnections' ) ) {
					$cbConnection				=	new \cbConnection( $userId );

					foreach( $cbConnection->getConnectedToMe( $userId ) as $connection ) {
						$connectionOptions[]	=	\moscomprofilerHTML::makeOption( (string) $connection->id, getNameFormat( $connection->name, $connection->username, Application::Config()->get( 'name_format', 3 ) ) );
					}
				}

				$connections[$userId]			=	$connectionOptions;
			}

			if ( ! isset( $custom[$activityId] ) ) {
				$activityOptions				=	array();

				if ( $activityId ) {
					$exclude					=	array();

					foreach ( $connections[$userId] as $connection ) {
						$exclude[]				=	$connection->value;
					}

					$query						=	'SELECT *'
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_tags' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'activity' )
												.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " = " . (int) $activityId
												.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " ASC";
					$_CB_database->setQuery( $query );
					$tags						=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\TagTable', array( $_CB_database ) );

					/** @var TagTable[] $tags */
					foreach ( $tags as $tag ) {
						if ( ! in_array( $tag->get( 'user' ), $exclude ) ) {
							$activityOptions[]	=	\moscomprofilerHTML::makeOption( (string) $tag->get( 'user' ), $tag->get( 'user' ) );
						}
					}
				}

				$custom[$activityId]			=	$activityOptions;
			}

			$options[$userId][$activityId]		=	array_merge( $connections[$userId], $custom[$activityId] );
		}

		return $options[$userId][$activityId];
	}

	/**
	 * Returns an options array of available emotes
	 *
	 * @param bool $substitutions
	 * @param bool $raw
	 * @return array|EmoteTable[]
	 */
	static public function loadEmoteOptions( $substitutions = false, $raw = false )
	{
		global $_CB_database;

		static $cache				=	array();
		static $emotes				=	null;

		if ( $emotes === null ) {
			$query					=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_emotes' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$emotes					=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\EmoteTable', array( $_CB_database ) );
		}

		if ( $raw ) {
			return $emotes;
		}

		if ( ! isset( $cache[$substitutions] ) ) {
			$options				=	array();

			if ( $substitutions !== true ) {
				$options[]			=	\moscomprofilerHTML::makeOption( 0, '&nbsp;', 'value', 'text', null, null, 'data-cbactivity-option-icon="' . htmlspecialchars( '<span class="fa fa-smile-o fa-lg"></span>' ) . '"' );
			}

			/** @var EmoteTable[] $emotes */
			foreach ( $emotes as $emote ) {
				if ( $substitutions === true ) {
					$key			=	':' . $emote->get( 'value' ) . ':';

					$options[$key]	=	$emote->icon();
				} else {
					$options[]		=	\moscomprofilerHTML::makeOption( (int) $emote->get( 'id' ), '&nbsp;', 'value', 'text', null, null, ' data-cbactivity-option-icon="' . htmlspecialchars( $emote->icon() ) . '"' );
				}
			}

			$cache[$substitutions]	=	$options;
		}

		return $cache[$substitutions];
	}

	/**
	 * Prefetches activity data
	 *
	 * @param TableInterface[] $rows
	 * @param string           $type
	 */
	static public function preFetchActivity( &$rows, $type )
	{
		global $_CB_database;

		if ( ( ! $rows ) || ( ! $type ) ) {
			return;
		}

		static $cache			=	array();

		$rowIds					=	array();

		foreach ( $rows as $row ) {
			if ( ! $row->get( 'item' ) ) {
				$rowIds[]		=	(int) $row->get( 'id' );
			}
		}

		$activity				=	array();

		if ( $rowIds ) {
			$id					=	md5( $type . ':' . implode( '|*|', $rowIds ) );

			if ( ! isset( $cache[$id] ) ) {
				$query			=	'SELECT ' . $_CB_database->NameQuote( 'item' ) . ', COUNT(*) AS count'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type )
								.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " IN " . $_CB_database->safeArrayOfIntegers( $rowIds )
								.	"\n GROUP BY " . $_CB_database->NameQuote( 'type' ) . ", " . $_CB_database->NameQuote( 'item' );
				$_CB_database->setQuery( $query );
				$cache[$id]		=	$_CB_database->loadAssocList( 'item', 'count' );
			}

			$activity			=	$cache[$id];
		}

		foreach ( $rows as $row ) {
			$rowId				=	(int) $row->get( 'id' );

			if ( $row->get( 'item' ) ) {
				$row->set( '_activity', true );
			} else {
				$row->set( '_activity', ( isset( $activity[$rowId] ) ? (int) $activity[$rowId] : 0 ) );
			}
		}
	}

	/**
	 * Prefetches comment data
	 *
	 * @param TableInterface[] $rows
	 * @param string           $type
	 */
	static public function preFetchComments( &$rows, $type )
	{
		global $_CB_database;

		if ( ( ! $rows ) || ( ! $type ) ) {
			return;
		}

		static $cache			=	array();

		$rowIds					=	array();

		foreach ( $rows as $row ) {
			if ( ! $row->get( 'item' ) ) {
				$rowIds[]		=	(int) $row->get( 'id' );
			}
		}

		$comments				=	array();

		if ( $rowIds ) {
			$id					=	md5( $type . ':' . implode( '|*|', $rowIds ) );

			if ( ! isset( $cache[$id] ) ) {
				$query			=	'SELECT ' . $_CB_database->NameQuote( 'item' ) . ', COUNT(*) AS count'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type )
								.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " IN " . $_CB_database->safeArrayOfIntegers( $rowIds )
								.	"\n GROUP BY " . $_CB_database->NameQuote( 'type' ) . ", " . $_CB_database->NameQuote( 'item' );
				$_CB_database->setQuery( $query );
				$cache[$id]		=	$_CB_database->loadAssocList( 'item', 'count' );
			}

			$comments			=	$cache[$id];
		}

		foreach ( $rows as $row ) {
			$rowId				=	(int) $row->get( 'id' );

			if ( $row->get( 'item' ) ) {
				$row->set( '_comments', true );
			} else {
				$row->set( '_comments', ( isset( $comments[$rowId] ) ? (int) $comments[$rowId] : 0 ) );
			}
		}
	}

	/**
	 * Prefetches tag data
	 *
	 * @param TableInterface[] $rows
	 * @param string           $type
	 */
	static public function preFetchTags( &$rows, $type )
	{
		global $_CB_database;

		if ( ( ! $rows ) || ( ! $type ) ) {
			return;
		}

		static $cache			=	array();

		$rowIds					=	array();

		foreach ( $rows as $row ) {
			if ( ! $row->get( 'item' ) ) {
				$rowIds[]		=	(int) $row->get( 'id' );
			}
		}

		$tags					=	array();

		if ( $rowIds ) {
			$id					=	md5( $type . ':' . implode( '|*|', $rowIds ) );

			if ( ! isset( $cache[$id] ) ) {
				$query			=	'SELECT ' . $_CB_database->NameQuote( 'item' ) . ', COUNT(*) AS count'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_tags' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type )
								.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " IN " . $_CB_database->safeArrayOfIntegers( $rowIds )
								.	"\n GROUP BY " . $_CB_database->NameQuote( 'type' ) . ", " . $_CB_database->NameQuote( 'item' );
				$_CB_database->setQuery( $query );
				$cache[$id]		=	$_CB_database->loadAssocList( 'item', 'count' );
			}

			$tags				=	$cache[$id];
		}

		foreach ( $rows as $row ) {
			$rowId				=	(int) $row->get( 'id' );

			if ( $row->get( 'item' ) ) {
				$row->set( '_tags', true );
			} else {
				$row->set( '_tags', ( isset( $tags[$rowId] ) ? (int) $tags[$rowId] : 0 ) );
			}
		}
	}

	/**
	 * Prefetches users
	 *
	 * @param TableInterface[] $rows
	 */
	static public function preFetchUsers( &$rows )
	{
		if ( ! $rows ) {
			return;
		}

		$users			=	array();

		/** @var TableInterface[] $rows */
		foreach ( $rows as $row ) {
			$users[]	=	(int) $row->get( 'user_id' );
		}

		$users			=	array_unique( $users );

		if ( $users ) {
			\CBuser::advanceNoticeOfUsersNeeded( $users );
		}
	}
}
