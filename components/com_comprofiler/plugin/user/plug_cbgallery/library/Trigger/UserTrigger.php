<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery\Trigger;

use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\Gallery\Table\FolderTable;
use CB\Plugin\Gallery\Table\ItemTable;

defined('CBLIB') or die();

class UserTrigger extends \cbPluginHandler
{

	/**
	 * Deletes items when the user is deleted
	 *
	 * @param  UserTable $user
	 * @param  int       $status
	 */
	public function deleteItems( $user, $status )
	{
		global $_CB_framework, $_CB_database, $_PLUGINS;

		$plugin					=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );
		$params					=	$_PLUGINS->getPluginParams( $plugin );

		if ( $params->get( 'general_delete', true, GetterInterface::BOOLEAN ) ) {
			// Delete all folders owned by the user:
			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_folders' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$folders			=	$_CB_database->loadObjectList( null, '\CB\Plugin\Gallery\Table\FolderTable', array( $_CB_database ) );

			/** @var FolderTable[] $folders */
			foreach ( $folders as $folder ) {
				$folder->delete();
			}

			// Delete all gallery entries owned by the user:
			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$items				=	$_CB_database->loadObjectList( null, '\CB\Plugin\Gallery\Table\ItemTable', array( $_CB_database ) );

			/** @var ItemTable[] $items */
			foreach ( $items as $item ) {
				$item->delete();
			}

			// Finalize storage cleaned by deleting their gallery folder and any remaining contents:
			$basePath			=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgallery/' . $user->get( 'id', 0, GetterInterface::INT );

			if ( is_dir( $basePath ) ) {
				$basePath		=	str_replace( '\\', '/', realpath( $basePath ) );

				if ( is_dir( $basePath ) ) {
					$files		=	new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $basePath ), \RecursiveIteratorIterator::CHILD_FIRST );

					if ( $files ) foreach ( $files as $file ) {
						$file	=	str_replace( '\\', '/', realpath( $file ) );

						if ( is_dir( $file ) ) {
							@rmdir( $file );
						} elseif ( is_file( $file ) ) {
							@unlink( $file );
						}
					}

					@rmdir( $basePath );
				}
			}
		}
	}
}