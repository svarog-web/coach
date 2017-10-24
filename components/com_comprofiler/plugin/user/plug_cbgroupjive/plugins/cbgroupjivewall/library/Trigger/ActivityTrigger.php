<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveWall\Trigger;

use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveWall\CBGroupJiveWall;
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

		$join[]				=	'LEFT JOIN ' . $_CB_database->NameQuote( '#__groupjive_plugin_wall' ) . ' AS gj_w'
							.	' ON a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group.wall' )
							.	' AND a.' . $_CB_database->NameQuote( 'item' ) . ' = gj_w.' . $_CB_database->NameQuote( 'id' );

		if ( ! CBGroupJive::isModerator() ) {
			$user			=	\CBuser::getMyUserDataInstance();

			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group.wall' )
							.	' AND gj_w.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL'
							.	' AND ( gj_w.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
							.		' OR ( gj_w.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
							.		' AND ( gj_g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
							.		' OR gj_u.' . $_CB_database->NameQuote( 'status' ) . ' > 0 ) ) ) )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'groupjive' )
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' != ' . $_CB_database->Quote( 'group.wall' ) . ' ) ) )';
		} else {
			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group.wall' )
							.	' AND gj_w.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'groupjive' )
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' != ' . $_CB_database->Quote( 'group.wall' ) . ' ) ) )';
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

		$postIds				=	array();

		foreach ( $rows as $row ) {
			if ( ! ( ( $row->get( 'type', null, GetterInterface::STRING ) == 'groupjive' ) && ( $row->get( 'subtype', null, GetterInterface::STRING ) == 'group.wall' ) ) ) {
				continue;
			}

			$postId				=	$row->get( 'item', 0, GetterInterface::INT );

			if ( $postId && ( ! in_array( $postId, $postIds ) ) ) {
				$postIds[]		=	$postId;
			}
		}

		if ( ! $postIds ) {
			return;
		}

		$replies				=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_wall' ) . " AS r"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS rcb"
								.	' ON rcb.' . $_CB_database->NameQuote( 'id' ) . ' = r.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS rj"
								.	' ON rj.' . $_CB_database->NameQuote( 'id' ) . ' = rcb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE r." . $_CB_database->NameQuote( 'reply' ) . " = p." . $_CB_database->NameQuote( 'id' )
								.	"\n AND rcb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND rcb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND rj." . $_CB_database->NameQuote( 'block' ) . " = 0";

		$query					=	'SELECT p.*'
								.	', ( ' . $replies . ' ) AS _replies'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_wall' ) . " AS p"
								.	"\n WHERE p." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $postIds );
		$_CB_database->setQuery( $query );
		$posts					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveWall\Table\WallTable', array( $_CB_database ) );

		if ( ! $posts ) {
			return;
		}

		CBGroupJiveWall::getPost( $posts );
		CBGroupJive::preFetchUsers( $posts );
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
		if ( ! ( ( $row->get( 'type', null, GetterInterface::STRING ) == 'groupjive' ) && ( $row->get( 'subtype', null, GetterInterface::STRING ) == 'group.wall' ) ) ) {
			return;
		}

		$post		=	CBGroupJiveWall::getPost( $row->get( 'item', 0, GetterInterface::INT ) );

		if ( ! $post->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		CBGroupJive::getTemplate( 'activity', true, true, $this->element );

		$insert		=	\HTML_groupjiveWallActivity::showWallActivity( $row, $title, $message, $stream, $post, $this );
	}
}