<?php
/**
* Joomla Community Builder Module: mod_cbpblatest
* @version $Id: mod_cbpblatest.php 2656 2012-10-25 16:40:49Z kyle $
* @package mod_cbpblatest
* @subpackage mod_cbpblatest.php
* @author Beat
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_framework, $_CB_database, $ueConfig, $mainframe;

if ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) {
	echo 'CB not installed!';
	return;
}
include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );


if ( ! function_exists( 'cbpblatest_plugin' ) ) {
	/**
	* returns CB plugin object of profilebook
	*
	* @return mixed
	*/
	function cbpblatest_plugin() {
		global $_PLUGINS;

		static $cache	=	null;

		if ( ! isset( $cache ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cb.profilebook' );

			if ( $plugin ) {
				$cache	=	$plugin;
			}
		}

		return $cache;
	}
}

if ( ! function_exists( 'cbpblatest_pluginclass' ) ) {
	/**
	 * returns CB class object of profilebook
	 *
	 * @param  string  $class  Class name
	 * @return mixed
	 */
	function cbpblatest_pluginclass( $class = 'getprofilebookTab' ) {
		global $_PLUGINS;

		static $cache				=	array();

		if ( ! isset( $cache[$class] ) ) {
			$cache[$class]			=	null;

			$plugin					=	cbpblatest_plugin();

			if ( $plugin ) {
				$pluginclass		=	$_PLUGINS->getInstanceOfPluginClass( $class, $plugin->id );

				if ( $pluginclass ) {
					$pluginclass->_loadParams( $plugin->id );

					if ( method_exists( $pluginclass, 'getPbConfig' ) ) {
						$pluginclass->getPbConfig();
					}

					$cache[$class]	=	$pluginclass;
				}
			}
		}

		return $cache[$class];
	}
}

if ( ! class_exists( 'cbpblatest_replacer' ) ) {
	/**
	 * Parses posts content with substitution parser replaceUserVars
	 */
	class cbpblatest_replacer {
		/**
		 * Poster should be used to store posters
		 * @var cbUser
		 */
		var $poster		=	null;
		/**
		 * User should be used to store recipients
		 * @var cbUser
		 */
		var $user		=	null;
		/**
		 * Entry should be used to store entries substitution array
		 * @var array
		 */
		var $entry		=	null;
		/**
		 * Type should be used to store the entries mode
		 * @var string
		 */
		var $type		=	null;

		/**
		 * Replace posts content with posters substitution data
		 *
		 * @param string $matches
		 * @return string
		 */
		public function replace_poster( $matches ) {
			return $this->poster->replaceUserVars( $matches[1] );
		}

		/**
		 * Replace posts content with recipient substitution data
		 *
		 * @param string $matches
		 * @return string
		 */
		public function replace_user( $matches ) {
			return $this->user->replaceUserVars( $matches[1] );
		}

		/**
		 * Replace posts content with posts substitution data
		 *
		 * @param string $matches
		 * @return string
		 */
		public function replace_entry( $matches ) {
			$entry			=	$matches[1];

			foreach ( $this->entry as $k => $v ) {
				$entry		=	str_replace( $k, $v, $entry );
			}

			switch ( $this->type ) {
				case 'g': // Guestbook Mode
					$class	=	'getprofilebookTab';
					break;
				case 'b': // Blog Mode
					$class	=	'getprofilebookblogTab';
					break;
				case 'w': // Wall Mode
					$class	=	'getprofilebookwallTab';
					break;
				default: // Unknown type
					$class	=	'getprofilebookTab';
					break;
			}

			$tabclass		=	cbpblatest_pluginclass( $class );

			if ( $tabclass ) {
				$entry		=	$tabclass->parseBBCode( $entry );
			}

			return $entry;
		}
	}
}

$class_sfx							=	$params->get( 'moduleclass_sfx', null );
$mode								=	$params->get( 'pblatest_mode', 'b' );
$connections						=	(int) $params->get( 'pblatest_connections', 0 );
$limit								=	(int) $params->get( 'pblatest_limit', 5 );
$include							=	$params->get( 'pblatest_include', null );
$exclude							=	$params->get( 'pblatest_exclude', null );
$guestbook_user						=	$params->get( 'pblatest_guestbook_user', null );
$guestbook_self						=	$params->get( 'pblatest_guestbook_self', null );
$blog_user							=	$params->get( 'pblatest_blog_user', null );
$blog_self							=	$params->get( 'pblatest_blog_self', null );
$wall_user							=	$params->get( 'pblatest_wall_user', null );
$wall_self							=	$params->get( 'pblatest_wall_self', null );

