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

function plug_cbgroupjivewall_install()
{
	// Grab GJ params to migrate the legacy params:
	$plugin				=	new PluginTable();

	$plugin->load( array( 'element' => 'cbgroupjive' ) );

	$pluginParams		=	new Registry( $plugin->get( 'params' ) );

	if ( ( ! $pluginParams->has( 'wall_inputlimit' ) ) || ( $pluginParams->get( 'wall_inputlimit' ) == null ) ) {
		return;
	}

	// Migrate wall integration parameters:
	$wall				=	new PluginTable();

	$wall->load( array( 'element' => 'cbgroupjivewall' ) );

	$wallParams			=	new Registry( $wall->get( 'params' ) );

	if ( $wallParams->get( 'migrated' ) ) {
		return;
	}

	$wallParams->set( 'groups_wall_character_limit', $pluginParams->get( 'wall_inputlimit' ) );
	$wallParams->set( 'groups_wall_replies', $pluginParams->get( 'wall_reply' ) );
	$wallParams->set( 'groups_wall_replies_paging', $pluginParams->get( 'wall_replypaging' ) );
	$wallParams->set( 'groups_wall_replies_limit', $pluginParams->get( 'wall_replylimit' ) );
	$wallParams->set( 'groups_wall_paging', $pluginParams->get( 'wall_paging' ) );
	$wallParams->set( 'groups_wall_limit', $pluginParams->get( 'wall_limit' ) );
	$wallParams->set( 'migrated', true );

	$wall->set( 'params', $wallParams->asJson() );

	$wall->store();
}