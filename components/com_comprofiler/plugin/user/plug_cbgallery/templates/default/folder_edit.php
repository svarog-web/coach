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
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Gallery\Table\FolderTable;
use CB\Plugin\Gallery\CBGallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryFolderEdit
{

	/**
	 * @param FolderTable      $row
	 * @param array            $items
	 * @param cbPageNav        $itemsPageNav
	 * @param array            $input
	 * @param UserTable        $viewer
	 * @param Gallery          $gallery
	 * @param CBplug_cbgallery $plugin
	 */
	static public function showFolderEdit( $row, $items, $itemsPageNav, $input, $viewer, $gallery, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$integrations				=	$_PLUGINS->trigger( 'gallery_onBeforeFolderEdit', array( &$row, &$input, $viewer, $gallery ) );

		initToolTip();
		cbValidator::loadValidation();

		$canModerate				=	CBGallery::canModerate( $gallery );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			$pageTitle				=	CBTxt::T( 'EDIT_ALBUM_NAME', 'Edit Album: [name]', array( '[name]' => ( $row->get( 'title', null, GetterInterface::STRING ) ? $row->get( 'title', null, GetterInterface::STRING ) : cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'GALLERY_LONG_DATE_FORMAT', 'F j, Y' ) ) ) ) );
		} else {
			$pageTitle				=	CBTxt::T( 'New Album' );
		}

		$returnUrl					=	CBGallery::getReturn( true, true );

		if ( ! $returnUrl ) {
			$returnUrl				=	$gallery->location();
		}

		if ( $pageTitle ) {
			$_CB_framework->setPageTitle( $pageTitle );
		}

		$return						=	'<div class="galleryFolderEdit">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'folder', 'func' => 'save', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id(), 'return' => CBGallery::getReturn( true ) ) ) . '" method="post" enctype="multipart/form-data" name="galleryFolderForm" id="galleryFolderForm" class="galleryFolderForm cb_form form-auto cbValidation">'
									.			( $pageTitle ? '<div class="galleryFolderTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
									.			implode( '', $integrations );

		if ( $canModerate || ( ! $gallery->get( 'folders_create_approval', false, GetterInterface::BOOLEAN ) ) || ( $row->get( 'id', 0, GetterInterface::INT ) && ( $row->get( 'published', 0, GetterInterface::INT ) != -1 ) ) ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="published" class="col-sm-3 control-label">' . CBTxt::T( 'Published' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['published']
									.					getFieldIcons( 1, 0, null, CBTxt::T( 'Select publish status of the album. If unpublished the album will not be visible to the public.' ) )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="title" class="col-sm-3 control-label">' . CBTxt::T( 'Title' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['title']
									.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input a title. If no title is provided the date will be displayed as the title.' ) )
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
									.				'<label for="description" class="col-sm-3 control-label">' . CBTxt::T( 'Description' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['description']
									.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input a description.' ) )
									.				'</div>'
									.			'</div>';

		$return						.=			implode( '', $_PLUGINS->trigger( 'gallery_onAfterFolderEdit', array( $row, $input, $viewer, $gallery ) ) );

		if ( Application::MyUser()->isGlobalModerator() ) {
			$return					.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="user_id" class="col-sm-3 control-label">' . CBTxt::T( 'Owner' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['user_id']
									.					getFieldIcons( 1, 1, null, CBTxt::T( 'Input owner as single integer user_id.' ) )
									.				'</div>'
									.			'</div>';
		}

		if ( $gallery->get( 'folders_create_captcha', false, GetterInterface::BOOLEAN ) && ( ! $canModerate ) ) {
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
									.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'Update Album' ) : CBTxt::T( 'Create Album' ) ) ) . '" class="galleryButton galleryButtonSubmit btn btn-primary" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="galleryButton galleryButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( htmlspecialchars( $returnUrl ) ) . '\'; })" />'
									.				'</div>'
									.			'</div>'
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>';

		if ( $items ) {
			$return					.=		'<hr />'
									.		'<div class="galleryFolderEditItems">'
									.			implode( '', $items );

			if ( $gallery->get( 'items_paging', true, GetterInterface::BOOLEAN ) && ( $itemsPageNav->total > $itemsPageNav->limit ) ) {
				$return				.=			'<div class="galleryFolderEditItemsPaging text-center">'
									.				$itemsPageNav->getListLinks()
									.			'</div>';
			}

			$return					.=		'</div>';
		}

		$return						.=	'</div>';

		echo $return;
	}
}