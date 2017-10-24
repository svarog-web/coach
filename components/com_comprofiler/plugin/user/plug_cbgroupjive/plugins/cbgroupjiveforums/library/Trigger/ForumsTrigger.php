<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveForums\Trigger;

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJiveForums\CBGroupJiveForums;

defined('CBLIB') or die();

class ForumsTrigger extends \cbPluginHandler
{

	/**
	 * render frontend forum group edit params
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
		if ( ! CBGroupJiveForums::getForum() ) {
			return null;
		}

		CBGroupJive::getTemplate( 'group_edit', true, true, $this->element );

		$listEnable			=	array();
		$listEnable[]		=	\moscomprofilerHTML::makeOption( 0, CBTxt::T( 'Disable' ) );
		$listEnable[]		=	\moscomprofilerHTML::makeOption( 1, CBTxt::T( 'Enable' ) );

		$enableTooltip		=	cbTooltip( null, CBTxt::T( 'Optionally enable or disable usage of forums.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['forums']	=	\moscomprofilerHTML::selectList( $listEnable, 'params[forums]', 'class="form-control"' . $enableTooltip, 'value', 'text', (int) $this->input( 'post/params.forums', $row->params()->get( 'forums', 1 ), GetterInterface::INT ), 1, false, false );

		return \HTML_groupjiveForumsParams::showForumsParams( $row, $input, $category, $user, $this );
	}

	/**
	 * store the forum category for the group or category that was deleted
	 *
	 * @param GroupTable|CategoryTable $row
	 */
	public function storeForum( $row )
	{
		if ( ( ! CBGroupJiveForums::getForum() ) || $row->get( '_skipForums', false, GetterInterface::BOOLEAN ) ) {
			return;
		}

		$parent					=	$this->params->get( 'groups_forums_category', 0, GetterInterface::INT );

		if ( ! $parent ) {
			return;
		}

		if ( ( $row instanceof GroupTable ) && $row->category()->get( 'id', 0, GetterInterface::INT ) ) {
			$parentCategory		=	CBGroupJiveForums::getForum()->getCategory( $row->category()->params()->get( 'forum_id', 0, GetterInterface::INT ) );

			if ( ! $parentCategory->get( 'id', 0, GetterInterface::INT ) ) {
				$parentCategory->set( 'parent', $parent );
				$parentCategory->set( 'name', $row->category()->get( 'name', null, GetterInterface::STRING ) );
				$parentCategory->set( 'alias', $row->category()->get( 'id', 0, GetterInterface::INT ) . '-' . $row->category()->get( 'name', null, GetterInterface::STRING ) );
				$parentCategory->set( 'description', $row->category()->get( 'description', null, GetterInterface::STRING ) );
				$parentCategory->set( 'published', ( ! $row->category()->params()->get( 'forums', true, GetterInterface::BOOLEAN ) ? 0 : $row->category()->get( 'published', 0, GetterInterface::INT ) ) );

				$parentCategory->access( $row->category() );

				if ( ! $parentCategory->check() ) {
					return;
				}

				if ( ! $parentCategory->store() ) {
					return;
				}

				$row->category()->set( '_skipForums', true );

				$row->category()->params()->set( 'forum_id', $parentCategory->get( 'id', 0, GetterInterface::INT ) );

				$row->category()->set( 'params', $row->category()->params()->asJson() );

				$row->category()->store();

				$row->category()->set( '_skipForums', false );
			}

			$parent				=	$parentCategory->get( 'id', 0, GetterInterface::INT );
		}

		$category				=	CBGroupJiveForums::getForum()->getCategory( $row->params()->get( 'forum_id', 0, GetterInterface::INT ) );

		$new					=	( $category->get( 'id', 0, GetterInterface::INT ) ? false : true );

		$category->set( 'parent', $parent );
		$category->set( 'name', $row->get( 'name', null, GetterInterface::STRING ) );
		$category->set( 'alias', $row->get( 'id', 0, GetterInterface::INT ) . '-' . $row->get( 'name', null, GetterInterface::STRING ) );
		$category->set( 'description', $row->get( 'description', null, GetterInterface::STRING ) );
		$category->set( 'published', ( ! $row->params()->get( 'forums', false, GetterInterface::BOOLEAN ) ? 0 : $row->get( 'published', 0, GetterInterface::INT ) ) );

		$category->access( $row );

		if ( ! $category->check() ) {
			return;
		}

		if ( ! $category->store() ) {
			return;
		}

		if ( ( $row instanceof GroupTable ) && ( ! CBGroupJive::isModerator( $row->get( 'user_id', 0, GetterInterface::INT ) ) ) ) {
			$moderators			=	$category->moderators();

			if ( ! in_array( $row->get( 'user_id', 0, GetterInterface::INT ), $moderators ) ) {
				$category->moderators( $row->get( 'user_id', 0, GetterInterface::INT ) );
			}
		}

		if ( $new ) {
			$row->set( '_skipForums', true );

			$row->params()->set( 'forum_id', $category->get( 'id', 0, GetterInterface::INT ) );

			$row->set( 'params', $row->params()->asJson() );

			$row->store();

			$row->set( '_skipForums', false );
		}
	}

