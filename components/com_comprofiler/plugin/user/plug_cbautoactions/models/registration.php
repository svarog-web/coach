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

class cbautoactionsActionRegistration extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_framework, $_PLUGINS, $ueConfig;

		$params						=	$trigger->getParams()->subTree( 'registration' );

		$approve					=	(int) $params->get( 'approve', null, GetterInterface::INT );
		$confirm					=	(int) $params->get( 'confirm', null, GetterInterface::INT );
		$approval					=	( $approve == 2 ? $ueConfig['reg_admin_approval'] : $approve );
		$confirmation				=	( $confirm == 2 ? $ueConfig['reg_confirmation'] : $confirm );
		$usergroup					=	$params->get( 'usergroup', null, GetterInterface::STRING );
		$password					=	$trigger->getSubstituteString( $params->get( 'password', null, GetterInterface::STRING ) );
		$name						=	array();

		if ( ! $usergroup ) {
			$gids					=	array( $_CB_framework->getCfg( 'new_usertype' ) );
		} else {
			$gids					=	explode( '|*|', $usergroup );
		}

		cbArrayToInts( $gids );

		$newUser					=	new UserTable();

		$newUser->set( 'gids', $gids );
		$newUser->set( 'sendEmail', 0 );
		$newUser->set( 'registerDate', $_CB_framework->getUTCDate() );
		$newUser->set( 'username', $trigger->getSubstituteString( $params->get( 'username', null, GetterInterface::STRING ) ) );
		$newUser->set( 'firstname', $trigger->getSubstituteString( $params->get( 'firstname', null, GetterInterface::STRING ) ) );
		$newUser->set( 'middlename', $trigger->getSubstituteString( $params->get( 'middlename', null, GetterInterface::STRING ) ) );
		$newUser->set( 'lastname', $trigger->getSubstituteString( $params->get( 'lastname', null, GetterInterface::STRING ) ) );

		if ( $newUser->get( 'firstname' ) ) {
			$name[]					=	$newUser->get( 'firstname' );
		}

		if ( $newUser->get( 'middlename' ) ) {
			$name[]					=	$newUser->get( 'middlename' );
		}

		if ( $newUser->get( 'lastname' ) ) {
			$name[]					=	$newUser->get( 'lastname' );
		}

		$newUser->set( 'name', implode( ' ', $name ) );
		$newUser->set( 'email', $trigger->getSubstituteString( $params->get( 'email', null, GetterInterface::STRING ) ) );

		if ( $password ) {
			$newUser->set( 'password', $newUser->hashAndSaltPassword( $password ) );
		} else {
			$newUser->setRandomPassword();

			$newUser->set( 'password', $newUser->hashAndSaltPassword( $newUser->get( 'password' ) ) );
		}

		$newUser->set( 'registeripaddr', cbGetIPlist() );

		if ( $approval == 0 ) {
			$newUser->set( 'approved', 1 );
		} else {
			$newUser->set( 'approved', 0 );
		}

		if ( $confirmation == 0 ) {
			$newUser->set( 'confirmed', 1 );
		} else {
			$newUser->set( 'confirmed', 0 );
		}

		if ( ( $newUser->get( 'confirmed' ) == 1 ) && ( $newUser->get( 'approved' ) == 1 ) ) {
			$newUser->set( 'block', 0 );
		} else {
			$newUser->set( 'block', 1 );
		}

		foreach ( $params->subTree( 'fields' ) as $row ) {
			/** @var ParamsInterface $row */
			$field					=	$row->get( 'field', null, GetterInterface::STRING );

			if ( $field ) {
				$newUser->set( $field, $trigger->getSubstituteString( $row->get( 'value', null, GetterInterface::RAW ), false, $row->get( 'translate', false, GetterInterface::BOOLEAN ) ) );
			}
		}

		$_PLUGINS->trigger( 'onBeforeUserRegistration', array( &$newUser, &$newUser ) );

		if ( ! $newUser->store() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_REGISTRATION_FAILED', ':: Action [action] :: Registration failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $newUser->getError() ) ) );
			}

			return;
		}

		if ( ( $newUser->get( 'confirmed' ) == 0 ) && ( $confirmation != 0 ) ) {
			if ( ! $newUser->store() ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_REGISTRATION_FAILED', ':: Action [action] :: Registration failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $newUser->getError() ) ) );
				}

				return;
			}
		}

		if ( $params->get( 'supress', 1, GetterInterface::BOOLEAN ) ) {
			$emails					=	false;
		} else {
			$emails					=	true;
		}

		activateUser( $newUser, 1, 'UserRegistration', $emails, $emails );

		$_PLUGINS->trigger( 'onAfterUserRegistration', array( &$newUser, &$newUser, true ) );
	}
}