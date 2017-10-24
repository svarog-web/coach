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
use CB\Plugin\GroupJiveVideo\Table\VideoTable;
use CB\Plugin\GroupJive\CBGroupJive;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveVideo
{

	/**
	 * render frontend videos
	 *
	 * @param VideoTable[]    $rows
	 * @param cbPageNav       $pageNav
	 * @param bool            $searching
	 * @param array           $input
	 * @param array           $counters
	 * @param GroupTable      $group
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showVideos( $rows, $pageNav, $searching, $input, &$counters, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$counters[]						=	'<span class="gjGroupVideoIcon fa-before fa-film"> ' . CBTxt::T( 'GROUP_VIDEOS_COUNT', '%%COUNT%% Video|%%COUNT%% Videos', array( '%%COUNT%%' => (int) $pageNav->total ) ) . '</span>';

		initToolTip();

		$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$isOwner						=	( $user->get( 'id' ) == $group->get( 'user_id' ) );
		$userStatus						=	CBGroupJive::getGroupStatus( $user, $group );
		$canCreate						=	CBGroupJive::canCreateGroupContent( $user, $group, 'video' );
		$canSearch						=	( $plugin->params->get( 'groups_video_search', 1 ) && ( $searching || $pageNav->total ) );
		$return							=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayVideos', array( &$return, &$rows, $group, $user ) );

		$return							.=	'<div class="gjGroupVideo">'
										.		'<form action="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) . '" method="post" name="gjGroupVideoForm" id="gjGroupVideoForm" class="gjGroupVideoForm">';

		if ( $canCreate || $canSearch ) {
			$return						.=			'<div class="gjHeader gjGroupVideoHeader row">';

			if ( $canCreate ) {
				$return					.=				'<div class="' . ( ! $canSearch ? 'col-sm-12' : 'col-sm-8' ) . ' text-left">'
										.					'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'video', 'func' => 'new', 'group' => (int) $group->get( 'id' ) ) ) . '\';" class="gjButton gjButtonNewVideo btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New Video' ) . '</button>'
										.				'</div>';
			}

			if ( $canSearch ) {
				$return					.=				'<div class="' . ( ! $canCreate ? 'col-sm-offset-8 ' : null ) . 'col-sm-4 text-right">'
										.					'<div class="input-group">'
										.						'<span class="input-group-addon"><span class="fa fa-search"></span></span>'
										.						$input['search']
										.					'</div>'
										.				'</div>';
			}

			$return						.=			'</div>';
		}

		$return							.=			'<div class="gjGroupVideoRows">';

		if ( $rows ) foreach ( $rows as $row ) {
			$rowOwner					=	( $user->get( 'id' ) == $row->get( 'user_id' ) );
			$rowCounters				=	array();
			$content					=	null;
			$menu						=	array();

			$_PLUGINS->trigger( 'gj_onDisplayVideo', array( &$row, &$rowCounters, &$content, &$menu, $group, $user ) );

			$return						.=				'<div class="gjGroupVideoRow gjCanvasBox cbCanvasBox cbCanvasBoxLg img-thumbnail">'
										.					'<div class="gjCanvasBoxTop cbCanvasBoxTop bg-muted">';

			if ( $row->mimeType() == 'video/youtube' ) {
				if ( preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:be/))([A-Za-z0-9_-]+)%', $row->get( 'url' ), $matches ) ) {
					$return				.=						'<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' . htmlspecialchars( $matches[1] ) . '" frameborder="0" allowfullscreen class="gjVideoPlayer"></iframe>';
				}
			} else {
				$return					.=						'<video width="100%" height="100%" style="width: 100%; height: 100%;" src="' . htmlspecialchars( $row->get( 'url' ) ) . '" type="' . htmlspecialchars( $row->mimeType() ) . '" controls="controls" preload="auto" class="gjVideoPlayer"></video>';
			}

			$return						.=					'</div>'
										.					'<div class="gjCanvasBoxBottom cbCanvasBoxBottom bg-default">'
										.						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-nowrap text-overflow">';

			if ( ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) && ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'video', 1 ) == 2 ) ) ) {
				$return					.=							'<span class="gjGroupPendingIcon fa fa-lg fa-clock-o text-warning" title="' . htmlspecialchars( CBTxt::T( 'Awaiting Approval' ) ) . '"></span> ';
			}

			$return						.=							'<a href="' . htmlspecialchars( $row->get( 'url' ) ) . '" target="_blank" rel="nofollow">' . htmlspecialchars( ( $row->get( 'title' ) ? $row->get( 'title' ) : $row->name() ) ) . '</a>'
										.							( $row->get( 'caption' ) ? '<div class="gjCanvasBoxDescription">' . cbTooltip( 1, $row->get( 'caption' ), $row->name(), 400, null, '<span class="fa fa-info-circle text-muted"></span>' ) . '</div>' : null )
										.						'</div>'
										.						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-muted small row">'
										.							'<div class="gjGroupVideoPublisher gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">' . CBuser::getInstance( (int) $row->get( 'user_id' ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</div>'
										.							'<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6 text-right">'
										.								'<span title="' . htmlspecialchars( $row->get( 'date' ) ) . '">'
										.									cbFormatDate( $row->get( 'date' ), true, false, CBTxt::T( 'GROUP_VIDEO_DATE_FORMAT', 'M j, Y' ) )
										.								'</span>'
										.							'</div>'
										.							( $rowCounters ? '<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">' . implode( '</div><div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">', $rowCounters ) . '</div>' : null )
										.						'</div>'
										.						( $content ? '<div class="gjCanvasBoxRow cbCanvasBoxRow">' . $content . '</div>' : null );

			if ( ( $isModerator || $isOwner || ( $userStatus >= 2 ) ) && ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'video', 1 ) == 2 ) ) {
				$return					.=						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-right">'
										.							'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'video', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '\';" class="gjButton gjButtonApprove btn btn-xs btn-success">' . CBTxt::T( 'Approve' ) . '</button>'
										.						'</div>';
			}

			$return						.=					'</div>';

			if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) || $menu ) {
				$menuItems				=	'<ul class="gjCanvasBoxMenuItems cbCanvasBoxMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

				if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
					$menuItems			.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'video', 'func' => 'edit', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

					if ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'video', 1 ) == 2 ) ) {
						if ( $isModerator || $isOwner || ( $userStatus >= 2 ) ) {
							$menuItems	.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'video', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
						}
					} elseif ( $row->get( 'published' ) == 1 ) {
						$menuItems		.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this Video?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'video', 'func' => 'unpublish', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
					} else {
						$menuItems		.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'video', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
					}
				}

				if ( $menu ) {
					$menuItems			.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem">' . implode( '</li><li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem">', $menu ) . '</li>';
				}

				if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
					$menuItems			.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this Video?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'video', 'func' => 'delete', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
				}

				$menuItems				.=	'</ul>';

				$menuAttr				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="gjCanvasBoxMenu cbCanvasBoxMenu btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

				$return					.=					'<div class="gjCanvasBoxButtons cbCanvasBoxButtons">'
										.						'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
										.					'</div>';
			}

			$return						.=				'</div>';
		} else {
			if ( $searching ) {
				$return					.=				CBTxt::T( 'No group video search results found.' );
			} else {
				$return					.=				CBTxt::T( 'This group currently has no videos.' );
			}
		}

		$return							.=			'</div>';

		if ( $plugin->params->get( 'groups_video_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return						.=			'<div class="gjGroupVideoPaging text-center">'
										.				$pageNav->getListLinks()
										.			'</div>';
		}

		$return							.=			$pageNav->getLimitBox( false )
										.		'</form>'
										.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayVideos', array( &$return, $rows, $group, $user ) );

		return $return;
	}
}