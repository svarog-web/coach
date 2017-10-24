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

function plug_cbgroupjivefile_install()
{
	// Grab GJ params to migrate the legacy params:
	$plugin				=	new PluginTable();

	$plugin->load( array( 'element' => 'cbgroupjive' ) );

	$pluginParams		=	new Registry( $plugin->get( 'params' ) );

	if ( ( ! $pluginParams->has( 'file_captcha' ) ) || ( $pluginParams->get( 'file_captcha' ) == null ) ) {
		return;
	}

	// Migrate file integration parameters:
	$file				=	new PluginTable();

	$file->load( array( 'element' => 'cbgroupjivefile' ) );

	$fileParams			=	new Registry( $file->get( 'params' ) );

	if ( $fileParams->get( 'migrated' ) ) {
		return;
	}

	$fileParams->set( 'groups_file_captcha', $pluginParams->get( 'file_captcha' ) );
	$fileParams->set( 'groups_file_max_size', $pluginParams->get( 'file_maxsize' ) );
	$fileParams->set( 'groups_file_extensions', $pluginParams->get( 'file_types' ) );
	$fileParams->set( 'groups_file_paging', $pluginParams->get( 'file_paging' ) );
	$fileParams->set( 'groups_file_limit', $pluginParams->get( 'file_limit' ) );
	$fileParams->set( 'groups_file_search', $pluginParams->get( 'file_search' ) );
	$fileParams->set( 'migrated', true );

	$file->set( 'params', $fileParams->asJson() );

	$file->store();
}