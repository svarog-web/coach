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

function plug_cbgroupjiveabout_install()
{
	// Grab GJ params to migrate the legacy params:
	$plugin				=	new PluginTable();

	$plugin->load( array( 'element' => 'cbgroupjive' ) );

	$pluginParams		=	new Registry( $plugin->get( 'params' ) );

	if ( ( ! $pluginParams->has( 'about_content' ) ) || ( $pluginParams->get( 'about_content' ) == null ) ) {
		return;
	}

	// Migrate about integration parameters:
	$about				=	new PluginTable();

	$about->load( array( 'element' => 'cbgroupjiveabout' ) );

	$aboutParams		=	new Registry( $about->get( 'params' ) );

	if ( $aboutParams->get( 'migrated' ) ) {
		return;
	}

	$aboutParams->set( 'groups_about_content_plugins', $pluginParams->get( 'about_content' ) );
	$aboutParams->set( 'migrated', true );

	$about->set( 'params', $aboutParams->asJson() );

	$about->store();
}