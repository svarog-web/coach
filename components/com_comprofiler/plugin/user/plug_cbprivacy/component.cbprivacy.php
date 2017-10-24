<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBplug_cbprivacy extends cbPluginHandler
{

	/**
	 * @param null      $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @param array     $postdata
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_CB_framework;

		outputCbJs( 1 );
		outputCbTemplate( 1 );

		$action			=	$this->input( 'action', null, GetterInterface::STRING );
		$function		=	$this->input( 'func', null, GetterInterface::STRING );
		$id				=	$this->input( 'id', null, GetterInterface::INT );
		$user			=	CBuser::getMyUserDataInstance();
		$profileUrl		=	$_CB_framework->userProfileUrl( $user->get( 'id' ), false );

		if ( ! $user->get( 'id' ) ) {
			$profileUrl	=	'index.php';
		}

		ob_start();
		switch ( $action ) {
			case 'privacy':
				switch ( $function ) {
					case 'disable':
						$this->disableProfile( $id, $user );
						break;
					case 'disableuser':
						cbSpoofCheck( 'plugin' );
						$this->disableUser( $id, $user );
						break;
					case 'delete':
						$this->deleteProfile( $id, $user );
						break;
					case 'deleteuser':
						cbSpoofCheck( 'plugin' );
						$this->deleteUser( $id, $user );
						break;
					default:
						cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
						break;
				}
				break;
			default:
				cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				break;
		}
		$html			=	ob_get_contents();
		ob_end_clean();

		$class			=	$this->params->get( 'general_class', null );

		$return			=	'<div id="cbPrivacy" class="cbPrivacy' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
						.		'<div id="cbPrivacyInner" class="cbPrivacyInner">'
						.			$html
						.		'</div>'
						.	'</div>';

		echo $return;
	}

	/**
	 * @param int       $userId
	 * @param UserTable $user
	 * @return null|FieldTable
	 */
	private function getDisableField( $userId, $user )
	{
		if ( ( ! $userId ) || ( ( $userId != $user->get( 'id' ) ) && ( ! cbprivacyClass::checkUserModerator( $user->get( 'id' ) ) ) ) || cbprivacyClass::checkUserModerator( $userId ) ) {
			return false;
		}

		$fields		=	CBuser::getInstance( $userId, false )->_cbtabs->_getTabFieldsDb( null, $user, 'edit', 'privacy_disable_me' );

		return array_shift( $fields );
	}

	/**
	 * @param int       $userId
	 * @param UserTable $user
	 * @return mixed
	 */
	public function disableProfile( $userId, $user )
	{
		global $_CB_framework;

		if ( ! $userId ) {
			$userId			=	$user->get( 'id' );
		}

		$profileUrl			=	$_CB_framework->userProfileUrl( $userId, false );

		if ( ! $userId ) {
			$profileUrl		=	'index.php';
		}

		if ( ! $this->getDisableField( $userId, $user ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		cbprivacyClass::getTemplate( 'disable' );

		HTML_privacyDisable::showDisable( $userId, $user, $this );
	}

	/**
	 * @param int       $userId
	 * @param UserTable $user
	 * @return mixed
	 */
	public function disableUser( $userId, $user )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ! $userId ) {
			$userId						=	$user->get( 'id' );
		}

		$profileUrl						=	$_CB_framework->userProfileUrl( $userId, false );

		if ( ! $userId ) {
			$profileUrl					=	'index.php';
		}

		if ( $this->getDisableField( $userId, $user ) ) {
			$cbUser						=	CBuser::getInstance( $userId, false );
			$disableUser				=	$cbUser->getUserData();

			if ( $disableUser->get( 'id' ) ) {
				$_PLUGINS->trigger( 'privacy_onBeforeAccountDisable', array( &$disableUser, $user ) );

				$disableUser->set( 'block', 1 );

				if ( $disableUser->storeBlock() ) {
					$closed				=	new cbprivacyClosedTable();

					$closed->set( 'user_id', (int) $disableUser->get( 'id' ) );
					$closed->set( 'username', $disableUser->get( 'username' ) );
					$closed->set( 'name', $disableUser->get( 'name' ) );
					$closed->set( 'email', $disableUser->get( 'email' ) );
					$closed->set( 'type', 'disable' );
					$closed->set( 'date', $_CB_framework->getUTCDate() );
					$closed->set( 'reason', $this->input( 'reason', null, GetterInterface::STRING ) );

					$closed->store();

					$notification		=	new cbNotification();

					$extra				=	array(	'ip_address' => cbGetIPlist(),
													'reason' => $closed->get( 'reason' ),
													'date' => $closed->get( 'date' )
												);

					$subject			=	$cbUser->replaceUserVars( CBTxt::T( 'User Account Disabled' ), true, false, $extra, false );
					$body				=	$cbUser->replaceUserVars( CBTxt::T( 'Name: [name]<br />Username: [username]<br />Email: [email]<br />IP Address: [ip_address]<br />Date: [date]<br /><br />[reason]<br /><br />' ), false, false, $extra, false );

					if ( $subject && $body ) {
						$notification->sendToModerators( $subject, $body, false, 1 );
					}

					$subject			=	CBTxt::T( 'Your Account has been Disabled' );
					$body				=	CBTxt::T( 'This is a notice that your account [username] on [siteurl] has been disabled.' );

					if ( $subject && $body ) {
						$notification->sendFromSystem( $disableUser, $subject, $body, true, 1, null, null, null, $extra );
					}

					$_PLUGINS->trigger( 'privacy_onAfterAccountDisable', array( $disableUser, $user ) );

					cbRedirect( 'index.php', CBTxt::T( 'Account disabled successfully!' ) );
				} else {
					cbRedirect( $profileUrl, CBTxt::T( 'ACCOUNT_FAILED_TO_DISABLE', 'Account failed to disable! Error: [error]', array( '[error]' => $disableUser->getError() ) ), 'error' );
				}
			}
		}

		cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
	}

	/**
	 * @param int       $userId
	 * @param UserTable $user
	 * @return null|FieldTable
	 */
	private function getDeleteField( $userId, $user )
	{
		if ( ( ! $userId ) || ( ( $userId != $user->get( 'id' ) ) && ( ! cbprivacyClass::checkUserModerator( $user->get( 'id' ) ) ) ) || cbprivacyClass::checkUserModerator( $userId ) ) {
			return false;
		}

		$fields		=	CBuser::getInstance( $userId, false )->_cbtabs->_getTabFieldsDb( null, $user, 'edit', 'privacy_delete_me' );

		return array_shift( $fields );
	}

	/**
	 * @param int       $userId
	 * @param UserTable $user
	 * @return mixed
	 */
	public function deleteProfile( $userId, $user )
	{
		global $_CB_framework;

		if ( ! $userId ) {
			$userId			=	$user->get( 'id' );
		}

		$profileUrl			=	$_CB_framework->userProfileUrl( $userId, false );

		if ( ! $userId ) {
			$profileUrl		=	'index.php';
		}

		if ( ! $this->getDeleteField( $userId, $user ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		cbprivacyClass::getTemplate( 'delete' );

		HTML_privacyDelete::showDelete( $userId, $user, $this );
	}

	/**
	 * @param int       $userId
	 * @param UserTable $user
	 * @return mixed
	 */
	public function deleteUser( $userId, $user )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ! $userId ) {
			$userId					=	$user->get( 'id' );
		}

		$profileUrl					=	$_CB_framework->userProfileUrl( $userId, false );

		if ( ! $userId ) {
			$profileUrl				=	'index.php';
		}

		if ( $this->getDeleteField( $userId, $user ) ) {
			$cbUser					=	CBuser::getInstance( $userId, false );
			$deleteUser				=	$cbUser->getUserData();

			$_PLUGINS->trigger( 'privacy_onBeforeAccountDelete', array( &$deleteUser, $user ) );

			if ( $deleteUser->delete( $userId ) ) {
				$closed				=	new cbprivacyClosedTable();

				$closed->set( 'user_id', (int) $deleteUser->get( 'id' ) );
				$closed->set( 'username', $deleteUser->get( 'username' ) );
				$closed->set( 'name', $deleteUser->get( 'name' ) );
				$closed->set( 'email', $deleteUser->get( 'email' ) );
				$closed->set( 'type', 'delete' );
				$closed->set( 'date', $_CB_framework->getUTCDate() );
				$closed->set( 'reason', $this->input( 'reason', null, GetterInterface::STRING ) );

				$closed->store();

				$notification		=	new cbNotification();

				$extra				=	array(	'ip_address' => cbGetIPlist(),
												'reason' => $closed->get( 'reason' ),
												'date' => $closed->get( 'date' )
											);

				$subject			=	$cbUser->replaceUserVars( CBTxt::T( 'User Account Deleted' ), true, false, $extra, false );
				$body				=	$cbUser->replaceUserVars( CBTxt::T( 'Name: [name]<br />Username: [username]<br />Email: [email]<br />IP Address: [ip_address]<br />Date: [date]<br /><br />[reason]<br /><br />' ), false, false, $extra, false );

				if ( $subject && $body ) {
					$notification->sendToModerators( $subject, $body, false, 1 );
				}

				$subject			=	CBTxt::T( 'Your Account has been Deleted' );
				$body				=	CBTxt::T( 'This is a notice that your account [username] on [siteurl] has been deleted.' );

				if ( $subject && $body ) {
					$notification->sendFromSystem( $deleteUser, $subject, $body, true, 1, null, null, null, $extra );
				}

				$_PLUGINS->trigger( 'privacy_onAfterAccountDelete', array( $deleteUser, $user ) );

				cbRedirect( 'index.php', CBTxt::T( 'Account deleted successfully!' ) );
			} else {
				cbRedirect( $profileUrl, CBTxt::T( 'ACCOUNT_FAILED_TO_DELETE', 'Account failed to delete! Error: [error]', array( '[error]' => $deleteUser->getError() ) ), 'error' );
			}
		}

		cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
	}
}
?>