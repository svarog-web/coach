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

class cbautoactionsActionPMS extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_PMS;

		foreach ( $trigger->getParams()->subTree( 'pms' ) as $row ) {
			/** @var ParamsInterface $row */
			$pmFrom			=	$row->get( 'from', null, GetterInterface::STRING );

			if ( ! $pmFrom ) {
				$pmFrom		=	(int) $user->get( 'id' );
			} else {
				$pmFrom		=	(int) $trigger->getSubstituteString( $pmFrom );
			}

			if ( ! $pmFrom ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_PMS_NO_FROM', ':: Action [action] :: Private Message skipped due to missing from', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$pmTo			=	$trigger->getSubstituteString( $row->get( 'to', null, GetterInterface::STRING ) );

			if ( ! $pmTo ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_PMS_NO_TO', ':: Action [action] :: Private Message skipped due to missing to', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$pmMessage		=	$trigger->getSubstituteString( $row->get( 'message', null, GetterInterface::RAW ), false );

			if ( ! $pmMessage ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_PMS_NO_MSG', ':: Action [action] :: Private Message skipped due to missing message', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$pmSubject		=	$trigger->getSubstituteString( $row->get( 'subject', null, GetterInterface::STRING ) );

			$_CB_PMS->sendPMSMSG( $pmTo, $pmFrom, $pmSubject, $pmMessage, true );
		}
	}
}