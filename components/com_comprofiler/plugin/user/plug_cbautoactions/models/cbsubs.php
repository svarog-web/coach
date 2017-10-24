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

class cbautoactionsActionCBSubs extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_framework;

		if ( ( ! $user->get( 'id' ) ) || ( ! $this->installed() ) ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_CBSUBS_NOT_INSTALLED', ':: Action [action] :: CB Paid Subscriptions is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		foreach ( $trigger->getParams()->subTree( 'cbsubs' ) as $row ) {
			/** @var ParamsInterface $row */
			$plans										=	$row->get( 'plans' );

			if ( $plans ) {
				$plans									=	explode( '|*|', $plans );

				cbArrayToInts( $plans );

				$mode									=	$row->get( 'mode', 1, GetterInterface::INT );
				$subscriptions							=	cbpaidSomethingMgr::getAllSomethingOfUser( $user, null );
				$activePlans							=	array();

				if ( $subscriptions ) foreach ( $subscriptions as $type ) {
					foreach ( array_keys( $type ) as $typeId ) {
						/** @var cbpaidSomething $subscription */
						$subscription					=	$type[$typeId];
						$subscriptionId					=	(int) $subscription->get( 'id' );
						$subscriptionStatus				=	$subscription->get( 'status' );
						$planId							=	(int) $subscription->get( 'plan_id' );

						if ( in_array( $planId, $plans ) ) {
							switch ( $mode ) {
								case 2:
									if ( $subscriptionStatus != 'A' ) {
										$subscription->activate( $user, $_CB_framework->now(), true, 'R' );
									}
									break;
								case 3:
									if ( $subscriptionStatus == 'A' ) {
										cbpaidControllerOrder::doUnsubscribeConfirm( $user, null, $planId, $subscriptionId );
									}
									break;
								case 4:
									if ( $subscription->canDelete() ) {
										$subscription->revert( $user, 'Denied' );
										$subscription->delete();
									}
									break;
								case 1:
								default:
									if ( ( $subscriptionStatus == 'A' ) && ( ! in_array( $planId, $activePlans ) ) ) {
										$activePlans[]	=	$planId;
									}
									break;
							}
						}
					}
				}

				if ( $mode == 1 ) {
					$plansMgr							=	cbpaidPlansMgr::getInstance();
					$postData							=	array();
					$chosenPlans						=	array();

					foreach ( $plans as $planId ) {
						if ( ! in_array( $planId, $activePlans ) ) {
							$chosenPlans[$planId]		=	$plansMgr->loadPlan( $planId );
						}
					}

					if ( $chosenPlans ) {
						cbpaidControllerOrder::createSubscriptionsAndPayment( $user, $chosenPlans, $postData, null, null, 'A', null, 'U', 'free' );
					}
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function plans()
	{
		$plansList					=	array();

		if ( $this->installed() ) {
			$plansMgr				=	cbpaidPlansMgr::getInstance();
			$plans					=	$plansMgr->loadPublishedPlans( null, true, 'any', null );

			if ( $plans ) {
				$plansList			=	array();

				foreach ( $plans as $k => $plan ) {
					$plansList[]	=	moscomprofilerHTML::makeOption( (string) $k, $plan->get( 'alias' ) );
				}
			}
		}

		return $plansList;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbpaidsubscriptions' ) ) {
			return true;
		}

		return false;
	}
}