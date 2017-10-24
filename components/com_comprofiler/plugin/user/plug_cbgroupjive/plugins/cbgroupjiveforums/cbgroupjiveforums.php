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

$_PLUGINS->registerFunction( 'gj_onAdminMenu', 'adminMenu', '\CB\Plugin\GroupJiveForums\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'gj_onBeforeDisplayGroupEdit', 'editGroup', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterUpdateCategory', 'storeForum', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterCreateCategory', 'storeForum', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterUpdateGroup', 'storeForum', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterCreateGroup', 'storeForum', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterDeleteCategory', 'deleteForum', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterDeleteGroup', 'deleteForum', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterUpdateUser', 'storeModerator', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterCreateUser', 'storeModerator', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterDeleteUser', 'deleteModerator', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );
$_PLUGINS->registerFunction( 'gj_onBeforeDisplayGroup', 'showForums', '\CB\Plugin\GroupJiveForums\Trigger\ForumsTrigger' );

$_PLUGINS->registerFunction( 'kunenaIntegration', 'kunena', '\CB\Plugin\GroupJiveForums\Forum\Kunena\KunenaTrigger' );