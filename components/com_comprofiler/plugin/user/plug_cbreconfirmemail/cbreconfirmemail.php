<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Database\DatabaseDriverInterface;
use CBLib\Database\Table\Table;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CBLib\Input\Get;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Application\Application;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onAfterUserUpdate', 'reconfirmEmail', 'CBplug_cbreconfirmemail' );

class CBplug_cbreconfirmemail extends cbPluginHandler
{
	/**
	 * @var array The array of users who have changed their email
	 */
	private $changed = array();

	/**
	 * @param TabTable   $tab
	 * @param UserTable  $user
	 * @param int        $ui
	 * @param array      $postdata
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_CB_database, $_PLUGINS;

		$action							=	$this->input( 'action', null, GetterInterface::STRING );
		$confirmcode					=	$this->input( 'confirmcode', null, GetterInterface::STRING );

		if ( ! $confirmcode ) {
			cbRedirect( 'index.php', CBTxt::T( 'Confirm code missing.' ), 'error' );
		}

		$userId							=	UserTable::getUserIdFromActivationCode( $confirmcode );

		if ( ! $userId ) {
			cbRedirect( 'index.php', CBTxt::T( 'User not associated with confirm code.' ), 'error' );
		}

		$cbUser							=	CBuser::getInstance( (int) $userId, false );
		$user							=	$cbUser->getUserData();

		if ( ! $user->checkActivationCode( $confirmcode ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Confirm code is not valid.' ), 'error' );
		}

		$reconfirmEmail					=	new cbreconfirmEmailTable();

		$reconfirmEmail->load( array( 'user_id' => (int) $user->get( 'id' ), 'code' => $confirmcode, 'status' => 'P' ) );

		if ( $reconfirmEmail->get( 'id' ) ) {
			if ( $action == 'cancel' ) {
				$_PLUGINS->trigger( 'reconfirm_onBeforeCancel', array( &$user, &$reconfirmEmail, $confirmcode ) );

				$this->changed[$userId]	=	true;

				if ( $user->storeDatabaseValue( 'cbactivation', '' ) ) {
					// Cancel all change requests as we wipe the activation code they are no longer valid or functional:
					$query				=	'UPDATE ' . $_CB_database->NameQuote( '#__comprofiler_plugin_emails' )
										.	"\n SET " . $_CB_database->NameQuote( 'status' ) . " = " . $_CB_database->Quote( 'X' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
										.	"\n AND " . $_CB_database->NameQuote( 'to' ) . " != " . $_CB_database->Quote( $user->get( 'email' ) )
										.	"\n AND " . $_CB_database->NameQuote( 'status' ) . " != " . $_CB_database->Quote( 'A' );
					$_CB_database->setQuery( $query );
					$_CB_database->query();

					$_PLUGINS->trigger( 'reconfirm_onAfterCancel', array( $user, $reconfirmEmail, $confirmcode ) );

					$redirect			=	$cbUser->replaceUserVars( $this->params->get( 'reconfirm_cancel_redirect' ), false, false, null, false );
					$message			=	$cbUser->replaceUserVars( CBTxt::T( $this->params->get( 'reconfirm_cancel_message', 'Email address change cancelled successfully!' ) ), true, false, null, false );

					if ( $redirect ) {
						cbRedirect( cbSef( $redirect, false ), $message );
					} else {
						echo '<div>' . $message . '</div>';
					}
				} else {
					cbRedirect( 'index.php', CBTxt::T( 'FAILED_CANCEL_EMAIL_CHANGE', 'Failed to cancel email address change! Error: [error]', array( '[error]' => $user->getError() ) ), 'error' );
				}
			} else {
				$_PLUGINS->trigger( 'reconfirm_onBeforeConfirm', array( &$user, &$reconfirmEmail, $confirmcode ) );

				$this->changed[$userId]	=	true;

				$data					=	array(	'email' => $reconfirmEmail->get( 'to' ),
													'cbactivation' => '',
												);

				if ( $user->storeDatabaseValues( $data ) ) {
					// Update the change request to Active for this code:
					$query				=	'UPDATE ' . $_CB_database->NameQuote( '#__comprofiler_plugin_emails' )
										.	"\n SET " . $_CB_database->NameQuote( 'status' ) . " = " . $_CB_database->Quote( 'A' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $reconfirmEmail->get( 'id' );
					$_CB_database->setQuery( $query );
					$_CB_database->query();

					// Cancel all other requests as they're no longer valid:
					$query				=	'UPDATE ' . $_CB_database->NameQuote( '#__comprofiler_plugin_emails' )
										.	"\n SET " . $_CB_database->NameQuote( 'status' ) . " = " . $_CB_database->Quote( 'X' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " != " . (int) $reconfirmEmail->get( 'id' );
					$_CB_database->setQuery( $query );
					$_CB_database->query();

					$_PLUGINS->trigger( 'reconfirm_onAfterConfirm', array( $user, $reconfirmEmail, $confirmcode ) );

					$redirect			=	$cbUser->replaceUserVars( $this->params->get( 'reconfirm_redirect' ), false, false, null, false );
					$message			=	$cbUser->replaceUserVars( CBTxt::T( $this->params->get( 'reconfirm_message', 'New email address confirmed successfully!' ) ), true, false, null, false );

					if ( $redirect ) {
						cbRedirect( cbSef( $redirect, false ), $message );
					} else {
						echo '<div>' . $message . '</div>';
					}
				} else {
					cbRedirect( 'index.php', CBTxt::T( 'FAILED_RECONFIRM_EMAIL', 'Failed to confirm new email address! Error: [error]', array( '[error]' => $user->getError() ) ), 'error' );
				}
			}
		} else {
			cbRedirect( 'index.php', CBTxt::T( 'Email address has already been confirmed.' ) );
		}
	}

	/**
	 * @param UserTable $user
	 * @param UserTable $user
	 * @param UserTable $oldUser
	 */
	public function reconfirmEmail( &$user, &$userDuplicate, $oldUser )
	{
		global $_CB_framework;

		$userId								=	(int) $user->get( 'user_id' );

		if ( ! isset( $this->changed[$userId] ) ) {
			$newEmail						=	$user->get( 'email' );
			$oldEmail						=	$oldUser->get( 'email' );

			if ( ( $newEmail != $oldEmail ) && ( ! Application::MyUser()->isGlobalModerator() ) && ( ! Application::Cms()->getClientId() ) ) {
				$this->changed[$userId]		=	true;

				$cbUser						=	CBuser::getInstance( (int) $user->get( 'user_id' ), false );

				$user->_setActivationCode();

				$reconfirmEmail				=	new cbreconfirmEmailTable();

				$reconfirmEmail->set( 'user_id', (int) $user->get( 'id' ) );
				$reconfirmEmail->set( 'from', $oldEmail );
				$reconfirmEmail->set( 'to', $newEmail );
				$reconfirmEmail->set( 'code', $user->get( 'cbactivation' ) );
				$reconfirmEmail->set( 'date', Application::Database()->getUtcDateTime() );
				$reconfirmEmail->set( 'status', 'P' );

				$reconfirmEmail->store();

				$reconfirm					=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'confirmcode' => $user->get( 'cbactivation' ) ) );
				$cancel						=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'confirmcode' => $user->get( 'cbactivation' ), 'action' => 'cancel' ) );

				$extra						=	array(	'reconfirm'	=>	$reconfirm,
														'cancel'	=>	$cancel,
														'new_email'	=>	$newEmail,
														'old_email'	=>	$oldEmail
													);

				$savedLanguage				=	CBTxt::setLanguage( $user->getUserLanguage() );

				$mailFromName				=	Get::clean( $cbUser->replaceUserVars( $this->params->get( 'reconfirm_from_name', null ), true, false, $extra ), GetterInterface::STRING );
				$mailFromAddr				=	Get::clean( $cbUser->replaceUserVars( $this->params->get( 'reconfirm_from_address', null ), true, false, $extra ), GetterInterface::STRING );
				$mailSubject				=	Get::clean( $cbUser->replaceUserVars( $this->params->get( 'reconfirm_subject', 'Your email address has changed' ), true, false, $extra ), GetterInterface::STRING );
				$mailBody					=	Get::clean( $cbUser->replaceUserVars( $this->params->get( 'reconfirm_body', 'The email address attached to your account [username] has changed to [new_email] and requires confirmation.<br><br>You can confirm your email address by clicking on the following link:<br><a href="[reconfirm]">[reconfirm]</a><br><br>If this was done in error please contact administration or cancel by <a href="[cancel]">clicking here</a>.' ), false, false, $extra ), GetterInterface::HTML );
				$mailCC						=	Get::clean( $cbUser->replaceUserVars( $this->params->get( 'reconfirm_cc', null ), true, false, $extra ), GetterInterface::STRING );
				$mailBCC					=	Get::clean( $cbUser->replaceUserVars( $this->params->get( 'reconfirm_bcc', null ), true, false, $extra ), GetterInterface::STRING );
				$mailAttachments			=	Get::clean( $cbUser->replaceUserVars( $this->params->get( 'reconfirm_attachments', null ), true, false, $extra ), GetterInterface::STRING );

				CBTxt::setLanguage( $savedLanguage );

				if ( $mailCC ) {
					$mailCC					=	preg_split( '/ *, */', $mailCC );
				}

				if ( $mailBCC ) {
					$mailBCC				=	preg_split( '/ *, */', $mailBCC );
				}

				if ( $mailAttachments ) {
					$mailAttachments		=	preg_split( '/ *, */', $mailAttachments );
				}

				$cbNotification				=	new cbNotification();

				$cbNotification->sendFromSystem( $user, $mailSubject, $mailBody, false, 1, $mailCC, $mailBCC, $mailAttachments, $extra, true, $mailFromName, $mailFromAddr );

				$message					=	CBTxt::T( $this->params->get( 'reconfirm_changed', 'Your email address has changed and requires reconfirmation. Please check your new email address for your confirmation email.' ) );

				if ( $message ) {
					$_CB_framework->enqueueMessage( $message, 'message' );
				}

				$user->storeDatabaseValue( 'email', $oldEmail );
			}
		}
	}
}

class cbreconfirmEmailTable extends Table
{
	var $id			=	null;
	var $user_id	=	null;
	var $from		=	null;
	var $to			=	null;
	var $code		=	null;
	var $date		=	null;
	var $status		=	null;

	/**
	 * Table constructor
	 *
	 * @param  DatabaseDriverInterface|null  $db
	 */
	public function __construct( $db = null )
	{
		parent::__construct( $db, '#__comprofiler_plugin_emails', 'id' );
	}
}
