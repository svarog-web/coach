<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CB\Database\Table\PluginTable;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class plgContentcbgallerybot extends JPlugin
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
		global $_PLUGINS;

		if ( ( $context == 'com_finder.indexer' ) || ( ! isset( $row->text ) ) ) {
			return true;
		}

		$ignore								=	$this->params->get( 'ignore_context' );

		if ( $ignore ) {
			$ignore							=	explode( ',', $ignore );

			foreach ( $ignore as $ignoreContext ) {
				if ( strpos( $context, $ignoreContext ) !== false ) {
					return true;
				}
			}
		}

		if ( strpos( $row->text, '[cbgallery:' ) === false ) {
			return true;
		}

		static $plugin						=	null;

		if ( $plugin === null ) {
			if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
				$plugin						=	false;

				return true;
			}

			include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

			cbimport( 'cb.html' );
			cbimport( 'language.front' );

			outputCbTemplate();

			$_PLUGINS->loadPluginGroup( 'user' );

			$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );

			outputCbJs();
			outputCbTemplate();
		}

		if ( ! $plugin ) {
			return true;
		}

		$row->text							=	$this->substituteText( htmlspecialchars_decode( $row->text, ENT_COMPAT ), $context, $row, $plugin );

		return true;
	}

	/**
	 * @param string      $text
	 * @param string      $context
	 * @param mixed       $row
	 * @param PluginTable $plugin
	 * @return mixed
	 */
	private function substituteText( $text, $context, $row, $plugin )
	{
		$ignore		=	array();
		$ignoreId	=	0;
		$galleryId	=	0;

		$text		=	preg_replace_callback( '%\[cbgallery:ignore\](.*?)\[/cbgallery:ignore\]%si', function( array $matches ) use ( &$ignore, &$ignoreId )
							{
								$ignoreId++;

								$ignore[$ignoreId]		=	$matches[1];

								return '[cbgallery:ignored ' . (int) $ignoreId . ']';
							},
							$text );

		$text		=	preg_replace_callback( '%\[cbgallery:gallery((?: [a-zA-Z_-]+="[^"]+")*) */\]%i', function( array $matches ) use ( &$galleryId, $context, $row, $plugin )
							{
								$galleryId++;

								$params					=	new Registry();

								if ( preg_match_all( '/(?:([a-zA-Z-_]+)="([^"]+)")+/i', $matches[1], $options, PREG_SET_ORDER ) ) {
									foreach( $options as $option ) {
										$k				=	( isset( $option[1] ) ? $option[1] : null );
										$v				=	( isset( $option[2] ) ? $option[2] : null );

										if ( $k ) {
											if ( is_numeric( $v ) ) {
												$v		=	(int) $v;
											} elseif ( $v === 'true' ) {
												$v		=	true;
											} elseif ( $v === 'false' ) {
												$v		=	false;
											}

											$params->set( $k, $v );
										}
									}
								}

								$asset					=	$params->get( 'asset', null, GetterInterface::STRING );

								if ( ( ! $asset ) && strpos( $context, 'com_content' ) !== false ) {
									$articleId			=	( isset( $row->id ) ? (int) $row->id : 0 );

									if ( $articleId ) {
										$asset			=	'article.' . $articleId;

										if ( ! $params->has( 'folders_paging' ) ) {
											$params->set( 'folders_paging', false );
										}

										if ( ! $params->has( 'folders_paging_limit' ) ) {
											$params->set( 'folders_paging_limit', 0 );
										}

										if ( ! $params->has( 'folders_search' ) ) {
											$params->set( 'folders_search', false );
										}

										if ( ! $params->has( 'items_paging' ) ) {
											$params->set( 'items_paging', false );
										}

										if ( ! $params->has( 'items_paging_limit' ) ) {
											$params->set( 'items_paging_limit', 0 );
										}

										if ( ! $params->has( 'items_search' ) ) {
											$params->set( 'items_search', false );
										}
									}
								}

								$gallery				=	new Gallery( $asset );

								$gallery->set( 'content.context', $context );
								$gallery->set( 'content.id', $galleryId );
								$gallery->set( 'location', 'current' );

								$gallery->parse( $params );

								$return					=	'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
														.		$gallery->gallery()
														.	'</div>';

								return $return;
							},
							$text );

		foreach ( $ignore as $id => $ignored ) {
			$text	=	str_replace( '[cbgallery:ignored ' . (int) $id . ']', $ignored, $text );
		}

		return $text;
	}
}