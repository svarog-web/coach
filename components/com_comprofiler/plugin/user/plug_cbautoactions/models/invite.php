<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionInvite extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_database;

		if ( ! $this->installed() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_INVITE_NOT_INSTALLED', ':: Action [action] :: CB Invites is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		foreach ( $trigger->getParams()->subTree( 'invite' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner					=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner				=	(int) $user->get( 'id' );
			} else {
				$owner				=	(int) $trigger->getSubstituteString( $owner );
			}

			if ( ! $owner ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_INVITE_NO_OWNER', ':: Action [action] :: CB Invites skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			if ( $user->get( 'id' ) != $owner ) {
				$user				=	CBuser::getUserDataInstance( $owner );
			}

			switch ( (int) cbGetParam( $params, 'invite_mode', 1 ) ) {
				case 1:
					$invite			=	new cbinvitesInviteTable();

					$toArray		=	explode( ',', $trigger->getSubstituteString( $row->get( 'to', null, GetterInterface::STRING ) ) );

					foreach ( $toArray as $to ) {
						$invite->set( 'id', null );
						$invite->set( 'to', $to );
						$invite->set( 'subject', $trigger->getSubstituteString( $row->get( 'subject', null, GetterInterface::STRING ) ) );
						$invite->set( 'body', $trigger->getSubstituteString( $row->get( 'body', null, GetterInterface::RAW ), false ) );
						$invite->set( 'user_id', $owner );
						$invite->set( 'code', md5( uniqid() ) );

						if ( ! $invite->store() ) {
							if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
								var_dump( CBTxt::T( 'AUTO_ACTION_INVITE_FAILED', ':: Action [action] :: CB Invites failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $invite->getError() ) ) );
							}

							continue;
						}

						if ( ! $invite->send() ) {
							if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
								var_dump( CBTxt::T( 'AUTO_ACTION_INVITE_SEND_FAILED', ':: Action [action] :: CB Invites failed to send. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $invite->getError() ) ) );
							}

							continue;
						}
					}
					break;
				case 2:
					$query			=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'to' ) . " = " . $_CB_database->Quote( $user->get( 'email' ) );
					$_CB_database->setQuery( $query );
					$invites		=	$_CB_database->loadObjectList( null, 'cbinvitesInviteTable', array( $_CB_database ) );

					/** @var cbinvitesInviteTable[] $invites */
					foreach ( $invites as $invite ) {
						$invite->accept( $user );
					}
					break;
				case 3:
					$query			=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' )
									.	"\n WHERE ( " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
									.	' OR ' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' ) . ' )';
					$_CB_database->setQuery( $query );
					$invites		=	$_CB_database->loadObjectList( null, 'cbinvitesInviteTable', array( $_CB_database ) );

					/** @var cbinvitesInviteTable[] $invites */
					foreach ( $invites as $invite ) {
						$invite->delete();
					}
					break;
			}
		}
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbinvites' ) ) {
			return true;
		}

		return false;
	}
}