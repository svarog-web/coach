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
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveGroupEdit
{

	/**
	 * render frontend group edit
	 *
	 * @param GroupTable         $row
	 * @param array              $input
	 * @param CategoryTable      $category
	 * @param UserTable          $user
	 * @param CBplug_cbgroupjive $plugin
	 */
	static function showGroupEdit( $row, $input, $category, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		cbValidator::loadValidation();
		initToolTip();

		$js							=	"$( '#canvas_method' ).on( 'change', function() {"
									.		"if ( $( this ).val() == 1 ) {"
									.			"$( '#gjCanvasUpload' ).removeClass( 'hidden' ).find( 'input' ).removeClass( 'cbValidationDisabled' );"
									.		"} else {"
									.			"$( '#gjCanvasUpload' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' ).val( '' );"
									.		"}"
									.	"}).change();"
									.	"$( '#logo_method' ).on( 'change', function() {"
									.		"if ( $( this ).val() == 1 ) {"
									.			"$( '#gjLogoUpload' ).removeClass( 'hidden' ).find( 'input' ).removeClass( 'cbValidationDisabled' );"
									.		"} else {"
									.			"$( '#gjLogoUpload' ).addClass( 'hidden' ).find( 'input' ).addClass( 'cbValidationDisabled' ).val( '' );"
									.		"}"
									.	"}).change();";

		$_CB_framework->outputCbJQuery( $js );

		$isModerator				=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$cssClass					=	$row->get( 'css', null, GetterInterface::STRING );
		$returnUrl					=	CBGroupJive::getReturn( true, true );
		$return						=	null;

		$integrations				=	$_PLUGINS->trigger( 'gj_onBeforeDisplayGroupEdit', array( &$return, &$row, &$input, $category, $user ) );

		if ( $row->get( 'id' ) ) {
			$pageTitle				=	CBTxt::T( 'Edit Group' );

			if ( ! $returnUrl ) {
				$returnUrl			=	$_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );
			}
		} else {
			$pageTitle				=	CBTxt::T( 'New Group' );

			if ( ! $returnUrl ) {
				$returnUrl			=	$_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $category->get( 'id' ) ) );
			}
		}

		$_CB_framework->setPageTitle( $pageTitle );

		$return						.=	'<div class="gjGroupEdit gjGroupEdit' . $row->get( 'id', 0, GetterInterface::INT ) . ( $cssClass ? ' ' . htmlspecialchars( $cssClass ) : null ) . '">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'save', 'id' => $row->get( 'id', null, GetterInterface::INT ), 'return' => CBGroupJive::getReturn( true ) ) ) . '" method="post" enctype="multipart/form-data" name="gjGroupEditForm" id="gjGroupEditForm" class="cb_form gjGroupEditForm form-auto cbValidation">'
									.			( $pageTitle ? '<div class="gjGroupEditTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null );

		if ( $isModerator || ( $row->get( 'id' ) && ( $row->get( 'published' ) != -1 ) ) || ( ! $plugin->params->get( 'groups_create_approval', 0 ) ) ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="published" class="col-sm-3 control-label">' . CBTxt::T( 'Published' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['published']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Select publish state of this group. Unpublished groups will not be visible to the public.' ) )
									.				'</div>'
									.			'</div>';
		}

		if ( $input['category'] ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="category" class="col-sm-3 control-label">' . CBTxt::T( 'Category' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['category']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Select the group category. This is the category a group will belong to and decide its navigation path.' ) )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="type" class="col-sm-3 control-label">' . CBTxt::T( 'Type' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['type']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Select the group type. Type determines the way your group is joined (e.g. Invite requires new users to be invited to join your group).' ) )
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="name" class="col-sm-3 control-label">' . CBTxt::T( 'Name' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['name']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Input the group name. This is the name that will distinguish this group from others. Suggested to input something unique and intuitive.' ) )
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
									.				'<label for="description" class="col-sm-3 control-label">' . CBTxt::T( 'Description' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['description']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally input the group description. The group description should be short and to the point; describing what your group is all about.' ) )
									.				'</div>'
									.			'</div>';

		if ( $row->get( 'canvas' ) ) {
			$return					.=			'<div class="cbft_delimiter form-group cb_form_line clearfix">'
									.				'<label for="canvas_method" class="col-sm-3 control-label">' . CBTxt::T( 'Canvas' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$row->canvas()
									.				'</div>'
									.			'</div>'
									.			'<div id="gjCanvasMethod" class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<div class="cb_field col-sm-offset-3 col-sm-9">'
									.					$input['canvas_method']
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div id="gjCanvasUpload" class="cbft_file cbtt_input form-group cb_form_line clearfix' . ( $row->get( 'canvas' ) ? ' hidden' : null ) . '">'
									.				( ! $row->get( 'canvas' ) ? '<label for="canvas" class="col-sm-3 control-label">' . CBTxt::T( 'Canvas' ) . '</label>' : null )
									.				'<div class="cb_field' . ( $row->get( 'canvas' ) ? ' col-sm-offset-3' : null ) . ' col-sm-9">'
									.					$input['canvas']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally select the group canvas. A canvas should represent the topic of your group; please be respectful and tasteful when selecting a canvas.' ) )
									.					'<div class="help-block">' . implode( ' ', $input['canvas_limits'] ) . '</div>'
									.				'</div>'
									.			'</div>';

		if ( $row->get( 'logo' ) ) {
			$return					.=			'<div class="cbft_delimiter form-group cb_form_line clearfix">'
									.				'<label for="logo_method" class="col-sm-3 control-label">' . CBTxt::T( 'Logo' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$row->logo()
									.				'</div>'
									.			'</div>'
									.			'<div id="gjLogoMethod" class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<div class="cb_field col-sm-offset-3 col-sm-9">'
									.					$input['logo_method']
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div id="gjLogoUpload" class="cbft_file cbtt_input form-group cb_form_line clearfix' . ( $row->get( 'logo' ) ? ' hidden' : null ) . '">'
									.				( ! $row->get( 'logo' ) ? '<label for="logo" class="col-sm-3 control-label">' . CBTxt::T( 'Logo' ) . '</label>' : null )
									.				'<div class="cb_field' . ( $row->get( 'logo' ) ? ' col-sm-offset-3' : null ) . ' col-sm-9">'
									.					$input['logo']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally select the group logo. A logo should represent the topic of your group; please be respectful and tasteful when selecting a logo.' ) )
									.					'<div class="help-block">' . implode( ' ', $input['logo_limits'] ) . '</div>'
									.				'</div>'
									.			'</div>';

		if ( ( $row->get( 'type' ) != 3 ) && ( $isModerator || $plugin->params->get( 'groups_invites_display', 1 ) ) ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="params__invites" class="col-sm-3 control-label">' . CBTxt::T( 'Invites' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['invites']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally enable or disable usage of group invites. Group invites allow group users to invite other users to join the group. Group owner and group administrators are exempt from this configuration and can always invite users. Note existing invites will still be accessible.' ) )
									.				'</div>'
									.			'</div>';
		}

		if ( is_array( $integrations ) && $integrations ) {
			$return					.=			implode( '', $integrations );
		}

		if ( $isModerator ) {
			$return					.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="user_id" class="col-sm-3 control-label">' . CBTxt::T( 'Owner' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['user_id']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Input the group owner id. Group owner determines the creator of the group specified as User ID.' ) )
									.				'</div>'
									.			'</div>';
		}

		if ( ( ! $isModerator ) && $plugin->params->get( 'groups_create_captcha', 0 ) ) {
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
									.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id' ) ? CBTxt::T( 'Update Group' ) : CBTxt::T( 'Create Group' ) ) ) . '" class="gjButton gjButtonSubmit btn btn-primary" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="gjButton gjButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( htmlspecialchars( $returnUrl ) ) . '\'; })" />'
									.				'</div>'
									.			'</div>'
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayGroupEdit', array( &$return, $row, $input, $category, $user ) );

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}