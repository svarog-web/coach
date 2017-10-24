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

class HTML_groupjiveCategory
{

	/**
	 * render frontend category
	 *
	 * @param CategoryTable      $category
	 * @param GroupTable[]       $rows
	 * @param cbPageNav          $pageNav
	 * @param bool               $searching
	 * @param array              $input
	 * @param UserTable          $user
	 * @param CBplug_cbgroupjive $plugin
	 */
	static function showCategory( $category, $rows, $pageNav, $searching, $input, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$pageTitle						=	( $category->get( 'id' ) ? CBTxt::T( $category->get( 'name' ) ) : CBTxt::T( 'Uncategorized' ) );

		$_CB_framework->setPageTitle( $pageTitle );

		initToolTip();

		$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$canCreateGroup					=	CBGroupJive::canCreateGroup( $user, $category );
		$cssClass						=	$category->get( 'css', null, GetterInterface::STRING );
		$canSearch						=	( $plugin->params->get( 'categories_groups_search', 1 ) && ( $searching || $pageNav->total ) );
		$counters						=	array();
		$return							=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayCategory', array( &$return, &$rows, &$category, &$counters, $user ) );

		$return							.=	'<div class="gjCategory gjCategory' . $category->get( 'id', 0, GetterInterface::INT ) . ( $cssClass ? ' ' . htmlspecialchars( $cssClass ) : null ) . '">'
										.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'categories', 'func' => 'show', 'id' => $category->get( 'id', 0, GetterInterface::INT ) ) ) . '" method="post" name="gjCategoryForm" id="gjCategoryForm" class="gjCategoryForm">'
										.			'<div class="gjCanvas cbCanvasLayout border-default">'
										.				'<div class="gjCanvasTop cbCanvasLayoutTop">'
										.					'<div class="gjCanvasBackground cbCanvasLayoutBackground bg-muted">'
										.						$category->canvas()
										.					'</div>'
										.					'<div class="gjCanvasPhoto cbCanvasLayoutPhoto">'
										.						$category->logo()
										.					'</div>'
										.				'</div>'
										.				'<div class="gjCanvasBottom cbCanvasLayoutBottom border-default">'
										.					'<div class="gjCanvasTitle cbCanvasLayoutTitle text-primary">'
										.						'<strong>' . $pageTitle . '</strong>'
										.						( $category->get( 'description' ) ? ' ' . cbTooltip( 1, CBTxt::T( $category->get( 'description' ) ), CBTxt::T( $category->get( 'name' ) ), 400, null, '<span class="fa fa-info-circle text-muted"></span>', null, 'class="gjCanvasDescription small"' ) : null )
										.					'</div>'
										.					'<div class="gjCanvasCounters cbCanvasLayoutCounters text-muted small">'
										.						'<span class="gjCanvasCounter cbCanvasLayoutCounter"><span class="gjCategoryGroupsIcon fa-before fa-users"> ' . CBTxt::T( 'GROUPS_COUNT', '%%COUNT%% Group|%%COUNT%% Groups', array( '%%COUNT%%' => (int) $pageNav->total ) ) . '</span></span>'
										.						( $counters ? ' <span class="gjCanvasCounter cbCanvasLayoutCounter">' . implode( '</span> <span class="gjCanvasCounter cbCanvasLayoutCounter">', $counters ) . '</span>' : null )
										.					'</div>'
										.				'</div>'
										.			'</div>'
										.			'<div class="gjCanvasMain cbCanvasLayoutMain">';

		if ( $canCreateGroup || $canSearch ) {
			$return						.=				'<div class="gjHeader gjCategoryHeader row">';

			if ( $canCreateGroup ) {
				$return					.=					'<div class="' . ( ! $canSearch ? 'col-sm-12' : 'col-sm-8' ) . ' text-left">'
										.						'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'groups', 'func' => 'new', 'category' => (int) $category->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) . '\';" class="gjButton gjButtonNewGroup btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New Group' ) . '</button>'
										.					'</div>';
			}

			if ( $canSearch ) {
				$return					.=					'<div class="' . ( ! $canCreateGroup ? 'col-sm-offset-8 ' : null ) . 'col-sm-4 text-right">'
										.						'<div class="input-group">'
										.							'<span class="input-group-addon"><span class="fa fa-search"></span></span>'
										.							$input['search']
										.						'</div>'
										.					'</div>';
			}

			$return						.=				'</div>';
		}

		$return							.=				'<div class="gjCategoryRows">';

		if ( $rows ) foreach ( $rows as $row ) {
			$rowOwner					=	( $user->get( 'id' ) == $row->get( 'user_id' ) );
			$userStatus					=	CBGroupJive::getGroupStatus( $user, $row );
			$cssClass					=	$row->get( 'css', null, GetterInterface::STRING );

			$counters					=	array();
			$content					=	null;
			$menu						=	array();

			$_PLUGINS->trigger( 'gj_onDisplayGroup', array( &$row, &$counters, &$content, &$menu, 'category', $user ) );

			$return						.=					'<div class="gjCategoryGroup gjCategoryGroup' . $row->get( 'id', 0, GetterInterface::INT ) . ' gjCanvasBox cbCanvasBox img-thumbnail' . ( $cssClass ? ' ' . htmlspecialchars( $cssClass ) : null ) . '">'
										.						'<div class="gjCanvasBoxTop cbCanvasBoxTop bg-muted">'
										.							'<div class="gjCanvasBoxBackground cbCanvasBoxBackground">'
										.								$row->canvas( true, true )
										.							'</div>'
										.							'<div class="gjCanvasBoxPhoto cbCanvasBoxPhoto cbCanvasBoxPhotoLeft">'
										.								$row->logo( true, true, true )
										.							'</div>'
										.						'</div>'
										.						'<div class="gjCanvasBoxBottom cbCanvasBoxBottom bg-default">'
										.							'<div class="gjCanvasBoxRow cbCanvasBoxRow text-nowrap text-overflow">';


			if ( ( $isModerator || $rowOwner ) && ( ( $row->get( 'published' ) == -1 ) && $plugin->params->get( 'groups_create_approval', 0 ) ) ) {
				$return					.=								'<span class="gjGroupPendingIcon fa fa-clock-o text-warning" title="' . htmlspecialchars( CBTxt::T( 'Awaiting Approval' ) ) . '"></span> ';
			}

			$return						.=								'<strong><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) ) . '">' . htmlspecialchars( CBTxt::T( $row->get( 'name' ) ) ) . '</a></strong>'
										.								( $row->get( 'description' ) ? '<div class="gjCanvasBoxDescription">' . cbTooltip( 1, CBTxt::T( $row->get( 'description' ) ), CBTxt::T( $row->get( 'name' ) ), 400, null, '<span class="fa fa-info-circle text-muted"></span>' ) . '</div>' : null )
										.							'</div>'
										.							'<div class="gjCanvasBoxRow cbCanvasBoxRow text-muted small row">'
										.								'<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6"><span class="gjGroupTypeIcon fa-before fa-globe"> ' . $row->type() . '</span></div>'
										.								'<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6"><span class="gjGroupUsersIcon fa-before fa-user"> ' . CBTxt::T( 'GROUP_USERS_COUNT', '%%COUNT%% User|%%COUNT%% Users', array( '%%COUNT%%' => (int) $row->get( '_users', 0 ) ) ) . '</span></div>'
										.								( $counters ? '<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">' . implode( '</div><div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">', $counters ) . '</div>' : null )
										.							'</div>'
										.							( $content ? '<div class="gjCanvasBoxRow cbCanvasBoxRow">' . $content . '</div>' : null );

			if ( $isModerator && ( $row->get( 'published' ) == -1 ) && $plugin->params->get( 'groups_create_approval', 0 ) ) {
				$return					.=							'<div class="gjCanvasBoxRow cbCanvasBoxRow text-right">'
										.								'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'publish', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) . '\';" class="gjButton gjButtonApprove btn btn-xs btn-success">' . CBTxt::T( 'Approve' ) . '</button>'
										.							'</div>';
			} elseif ( ! $rowOwner ) {
				if ( $userStatus === null ) {
					$return				.=							'<div class="gjCanvasBoxRow cbCanvasBoxRow text-right">'
										.								( $row->get( '_invite_id' ) ? '<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to reject all invites to this Group?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'reject', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) ) . '\'; })" class="gjButton gjButtonReject btn btn-xs btn-danger">' . CBTxt::T( 'Reject' ) . '</button> ' : null )
										.								'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'join', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) . '\';" class="gjButton gjButtonJoin btn btn-xs btn-success">' . ( $row->get( '_invite_id' ) ? CBTxt::T( 'Accept Invite' ) : CBTxt::T( 'Join' ) ) . '</button>'
										.							'</div>';
				} elseif ( $userStatus === 0 ) {
					$return				.=							'<div class="gjCanvasBoxRow cbCanvasBoxRow text-right">'
										.								'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel your pending join request to this Group?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'cancel', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) ) . '\'; })" class="gjButton gjButtonCancel btn btn-xs btn-danger">' . CBTxt::T( 'Cancel' ) . '</button> '
										.								'<span class="gjButton gjButtonPending btn btn-xs btn-warning disabled">' . CBTxt::T( 'Pending Approval' ) . '</span>'
										.							'</div>';
				}
			}

			$return						.=						'</div>';

			if ( $isModerator || $rowOwner || $menu ) {
				$menuItems				=	'<ul class="gjCanvasBoxMenuItems cbCanvasBoxMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

				if ( $isModerator || $rowOwner ) {
					$menuItems			.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'edit', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

					if ( ( $row->get( 'published' ) == -1 ) && $plugin->params->get( 'groups_create_approval', 0 ) ) {
						if ( $isModerator ) {
							$menuItems	.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'publish', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
						}
					} elseif ( $row->get( 'published' ) == 1 ) {
						$menuItems		.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this Group?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'unpublish', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
					} else {
						$menuItems		.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'publish', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
					}
				}

				if ( $menu ) {
					$menuItems			.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem">' . implode( '</li><li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem">', $menu ) . '</li>';
				}

				if ( $isModerator || $rowOwner ) {
					$menuItems			.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this Group?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'delete', 'id' => (int) $row->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
				}

				$menuItems				.=	'</ul>';

				$menuAttr				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="gjCanvasBoxMenu cbCanvasBoxMenu btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

				$return					.=						'<div class="gjCanvasBoxButtons cbCanvasBoxButtons">'
										.							'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
										.						'</div>';
			}

			$return						.=					'</div>';
		} else {
			if ( $searching ) {
				$return					.=					CBTxt::T( 'No category group search results found.' );
			} else {
				$return					.=					CBTxt::T( 'There are no groups available in this category.' );
			}
		}

		if ( $plugin->params->get( 'categories_groups_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return						.=				'<div class="gjCategoryPaging text-center">'
										.					$pageNav->getListLinks()
										.				'</div>';
		}

		$return							.=				'</div>'
										.			'</div>'
										.			$pageNav->getLimitBox( false )
										.		'</form>'
										.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayCategory', array( &$return, $rows, $category, $user ) );

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}