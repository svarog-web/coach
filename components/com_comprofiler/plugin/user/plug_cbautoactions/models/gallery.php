<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CB\Plugin\Gallery\Table\ItemTable;
use CB\Plugin\Gallery\Table\FolderTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionGallery extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 * @return null|string
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_database;

		if ( ! $this->installed() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_GALLERY_NOT_INSTALLED', ':: Action [action] :: CB Gallery is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return null;
		}

		foreach ( $trigger->getParams()->subTree( 'gallery' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner							=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner						=	(int) $user->get( 'id' );
			} else {
				$owner						=	(int) $trigger->getSubstituteString( $owner );
			}

			if ( ! $owner ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_OWNER', ':: Action [action] :: CB Gallery skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$mode							=	$row->get( 'mode', 'item', GetterInterface::STRING );
			$type							=	$row->get( 'type', 'photos', GetterInterface::STRING );
			$asset							=	$trigger->getSubstituteString( $row->get( 'asset', null, GetterInterface::STRING ) );
			$value							=	$trigger->getSubstituteString( $row->get( 'value', null, GetterInterface::STRING ) );
			$title							=	$trigger->getSubstituteString( $row->get( 'title', null, GetterInterface::STRING ) );
			$description					=	$trigger->getSubstituteString( $row->get( 'description', null, GetterInterface::STRING ) );

			switch ( $mode ) {
				case 'folder':
					if ( class_exists( 'cbgalleryFolderTable' ) ) {
						$object				=	new cbgalleryFolderTable( $_CB_database );
					} else {
						$object				=	new FolderTable();

						if ( $asset ) {
							$object->set( 'asset', $asset );
						}
					}
					break;
				case 'item':
				default:
					if ( class_exists( 'cbgalleryItemTable' ) ) {
						$object				=	new cbgalleryItemTable( $_CB_database );
					} else {
						$object				=	new ItemTable();

						if ( $asset ) {
							$object->set( 'asset', $asset );
						}
					}
					break;
			}

			$object->set( 'user_id', $owner );

			if ( $type ) {
				$object->set( 'type', $type );
			}

			if ( $title ) {
				$object->set( 'title', $title );
			}

			if ( $description ) {
				$object->set( 'description', $description );
			}

			if ( $mode == 'item' ) {
				if ( ! $value ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_VALUE', ':: Action [action] :: CB Gallery skipped due to missing value', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
					}

					continue;
				}

				$object->set( 'value', $value );
			}

			if ( ! $object->store() ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_GALLERY_FAILED', ':: Action [action] :: CB Gallery failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $object->getError() ) ) );
				}
			}
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' ) ) {
			return true;
		}

		return false;
	}
}