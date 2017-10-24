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
use CB\Plugin\Gallery\Table\FolderTable;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryFolderContainer
{

	/**
	 * @param FolderTable      $row
	 * @param UserTable        $viewer
	 * @param Gallery          $gallery
	 * @param CBplug_cbgallery $plugin
	 * @param string           $output
	 * @return string
	 */
	static public function showFolderContainer( $row, $viewer, $gallery, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$canModerate				=	CBGallery::canModerate( $gallery );

		$menu						=	array();

		$content					=	$_PLUGINS->trigger( 'gallery_onDisplayFolder', array( &$row, &$menu, $gallery, $output ) );

		$owner						=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user_id', 0, GetterInterface::INT ) );
		$title						=	( $row->get( 'title', null, GetterInterface::STRING ) ? $row->get( 'title', null, GetterInterface::STRING ) : cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'GALLERY_SHORT_DATE_FORMAT', 'M j, Y' ) ) );
		$pending					=	( ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) && $gallery->get( 'folders_create_approval', false, GetterInterface::BOOLEAN ) );

		$returnUrl					=	base64_encode( $gallery->location() );

		$return						=	'<div class="galleryFolderContainer galleryContainer img-thumbnail">';

		if ( ( $output != 'compact' ) && ( $canModerate || $owner || $menu ) ) {
			$menuItems				=	'<ul class="galleryMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

			if ( $menu ) {
				$menuItems			.=		'<li class="galleryMenuItem">' . implode( '</li><li class="galleryMenuItem">', $menu ) . '</li>';
			}

			if ( $canModerate || $owner ) {
				$menuItems			.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

				if ( $pending ) {
					if ( $canModerate ) {
						$menuItems	.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
					}
				} elseif ( $row->get( 'published', 1, GetterInterface::INT ) > 0 ) {
					$menuItems		.=		'<li class="galleryMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this album?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'unpublish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
				} else {
					$menuItems		.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
				}

				$menuItems			.=		'<li class="galleryMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this album and all its files?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
			}

			$menuItems				.=	'</ul>';

			$menuAttr				=	cbTooltip( null, $menuItems, null, 'auto', null, null, null, 'class="galleryButton galleryButtonMenu btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

			$return					.=		'<div class="galleryContainerMenu">'
									.			'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
									.		'</div>';
		}

		$width						=	$row->get( '_width', 0, GetterInterface::INT );

		if ( ! $width ) {
			$width					=	$gallery->get( 'folders_width', 200, GetterInterface::INT );
		}

		if ( ! $width ) {
			$width					=	200;
		} elseif ( $width < 100 ) {
			$width					=	100;
		}

		$return						.=		'<div class="galleryContainerInner" style="width: ' . $width . 'px;">'
									.			'<div class="galleryContainerTop" style="height: ' . $width . 'px; line-height: ' . $width . 'px;">'
									.				'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'show', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '" class="galleryItemEmbed">'
									.					$row->thumbnail( $gallery, $width )
									.				'</a>'
									.			'</div>';

		if ( $output != 'compact' ) {
			$return					.=			'<div class="galleryContainerBottom bg-default">'
									.				'<div class="galleryContainerContent">'
									.					'<div class="galleryContainerContentRow text-nowrap text-overflow small">'
									.						'<strong>'
									.							'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'show', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '">'
									.								$title
									.							'</a>'
									.						'</strong>'
									.					'</div>'
									.					'<div class="galleryContainerContentRow text-nowrap text-overflow small">'
									.						CBTxt::T( 'COUNT_FILES', '%%COUNT%% File|%%COUNT%% Files|{0}Empty', array( '%%COUNT%%' => $row->items( true, $gallery ) ) )
									.						'<div class="galleryContainerIcons">';

			if ( $pending ) {
				$return				.=							cbTooltip( null, CBTxt::T( 'Pending Approval' ), null, 'auto', null, '<span class="fa fa-warning text-warning"></span>', null, 'class="galleryContainerIconPending" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
			} elseif ( $row->get( 'published', 1, GetterInterface::INT ) != 1 ) {
				$return				.=							cbTooltip( null, CBTxt::T( 'Unpublished' ), null, 'auto', null, '<span class="fa fa-eye-slash"></span>', null, 'class="galleryContainerIconUnpublished" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
			}

			$return					.=							( $row->get( 'description', null, GetterInterface::STRING ) ? cbTooltip( null, $row->get( 'description', null, GetterInterface::STRING ), $title, null, null, '<span class="fa fa-info-circle text-muted"></span>', null, 'class="galleryContainerIconDescription"' ) : null )
									.						'</div>'
									.					'</div>'
									.					( $content ? '<div class="galleryContainerContentRow text-nowrap text-overflow small">' . implode( '</div><div class="galleryContainerContentRow text-nowrap text-overflow small">', $content ) . '</div>' : null )
									.				'</div>'
									.			'</div>';
		}

		$return						.=		'</div>';

		if ( $output == 'approval' ) {
			$return					.=		'<div class="galleryContainerApproval text-center">'
									.			'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '\';" class="galleryButton galleryButtonApprove btn btn-xs btn-success">' . CBTxt::T( 'Approve' ) . '</button>'
									.			' <button onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this album and all its files?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) ) . '\'; })" class="galleryButton galleryButtonDelete btn btn-xs btn-danger">' . CBTxt::T( 'Delete' ) . '</button>'
									.		'</div>';
		}

		$return						.=	'</div>';

		return $return;
	}
}