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

class cbautoactionsActionConnection extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $ueConfig;

		if ( ! $user->get( 'id' ) ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_CONNECTION_NO_USER', ':: Action [action] :: Connection skipped due to no user', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		foreach ( $trigger->getParams()->subTree( 'connection' ) as $row ) {
			/** @var ParamsInterface $row */
			$users										=	$trigger->getSubstituteString( $row->get( 'users', null, GetterInterface::STRING ) );

			if ( $users ) {
				$users									=	explode( ',', $users );

				cbArrayToInts( $users );

				$message								=	$trigger->getSubstituteString( $row->get( 'message', null, GetterInterface::RAW ), false );
				$mutual									=	$row->get( 'mutual', 2, GetterInterface::INT );
				$cross									=	$row->get( 'cross', 1, GetterInterface::INT );
				$notify									=	$row->get( 'notify', 0, GetterInterface::BOOLEAN );

				if ( $mutual ) {
					$oldMutual							=	$ueConfig['useMutualConnections'];
					$ueConfig['useMutualConnections']	=	( $mutual == 1 ? '1' : '0' );
				}

				if ( $cross ) {
					$oldCross							=	$ueConfig['autoAddConnections'];
					$ueConfig['autoAddConnections']		=	( $cross == 1 ? '1' : '0' );
				}

				if ( $row->get( 'direction', 0, GetterInterface::BOOLEAN ) ) {
					foreach ( $users as $userId ) {
						if ( $userId != $user->get( 'id' ) ) {
							$connections				=	new cbConnection( $userId );

							if ( ! $connections->getConnectionDetails( $userId, $user->get( 'id' ) ) ) {
								$connections->addConnection( $user->get( 'id' ), $message, $notify );
							}
						}
					}
				} else {
					$connections						=	new cbConnection( $user->get( 'id' ) );

					foreach ( $users as $userId ) {
						if ( $userId != $user->get( 'id' ) ) {
							if (  ! $connections->getConnectionDetails( $user->get( 'id' ), $userId ) ) {
								$connections->addConnection( $userId, $message, $notify );
							}
						}
					}
				}

				if ( $mutual ) {
					$ueConfig['useMutualConnections']	=	$oldMutual;
				}

				if ( $cross ) {
					$ueConfig['autoAddConnections']		=	$oldCross;
				}
			}
		}
	}
}