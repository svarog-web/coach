<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive\Trigger;

use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
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

		$join[]				=	'LEFT JOIN ' . $_CB_database->NameQuote( '#__groupjive_groups' ) . ' AS gj_g'
							.	' ON a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND ( ( a.' . $_CB_database->NameQuote( 'subtype' ) . ' = ' . $_CB_database->Quote( 'group' )
							.	' AND a.' . $_CB_database->NameQuote( 'item' ) . ' = gj_g.' . $_CB_database->NameQuote( 'id' ) . ' )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'subtype' ) . ' != ' . $_CB_database->Quote( 'group' )
							.	' AND a.' . $_CB_database->NameQuote( 'parent' ) . ' = gj_g.' . $_CB_database->NameQuote( 'id' ) . ' ) )';

		if ( ! CBGroupJive::isModerator() ) {
			$user			=	\CBuser::getMyUserDataInstance();

			$join[]			=	'LEFT JOIN ' . $_CB_database->NameQuote( '#__groupjive_categories' ) . ' AS gj_c'
							.	' ON gj_c.' . $_CB_database->NameQuote( 'id' ) . ' = gj_g.' . $_CB_database->NameQuote( 'category' );

			$join[]			=	'LEFT JOIN ' . $_CB_database->NameQuote( '#__groupjive_users' ) . ' AS gj_u'
							.	' ON gj_u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
							.	' AND gj_u.' . $_CB_database->NameQuote( 'group' ) . ' = gj_g.' . $_CB_database->NameQuote( 'id' );

			$join[]			=	'LEFT JOIN ' . $_CB_database->NameQuote( '#__groupjive_invites' ) . ' AS gj_i'
							.	' ON gj_i.' . $_CB_database->NameQuote( 'group' ) . ' = gj_g.' . $_CB_database->NameQuote( 'id' )
							.	' AND gj_i.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
							.	' AND ( ( gj_i.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) )
							.	' AND gj_i.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
							.	' OR ( gj_i.' . $_CB_database->NameQuote( 'user' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
							.	' AND gj_i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';

			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND gj_g.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL'
							.	' AND ( gj_g.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
							.		' OR ( gj_g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
							.		' AND ( gj_g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
							.		' OR gj_u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
							.		' OR gj_i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )'
							.	' AND ( ( gj_c.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
							.		' AND gj_c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( $user->get( 'id', 0, GetterInterface::INT ) ) ) . ' )'
							.		( $this->params->get( 'groups_uncategorized', true, GetterInterface::BOOLEAN ) ? ' OR gj_g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 ) )' : ' ) )' )
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'groupjive' ) . ' ) )';
		} else {
			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'groupjive' )
							.	' AND gj_g.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'groupjive' ) . ' ) )';
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

		$groupIds				=	array();

		foreach ( $rows as $row ) {
			if ( $row->get( 'type', null, GetterInterface::STRING ) != 'groupjive' ) {
				continue;
			} elseif ( $row->get( 'subtype', null, GetterInterface::STRING ) == 'category' ) {
				continue;
			}

			if ( $row->get( 'subtype', null, GetterInterface::STRING ) == 'group' ) {
				$groupId		=	$row->get( 'item', 0, GetterInterface::INT );
			} else {
				$groupId		=	$row->get( 'parent', 0, GetterInterface::INT );
			}

			if ( $groupId && ( ! in_array( $groupId, $groupIds ) ) ) {
				$groupIds[]		=	$groupId;
			}
		}

		if ( ! $groupIds ) {
			return;
		}

		$user					=	\CBuser::getMyUserDataInstance();

		$users					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS uc"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS uccb"
								.	' ON uccb.' . $_CB_database->NameQuote( 'id' ) . ' = uc.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS ucj"
								.	' ON ucj.' . $_CB_database->NameQuote( 'id' ) . ' = uccb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE uc." . $_CB_database->NameQuote( 'group' ) . " = g." . $_CB_database->NameQuote( 'id' )
								.	"\n AND uccb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND uccb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND ucj." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $this->params->get( 'groups_users_owner', true, GetterInterface::BOOLEAN ) ) {
			$users				.=	"\n AND uc." . $_CB_database->NameQuote( 'status' ) . " != 4";
		}

		$query					=	'SELECT g.*'
								.	', c.' . $_CB_database->NameQuote( 'name' ) . ' AS _category_name'
								.	', u.' . $_CB_database->NameQuote( 'status' ) . ' AS _user_status'
								.	', i.' . $_CB_database->NameQuote( 'id' ) . ' AS _invite_id'
								.	', ( ' . $users . ' ) AS _users'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c"
								.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'category' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
								.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
								.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
								.	' ON i.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	' AND i.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
								.	' AND ( ( i.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) )
								.	' AND i.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
								.	' OR ( i.' . $_CB_database->NameQuote( 'user' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
								.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )'
								.	"\n WHERE g." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $groupIds );
		$_CB_database->setQuery( $query );
		$groups					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );

		if ( ! $groups ) {
			return;
		}

		CBGroupJive::getGroup( $groups );
		CBGroupJive::preFetchUsers( $groups );
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
		if ( $row->get( 'type', null, GetterInterface::STRING ) != 'groupjive' ) {
			return;
		}

		$row->set( '_links', false );

		if ( ! in_array( $row->get( 'subtype', null, GetterInterface::STRING ), array( 'group', 'group.join', 'group.leave' ) ) ) {
			return;
		}

		if ( $row->get( 'subtype', null, GetterInterface::STRING ) == 'group' ) {
			$groupId	=	$row->get( 'item', 0, GetterInterface::INT );
		} else {
			$groupId	=	$row->get( 'parent', 0, GetterInterface::INT );
		}

		$group			=	CBGroupJive::getGroup( $groupId );

		if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		CBGroupJive::getTemplate( 'activity' );

		$insert			=	\HTML_groupjiveActivity::showActivity( $row, $title, $message, $stream, $group, $this );
	}
}