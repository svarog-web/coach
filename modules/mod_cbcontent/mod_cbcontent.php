<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CBLib\Application\Application;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_framework, $_PLUGINS;

static $CB_loaded			=	0;

if ( ! $CB_loaded++ ) {
	if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
		echo 'CB not installed'; return;
	}

	include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

	cbimport( 'cb.html' );

	if ( $params->get( 'maincbtpl', 0 ) ) {
		outputCbTemplate( 1 );
	}

	if ( $params->get( 'maincbjs', 0 ) ) {
		outputCbJs( 1 );
	}

	if ( $params->get( 'load_tooltip', 0 ) ) {
		initToolTip( 1 );
	}

	if ( $params->get( 'load_lang', 1 ) ) {
		cbimport( 'language.front' );
	}

	if ( $params->get( 'load_plgs', 0 ) ) {
		$_PLUGINS->loadPluginGroup( 'user' );
	}
}

$cbUser						=	CBuser::getInstance( (int) $_CB_framework->myId(), false );
$user						=	$cbUser->getUserData();

$moduleclass_sfx			=	$params->get( 'moduleclass_sfx' );
$prepareContent				=	$params->get( 'prepare_content', 0 );
$templateClass				=	'cb_template cb_template_' . selectTemplate( 'dir' );

static $cache				=	array();

$cacheId					=	( isset( $module->id ) ? $module->id : $user->get( 'id' ) );

if ( ! isset( $cache[$cacheId] ) ) {
	$mainCSS 				=	$params->get( 'maincss', null );

	if ( $mainCSS ) {
		if ( $prepareContent ) {
			$mainCSS		=	Application::Cms()->prepareHtmlContentPlugins( $mainCSS );
		}

		$mainCSS 			=	$cbUser->replaceUserVars( $mainCSS, true, false, null, false );

		if ( $mainCSS ) {
			$_CB_framework->document->addHeadStyleInline( $mainCSS );
		}
	}

	$mainJS 				=	$params->get( 'mainjs', null );

	if ( $mainJS ) {
		if ( $prepareContent ) {
			$mainJS			=	Application::Cms()->prepareHtmlContentPlugins( $mainJS );
		}

		$mainJS 			=	$cbUser->replaceUserVars( $mainJS, true, false, null, false );

		if ( $mainJS ) {
			$_CB_framework->document->addHeadScriptDeclaration( $mainJS );
		}
	}

	$mainJquery 			=	$params->get( 'mainjquery', null );

	if ( $mainJquery ) {
		if ( $prepareContent ) {
			$mainJquery		=	Application::Cms()->prepareHtmlContentPlugins( $mainJquery );
		}

		$mainJquery 		=	$cbUser->replaceUserVars( $mainJquery, true, false, null, false );
		$mainJqueryPlgs		=	$params->get( 'mainjquery_plgs', null );

		if ( $mainJquery ) {
			if ( $mainJqueryPlgs ) {
				$plgs		=	explode( ',', $mainJqueryPlgs );
			} else {
				$plgs		=	null;
			}

			$_CB_framework->outputCbJQuery( $mainJquery, $plgs );
		}
	}
}

$mainText 					=	$params->get( 'maintext', null );

if ( $mainText ) {
	if ( $prepareContent ) {
		$mainText			=	Application::Cms()->prepareHtmlContentPlugins( $mainText );
	}

	$module->content		=	$cbUser->replaceUserVars( $mainText, false );

	require JModuleHelper::getLayoutPath( 'mod_cbcontent', $params->get( 'layout', 'default' ) );
}
?>
