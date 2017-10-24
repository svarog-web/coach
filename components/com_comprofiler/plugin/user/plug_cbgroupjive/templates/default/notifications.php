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
use CB\Plugin\GroupJive\Table\NotificationTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveNotifications
{

	/**
	 * render frontend notifications
	 *
	 * @param NotificationTable  $row
	 * @param array              $input
	 * @param GroupTable         $group
	 * @param UserTable          $user
	 * @param CBplug_cbgroupjive $plugin
	 */
	static function showNotifications( $row, $input, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$js							=	"$( '.gjToggleNotifications' ).on( 'change', function() {"
									.		"if ( $( this ).val() == 0 ) {"
									.			"$( '.gjNotificationsForm' ).find( 'select:not(.gjToggleNotifications)' ).val( 0 ).change();"
									.		"} else if ( $( this ).val() == 1 ) {"
									.			"$( '.gjNotificationsForm' ).find( 'select:not(.gjToggleNotifications)' ).val( 1 ).change();"
									.		"}"
									.		"$( this ).val( -1 );"
									.	"});";

		$_CB_framework->outputCbJQuery( $js );

		$pageTitle					=	CBTxt::T( 'Notifications' );

		$_CB_framework->setPageTitle( $pageTitle );

		cbValidator::loadValidation();
		initToolTip();

		$isModerator				=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$isOwner					=	( $user->get( 'id' ) == $group->get( 'user_id' ) );
		$status						=	CBGroupJive::getGroupStatus( $user, $group );
		$return						=	null;

		$integrations				=	$_PLUGINS->trigger( 'gj_onBeforeDisplayNotifications', array( &$return, &$row, &$input, $group, $user ) );

		$return						.=	'<div class="gjNotifications gjNotifications' . $group->get( 'id', 0, GetterInterface::INT ) . '">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'notifications', 'func' => 'save', 'id' => $group->get( 'id', 0, GetterInterface::INT ) ) ) . '" method="post" enctype="multipart/form-data" name="gjNotificationsForm" id="gjNotificationsForm" class="cb_form gjNotificationsForm form-auto cbValidation">'
									.			( $pageTitle ? '<div class="gjNotificationsTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
									.			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<div class="cb_field col-sm-12 text-right">'
									.					$input['toggle']
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="params__user_join" class="col-sm-9 control-label">' . CBTxt::T( 'Join of new user' ) . '</label>'
									.				'<div class="cb_field col-sm-3 text-right">'
									.					$input['user_join']
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="params__user_leave" class="col-sm-9 control-label">' . CBTxt::T( 'Leave of existing user' ) . '</label>'
									.				'<div class="cb_field col-sm-3 text-right">'
									.					$input['user_leave']
									.				'</div>'
									.			'</div>';

		if ( ( $isModerator || $isOwner || ( $status >= 2 ) ) && ( $group->get( 'type' ) == 2 ) ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="params__user_approve" class="col-sm-9 control-label">' . CBTxt::T( 'New user requires approval' ) . '</label>'
									.				'<div class="cb_field col-sm-3 text-right">'
									.					$input['user_approve']
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="params__user_cancel" class="col-sm-9 control-label">' . CBTxt::T( 'New user join request cancelled' ) . '</label>'
									.				'<div class="cb_field col-sm-3 text-right">'
									.					$input['user_cancel']
									.				'</div>'
									.			'</div>';
		}

		if ( ( $plugin->params->get( 'groups_invites_display', 1 ) || ( $group->get( 'type' ) == 3 ) ) && ( $group->params()->get( 'invites', 1 ) || ( $isModerator || $isOwner || ( $status >= 3 ) ) ) ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="params__invite_accept" class="col-sm-9 control-label">' . CBTxt::T( 'My group invite requests accepted' ) . '</label>'
									.				'<div class="cb_field col-sm-3 text-right">'
									.					$input['invite_accept']
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="params__invite_reject" class="col-sm-9 control-label">' . CBTxt::T( 'My group invite requests rejected' ) . '</label>'
									.				'<div class="cb_field col-sm-3 text-right">'
									.					$input['invite_reject']
									.				'</div>'
									.			'</div>';
		}

		if ( is_array( $integrations ) && $integrations ) {
			$return					.=			implode( '', $integrations );
		}

		$return						.=			'<div class="form-group cb_form_line clearfix">'
									.				'<div class="col-sm-12 text-right">'
									.					'<input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="gjButton gjButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) ) . '\'; })" />'
									.					' <input type="submit" value="' . htmlspecialchars( CBTxt::T( 'Update Notifications' ) ) . '" class="gjButton gjButtonSubmit btn btn-primary" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.				'</div>'
									.			'</div>'
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayNotifications', array( &$return, $row, $input, $group, $user ) );

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}