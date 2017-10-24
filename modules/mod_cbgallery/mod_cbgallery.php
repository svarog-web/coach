<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_framework, $ueConfig, $_PLUGINS;

static $error		=	null;

if ( $error === null ) {
	if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
		$error		=	'CB not installed';

		echo $error;
		return;
	}

	include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

	cbimport( 'cb.html' );
	cbimport( 'language.front' );

	$_PLUGINS->loadPluginGroup( 'user' );

	if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' ) ) {
		$error		=	'CB Gallery not installed';

		echo $error;
		return;
	}

	outputCbJs();
	outputCbTemplate();

	$error			=	false;
}

if ( $error ) {
	echo $error;
	return;
}

$templateClass		=	'cb_template cb_template_' . selectTemplate( 'dir' );

$gallery			=	new Gallery( $params->get( 'gallery_asset' ) );

$gallery->set( 'module', (int) $module->id );
$gallery->set( 'location', 'current' );

$folder				=	(int) $params->get( 'gallery_folder' );

if ( $folder ) {
	$gallery->set( 'folder', $folder );
}

$gallery->parse( $params->toArray(), 'gallery_' );

if ( ( ! $gallery->folders( true ) ) && ( ! $gallery->items( true ) ) && ( ! CBGallery::canCreateFolders( $gallery ) ) && ( ! CBGallery::canCreateItems( 'all', 'both', $gallery ) ) ) {
	return;
}

require JModuleHelper::getLayoutPath( 'mod_cbgallery', $params->get( 'layout', 'default' ) );