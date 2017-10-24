<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CB\Database\Table\PluginTable;
use CB\Database\Table\TabTable;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

function plug_cbgroupjive_install()
{
	global $_CB_database, $_PLUGINS;

	// Uninstall the old integrations to avoid conflicts:
	$integrations									=	array(	'cbgroupjiveabout', 'cbgroupjiveevents', 'cbgroupjivefile',
																'cbgroupjiveforums', 'cbgroupjivephoto', 'cbgroupjivevideo',
																'cbgroupjivewall', 'cbgroupjiveauto'
															);

	foreach ( $integrations as $integration ) {
		$plugin										=	new PluginTable();

		$plugin->load( array( 'element' => $integration ) );

		if ( $plugin->get( 'id' ) && ( ! is_dir( $_PLUGINS->getPluginPath( $plugin ) . '/xml' ) ) ) {
			$plugin->delete();
		}
	}

	// Migrate categories:
	$table											=	'#__groupjive_categories';
	$fields											=	$_CB_database->getTableFields( $table );
	$migrate										=	false;

	if ( isset( $fields[$table]['parent'] ) ) {
		$migrate									=	true;

		$query										=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_categories' );
		$_CB_database->setQuery( $query );
		$categories									=	$_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( $_CB_database, '#__groupjive_categories', 'id' ) );

		/** @var $categories Table[] */
		foreach ( $categories as $category ) {
			$category->set( 'access', ( $category->get( 'access' ) == -2 ? 1 : ( $category->get( 'access' ) == -1 ? 2 : Application::CmsPermissions()->convertOldGroupToViewAccessLevel( $category->get( 'access' ), 'CB GroupJive: Category Access - ' . (int) $category->get( 'id' ) ) ) ) );
			$category->set( 'create_access', ( ! $category->get( 'create' ) ? -1 : ( $category->get( 'create_access' ) == -1 ? 2 : Application::CmsPermissions()->convertOldGroupToViewAccessLevel( $category->get( 'create_access' ), 'CB GroupJive: Category Create Access - ' . (int) $category->get( 'id' ) ) ) ) );

			$categoryParams							=	new Registry( $category->get( 'params' ) );

			// CB GroupJive Forums:
			$categoryParams->set( 'forums', $categoryParams->get( 'forum_show' ) );

			$category->set( 'params', $categoryParams->asJson() );

			$category->store();
		}

		// Remove the old columns:
		$_CB_database->dropColumn( $table, 'parent' );
		$_CB_database->dropColumn( $table, 'create' );
		$_CB_database->dropColumn( $table, 'nested' );
		$_CB_database->dropColumn( $table, 'nested_access' );
	}

	// Migrate groups:
	$table											=	'#__groupjive_groups';
	$fields											=	$_CB_database->getTableFields( $table );

	if ( isset( $fields[$table]['parent'] ) ) {
		$migrate									=	true;

		$query										=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' );
		$_CB_database->setQuery( $query );
		$groups										=	$_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( $_CB_database, '#__groupjive_groups', 'id' ) );

		/** @var $groups Table[] */
		foreach ( $groups as $group ) {
			$groupParams							=	new Registry( $group->get( 'params' ) );

			// Core:
			$groupParams->set( 'invites', ( $group->get( 'invite' ) > 0 ? 0 : 1 ) );

			// CB GroupJive Events:
			$groupParams->set( 'events', ( $groupParams->get( 'events_approve' ) ? 2 : $groupParams->get( 'events_show' ) ) );

			// CB GroupJive File:
			$groupParams->set( 'file', ( $groupParams->get( 'file_approve' ) ? 2 : $groupParams->get( 'file_show' ) ) );

			// CB GroupJive Forums:
			$groupParams->set( 'forums', $groupParams->get( 'forum_show' ) );

			// CB GroupJive Photo:
			$groupParams->set( 'photo', ( $groupParams->get( 'photo_approve' ) ? 2 : $groupParams->get( 'photo_show' ) ) );

			// CB GroupJive Video:
			$groupParams->set( 'video', ( $groupParams->get( 'video_approve' ) ? 2 : $groupParams->get( 'video_show' ) ) );

			// CB GroupJive Wall:
			$groupParams->set( 'wall', ( $groupParams->get( 'wall_approve' ) ? 2 : $groupParams->get( 'wall_show' ) ) );

			$group->set( 'params', $groupParams->asJson() );

			$group->store();
		}

		// Remove the old columns:
		$_CB_database->dropColumn( $table, 'parent' );
		$_CB_database->dropColumn( $table, 'access' );
		$_CB_database->dropColumn( $table, 'invite' );
		$_CB_database->dropColumn( $table, 'users' );
		$_CB_database->dropColumn( $table, 'nested' );
		$_CB_database->dropColumn( $table, 'nested_access' );
	}

	// Migrate notifications:
	$table											=	'#__groupjive_notifications';
	$fields											=	$_CB_database->getTableFields( $table );

	if ( isset( $fields[$table]['type'] ) ) {
		$migrate									=	true;

		// Delete notification types no longer supported:
		$query										=	'DELETE'
													.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_notifications' )
													.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " != " . $_CB_database->Quote( 'group' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migration notification parameters:
		$query										=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_notifications' );
		$_CB_database->setQuery( $query );
		$notifications								=	$_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( $_CB_database, '#__groupjive_notifications', 'id' ) );

		/** @var $notifications Table[] */
		foreach ( $notifications as $notification ) {
			if ( ( $notification->get( 'type' ) == 'group' ) && $notification->get( 'item' ) ) {
				$notification->set( 'group', (int) $notification->get( 'item' ) );

				$notificationParams					=	new Registry( $notification->get( 'params' ) );

				// Core:
				$notificationParams->set( 'user_join', $notificationParams->get( 'group_userjoin' ) );
				$notificationParams->set( 'user_leave', $notificationParams->get( 'group_userleave' ) );
				$notificationParams->set( 'user_approve', $notificationParams->get( 'group_userapprove' ) );
				$notificationParams->set( 'invite_accept', $notificationParams->get( 'group_inviteaccept' ) );

				// CB GroupJive Events:
				$notificationParams->set( 'event_new', $notificationParams->get( 'group_eventnew' ) );
				$notificationParams->set( 'event_approve', $notificationParams->get( 'group_eventapprove' ) );
				$notificationParams->set( 'event_attend', $notificationParams->get( 'group_eventyes' ) );
				$notificationParams->set( 'event_unattend', $notificationParams->get( 'group_eventno' ) );

				// CB GroupJive File:
				$notificationParams->set( 'file_new', $notificationParams->get( 'group_filenew' ) );
				$notificationParams->set( 'file_approve', $notificationParams->get( 'group_fileapprove' ) );

				// CB GroupJive Photo:
				$notificationParams->set( 'photo_new', $notificationParams->get( 'group_photonew' ) );
				$notificationParams->set( 'photo_approve', $notificationParams->get( 'group_photoapprove' ) );

				// CB GroupJive Video:
				$notificationParams->set( 'video_new', $notificationParams->get( 'group_videonew' ) );
				$notificationParams->set( 'video_approve', $notificationParams->get( 'group_videoapprove' ) );

				// CB GroupJive Wall:
				$notificationParams->set( 'wall_new', $notificationParams->get( 'group_wallnew' ) );
				$notificationParams->set( 'wall_approve', $notificationParams->get( 'group_wallapprove' ) );
				$notificationParams->set( 'wall_reply', $notificationParams->get( 'group_wallreply' ) );

				$notification->set( 'params', $notificationParams->asJson() );

				$notification->store();
			}
		}

		// Remove the old columns:
		$_CB_database->dropColumn( $table, 'type' );
		$_CB_database->dropColumn( $table, 'item' );
	}

	if ( $migrate ) {
		// Migrate global parameters:
		$plugin										=	new PluginTable();

		$plugin->load( array( 'element' => 'cbgroupjive' ) );

		$pluginParams								=	new Registry( $plugin->get( 'params' ) );

		// Notifications:
		$pluginParams->set( 'notifications', $pluginParams->get( 'general_notifications' ) );
		$pluginParams->set( 'notifications_notifyby', ( $pluginParams->get( 'general_notifyby' ) < 4 ? 2 : 1 ) );
		$pluginParams->set( 'notifications_default_user_join', $pluginParams->get( 'notifications_group_userjoin' ) );
		$pluginParams->set( 'notifications_default_user_leave', $pluginParams->get( 'notifications_group_userleave' ) );
		$pluginParams->set( 'notifications_default_user_approve', $pluginParams->get( 'notifications_group_userapprove' ) );
		$pluginParams->set( 'notifications_default_invite_accept', $pluginParams->get( 'notifications_group_inviteaccept' ) );

		// Categories:
		$pluginParams->set( 'categories_paging', $pluginParams->get( 'overview_paging' ) );
		$pluginParams->set( 'categories_limit', $pluginParams->get( 'overview_limit' ) );
		$pluginParams->set( 'categories_search', $pluginParams->get( 'overview_search' ) );

		switch( (int) $pluginParams->get( 'overview_orderby' ) ) {
			case 7:
				$orderBy							=	5;
				break;
			case 8:
				$orderBy							=	6;
				break;
			case 5:
				$orderBy							=	3;
				break;
			case 6:
				$orderBy							=	4;
				break;
			case 1:
			case 2:
				$orderBy							=	(int) $pluginParams->get( 'overview_orderby' );
				break;
			case 3:
			case 4:
			case 9:
			case 10:
			default:
				$orderBy							=	1;
				break;
		}

		$pluginParams->set( 'categories_orderby', $orderBy );
		$pluginParams->set( 'categories_groups_paging', $pluginParams->get( 'category_groups_paging' ) );
		$pluginParams->set( 'categories_groups_limit', $pluginParams->get( 'category_groups_limit' ) );
		$pluginParams->set( 'categories_groups_search', $pluginParams->get( 'category_groups_search' ) );
		$pluginParams->set( 'categories_groups_orderby', ( $pluginParams->get( 'category_groups_orderby' ) > 8 ? 4 : $pluginParams->get( 'category_groups_orderby' ) ) );

		// Groups:
		$pluginParams->set( 'groups_create_access', ( ! $pluginParams->get( 'group_create' ) ? -1 : ( $pluginParams->get( 'group_create_access' ) == -1 ? 2 : Application::CmsPermissions()->convertOldGroupToViewAccessLevel( $pluginParams->get( 'group_create_access' ), 'CB GroupJive: Groups Create Access' ) ) ) );
		$pluginParams->set( 'groups_create_limit', $pluginParams->get( 'group_limit' ) );
		$pluginParams->set( 'groups_create_approval', $pluginParams->get( 'group_approve' ) );
		$pluginParams->set( 'groups_create_captcha', $pluginParams->get( 'group_captcha' ) );
		$pluginParams->set( 'groups_message', $pluginParams->get( 'group_message' ) );
		$pluginParams->set( 'groups_message_captcha', $pluginParams->get( 'group_message_captcha' ) );
		$pluginParams->set( 'groups_users_paging', $pluginParams->get( 'group_users_paging' ) );
		$pluginParams->set( 'groups_users_limit', $pluginParams->get( 'group_users_limit' ) );
		$pluginParams->set( 'groups_users_search', $pluginParams->get( 'group_users_search' ) );
		$pluginParams->set( 'groups_invites_display', $pluginParams->get( 'group_invites_display' ) );
		$pluginParams->set( 'groups_invites_by', $pluginParams->get( 'group_invites_by' ) );
		$pluginParams->set( 'groups_invites_list', $pluginParams->get( 'group_invites_list' ) );
		$pluginParams->set( 'groups_invites_accept', $pluginParams->get( 'group_invites_accept' ) );
		$pluginParams->set( 'groups_invites_captcha', $pluginParams->get( 'group_invites_captcha' ) );
		$pluginParams->set( 'groups_invites_paging', $pluginParams->get( 'group_invites_paging' ) );
		$pluginParams->set( 'groups_invites_limit', $pluginParams->get( 'group_invites_limit' ) );
		$pluginParams->set( 'groups_invites_search', $pluginParams->get( 'group_invites_search' ) );
		$pluginParams->set( 'groups_paging', $pluginParams->get( 'group_all_paging' ) );
		$pluginParams->set( 'groups_limit', $pluginParams->get( 'group_all_limit' ) );
		$pluginParams->set( 'groups_search', $pluginParams->get( 'group_all_search' ) );
		$pluginParams->set( 'groups_orderby', ( $pluginParams->get( 'group_all_orderby' ) > 8 ? 4 : $pluginParams->get( 'group_all_orderby' ) ) );

		$plugin->set( 'params', $pluginParams->asJson() );

		$plugin->store();

		// Migrate the old auto fields to core GJ:
		$query										=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_fields' )
													.	"\n SET " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'groupautojoin' )
													.	', ' . $_CB_database->NameQuote( 'pluginid' ) . ' = ' . (int) $plugin->get( 'id' )
													.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'cbgjautojoin' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Migrate tab parameters:
		$tab										=	new TabTable();

		$tab->load( array( 'pluginclass' => 'cbgjTab' ) );

		$tabParams									=	new Registry( $tab->get( 'params' ) );

		$tabParams->set( 'tab_paging', $pluginParams->get( 'group_tab_paging' ) );
		$tabParams->set( 'tab_limit', $pluginParams->get( 'group_tab_limit' ) );
		$tabParams->set( 'tab_search', $pluginParams->get( 'group_tab_search' ) );
		$tabParams->set( 'tab_orderby', ( $pluginParams->get( 'group_tab_orderby' ) > 8 ? 4 : $pluginParams->get( 'group_tab_orderby' ) ) );

		$tab->set( 'params', $tabParams->asJson() );

		$tab->store();
	}

	// Migrate gj auto to cb auto actions if possible:
	$table											=	'#__groupjive_plugin_auto';

	if ( $_CB_database->getTableStatus( $table ) ) {
		$fields										=	$_CB_database->getTableFields( $table );

		if ( isset( $fields[$table]['trigger'] ) ) {
			$autoActions							=	new PluginTable();

			$autoActions->load( array( 'element' => 'cbautoactions' ) );

			if ( $autoActions->get( 'id' ) ) {
				$table								=	'#__comprofiler_plugin_autoactions';
				$fields								=	$_CB_database->getTableFields( $table );

				if ( ! isset( $fields[$table]['conditions'] ) ) {
					return;
				}

				$query								=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_auto' );
				$_CB_database->setQuery( $query );
				$autos								=	$_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( $_CB_database, '#__groupjive_plugin_auto', 'id' ) );

				/** @var $autos Table[] */
				foreach ( $autos as $auto ) {
					$oldParams						=	new Registry( $auto->get( 'params' ) );

					if ( $oldParams->get( 'migrated' ) ) {
						continue;
					}

					$newParams						=	new Registry();
					$newConditions					=	null;

					if ( $auto->get( 'field' ) ) {
						$fields						=	new Registry( $auto->get( 'field' ) );
						$operators					=	new Registry( $auto->get( 'operator' ) );
						$values						=	new Registry( $auto->get( 'value' ) );

						$conditionals				=	count( $fields );

						if ( $conditionals ) {
							$conditions				=	array();

							for ( $i = 0, $n = $conditionals; $i < $n; $i++ ) {
								$field				=	$fields->get( "field$i" );
								$operator			=	$operators->get( "operator$i" );
								$value				=	$values->get( "value$i" );

								if ( $operator ) {
									$conditions[]	=	array( 'field' => $field, 'operator' => $operator, 'value' => $value, 'translate' => 0 );
								}
							}

							if ( $conditions ) {
								$newConditionals	=	new Registry( $conditions );
								$newConditions		=	$newConditionals->asJson();
							}
						}
					}

					$mode							=	$oldParams->get( 'auto' );

					$join							=	array(	'mode'					=>	$mode,
																'groups'				=>	$oldParams->get( 'groups' ),
																'status'				=>	$oldParams->get( 'status' ),
																'name'					=>	( $mode == 2 ? $oldParams->get( 'grp_name' ) : $oldParams->get( 'cat_name' ) ),
																'category'				=>	$oldParams->get( 'category' ),
																'category_name'			=>	$oldParams->get( 'cat_name' ),
																'category_parent'		=>	$oldParams->get( 'cat_parent' ),
																'category_types'		=>	$oldParams->get( 'types' ),
																'category_description'	=>	$oldParams->get( 'cat_description' ),
																'category_unique'		=>	$oldParams->get( 'cat_unique' ),
																'group_parent'			=>	$oldParams->get( 'grp_parent' ),
																'type'					=>	$oldParams->get( 'type' ),
																'parent'				=>	$oldParams->get( 'cat_parent' ),
																'types'					=>	$oldParams->get( 'types' ),
																'description'			=>	( $mode == 2 ? $oldParams->get( 'grp_description' ) : $oldParams->get( 'cat_description' ) ),
																'owner'					=>	( $mode == 2 ? $oldParams->get( 'grp_owner' ) : $oldParams->get( 'cat_owner' ) ),
																'unique'				=>	( $mode == 2 ? $oldParams->get( 'grp_unique' ) : $oldParams->get( 'cat_unique' ) ),
																'autojoin'				=>	$oldParams->get( 'grp_autojoin' ),
																'group_status'			=>	$oldParams->get( 'status' )
															);

					$newParams->set( 'groupjive', array( $join ) );
					$newParams->set( 'exclude', $auto->get( 'exclude' ) );

					$query							=	'INSERT IGNORE INTO '. $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
													.	' ( '
													.		$_CB_database->NameQuote( 'published' )
													.		', ' . $_CB_database->NameQuote( 'title' )
													.		', ' . $_CB_database->NameQuote( 'description' )
													.		', ' . $_CB_database->NameQuote( 'type' )
													.		', ' . $_CB_database->NameQuote( 'trigger' )
													.		', ' . $_CB_database->NameQuote( 'object' )
													.		', ' . $_CB_database->NameQuote( 'variable' )
													.		', ' . $_CB_database->NameQuote( 'access' )
													.		', ' . $_CB_database->NameQuote( 'conditions' )
													.		', ' . $_CB_database->NameQuote( 'params' )
													.	' ) VALUES ( '
													.		(int) $auto->get( 'published' )
													.		', ' . $_CB_database->Quote( $auto->get( 'title' ) )
													.		', ' . $_CB_database->Quote( $auto->get( 'description' ) )
													.		', ' . $_CB_database->Quote( 'groupjive' )
													.		', ' . $_CB_database->Quote( str_replace( ',', '|*|', $auto->get( 'trigger' ) ) )
													.		', ' . $_CB_database->Quote( $auto->get( 'object' ) )
													.		', ' . $_CB_database->Quote( $auto->get( 'variable' ) )
													.		', ' . $_CB_database->Quote( $auto->get( 'access' ) )
													.		', ' . $_CB_database->Quote( $newConditions )
													.		', ' . $_CB_database->Quote( $newParams->asJson() )
													.	' )';
					$_CB_database->setQuery( $query );
					$_CB_database->query();

					$oldParams->set( 'migrated', true );

					$auto->set( 'params', $oldParams->asJson() );

					$auto->store();
				}
			}
		}
	}
}