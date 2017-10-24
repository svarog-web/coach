<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Application\Application;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBplug_cbinvites extends cbPluginHandler
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

		$action					=	$this->input( 'action', null, GetterInterface::STRING );
		$function				=	$this->input( 'func', null, GetterInterface::STRING );
		$id						=	$this->input( 'id', null, GetterInterface::INT );
		$user					=	CBuser::getMyUserDataInstance();

		$tab					=	new TabTable();

		$tab->load( array( 'pluginclass' => 'cbinvitesTab' ) );

		$profileUrl				=	$_CB_framework->userProfileUrl( $user->get( 'id' ), false, 'cbinvitesTab' );

		if ( ! ( $tab->enabled && Application::MyUser()->canViewAccessLevel( $tab->viewaccesslevel ) ) ) {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}

		ob_start();
		switch ( $action ) {
			case 'invites':
				switch ( $function ) {
					case 'new':
						$this->showInviteEdit( null, $user );
						break;
					case 'edit':
						$this->showInviteEdit( $id, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveInviteEdit( $id, $user );
						break;
					case 'send':
						$this->sendInvite( $id, $user );
						break;
					case 'delete':
						$this->deleteInvite( $id, $user );
						break;
					case 'show':
					default:
						cbRedirect( $profileUrl );
						break;
				}
				break;
			default:
				cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
				break;
		}
		$html					=	ob_get_contents();
		ob_end_clean();

		$class					=	$this->params->get( 'general_class', null );

		$return					=	'<div id="cbInvites" class="cbInvites' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
								.		'<div id="cbInvitesInner" class="cbInvitesInner">'
								.			$html
								.		'</div>'
								.	'</div>';

		echo $return;
	}

	/**
	 * @param null|int    $id
	 * @param UserTable   $user
	 * @param null|string $message
	 * @param null|string $messageType
	 */
	public function showInviteEdit( $id, $user, $message = null, $messageType = 'error' )
	{
		global $_CB_framework, $_CB_database;

		$inviteLimit						=	(int) $this->params->get( 'invite_limit', null );
		$cbModerator						=	Application::User( (int) $user->get( 'id' ) )->isGlobalModerator();

		$row								=	new cbinvitesInviteTable();

		$row->load( (int) $id );

		$canAccess							=	false;

		if ( ! $row->get( 'id' ) ) {
			if ( $cbModerator ) {
				$canAccess					=	true;
			} elseif ( $user->get( 'id' ) && Application::MyUser()->canViewAccessLevel( $this->params->get( 'invite_create_access', 2 ) ) ) {
				if ( $inviteLimit ) {
					$query					=	'SELECT COUNT(*)'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' )
											.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
											.	"\n AND ( " . $_CB_database->NameQuote( 'user' ) . " IS NULL OR " . $_CB_database->NameQuote( 'user' ) . " = " . $_CB_database->Quote( '' ) . " )";
					$_CB_database->setQuery( $query );
					$inviteCount			=	(int) $_CB_database->loadResult();

					if ( $inviteCount < $inviteLimit ) {
						$canAccess			=	true;
					}
				} else {
					$canAccess				=	true;
				}
			}
		} elseif ( $cbModerator || ( $row->get( 'user_id' ) == $user->get( 'id' ) ) ) {
			$canAccess						=	true;
		}

		$profileUrl							=	$_CB_framework->userProfileUrl( $row->get( 'user_id', $user->get( 'id' ) ), false, 'cbinvitesTab' );

		if ( $canAccess && ( ! $row->isAccepted() ) ) {
			$inviteEditor					=	$this->params->get( 'invite_editor', 2 );

			cbinvitesClass::getTemplate( 'invite_edit' );

			$input							=	array();

			$toTooltip						=	cbTooltip( null, ( $this->params->get( 'invite_multiple', 1 ) ? CBTxt::T( 'Input invite email to address. Separate multiple email addresses with a comma.' ) : CBTxt::T( 'Input invite email to address.' ) ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['to']					=	'<input type="text" id="to" name="to" value="' . htmlspecialchars( $this->input( 'post/to', $row->get( 'to' ), GetterInterface::STRING ) ) . '" class="required form-control" size="35"' . ( $toTooltip ? ' ' . $toTooltip : null ) . ' />';

			$subjectTooltip					=	cbTooltip( null, CBTxt::T( 'Input invite email subject; if left blank a subject will be applied.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['subject']				=	'<input type="text" id="subject" name="subject" value="' . htmlspecialchars( $this->input( 'post/subject', $row->get( 'subject' ), GetterInterface::STRING ) ) . '" class="form-control" size="25"' . ( $subjectTooltip ? ' ' . $subjectTooltip : null ) . ' />';

			if ( $inviteEditor >= 2 ) {
				$body						=	$this->input( 'post/body', $row->get( 'body' ), GetterInterface::HTML );
			} else {
				$body						=	$this->input( 'post/body', $row->get( 'body' ), GetterInterface::STRING );
			}

			if ( $inviteEditor == 3 ) {
				$input['body']				=	cbTooltip( null, CBTxt::T( 'Optionally input private message to include with invite email.' ), null, null, null, $_CB_framework->displayCmsEditor( 'body', $body, 350, 175, 35, 4 ), null, 'style="display:block;"' );
			} else {
				$bodyTooltip				=	cbTooltip( null, CBTxt::T( 'Optionally input private message to include with invite email.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

				$input['body']				=	'<textarea id="body" name="body" class="form-control" cols="35" rows="4"' . ( $bodyTooltip ? ' ' . $bodyTooltip : null ) . '>' . htmlspecialchars( $body ) . '</textarea>';
			}

			$ownerTooltip					=	cbTooltip( null, CBTxt::T( 'Input owner of invite as single integer user_id. This is the user who sent the invite.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['user_id']				=	'<input type="text" id="user_id" name="user_id" value="' . (int) $this->input( 'post/user_id', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ) . '" class="digits required form-control" size="6"' . ( $ownerTooltip ? ' ' . $ownerTooltip : null ) . ' />';

			$userTooltip					=	cbTooltip( null, CBTxt::T( 'Optionally input user of invite as single integer user_id. This is the user who accepted the invite.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['user']					=	'<input type="text" id="user" name="user" value="' . (int) $this->input( 'post/user', $row->get( 'user' ), GetterInterface::INT ) . '" class="digits form-control" size="6"' . ( $userTooltip ? ' ' . $userTooltip : null ) . ' />';

			if ( $message ) {
				$_CB_framework->enqueueMessage( $message, $messageType );
			}

			HTML_cbinvitesInviteEdit::showInviteEdit( $row, $input, $user, $this );
		} else {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}
	}

	/**
	 * @param null|int  $id
	 * @param UserTable $user
	 */
	private function saveInviteEdit( $id, $user )
	{
		global $_CB_framework, $_CB_database, $_PLUGINS;

		$inviteLimit						=	(int) $this->params->get( 'invite_limit', null );
		$cbModerator						=	Application::User( (int) $user->get( 'id' ) )->isGlobalModerator();

		$row								=	new cbinvitesInviteTable();

		$row->load( (int) $id );

		$canAccess							=	false;
		$inviteCount						=	0;

		if ( ! $row->get( 'id' ) ) {
			if ( $cbModerator ) {
				$canAccess					=	true;
			} elseif ( $user->get( 'id' ) && Application::MyUser()->canViewAccessLevel( $this->params->get( 'invite_create_access', 2 ) ) ) {
				if ( $inviteLimit ) {
					$query					=	'SELECT COUNT(*)'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' )
											.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
											.	"\n AND ( " . $_CB_database->NameQuote( 'user' ) . " IS NULL OR " . $_CB_database->NameQuote( 'user' ) . " = " . $_CB_database->Quote( '' ) . " )";
					$_CB_database->setQuery( $query );
					$inviteCount			=	(int) $_CB_database->loadResult();

					if ( $inviteCount < $inviteLimit ) {
						$canAccess			=	true;
					}
				} else {
					$canAccess				=	true;
				}
			}
		} elseif ( $cbModerator || ( $row->get( 'user_id' ) == $user->get( 'id' ) ) ) {
			$canAccess						=	true;
		}

		$profileUrl							=	$_CB_framework->userProfileUrl( $row->get( 'user_id', $user->get( 'id' ) ), false, 'cbinvitesTab' );

		if ( $canAccess && ( ! $row->isAccepted() ) ) {
			$toArray						=	explode( ',', $this->input( 'post/to', null, GetterInterface::STRING ) );

			if ( ( ! $this->params->get( 'invite_multiple', 1 ) ) && ( ! $cbModerator ) && ( count( $toArray ) > 1 ) ) {
				$this->showInviteEdit( $row->get( 'id' ), $user, CBTxt::T( 'Comma seperated lists are not supported! Please use a single To address.' ) ); return;
			}

			$sent							=	false;

			if ( ! empty( $toArray ) ) {
				foreach ( $toArray as $k => $to ) {
					if ( $k != 0 ) {
						$row->set( 'id', null );
						$row->set( 'code', null );
					}

					$orgTo					=	$row->get( 'to' );

					$row->set( 'to', $to );
					$row->set( 'subject', $this->input( 'post/subject', $row->get( 'subject' ), GetterInterface::STRING ) );

					if ( $this->params->get( 'invite_editor', 2 ) >= 2 ) {
						$row->set( 'body', $this->input( 'post/body', $row->get( 'body' ), GetterInterface::HTML ) );
					} else {
						$row->set( 'body', $this->input( 'post/body', $row->get( 'body' ), GetterInterface::STRING ) );
					}

					$row->set( 'user_id', (int) $this->input( 'post/user_id', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ) );

					if ( $cbModerator ) {
						$row->set( 'user', (int) $this->input( 'post/user', $row->get( 'user' ), GetterInterface::INT ) );
					}

					$new					=	( $row->get( 'id' ) ? false : true );

					if ( $new && $inviteLimit ) {
						$inviteCount++;

						if ( $inviteCount > $inviteLimit ) {
							cbRedirect( $profileUrl, CBTxt::T( 'Invite limit reached!' ), 'error' );
						}
					}

					if ( ! $row->get( 'user' ) ) {
						$toUser				=	new UserTable();

						$toUser->loadByEmail( $row->get( 'to' ) );
					} else {
						$toUser				=	CBuser::getUserDataInstance( (int) $row->get( 'user' ) );
					}

					if ( ! $row->get( 'to' ) ) {
						$row->setError( CBTxt::T( 'To address not specified.' ) );
					} elseif ( ! cbIsValidEmail( $row->get( 'to' ) ) ) {
						$row->setError( CBTxt::T( 'INVITE_TO_ADDRESS_INVALID', 'To address not valid: [to_address]', array( '[to_address]' => $row->get( 'to' ) ) ) );
					} elseif ( $toUser->id == $row->get( 'user_id' ) ) {
						$row->setError( CBTxt::T( 'You can not invite your self.' ) );
					} elseif ( $toUser->id && ( $row->get( 'to' ) != $orgTo ) ) {
						$row->setError( CBTxt::T( 'To address is already a user.' ) );
					} elseif ( ( ! $this->params->get( 'invite_duplicate', 0 ) ) && ( ! $cbModerator ) && $row->isDuplicate() ) {
						$row->setError( CBTxt::T( 'To address is already invited.' ) );
					} elseif ( $this->params->get( 'invite_captcha', 0 ) && ( ! $row->get( 'id' ) ) && ( $k == 0 ) && ( ! $cbModerator ) ) {
						$_PLUGINS->loadPluginGroup( 'user' );

						$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

						if ( $_PLUGINS->is_errors() ) {
							$row->setError( CBTxt::T( $_PLUGINS->getErrorMSG() ) );
						}
					}

					$_PLUGINS->trigger( 'invites_onBeforeInvite', array( &$row, $user ) );

					if ( $row->getError() || ( ! $row->store() ) ) {
						$this->showInviteEdit( $row->get( 'id' ), $user, CBTxt::T( 'INVITE_FAILED_SAVE_ERROR', 'Invite failed to save! Error: [error]', array( '[error]' => $row->getError() ) ) ); return;
					}

					if ( ( $new || ( ! $row->isSent() ) ) && ( ! $toUser->id ) ) {
						if ( ! $row->send() ) {
							$this->showInviteEdit( $row->get( 'id' ), $user, CBTxt::T( 'INVITE_FAILED_SEND_ERROR', 'Invite failed to send! Error: [error]', array( '[error]' => $row->getError() ) ) ); return;
						} else {
							$sent			=	true;
						}
					}

					$_PLUGINS->trigger( 'invites_onAfterInvite', array( $row, $sent, $user ) );
				}

				cbRedirect( $profileUrl, ( $sent ? CBTxt::T( 'Invite sent successfully!' ) : CBTxt::T( 'Invite saved successfully!' ) ) );
			} else {
				$this->showInviteEdit( $row->get( 'id' ), $user, CBTxt::T( 'To address not specified.' ) ); return;
			}
		} else {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}
	}

	/**
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function sendInvite( $id, $user )
	{
		global $_CB_framework;

		$cbModerator			=	Application::User( (int) $user->get( 'id' ) )->isGlobalModerator();

		$row					=	new cbinvitesInviteTable();

		$row->load( (int) $id );

		$canAccess				=	false;

		if ( $row->get( 'id' ) && ( $cbModerator || ( $row->get( 'user_id' ) == $user->get( 'id' ) ) ) ) {
			$canAccess			=	true;
		}

		$profileUrl				=	$_CB_framework->userProfileUrl( $row->get( 'user_id', $user->get( 'id' ) ), false, 'cbinvitesTab' );

		if ( $canAccess ) {
			if ( $row->isAccepted() ) {
				cbRedirect( $profileUrl, CBTxt::T( 'Invite already accepted and can not be resent.' ), 'error' );
			}

			if ( ! $row->canResend() ) {
				cbRedirect( $profileUrl, CBTxt::T( 'Invite resend not applicable at this time.' ), 'error' );
			}

			if ( ! $row->send() ) {
				cbRedirect( $profileUrl, CBTxt::T( 'INVITE_FAILED_SEND_ERROR', 'Invite failed to send! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			cbRedirect( $profileUrl, CBTxt::T( 'Invite sent successfully!' ) );
		} else {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}
	}

	/**
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteInvite( $id, $user )
	{
		global $_CB_framework;

		$cbModerator			=	Application::User( (int) $user->get( 'id' ) )->isGlobalModerator();

		$row					=	new cbinvitesInviteTable();

		$row->load( (int) $id );

		$canAccess				=	false;

		if ( $row->get( 'id' ) && ( $cbModerator || ( $row->get( 'user_id' ) == $user->get( 'id' ) ) ) ) {
			$canAccess			=	true;
		}

		$profileUrl				=	$_CB_framework->userProfileUrl( $row->get( 'user_id', $user->get( 'id' ) ), false, 'cbinvitesTab' );

		if ( $canAccess ) {
			if ( $row->isAccepted() ) {
				cbRedirect( $profileUrl, CBTxt::T( 'Invite already accepted and can not be deleted.' ), 'error' );
			}

			if ( ! $row->delete() ) {
				cbRedirect( $profileUrl, CBTxt::T( 'INVITE_FAILED_DELETE_ERROR', 'Invite failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			cbRedirect( $profileUrl, CBTxt::T( 'Invite deleted successfully!' ) );
		} else {
			cbRedirect( $profileUrl, CBTxt::T( 'Not authorized.' ), 'error' );
		}
	}
}
?>