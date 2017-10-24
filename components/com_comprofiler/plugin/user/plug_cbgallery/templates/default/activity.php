<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\Table\ItemTable;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Gallery\Trigger\ActivityTrigger;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryActivity
{

	/**
	 * render frontend gallery activity
	 *
	 * @param ActivityTable   $row
	 * @param string          $title
	 * @param string          $message
	 * @param Activity        $stream
	 * @param ItemTable[]     $items
	 * @param Gallery         $gallery
	 * @param ActivityTrigger $plugin
	 * @return string
	 */
	static public function showActivity( $row, &$title, &$message, $stream, $items, $gallery, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ! $items ) {
			return null;
		}

		static $JS_LOADED				=	0;

		if ( ! $JS_LOADED++ ) {
			initToolTip();

			$_CB_framework->outputCbJQuery( "$( '.galleryItemName,.galleryItemEmbed' ).cbgallery();", 'cbgallery' );
		}

		$type							=	CBGallery::translateType( $row->get( 'subtype', null, GetterInterface::STRING ), false, true );

		if ( count( $items ) > 1 ) {
			$type						=	CBTxt::T( 'COUNT_TYPES', '%%COUNT%% [types]', array( '%%COUNT%%' => count( $items ), '[types]' => CBGallery::translateType( $row->get( 'subtype', null, GetterInterface::STRING ), true, true ) ) );
		}

		$title							=	null;
		$message						=	null;

		$typeModal						=	null;
		$folder							=	null;

		$return							=	'<div class="galleryActivity clearfix">';

		$items							=	array_values( $items );

		/** @var ItemTable[] $items */
		foreach ( $items as $i => $item ) {
			if ( ! $item->exists() ) {
				continue;
			}

			if ( count( $items ) > 1 ) {
				$previous				=	( $i == 0 ? ( count( $items ) - 1 ) : ( $i - 1 ) );

				if ( isset( $items[$previous] ) ) {
					$item->set( '_previous', '.galleryContainer' . htmlspecialchars( $gallery->id() ) . $items[$previous]->get( 'id', 0, GetterInterface::INT ) );
				}

				$next					=	( ( $i + 1 ) <= ( count( $items ) - 1 )  ? ( $i + 1 ) : 0 );

				if ( isset( $items[$next] ) ) {
					$item->set( '_next', '.galleryContainer' . htmlspecialchars( $gallery->id() ) . $items[$next]->get( 'id', 0, GetterInterface::INT ) );
				}
			}

			$displayPath				=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'display', 'id' => $item->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn() ), 'raw', 0, true );
			$data						=	array();

			if ( $item->get( '_previous', null, GetterInterface::STRING ) ) {
				$data['previous']		=	$item->get( '_previous', null, GetterInterface::STRING );
			}

			if ( $item->get( '_next', null, GetterInterface::STRING ) ) {
				$data['next']			=	$item->get( '_next', null, GetterInterface::STRING );
			}

			if ( ! $typeModal ) {
				$typeModal				=	cbTooltip( null, '', null, array( '90%', '90%' ), null, $type, 'javascript: void(0);', 'class="galleryItemName" data-cbtooltip-modal="true" data-cbtooltip-open-solo="document" data-cbtooltip-classes="galleryModal" data-cbgallery-url="' . $displayPath . '" data-cbgallery-request="' . htmlspecialchars( json_encode( $data ) ) . '"' );
			}

			if ( ( ! $folder ) && $item->get( 'folder', 0, GetterInterface::INT ) ) {
				$itemFolder				=	$item->folder( $gallery );

				if ( $itemFolder->get('id', 0, GetterInterface::INT ) && ( ( $itemFolder->get( 'published', 1, GetterInterface::INT ) ) || ( ( Application::MyUser()->getUserId() == $itemFolder->get( 'user_id', 0, GetterInterface::INT ) ) || CBGallery::canModerate( $gallery ) ) ) ) {
					$folder				=	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'show', 'id' => $itemFolder->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) . '">'
										.		( $itemFolder->get( 'title', null, GetterInterface::STRING ) ? $itemFolder->get( 'title', null, GetterInterface::STRING ) : cbFormatDate( $itemFolder->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'GALLERY_SHORT_DATE_FORMAT', 'M j, Y' ) ) )
										.	'</a>';
				}
			}

			if ( ( ! $message ) && ( count( $items ) == 1 ) ) {
				$message				=	( $item->get( 'description', null, GetterInterface::STRING ) ? $item->get( 'description', null, GetterInterface::STRING ) : null );
			}

			if ( $item->domain() ) {
				$showPath				=	htmlspecialchars( $item->path() );
				$downloadPath			=	null;
			} else {
				$showPath				=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'show', 'id' => $item->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true );
				$downloadPath			=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'download', 'id' => $item->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true );
			}

			switch ( $item->get( 'type', null, GetterInterface::STRING ) ) {
				case 'photos':
					$single				=	( count( $items ) == 1 );
					$width				=	$item->get( '_width', 0, GetterInterface::INT );

					if ( ! $width ) {
						$width			=	$gallery->get( 'items_width', 200, GetterInterface::INT );
					}

					if ( ! $width ) {
						$width			=	200;
					} elseif ( $width < 100 ) {
						$width			=	100;
					}

					if ( $i == 0 ) {
						$width			=	( $width * 2 );
					}

					$embed				=	$item->thumbnail( $gallery, 0, ( ( ! $single ) && ( ( $item->width( true ) >= $width ) || ( $item->height( true ) >= $width ) ) ) );

					if ( ( $i == 5 ) && ( count( $items ) > 6 ) ) {
						$embed			.=	'<div class="galleryContainerMore" style="font-size: ' . ( $width * 0.25 ) . 'px; vertical-align: middle;">+' . ( count( $items ) - 5 ) . '</div>';
					}

					$return				.=		'<div class="galleryItemContainer galleryContainer galleryContainer' . htmlspecialchars( $gallery->id() ) . $item->get( 'id', 0, GetterInterface::INT ) . ( $i > 5 ? ' hidden' : null ) . '">'
										.			'<div class="galleryContainerInner"' . ( ! $single ? ' style="width: ' . $width . 'px;"' : null ) . '>'
										.				'<div class="galleryContainerTop"' . ( ! $single ? ' style="height: ' . $width . 'px; line-height: ' . $width . 'px;"' : null ) . '>'
										.					cbTooltip( null, '', null, array( '90%', '90%' ), null, $embed, 'javascript: void(0);', 'class="galleryItemEmbed galleryModalToggle" data-cbtooltip-modal="true" data-cbtooltip-open-solo="document" data-cbtooltip-classes="galleryModal" data-cbgallery-url="' . $displayPath . '" data-cbgallery-request="' . htmlspecialchars( json_encode( $data ) ) . '"' )
										.				'</div>'
										.			'</div>'
										.		'</div>';
					break;
				case 'files':
					$return				.=		'<table class="galleryMediaFileTable table table-bordered">'
										.			'<tbody>'
										.				'<tr>'
										.					'<th style="width: 125px;">' . CBTxt::T( 'File' ) . '</th>'
										.					'<td><a href="' . $showPath . '" target="_blank">' . $item->name() . '</a></td>'
										.				'</tr>'
										.				'<tr>'
										.					'<th style="width: 125px;">' . CBTxt::T( 'Extension' ) . '</th>'
										.					'<td>' . $item->extension() . '</td>'
										.				'</tr>'
										.				'<tr>'
										.					'<th style="width: 125px;">' . CBTxt::T( 'Size' ) . '</th>'
										.					'<td>' . $item->size() . '</td>'
										.				'</tr>'
										.				'<tr>'
										.					'<th style="width: 125px;">' . CBTxt::T( 'Modified' ) . '</th>'
										.					'<td>' . cbFormatDate( $item->modified() ) . '</td>'
										.				'</tr>'
										.				'<tr>'
										.					'<td colspan="2" class="text-right"><a href="' . $downloadPath . '" target="_blank" class="btn btn-sm btn-primary">' . CBTxt::T( 'Download' ) . '</a></td>'
										.				'</tr>'
										.			'</tbody>'
										.		'</table>';
					break;
				case 'videos':
					if ( $item->mimeType() == 'video/x-youtube' ) {
						if ( preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:be/))([A-Za-z0-9_-]+)%', $showPath, $matches ) ) {
							$return		.=		'<iframe width="100%" height="360" src="https://www.youtube.com/embed/' . htmlspecialchars( $matches[1] ) . '" frameborder="0" allowfullscreen class="galleryVideoPlayer"></iframe>';
						}
					} else {
						$return			.=		'<video width="100%" height="100%" style="width: 100%; height: 100%;" src="' . $showPath . '" type="' . htmlspecialchars( $item->mimeType() ) . '" controls="controls" preload="auto" class="galleryVideoPlayer"></video>';
					}
					break;
				case 'music':
					$return				.=		'<audio style="width: 100%;" src="' . $showPath . '" type="' . htmlspecialchars( $item->mimeType() ) . '" controls="controls" preload="auto" class="galleryAudioPlayer"></audio>';
					break;
			}
		}

		$return							.=	'</div>';

		switch ( $row->get( 'subtype', null, GetterInterface::STRING ) ) {
			case 'photos':
			case 'files':
			case 'videos':
				if ( $folder ) {
					if ( count( $items ) > 1 ) {
						$title			=	CBTxt::T( 'SHARED_COUNT_TYPES_IN_ALBUM', 'shared [types] in album [album]', array( '[types]' => $typeModal, '[album]' => $folder ) );
					} else {
						$title			=	CBTxt::T( 'SHARED_A_TYPE_IN_ALBUM', 'shared a [type] in album [album]', array( '[type]' => $typeModal, '[album]' => $folder ) );
					}
				} else {
					if ( count( $items ) > 1 ) {
						$title			=	CBTxt::T( 'SHARED_COUNT_TYPES', 'shared [types]', array( '[types]' => $typeModal ) );
					} else {
						$title			=	CBTxt::T( 'SHARED_A_TYPE', 'shared a [type]', array( '[type]' => $typeModal ) );
					}
				}
				break;
			case 'music':
				$title					=	CBTxt::T( 'SHARED_TYPE', 'shared [type]', array( '[type]' => $typeModal ) );
				break;
		}

		$_PLUGINS->trigger( 'gallery_onAfterActivity', array( $row, &$title, &$message, $stream, $items, $gallery, $plugin ) );

		return $return;
	}
}