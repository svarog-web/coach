<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveEvents\Trigger;

use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveEvents\CBGroupJiveEvents;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;

defined('CBLIB') or die();

class ActivityTrigger extends \cbPluginHandler
{

	/**
	 * @param bool     $count
	 * @param array    $select
	 * @param array    $where
	 * @param array    $join
	 * @param Activity $stream
	 */
	public function activityQuery( $count, &$select, &$where, &$join, &$stream )
	{
		global $_CB_database;

		$join[]				=	'LEFT JOIN ' . $_CB_database->NameQuote( '#__groupjive_plugin_events' ) . ' AS gj_e'
							.	' ON a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group.event' )
							.	' AND a.' . $_CB_database->NameQuote( 'item' ) . ' = gj_e.' . $_CB_database->NameQuote( 'id' );

		if ( ! CBGroupJive::isModerator() ) {
			$user			=	\CBuser::getMyUserDataInstance();

			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group.event' )
							.	' AND gj_e.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL'
							.	' AND ( gj_e.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
							.		' OR ( gj_e.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
							.		' AND ( gj_g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
							.		' OR gj_u.' . $_CB_database->NameQuote( 'status' ) . ' > 0 ) ) ) )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'groupjive' )
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' != ' . $_CB_database->Quote( 'group.event' ) . ' ) ) )';
		} else {
			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group.event' )
							.	' AND gj_e.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'groupjive' )
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' != ' . $_CB_database->Quote( 'group.event' ) . ' ) ) )';
		}
	}

	/**
	 * @param string          $return
	 * @param ActivityTable[] $rows
	 * @param Activity        $stream
	 * @param int             $output 0: Normal, 1: Raw, 2: Inline, 3: Load, 4: Save
	 */
	public function activityPrefetch( &$return, &$rows, $stream, $output )
	{
		global $_CB_database;

		$eventIds				=	array();

		foreach ( $rows as $row ) {
			if ( ! ( ( $row->get( 'type', null, GetterInterface::STRING ) == 'groupjive' ) && ( $row->get( 'subtype', null, GetterInterface::STRING ) == 'group.event' ) ) ) {
				continue;
			}

			$eventId			=	$row->get( 'item', 0, GetterInterface::INT );

			if ( $eventId && ( ! in_array( $eventId, $eventIds ) ) ) {
				$eventIds[]		=	$eventId;
			}
		}

		if ( ! $eventIds ) {
			return;
		}

		$user					=	\CBuser::getMyUserDataInstance();

		$guests					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_events_attendance' ) . " AS ea"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS eacb"
								.	' ON eacb.' . $_CB_database->NameQuote( 'id' ) . ' = ea.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS eaj"
								.	' ON eaj.' . $_CB_database->NameQuote( 'id' ) . ' = eacb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE ea." . $_CB_database->NameQuote( 'event' ) . " = e." . $_CB_database->NameQuote( 'id' )
								.	"\n AND eacb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND eacb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND eaj." . $_CB_database->NameQuote( 'block' ) . " = 0";

		$query					=	'SELECT e.*'
								.	', a.' . $_CB_database->NameQuote( 'id' ) . ' AS _attending'
								.	', ( ' . $guests . ' ) AS _guests'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_events' ) . " AS e"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_plugin_events_attendance' ) . " AS a"
								.	' ON a.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
								.	' AND a.' . $_CB_database->NameQuote( 'event' ) . ' = e.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE e." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $eventIds );
		$_CB_database->setQuery( $query );
		$events					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveEvents\Table\EventTable', array( $_CB_database ) );

		if ( ! $events ) {
			return;
		}

		CBGroupJiveEvents::getEvent( $events );
		CBGroupJive::preFetchUsers( $events );
	}

	/**
	 * @param ActivityTable $row
	 * @param null|string   $title
	 * @param null|string   $date
	 * @param null|string   $message
	 * @param null|string   $insert
	 * @param null|string   $footer
	 * @param array         $menu
	 * @param array         $extras
	 * @param Activity      $stream
	 * @param int           $output 0: Normal, 1: Raw, 2: Inline, 3: Load, 4: Save
	 */
	public function activityDisplay( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, &$extras, $stream, $output )
	{
		if ( ! ( ( $row->get( 'type', null, GetterInterface::STRING ) == 'groupjive' ) && ( $row->get( 'subtype', null, GetterInterface::STRING ) == 'group.event' ) ) ) {
			return;
		}

		$event			=	CBGroupJiveEvents::getEvent( $row->get( 'item', 0, GetterInterface::INT ) );

		if ( ! $event->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		CBGroupJive::getTemplate( 'activity', true, true, $this->element );

		$insert			=	\HTML_groupjiveEventActivity::showEventActivity( $row, $title, $message, $stream, $event, $this );
	}
}