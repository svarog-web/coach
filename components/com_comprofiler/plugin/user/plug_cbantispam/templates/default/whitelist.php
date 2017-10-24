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

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbantispamWhitelist
{

	/**
	 * @param cbantispamWhitelistTable $row
	 * @param array                    $input
	 * @param string                   $type
	 * @param int|string               $tab
	 * @param UserTable                $user
	 * @param cbPluginHandler          $plugin
	 */
	static public function showWhitelist( $row, $input, $type, $tab, $user, $plugin )
	{
		global $_CB_framework;

		cbValidator::loadValidation();

		$name			=	CBuser::getInstance( (int) $user->get( 'id' ), false )->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );
		$pageTitle		=	CBTxt::T( 'WHITELIST_NAME', 'Whitelist [name]', array( '[name]' => $name ) );

		$_CB_framework->setPageTitle( $pageTitle );
		$_CB_framework->appendPathWay( htmlspecialchars( CBTxt::T( 'Whitelists' ) ), $_CB_framework->userProfileUrl( (int) $user->get( 'id' ), true, $tab ) );
		$_CB_framework->appendPathWay( htmlspecialchars( $pageTitle ), $_CB_framework->pluginClassUrl( $plugin->element, true, ( $row->get( 'id' ) ? array( 'action' => 'whitelist', 'func' => ( $type ? $type : 'edit' ), 'id' => (int) $row->get( 'id' ), 'usr' => (int) $user->get( 'id' ) ) : array( 'action' => 'whitelist', 'func' => ( $type ? $type : 'new' ), 'usr' => (int) $user->get( 'id' ) ) ) ) );

		$return			=	'<div class="whitelistEdit">'
						.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'whitelist', 'func' => 'save', 'id' => (int) $row->get( 'id' ), 'usr' => (int) $user->get( 'id' ) ) ) . '" method="post" enctype="multipart/form-data" name="whitelistForm" id="whitelistForm" class="cb_form whitelistForm form-auto cbValidation">'
						.			( $pageTitle ? '<div class="whitelistTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
						.			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
						.				'<label for="type" class="col-sm-3 control-label">' . CBTxt::T( 'Type' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['type']
						.					getFieldIcons( 1, 1, null, CBTxt::T( 'Select the whitelist type. Type determines what value should be supplied.' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
						.				'<label for="value" class="col-sm-3 control-label">' . CBTxt::T( 'Value' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['value']
						.					getFieldIcons( 1, 1, null, CBTxt::T( 'Input whitelist value in relation to the type. User type use the users user_id (e.g. 42). IP Address type use a full valid IP Address (e.g. 192.168.0.1). Email type use a fill valid email address (e.g. invalid@cb.invalid). Email Domain type use a full email address domain after @ (e.g. example.com).' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
						.				'<label for="reason" class="col-sm-3 control-label">' . CBTxt::T( 'Reason' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['reason']
						.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input whitelist reason. Note this is for administrative purposes only.' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="form-group cb_form_line clearfix">'
						.				'<div class="col-sm-offset-3 col-sm-9">'
						.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id' ) ? CBTxt::T( 'Update Whitelist' ) : CBTxt::T( 'Create Whitelist' ) ) ) . '" class="whitelistButton whitelistButtonSubmit btn btn-primary"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />&nbsp;'
						.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="whitelistButton whitelistButtonCancel btn btn-default" onclick="if ( confirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ) ) { location.href = \'' . $_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, $tab ) . '\'; }" />'
						.				'</div>'
						.			'</div>'
						.			cbGetSpoofInputTag( 'plugin' )
						.		'</form>'
						.	'</div>';

		echo $return;
	}
}
?>