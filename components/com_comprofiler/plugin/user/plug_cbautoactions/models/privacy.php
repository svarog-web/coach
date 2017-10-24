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

class cbautoactionsActionPrivacy extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		if ( ! $this->installed() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_PRIVACY_NOT_INSTALLED', ':: Action [action] :: CB Privacy is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		foreach ( $trigger->getParams()->subTree( 'privacy' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner						=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner					=	(int) $user->get( 'id' );
			} else {
				$owner					=	(int) $trigger->getSubstituteString( $owner );
			}

			if ( ! $owner ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_PRIVACY_NO_OWNER', ':: Action [action] :: CB Privacy skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$type						=	$trigger->getSubstituteString( $row->get( 'type', null, GetterInterface::STRING ) );

			if ( ! $type ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_PRIVACY_NO_TYPE', ':: Action [action] :: CB Privacy skipped due to missing type', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$rule						=	$row->get( 'rule', null, GetterInterface::STRING );

			if ( ! $rule ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_PRIVACY_NO_RULE', ':: Action [action] :: CB Privacy skipped due to missing rule', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$subtype					=	$trigger->getSubstituteString( $row->get( 'subtype', null, GetterInterface::STRING ) );
			$item						=	$trigger->getSubstituteString( $row->get( 'item', null, GetterInterface::STRING ) );

			$privacy					=	new cbprivacyPrivacyTable();

			if ( $item && $row->get( 'load', true, GetterInterface::BOOLEAN ) ) {
				$load					=	array( 'user_id' => $owner, 'type' => $type, 'item' => $item );

				if ( $subtype ) {
					$load['subtype']	=	$subtype;
				}

				$privacy->load( $load );
			}

			$privacy->set( 'user_id', $user );
			$privacy->set( 'type', $type );

			if ( $subtype ) {
				$privacy->set( 'subtype', $subtype );
			}

			if ( $item ) {
				$privacy->set( 'item', $item );
			}

			$privacy->set( 'rule', implode( '|*|', cbprivacyClass::validatePrivacy( $rule ) ) );

			if ( ! $privacy->store() ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_PRIVACY_FAILED', ':: Action [action] :: CB Privacy failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $privacy->getError() ) ) );
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function privacyList()
	{
		$options		=	array();

		if ( $this->installed() ) {
			$options	=	cbprivacyClass::getPrivacyOptions();
		}

		return $options;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbprivacy' ) ) {
			return true;
		}

		return false;
	}
}