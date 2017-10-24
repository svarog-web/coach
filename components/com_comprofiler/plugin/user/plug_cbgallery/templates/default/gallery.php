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
use CB\Plugin\Gallery\Table\FolderTable;
use CB\Plugin\Gallery\Table\ItemTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryGallery
{

	/**
	 * @param FolderTable|null   $folder
	 * @param FolderTable[]|null $folders
	 * @param cbPageNav|null     $foldersPageNav
	 * @param ItemTable[]        $items
	 * @param cbPageNav          $itemsPageNav
	 * @param string             $searching
	 * @param array              $input
	 * @param UserTable          $viewer
	 * @param Gallery            $gallery
	 * @param CBplug_cbgallery   $plugin
	 * @param string             $output
	 */
	static public function showGallery( $folder, $folders, $foldersPageNav, $items, $itemsPageNav, $searching, $input, $viewer, $gallery, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		if ( $input['type']  ) {
			static $JS_LOADED					=	0;

			if ( ! $JS_LOADED++ ) {
				$js								=	"$( '.gallerySearchType' ).cbselect({"
												.		"width: 'auto',"
												.		"height: '100%',"
												.		"minimumResultsForSearch: Infinity"
												.	"});";

				$_CB_framework->outputCbJQuery( $js, array( 'cbselect' ) );
			}
		}

		if ( $gallery->get( 'published', null, GetterInterface::INT ) == -1 ) {
			$canCreateFolders					=	false;
			$canCreateItems						=	false;
		} else {
			$canCreateFolders					=	( $folder === null ? CBGallery::canCreateFolders( $gallery ) : false );
			$canCreateItems						=	CBGallery::canCreateItems( 'all', 'both', $gallery );
		}

		$canSearchFolders						=	( $gallery->get( 'folders', true, GetterInterface::BOOLEAN ) && ( $folder === null ) ? ( $gallery->get( 'folders_search', true, GetterInterface::BOOLEAN ) && ( $gallery->folders( true ) || $searching ) ) : false );
		$canSearchItems							=	( $gallery->get( 'items_search', true, GetterInterface::BOOLEAN ) && ( $gallery->items( true ) || $searching ) );

		$integrations							=	$_PLUGINS->trigger( 'gallery_onBeforeDisplayGallery', array( &$folder, &$folders, &$foldersPageNav, &$items, &$itemsPageNav, &$searching, &$input, $viewer, $gallery, $output ) );

		$returnUrl								=	base64_encode( $gallery->location() );

		$return									=	'<div class="gallery' . htmlspecialchars( $gallery->id() ) . '">'
												.		implode( '', $integrations )
												.		'<form action="' . htmlspecialchars( $gallery->location() ) . '" method="post" name="galleryForm' . htmlspecialchars( $gallery->id() ) . '" id="galleryForm' . htmlspecialchars( $gallery->id() ) . '" class="galleryForm">';

		if ( $folder !== null ) {
			$return								.=			HTML_cbgalleryFolder::showFolder( $folder, $viewer, $gallery, $plugin, $output );
		}

		if ( $canCreateFolders || $canCreateItems || $canSearchFolders || $canSearchItems ) {
			$return								.=			'<div class="galleryHeader row">';

			if ( $canCreateFolders || $canCreateItems ) {
				$return							.=				'<div class="' . ( ! ( $canSearchFolders || $canSearchItems ) ? 'col-sm-12' : ( $input['type'] ? 'col-sm-6' : 'col-sm-8' ) ) . ' text-left">'
												.					'<div class="btn-group">';

				if ( $canCreateItems ) {
					$canUpload					=	CBGallery::canCreateItems( 'all', 'upload', $gallery );
					$canLink					=	CBGallery::canCreateItems( 'all', 'link', $gallery );

					$return						.=						'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'items', 'func' => 'new', 'folder' => $gallery->get( 'folder', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '\';" class="galleryButton galleryButtonNewItem btn btn-success"><span class="fa fa-plus-circle"></span> ';

					if ( $canUpload && $canLink ) {
						$return					.=							CBTxt::T( 'New Upload / Link' );
					} elseif ( $canUpload ) {
						$return					.=							CBTxt::T( 'New Upload' );
					} elseif ( $canLink ) {
						$return					.=							CBTxt::T( 'New Link' );
					}

					$return						.=						'</button>';
				}

				$return							.=						( $canCreateFolders ? '<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'new', 'gallery' => $gallery->id(), 'return' => $returnUrl ) ) . '\';" class="galleryButton galleryButtonNewAlbum btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New Album' ) . '</button>' : null )
												.					'</div>'
												.				'</div>';
			}

			if ( $canSearchFolders || $canSearchItems ) {
				$return							.=				'<div class="' . ( ! ( $canCreateFolders || $canCreateItems ) ? ( $input['type'] ? 'col-sm-offset-6 ' : 'col-sm-offset-8 ' ) : null ) . ( $input['type'] ? 'col-sm-6' : 'col-sm-4' ) . ' text-right">'
												.					'<div class="input-group">'
												.						'<span class="input-group-addon"><span class="fa fa-search"></span></span>'
												.						$input['search']
												.						( $input['type'] ? '<span class="input-group-addon">' . $input['type'] . '</span>' : null )
												.					'</div>'
												.				'</div>';
			}

			$return								.=			'</div>';
		}

		if ( $folders !== null ) {
			$return								.=			HTML_cbgalleryFolders::showFolders( $folders, $foldersPageNav, $viewer, $gallery, $plugin, $output );
		}

		if ( $items || ( $folder !== null ) || ( $folders === null ) || $searching ) {
			if ( $folders !== null ) {
				$return							.=			'<hr />';
			}

			$return								.=			HTML_cbgalleryItems::showItems( $items, $itemsPageNav, $viewer, $gallery, $plugin, $output );
		}

		$return									.=			'<input type="hidden" name="gallery" value="' . htmlspecialchars( $gallery->id() ) . '" />'
												.		'</form>'
												.		implode( '', $_PLUGINS->trigger( 'gallery_onAfterDisplayGallery', array( $folder, $folders, $foldersPageNav, $items, $itemsPageNav, $searching, $input, $viewer, $gallery, $output ) ) );

		if ( ( $folder !== null ) && CBGallery::getReturn( true ) ) {
			$backUrl							=	CBGallery::getReturn( true, true );

			if ( $gallery->get( 'folder', 0, GetterInterface::INT ) && ( $returnUrl == CBGallery::getReturn( true ) ) ) {
				$backUrl						=	$gallery->reset()->location();
			}

			$return								.=		'<div class="galleryFolderBack text-right">'
												.			'<button type="button" onclick="window.location.href=\'' . htmlspecialchars( $backUrl ) . '\';" class="galleryButton galleryButtonBack btn btn-sm btn-default">' . CBTxt::T( 'Back' ) . '</button>'
												.		'</div>';
		}

		$return									.=	'</div>';

		echo $return;
	}
}