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

$_PLUGINS->registerFunction( 'onBuildRoute', 'build', '\CB\Plugin\GroupJiveAbout\RouterTrigger' );
$_PLUGINS->registerFunction( 'onParseRoute', 'parse', '\CB\Plugin\GroupJiveAbout\RouterTrigger' );

$_PLUGINS->registerFunction( 'gj_onBeforeDisplayGroup', 'showAbout', '\CB\Plugin\GroupJiveAbout\AboutTrigger' );