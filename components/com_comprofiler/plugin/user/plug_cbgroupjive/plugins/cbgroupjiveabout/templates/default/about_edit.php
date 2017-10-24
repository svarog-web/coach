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
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveAboutEdit
{

	/**
	 * render frontend about edit
	 *
	 * @param GroupTable      $row
	 * @param array           $input
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 */
	static function showAboutEdit( $row, $input, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$pageTitle					=	CBTxt::T( 'About' );

		$_CB_framework->setPageTitle( $pageTitle );

		cbValidator::loadValidation();
		initToolTip();

		$return						=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayAboutEdit', array( &$return, &$row, &$input, $user ) );

		$return						.=	'<div class="gjAboutEdit">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'about', 'func' => 'save', 'id' => $row->get( 'id', null, GetterInterface::INT ) ) ) . '" method="post" enctype="multipart/form-data" name="gjAboutEditForm" id="gjAboutEditForm" class="cb_form gjAboutEditForm form-auto cbValidation">'
									.			( $pageTitle ? '<div class="gjAboutEditTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
									.			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix cbtwolines">'
									.				'<div class="cb_field col-sm-12">'
									.					$input['about']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally input a detailed description about this group.' ) )
									.				'</div>'
									.			'</div>';

		if ( ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) && $plugin->params->get( 'groups_about_captcha', 0 ) ) {
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
									.				'<div class="col-sm-12">'
									.					'<input type="submit" value="' . htmlspecialchars( CBTxt::T( 'Update About' ) ) . '" class="gjButton gjButtonSubmit btn btn-primary" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="gjButton gjButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) ) . '\'; })" />'
									.				'</div>'
									.			'</div>'
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayAboutEdit', array( &$return, $row, $input, $user ) );

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}