	/**
	 * delete the forum category for the group or category that was deleted
	 *
	 * @param GroupTable|CategoryTable $row
	 */
	public function deleteForum( $row )
	{
		if ( ! CBGroupJiveForums::getForum() ) {
			return;
		}

		$category		=	CBGroupJiveForums::getForum()->getCategory( $row->params()->get( 'forum_id', 0, GetterInterface::INT ) );

		if ( ! $category->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$category->delete();
	}

	/**
	 * add or remove forum category moderators
	 *
	 * @param \CB\Plugin\GroupJive\Table\UserTable $row
	 */
	public function storeModerator( $row )
	{
		if ( ( ! CBGroupJiveForums::getForum() ) || CBGroupJive::isModerator( $row->get( 'user_id', 0, GetterInterface::INT ) ) ) {
			return;
		}

		$category		=	CBGroupJiveForums::getForum()->getCategory( $row->group()->params()->get( 'forum_id', 0, GetterInterface::INT ) );

		if ( ! $category->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$moderators		=	$category->moderators();

		if ( $row->get( 'status', 0, GetterInterface::INT ) >= 2 ) {
			if ( ! in_array( $row->get( 'user_id', 0, GetterInterface::INT ), $moderators ) ) {
				$category->moderators( $row->get( 'user_id', 0, GetterInterface::INT ) );
			}
		} else {
			if ( in_array( $row->get( 'user_id', 0, GetterInterface::INT ), $moderators ) ) {
				$category->moderators( null, $row->get( 'user_id', 0, GetterInterface::INT ) );
			}
		}
	}

	/**
	 * remove forum category moderators
	 *
	 * @param \CB\Plugin\GroupJive\Table\UserTable $row
	 */
	public function deleteModerator( $row )
	{
		if ( ( ! CBGroupJiveForums::getForum() ) || CBGroupJive::isModerator( $row->get( 'user_id', 0, GetterInterface::INT ) ) ) {
			return;
		}

		$category		=	CBGroupJiveForums::getForum()->getCategory( (int) $row->group()->params()->get( 'forum_id', 0, GetterInterface::INT ) );

		if ( ! $category->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$moderators		=	$category->moderators();

		if ( in_array( $row->get( 'user_id', 0, GetterInterface::INT ), $moderators ) ) {
			$category->moderators( null, $row->get( 'user_id', 0, GetterInterface::INT ) );
		}
	}

	/**
	 * prepare frontend forum render
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
	public function showForums( &$return, &$group, &$users, &$invites, &$counters, &$buttons, &$menu, &$tabs, $user )
	{
		if ( ! CBGroupJiveForums::getForum() ) {
			return null;
		}

		return CBGroupJiveForums::getForum()->getTopics( $user, $group, $counters );
	}
}