if ( $include ) {
	$include						=	explode( ',', $exclude );

	cbArrayToInts( $include );

	$include						=	implode( ',', $exclude );
}

if ( $exclude ) {
	$exclude						=	explode( ',', $exclude );

	cbArrayToInts( $exclude );

	$exclude						=	implode( ',', $exclude );
}

$query								=	'SELECT DISTINCT a.*'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' ) . " AS a";

if ( $connections ) switch ( $connections ) {
	case '1': // Posts By
		$query						.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_members' ) . " AS b"
									.	' ON a.' . $_CB_database->NameQuote( 'posterid' ) . ' = b.' . $_CB_database->NameQuote( 'memberid' );
		break;
	case '2': // Posts To
		$query						.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_members' ) . " AS b"
									.	' ON a.' . $_CB_database->NameQuote( 'userid' ) . ' = b.' . $_CB_database->NameQuote( 'memberid' );
		break;
	case '3': // Posts By and To
		$query						.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_members' ) . " AS b"
									.	' ON ( a.' . $_CB_database->NameQuote( 'posterid' ) . ' = b.' . $_CB_database->NameQuote( 'memberid' )
									.	' OR a.' . $_CB_database->NameQuote( 'userid' ) . ' = b.' . $_CB_database->NameQuote( 'memberid' ) . ' )';
		break;
}

$query								.=	"\n WHERE a." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	( $mode != 'a' ? "\n AND a." . $_CB_database->NameQuote( 'mode' ) . " = " . $_CB_database->Quote( $mode ) : null )
									.	( $include ? "\n AND a." . $_CB_database->NameQuote( 'userid' ) . " IN ( " . $_CB_database->Quote( $include ) . " )" : null )
									.	( $exclude ? "\n AND a." . $_CB_database->NameQuote( 'userid' ) . " NOT IN ( " . $_CB_database->Quote( $exclude ) . " )" : null )
									.	( $connections ? "\n AND b." . $_CB_database->NameQuote( 'referenceid' ) . " = " . (int) Application::MyUser()->getUserId() : null )
									.	"\n ORDER BY a." . $_CB_database->NameQuote( 'date' ) . " DESC"
									.	( $limit ? "\n LIMIT " . (int) $limit : null );
$_CB_database->setQuery( $query );
$entries							=	$_CB_database->loadObjectList();

$return								=	null;

