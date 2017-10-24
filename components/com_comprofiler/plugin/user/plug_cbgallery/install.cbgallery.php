<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Registry\Registry;
use CBLib\Database\Table\Table;
use CB\Database\Table\PluginTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

function plug_cbgallery_install()
{
	global $_CB_framework, $_CB_database;

	$plugin								=	new PluginTable();

	if ( $plugin->load( array( 'element' => 'cb.profilegallery' ) ) ) {
		$path							=	$_CB_framework->getCfg( 'absolute_path' );
		$indexPath						=	$path . '/components/com_comprofiler/plugin/user/plug_cbgallery/index.html';
		$oldFilesPath					=	$path . '/images/comprofiler/plug_profilegallery';
		$newFilesPath					=	$path . '/images/comprofiler/plug_cbgallery';

		$query							=	'SELECT *'
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilegallery' );
		$_CB_database->setQuery( $query );
		$rows							=	$_CB_database->loadObjectList( null, '\CBLib\Database\Table\Table', array( $_CB_database, '#__comprofiler_plug_profilegallery', 'id' ) );

		/** @var $rows Table[] */
		foreach ( $rows as $row ) {
			$oldFilePath				=	$oldFilesPath . '/' . $row->get( 'userid', 0, GetterInterface::INT );

			if ( in_array( $row->get( 'pgitemtype' ), array( 'jpg', 'jpeg', 'gif', 'png' ) ) ) {
				$type					=	'photos';
			} else {
				$type					=	'files';
			}

			$newFilePath				=	$newFilesPath . '/' . $row->get( 'userid', 0, GetterInterface::INT ) . '/' . $type;

			if ( ( ! file_exists( $oldFilePath . '/' . $row->get( 'pgitemfilename', null, GetterInterface::STRING ) ) ) || ( ( $type == 'photos' ) && ( ! file_exists( $oldFilePath . '/tn' . $row->get( 'pgitemfilename', null, GetterInterface::STRING ) ) ) ) ) {
				continue;
			}

			$cleanFileName				=	str_replace( 'pg_', '', pathinfo( $row->get( 'pgitemfilename', null, GetterInterface::STRING ), PATHINFO_FILENAME ) );
			$newFileName				=	uniqid( $cleanFileName . '_' ) . '.' . strtolower( pathinfo( $row->get( 'pgitemfilename', null, GetterInterface::STRING ), PATHINFO_EXTENSION ) );

			if ( cbReadDirectory( $newFilePath, '^' . preg_quote( $cleanFileName ) ) ) {
				$query					=	'SELECT COUNT(*)'
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $row->get( 'userid', 0, GetterInterface::INT )
										.	"\n AND " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $cleanFileName, true ) . '%', false );
				$_CB_database->setQuery( $query );
				if ( $_CB_database->loadResult() ) {
					continue;
				}
			}

			if ( ! is_dir( $newFilesPath ) ) {
				$oldMask				=	@umask( 0 );

				if ( @mkdir( $newFilesPath, 0755, true ) ) {
					@umask( $oldMask );
					@chmod( $newFilesPath, 0755 );

					if ( ! file_exists( $newFilesPath . '/index.html' ) ) {
						@copy( $indexPath, $newFilesPath . '/index.html' );
						@chmod( $newFilesPath . '/index.html', 0755 );
					}
				} else {
					@umask( $oldMask );
				}
			}

			if ( ! file_exists( $newFilesPath . '/.htaccess' ) ) {
				file_put_contents( $newFilesPath . '/.htaccess', 'deny from all' );
			}

			if ( ! is_dir( $newFilePath ) ) {
				$oldMask				=	@umask( 0 );

				if ( @mkdir( $newFilePath, 0755, true ) ) {
					@umask( $oldMask );
					@chmod( $newFilePath, 0755 );

					if ( ! file_exists( $newFilePath . '/index.html' ) ) {
						@copy( $indexPath, $newFilePath . '/index.html' );
						@chmod( $newFilePath . '/index.html', 0755 );
					}
				} else {
					@umask( $oldMask );
				}
			}

			if ( ! @copy( $oldFilePath . '/' . $row->get( 'pgitemfilename', null, GetterInterface::STRING ), $newFilePath . '/' . $newFileName ) ) {
				continue;
			} else {
				@chmod( $newFilePath . '/' . $newFileName, 0755 );
			}

			if ( $type == 'photos' ) {
				if ( ! @copy( $oldFilePath . '/tn' . $row->get( 'pgitemfilename', null, GetterInterface::STRING ), $newFilePath . '/tn' . $newFileName ) ) {
					continue;
				} else {
					@chmod( $newFilePath . '/tn' . $newFileName, 0755 );
				}
			}

			$item						=	new Table( null, '#__comprofiler_plugin_gallery_items', 'id' );

			$item->set( 'user_id', $row->get( 'userid', 0, GetterInterface::INT ) );
			$item->set( 'asset', 'profile.' . $row->get( 'userid', 0, GetterInterface::INT ) );
			$item->set( 'type', $type );
			$item->set( 'value', $newFileName );
			$item->set( 'folder', 0 );
			$item->set( 'title', $row->get( 'pgitemtitle', null, GetterInterface::STRING ) );
			$item->set( 'description', $row->get( 'pgitemdescription', null, GetterInterface::STRING ) );
			$item->set( 'date', $row->get( 'pgitemdate', null, GetterInterface::STRING ) );
			$item->set( 'published', ( $row->get( 'pgitemapproved', 0, GetterInterface::INT ) ? $row->get( 'pgitempublished', 0, GetterInterface::INT ) : -1 ) );

			if ( ! $item->store() ) {
				@unlink( $newFilePath . '/' . $newFileName );

				if ( $type == 'photos' ) {
					@unlink( $newFilePath . '/tn' . $newFileName );
				}
			}
		}

		$field							=	new FieldTable();

		if ( $field->load( array( 'name' => 'cb_pgtotalquotaitems' ) ) ) {
			$field->set( 'type', 'integer' );
			$field->set( 'tabid', 11 );
			$field->set( 'pluginid', 1 );
			$field->set( 'readonly', 1 );
			$field->set( 'calculated', 0 );
			$field->set( 'sys', 0 );

			$field->store();
		}

		$gallery						=	new PluginTable();

		if ( $gallery->load( array( 'element' => 'cbgallery' ) ) ) {
			$galleryParams				=	new Registry( $gallery->params );

			$galleryParams->set( 'photos_item_limit', 'cb_pgtotalquotaitems' );
			$galleryParams->set( 'files_item_limit', 'cb_pgtotalquotaitems' );

			$gallery->set( 'params', $galleryParams->asJson() );

			$gallery->store();
		}

		ob_start();
		$plgInstaller					=	new cbInstallerPlugin();

		$plgInstaller->uninstall( $plugin->id, 'com_comprofiler' );
		ob_end_clean();
	}

	// Fix items with a missing asset:
	$query								=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT( " . $_CB_database->Quote( 'profile.' ) . ", " . $_CB_database->NameQuote( 'user_id' ) . " )"
										.	"\n WHERE ( " . $_CB_database->NameQuote( 'asset' ) . " IS NULL OR " . $_CB_database->NameQuote( 'asset' ) . " = '' )";
	$_CB_database->setQuery( $query );
	$_CB_database->query();

	// Fix folders with a missing asset:
	$query								=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_folders' )
										.	"\n SET " . $_CB_database->NameQuote( 'asset' ) . " = CONCAT( " . $_CB_database->Quote( 'profile.' ) . ", " . $_CB_database->NameQuote( 'user_id' ) . " )"
										.	"\n WHERE ( " . $_CB_database->NameQuote( 'asset' ) . " IS NULL OR " . $_CB_database->NameQuote( 'asset' ) . " = '' )";
	$_CB_database->setQuery( $query );
	$_CB_database->query();

	// Migrate old tabs if they exist:
	$tabs								=	array( 'cbgalleryTabGallery', 'cbgalleryTabPhotos', 'cbgalleryTabFiles', 'cbgalleryTabVideos', 'cbgalleryTabMusic' );
	$tab								=	new TabTable();

	if ( $tab->load( array( 'pluginclass' => 'cbgalleryTab' ) ) ) {
		$tabParams						=	new Registry( $tab->params );
		$tabMigrate						=	false;

		foreach ( $tabs as $oldTabClass ) {
			$oldTab						=	new TabTable();

			if ( ! $oldTab->load( array( 'pluginclass' => $oldTabClass ) ) ) {
				continue;
			} elseif ( ! $oldTab->get( 'enabled', 0, GetterInterface::INT ) ) {
				$oldTab->delete();

				continue;
			}

			$oldTabParams				=	new Registry( $oldTab->params );

			if ( in_array( $oldTabClass, array( 'cbgalleryTabGallery', 'cbgalleryTabPhotos' ) ) ) {
				$tabParams->set( 'photos_download', $oldTabParams->get( 'tab_photos_download', 0, GetterInterface::INT ) );
			}

			if ( in_array( $oldTabClass, array( 'cbgalleryTabGallery', 'cbgalleryTabVideos' ) ) ) {
				$tabParams->set( 'videos_download', $oldTabParams->get( 'tab_videos_download', 0, GetterInterface::INT ) );
			}

			if ( in_array( $oldTabClass, array( 'cbgalleryTabGallery', 'cbgalleryTabMusic' ) ) ) {
				$tabParams->set( 'music_download', $oldTabParams->get( 'tab_music_download', 0, GetterInterface::INT ) );
			}

			$tabMigrate					=	true;

			$oldTab->delete();
		}

		if ( $tabMigrate ) {
			$tab->set( 'params', $tabParams->asJson() );

			$tab->store();
		}
	}

	// Migrate old global params if they exist:
	$params								=	array(	'photos_item_create_access' => 'photos_create_access', 'photos_item_limit' => 'photos_create_limit', 'photos_item_limit_custom' => 'photos_create_limit_custom',
													'photos_item_upload' => 'photos_upload', 'photos_item_link' => 'photos_link', 'photos_item_min_size' => 'photos_min_size',
													'photos_item_max_size' => 'photos_max_size', 'photos_item_approval' => 'photos_create_approval', 'files_item_create_access' => 'files_create_access',
													'files_item_limit' => 'files_create_limit', 'files_item_limit_custom' => 'files_create_limit_custom', 'files_item_upload' => 'files_upload',
													'files_item_link' => 'files_link', 'files_item_extensions' => 'files_extensions', 'files_item_min_size' => 'files_min_size',
													'files_item_max_size' => 'files_max_size', 'files_item_approval' => 'files_create_approval', 'videos_item_create_access' => 'videos_create_access',
													'videos_item_limit' => 'videos_create_limit', 'videos_item_limit_custom' => 'videos_create_limit_custom', 'videos_item_upload' => 'videos_upload',
													'videos_item_link' => 'videos_link', 'videos_item_min_size' => 'videos_min_size', 'videos_item_max_size' => 'videos_max_size',
													'videos_item_approval' => 'videos_create_approval', 'music_item_create_access' => 'music_create_access', 'music_item_limit' => 'music_create_limit',
													'music_item_limit_custom' => 'music_create_limit_custom', 'music_item_upload' => 'music_upload', 'music_item_link' => 'music_link',
													'music_item_min_size' => 'music_min_size', 'music_item_max_size' => 'music_max_size', 'music_item_approval' => 'music_create_approval'
												);

	$plugin								=	new PluginTable();

	if ( $plugin->load( array( 'element' => 'cbgallery' ) ) ) {
		$pluginParams					=	new Registry( $plugin->params );
		$pluginMigrate					=	false;

		foreach ( $params as $oldParam => $newParam ) {
			if ( ! $pluginParams->has( $oldParam ) ) {
				continue;
			}

			$oldValue					=	$pluginParams->get( $oldParam, null, GetterInterface::RAW );

			if ( in_array( $oldParam, array( 'photos_item_limit', 'files_item_limit', 'videos_item_limit', 'music_item_limit' ) ) && ( ! $oldValue ) ) {
				$oldValue				=	'custom';
			}

			$pluginParams->set( $newParam, $oldValue );
			$pluginParams->unsetEntry( $oldParam );

			$pluginMigrate				=	true;
		}

		if ( $pluginMigrate ) {
			$plugin->set( 'params', $pluginParams->asJson() );

			$plugin->store();
		}
	}
}