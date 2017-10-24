<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbinvitesInviteEdit
{

	/**
	 * @param cbinvitesInviteTable $row
	 * @param array                $input
	 * @param UserTable            $user
	 * @param cbPluginHandler      $plugin
	 */
	static function showInviteEdit( $row, $input, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		cbValidator::loadValidation();

		$cbModerator		=	Application::User( (int) $user->get( 'id' ) )->isGlobalModerator();
		$pageTitle			=	( $row->get( 'to' ) ? CBTxt::T( 'Edit Invite' ) : CBTxt::T( 'Create Invite' ) );

		$_CB_framework->setPageTitle( $pageTitle );
		$_CB_framework->appendPathWay( htmlspecialchars( CBTxt::T( 'Invites' ) ), $_CB_framework->userProfileUrl( $row->get( 'user', $user->get( 'id' ) ), true, 'cbinvitesTab' ) );
		$_CB_framework->appendPathWay( htmlspecialchars( $pageTitle ), $_CB_framework->pluginClassUrl( $plugin->element, true, ( $row->get( 'id' ) ? array( 'action' => 'invites', 'func' => 'edit', 'id' => (int) $row->get( 'id' ) ) : array( 'action' => 'invites', 'func' => 'new' ) ) ) );

		initToolTip();

		$return				=	'<div class="invitesEdit">'
							.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'invites', 'func' => 'save', 'id' => (int) $row->get( 'id' ) ) ) . '" method="post" enctype="multipart/form-data" name="invitesForm" id="invitesForm" class="cb_form invitesForm form-auto cbValidation">'
							.			( $pageTitle ? '<div class="invitesTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
							.			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
							.				'<label for="to" class="col-sm-3 control-label">' . CBTxt::T( 'To' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					$input['to']
							.					getFieldIcons( 1, 1, null, ( $plugin->params->get( 'invite_multiple', 1 ) ? CBTxt::T( 'Input invite email to address. Separate multiple email addresses with a comma.' ) : CBTxt::T( 'Input invite email to address.' ) ) )
							.				'</div>'
							.			'</div>'
							.			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
							.				'<label for="subject" class="col-sm-3 control-label">' . CBTxt::T( 'Subject' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					$input['subject']
							.					getFieldIcons( 1, 0, null, CBTxt::T( 'Input invite email subject; if left blank a subject will be applied.' ) )
							.				'</div>'
							.			'</div>'
							.			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
							.				'<label for="body" class="col-sm-3 control-label">' . CBTxt::T( 'Body' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					$input['body']
							.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input private message to include with invite email.' ) )
							.				'</div>'
							.			'</div>';

		if ( $cbModerator ) {
			$return			.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
							.				'<label for="user_id" class="col-sm-3 control-label">' . CBTxt::T( 'Owner' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					$input['user_id']
							.					getFieldIcons( 1, 1, null, CBTxt::T( 'Input owner of invite as single integer user_id. This is the user who sent the invite.' ) )
							.				'</div>'
							.			'</div>'
							.			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
							.				'<label for="user" class="col-sm-3 control-label">' . CBTxt::T( 'User' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					$input['user']
							.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input user of invite as single integer user_id. This is the user who accepted the invite.' ) )
							.				'</div>'
							.			'</div>';
		}

		if ( $plugin->params->get( 'invite_captcha', 0 ) && ( ! $cbModerator ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$captcha		=	$_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( false ) );

			if ( ! empty( $captcha ) ) {
				$captcha	=	$captcha[0];

				$return		.=			'<div class="form-group cb_form_line clearfix">'
							.				'<label class="col-sm-3 control-label">' . CBTxt::Th( 'Captcha' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					( isset( $captcha[0] ) ? $captcha[0] : null )
							.				'</div>'
							.			'</div>'
							.			'<div class="form-group cb_form_line clearfix">'
							.				'<div class="cb_field col-sm-offset-3 col-sm-9">'
							.					str_replace( 'inputbox', 'form-control', ( isset( $captcha[1] ) ? $captcha[1] : null ) )
							.					getFieldIcons( 1, 1, null )
							.				'</div>'
							.			'</div>';
			}
		}

		$return				.=			'<div class="form-group cb_form_line clearfix">'
							.				'<div class="col-sm-offset-3 col-sm-9">'
							.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id' ) ? CBTxt::T( 'Update Invite' ) : CBTxt::T( 'Send Invite' ) ) ) . '" class="invitesButton invitesButtonSubmit btn btn-primary"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />&nbsp;'
							.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="invitesButton invitesButtonCancel btn btn-default" onclick="if ( confirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ) ) { location.href = \'' . $_CB_framework->userProfileUrl( $row->get( 'user', $user->get( 'id' ) ), false, 'cbinvitesTab' ) . '\'; }" />'
							.				'</div>'
							.			'</div>'
							.			cbGetSpoofInputTag( 'plugin' )
							.		'</form>'
							.	'</div>';

		echo $return;
	}
}
?>