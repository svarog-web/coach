<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Activity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_framework, $ueConfig, $_PLUGINS;

if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
	echo 'CB not installed'; return;
}

include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

cbimport( 'cb.html' );
cbimport( 'language.front' );

$_PLUGINS->loadPluginGroup( 'user' );

if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' ) ) {
	echo 'CB Activity not installed'; return;
}

outputCbJs();
outputCbTemplate();

$templateClass		=	'cb_template cb_template_' . selectTemplate( 'dir' );

$activity			=	new Activity( 'recent' );

CBActivity::loadStreamDefaults( $activity, $params, 'activity_' );

require JModuleHelper::getLayoutPath( 'mod_cbactivity', $params->get( 'layout', 'default' ) );