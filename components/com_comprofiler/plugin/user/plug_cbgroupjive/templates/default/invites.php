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
use CB\Plugin\GroupJive\Table\InviteTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveInvites
{

	/**
	 * render frontend group invites
	 *
	 * @param InviteTable[]      $rows
	 * @param cbPageNav          $pageNav
	 * @param bool               $searching
	 * @param array              $input
	 * @param GroupTable         $group
	 * @param UserTable          $user
	 * @param CBplug_cbgroupjive $plugin
	 * @return string
	 */
	static function showInvites( $rows, $pageNav, $searching, $input, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		initToolTip();

		$canCreateInvite				=	CBGroupJive::canCreateGroupContent( $user, $group, 'invites' );
		$canSearch						=	( $plugin->params->get( 'groups_invites_search', 0 ) && ( $searching || $pageNav->total ) );
		$return							=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayInvites', array( &$return, &$rows, $group, $user ) );

		$return							.=	'<div class="gjGroupInvites">'
										.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'show', 'id' => $group->get( 'id', 0, GetterInterface::INT ) ) ) . '" method="post" name="gjGroupInvitesForm" id="gjGroupInvitesForm" class="gjGroupInvitesForm">';

		if ( $canCreateInvite || $canSearch ) {
			$return						.=			'<div class="gjHeader gjGroupInvitesHeader row">';

			if ( $canCreateInvite ) {
				$return					.=				'<div class="' . ( ! $canSearch ? 'col-sm-12' : 'col-sm-8' ) . ' text-left">'
										.					'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'invites', 'func' => 'new', 'group' => (int) $group->get( 'id' ) ) ) . '\';" class="gjButton gjButtonNewInvite btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New Invite' ) . '</button>'
										.				'</div>';
			}

			if ( $canSearch ) {
				$return					.=				'<div class="' . ( ! $canCreateInvite ? 'col-sm-offset-8 ' : null ) . 'col-sm-4 text-right">'
										.					'<div class="input-group">'
										.						'<span class="input-group-addon"><span class="fa fa-search"></span></span>'
										.						$input['search']
										.					'</div>'
										.				'</div>';
			}

			$return						.=			'</div>';
		}

		$return							.=			'<table class="gjGroupInvitesRows table table-hover table-responsive">'
										.				'<thead>'
										.					'<tr>'
										.						'<th class="text-left">' . CBTxt::T( 'To' ) . '</th>'
										.						'<th style="width: 25%;" class="text-left hidden-xs">' . CBTxt::T( 'Date' ) . '</th>'
										.						'<th style="width: 5%;" class="text-center">' . CBTxt::T( 'Status' ) . '</th>'
										.						'<th style="width: 1%;">&nbsp;</th>'
										.					'</tr>'
										.				'</thead>'
										.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$menu						=	array();

			$_PLUGINS->trigger( 'gj_onDisplayInvite', array( &$row, &$menu, $group, $user ) );

			if ( $row->get( 'user' ) ) {
				$toUser					=	CBuser::getInstance( (int) $row->get( 'user' ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
			} else {
				$toUser					=	'<a href="mailto:' . htmlspecialchars( $row->get( 'email' ) ) . '">' . $row->get( 'email' ) . '</a>';
			}

			$return						.=					'<tr>'
										.						'<td class="text-left">' . $toUser . '</td>'
										.						'<td style="width: 25%;" class="text-left hidden-xs">'
										.							( $row->invited() ? '<div class="text-info" title="' . htmlspecialchars( CBTxt::T( 'Invited' ) ) . '">' . cbFormatDate( $row->get( 'invited' ) ) . '</div>' : '<div class="text-warning">' . CBTxt::T( 'Please Resend' ) . '</div>' )
										.							( $row->accepted() ? '<div class="text-success" title="' . htmlspecialchars( CBTxt::T( 'Accepted' ) ) . '">' . cbFormatDate( $row->get( 'accepted' ) ) . '</div>' : null )
										.						'</td>'
										.						'<td style="width: 5%;" class="text-center hidden-xs">';

			if ( $row->accepted() ) {
				$return					.=							'<span class="gjInviteAcceptedIcon fa fa-check text-success" title="' . htmlspecialchars( CBTxt::T( 'Accepted' ) ) . '"></span>';
			} elseif ( $row->invited() ) {
				$return					.=							'<span class="gjInvitePendingIcon fa fa-clock-o text-warning" title="' . htmlspecialchars( CBTxt::T( 'Pending' ) ) . '"></span>';
			} else {
				$return					.=							'<span class="gjInviteResentIcon fa fa-warning text-danger" title="' . htmlspecialchars( CBTxt::T( 'Resend' ) ) . '"></span>';
			}

			$return						.=						'</td>';

			$menuItems					=	'<ul class="gjInviteMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

			if ( $row->canResend() ) {
				$menuItems				.=		'<li class="gjInviteMenuItems"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'invites', 'func' => 'send', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-share-square-o"></span> ' . CBTxt::T( 'Resend' ) . '</a></li>';
			}

			if ( ! $row->accepted() ) {
				$menuItems				.=		'<li class="gjInviteMenuItems"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'invites', 'func' => 'edit', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';
			}

			if ( $menu ) {
				$menuItems				.=		'<li class="gjInviteMenuItems">' . implode( '</li><li class="gjGroupMenuItem">', $menu ) . '</li>';
			}

			$menuItems					.=		'<li class="gjInviteMenuItems"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this Invite?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'invites', 'func' => 'delete', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>'
										.	'</ul>';

			$menuAttr					=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

			$return						.=						'<td style="width: 1%;" class="text-right">'
										.							'<div class="gjInviteMenu btn-group">'
										.								'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
										.							'</div>'
										.						'</td>'
										.					'</tr>';
		} else {
			$return						.=					'<tr>'
										.						'<td colspan="4" class="text-left">';

			if ( $searching ) {
				$return					.=							CBTxt::T( 'No group invite search results found.' );
			} else {
				$return					.=							CBTxt::T( 'You currently have no invites in this group.' );
			}

			$return						.=						'</td>'
										.					'</tr>';
		}

		$return							.=				'</tbody>';

		if ( $plugin->params->get( 'groups_invites_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return						.=				'<tfoot>'
										.					'<tr>'
										.						'<td colspan="4" class="gjGroupInvitesPaging text-center">'
										.							$pageNav->getListLinks()
										.						'</td>'
										.					'</tr>'
										.				'</tfoot>';
		}

		$return							.=			'</table>'
										.			$pageNav->getLimitBox( false )
										.		'</form>'
										.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayInvites', array( &$return, &$rows, $group, $user ) );

		return $return;
	}
}