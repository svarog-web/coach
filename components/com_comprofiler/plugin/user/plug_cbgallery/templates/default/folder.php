<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Gallery\Table\FolderTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryFolder
{

	/**
	 * @param FolderTable      $row
	 * @param UserTable        $viewer
	 * @param Gallery          $gallery
	 * @param CBplug_cbgallery $plugin
	 * @param string           $output
	 * @return string
	 */
	static public function showFolder( $row, $viewer, $gallery, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$menu							=	array();

		$integrations					=	$_PLUGINS->trigger( 'gallery_onBeforeDisplayFolder', array( &$row, &$menu, $viewer, $gallery, $output ) );

		$cbUser							=	CBuser::getInstance( $row->get( 'user_id', 0, GetterInterface::INT ), false );
		$canModerate					=	CBGallery::canModerate( $gallery );
		$owner							=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'user_id', 0, GetterInterface::INT ) );

		$returnUrl						=	base64_encode( $gallery->location() );

		$return							=	'<div class="galleryFolderHeader page-header clearfix">'
										.		implode( '', $integrations )
										.		'<h3 class="row">'
										.			'<div class="col-xs-8 text-left">'
										.				( $row->get( 'title', null, GetterInterface::STRING ) ? htmlspecialchars( $row->get( 'title', null, GetterInterface::STRING ) ) . ( $row->get( 'id', 0, GetterInterface::INT ) !== 0 ? ' <span class="galleryFolderHeaderDate small">' . cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, 'timeago' ) . '</span>' : null ) : cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'GALLERY_LONG_DATE_FORMAT', 'F j, Y' ) ) )
										.				( $row->get( 'description', null, GetterInterface::STRING ) ? '<div class="galleryFolderHeaderDescription small">' . $row->get( 'description', null, GetterInterface::STRING ) . '</div>' : null )
										.			'</div>'
										.			'<div class="col-xs-4 text-right">'
										.				'<div class="media small">'
										.					'<div class="media-body">'
										.						'<div class="galleryFolderHeaderUser">'
										.							$cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true )
										.						'</div>';

		if ( $canModerate || $owner || $menu ) {
			$menuItems					=	'<ul class="galleryMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

			if ( $menu ) {
				$menuItems				.=		'<li class="galleryMenuItem">' . implode( '</li><li class="galleryMenuItem">', $menu ) . '</li>';
			}

			if ( $canModerate || $owner ) {
				$menuItems				.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

				if ( ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) && $gallery->get( 'folders_create_approval', false, GetterInterface::BOOLEAN ) ) {
					if ( $canModerate ) {
						$menuItems		.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
					}
				} elseif ( $row->get( 'published', 1, GetterInterface::INT ) > 0 ) {
					$menuItems			.=		'<li class="galleryMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this album?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'unpublish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
				} else {
					$menuItems			.=		'<li class="galleryMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
				}

				$menuItems				.=		'<li class="galleryMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this album and all its files?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
			}

			$menuItems					.=	'</ul>';

			$menuAttr					=	cbTooltip( null, $menuItems, null, 'auto', null, null, null, 'class="galleryButton galleryButtonMenu btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

			$return						.=						'<div class="galleryFolderHeaderMenu">'
										.							'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
										.						'</div>';
		}

		$return							.=					'</div>'
										.					'<div class="galleryFolderHeaderAvatar media-right">'
										.						$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
										.					'</div>'
										.				'</div>';


		$return							.=			'</div>'
										.		'</h3>'
										.		implode( '', $_PLUGINS->trigger( 'gallery_onAfterDisplayFolder', array( $row, $viewer, $gallery, $output ) ) )
										.	'</div>';

		return $return;
	}
}