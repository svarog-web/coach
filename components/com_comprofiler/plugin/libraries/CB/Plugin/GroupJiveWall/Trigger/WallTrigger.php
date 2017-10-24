<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveWall\Trigger;

use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\NotificationTable;
use CB\Plugin\GroupJiveWall\Table\WallTable;
use CB\Plugin\GroupJiveWall\CBGroupJiveWall;

defined('CBLIB') or die();

class WallTrigger extends \cbPluginHandler
{

	/**
	 * render frontend wall group edit params
	 *
	 * @param string        $return
	 * @param GroupTable    $row
	 * @param array         $input
	 * @param CategoryTable $category
	 * @param UserTable     $user
	 * @return string
	 */
	public function editGroup( &$return, &$row, &$input, $category, $user )
	{
		CBGroupJive::getTemplate( 'group_edit', true, true, $this->element );

		$listEnable			=	array();
		$listEnable[]		=	\moscomprofilerHTML::makeOption( 0, CBTxt::T( 'Disable' ) );
		$listEnable[]		=	\moscomprofilerHTML::makeOption( 1, CBTxt::T( 'Enable' ) );
		$listEnable[]		=	\moscomprofilerHTML::makeOption( 2, CBTxt::T( 'Enable, with Approval' ) );

		$enableTooltip		=	cbTooltip( null, CBTxt::T( 'Optionally enable or disable usage of the wall. Group owner and group administrators are exempt from this configuration and can always post. Note existing posts will still be accessible.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['wall']		=	\moscomprofilerHTML::selectList( $listEnable, 'params[wall]', 'class="form-control"' . $enableTooltip, 'value', 'text', (int) $this->input( 'post/params.wall', $row->params()->get( 'wall', 1 ), GetterInterface::INT ), 1, false, false );

		return \HTML_groupjiveWallParams::showWallParams( $row, $input, $category, $user, $this );
	}

	/**
	 * delete all the posts for the group that was deleted
	 *
	 * @param GroupTable $group
	 */
	public function deleteGroup( $group )
	{
		global $_CB_database;

		$query			=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_wall' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' );
		$_CB_database->setQuery( $query );
		$posts			=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveWall\Table\WallTable', array( $_CB_database ) );

		/** @var WallTable[] $posts */
		foreach ( $posts as $post ) {
			$post->delete();
		}
	}

	/**
	 * render frontend wall group notifications edit params
	 *
	 * @param string            $return
	 * @param NotificationTable $row
	 * @param array             $input
	 * @param GroupTable        $group
	 * @param UserTable         $user
	 * @return string
	 */
	public function editNotifications( &$return, &$row, &$input, $group, $user )
	{
		CBGroupJive::getTemplate( 'notifications', true, true, $this->element );

		$listToggle					=	array();
		$listToggle[]				=	\moscomprofilerHTML::makeOption( '0', CBTxt::T( "Don't Notify" ) );
		$listToggle[]				=	\moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Notify' ) );

		$input['wall_new']			=	\moscomprofilerHTML::yesnoSelectList( 'params[wall_new]', 'class="form-control"', (int) $this->input( 'post/params.wall_new', $row->params()->get( 'wall_new', $this->params->get( 'notifications_default_wall_new', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );
		$input['wall_approve']		=	\moscomprofilerHTML::yesnoSelectList( 'params[wall_approve]', 'class="form-control"', (int) $this->input( 'post/params.wall_approve', $row->params()->get( 'wall_approve', $this->params->get( 'notifications_default_wall_approve', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );
		$input['wall_reply']		=	\moscomprofilerHTML::yesnoSelectList( 'params[wall_reply]', 'class="form-control"', (int) $this->input( 'post/params.wall_reply', $row->params()->get( 'wall_reply', $this->params->get( 'notifications_default_wall_reply', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );

		return \HTML_groupjiveWallNotifications::showWallNotifications( $row, $input, $group, $user, $this );
	}

	/**
	 * store default notifications
	 *
	 * @param \CB\Plugin\GroupJive\Table\UserTable $row
	 * @param Registry                             $notifications
	 */
	public function storeNotifications( $row, &$notifications )
	{
		$notifications->set( 'wall_new', $this->params->get( 'notifications_default_wall_new', 0 ) );
		$notifications->set( 'wall_approve', $this->params->get( 'notifications_default_wall_approve', 0 ) );
		$notifications->set( 'wall_reply', $this->params->get( 'notifications_default_wall_reply', 0 ) );
	}

	/**
	 * prepare frontend wall render
	 *
	 * @param string     $return
	 * @param GroupTable $group
	 * @param string     $users
	 * @param string     $invites
	 * @param array      $counters
	 * @param array      $buttons
	 * @param array      $menu
	 * @param \cbTabs    $tabs
	 * @param UserTable  $user
	 * @return array|null
	 */
	public function showWall( &$return, &$group, &$users, &$invites, &$counters, &$buttons, &$menu, &$tabs, $user )
	{
		global $_CB_framework, $_CB_database;

		CBGroupJive::getTemplate( 'wall', true, true, $this->element );

		$canModerate			=	( CBGroupJive::isModerator( $user->get( 'id' ) ) || ( CBGroupJive::getGroupStatus( $user, $group ) >= 2 ) );
		$prefix					=	'gj_group_' . $group->get( 'id', 0, GetterInterface::INT ) . '_wall_';
		$limit					=	(int) $this->params->get( 'groups_wall_limit', 15 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_wall' ) . " AS p"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = p.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE p." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND p." . $_CB_database->NameQuote( 'reply' ) . " = 0"
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND ( p." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR p.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
		}

		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		if ( ( ! $total ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'wall' ) ) ) {
			return null;
		}

		$pageNav				=	new \cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $prefix );

		switch( (int) $this->params->get( 'groups_wall_orderby', 2 ) ) {
			case 1:
				$orderBy		=	'p.' . $_CB_database->NameQuote( 'date' ) . ' ASC';
				break;
			case 3:
				$orderBy		=	'p.' . $_CB_database->NameQuote( 'reply' ) . ' ASC';
				break;
			case 4:
				$orderBy		=	'p.' . $_CB_database->NameQuote( 'reply' ) . ' DESC';
				break;
			case 2:
			default:
				$orderBy		=	'p.' . $_CB_database->NameQuote( 'date' ) . ' DESC';
				break;
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
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = p.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE p." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND p." . $_CB_database->NameQuote( 'reply' ) . " = 0"
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND ( p." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR p.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
		}

		$query					.=	"\n ORDER BY " . $orderBy;
		if ( $this->params->get( 'groups_wall_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveWall\Table\WallTable', array( $_CB_database ) );

		CBGroupJiveWall::getPost( $rows );
		CBGroupJive::preFetchUsers( $rows );

		$group->set( '_wall', $pageNav->total );

		return array(	'id'		=>	'wall',
						'title'		=>	CBTxt::T( 'Wall' ),
						'content'	=>	\HTML_groupjiveWall::showWall( $rows, $pageNav, $group, $user, $this )
					);
	}

	/**
	 * prepare frontend wall replies render
	 *
	 * @param WallTable  $reply
	 * @param GroupTable $group
	 * @param UserTable  $user
	 * @return string
	 */
	public function showReplies( $reply, $group, $user )
	{
		global $_CB_framework, $_CB_database;

		CBGroupJive::getTemplate( 'replies', true, true, $this->element );

		$canModerate			=	( CBGroupJive::isModerator( $user->get( 'id' ) ) || ( CBGroupJive::getGroupStatus( $user, $group ) >= 2 ) );
		$prefix					=	'gj_wall_' . $reply->get( 'id', 0, GetterInterface::INT ) . '_replies_';
		$limit					=	(int) $this->params->get( 'groups_wall_replies_limit', 15 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );

		if ( $reply->get( '_replies' ) ) {
			$query				=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_wall' ) . " AS r"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = r.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE r." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND r." . $_CB_database->NameQuote( 'reply' ) . " = " . (int) $reply->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

			if ( ! $canModerate ) {
				$query			.=	"\n AND ( r." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR r.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
			}

			$_CB_database->setQuery( $query );
			$total				=	(int) $_CB_database->loadResult();
		} else {
			$total				=	0;
		}

		if ( ( ! $total ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'wall' ) ) ) {
			return null;
		}

		$pageNav				=	new \cbPageNav( $total, $limitstart, $limit );

		$pageNav->setClasses( array( 'cbPaginationLinks' => 'cbPaginationLinks pagination pagination-sm' ) );
		$pageNav->setInputNamePrefix( $prefix );

		if ( $reply->get( '_replies' ) ) {
			switch( (int) $this->params->get( 'groups_wall_replies_orderby', 2 ) ) {
				case 1:
					$orderBy	=	'r.' . $_CB_database->NameQuote( 'date' ) . ' ASC';
					break;
				case 2:
				default:
					$orderBy	=	'r.' . $_CB_database->NameQuote( 'date' ) . ' DESC';
					break;
			}

			$query				=	'SELECT r.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_wall' ) . " AS r"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = r.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE r." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND r." . $_CB_database->NameQuote( 'reply' ) . " = " . (int) $reply->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

			if ( ! $canModerate ) {
				$query			.=	"\n AND ( r." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR r.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
			}

			$query				.=	"\n ORDER BY " . $orderBy;
			if ( $this->params->get( 'groups_wall_replies_paging', 1 ) ) {
				$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
			} else {
				$_CB_database->setQuery( $query );
			}
			$rows				=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveWall\Table\WallTable', array( $_CB_database ) );

			CBGroupJiveWall::getPost( $rows );
			CBGroupJive::preFetchUsers( $rows );
		} else {
			$rows				=	array();
		}

		return \HTML_groupjiveWallReplies::showReplies( $reply, $rows, $pageNav, $group, $user, $this );
	}
}