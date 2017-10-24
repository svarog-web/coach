<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionAcymailing extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_framework;

		if ( ! $this->installed() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_ACYMAILING_NOT_INSTALLED', ':: Action [action] :: AcyMailing is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		$params							=	$trigger->getParams()->subTree( 'acymailing' );

		require_once( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_acymailing/helpers/helper.php' );

		/** @var subscriberClass $acySubscriberAPI */
		$acySubscriberAPI				=	acymailing::get( 'class.subscriber' );
		$subscriberId					=	$acySubscriberAPI->subid( (int) $user->get( 'id' ) );

		if ( ! $subscriberId ) {
			$newSubscriber				=	new stdClass();
			$newSubscriber->email		=	$user->get( 'email' );
			$newSubscriber->userid		=	(int) $user->get( 'id' );
			$newSubscriber->name		=	$user->get( 'name' );
			$newSubscriber->created		=	$_CB_framework->getUTCTimestamp( $user->get( 'registerDate' ) );
			$newSubscriber->confirmed	=	1;
			$newSubscriber->enabled		=	1;
			$newSubscriber->accept		=	1;
			$newSubscriber->ip			=	$user->get( 'registeripaddr' );
			$newSubscriber->html		=	1;

			$subscriberId				=	$acySubscriberAPI->save( $newSubscriber );
		}

		if ( ! $subscriberId ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_ACYMAILING_NO_SUB', ':: Action [action] :: AcyMailing skipped due to missing subscriber id', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		$lists							=	array();

		$subscribe						=	$params->get( 'subscribe' );

		if ( $subscribe ) {
			$subscribe					=	explode( '|*|', $subscribe );

			cbArrayToInts( $subscribe );

			foreach ( $subscribe as $listId ) {
				$lists[$listId]			=	array( 'status' => 1 );
			}
		}

		$unsubscribe					=	$params->get( 'unsubscribe' );

		if ( $unsubscribe ) {
			$unsubscribe				=	explode( '|*|', $unsubscribe );

			cbArrayToInts( $unsubscribe );

			foreach ( $unsubscribe as $listId ) {
				$lists[$listId]			=	array( 'status' => -1 );
			}
		}

		$remove							=	$params->get( 'remove' );

		if ( $remove ) {
			$remove						=	explode( '|*|', $remove );

			cbArrayToInts( $remove );

			foreach ( $remove as $listId ) {
				$lists[$listId]			=	array( 'status' => 0 );
			}
		}

		$pending						=	$params->get( 'pending' );

		if ( $pending ) {
			$pending					=	explode( '|*|', $pending );

			cbArrayToInts( $pending );

			foreach ( $pending as $listId ) {
				$lists[$listId]			=	array( 'status' => 2 );
			}
		}

		if ( $lists ) {
			$acySubscriberAPI->saveSubscription( $subscriberId, $lists );
		}
	}

	/**
	 * @return array
	 */
	public function lists()
	{
		global $_CB_framework;

		$lists					=	array();

		if ( $this->installed() ) {
			require_once( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_acymailing/helpers/helper.php' );

			/** @var listClass $acyListAPI */
			$acyListAPI			=	acymailing::get( 'class.list' );
			$acyLists			=	$acyListAPI->getLists();

			if ( $acyLists ) {
				foreach ( $acyLists as $acyList ) {
					$lists[]	=	moscomprofilerHTML::makeOption( (string) $acyList->listid, $acyList->name );
				}
			}
		}

		return $lists;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_CB_framework;

		if ( file_exists( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_acymailing/helpers/helper.php' ) ) {
			return true;
		}

		return false;
	}
}