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

class cbautoactionsActionAntiSpam extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_framework, $_CB_database;

		if ( ! $this->installed() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_ANTISPAM_NOT_INSTALLED', ':: Action [action] :: CB AntiSpam is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		foreach ( $trigger->getParams()->subTree( 'antispam' ) as $row ) {
			/** @var ParamsInterface $row */
			$mode					=	$row->get( 'mode', 'block', GetterInterface::STRING );
			$type					=	$row->get( 'type', 'user', GetterInterface::STRING );
			$value					=	$row->get( 'value', null, GetterInterface::STRING );

			if ( ! $value ) {
				switch ( $type ) {
					case 'user':
						$value		=	(int) $user->get( 'id' );
						break;
					case 'ip':
						$value		=	$user->get( 'registeripaddr' );
						break;
					case 'email':
						$value		=	$user->get( 'email' );
						break;
					case 'domain':
						$email		=	explode( '@', $user->get( 'email' ) );

						if ( count( $email ) > 1 ) {
							$value	=	array_pop( $email );
						}
						break;
				}
			} else {
				$value				=	$trigger->getSubstituteString( $value );
			}

			if ( ! $value ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_ANTISPAM_NO_VALUE', ':: Action [action] :: CB AntiSpam skipped due to missing value', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			if ( $mode == 'block' ) {
				$duration			=	$row->get( 'duration', '+1 MONTH', GetterInterface::STRING );

				if ( $duration == 'custom' ) {
					$duration		=	$trigger->getSubstituteString( $row->get( 'custom_duration', null, GetterInterface::STRING ) );
				}

				$entry				=	new cbantispamBlock( $_CB_database );

				$entry->set( 'date', $_CB_framework->getUTCDate() );
				$entry->set( 'duration', $duration );
			} else {
				$entry				=	new cbantispamWhitelist( $_CB_database );
			}

			$entry->set( 'type', $type );
			$entry->set( 'value', $value );
			$entry->set( 'reason', $trigger->getSubstituteString( $row->get( 'reason', null, GetterInterface::RAW ) ), false );

			if ( ! $entry->store() ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_ANTISPAM_FAILED', ':: Action [action] :: CB AntiSpam failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $entry->getError() ) ) );
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' ) ) {
			return true;
		}

		return false;
	}
}