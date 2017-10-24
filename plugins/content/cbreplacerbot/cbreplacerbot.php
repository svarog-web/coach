<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class plgContentcbreplacerbot extends JPlugin
{

	/**
	 * Loads in CB API
	 *
	 * @return bool
	 */
	private function loadCB()
	{
		static $CB_loaded	=	0;

		if ( ! $CB_loaded++ ) {
			if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
				return false;
			}

			include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

			cbimport( 'cb.html' );
			cbimport( 'language.front' );
		}

		return true;
	}

	/**
	 * @param string $context The context of the content being passed to the plugin.
	 * @param mixed  &$row    An object with a "text" property
	 * @param mixed  $params  Additional parameters. See {@see PlgContentContent()}.
	 * @param int    $page    Optional page number. Unused. Defaults to zero.
	 *
	 * @return bool
	 */
	public function onContentPrepare( $context, &$row, &$params, $page = 0 )
	{
		if ( ( $context == 'com_finder.indexer' ) || ( ! isset( $row->text ) ) ) {
			return true;
		}

		$replacers					=	$this->params->get( 'replacers' );

		if ( ! $replacers ) {
			return true;
		}

		$replacers					=	json_decode( $replacers, true );

		if ( ( ! $replacers ) || ( ! isset( $replacers['from'] ) ) ) {
			return true;
		}

		// REGEXP:
		$regexpFrom					=	array();
		$regexpTo					=	array();

		// Non-Case Sensitive:
		$stringFrom					=	array();
		$stringeTo					=	array();

		// Case Sensitive:
		$caseFrom					=	array();
		$caseTo						=	array();

		foreach ( $replacers['from'] as $k => $from ) {
			if ( ! $from ) {
				continue;
			}

			$to						=	( isset( $replacers['to'][$k] ) ? $replacers['to'][$k] : '' );
			$regexp					=	( isset( $replacers['regexp'][$k] ) ? (int) $replacers['regexp'][$k] : 0 );
			$translate				=	( isset( $replacers['translate'][$k] ) ? (int) $replacers['translate'][$k] : 0 );
			$substitutions			=	( isset( $replacers['substitutions'][$k] ) ? (int) $replacers['substitutions'][$k] : 0 );
			$caseSensitive			=	( isset( $replacers['casesensitive'][$k] ) ? (int) $replacers['casesensitive'][$k] : 0 );

			if ( $translate ) {
				if ( ! $this->loadCB() ) {
					continue;
				}

				$from				=	CBTxt::T( $from );
				$to					=	CBTxt::T( $to );
			}

			if ( $substitutions ) {
				if ( ! $this->loadCB() ) {
					continue;
				}

				$to					=	CBuser::getMyInstance()->replaceUserVars( $to, false, false, null, false );
			}

			if ( $regexp ) {
				$regexpFrom[]		=	$from;
				$regexpTo[]			=	$to;
			} else {
				if ( $caseSensitive ) {
					$caseFrom[]		=	$from;
					$caseTo[]		=	$to;
				} else {
					$stringFrom[]	=	$from;
					$stringeTo[]	=	$to;
				}
			}
		}

		if ( $regexpFrom ) {
			$row->text				=	preg_replace( $regexpFrom, $regexpTo, $row->text );
		}

		if ( $stringFrom ) {
			$row->text				=	str_ireplace( $stringFrom, $stringeTo, $row->text );
		}

		if ( $caseFrom ) {
			$row->text				=	str_replace( $caseFrom, $caseTo, $row->text );
		}

		return true;
	}
}