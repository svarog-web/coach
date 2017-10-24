<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @author Trail, Nant (modified for CB 2.0)
 * @copyright (C)2005-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cblastviewsClass
{

	/**
	 * @param null|array $files
	 * @param bool       $loadGlobal
	 * @param bool       $loadHeader
	 */
	static public function getTemplate( $files = null, $loadGlobal = true, $loadHeader = true )
	{
		global $_CB_framework, $_PLUGINS;

		static $tmpl							=	array();

		if ( ! $files ) {
			$files								=	array();
		} elseif ( ! is_array( $files ) ) {
			$files								=	array( $files );
		}

		$id										=	md5( serialize( array( $files, $loadGlobal, $loadHeader ) ) );

		if ( ! isset( $tmpl[$id] ) ) {
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cb.lastviews' );

			if ( ! $plugin ) {
				return;
			}

			$livePath							=	$_PLUGINS->getPluginLivePath( $plugin );
			$absPath							=	$_PLUGINS->getPluginPath( $plugin );
			$params								=	$_PLUGINS->getPluginParams( $plugin );

			$template							=	$params->get( 'general_template', 'default' );
			$paths								=	array( 'global_css' => null, 'php' => null, 'css' => null, 'js' => null, 'override_css' => null );

			foreach ( $files as $file ) {
				$file							=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $file );
				$globalCss						=	'/templates/' . $template . '/template.css';
				$overrideCss					=	'/templates/' . $template . '/override.css';

				if ( $file ) {
					$php						=	$absPath . '/templates/' . $template . '/' . $file . '.php';
					$css						=	'/templates/' . $template . '/' . $file . '.css';
					$js							=	'/templates/' . $template . '/' . $file . '.js';
				} else {
					$php						=	null;
					$css						=	null;
					$js							=	null;
				}

				if ( $loadGlobal && $loadHeader ) {
					if ( ! file_exists( $absPath . $globalCss ) ) {
						$globalCss				=	'/templates/default/template.css';
					}

					if ( file_exists( $absPath . $globalCss ) ) {
						$_CB_framework->document->addHeadStyleSheet( $livePath . $globalCss );

						$paths['global_css']	=	$livePath . $globalCss;
					}
				}

				if ( $file ) {
					if ( ! file_exists( $php ) ) {
						$php					=	$absPath . '/templates/default/' . $file . '.php';
					}

					if ( file_exists( $php ) ) {
						require_once( $php );

						$paths['php']			=	$php;
					}

					if ( $loadHeader ) {
						if ( ! file_exists( $absPath . $css ) ) {
							$css				=	'/templates/default/' . $file . '.css';
						}

						if ( file_exists( $absPath . $css ) ) {
							$_CB_framework->document->addHeadStyleSheet( $livePath . $css );

							$paths['css']		=	$livePath . $css;
						}

						if ( ! file_exists( $absPath . $js ) ) {
							$js					=	'/templates/default/' . $file . '.js';
						}

						if ( file_exists( $absPath . $js ) ) {
							$_CB_framework->document->addHeadScriptUrl( $livePath . $js );

							$paths['js']		=	$livePath . $js;
						}
					}
				}

				if ( $loadGlobal && $loadHeader ) {
					if ( file_exists( $absPath . $overrideCss ) ) {
						$_CB_framework->document->addHeadStyleSheet( $livePath . $overrideCss );

						$paths['override_css']	=	$livePath . $overrideCss;
					}
				}
			}

			$tmpl[$id]							=	$paths;
		}
	}
}

class getLastViewsTab extends cbTabHandler
{

	/**
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_database;

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params	=	new Registry( $tab->params );
		}

		$viewer				=	CBuser::getMyUserDataInstance();

		outputCbJs( 1 );
		outputCbTemplate( 1 );
		cbimport( 'cb.pagination' );

		cblastviewsClass::getTemplate( 'tab' );

		$exclude			=	$tab->params->get( 'display_exclude', '42' );
		$displayLimit		=	(int) $tab->params->get( 'display_limit', 15 );

		if ( ! $displayLimit ) {
			$displayLimit	=	15;
		}

		$query				=	'SELECT a.*'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_views' ) . " AS a"
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS b"
							.	' ON b.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'viewer_id' )
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS c"
							.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = b.' . $_CB_database->NameQuote( 'id' )
							.	"\n WHERE a." . $_CB_database->NameQuote( 'profile_id' ) . " = " . (int) $user->get( 'id' )
							.	"\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " > 0"
							.	( $exclude ? "\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( explode( ',', $exclude ) ) : null )
							.	"\n AND b." . $_CB_database->NameQuote( 'approved' ) . " = 1"
							.	"\n AND b." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
							.	"\n AND c." . $_CB_database->NameQuote( 'block' ) . " = 0"
							.	"\n ORDER BY " . $_CB_database->NameQuote( 'lastview' ) . " DESC";
		$_CB_database->setQuery( $query, 0, $displayLimit );
		$rows				=	$_CB_database->loadObjectList( null, '\CB\Database\Table\UserViewTable', array( $_CB_database ) );

		if ( $tab->params->get( 'display_total_views', 1 ) ) {
			$query			=	'SELECT COUNT(*)'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_views' ) . " AS a"
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS b"
							.	' ON b.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'viewer_id' )
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS c"
							.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = b.' . $_CB_database->NameQuote( 'id' )
							.	"\n WHERE a." . $_CB_database->NameQuote( 'profile_id' ) . " = " . (int) $user->get( 'id' )
							.	"\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " > 0"
							.	( $exclude ? "\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( explode( ',', $exclude ) ) : null )
							.	"\n AND b." . $_CB_database->NameQuote( 'approved' ) . " = 1"
							.	"\n AND b." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
							.	"\n AND c." . $_CB_database->NameQuote( 'block' ) . " = 0";
			$_CB_database->setQuery( $query );
			$viewsCount		=	(int) $_CB_database->loadResult();
		} else {
			$viewsCount		=	0;
		}

		if ( $tab->params->get( 'display_guest_views', 1 ) ) {
			$query			=	'SELECT COUNT(*)'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_views' ) . " AS a"
							.	"\n WHERE a." . $_CB_database->NameQuote( 'profile_id' ) . " = " . (int) $user->get( 'id' )
							.	"\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " = 0";
			$_CB_database->setQuery( $query );
			$guestCount		=	(int) $_CB_database->loadResult();
		} else {
			$guestCount		=	0;
		}

		if ( ( ! $rows ) && ( ! $viewsCount ) && ( ! $guestCount ) ) {
			return null;
		}

		$class				=	$this->params->get( 'general_class', null );

		$return				=	'<div id="cbLastViews" class="cbLastViews' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
							.		'<div id="cbLastViewsInner" class="cbLastViewsInner">'
							.			HTML_cblastviewsTab::showViews( $rows, $viewsCount, $guestCount, $viewer, $user, $tab, $this )
							.		'</div>'
							.	'</div>';

		return $return;
	}
}