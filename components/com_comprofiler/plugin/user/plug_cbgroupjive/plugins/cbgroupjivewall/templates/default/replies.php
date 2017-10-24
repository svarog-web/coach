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
use CB\Plugin\GroupJiveWall\Table\WallTable;
use CB\Plugin\GroupJive\CBGroupJive;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveWallReplies
{

	/**
	 * render frontend replies
	 *
	 * @param WallTable       $reply
	 * @param WallTable[]     $rows
	 * @param cbPageNav       $pageNav
	 * @param GroupTable      $group
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showReplies( $reply, $rows, $pageNav, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$isModerator						=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$messageLimit						=	( ! $isModerator ? (int) $plugin->params->get( 'groups_wall_character_limit', 0 ) : 0 );

		$js									=	"$( '.gjGroupWallNewReply:not(.gjGroupWallNewReplyOpen)' ).on( 'click', function() {"
											.		"$( this ).find( '.gjGroupWallLimit,.gjGroupWallNewReplyFooter' ).removeClass( 'hidden' );"
											.		"$( this ).addClass( 'gjGroupWallNewReplyOpen' );"
											.		"$( this ).find( 'textarea' ).autosize({"
											.			"append: '',"
											.			"resizeDelay: 0,"
											.			"placeholder: false"
											.		"});"
											.	"});"
											.	"$( '.gjGroupWallReplies .cbMoreLess' ).cbmoreless();";

		if ( $messageLimit ) {
			$js								.=	"$( '.gjGroupWallNewReply textarea' ).on( 'keyup input change', function() {"
											.		"var inputLength = $( this ).val().length;"
											.		"if ( inputLength > " . (int) $messageLimit . " ) {"
											.			"$( this ).val( $( this ).val().substr( 0, " . (int) $messageLimit . " ) );"
											.		"} else {"
											.			"$( this ).siblings( '.gjGroupWallLimit' ).find( '.gjGroupWallLimitCurrent' ).html( $( this ).val().length );"
											.		"}"
											.	"});";
		}

		$_CB_framework->outputCbJQuery( $js, array( 'cbmoreless', 'autosize' ) );

		$isOwner							=	( $user->get( 'id' ) == $group->get( 'user_id' ) );
		$userStatus							=	CBGroupJive::getGroupStatus( $user, $group );
		$canCreate							=	CBGroupJive::canCreateGroupContent( $user, $group, 'wall' );
		$return								=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayWall', array( &$return, &$rows, $group, $user ) );

		$return								.=	'<div class="gjGroupWallReplies">'
											.		'<div class="gjGroupWallRows">';

		if ( $rows ) foreach ( $rows as $row ) {
			$rowOwner						=	( $user->get( 'id' ) == $row->get( 'user_id' ) );
			$menu							=	array();

			$integrations					=	$_PLUGINS->trigger( 'gj_onDisplayWall', array( &$row, &$menu, $group, $user ) );

			$cbUser							=	CBuser::getInstance( (int) $row->get( 'user_id' ), false );

			$return							.=			'<div class="gjGroupWallRow">'
											.				'<div class="gjGroupWallHeader media clearfix">'
											.					'<div class="gjGroupWallAvatar media-left">'
											.						$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
											.					'</div>'
											.					'<div class="gjGroupWallDetails media-body">'
											.						'<div class="cbMoreLess">'
											.							'<div class="cbMoreLessContent">'
											.								'<strong>' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong> '
											.								$row->post()
											.							'</div>'
											.							'<div class="cbMoreLessOpen fade-edge hidden">'
											.								'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
											.							'</div>'
											.						'</div>'
											.						'<div class="gjGroupWallDate text-muted small">'
											.							cbFormatDate( $row->get( 'date' ), true, 'timeago' );

			if ( ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) && ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'wall', 1 ) == 2 ) ) ) {
				$return						.=							' <span class="gjGroupPendingIcon fa fa-clock-o text-warning" title="' . htmlspecialchars( CBTxt::T( 'Awaiting Approval' ) ) . '"></span>';
			}

			$return							.=						'</div>'
											.					'</div>'
											.				'</div>'
											.				( is_array( $integrations ) && $integrations ? '<div class="gjGroupWallFooter panel-footer">' . implode( '', $integrations ) . '</div>' : null );

			if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) || $menu ) {
				$menuItems					=	'<ul class="gjWallMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

				if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
					$menuItems				.=		'<li class="gjWallMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'wall', 'func' => 'edit', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

					if ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'wall', 1 ) == 2 ) ) {
						if ( $isModerator || $isOwner || ( $userStatus >= 2 ) ) {
							$menuItems		.=		'<li class="gjWallMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'wall', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
						}
					} elseif ( $row->get( 'published' ) == 1 ) {
						$menuItems			.=		'<li class="gjWallMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this Post?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'wall', 'func' => 'unpublish', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
					} else {
						$menuItems			.=		'<li class="gjWallMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'wall', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
					}
				}

				if ( $menu ) {
					$menuItems				.=		'<li class="gjWallMenuItem">' . implode( '</li><li class="gjWallMenuItem">', $menu ) . '</li>';
				}

				if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
					$menuItems				.=		'<li class="gjWallMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this Post?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'wall', 'func' => 'delete', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
				}

				$menuItems					.=	'</ul>';

				$menuAttr					=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

				$return						.=				'<div class="gjWallMenu btn-group">'
											.					'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
											.				'</div>';
			}

			$return							.=			'</div>';
		}

		if ( $plugin->params->get( 'groups_wall_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return							.=			'<div class="gjGroupWallRow gjGroupWallPaging text-center">'
											.				'<form action="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) . '" method="post" name="gjGroupWallFormPaging" id="gjGroupWallFormPaging" class="gjGroupWallForm">'
											.					$pageNav->getListLinks()
											.					$pageNav->getLimitBox( false )
											.				'</form>'
											.			'</div>';
		}

		if ( $canCreate ) {
			$return							.=			'<div class="gjGroupWallRow gjGroupWallNewReply">'
											.				'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'wall', 'func' => 'save' ) ) . '" method="post" name="gjGroupWallFormNewReply' . (int) $reply->get( 'id' ) . '" id="gjGroupWallFormNewReply' . (int) $reply->get( 'id' ) . '" class="gjGroupWallForm cbValidation">';

			if ( $messageLimit ) {
				$return						.=					'<div class="gjGroupWallLimit small hidden">'
											.						'<div class="gjGroupWallLimitCurrent">0</div>'
											.						' / <div class="gjGroupWallLimitMax">' . (int) $messageLimit . '</div>'
											.					'</div>';
			}

			$return							.=					'<textarea name="post" rows="1" class="form-control required" placeholder="' . htmlspecialchars( CBTxt::T( 'Write a reply...' ) ) . '"' . ( $messageLimit ? ' maxlength="' . (int) $messageLimit . '"' : null ) . '></textarea>'
											.					'<div class="gjGroupWallNewReplyFooter text-right hidden">'
											.						'<button type="submit" class="gjButton gjButtonSubmit btn btn-primary btn-xs" ' . cbValidator::getSubmitBtnHtmlAttributes() . '>' . CBTxt::T( 'Reply' ) . '</button>'
											.					'</div>'
											.					'<input type="hidden" name="group" value="' . (int) $group->get( 'id' ) . '" />'
											.					'<input type="hidden" name="reply" value="' . (int) $reply->get( 'id' ) . '" />'
											.					cbGetSpoofInputTag( 'plugin' )
											.				'</form>'
											.			'</div>';
		}

		$return								.=		'</div>'
											.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayWall', array( &$return, $rows, $group, $user ) );

		return $return;
	}
}