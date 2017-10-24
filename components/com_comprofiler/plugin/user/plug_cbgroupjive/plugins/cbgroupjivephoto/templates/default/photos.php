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
use CB\Plugin\GroupJivePhoto\Table\PhotoTable;
use CB\Plugin\GroupJive\CBGroupJive;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjivePhoto
{

	/**
	 * render frontend photos
	 *
	 * @param PhotoTable[]    $rows
	 * @param cbPageNav       $pageNav
	 * @param bool            $searching
	 * @param array           $input
	 * @param array           $counters
	 * @param GroupTable      $group
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showPhotos( $rows, $pageNav, $searching, $input, &$counters, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$js								=	"$( document ).on( 'click', '.gjGroupPhotoImageScrollLeftIcon', function( e ) {"
										.		"var previous = $( this ).data( 'previous-photo' );"
										.		"if ( previous ) {"
										.			"$( previous ).find( '.gjGroupPhotoItem' ).click();"
										.			"e.preventDefault();"
										.		"}"
										.	"});"
										.	"$( document ).on( 'click', '.gjGroupPhotoImageScrollRightIcon', function( e ) {"
										.		"var next = $( this ).data( 'next-photo' );"
										.		"if ( next ) {"
										.			"$( next ).find( '.gjGroupPhotoItem' ).click();"
										.			"e.preventDefault();"
										.		"}"
										.	"});"
										.	"$( '.gjGroupPhotoItem.cbTooltip,.gjGroupPhotoLogo.cbTooltip' ).on( 'cbtooltip.move', function( e, cbtooltip, event, api ) {"
										.		"if ( api.elements.tooltip ) {"
										.			"api.elements.content.find( '.gjGroupPhotoImage' ).css( 'line-height', api.elements.content.css( 'max-height' ) );"
										.		"}"
										.	"});";

		$_CB_framework->outputCbJQuery( $js );

		$counters[]						=	'<span class="gjGroupPhotoIcon fa-before fa-photo"> ' . CBTxt::T( 'GROUP_PHOTOS_COUNT', '%%COUNT%% Photo|%%COUNT%% Photos', array( '%%COUNT%%' => (int) $pageNav->total ) ) . '</span>';

		initToolTip();

		$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$isOwner						=	( $user->get( 'id' ) == $group->get( 'user_id' ) );
		$userStatus						=	CBGroupJive::getGroupStatus( $user, $group );
		$canCreate						=	CBGroupJive::canCreateGroupContent( $user, $group, 'photo' );
		$canSearch						=	( $plugin->params->get( 'groups_photo_search', 1 ) && ( $searching || $pageNav->total ) );
		$return							=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayPhotos', array( &$return, &$rows, $group, $user ) );

		$return							.=	'<div class="gjGroupPhoto">'
										.		'<form action="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) . '" method="post" name="gjGroupPhotoForm" id="gjGroupPhotoForm" class="gjGroupPhotoForm">';

		if ( $canCreate || $canSearch ) {
			$return						.=			'<div class="gjHeader gjGroupPhotoHeader row">';

			if ( $canCreate ) {
				$return					.=				'<div class="' . ( ! $canSearch ? 'col-sm-12' : 'col-sm-8' ) . ' text-left">'
										.					'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'photo', 'func' => 'new', 'group' => (int) $group->get( 'id' ) ) ) . '\';" class="gjButton gjButtonNewPhoto btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New Photo' ) . '</button>'
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

		$i								=	0;

		$return							.=			'<div class="gjGroupPhotoRows">';

		if ( $rows ) foreach ( $rows as $row ) {
			$rowOwner					=	( $user->get( 'id' ) == $row->get( 'user_id' ) );
			$rowCounters				=	array();
			$content					=	null;
			$menu						=	array();

			$_PLUGINS->trigger( 'gj_onDisplayPhoto', array( &$row, &$rowCounters, &$content, &$menu, $group, $user ) );

			$title						=	( $row->get( 'title' ) ? htmlspecialchars( $row->get( 'title' ) ) : $row->name() );
			$item						=	$title;
			$logo						=	null;

			if ( $row->exists() ) {
				$showPath				=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ), 'raw', 0, true );

				$image					=	'<div class="gjGroupPhotoImageContainer">';

				if ( $pageNav->total > 1 ) {
					$image				.=		'<div class="gjGroupPhotoImageScrollLeft">'
										.			'<table>'
										.				'<tr>'
										.					'<td>'
										.						'<span class="gjGroupPhotoImageScrollLeftIcon fa fa-chevron-left" data-previous-photo=".gjGroupPhotoRow' . ( $i == 0 ? ( count( $rows ) - 1 ) : ( $i - 1 ) ) . '"></span>'
										.					'</td>'
										.				'</tr>'
										.			'</table>'
										.		'</div>';
				}

				$image					.=		'<div class="gjGroupPhotoImage text-center">'
										.			'<img alt="' . htmlspecialchars( $title ) . '" src="' . htmlspecialchars( $showPath ) . '" class="cbImgPict cbFullPict img-thumbnail" />'
										.		'</div>'
										.		'<div class="gjGroupPhotoImageInfo">'
										.			'<div class="gjGroupPhotoImageInfoRow">'
										.				'<div class="gjGroupPhotoImageInfoTitle col-sm-8 text-left"><strong>' . $title . '</strong></div>'
										.				'<div class="gjGroupPhotoImageInfoOriginal col-sm-4 text-right">'
										.					'<a href="' . $showPath . '" target="_blank">'
										.						CBTxt::T( 'Original' )
										.					'</a>'
										.				'</div>'
										.			'</div>';

				if ( $row->get( 'description' ) ) {
					$image				.=			'<div class="gjGroupPhotoImageInfoRow">'
										.				'<div class="gjGroupPhotoImageInfoDescription col-sm-8 text-left">' . htmlspecialchars( $row->get( 'description' ) ) . '</div>'
										.				'<div class="gjGroupPhotoImageInfoDownload col-sm-4 text-right">'
										.				'</div>'
										.			'</div>';
				}

				$image					.=		'</div>';

				if ( $pageNav->total > 1 ) {
					$image				.=		'<div class="gjGroupPhotoImageScrollRight">'
										.			'<table>'
										.				'<tr>'
										.					'<td>'
										.						'<span class="gjGroupPhotoImageScrollRightIcon fa fa-chevron-right" data-next-photo=".gjGroupPhotoRow' . ( isset( $rows[$i+1] ) ? ( $i + 1 ) : 0 ) . '"></span>'
										.					'</td>'
										.				'</tr>'
										.			'</table>'
										.		'</div>';
				}

				$image					.=	'</div>';

				$item					=	cbTooltip( 1, $image, null, array( '90%', '90%' ), null, $item, 'javascript: void(0);', 'class="gjGroupPhotoItem" data-cbtooltip-modal="true" data-cbtooltip-classes="gjGroupPhotoImageModal"' );
				$logo					=	cbTooltip( 1, $image, null, array( '90%', '90%' ), null, '<img alt="' . htmlspecialchars( $title ) . '" src="' . htmlspecialchars( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'preview', 'id' => (int) $row->get( 'id' ) ), 'raw', 0, true ) ) . '" class="cbImgPict cbThumbPict img-thumbnail" />', 'javascript: void(0);', 'class="gjGroupPhotoLogo" data-cbtooltip-modal="true" data-cbtooltip-classes="gjGroupPhotoImageModal"' );
			}

			$return						.=				'<div class="gjGroupPhotoRow gjGroupPhotoRow' . $i . ' gjCanvasBox cbCanvasBox cbCanvasBoxSq cbCanvasBoxHover img-thumbnail">'
										.					'<div class="gjCanvasBoxTop cbCanvasBoxTop bg-muted">'
										.						'<div class="gjCanvasBoxPhoto cbCanvasBoxPhoto cbCanvasBoxPhotoCenter text-center">'
										.							$logo
										.						'</div>'
										.					'</div>'
										.					'<div class="gjCanvasBoxBottom cbCanvasBoxBottom bg-default">'
										.						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-nowrap text-overflow">';

			if ( ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) && ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'photo', 1 ) == 2 ) ) ) {
				$return					.=							'<span class="gjGroupPendingIcon fa fa-lg fa-clock-o text-warning" title="' . htmlspecialchars( CBTxt::T( 'Awaiting Approval' ) ) . '"></span> ';
			}

			$return						.=							$item
										.							( $row->get( 'description' ) ? '<div class="gjCanvasBoxDescription">' . cbTooltip( 1, $row->get( 'description' ), $row->name(), 400, null, '<span class="fa fa-info-circle text-muted"></span>' ) . '</div>' : null )
										.						'</div>'
										.						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-muted small row">'
										.							'<div class="gjGroupPhotoPublisher gjCanvasBoxCounter col-sm-6">' . CBuser::getInstance( (int) $row->get( 'user_id' ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</div>'
										.							'<div class="gjCanvasBoxCounter col-sm-6 text-right">'
										.								'<span title="' . htmlspecialchars( $row->get( 'date' ) ) . '">'
										.									cbFormatDate( $row->get( 'date' ), true, false, CBTxt::T( 'GROUP_PHOTO_DATE_FORMAT', 'M j, Y' ) )
										.								'</span>'
										.							'</div>'
										.							( $rowCounters ? '<div class="gjCanvasBoxCounter col-sm-6">' . implode( '</div><div class="gjCanvasBoxCounter col-sm-6">', $rowCounters ) . '</div>' : null )
										.						'</div>'
										.						( $content ? '<div class="gjCanvasBoxRow cbCanvasBoxRow">' . $content . '</div>' : null );

			if ( ( $isModerator || $isOwner || ( $userStatus >= 2 ) ) && ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'photo', 1 ) == 2 ) ) {
				$return					.=						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-right">'
										.							'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '\';" class="gjButton gjButtonApprove btn btn-xs btn-success">' . CBTxt::T( 'Approve' ) . '</button>'
										.						'</div>';
			}

			$return						.=					'</div>';

			if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) || $menu ) {
				$menuItems				=	'<ul class="gjCanvasBoxMenuItems cbCanvasBoxMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

				if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
					$menuItems			.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'edit', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

					if ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'photo', 1 ) == 2 ) ) {
						if ( $isModerator || $isOwner || ( $userStatus >= 2 ) ) {
							$menuItems	.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
						}
					} elseif ( $row->get( 'published' ) == 1 ) {
						$menuItems		.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this Photo?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'unpublish', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
					} else {
						$menuItems		.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
					}
				}

				if ( $menu ) {
					$menuItems			.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem">' . implode( '</li><li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem">', $menu ) . '</li>';
				}

				if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
					$menuItems			.=		'<li class="gjCanvasBoxMenuItem cbCanvasBoxMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this Photo?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'delete', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
				}

				$menuItems				.=	'</ul>';

				$menuAttr				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="gjCanvasBoxMenu cbCanvasBoxMenu btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

				$return					.=					'<div class="gjCanvasBoxButtons cbCanvasBoxButtons">'
										.						'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
										.					'</div>';
			}

			$return						.=				'</div>';

			$i++;
		} else {
			if ( $searching ) {
				$return					.=				CBTxt::T( 'No group photo search results found.' );
			} else {
				$return					.=				CBTxt::T( 'This group currently has no photos.' );
			}
		}

		$return							.=			'</div>';

		if ( $plugin->params->get( 'groups_photo_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return						.=			'<div class="gjGroupPhotoPaging text-center">'
										.				$pageNav->getListLinks()
										.			'</div>';
		}

		$return							.=			$pageNav->getLimitBox( false )
										.		'</form>'
										.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayPhotos', array( &$return, $rows, $group, $user ) );

		return $return;
	}
}