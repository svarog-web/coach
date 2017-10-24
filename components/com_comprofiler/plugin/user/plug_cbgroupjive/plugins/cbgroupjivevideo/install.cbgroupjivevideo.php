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

function plug_cbgroupjivevideo_install()
{
	// Grab GJ params to migrate the legacy params:
	$plugin				=	new PluginTable();

	$plugin->load( array( 'element' => 'cbgroupjive' ) );

	$pluginParams		=	new Registry( $plugin->get( 'params' ) );

	if ( ( ! $pluginParams->has( 'video_captcha' ) ) || ( $pluginParams->get( 'video_captcha' ) == null ) ) {
		return;
	}

	// Migrate video integration parameters:
	$video				=	new PluginTable();

	$video->load( array( 'element' => 'cbgroupjivevideo' ) );

	$videoParams		=	new Registry( $video->get( 'params' ) );

	if ( $videoParams->get( 'migrated' ) ) {
		return;
	}

	$videoParams->set( 'groups_video_captcha', $pluginParams->get( 'video_captcha' ) );
	$videoParams->set( 'groups_video_paging', $pluginParams->get( 'video_paging' ) );
	$videoParams->set( 'groups_video_limit', $pluginParams->get( 'video_limit' ) );
	$videoParams->set( 'groups_video_search', $pluginParams->get( 'video_search' ) );
	$videoParams->set( 'migrated', true );

	$video->set( 'params', $videoParams->asJson() );

	$video->store();
}