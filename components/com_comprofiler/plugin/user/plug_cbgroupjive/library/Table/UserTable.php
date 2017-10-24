<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive\Table;

use CBLib\Application\Application;
use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

class UserTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var int  */
	public $group			=	null;
	/** @var int  */
	public $status			=	null;
	/** @var string  */
	public $date			=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__groupjive_users';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( $this->get( 'user_id' ) == '' ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		} elseif ( $this->get( 'group' ) == '' ) {
			$this->setError( CBTxt::T( 'Group not specified!' ) );

			return false;
		} else {
			$group		=	$this->group();

			if ( ! $group->get( 'id' ) ) {
				$this->setError( CBTxt::T( 'Group does not exist!' ) );

				return false;
			} elseif ( ( $this->get( 'user_id' ) == $group->get( 'user_id' ) ) && ( $this->get( 'status' ) != 4 ) ) {
				$this->setError( CBTxt::T( 'Group owner can not be demoted!' ) );

				return false;
			}
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new				=	( $this->get( 'id' ) ? false : true );
		$old				=	new self();

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime() ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'gj_onBeforeUpdateUser', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onBeforeCreateUser', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		// Promote to group owner if status is changed to owner:
		if ( $this->get( 'status' ) == 4 ) {
			$group			=	CBGroupJive::getGroup( $this->get( 'group' ) );

			if ( $group->get( 'id' ) && ( $group->get( 'user_id' ) != $this->get( 'user_id' ) ) ) {
				$group->set( 'user_id', (int) $this->get( 'user_id' ) );

				$group->store();
			}
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gj_onAfterUpdateUser', array( $this, $old ) );
		} else {
			static $params	=	null;

			if ( ! $params ) {
				$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
				$params		=	$_PLUGINS->getPluginParams( $plugin );
			}

			// Set the default notifications:
			$notification	=	new NotificationTable();

			$notification->load( array( 'user_id' => (int) $this->get( 'user_id' ), 'group' => (int) $this->get( 'group' ) ) );

			$notification->set( 'user_id', (int) $this->get( 'user_id' ) );
			$notification->set( 'group', (int) $this->get( 'group' ) );

			$notifications	=	$notification->params();

			$notifications->set( 'user_join', $params->get( 'notifications_default_user_join', 0 ) );
			$notifications->set( 'user_leave', $params->get( 'notifications_default_user_leave', 0 ) );
			$notifications->set( 'user_approve', $params->get( 'notifications_default_user_approve', 0 ) );
			$notifications->set( 'user_cancel', $params->get( 'notifications_default_user_cancel', 0 ) );
			$notifications->set( 'invite_accept', $params->get( 'notifications_default_invite_accept', 0 ) );
			$notifications->set( 'invite_reject', $params->get( 'notifications_default_invite_reject', 0 ) );

			$_PLUGINS->trigger( 'gj_onAfterCreateUser', array( $this, &$notifications ) );

			$notification->set( 'params', $notifications->asJson() );

			$notification->store();
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function canDelete( $id = null )
	{
		if ( ! $id ) {
			$id		=	$this->get( 'group' );
		}

		$group		=	CBGroupJive::getGroup( $id );

		if ( $group->get( 'id' ) && ( $group->get( 'user_id' ) == $this->get( 'user_id' ) ) ) {
			$this->setError( CBTxt::T( 'Group owner can not be deleted!' ) );

			return false;
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'gj_onBeforeDeleteUser', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		static $params			=	null;

		if ( ! $params ) {
			$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params				=	$_PLUGINS->getPluginParams( $plugin );
		}

		if ( ( ! CBGroupJive::isModerator( $this->get( 'user_id' ) ) ) && $params->get( 'groups_delete', 1 ) ) {
			// Delete this users group invites (to and from):
			$query				=	'SELECT *'
								.	"\n FROM " . $this->getDbo()->NameQuote( '#__groupjive_invites' )
								.	"\n WHERE " . $this->getDbo()->NameQuote( 'group' ) . " = " . (int) $this->get( 'group' )
								.	"\n AND ( " . $this->getDbo()->NameQuote( 'user_id' ) . " = " . (int) $this->get( 'user_id' )
								.	"\n OR " . $this->getDbo()->NameQuote( 'user' ) . " = " . (int) $this->get( 'user_id' ) . " )";
			$this->getDbo()->setQuery( $query );
			$invites			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\GroupJive\Table\InviteTable', array( $this->getDbo() ) );

			/** @var InviteTable[] $invites */
			foreach ( $invites as $invite ) {
				$invite->delete();
			}

			// Delete this users group notifications:
			$notification		=	new NotificationTable();

			$notification->load( array( 'user_id' => (int) $this->get( 'user_id' ), 'group' => (int) $this->get( 'group' ) ) );

			if ( $notification->get( 'id' ) ) {
				$notification->delete();
			}
		}

		$_PLUGINS->trigger( 'gj_onAfterDeleteUser', array( $this ) );

		return true;
	}

	/**
	 * @return Registry
	 */
	public function params()
	{
		if ( ! ( $this->get( '_params' ) instanceof Registry ) ) {
			$this->set( '_params', new Registry( $this->get( 'params' ) ) );
		}

		return $this->get( '_params' );
	}

	/**
	 * @return GroupTable
	 */
	public function group()
	{
		return CBGroupJive::getGroup( (int) $this->get( 'group' ) );
	}

	/**
	 * @return string
	 */
	public function status()
	{
		static $cache				=	array();

		$id							=	(int) $this->get( 'status' );

		if ( ! isset( $cache[$id] ) ) {
			switch ( $id ) {
				case -1:
					$cache[$id]		=	CBTxt::T( 'Banned' );
					break;
				case 0:
					$cache[$id]		=	CBTxt::T( 'Pending' );
					break;
				case 1:
					$cache[$id]		=	CBTxt::T( 'Active' );
					break;
				case 2:
					$cache[$id]		=	CBTxt::T( 'Moderator' );
					break;
				case 3:
					$cache[$id]		=	CBTxt::T( 'Admin' );
					break;
				case 4:
					$cache[$id]		=	CBTxt::T( 'Owner' );
					break;
				default:
					$cache[$id]		=	CBTxt::T( 'Unknown' );
					break;
			}
		}

		return $cache[$id];
	}
}