<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJivePhoto\Trigger;

use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJivePhoto\Table\PhotoTable;
use CB\Plugin\GroupJive\Table\NotificationTable;
use CB\Plugin\GroupJivePhoto\CBGroupJivePhoto;

defined('CBLIB') or die();

class PhotoTrigger extends \cbPluginHandler
{

	/**
	 * check photo create limit
	 *
	 * @param bool       $access
	 * @param string     $param
	 * @param GroupTable $group
	 * @param UserTable  $user
	 */
	public function canCreate( &$access, $param, $group, $user )
	{
		global $_CB_database;

		if ( $param == 'photo' ) {
			$createLimit				=	(int) $this->params->get( 'groups_photo_create_limit', 0 );

			if ( $createLimit ) {
				static $count			=	array();

				$countId				=	md5( $user->get( 'id' ) . $group->get( 'id' ) );

				if ( ! isset( $count[$countId] ) ) {
					$query				=	'SELECT COUNT(*)'
										.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_photo' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
										.	"\n AND " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' );
					$_CB_database->setQuery( $query );
					$count[$countId]	=	(int) $_CB_database->loadResult();
				}

				if ( $count[$countId] >= $createLimit ) {
					$access				=	false;
				}
			}
		}
	}

	/**
	 * render frontend photo group edit params
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

		$enableTooltip		=	cbTooltip( null, CBTxt::T( 'Optionally enable or disable usage of photos. Group owner and group administrators are exempt from this configuration and can always upload photos. Note existing photos will still be accessible.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['photo']		=	\moscomprofilerHTML::selectList( $listEnable, 'params[photo]', 'class="form-control"' . $enableTooltip, 'value', 'text', (int) $this->input( 'post/params.photo', $row->params()->get( 'photo', 1 ), GetterInterface::INT ), 1, false, false );

		return \HTML_groupjivePhotoParams::showPhotoParams( $row, $input, $category, $user, $this );
	}

	/**
	 * delete the local photos and the directory when a category is deleted
	 *
	 * @param CategoryTable $category
	 */
	public function deleteCategory( $category )
	{
		global $_CB_framework;

		CBGroupJive::deleteDirectory( $_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivephoto/' . (int) $category->get( 'id' ) );
	}

	/**
	 * delete all the photos for the group that was deleted
	 *
	 * @param GroupTable $group
	 */
	public function deleteGroup( $group )
	{
		global $_CB_database;

		$query			=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_photo' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' );
		$_CB_database->setQuery( $query );
		$photos			=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJivePhoto\Table\PhotoTable', array( $_CB_database ) );

		/** @var PhotoTable[] $photos */
		foreach ( $photos as $photo ) {
			$photo->delete();
		}
	}

	/**
	 * render frontend photo group notifications edit params
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

		$input['photo_new']			=	\moscomprofilerHTML::yesnoSelectList( 'params[photo_new]', 'class="form-control"', (int) $this->input( 'post/params.photo_new', $row->params()->get( 'photo_new', $this->params->get( 'notifications_default_photo_new', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );
		$input['photo_approve']		=	\moscomprofilerHTML::yesnoSelectList( 'params[photo_approve]', 'class="form-control"', (int) $this->input( 'post/params.photo_approve', $row->params()->get( 'photo_approve', $this->params->get( 'notifications_default_photo_approve', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );

		return \HTML_groupjivePhotoNotifications::showPhotoNotifications( $row, $input, $group, $user, $this );
	}

	/**
	 * store default notifications
	 *
	 * @param \CB\Plugin\GroupJive\Table\UserTable $row
	 * @param Registry                             $notifications
	 */
	public function storeNotifications( $row, &$notifications )
	{
		$notifications->set( 'photo_new', $this->params->get( 'notifications_default_photo_new', 0 ) );
		$notifications->set( 'photo_approve', $this->params->get( 'notifications_default_photo_approve', 0 ) );
	}

	/**
	 * prepare frontend photos render
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
	public function showPhotos( &$return, &$group, &$users, &$invites, &$counters, &$buttons, &$menu, &$tabs, $user )
	{
		global $_CB_framework, $_CB_database;

		CBGroupJive::getTemplate( 'photos', true, true, $this->element );

		$canModerate			=	( CBGroupJive::isModerator( $user->get( 'id' ) ) || ( CBGroupJive::getGroupStatus( $user, $group ) >= 2 ) );
		$prefix					=	'gj_group_' . $group->get( 'id', 0, GetterInterface::INT ) . '_photos_';
		$limit					=	(int) $this->params->get( 'groups_photo_limit', 15 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );
		$search					=	$_CB_framework->getUserStateFromRequest( $prefix . 'search{com_comprofiler}', $prefix . 'search' );
		$where					=	null;

		if ( $search && $this->params->get( 'groups_photo_search', 1 ) ) {
			$where				.=	"\n AND ( p." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR p." . $_CB_database->NameQuote( 'filename' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR p." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( $where ? true : false );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_photo' ) . " AS p"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = p.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE p." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND ( p." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR p.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
		}

		$query					.=	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		if ( ( ! $total ) && ( ! $searching ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'photo' ) ) ) {
			return null;
		}

		$pageNav				=	new \cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $prefix );

		switch( (int) $this->params->get( 'groups_photo_orderby', 2 ) ) {
			case 1:
				$orderBy		=	'p.' . $_CB_database->NameQuote( 'date' ) . ' ASC';
				break;
			case 3:
				$orderBy		=	'p.' . $_CB_database->NameQuote( 'filename' ) . ' ASC';
				break;
			case 4:
				$orderBy		=	'p.' . $_CB_database->NameQuote( 'filename' ) . ' DESC';
				break;
			case 2:
			default:
				$orderBy		=	'p.' . $_CB_database->NameQuote( 'date' ) . ' DESC';
				break;
		}

		$query					=	'SELECT p.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_photo' ) . " AS p"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = p.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE p." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND ( p." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR p.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
		}

		$query					.=	$where
								.	"\n ORDER BY " . $orderBy;
		if ( $this->params->get( 'groups_photo_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJivePhoto\Table\PhotoTable', array( $_CB_database ) );

		$input					=	array();

		$input['search']		=	'<input type="text" name="' . htmlspecialchars( $prefix ) . 'search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjGroupPhotoForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Photos...' ) ) . '" class="form-control" />';

		CBGroupJivePhoto::getPhoto( $rows );
		CBGroupJive::preFetchUsers( $rows );

		$group->set( '_photos', $pageNav->total );

		return array(	'id'		=>	'photo',
						'title'		=>	CBTxt::T( 'Photos' ),
						'content'	=>	\HTML_groupjivePhoto::showPhotos( $rows, $pageNav, $searching, $input, $counters, $group, $user, $this )
					);
	}
}