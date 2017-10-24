<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CB\Database\Table\PluginTable;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

function plug_cbgroupjiveevents_install()
{
	global $_CB_database;

	// Grab GJ params to migrate the legacy params:
	$plugin						=	new PluginTable();

	$plugin->load( array( 'element' => 'cbgroupjive' ) );

	$pluginParams				=	new Registry( $plugin->get( 'params' ) );

	if ( $pluginParams->has( 'events_event_content' ) || ( $pluginParams->get( 'events_event_content' ) != null ) ) {
		// Migrate events integration parameters:
		$events					=	new PluginTable();

		$events->load( array( 'element' => 'cbgroupjiveevents' ) );

		$eventsParams			=	new Registry( $events->get( 'params' ) );

		if ( ! $eventsParams->get( 'migrated' ) ) {
			$eventsParams->set( 'groups_events_content_plugins', $pluginParams->get( 'events_event_content' ) );
			$eventsParams->set( 'groups_events_address', $pluginParams->get( 'events_plotting' ) );
			$eventsParams->set( 'groups_events_captcha', $pluginParams->get( 'events_captcha' ) );
			$eventsParams->set( 'groups_events_paging', $pluginParams->get( 'group_tab_paging' ) );
			$eventsParams->set( 'groups_events_limit', $pluginParams->get( 'group_tab_limit' ) );
			$eventsParams->set( 'groups_events_search', $pluginParams->get( 'group_tab_search' ) );
			$eventsParams->set( 'migrated', true );

			$events->set( 'params', $eventsParams->asJson() );

			$events->store();
		}
	}

	$table						=	'#__groupjive_plugin_events';
	$fields						=	$_CB_database->getTableFields( $table );

	if ( isset( $fields[$table]['date'] ) ) {
		$now					=	Application::Database()->getUtcDateTime();

		// Move attending to attendance table:
		$query					=	'SELECT ' . $_CB_database->NameQuote( 'id' )
								.	', ' . $_CB_database->NameQuote( 'attending' )
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_events' );
		$_CB_database->setQuery( $query );
		$rows					=	$_CB_database->loadAssocList( 'id', 'attending' );

		$attend					=	array();

		foreach ( $rows as $id => $attending ) {
			foreach ( explode( '|*|', $attending ) as $attendee ) {
				$attendee		=	explode( ':', $attendee );
				$userId			=	( isset( $attendee[0] ) ? (int) $attendee[0] : null );
				$attendance		=	( isset( $attendee[1] ) ? (int) $attendee[1] : null );

				if ( $userId && ( $attendance == 1 ) ) {
					$attend[]	=	'( ' . (int) $userId . ', ' . (int) $id . ', ' . $_CB_database->Quote( $now ) . ' )';
				}
			}
		}

		if ( $attend ) {
			$query				=	'INSERT IGNORE INTO '. $_CB_database->NameQuote( '#__groupjive_plugin_events_attendance' )
								.	' ( '
								.		$_CB_database->NameQuote( 'user_id' )
								.		', ' . $_CB_database->NameQuote( 'event' )
								.		', ' . $_CB_database->NameQuote( 'date' )
								.	' ) VALUES ' . implode( ', ', $attend );
			$_CB_database->setQuery( $query );
			$_CB_database->query();
		}

		// Mode date to start:
		$query					=	'UPDATE '. $_CB_database->NameQuote( '#__groupjive_plugin_events' )
								.	"\n SET " . $_CB_database->NameQuote( 'start' ) . " = " . $_CB_database->NameQuote( 'date' );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Remove the old columns:
		$_CB_database->dropColumn( $table, 'latitude' );
		$_CB_database->dropColumn( $table, 'longitude' );
		$_CB_database->dropColumn( $table, 'attending' );
		$_CB_database->dropColumn( $table, 'date' );
	}
}