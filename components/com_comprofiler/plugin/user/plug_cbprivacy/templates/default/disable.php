<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_privacyDisable
{

	/**
	 * @param int             $userId
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 */
	static public function showDisable( $userId, $user, $plugin )
	{
		global $_CB_framework;

		cbValidator::loadValidation();

		$profileUrl		=	$_CB_framework->userProfileUrl( $user->get( 'id' ) );
		$pageTitle		=	CBTxt::T( 'Disable My Account' );

		$_CB_framework->setPageTitle( $pageTitle );
		$_CB_framework->appendPathWay( htmlspecialchars( $pageTitle ), $profileUrl );

		initToolTip();

		$tooltip		=	cbTooltip( null, CBTxt::T( 'Optionally input a reason for disabling your account.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$return			=	'<div class="privacyDisableAccount">'
						.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'privacy', 'func' => 'disableuser', 'id' => (int) $userId ) ) . '" method="post" enctype="multipart/form-data" name="privacyForm" id="privacyForm" class="cb_form privacyForm form-auto cbValidation">'
						.			( $pageTitle ? '<div class="privacyTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
						.			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
						.				'<label for="reason" class="col-sm-3 control-label">' . CBTxt::T( 'Reason' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					'<textarea id="reason" name="reason" class="form-control" cols="40" rows="5"' . ( $tooltip ? ' ' . $tooltip : null ) . '></textarea>'
						.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input a reason for disabling your account.' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="form-group cb_form_line clearfix">'
						.				'<div class="col-sm-offset-3 col-sm-9">'
						.					'<input type="submit" value="' . htmlspecialchars( CBTxt::T( 'Disable Account' ) ) . '" class="privacyButton privacyButtonSubmit btn btn-primary"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />&nbsp;'
						.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="privacyButton privacyButtonCancel btn btn-default" onclick="location.href = \'' . $profileUrl . '\';" />'
						.				'</div>'
						.			'</div>'
						.			cbGetSpoofInputTag( 'plugin' )
						.		'</form>'
						.	'</div>';

		echo $return;
	}
}
?>