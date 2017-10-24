<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'gj_onAdminMenu', 'adminMenu', '\CB\Plugin\GroupJiveWall\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'onBuildRoute', 'build', '\CB\Plugin\GroupJiveWall\Trigger\RouterTrigger' );
$_PLUGINS->registerFunction( 'onParseRoute', 'parse', '\CB\Plugin\GroupJiveWall\Trigger\RouterTrigger' );

$_PLUGINS->registerFunction( 'gj_onBeforeDisplayGroupEdit', 'editGroup', '\CB\Plugin\GroupJiveWall\Trigger\WallTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterDeleteGroup', 'deleteGroup', '\CB\Plugin\GroupJiveWall\Trigger\WallTrigger' );
$_PLUGINS->registerFunction( 'gj_onBeforeDisplayNotifications', 'editNotifications', '\CB\Plugin\GroupJiveWall\Trigger\WallTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterCreateUser', 'storeNotifications', '\CB\Plugin\GroupJiveWall\Trigger\WallTrigger' );
$_PLUGINS->registerFunction( 'gj_onBeforeDisplayGroup', 'showWall', '\CB\Plugin\GroupJiveWall\Trigger\WallTrigger' );

$_PLUGINS->registerFunction( 'activity_onQueryActivity', 'activityQuery', '\CB\Plugin\GroupJiveWall\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onBeforeDisplayActivity', 'activityPrefetch', '\CB\Plugin\GroupJiveWall\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayActivity', 'activityDisplay', '\CB\Plugin\GroupJiveWall\Trigger\ActivityTrigger' );