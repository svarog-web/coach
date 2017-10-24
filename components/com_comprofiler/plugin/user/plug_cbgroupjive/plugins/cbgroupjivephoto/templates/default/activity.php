<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJivePhoto\Table\PhotoTable;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjivePhotoActivity
{

	/**
	 * render frontend photo activity
	 *
	 * @param ActivityTable   $row
	 * @param string          $title
	 * @param string          $message
	 * @param Activity        $stream
	 * @param PhotoTable      $photo
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showPhotoActivity( $row, &$title, &$message, $stream, $photo, $plugin )
	{
		global $_CB_framework;

		static $loaded		=	0;

		if ( ! $loaded++ ) {
			$js				=	"$( '.gjGroupPhotoItem.cbTooltip,.gjGroupPhotoLogo.cbTooltip' ).on( 'cbtooltip.move', function( e, cbtooltip, event, api ) {"
							.		"if ( api.elements.tooltip ) {"
							.			"api.elements.content.find( '.gjGroupPhotoImage' ).css( 'line-height', api.elements.content.css( 'max-height' ) );"
							.		"}"
							.	"});";

			$_CB_framework->outputCbJQuery( $js );
		}

		$title				=	CBTxt::T( 'GROUP_PHOTO_ACTIVITY_TITLE', 'uploaded a photo in [group]', array( '[group]' => '<strong><a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $photo->group()->get( 'id' ) ) ) . '">' . htmlspecialchars( CBTxt::T( $photo->group()->get( 'name' ) ) ) . '</a></strong>' ) );

		$name				=	( $photo->get( 'title' ) ? htmlspecialchars( $photo->get( 'title' ) ) : $photo->name() );
		$item				=	$name;
		$logo				=	null;

		if ( $photo->exists() ) {
			$showPath		=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'show', 'id' => (int) $photo->get( 'id' ) ), 'raw', 0, true );

			$image			=	'<div class="gjGroupPhotoImageContainer">'
							.		'<div class="gjGroupPhotoImage text-center">'
							.			'<img alt="' . htmlspecialchars( $title ) . '" src="' . htmlspecialchars( $showPath ) . '" class="cbImgPict cbFullPict img-thumbnail" />'
							.		'</div>'
							.		'<div class="gjGroupPhotoImageInfo">'
							.			'<div class="gjGroupPhotoImageInfoRow">'
							.				'<div class="gjGroupPhotoImageInfoTitle col-sm-8 text-left"><strong>' . $name . '</strong></div>'
							.				'<div class="gjGroupPhotoImageInfoOriginal col-sm-4 text-right">'
							.					'<a href="' . $showPath . '" target="_blank">'
							.						CBTxt::T( 'Original' )
							.					'</a>'
							.				'</div>'
							.			'</div>';

			if ( $photo->get( 'description' ) ) {
				$image		.=			'<div class="gjGroupPhotoImageInfoRow">'
							.				'<div class="gjGroupPhotoImageInfoDescription col-sm-8 text-left">' . htmlspecialchars( $photo->get( 'description' ) ) . '</div>'
							.				'<div class="gjGroupPhotoImageInfoDownload col-sm-4 text-right">'
							.				'</div>'
							.			'</div>';
			}

			$image			.=		'</div>'
							.	'</div>';

			$item			=	cbTooltip( 1, $image, null, array( '80%', '80%' ), null, $item, 'javascript: void(0);', 'class="gjGroupPhotoItem" data-cbtooltip-modal="true" data-cbtooltip-classes="gjGroupPhotoImageModal"' );
			$logo			=	cbTooltip( 1, $image, null, array( '80%', '80%' ), null, '<img src="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'photo', 'func' => 'preview', 'id' => (int) $photo->get( 'id' ) ), 'raw', 0, true ) . '" class="cbImgPict cbThumbPict img-thumbnail" />', 'javascript: void(0);', 'class="gjGroupPhotoLogo" data-cbtooltip-modal="true" data-cbtooltip-classes="gjGroupPhotoImageModal"' );
		}

		$return				=	'<div class="gjPhotoActivity">'
							.		'<div class="gjGroupPhotoRow gjCanvasBox cbCanvasBox cbCanvasBoxInline">'
							.			'<div class="gjCanvasBoxTop cbCanvasBoxTop">'
							.				'<div class="gjCanvasBoxPhoto cbCanvasBoxPhoto cbCanvasBoxPhotoCenter">'
							.					$logo
							.				'</div>'
							.			'</div>'
							.		'</div>'
							.	'</div>';

		return $return;
	}
}