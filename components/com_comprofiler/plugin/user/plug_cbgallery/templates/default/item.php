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
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Gallery\Table\ItemTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryItem
{

	/**
	 * @param ItemTable        $row
	 * @param UserTable        $viewer
	 * @param Gallery          $gallery
	 * @param CBplug_cbgallery $plugin
	 */
	static public function showItem( $row, $viewer, $gallery, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ! $row->exists() ) {
			return;
		}

		initToolTip();

		$cbUser						=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );
		$canModerate				=	CBGallery::canModerate( $gallery );
		$owner						=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user_id', 0, GetterInterface::INT ) );
		$folder						=	null;

		$title						=	( $row->get( 'title', null, GetterInterface::STRING ) ? $row->get( 'title', null, GetterInterface::STRING ) : $row->name() );
		$date						=	null;
		$menu						=	array();

		if ( $row->domain() ) {
			$showPath				=	htmlspecialchars( $row->path() );
			$downloadPath			=	null;
		} else {
			$showPath				=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'show', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true );
			$downloadPath			=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'download', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true );
		}

		$previous					=	$plugin->input( 'previous', null, GetterInterface::STRING );
		$next						=	$plugin->input( 'next', null, GetterInterface::STRING );

		$integrations				=	$_PLUGINS->trigger( 'gallery_onDisplayModal', array( &$row, &$title, &$date, &$menu, $gallery ) );

		$type						=	$row->get( 'type', null, GetterInterface::STRING );
		$canDownload				=	( ( $type != 'files' ) && $gallery->get( $type . '_download', false, GetterInterface::BOOLEAN ) );

		$return						=	'<div class="galleryModalContainer bg-inverse text-inverse">'
									.		'<div class="galleryModalDisplay col-md-9">';

		if ( $previous ) {
			$return					.=			'<div class="galleryModalScrollLeft">'
									.				'<table>'
									.					'<tr>'
									.						'<td>'
									.							'<span class="galleryModalScrollLeftIcon fa fa-chevron-left" data-cbgallery-previous="' . htmlspecialchars( $previous ) . '"></span>'
									.						'</td>'
									.					'</tr>'
									.				'</table>'
									.			'</div>';
		}

		$return						.=			'<div class="galleryModalItem text-center">';

		switch( $type ) {
			case 'photos':
				switch ( $row->params()->get( 'rotate', 0, GetterInterface::INT ) ) {
					case 90:
						$rotate		=	' galleryRotate90';
						break;
					case 180:
						$rotate		=	' galleryRotate180';
						break;
					case 270:
						$rotate		=	' galleryRotate270';
						break;
					case 360:
					default:
						$rotate		=	null;
						break;
				}

				$return				.=				'<div class="galleryMediaPhoto">'
									.					'<img alt="' . htmlspecialchars( $title ) . '" src="' . $showPath . '" class="cbImgPict cbFullPict img-thumbnail' . $rotate . '" />'
									.				'</div>';
				break;
			case 'files':
				$return				.=				'<table class="galleryMediaFile">'
									.					'<tbody>'
									.						'<tr>'
									.							'<td>'
									.								'<table class="galleryMediaFileTable table table-bordered bg-default text-default">'
									.									'<tbody>'
									.										'<tr>'
									.											'<th style="width: 125px;">' . CBTxt::T( 'File' ) . '</th>'
									.											'<td><a href="' . $showPath . '" target="_blank">' . $row->name() . '</a></td>'
									.										'</tr>'
									.										'<tr>'
									.											'<th style="width: 125px;">' . CBTxt::T( 'Extension' ) . '</th>'
									.											'<td>' . $row->extension() . '</td>'
									.										'</tr>'
									.										'<tr>'
									.											'<th style="width: 125px;">' . CBTxt::T( 'Size' ) . '</th>'
									.											'<td>' . $row->size() . '</td>'
									.										'</tr>'
									.										'<tr>'
									.											'<th style="width: 125px;">' . CBTxt::T( 'Modified' ) . '</th>'
									.											'<td>' . cbFormatDate( $row->modified() ) . '</td>'
									.										'</tr>'
									.										'<tr>'
									.											'<td colspan="2" class="text-right"><a href="' . $downloadPath . '" target="_blank" class="btn btn-sm btn-primary">' . CBTxt::T( 'Download' ) . '</a></td>'
									.										'</tr>'
									.									'</tbody>'
									.								'</table>'
									.							'</td>'
									.						'</tr>'
									.					'</tbody>'
									.				'</table>';
				break;
			case 'videos':
				$return				.=				'<div class="galleryMediaVideo">';

				if ( $row->mimeType() == 'video/x-youtube' ) {
					if ( preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:be/))([A-Za-z0-9_-]+)%', $showPath, $matches ) ) {
						$return		.=					'<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' . htmlspecialchars( $matches[1] ) . '" frameborder="0" allowfullscreen class="galleryVideoPlayer"></iframe>';
					}
				} else {
					$return			.=					'<video width="100%" height="100%" style="width: 100%; height: 100%;" src="' . $showPath . '" type="' . htmlspecialchars( $row->mimeType() ) . '" controls="controls" preload="auto" class="galleryVideoPlayer"></video>';
				}

				$return				.=				'</div>';
				break;
			case 'music':
				$return				.=				'<div class="galleryMediaMusic">'
									.					'<audio style="width: 100%;" src="' . $showPath . '" type="' . htmlspecialchars( $row->mimeType() ) . '" controls="controls" preload="auto" class="galleryAudioPlayer"></audio>'
									.				'</div>';
				break;
		}

		$return						.=			'</div>';

		if ( $next ) {
			$return					.=			'<div class="galleryModalScrollRight">'
									.				'<table>'
									.					'<tr>'
									.						'<td>'
									.							'<span class="galleryModalScrollRightIcon fa fa-chevron-right" data-cbgallery-next="' . htmlspecialchars( $next ) . '"></span>'
									.						'</td>'
									.					'</tr>'
									.				'</table>'
									.			'</div>';
		}

		$return						.=			'<div class="galleryModalClose cbTooltipClose"><span class="fa fa-close fa-lg"></span></div>'
									.		'</div>'
									.		'<div class="galleryModalInfo col-md-9">'
									.			'<div>'
									.				'<div class="galleryModalInfoTitle">'
									.					'<strong>'
									.						( count( $gallery->types() ) > 1 ? '<div class="galleryModalType">' . cbTooltip( null, CBGallery::translateType( $row ), null, 'auto', null, '<span class="fa ' . CBGallery::getTypeIcon( $row ) . '"></span>', null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' ) . '</div>' : null )
									.						$title;

		if ( $row->get( 'folder', 0, GetterInterface::INT ) ) {
			$folder					=	$row->folder( $gallery );

			if ( $folder->get('id', 0, GetterInterface::INT ) && ( ( $folder->get( 'published', 1, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) == $folder->get( 'user_id', 0, GetterInterface::INT ) ) || $canModerate ) ) ) {
				$inFolder			=	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'show', 'id' => $folder->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) . '">'
									.		( $folder->get( 'title', null, GetterInterface::STRING ) ? $folder->get( 'title', null, GetterInterface::STRING ) : cbFormatDate( $folder->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'GALLERY_SHORT_DATE_FORMAT', 'M j, Y' ) ) )
									.	'</a>';

				$return				.=						'<div class="text-small">' . CBTxt::T( 'IN_FOLDER', 'in [folder]', array( '[folder]' => $inFolder ) ) . '</div>';
			}
		}

		$return						.=					'</strong>'
									.				'</div>';

		if ( $canModerate || $owner || $canDownload || $menu ) {
			$menuItems				=	'<ul class="galleryMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

			if ( $menu ) {
				$menuItems			.=		'<li class="galleryMenuItem">' . implode( '</li><li class="galleryMenuItem">', $menu ) . '</li>';
			}

			if ( $canDownload ) {
				$menuItems			.=		'<li class="galleryMenuItem"><a href="' . $downloadPath . '" target="_blank"><span class="fa fa-download"></span> ' . CBTxt::T( 'Download' ) . '</a></li>';
			}

			if ( $canModerate || $owner ) {
				if ( $menu || $canDownload ) {
					$menuItems		.=		'<li class="galleryMenuItem divider"></li>';
				}

				$canThumbnail		=	( $folder && ( $folder->get( 'thumbnail', 0, GetterInterface::INT ) != $row->get( 'id', 0, GetterInterface::INT ) ) );

				if ( $canThumbnail ) {
					$menuItems		.=		'<li class="galleryMenuItem galleryModalAction"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'cover', 'id' => $folder->get( 'id', 0, GetterInterface::INT ), 'item' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true ) . '"><span class="fa fa-exchange"></span> ' . CBTxt::T( 'Make Album Cover' ) . '</a></li>';
				}

				if ( $type == 'photos' ) {
					$menuItems		.=		'<li class="galleryMenuItem galleryModalAction"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'rotate', 'direction' => 'left', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true ) . '"><span class="fa fa-rotate-left"></span> ' . CBTxt::T( 'Rotate Left' ) . '</a></li>'
									.		'<li class="galleryMenuItem galleryModalAction"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'rotate', 'direction' => 'right', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true ) . '"><span class="fa fa-rotate-right"></span> ' . CBTxt::T( 'Rotate Right' ) . '</a></li>';
				}

				if ( $canThumbnail || ( $type == 'photos' ) ) {
					$menuItems		.=		'<li class="galleryMenuItem divider"></li>';
				}

				$menuItems			.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

				if ( ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) && $gallery->get( $type . '_create_approval', false, GetterInterface::BOOLEAN ) ) {
					if ( $canModerate ) {
						$menuItems	.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
					}
				} elseif ( $row->get( 'published', 1, GetterInterface::INT ) > 0 ) {
					// CBTxt::T( 'ARE_YOU_SURE_UNPUBLISH_TYPE', 'Are you sure you want to unpublish this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) )
					$menuItems		.=		'<li class="galleryMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'ARE_YOU_SURE_UNPUBLISH_TYPE ARE_YOU_SURE_UNPUBLISH_' . strtoupper( $type ), 'Are you sure you want to unpublish this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'unpublish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
				} else {
					$menuItems		.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
				}

				// CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE', 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) )
				$menuItems			.=		'<li class="galleryMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE ARE_YOU_SURE_DELETE_' . strtoupper( $type ), 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
			}

			$menuItems				.=	'</ul>';

			$menuAttr				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="galleryButton galleryButtonMenu btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

			$return					.=				'<div class="galleryModalInfoMenu">'
									.					'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
									.				'</div>';
		}

		$return						.=			'</div>'
									.		'</div>'
									.		'<div class="galleryModalDetails col-md-3 bg-default text-default">'
									.			'<div class="galleryModalDetailsHeader media">'
									.				'<div class="galleryModalDetailsHeaderAvatar media-left">'
									.					$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
									.				'</div>'
									.				'<div class="galleryModalDetailsHeaderBody media-body">'
									.					'<div class="galleryModalDetailsHeaderBodyTop">'
									.						'<strong>' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong>'
									.					'</div>'
									.					'<div class="galleryModalDetailsHeaderBodyBottom text-muted small">'
									.						cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, 'timeago' )
									.						( $date ? ' ' . $date : null )
									.					'</div>'
									.				'</div>'
									.			'</div>'
									.			( $row->get( 'description', null, GetterInterface::STRING ) ? '<div class="galleryModalDetailsBody text-small">' . $row->get( 'description', null, GetterInterface::STRING ) . '</div>' : null )
									.			( $integrations ? '<div class="galleryModalDetailsFooter border-default bg-muted">' . implode( '', $integrations ) . '</div>' : null )
									.		'</div>'
									.		CBGallery::reloadHeaders()
									.	'</div>';

		echo $return;
	}
}