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

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryItemsNew
{

	/**
	 * @param UserTable       $viewer
	 * @param Gallery         $gallery
	 * @param cbPluginHandler $plugin
	 */
	static public function showItemsNew( $viewer, $gallery, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$canUpload						=	CBGallery::canCreateItems( 'all', 'upload', $gallery );
		$canLink						=	CBGallery::canCreateItems( 'all', 'link', $gallery );

		if ( $canUpload && $canLink ) {
			$pageTitle					=	CBTxt::T( 'New Upload / Link' );
		} elseif ( $canLink ) {
			$pageTitle					=	CBTxt::T( 'New Link' );
		} else {
			$pageTitle					=	CBTxt::T( 'New Upload' );
		}

		$returnUrl						=	CBGallery::getReturn( true, true );

		if ( ! $returnUrl ) {
			$returnUrl					=	$gallery->location();
		}

		if ( $pageTitle ) {
			$folderId					=	$gallery->get( 'folder', 0, GetterInterface::INT );

			if ( $folderId ) {
				$folder					=	$gallery->folder( $folderId );

				if ( $folder->get('id', 0, GetterInterface::INT ) && ( ( $folder->get( 'published', 1, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) == $folder->get( 'user_id', 0, GetterInterface::INT ) ) || CBGallery::canModerate( $gallery ) ) ) ) {
					$pageTitle			.=	' ' . CBTxt::T( 'IN_FOLDER', 'in [folder]', array( '[folder]' => ( $folder->get( 'title', null, GetterInterface::STRING ) ? $folder->get( 'title', null, GetterInterface::STRING ) : cbFormatDate( $folder->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'GALLERY_LONG_DATE_FORMAT', 'F j, Y' ) ) ) ) );
				}
			}

			$_CB_framework->setPageTitle( $pageTitle );
		}

		$integrations					=	$_PLUGINS->trigger( 'gallery_onBeforeItemsNew', array( $viewer, $gallery ) );

		initToolTip();
		cbValidator::loadValidation();

		static $JS_LOADED				=	0;

		if ( ( ! $JS_LOADED++ ) && ( $canUpload || $canLink ) ) {
			$uploadRow					=	'<tr class="galleryShareUploadProgressRow galleryItemsNewUploadFile">'
										.		"<td class=\"galleryItemsNewUploadFileName\">' + ( typeof file.name != 'undefined' ? file.name : Date.now() ) + '</td>"
										.		'<td class="galleryItemsNewUploadFileProgress" style="width: 20%;">'
										.			'<div class="progress">'
										.				'<div class="progress-bar progress-bar-striped active" style="width: 0;"></div>'
										.			'</div>'
										.		'</td>'
										.		'<td class="galleryItemsNewUploadFileActions" style="width: 1%;">'
										.			'<button class="galleryShareUploadProgressCancel galleryItemsNewUploadFileCancel btn btn-xs btn-danger">' . addslashes( CBTxt::T( 'Cancel' ) ) . '</button>'
										.			'<button class="galleryShareUploadProgressClear galleryItemsNewUploadFileClear btn btn-xs btn-danger hidden"><span class="fa fa-times"></span></button>'
										.		'</td>'
										.	'</tr>';

			$uploadError				=	'<tr class="galleryShareUploadProgressError galleryItemsNewUploadFileAlert danger text-danger">'
										.		"<td colspan=\"3\">' + response.message + '</td>"
										.	'</tr>';

			$linkError					=	'<span class="galleryShareLinkError help-block">'
										.		"' + response.message + '"
										.	'</span>';

			$js							=	"$( '.galleryItemsNew' ).cbgallery({"
										.		"mode: 'share',"
										.		"url: '" . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'item', 'func' => 'save', 'folder' => $gallery->get( 'folder', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), cbSpoofField() => cbSpoofString( null, 'plugin' ) ), 'raw', 0, true ) ) . "',"
										.		"callback: {"
										.			"upload: {"
										.				"add: function ( cbgallery, data, file ) {"
										.					"return $( '" . $uploadRow . "' );"
										.				"},"
										.				"error: function ( cbgallery, response ) {"
										.					"return $( '" . $uploadError . "' );"
										.				"}"
										.			"},"
										.			"link: {"
										.				"error: function ( cbgallery, response ) {"
										.					"return $( '" . $linkError . "' );"
										.				"}"
										.			"}"
										.		"}"
										.	"});";

			$_CB_framework->outputCbJQuery( $js, 'cbgallery' );
		}

		$return							=	'<div class="galleryItemsNew">'
										.		( $pageTitle ? '<div class="galleryFolderTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
										.		implode( '', $integrations );

		if ( $canUpload ) {
			$return						.=		'<div class="galleryItemsNewUpload">'
										.			'<div class="galleryShareUploadDropZone galleryItemsNewUploadArea well well-lg text-center">'
										.				'<strong><span class="fa fa-upload"></span> ' . CBTxt::T( 'Click or Drag & Drop to Upload' ) . '</strong>'
										.				'<input type="file" name="upload" class="galleryShareUpload hidden" multiple />'
										.			'</div>'
										.			'<div class="galleryShareUploadProgress galleryItemsNewUploadFiles hidden">'
										.				'<table class="table table-bordered">'
										.					'<tbody class="galleryShareUploadProgressRows">'
										.					'</tbody>'
										.				'</table>'
										.			'</div>'
										.		'</div>';
		}

		if ( $canLink ) {
			if ( $canUpload ) {
				$return					.=		'<div class="galleryItemsNewOr text-center text-muted text-small">'
										.			'<span class="galleryItemsNewOrLine bg-muted"></span>' . CBTxt::T( 'OR' ) . '<span class="galleryItemsNewOrLine bg-muted"></span>'
										.		'</div>';
			}

			$return						.=		'<div class="galleryShareLinkArea galleryItemsNewLink form-group">'
										.			'<div class="input-group">'
										.				'<input type="text" class="galleryShareLink form-control" placeholder="' . htmlspecialchars( CBTxt::T( 'Have a media link to share?' ) ) . '">'
										.				'<span class="input-group-btn">'
										.					'<span class="galleryShareLinkLoading fa fa-spinner fa-pulse hidden"></span>'
										.					'<button type="button" class="galleryShareLinkSave galleryButton galleryButtonShare btn btn-primary">' . CBTxt::T( 'Share' ) . '</button>'
										.				'</span>'
										.			'</div>'
										.		'</div>';
		}

		$return							.=		'<div class="galleryShareEdit galleryItemsNewEdit hidden">'
										.			'<hr />'
										.		'</div>'
										.		implode( '', $_PLUGINS->trigger( 'gallery_onAfterItemsNew', array( $viewer, $gallery ) ) )
										.		'<div class="galleryItemsNewButtons text-right">'
										.			'<button type="button" class="galleryShareEditDone galleryButton galleryButtonDone btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you are done? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( htmlspecialchars( $returnUrl ) ) . '\'; })">' . CBTxt::T( 'Done' ) . '</button>'
										.		'</div>'
										.	'</div>';

		echo $return;
	}
}