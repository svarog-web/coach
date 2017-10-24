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
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Table\ItemTable;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryItemEdit
{

	/**
	 * @param ItemTable        $row
	 * @param array            $input
	 * @param UserTable        $viewer
	 * @param Gallery          $gallery
	 * @param CBplug_cbgallery $plugin
	 */
	static public function showItemEdit( $row, $input, $viewer, $gallery, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$canModerate				=	CBGallery::canModerate( $gallery );
		$canUpload					=	CBGallery::canCreateItems( 'all', 'upload', $gallery );
		$canLink					=	CBGallery::canCreateItems( 'all', 'link', $gallery );
		$type						=	$row->get( 'type', null, GetterInterface::STRING );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			$pageTitle				=	CBTxt::T( 'EDIT_TYPE_NAME EDIT_' . strtoupper( $type ) . '_NAME', 'Edit [type]: [name]', array( '[type]' => CBGallery::translateType( $type ), '[name]' => ( $row->get( 'title', null, GetterInterface::STRING ) ? $row->get( 'title', null, GetterInterface::STRING ) : $row->name() ) ) );
		} else {
			if ( $canUpload && $canLink ) {
				$pageTitle			=	CBTxt::T( 'New Upload / Link' );
			} elseif ( $canLink ) {
				$pageTitle			=	CBTxt::T( 'New Link' );
			} else {
				$pageTitle			=	CBTxt::T( 'New Upload' );
			}
		}

		$returnUrl					=	CBGallery::getReturn( true, true );

		if ( ! $returnUrl ) {
			$returnUrl				=	$gallery->location();
		}

		if ( $pageTitle ) {
			if ( $row->get( 'folder', 0, GetterInterface::INT ) ) {
				$folder				=	$row->folder( $gallery );

				if ( $folder->get('id', 0, GetterInterface::INT ) && ( ( $folder->get( 'published', 1, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) == $folder->get( 'user_id', 0, GetterInterface::INT ) ) || $canModerate ) ) ) {
					$pageTitle		.=	' ' . CBTxt::T( 'IN_FOLDER', 'in [folder]', array( '[folder]' => ( $folder->get( 'title', null, GetterInterface::STRING ) ? $folder->get( 'title', null, GetterInterface::STRING ) : cbFormatDate( $folder->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'GALLERY_LONG_DATE_FORMAT', 'F j, Y' ) ) ) ) );
				}
			}

			$_CB_framework->setPageTitle( $pageTitle );
		}

		$integrations				=	$_PLUGINS->trigger( 'gallery_onBeforeItemEdit', array( &$row, &$input, $viewer, $gallery ) );

		initToolTip();
		cbValidator::loadValidation();

		static $JS_LOADED			=	0;

		if ( ! $JS_LOADED++ ) {
			$_CB_framework->outputCbJQuery( "$( '.galleryItemEdit #folder' ).cbselect();", 'cbselect' );
		}

		$return						=	'<div class="galleryItemEdit">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'item', 'func' => 'save', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) . '" method="post" enctype="multipart/form-data" name="galleryItemForm" id="galleryItemForm" class="galleryItemForm cb_form form-auto cbValidation">'
									.			( $pageTitle ? '<div class="galleryItemTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
									.			implode( '', $integrations );

		if ( $canModerate || ( ! $gallery->get( $type . '_create_approval', false, GetterInterface::BOOLEAN ) ) || ( $row->get( 'id', 0, GetterInterface::INT ) && ( $row->get( 'published', 0, GetterInterface::INT ) != -1 ) ) ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="published" class="col-sm-3 control-label">' . CBTxt::T( 'Published' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['published']
									.					getFieldIcons( 1, 0, null, CBTxt::T( 'Select publish status of the file. If unpublished the file will not be visible to the public.' ) )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="title" class="col-sm-3 control-label">' . CBTxt::T( 'Title' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['title']
									.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input a title. If no title is provided the filename will be displayed as the title.' ) )
									.				'</div>'
									.			'</div>';

		if ( $gallery->get( 'folders', true, GetterInterface::BOOLEAN ) && $input['folder'] ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="folder" class="col-sm-3 control-label">' . CBTxt::T( 'Album' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['folder']
									.					getFieldIcons( 1, 0, null, CBTxt::T( 'Select the album for this file.' ) )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
									.				'<label for="description" class="col-sm-3 control-label">' . CBTxt::T( 'Description' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['description']
									.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input a description.' ) )
									.				'</div>'
									.			'</div>';

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			$return					.=			'<div class="cbft_delimiter form-group cb_form_line clearfix">'
									.				'<div class="cb_field col-sm-offset-3 col-sm-9">'
									.					HTML_cbgalleryItemContainer::showItemContainer( $row, $viewer, $gallery, $plugin, 'compact' )
									.				'</div>'
									.			'</div>';
		}

		if ( $input['method'] ) {
			$return					.=			'<div id="itemMethod" class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="method" class="col-sm-3 control-label">' . CBTxt::T( 'File' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['method']
									.				'</div>'
									.			'</div>';
		}

		if ( $input['upload'] ) {
			$return					.=			'<div id="itemUpload" class="cbft_file cbtt_input form-group cb_form_line clearfix' . ( $input['method'] ? ' hidden' : null ) . '">'
									.				( ! $input['method'] ? '<label for="upload" class="col-sm-3 control-label">' . CBTxt::T( 'File' ) . '</label>' : null )
									.				'<div class="cb_field' . ( $input['method'] ? ' col-sm-offset-3' : null ) . ' col-sm-9">'
									.					$input['upload']
									.					getFieldIcons( 1, ( ! $row->get( 'id' ) ? 1 : 0 ), null, CBTxt::T( 'Select the file to upload.' ) )
									.					( $input['upload_limits'] ? '<div class="help-block">' . implode( ' ', $input['upload_limits'] ) . '</div>' : null )
									.				'</div>'
									.			'</div>';
		}

		if ( $input['link'] ) {
			$return					.=			'<div id="itemLink" class="cbft_text cbtt_input form-group cb_form_line clearfix' . ( $input['method'] ? ' hidden' : null ) . '">'
									.				( ! $input['method'] ? '<label for="link" class="col-sm-3 control-label">' . CBTxt::T( 'File' ) . '</label>' : null )
									.				'<div class="cb_field' . ( $input['method'] ? ' col-sm-offset-3' : null ) . ' col-sm-9">'
									.					$input['link']
									.					getFieldIcons( 1, ( ! $row->get( 'id' ) ? 1 : 0 ), null, CBTxt::T( 'Input the URL to the file to link.' ) )
									.					( $input['link_limits'] ? '<div class="help-block">' . implode( ' ', $input['link_limits'] ) . '</div>' : null )
									.				'</div>'
									.			'</div>';
		}

		if ( ( $type != 'photos' ) && ( $input['thumbnail_upload'] || $input['thumbnail_link'] ) ) {
			if ( $input['thumbnail_method'] ) {
				$return				.=			'<div id="itemThumbnailMethod" class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="thumbnail_method" class="col-sm-3 control-label">' . CBTxt::T( 'Thumbnail' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['thumbnail_method']
									.				'</div>'
									.			'</div>';
			}

			if ( $input['thumbnail_upload'] ) {
				$return				.=			'<div id="itemThumbnailUpload" class="cbft_file cbtt_input form-group cb_form_line clearfix' . ( $input['thumbnail_method'] ? ' hidden' : null ) . '">'
									.				( ! $input['thumbnail_method'] ? '<label for="thumbnail_upload" class="col-sm-3 control-label">' . CBTxt::T( 'Thumbnail' ) . '</label>' : null )
									.				'<div class="cb_field' . ( $input['thumbnail_method'] ? ' col-sm-offset-3' : null ) . ' col-sm-9">'
									.					$input['thumbnail_upload']
									.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally select the thumbnail file to upload.' ) )
									.					( $input['thumbnail_upload_limits'] ? '<div class="help-block">' . implode( ' ', $input['thumbnail_upload_limits'] ) . '</div>' : null )
									.				'</div>'
									.			'</div>';
			}

			if ( $input['thumbnail_link'] ) {
				$return				.=			'<div id="itemThumbnailLink" class="cbft_text cbtt_input form-group cb_form_line clearfix' . ( $input['thumbnail_method'] ? ' hidden' : null ) . '">'
									.				( ! $input['thumbnail_method'] ? '<label for="thumbnail_link" class="col-sm-3 control-label">' . CBTxt::T( 'Thumbnail' ) . '</label>' : null )
									.				'<div class="cb_field' . ( $input['thumbnail_method'] ? ' col-sm-offset-3' : null ) . ' col-sm-9">'
									.					$input['thumbnail_link']
									.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input the URL to the thumbnail file to link.' ) )
									.					( $input['thumbnail_link_limits'] ? '<div class="help-block">' . implode( ' ', $input['thumbnail_link_limits'] ) . '</div>' : null )
									.				'</div>'
									.			'</div>';
			}
		}

		$return						.=			implode( '', $_PLUGINS->trigger( 'gallery_onAfterItemEdit', array( $row, $input, $viewer, $gallery ) ) );

		if ( Application::MyUser()->isGlobalModerator() ) {
			$return					.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="user_id" class="col-sm-3 control-label">' . CBTxt::T( 'Owner' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['user_id']
									.					getFieldIcons( 1, 1, null, CBTxt::T( 'Input owner as single integer user_id.' ) )
									.				'</div>'
									.			'</div>';
		}

		if ( $gallery->get( 'items_create_captcha', false, GetterInterface::BOOLEAN ) && ( ! $canModerate ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$captcha				=	$_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( false ) );

			if ( ! empty( $captcha ) ) {
				$captcha			=	$captcha[0];

				$return				.=			'<div class="cbft_delimiter form-group cb_form_line clearfix">'
									.				'<label class="col-sm-3 control-label">' . CBTxt::T( 'Captcha' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					( isset( $captcha[0] ) ? $captcha[0] : null )
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<div class="cb_field col-sm-offset-3 col-sm-9">'
									.					str_replace( 'inputbox', 'form-control', ( isset( $captcha[1] ) ? $captcha[1] : null ) )
									.					getFieldIcons( 1, 1, null )
									.				'</div>'
									.			'</div>';
			}
		}

		$return						.=			'<div class="cbft_delimiter form-group cb_form_line clearfix">'
									.				'<div class="col-sm-offset-3 col-sm-9">'
									.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'UPDATE_TYPE', 'Update [type]', array( '[type]' => CBGallery::translateType( $type ) ) ) : ( $canUpload && $canLink ? CBTxt::T( 'Create Upload / Link' ) : ( $canUpload ? CBTxt::T( 'Create Upload' ) : CBTxt::T( 'Create Link' ) ) ) ) ) . '" class="galleryButton galleryButtonSubmit btn btn-primary" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="galleryButton galleryButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( htmlspecialchars( $returnUrl ) ) . '\'; })" />'
									.				'</div>'
									.			'</div>'
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.	'</div>';

		echo $return;
	}
}