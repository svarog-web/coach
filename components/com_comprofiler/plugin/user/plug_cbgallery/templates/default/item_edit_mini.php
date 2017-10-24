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

class HTML_cbgalleryItemEditMini
{

	/**
	 * @param ItemTable        $row
	 * @param array            $input
	 * @param UserTable        $viewer
	 * @param Gallery          $gallery
	 * @param CBplug_cbgallery $plugin
	 * @param string           $output
	 * @return string
	 */
	static public function showItemEditMini( $row, $input, $viewer, $gallery, $plugin, $output = null )
	{
		global $_CB_framework, $_PLUGINS;

		$integrations				=	$_PLUGINS->trigger( 'gallery_onBeforeItemEditMini', array( &$row, &$input, $viewer, $gallery ) );

		initToolTip();
		cbValidator::loadValidation();

		static $JS_LOADED			=	0;

		if ( ! $JS_LOADED++ ) {
			$editError				=	'<div class="galleryEditError alert alert-danger">'
									.		"' + response.message + '"
									.	'</div>';

			$js						=	"$( '.galleryItemEditMini #folder' ).cbselect({ width: '100%' });"
									.	"$( '.galleryEdit' ).cbgallery({"
									.		"mode: 'edit',"
									.		"callback: {"
									.			"delete: {"
									.				"error: function ( cbgallery, response ) {"
									.					"return $( '" . $editError . "' );"
									.				"}"
									.			"},"
									.			"edit: {"
									.				"error: function ( cbgallery, response ) {"
									.					"return $( '" . $editError . "' );"
									.				"}"
									.			"}"
									.		"}"
									.	"});";

			$_CB_framework->outputCbJQuery( $js, array( 'cbselect', 'cbgallery' ) );
		}

		$type						=	$row->get( 'type', null, GetterInterface::STRING );

		$return						=	'<div class="galleryEdit galleryItemEditMini panel panel-default container-fluid">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'save', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true ) . '" method="post" enctype="multipart/form-data" name="galleryItemForm" id="galleryItemForm" class="galleryEditForm galleryItemForm cb_form form-auto cbValidation">'
									.			'<div class="row panel-body">'
									.				'<div class="col-sm-4 galleryItemEditMiniPreview">'
									.					HTML_cbgalleryItemContainer::showItemContainer( $row, $viewer, $gallery, $plugin, 'compact' )
									.				'</div>'
									.				'<div class="col-sm-8 galleryItemEditMiniRows">'
									.					implode( '', $integrations )
									.					'<div class="row">';

		if ( $gallery->get( 'folders', true, GetterInterface::BOOLEAN ) && $input['folder'] ) {
			$return					.=						'<div class="col-sm-7 galleryItemEditMiniTitle">'
									.							$input['title']
									.						'</div>'
									.						'<div class="col-sm-5 galleryItemEditMiniAlbum">'
									.							$input['folder']
									.						'</div>';
		} else {
			$return					.=						'<div class="col-sm-12 galleryItemEditMiniTitle">'
									.							$input['title']
									.						'</div>';
		}

		$return						.=					'</div>'
									.					'<div class="row">'
									.						'<div class="col-sm-12 galleryItemEditMiniDescription">'
									.							$input['description']
									.						'</div>'
									.					'</div>'
									.					implode( '', $_PLUGINS->trigger( 'gallery_onAfterItemEditMini', array( $row, $input, $viewer, $gallery ) ) )
									.				'</div>'
									.			'</div>'
									.			'<div class="row panel-footer">'
													// CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE', 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) )
									.				'<input type="button" value="' . htmlspecialchars( CBTxt::T( 'Delete' ) ) . '" data-cbgallery-delete-message="' . htmlspecialchars( CBTxt::T( 'ARE_YOU_SURE_DELETE_TYPE ARE_YOU_SURE_DELETE_' . strtoupper( $type ), 'Are you sure you want to delete this [type]?', array( '[type]' => CBGallery::translateType( $type, false, true ) ) ) ) . '" data-cbgallery-delete-url="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true ) . '" class="galleryEditDelete galleryButton galleryButtonDelete btn btn-xs btn-danger pull-left" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.				'<input type="button" value="' . htmlspecialchars( CBTxt::T( 'Save' ) ) . '" class="galleryEditSave galleryButton galleryButtonSubmit btn btn-xs btn-primary pull-right" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.			'</div>'
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.		'<div class="galleryEditLoading text-center hidden"><span class="fa fa-spinner fa-pulse fa-3x"></span></div>'
									.		( $output == 'ajax' ? CBGallery::reloadHeaders() : null )
									.	'</div>';

		return $return;
	}
}