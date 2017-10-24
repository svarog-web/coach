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
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

class InviteTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var int  */
	public $group			=	null;
	/** @var string  */
	public $message			=	null;
	/** @var string  */
	public $invited			=	null;
	/** @var string  */
	public $accepted		=	null;
	/** @var string  */
	public $code			=	null;
	/** @var string  */
	public $email			=	null;
	/** @var string  */
	public $user			=	null;
	/** @var string  */
	public $params			=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__groupjive_invites';

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
		} elseif ( ( $this->get( 'email' ) == '' ) && ( $this->get( 'user' ) == '' ) ) {
			$this->setError( CBTxt::T( 'Recipient not specified or invalid!' ) );

			return false;
		} else {
			$group					=	$this->group();

			if ( ! $group->get( 'id' ) ) {
				$this->setError( CBTxt::T( 'Group does not exist!' ) );

				return false;
			} elseif ( ! $this->get( 'id' ) ) {
				$user				=	new \CB\Database\Table\UserTable();

				if ( $this->get( 'email' ) ) {
					$user->load( array( 'email' => $this->get( 'email' ) ) );
				} elseif ( $this->get( 'user' ) ) {
					$user->load( (int) $this->get( 'user' ) );

					if ( ! $user->get( 'id' ) ) {
						$this->setError( CBTxt::T( 'The user you are inviting does not exist!' ) );

						return false;
					}
				}

				if ( $user->get( 'id' ) ) {
					if ( $this->get( 'user_id' ) == $user->get( 'id' ) ) {
						$this->setError( CBTxt::T( 'You can not invite your self!' ) );

						return false;
					} elseif ( $group->get( 'user_id' ) == $user->get( 'id' ) ) {
						$this->setError( CBTxt::T( 'You can not invite the group owner!' ) );

						return false;
					} elseif ( $user->get( 'block' ) || ( ! $user->get( 'approved' ) ) || ( ! $user->get( 'confirmed' ) ) ) {
						$this->setError( CBTxt::T( 'The user you are inviting does not exist!' ) );

						return false;
					} else {
						$groupUser	=	new UserTable();

						$groupUser->load( array( 'group' => (int) $this->get( 'group' ), 'user_id' => (int) $user->get( 'id' ) ) );

						if ( $groupUser->get( 'id' ) ) {
							$this->setError( CBTxt::T( 'The user you are inviting already belongs to this group!' ) );

							return false;
						}
					}
				}

				$invite				=	new InviteTable();

				if ( $this->get( 'email' ) ) {
					$invite->load( array( 'group' => (int) $this->get( 'group' ), 'email' => $this->get( 'email' ) ) );

					if ( $invite->get( 'id' ) ) {
						$this->setError( CBTxt::T( 'The email address you are inviting has already been invited to this group!' ) );

						return false;
					}
				} elseif ( $this->get( 'user' ) ) {
					$invite->load( array( 'group' => (int) $this->get( 'group' ), 'user' => (int) $this->get( 'user' ) ) );

					if ( $invite->get( 'id' ) ) {
						$this->setError( CBTxt::T( 'The user you are inviting has already been invited to this group!' ) );

						return false;
					}
				}
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

		$new		=	( $this->get( 'id' ) ? false : true );
		$old		=	new self();

		$this->set( 'code', $this->get( 'code', md5( uniqid() ) ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'gj_onBeforeUpdateInvite', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onBeforeCreateInvite', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gj_onAfterUpdateInvite', array( $this, $old ) );
		} else {
			$this->send();

			$_PLUGINS->trigger( 'gj_onAfterCreateInvite', array( $this ) );
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

		$_PLUGINS->trigger( 'gj_onBeforeDeleteInvite', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'gj_onAfterDeleteInvite', array( $this ) );

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
	 * @return bool
	 */
	public function invited()
	{
		if ( $this->get( 'invited', '0000-00-00 00:00:00' ) != '0000-00-00 00:00:00' ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function accepted()
	{
		if ( $this->get( 'accepted', '0000-00-00 00:00:00' ) != '0000-00-00 00:00:00' ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function canResend()
	{
		global $_PLUGINS;

		if ( $this->accepted() ) {
			return false;
		}

		if ( ( ! $this->invited() ) || Application::Cms()->getClientId() ) {
			return true;
		}

		static $params		=	null;

		if ( ! $params ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params			=	$_PLUGINS->getPluginParams( $plugin );
		}

		$days				=	(int) $params->get( 'groups_invites_resend', 7 );

		if ( ! $days ) {
			return false;
		}

		$diff				=	Application::Date( 'now', 'UTC' )->diff( $this->get( 'invited' ) );

		if ( ( $diff === false ) || ( $diff->days < $days ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function send()
	{
		if ( $this->accepted() ) {
			$this->setError( CBTxt::T( 'Invite already accepted!' ) );

			return false;
		}

		if ( $this->invited() && ( ! $this->canResend() ) ) {
			$this->setError( CBTxt::T( 'Invite already sent!' ) );

			return false;
		}

		if ( ( $this->group()->get( 'published', 1 ) != 1 ) || ( ! $this->group()->category()->get( 'published', 1 ) ) ) {
			$this->setError( CBTxt::T( 'Can not invite to an unpublished group!' ) );

			return false;
		}

		if ( $this->get( 'user' ) ) {
			$to		=	(int) $this->get( 'user' );
		} else {
			$to		=	$this->get( 'email' );
		}

		CBGroupJive::sendNotification( 4, (int) $this->get( 'user_id' ), $to, CBTxt::T( 'Group invite' ), CBTxt::T( 'GROUP_INVITE_MESSAGE', "You have been invited to join the group [group] by [user]!\n\n[message]", array( '[message]' => htmlspecialchars( $this->get( 'message' ) ) ) ), $this->group() );

		$this->set( 'invited', $this->get( 'invited', Application::Database()->getUtcDateTime() ) );

		if ( ! $this->store() ) {
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function accept()
	{
		if ( $this->accepted() ) {
			return true;
		}

		$userId				=	$this->get( 'user', 0, GetterInterface::INT );

		if ( ! $userId ) {
			$email			=	$this->get( 'email', null, GetterInterface::STRING );

			if ( $email ) {
				$user		=	new \CB\Database\Table\UserTable();

				$user->loadByEmail( $email );

				$userId		=	$user->get( 'id', 0, GetterInterface::INT );
			}
		}

		if ( ! $userId ) {
			return false;
		}

		$row				=	new UserTable();

		$row->load( array( 'user_id' => $userId, 'group' => $this->get( 'group', 0, GetterInterface::INT ) ) );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			$row->set( 'user_id', $userId );
			$row->set( 'group', $this->get( 'group', 0, GetterInterface::INT ) );
			$row->set( 'status', 1 );

			if ( ! $row->store() ) {
				$this->setError( $row->getError() );

				return false;
			}
		}

		$this->set( 'accepted', Application::Database()->getUtcDateTime() );

		if ( ! $this->store() ) {
			return false;
		}

		return true;
	}
}