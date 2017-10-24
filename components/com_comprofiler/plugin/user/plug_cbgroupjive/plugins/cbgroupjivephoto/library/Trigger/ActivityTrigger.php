<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJivePhoto\Trigger;

use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJivePhoto\CBGroupJivePhoto;
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

		$join[]				=	'LEFT JOIN ' . $_CB_database->NameQuote( '#__groupjive_plugin_photo' ) . ' AS gj_p'
							.	' ON a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group.photo' )
							.	' AND a.' . $_CB_database->NameQuote( 'item' ) . ' = gj_p.' . $_CB_database->NameQuote( 'id' );

		if ( ! CBGroupJive::isModerator() ) {
			$user			=	\CBuser::getMyUserDataInstance();

			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group.photo' )
							.	' AND gj_p.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL'
							.	' AND ( gj_p.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
							.		' OR ( gj_p.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
							.		' AND ( gj_g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
							.		' OR gj_u.' . $_CB_database->NameQuote( 'status' ) . ' > 0 ) ) ) )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'groupjive' )
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' != ' . $_CB_database->Quote( 'group.photo' ) . ' ) ) )';
		} else {
			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group.photo' )
							.	' AND gj_p.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'groupjive' )
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' != ' . $_CB_database->Quote( 'group.photo' ) . ' ) ) )';
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

		$photoIds				=	array();

		foreach ( $rows as $row ) {
			if ( ! ( ( $row->get( 'type', null, GetterInterface::STRING ) == 'groupjive' ) && ( $row->get( 'subtype', null, GetterInterface::STRING ) == 'group.photo' ) ) ) {
				continue;
			}

			$photoId			=	$row->get( 'item', 0, GetterInterface::INT );

			if ( $photoId && ( ! in_array( $photoId, $photoIds ) ) ) {
				$photoIds[]		=	$photoId;
			}
		}

		if ( ! $photoIds ) {
			return;
		}

		$query					=	'SELECT p.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_photo' ) . " AS p"
								.	"\n WHERE p." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $photoIds );
		$_CB_database->setQuery( $query );
		$photos					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJivePhoto\Table\PhotoTable', array( $_CB_database ) );

		if ( ! $photos ) {
			return;
		}

		CBGroupJivePhoto::getPhoto( $photos );
		CBGroupJive::preFetchUsers( $photos );
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
		if ( ! ( ( $row->get( 'type', null, GetterInterface::STRING ) == 'groupjive' ) && ( $row->get( 'subtype', null, GetterInterface::STRING ) == 'group.photo' ) ) ) {
			return;
		}

		$photo		=	CBGroupJivePhoto::getPhoto( $row->get( 'item', 0, GetterInterface::INT ) );

		if ( ! $photo->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		CBGroupJive::getTemplate( 'activity', true, true, $this->element );

		$insert		=	\HTML_groupjivePhotoActivity::showPhotoActivity( $row, $title, $message, $stream, $photo, $this );
	}
}