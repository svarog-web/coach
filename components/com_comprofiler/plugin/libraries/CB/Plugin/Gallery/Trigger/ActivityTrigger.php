<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery\Trigger;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;

defined('CBLIB') or die();

class ActivityTrigger extends \cbPluginHandler
{

	/**
	 * @param int $profileId
	 * @return Gallery|null
	 */
	public function activityGallery( $profileId )
	{
		if ( ! $profileId ) {
			return null;
		}

		static $galleries				=	array();

		if ( ! isset( $galleries[$profileId] ) ) {
			$tab						=	CBGallery::getTab( $profileId );

			if ( ! $tab ) {
				return null;
			}

			$gallery					=	new Gallery( null, $profileId );

			$gallery->set( 'tab', $tab->get( 'tabid', 0, GetterInterface::INT ) );

			$gallery->parse( $tab->params, 'gallery_' );

			$galleries[$profileId]		=	$gallery;
		}

		return $galleries[$profileId];
	}

	/**
	 * @param bool                        $count
	 * @param array                       $select
	 * @param array                       $where
	 * @param array                       $join
	 * @param Activity $stream
	 */
	public function activityQuery( $count, &$select, &$where, &$join, &$stream )
	{
		global $_CB_database;

		$join[]				=	'LEFT JOIN ' . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' ) . ' AS gallery_item'
							.	' ON a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'gallery' )
							.	' AND a.' . $_CB_database->NameQuote( 'subtype' ) . ' = gallery_item.' . $_CB_database->NameQuote( 'type' )
							.	' AND a.' . $_CB_database->NameQuote( 'item' ) . ' = gallery_item.' . $_CB_database->NameQuote( 'id' );

		$join[]				=	'LEFT JOIN ' . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_folders' ) . ' AS gallery_folder'
							.	' ON a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'gallery' )
							.	' AND gallery_item.' . $_CB_database->NameQuote( 'folder' ) . ' = gallery_folder.' . $_CB_database->NameQuote( 'id' );

		if ( ! Application::MyUser()->isGlobalModerator() ) {
			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'gallery' )
							.	' AND gallery_item.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL'
							.	' AND ( gallery_item.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
							.	' OR gallery_item.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) Application::MyUser()->getUserId() . ' )'
							.	' AND ( gallery_item.' . $_CB_database->NameQuote( 'folder' ) . ' = 0'
							.	' OR ( gallery_folder.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL'
							.	' AND ( gallery_folder.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
							.	' OR gallery_folder.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) Application::MyUser()->getUserId() . ' ) ) ) )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'gallery' ) . ' ) )';
		} else {
			$where[]		=	'( ( a.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'gallery' )
							.	' AND gallery_item.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL'
							.	' AND ( gallery_item.' . $_CB_database->NameQuote( 'folder' ) . ' = 0'
							.	' OR gallery_folder.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) )'
							.	' OR ( a.' . $_CB_database->NameQuote( 'type' ) . ' != ' . $_CB_database->Quote( 'gallery' ) . ' ) )';
		}
	}

	/**
	 * @param ActivityTable[] $rows
	 * @param Activity              $stream
	 */
	public function activityLoad( &$rows, $stream )
	{
		$items									=	array();

		foreach ( $rows as $k => $row ) {
			if ( $row->get( 'type', null, GetterInterface::STRING ) != 'gallery' ) {
				continue;
			}

			$id									=	$row->get( 'item', 0, GetterInterface::INT );

			if ( ! $id ) {
				unset( $rows[$k] );
				continue;
			}

			$profileId							=	$row->get( 'user_id', 0, GetterInterface::INT );

			if ( ! $this->activityGallery( $profileId ) ) {
				unset( $rows[$k] );
				continue;
			}

			$items[$profileId][$k]				=	$id;
		}

		foreach ( $items as $profileId => $media ) {
			$media								=	cbToArrayOfInt( array_unique( $media ) );

			if ( ! $media ) {
				continue;
			}

			$gallery							=	$this->activityGallery( $profileId );

			if ( ! $gallery ) {
				continue;
			}

			$gallery->set( 'id', $media );

			$photos								=	array();
			$found								=	$gallery->items();

			foreach ( $media as $rowId => $itemId ) {
				if ( ( ! key_exists( $itemId, $found ) ) && isset( $rows[$rowId] ) ) {
					unset( $rows[$rowId] );
					continue;
				}

				if ( $found[$itemId]->get( 'type', null, GetterInterface::STRING ) == 'photos' ) {
					$folder						=	$found[$itemId]->get( 'folder', null, GetterInterface::INT );

					$photos[$folder][$rowId]	=	$itemId;
				}
			}

			foreach ( $photos as $folderId => $items ) {
				if ( count( $items ) <= 1 ) {
					continue;
				}

				$previous						=	null;

				foreach ( $items as $rowId => $itemId ) {
					if ( ! $previous ) {
						$previous				=	array( $rowId, $itemId );
					} else {
						$previousItems			=	$rows[$previous[0]]->get( '_items', array(), GetterInterface::RAW );
						$previousPhoto			=	$found[$previous[1]]->get( 'date', null, GetterInterface::STRING );
						$currentPhoto			=	$found[$itemId]->get( 'date', null, GetterInterface::STRING );
						$dateDiff				=	Application::Date( $previousPhoto, 'UTC' )->diff( $currentPhoto );

						if ( ( $dateDiff->days == 0 ) && ( $dateDiff->m <= 15 ) ) {
							$rows[$previous[0]]->params()->set( 'comments.source', 0 );

							$previousItems[]	=	$itemId;

							$rows[$previous[0]]->set( '_items', $previousItems );

							unset( $rows[$rowId] );
						} else {
							$previous			=	array( $rowId, $itemId );
						}
					}
				}
			}
		}
	}

	/**
	 * @param ActivityTable $row
	 * @param null|string   $title
	 * @param null|string   $date
	 * @param null|string   $message
	 * @param null|string   $insert
	 * @param null|string   $footer
	 * @param array         $menu
	 * @param array         $extras
	 * @param Activity      $stream
	 * @param int           $output 0: Normal, 1: Raw, 2: Inline, 3: Load, 4: Save
	 */
	public function activityDisplay( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, &$extras, $stream, $output )
	{
		if ( $row->get( 'type', null, GetterInterface::STRING ) != 'gallery' ) {
			return;
		}

		$row->set( '_links', false );

		if ( ! in_array( $row->get( 'subtype', null, GetterInterface::STRING ), array( 'photos', 'files', 'videos', 'music' ) ) ) {
			return;
		}

		$ids			=	$row->get( '_items', array(), GetterInterface::RAW );

		array_unshift( $ids, $row->get( 'item', 0, GetterInterface::INT ) );

		if ( ! $ids ) {
			return;
		}

		$profileId		=	$row->get( 'user_id', 0, GetterInterface::INT );

		if ( ! $profileId ) {
			return;
		}

		$gallery		=	$this->activityGallery( $profileId );

		if ( ! $gallery ) {
			return;
		}

		CBGallery::getTemplate( 'activity' );

		$insert			=	\HTML_cbgalleryActivity::showActivity( $row, $title, $message, $stream, $gallery->reset()->setId( $ids )->items(), $gallery, $this );
	}
}