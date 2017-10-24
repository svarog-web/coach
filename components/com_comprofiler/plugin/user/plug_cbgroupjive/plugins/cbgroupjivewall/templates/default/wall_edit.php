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
use CB\Plugin\GroupJiveWall\Table\WallTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveWallEdit
{

	/**
	 * render frontend wall edit
	 *
	 * @param WallTable              $row
	 * @param array                  $input
	 * @param GroupTable             $group
	 * @param UserTable              $user
	 * @param CBplug_cbgroupjivewall $plugin
	 */
	static function showWallEdit( $row, $input, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$pageTitle					=	( $row->get( 'id' ) ? CBTxt::T( 'Edit Post' ) : CBTxt::T( 'New Post' ) );

		$_CB_framework->setPageTitle( $pageTitle );

		cbValidator::loadValidation();
		initToolTip();

		$isModerator				=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$canModerate				=	( CBGroupJive::getGroupStatus( $user, $group ) >= 2 );
		$return						=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayWallEdit', array( &$return, &$row, &$input, $group, $user ) );

		$return						.=	'<div class="gjWallEdit">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'wall', 'func' => 'save', 'id' => $row->get( 'id', null, GetterInterface::INT ) ) ) . '" method="post" enctype="multipart/form-data" name="gjWallEditForm" id="gjWallEditForm" class="cb_form gjWallEditForm form-auto cbValidation">'
									.			( $pageTitle ? '<div class="gjWallEditTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null );

		if ( $isModerator || $canModerate || ( $row->get( 'id' ) && ( $row->get( 'published' ) != -1 ) ) || ( $group->params()->get( 'wall', 1 ) != 2 ) ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="published" class="col-sm-3 control-label">' . CBTxt::T( 'Published' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['published']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Select publish state of this post. Unpublished posts will not be visible to the public.' ) )
									.				'</div>'
									.			'</div>';
		}

		if ( $row->reply()->get( 'id' ) ) {
			$return					.=			'<div class="cbft_delimiter form-group cb_form_line clearfix">'
									.				'<label class="col-sm-3 control-label">' . CBTxt::T( 'Reply' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$row->reply()->post()
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
									.				'<label for="post" class="col-sm-3 control-label">' . CBTxt::T( 'Post' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['post']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Input the post to share.' ) )
									.				'</div>'
									.			'</div>';

		if ( $isModerator ) {
			$return					.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="user_id" class="col-sm-3 control-label">' . CBTxt::T( 'Owner' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['user_id']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Input the post owner id. Post owner determines the creator of the post specified as User ID.' ) )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="form-group cb_form_line clearfix cbtwolines">'
									.				'<div class="col-sm-offset-3 col-sm-9">'
									.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id' ) ? CBTxt::T( 'Update Post' ) : CBTxt::T( 'Post' ) ) ) . '" class="gjButton gjButtonSubmit btn btn-primary" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="gjButton gjButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) ) . '\'; })" />'
									.				'</div>'
									.			'</div>'
									.			( ! $row->get( 'id' ) ? '<input type="hidden" id="group" name="group" value="' . (int) $group->get( 'id' ) . '" />' : null )
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayWallEdit', array( &$return, $row, $input, $group, $user ) );

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}