<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\Table\InviteTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveInviteEdit
{

	/**
	 * render frontend invite edit
	 *
	 * @param InviteTable        $row
	 * @param array              $input
	 * @param GroupTable         $group
	 * @param UserTable          $user
	 * @param CBplug_cbgroupjive $plugin
	 */
	static function showInviteEdit( $row, $input, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$js							=	"$( '.gjInviteConnection' ).on( 'change', function() {"
									.		"if ( $( this ).val() != 0 ) {"
									.			"$( '.gjInviteOther' ).addClass( 'hidden' );"
									.			"$( this ).parent().css({ display: 'inline' });"
									.		"} else {"
									.			"$( '.gjInviteOther' ).removeClass( 'hidden' );"
									.			"$( this ).parent().css({ display: 'block' });"
									.		"}"
									.	"});";

		$_CB_framework->outputCbJQuery( $js );

		$pageTitle					=	( $row->get( 'id' ) ? CBTxt::T( 'Edit Invite' ) : CBTxt::T( 'New Invite' ) );

		$_CB_framework->setPageTitle( $pageTitle );

		cbValidator::loadValidation();
		initToolTip();

		$isModerator				=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$return						=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayInviteEdit', array( &$return, &$row, &$input, $group, $user ) );

		$return						.=	'<div class="gjInviteEdit">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'invites', 'func' => 'save', 'id' => $row->get( 'id', null, GetterInterface::INT ) ) ) . '" method="post" enctype="multipart/form-data" name="gjInviteEditForm" id="gjInviteEditForm" class="cb_form gjInviteEditForm form-auto cbValidation">'
									.			( $pageTitle ? '<div class="gjInviteEditTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null );

		if ( ! $row->get( 'id' ) ) {
			$return					.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="to" class="col-sm-3 control-label">' . CBTxt::T( 'To' ) . '</label>'
									.				'<div class="cb_field col-sm-9">';

			if ( $plugin->params->get( 'groups_invites_list', 0 ) && $input['list'] ) {
				$return				.=					'<div style="margin-bottom: 10px;">' . $input['list'] . '</div>';
			}

			$return					.=					$input['to'];

			if ( $plugin->params->get( 'groups_invites_list', 0 ) && $input['list'] ) {
				$return				.=					getFieldIcons( null, 1, null, CBTxt::T( 'GROUP_INVITE_BY_LIST', 'Input the recipient as [invite_by] or select one of your connections.', array( '[invite_by]' => implode( ', ', $input['invite_by'] ) ) ) );
			} else {
				$return				.=					getFieldIcons( null, 1, null, CBTxt::T( 'GROUP_INVITE_BY', 'Input the recipient as [invite_by].', array( '[invite_by]' => implode( ', ', $input['invite_by'] ) ) ) );
			}

			$return					.=				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
									.				'<label for="message" class="col-sm-3 control-label">' . CBTxt::T( 'Message' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['message']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally input private message to include with the invite.' ) )
									.				'</div>'
									.			'</div>';

		if ( ( ! $isModerator ) && $plugin->params->get( 'groups_invites_captcha', 0 ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$captcha				=	$_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( false ) );

			if ( ! empty( $captcha ) ) {
				$captcha			=	$captcha[0];

				$return				.=			'<div class="form-group cb_form_line clearfix">'
									.				'<label class="col-sm-3 control-label">' . CBTxt::T( 'Captcha' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					( isset( $captcha[0] ) ? $captcha[0] : null )
									.				'</div>'
									.			'</div>'
									.			'<div class="form-group cb_form_line clearfix">'
									.				'<div class="cb_field col-sm-offset-3 col-sm-9">'
									.					str_replace( 'inputbox', 'form-control', ( isset( $captcha[1] ) ? $captcha[1] : null ) )
									.					getFieldIcons( null, 1, null )
									.				'</div>'
									.			'</div>';
			}
		}

		$return						.=			'<div class="form-group cb_form_line clearfix">'
									.				'<div class="col-sm-offset-3 col-sm-9">'
									.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id' ) ? CBTxt::T( 'Update Invite' ) : CBTxt::T( 'Create Invite' ) ) ) . '" class="gjButton gjButtonSubmit btn btn-primary" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="gjButton gjButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) ) . '\'; })" />'
									.				'</div>'
									.			'</div>'
									.			( ! $row->get( 'id' ) ? '<input type="hidden" id="group" name="group" value="' . (int) $group->get( 'id' ) . '" />' : null )
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayInviteEdit', array( &$return, $row, $input, $group, $user ) );

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}