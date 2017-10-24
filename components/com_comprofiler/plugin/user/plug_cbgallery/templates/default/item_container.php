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
use CB\Plugin\Gallery\Table\ItemTable;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryItemContainer
{

	/**
	 * @param ItemTable        $row
	 * @param UserTable        $viewer
	 * @param Gallery          $gallery
	 * @param CBplug_cbgallery $plugin
	 * @param string           $output
	 * @return string
	 */
	static public function showItemContainer( $row, $viewer, $gallery, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$canModerate				=	CBGallery::canModerate( $gallery );

		static $JS_LOADED			=	0;

		if ( ! $JS_LOADED++ ) {
			initToolTip();

			$_CB_framework->outputCbJQuery( "$( '.galleryItemName,.galleryItemEmbed' ).cbgallery();", 'cbgallery' );
		}

		$menu						=	array();

		$content					=	$_PLUGINS->trigger( 'gallery_onDisplayItem', array( &$row, &$menu, $gallery ) );

		$owner						=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user_id', 0, GetterInterface::INT ) );
		$title						=	( $row->get( 'title', null, GetterInterface::STRING ) ? $row->get( 'title', null, GetterInterface::STRING ) : $row->name() );
		$type						=	$row->get( 'type', null, GetterInterface::STRING );
		$pending					=	( ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) && $gallery->get( $type . '_create_approval', false, GetterInterface::BOOLEAN ) );

		$returnUrl					=	base64_encode( $gallery->location() );

		$return						=	'<div class="galleryItemContainer galleryContainer galleryContainer' . htmlspecialchars( $gallery->id() ) . $row->get( 'id', 0, GetterInterface::INT ) . ' img-thumbnail">';

		if ( ( $output != 'compact' ) && ( $canModerate || $owner || $menu ) ) {
			$menuItems				=	'<ul class="galleryMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

			if ( $menu ) {
				$menuItems			.=		'<li class="galleryMenuItem">' . implode( '</li><li class="galleryMenuItem">', $menu ) . '</li>';
			}

			if ( $canModerate || $owner ) {
				$menuItems			.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

				if ( $pending ) {
					if ( $canModerate ) {
						$menuItems	.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
					}
				} elseif ( $row->get( 'published', 1, GetterInterface::INT ) > 0 ) {
					// CBTxt::T( 'ARE_YOU_SURE_UNPUBLISH_TYPE', 'Are you sure you want to unpublish this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) )
					$menuItems		.=		'<li class="galleryMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'ARE_YOU_SURE_UNPUBLISH_TYPE ARE_YOU_SURE_UNPUBLISH_' . strtoupper( $type ), 'Are you sure you want to unpublish this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'unpublish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
				} else {
					$menuItems		.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
				}

				// CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE', 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) )
				$menuItems			.=		'<li class="galleryMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE ARE_YOU_SURE_DELETE_' . strtoupper( $type ), 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
			}

			$menuItems				.=	'</ul>';

			$menuAttr				=	cbTooltip( null, $menuItems, null, 'auto', null, null, null, 'class="galleryButton galleryButtonMenu btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

			$return					.=		'<div class="galleryContainerMenu">'
									.			'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
									.		'</div>';
		}

		$width						=	$row->get( '_width', 0, GetterInterface::INT );

		if ( ! $width ) {
			$width					=	$gallery->get( 'items_width', 200, GetterInterface::INT );
		}

		if ( ! $width ) {
			$width					=	200;
		} elseif ( $width < 100 ) {
			$width					=	100;
		}

		$name						=	$title;
		$embed						=	'<span class="fa fa-question" style="font-size: ' . ( $width - 25 ) . 'px; vertical-align: middle;"></span>';

		if ( $row->exists() ) {
			$displayPath			=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'display', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ), 'raw', 0, true );
			$data					=	array();

			if ( $row->get( '_previous', null, GetterInterface::STRING ) ) {
				$data['previous']	=	$row->get( '_previous', null, GetterInterface::STRING );
			}

			if ( $row->get( '_next', null, GetterInterface::STRING ) ) {
				$data['next']		=	$row->get( '_next', null, GetterInterface::STRING );
			}

			$name					=	cbTooltip( null, '', null, array( '90%', '90%' ), null, $name, 'javascript: void(0);', 'class="galleryItemName galleryModalToggle" data-cbtooltip-modal="true" data-cbtooltip-open-solo="document" data-cbtooltip-classes="galleryModal" data-cbgallery-url="' . $displayPath . '" data-cbgallery-request="' . htmlspecialchars( json_encode( $data ) ) . '"' );
			$embed					=	cbTooltip( null, '', null, array( '90%', '90%' ), null, $row->thumbnail( $gallery, $width, ( ( $row->width( true ) >= $width ) || ( $row->height( true ) >= $width ) || ( $row->mimeType() == 'video/x-youtube' ) ) ), 'javascript: void(0);', 'class="galleryItemEmbed" data-cbtooltip-modal="true" data-cbtooltip-open-solo="document" data-cbtooltip-classes="galleryModal" data-cbgallery-url="' . $displayPath . '" data-cbgallery-request="' . htmlspecialchars( json_encode( $data ) ) . '"' );
		}

		$return						.=		'<div class="galleryContainerInner" style="width: ' . $width . 'px;">'
									.			'<div class="galleryContainerTop" style="height: ' . $width . 'px; line-height: ' . $width . 'px;">'
									.				$embed
									.			'</div>';

		if ( $output != 'compact' ) {
			$return					.=			'<div class="galleryContainerBottom bg-default">'
									.				'<div class="galleryContainerContent">'
									.					'<div class="galleryContainerContentRow text-nowrap text-overflow small">'
									.						'<strong>'
									.							$name
									.						'</strong>'
									.					'</div>'
									.					'<div class="galleryContainerContentRow text-nowrap text-overflow small">'
									.						'<span>'
									.							cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'GALLERY_SHORT_DATE_FORMAT', 'M j, Y' ) )
									.						'</span>'
									.						'<div class="galleryContainerIcons">';

			if ( $pending ) {
				$return				.=							cbTooltip( null, CBTxt::T( 'Pending Approval' ), null, 'auto', null, '<span class="fa fa-warning text-warning"></span>', null, 'class="galleryContainerIconPending" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
			} elseif ( $row->get( 'published', 1, GetterInterface::INT ) != 1 ) {
				$return				.=							cbTooltip( null, CBTxt::T( 'Unpublished' ), null, 'auto', null, '<span class="fa fa-eye-slash"></span>', null, 'class="galleryContainerIconUnpublished" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
			}

			$return					.=							( $row->get( 'description', null, GetterInterface::STRING ) ? cbTooltip( null, $row->get( 'description', null, GetterInterface::STRING ), $title, null, null, '<span class="fa fa-info-circle text-muted"></span>', null, 'class="galleryContainerIconDescription"' ) : null )
									.							( count( $gallery->types() ) > 1 ? cbTooltip( null, CBGallery::translateType( $type ), null, 'auto', null, '<span class="fa ' . CBGallery::getTypeIcon( $type ) . '"></span>', null, 'class="galleryContainerIconType" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' ) : null )
									.						'</div>'
									.					'</div>'
									.					( $content ? '<div class="galleryContainerContentRow text-nowrap text-overflow small">' . implode( '</div><div class="galleryContainerContentRow text-nowrap text-overflow small">', $content ) . '</div>' : null )
									.				'</div>'
									.			'</div>';
		}

		$return						.=		'</div>';

		if ( $output == 'approval' ) {
			$return					.=		'<div class="galleryContainerApproval text-center">'
									.			'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '\';" class="galleryButton galleryButtonApprove btn btn-xs btn-success">' . CBTxt::T( 'Approve' ) . '</button>'
												// CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE', 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) )
									.			' <button onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE ARE_YOU_SURE_DELETE_' . strtoupper( $type ), 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) ) . '\'; })" class="galleryButton galleryButtonDelete btn btn-xs btn-danger">' . CBTxt::T( 'Delete' ) . '</button>'
									.		'</div>';
		}

		$return						.=	'</div>';

		return $return;
	}
}