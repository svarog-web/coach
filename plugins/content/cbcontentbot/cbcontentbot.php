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

class plgContentcbcontentbot extends JPlugin
{

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
		global $_CB_framework, $_PLUGINS;

		if ( ( $context == 'com_finder.indexer' ) || ( ! isset( $row->text ) ) ) {
			return true;
		}

		$ignore						=	$this->params->get( 'ignore_context' );

		if ( $ignore ) {
			$ignore					=	explode( ',', $ignore );

			foreach ( $ignore as $ignoreContext ) {
				if ( strpos( $context, $ignoreContext ) !== false ) {
					return true;
				}
			}
		}

		$rawText					=	preg_replace( '%{cb}(.*?){/cb}%si', '', $row->text );
		$hasRaw						=	preg_match( '/\[([\w-]+)\]/', $rawText );

		if ( ! $hasRaw ) {
			$rawTags				=	array( '[cb:userdata', '[cb:userfield', '[cb:usertab', '[cb:userposition', '[cb:date', '[cb:url', '[cb:config', '[cb:if' );

			foreach ( $rawTags as $rawTag ) {
				if ( strpos( $rawText, $rawTag ) !== false ) {
					$hasRaw	=	true;
					break;
				}
			}
		}

		if ( ( strpos( $row->text, '{cb}' ) === false ) && ( ! $hasRaw ) ) {
			return true;
		}

		static $CB_loaded			=	0;

		if ( ! $CB_loaded++ ) {
			if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
				return true;
			}

			include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

			cbimport( 'cb.html' );

			if ( $this->params->get( 'load_tpl', 0 ) ) {
				outputCbTemplate();
			}

			if ( $this->params->get( 'load_js', 0 ) ) {
				outputCbJs();
			}

			if ( $this->params->get( 'load_tooltip', 0 ) ) {
				initToolTip();
			}

			if ( $this->params->get( 'load_lang', 1 ) ) {
				cbimport( 'language.front' );
			}

			if ( $this->params->get( 'load_plgs', 0 ) ) {
				$_PLUGINS->loadPluginGroup( 'user' );
			}
		}

		$extra						=	array();

		if ( strpos( $context, 'com_content' ) !== false ) {
			if ( $this->params->get( 'user', 0 ) ) {
				$userId				=	( isset( $row->created_by ) ? (int) $row->created_by : 0 );
			} else {
				$userId				=	Application::MyUser()->getUserId();
			}

			$this->articleToArray( $row, $extra );
		} elseif ( strpos( $context, 'com_comprofiler' ) !== false ) {
			if ( $row->created_by ) {
				$userId				=	(int) $row->created_by;
			} else {
				$userId				=	Application::MyUser()->getUserId();
			}
		} else {
			$userId					=	Application::MyUser()->getUserId();
		}

		$cbUser						=	CBuser::getInstance( (int) $userId, false );
		$user						=	$cbUser->getUserData();

		static $cache				=	array();

		$cacheId					=	( isset( $row->id ) ? $row->id : $user->get( 'id' ) );

		if ( ! isset( $cache[$cacheId] ) ) {
			$css					=	$cbUser->replaceUserVars( $this->params->get( 'css' ), true, false, $extra, false );

			if ( $css ) {
				$_CB_framework->document->addHeadStyleInline( $css );
			}

			$js						=	$cbUser->replaceUserVars( $this->params->get( 'js' ), true, false, $extra, false );

			if ( $js ) {
				$_CB_framework->document->addHeadScriptDeclaration( $js );
			}

			$jQuery					=	$cbUser->replaceUserVars( $this->params->get( 'jquery' ), true, false, $extra, false );
			$jQueryPlgs				=	$this->params->get( 'jquery_plgs' );

			if ( $jQuery ) {
				if ( $jQueryPlgs ) {
					$plgs			=	explode( ',', $jQueryPlgs );
				} else {
					$plgs			=	null;
				}

				$_CB_framework->outputCbJQuery( $jQuery, $plgs );
			}

			$cache[$cacheId]		=	true;
		}

		$row->text					=	$this->substituteText( htmlspecialchars_decode( $row->text, ENT_COMPAT ), $cbUser, $extra, $hasRaw );

		return true;
	}

	/**
	 * @param string $text
	 * @param CBuser $cbUser
	 * @param array  $extra
	 * @param bool   $hasRaw
	 * @return mixed
	 */
	private function substituteText( $text, $cbUser, $extra, $hasRaw )
	{
		$ignore		=	array();
		$ignoreId	=	0;

		$text		=	preg_replace_callback( '%\[cb:ignore\](.*?)\[/cb:ignore\]%si', function( array $matches ) use ( &$ignore, &$ignoreId )
							{
								$ignoreId++;

								$ignore[$ignoreId]		=	$matches[1];

								return '[cb:ignored ' . (int) $ignoreId . ']';
							},
							$text );

		if ( strpos( $text, '{cb}' ) !== false ) {
			$text	=	preg_replace_callback( '%{cb}(.*?){/cb}%si', function( array $matches ) use ( $cbUser, $extra )
							{
								return $cbUser->replaceUserVars( $matches[1], false, true, $extra, false );
							},
							$text );
		}

		if ( $hasRaw ) {
			$text	=	$cbUser->replaceUserVars( $text, false, true, $extra, false );
		}

		foreach ( $ignore as $id => $ignored ) {
			$text	=	str_replace( '[cb:ignored ' . (int) $id . ']', $ignored, $text );
		}

		return $text;
	}

	/**
	 * @param  object $article
	 * @param  array  $extra
	 * @return array
	 */
	private function articleToArray( $article, &$extra )
	{
		if ( ! is_object( $article ) ) {
			return $extra;
		}

		if ( $article ) foreach ( $article as $k => $v ) {
			if ( ( ! is_array( $v ) ) && ( ! is_object( $v ) ) ) {
				$extra["article_$k"]	=	$v;
			}
		}

		if ( ! in_array( 'article_text', $extra ) ) {
			$text						=	null;

			if ( isset( $article->introtext ) ) {
				$text					.=	$article->introtext;
			}

			if ( isset( $article->fulltext ) ) {
				$text					.=	$article->fulltext;
			}

			$extra['article_text']		=	$text;
		}

		return $extra;
	}
}