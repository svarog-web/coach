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
use CBLib\Language\CBTxt;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\Table\InviteTable;

defined('CBLIB') or die();

class UserTrigger extends \cbPluginHandler
{

	/**
	 * Deletes data when a user is deleted
	 *
	 * @param  UserTable $user
	 * @param  int       $status
	 */
	public function deleteGroups( $user, $status )
	{
		global $_CB_database;

		if ( $this->params->get( 'general_delete', true, GetterInterface::BOOLEAN ) ) {
			$query		=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$groups		=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );

			/** @var GroupTable[] $groups */
			foreach ( $groups as $group ) {
				$group->delete();
			}
		}
	}

	/**
	 * Auto accepts invites on registration
	 *
	 * @param  UserTable $user
	 */
	public function acceptInvites( $user )
	{
		global $_CB_database;

		if ( $this->params->get( 'groups_invites_accept', true, GetterInterface::BOOLEAN ) ) {
			$query					=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_invites' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
									.	"\n AND ( ( " . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) )
									.	' AND ' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
									.	' OR ( ' . $_CB_database->NameQuote( 'user' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
									.	' AND ' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';
			$_CB_database->setQuery( $query );
			$invites				=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\InviteTable', array( $_CB_database ) );

			$notified				=	array();

			/** @var InviteTable[] $invites */
			foreach ( $invites as $invite ) {
				if ( $invite->accept() && ( ! in_array( $invite->get( 'user_id', 0, GetterInterface::INT ), $notified ) ) ) {
					CBGroupJive::sendNotifications( 'invite_accept', CBTxt::T( 'Group invite accepted' ), CBTxt::T( 'Your group [group] invite to [user] has been accepted!' ), $invite->group(), $user, $invite->get( 'user_id', 0, GetterInterface::INT ), $notified );

					$notified[]		=	$invite->get( 'user_id', 0, GetterInterface::INT );
				}
			}
		}
	}
}