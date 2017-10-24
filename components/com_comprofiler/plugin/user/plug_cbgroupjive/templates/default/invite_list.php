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
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveInviteList
{

	/**
	 * render frontend invite list
	 *
	 * @param string             $to
	 * @param UserTable[]        $users
	 * @param InviteTable        $invite
	 * @param GroupTable         $group
	 * @param UserTable          $user
	 * @param CBplug_cbgroupjive $plugin
	 */
	static function showInviteList( $to, $users, $invite, $group, $user, $plugin )
	{
		global $_CB_framework;

		$js								=	"$( '.gjButtonInvite' ).on( 'click', function() {"
										.		"var from = $( this ).closest( 'form' );"
										.		"from.find( '#selected' ).val( $( this ).data( 'user-id' ) );"
										.		"from.find( '.gjButtonInvite' ).addClass( 'disabled' ).prop( 'disabled', true );"
										.		"from.submit();"
										.	"});";

		$_CB_framework->outputCbJQuery( $js );

		$_CB_framework->enqueueMessage( CBTxt::T( 'Multiple matching users found. Please select the user to invite. If you do not see the user you are looking for please be more specific.' ) );

		$pageTitle					=	CBTxt::T( 'INVITE_LIST_TO', 'Invite: [to]', array( '[to]' => $to ) );

		$_CB_framework->setPageTitle( $pageTitle );

		$return						=	'<div class="gjInviteList">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'invites', 'func' => 'save', 'id' => $invite->get( 'id', null, GetterInterface::INT ) ) ) . '" method="post" enctype="multipart/form-data" name="gjInviteListForm" id="gjInviteListForm" class="cb_form gjInviteListForm form-auto">'
									.			( $pageTitle ? '<div class="gjInviteListTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
									.			'<div class="gjInviteListRows">';

		foreach ( $users as $userId ) {
			$cbUser					=	CBuser::getInstance( (int) $userId, false );

			$return					.=				'<div class="gjInviteListUser gjCanvasBox cbCanvasBox cbCanvasBoxSm img-thumbnail">'
									.					'<div class="gjCanvasBoxTop cbCanvasBoxTop bg-muted">'
									.						'<div class="gjCanvasBoxBackground cbCanvasBoxBackground">'
									.							$cbUser->getField( 'canvas', null, 'html', 'none', 'profile', 0, true )
									.						'</div>'
									.						'<div class="gjCanvasBoxPhoto cbCanvasBoxPhoto cbCanvasBoxPhotoLeft">'
									.							$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
									.						'</div>'
									.					'</div>'
									.					'<div class="gjCanvasBoxBottom cbCanvasBoxBottom bg-default">'
									.						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-nowrap text-overflow">'
									.							$cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) )
									.							' ' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) )
									.						'</div>'
									.						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-right">'
									.							'<button type="button" class="gjButton gjButtonInvite btn btn-xs btn-success" data-user-id="' . (int) $userId . '">' . CBTxt::T( 'Invite' ) . '</button>'
									.						'</div>'
									.					'</div>'
									.				'</div>';
		}

		$return						.=			'</div>'
									.			'<div class="form-group cb_form_line clearfix">'
									.				'<input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="gjButton gjButtonCancel btn btn-default" onclick="window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) ) . '\';" />'
									.			'</div>'
									.			'<input type="hidden" id="selected" name="selected" value="0" />';

		if ( ! $invite->get( 'id' ) ) {
			$return					.=			'<input type="hidden" id="to" name="to" value="' . htmlspecialchars( $to ) . '" />'
									.			'<input type="hidden" id="group" name="group" value="' . (int) $group->get( 'id' ) . '" />'
									.			'<input type="hidden" id="message" name="message" value="' . htmlspecialchars( $invite->get( 'message' ) ) . '" />';
		}

		$return						.=			'<input type="hidden" id="token" name="token" value="' . htmlspecialchars( md5( $invite->get( 'user_id' ) . $to . $invite->get( 'group' ) . $invite->get( 'message' ) . $_CB_framework->getCfg( 'secret' ) ) ) . '" />'
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.	'</div>';

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}