if ( $entries ) foreach ( $entries as $e ) {
	// Poster Object:
	$cbPoster						=&	CBuser::getInstance( $e->posterid );

	if ( ! $cbPoster ) {
		$cbPoster					=&	CBuser::getInstance( null );
	}

	$cbPosterTabs					=	$cbPoster->_getCbTabs( false );
	$cbPosterUser					=	$cbPoster->getUserData();
	$cbPosterTabsList				=	$cbPosterTabs->_getTabsDb( $cbPosterUser, 'profile' );
	$cbPosterTabClasses				=	array();

	if ( $cbPosterTabsList ) foreach ( $cbPosterTabsList as $k => $v ) {
		if ( isset( $v->pluginclass ) && ( ! in_array( $v->pluginclass, $cbPosterTabClasses ) ) ) {
			$cbPosterTabClasses[]	=	$v->pluginclass;
		}
	}

	switch ( $e->mode ) {
		case 'g': // Guestbook Mode
			if ( $e->posterid == $e->userid ) {
				$entry_url			=	$_CB_framework->userProfileUrl( $e->posterid, false, 'getprofilebooktab' );
				$entry_text			=	( $guestbook_self ? $guestbook_self : CBTxt::T( '{p[cb:userfield field="formatname" reason="list" /]} added a new <a href="{e[url]}">guestbook entry</a> - {e[date]}' ) );
			} else {
				$entry_url			=	$_CB_framework->userProfileUrl( $e->userid, false, 'getprofilebooktab' );
				$entry_text			=	( $guestbook_user ? $guestbook_user : CBTxt::T( '{p[cb:userfield field="formatname" reason="list" /]} added a new <a href="{e[url]}">guestbook entry</a> to {r[cb:userfield field="formatname" reason="list" /]} - {e[date]}' ) );
			}

			$access					=	( in_array( 'getprofilebookTab', $cbPosterTabClasses ) ? true : false );
			break;
		case 'b': // Blog Mode
			if ( $e->posterid == $e->userid ) {
				$entry_url			=	$_CB_framework->userProfileUrl( $e->posterid, false, 'getprofilebookblogtab' );
				$entry_text			=	( $blog_self ? $blog_self : CBTxt::T( '{p[cb:userfield field="formatname" reason="list" /]} wrote a new blog "<a href="{e[url]}">{e[title]}</a>" - {e[date]}' ) );
			} else {
				$entry_url			=	$_CB_framework->userProfileUrl( $e->userid, false, 'getprofilebookblogtab' );
				$entry_text			=	( $blog_user ? $blog_user : CBTxt::T( '{p[cb:userfield field="formatname" reason="list" /]} wrote a new blog "<a href="{e[url]}">{e[title]}</a>" to {r[cb:userfield field="formatname" reason="list" /]} - {e[date]}' ) );
			}

			$access					=	( in_array( 'getprofilebookblogTab', $cbPosterTabClasses ) ? true : false );
			break;
		case 'w': // Wall Mode
			if ( $e->posterid == $e->userid ) {
				$entry_url			=	$_CB_framework->userProfileUrl( $e->posterid, false, 'getprofilebookwalltab' );
				$entry_text			=	( $wall_self ? $wall_self : CBTxt::T( '{p[cb:userfield field="formatname" reason="list" /]} added a new <a href="{e[url]}">wall entry</a> - {e[date]}' ) );
			} else {
				$entry_url			=	$_CB_framework->userProfileUrl( $e->userid, false, 'getprofilebookwalltab' );
				$entry_text			=	( $wall_user ? $wall_user : CBTxt::T( '{p[cb:userfield field="formatname" reason="list" /]} added a new <a href="{e[url]}">wall entry</a> to {r[cb:userfield field="formatname" reason="list" /]} - {e[date]}' ) );
			}

			$access					=	( in_array( 'getprofilebookwallTab', $cbPosterTabClasses ) ? true : false );
			break;
		default: // Unknown type
			if ( $e->posterid == $e->userid ) {
				$entry_url			=	$_CB_framework->userProfileUrl( $e->posterid, false, 'getprofilebookwalltab' );
				$entry_text			=	CBTxt::T( '{p[cb:userfield field="formatname" reason="list" /]} added a new <a href="{e[url]}">entry</a> - {e[date]}' );
			} else {
				$entry_url			=	$_CB_framework->userProfileUrl( $e->userid, false, 'getprofilebookwalltab' );
				$entry_text			=	CBTxt::T( '{p[cb:userfield field="formatname" reason="list" /]} added a new <a href="{e[url]}">entry</a> to {r[cb:userfield field="formatname" reason="list" /]} - {e[date]}' );
			}

			$access					=	( in_array( 'getprofilebookTab', $cbPosterTabClasses ) ? true : false );
			break;
	}

	if ( $access ) {
		// User Object:
		$cbUser						=&	CBuser::getInstance( $e->userid );

		if ( ! $cbUser ) {
			$cbUser					=&	CBuser::getInstance( null );
		}

		// Replacer Object:
		$replacer					=	new cbpblatest_replacer();
		$replacer->poster			=	$cbPoster;
		$replacer->user				=	$cbUser;
		$replacer->entry			=	array( '[title]' => htmlspecialchars( $e->postertitle ), '[post]' => htmlspecialchars( $e->postercomment ), '[date]' => cbFormatDate( $e->date, false ), '[url]' => $entry_url );
		$replacer->type				=	$e->mode;

		// Replacements
		$entry_text					=	preg_replace_callback( '/\{p(.*?)\}/s', array( &$replacer, 'replace_poster' ), $entry_text );
		$entry_text					=	preg_replace_callback( '/\{r(.*?)\}/s', array( &$replacer, 'replace_user' ), $entry_text );
		$entry_text					=	preg_replace_callback( '/\{e(.*?)\}/s', array( &$replacer, 'replace_entry' ), $entry_text );

		// Entry
		$return						.=		'<li>' . $entry_text . '</li>';
	}
}

if ( ! $return ) {
	$return							=	CBTxt::T( 'No entries have been made!' );
} else {
	$return							=	'<ul id="mod_cbpblatest_' . $mode . $class_sfx . '">' . $return . '</ul>';
}

echo $return;
