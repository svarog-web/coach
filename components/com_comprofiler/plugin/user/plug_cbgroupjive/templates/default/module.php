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
use CB\Database\Table\PluginTable;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveModule
{

	/**
	 * render frontend categories module
	 *
	 * @param CategoryTable[]           $rows
	 * @param UserTable                 $user
	 * @param \Joomla\Registry\Registry $params
	 * @param PluginTable               $plugin
	 * @return string
	 */
	static function showCategories( $rows, $user, $params, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		initToolTip();

		$return			=	null;

		if ( $rows ) foreach ( $rows as $row ) {
			$cssClass	=	$row->get( 'css', null, GetterInterface::STRING );
			$counters	=	array();
			$content	=	null;

			$_PLUGINS->trigger( 'gj_onDisplayCategory', array( &$row, &$counters, &$content, $user ) );

			$return		.=	'<div class="gjModuleCategory gjModuleCategory' . $row->get( 'id', 0, GetterInterface::INT ) . ' gjCanvasBox cbCanvasBox img-thumbnail' . ( $cssClass ? ' ' . htmlspecialchars( $cssClass ) : null ) . '">'
						.		'<div class="gjCanvasBoxTop cbCanvasBoxTop bg-muted">'
						.			'<div class="gjCanvasBoxBackground cbCanvasBoxBackground">'
						.				$row->canvas( true, true )
						.			'</div>'
						.			'<div class="gjCanvasBoxPhoto cbCanvasBoxPhoto cbCanvasBoxPhotoLeft">'
						.				$row->logo( true, true, true )
						.			'</div>'
						.		'</div>'
						.		'<div class="gjCanvasBoxBottom cbCanvasBoxBottom bg-default">'
						.			'<div class="gjCanvasBoxRow cbCanvasBoxRow text-nowrap text-overflow">'
						.				'<strong><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) ) . '">' . CBTxt::T( $row->get( 'name' ) ) . '</a></strong>'
						.				( $row->get( 'description' ) ? '<div class="gjCanvasBoxDescription">' . cbTooltip( 1, CBTxt::T( $row->get( 'description' ) ), CBTxt::T( $row->get( 'name' ) ), 400, null, '<span class="fa fa-info-circle text-muted"></span>' ) . '</div>' : null )
						.			'</div>'
						.			'<div class="gjCanvasBoxRow cbCanvasBoxRow text-muted small row">'
						.				'<div class="gjCanvasBoxCounter text-nowrap text-overflow ' . ( $counters ? 'col-sm-6' : 'col-sm-12' ) . '"><span class="gjCategoryGroupsIcon fa-before fa-users"> ' . CBTxt::T( 'GROUPS_COUNT', '%%COUNT%% Group|%%COUNT%% Groups', array( '%%COUNT%%' => (int) $row->get( '_groups', 0 ) ) ) . '</span></div>'
						.				( $counters ? '<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">' . implode( '</div><div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">', $counters ) . '</div>' : null )
						.			'</div>'
						.			( $content ? '<div class="gjCanvasBoxRow cbCanvasBoxRow">' . $content . '</div>' : null )
						.		'</div>'
						.	'</div>';
		}

		return $return;
	}

	/**
	 * render frontend groups module
	 *
	 * @param GroupTable[]              $rows
	 * @param UserTable                 $user
	 * @param \Joomla\Registry\Registry $params
	 * @param PluginTable               $plugin
	 * @return string
	 */
	static function showGroups( $rows, $user, $params, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		initToolTip();

		$isModerator			=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$return					=	null;

		if ( $rows ) foreach ( $rows as $row ) {
			$rowOwner			=	( $user->get( 'id' ) == $row->get( 'user_id' ) );
			$userStatus			=	CBGroupJive::getGroupStatus( $user, $row );
			$cssClass			=	$row->get( 'css', null, GetterInterface::STRING );

			$counters			=	array();
			$content			=	null;
			$menu				=	array();

			$_PLUGINS->trigger( 'gj_onDisplayGroup', array( &$row, &$counters, &$content, &$menu, 'module', $user ) );

			$return				.=	'<div class="gjModuleGroup gjModuleGroup' . $row->get( 'id', 0, GetterInterface::INT ) . ' gjCanvasBox cbCanvasBox img-thumbnail' . ( $cssClass ? ' ' . htmlspecialchars( $cssClass ) : null ) . '">'
								.		'<div class="gjCanvasBoxTop cbCanvasBoxTop bg-muted">'
								.			'<div class="gjCanvasBoxBackground cbCanvasBoxBackground">'
								.				$row->canvas( true, true )
								.			'</div>'
								.			'<div class="gjCanvasBoxPhoto cbCanvasBoxPhoto cbCanvasBoxPhotoLeft">'
								.				$row->logo( true, true, true )
								.			'</div>'
								.		'</div>'
								.		'<div class="gjCanvasBoxBottom cbCanvasBoxBottom bg-default">'
								.			'<div class="gjCanvasBoxRow cbCanvasBoxRow text-nowrap text-overflow">'
								.				'<strong><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) ) . '">' . htmlspecialchars( CBTxt::T( $row->get( 'name' ) ) ) . '</a></strong>'
								.				( $row->get( 'description' ) ? '<div class="gjCanvasBoxDescription">' . cbTooltip( 1, CBTxt::T( $row->get( 'description' ) ), CBTxt::T( $row->get( 'name' ) ), 400, null, '<span class="fa fa-info-circle text-muted"></span>' ) . '</div>' : null )
								.			'</div>';

			if ( $row->get( 'category' ) ) {
				$return			.=			'<div class="gjCanvasBoxRow cbCanvasBoxRow text-nowrap text-overflow small">'
								.				'<strong><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $row->get( 'category' ) ) ) . '">' . htmlspecialchars( CBTxt::T( $row->get( '_category_name' ) ) ) . '</a></strong>'
								.			'</div>';
			}

			$return				.=			'<div class="gjCanvasBoxRow cbCanvasBoxRow text-muted small row">'
								.				'<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6"><span class="gjGroupTypeIcon fa-before fa-globe"> ' . $row->type() . '</span></div>'
								.				'<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6"><span class="gjGroupUsersIcon fa-before fa-user"> ' . CBTxt::T( 'GROUP_USERS_COUNT', '%%COUNT%% User|%%COUNT%% Users', array( '%%COUNT%%' => (int) $row->get( '_users', 0 ) ) ) . '</span></div>'
								.				( $counters ? '<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">' . implode( '</div><div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">', $counters ) . '</div>' : null )
								.			'</div>'
								.			( $content ? '<div class="gjCanvasBoxRow cbCanvasBoxRow">' . $content . '</div>' : null );

			if ( $isModerator && ( $row->get( 'published' ) == -1 ) && $plugin->params->get( 'groups_create_approval', 0 ) ) {
				$return			.=			'<div class="gjCanvasBoxRow cbCanvasBoxRow text-right">'
								.				'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'publish', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) . '\';" class="gjButton gjButtonApprove btn btn-xs btn-success">' . CBTxt::T( 'Approve' ) . '</button>'
								.			'</div>';
			} elseif ( ! $rowOwner ) {
				if ( $userStatus === null ) {
					$return		.=			'<div class="gjCanvasBoxRow cbCanvasBoxRow text-right">'
								.				( $row->get( '_invite_id' ) ? '<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to reject all invites to this Group?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'reject', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) ) . '\'; })" class="gjButton gjButtonReject btn btn-xs btn-danger">' . CBTxt::T( 'Reject' ) . '</button> ' : null )
								.				'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'join', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) . '\';" class="gjButton gjButtonJoin btn btn-xs btn-success">' . ( $row->get( '_invite_id' ) ? CBTxt::T( 'Accept Invite' ) : CBTxt::T( 'Join' ) ) . '</button>'
								.			'</div>';
				} elseif ( $userStatus === 0 ) {
					$return		.=			'<div class="gjCanvasBoxRow cbCanvasBoxRow text-right">'
								.				'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel your pending join request to this Group?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'cancel', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) ) . '\'; })" class="gjButton gjButtonCancel btn btn-xs btn-danger">' . CBTxt::T( 'Cancel' ) . '</button> '
								.				'<span class="gjButton gjButtonPending btn btn-xs btn-warning disabled">' . CBTxt::T( 'Pending Approval' ) . '</span>'
								.			'</div>';
				}
			}

			$return				.=		'</div>'
								.	'</div>';
		}

		return $return;
	}
}