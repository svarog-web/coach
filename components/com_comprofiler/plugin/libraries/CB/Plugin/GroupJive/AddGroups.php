<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive;

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Database\Table\Table;
use CB\Plugin\GroupJive\Table\UserTable;

defined('CBLIB') or die();

class AddGroups extends Table
{
	/** @var int  */
	public $id			=	null;
	/** @var array  */
	public $groups		=	array();
	/** @var array  */
	public $users		=	array();

	/**
	 * @param null|int $oid
	 * @return bool
	 */
	public function load( $oid = null )
	{
		$input					=	Application::Input()->subTree( 'usersbrowser' );

		foreach ( $input->subTree( 'idcid' ) as $id ) {
			if ( $id ) {
				$this->users[]	=	array( 'user_id' => (int) $id, 'status' => 1 );
			}
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( ! $this->get( 'groups' ) ) {
			$this->setError( CBTxt::T( 'Groups not specified!' ) );

			return false;
		} elseif ( ! $this->get( 'users' ) ) {
			$this->setError( CBTxt::T( 'Users not specified!' ) );

			return false;
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		$groups				=	( is_string( $this->get( 'groups' ) ) ? explode( '|*|', $this->get( 'groups' ) ) : $this->get( 'groups' ) );
		$users				=	( is_string( $this->get( 'users' ) ) ? json_decode( $this->get( 'users' ), true ) : $this->get( 'users' ) );

		foreach ( $users as $user ) {
			$userId			=	( isset( $user['user_id'] ) ? (int) $user['user_id'] : 0 );

			if ( ! $userId ) {
				continue;
			}

			foreach ( $groups as $group ) {
				$groupId	=	(int) $group;

				if ( ! $groupId ) {
					continue;
				}

				$row		=	new UserTable();

				$row->load( array( 'user_id' => $userId, 'group' => $groupId ) );

				$row->set( 'user_id', $userId );
				$row->set( 'group', $groupId );
				$row->set( 'status', ( isset( $user['user_id'] ) ? (int) $user['status'] : 1 ) );

				if ( $row->check() ) {
					$row->store();
				}
			}
		}

		if ( Application::Cms()->getClientId() ) {
			cbRedirect( 'index.php?option=com_comprofiler&view=showusers', CBTxt::T( 'Users successfully added to CB GroupJive groups!' ) );
		}

		return true;
	}
}