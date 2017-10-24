<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\PluginTable;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

function plug_cbgroupjivephoto_install()
{
	// Grab GJ params to migrate the legacy params:
	$plugin				=	new PluginTable();

	$plugin->load( array( 'element' => 'cbgroupjive' ) );

	$pluginParams		=	new Registry( $plugin->get( 'params' ) );

	if ( ( ! $pluginParams->has( 'photo_captcha' ) ) || ( $pluginParams->get( 'photo_captcha' ) == null ) ) {
		return;
	}

	// Migrate photo integration parameters:
	$photo				=	new PluginTable();

	$photo->load( array( 'element' => 'cbgroupjivephoto' ) );

	$photoParams		=	new Registry( $photo->get( 'params' ) );

	if ( $photoParams->get( 'migrated' ) ) {
		return;
	}

	$photoParams->set( 'groups_photo_captcha', $pluginParams->get( 'photo_captcha' ) );
	$photoParams->set( 'groups_photo_paging', $pluginParams->get( 'photo_paging' ) );
	$photoParams->set( 'groups_photo_limit', $pluginParams->get( 'photo_limit' ) );
	$photoParams->set( 'groups_photo_search', $pluginParams->get( 'photo_search' ) );
	$photoParams->set( 'migrated', true );

	$photo->set( 'params', $photoParams->asJson() );

	$photo->store();
}