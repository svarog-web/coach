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
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJivePhoto\Table\PhotoTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjivePhotoEdit
{

	/**
	 * render frontend photo edit
	 *
	 * @param PhotoTable      $row
	 * @param array           $input
	 * @param GroupTable      $group
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showPhotoEdit( $row, $input, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$pageTitle					=	( $row->get( 'id' ) ? CBTxt::T( 'Edit Photo' ) : CBTxt::T( 'New Photo' ) );

		$_CB_framework->setPageTitle( $pageTitle );

		cbValidator::loadValidation();
		initToolTip();

		$isModerator				=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$canModerate				=	( CBGroupJive::getGroupStatus( $user, $group ) >= 2 );
		$return						=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayPhotoEdit', array( &$return, &$row, &$input, $group, $user ) );

		$return						.=	'<div class="gjPhotoEdit">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'save', 'id' => $row->get( 'id', null, GetterInterface::INT ) ) ) . '" method="post" enctype="multipart/form-data" name="gjPhotoEditForm" id="gjPhotoEditForm" class="cb_form gjPhotoEditForm form-auto cbValidation">'
									.			( $pageTitle ? '<div class="gjPhotoEditTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null );

		if ( $isModerator || $canModerate || ( $row->get( 'id' ) && ( $row->get( 'published' ) != -1 ) ) || ( $group->params()->get( 'photo', 1 ) != 2 ) ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="published" class="col-sm-3 control-label">' . CBTxt::T( 'Published' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['published']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Select publish state of this photo. Unpublished photos will not be visible to the public.' ) )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="title" class="col-sm-3 control-label">' . CBTxt::T( 'Title' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['title']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally input a photo title to display instead of filename.' ) )
									.				'</div>'
									.			'</div>';

		if ( $row->get( 'id' ) && $row->exists() ) {
			$return					.=			'<div class="cbft_delimiter form-group cb_form_line clearfix">'
									.				'<div class="cb_field col-sm-offset-3 col-sm-9">'
									.					'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'download', 'id' => (int) $row->get( 'id' ) ), 'raw', 0, true ) . '" target="_blank">'
									.						$row->name()
									.					'</a>'
									.					' (' . $row->size() . ')'
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_delimiter form-group cb_form_line clearfix">'
									.				'<div class="cb_field col-sm-offset-3 col-sm-9">'
									.					'<img src="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'preview', 'id' => (int) $row->get( 'id' ) ), 'raw', 0, true ) . '" class="gjPhotoPreview img-responsive" />'
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_file cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="image" class="col-sm-3 control-label">' . CBTxt::T( 'Photo' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['image']
									.					getFieldIcons( null, ( ! $row->get( 'id' ) ? 1 : 0 ), null, CBTxt::T( 'Select the photo to upload.' ) )
									.					( $input['image_limits'] ? '<div class="help-block">' . implode( ' ', $input['image_limits'] ) . '</div>' : null )
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
									.				'<label for="description" class="col-sm-3 control-label">' . CBTxt::T( 'Description' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['description']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally input a photo description.' ) )
									.				'</div>'
									.			'</div>';

		if ( $isModerator ) {
			$return					.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="user_id" class="col-sm-3 control-label">' . CBTxt::T( 'Owner' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['user_id']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Input the photo owner id. Photo owner determines the creator of the photo specified as User ID.' ) )
									.				'</div>'
									.			'</div>';
		}

		if ( ( ! $isModerator ) && $plugin->params->get( 'groups_photo_captcha', 0 ) ) {
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

		$return						.=			'<div class="form-group cb_form_line clearfix cbtwolines">'
									.				'<div class="col-sm-offset-3 col-sm-9">'
									.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id' ) ? CBTxt::T( 'Update Photo' ) : CBTxt::T( 'Upload Photo' ) ) ) . '" class="gjButton gjButtonSubmit btn btn-primary" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="gjButton gjButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) ) . '\'; })" />'
									.				'</div>'
									.			'</div>'
									.			( ! $row->get( 'id' ) ? '<input type="hidden" id="group" name="group" value="' . (int) $group->get( 'id' ) . '" />' : null )
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayPhotoEdit', array( &$return, $row, $input, $group, $user ) );

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}