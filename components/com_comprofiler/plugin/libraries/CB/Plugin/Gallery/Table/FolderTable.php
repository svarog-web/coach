<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Gallery\CBGallery;

defined('CBLIB') or die();

class FolderTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var string  */
	public $asset			=	null;
	/** @var string  */
	public $title			=	null;
	/** @var string  */
	public $description		=	null;
	/** @var int  */
	public $thumbnail		=	null;
	/** @var string  */
	public $date			=	null;
	/** @var int  */
	public $published		=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_gallery_folders';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( ! $this->get( 'user_id', 0, GetterInterface::INT ) ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_CB_database, $_PLUGINS;

		$new			=	( $this->get( 'id', 0, GetterInterface::INT ) ? false : true );
		$old			=	new self();

		if ( ! $this->get( 'asset', null, GetterInterface::STRING ) ) {
			$this->set( 'asset', 'profile.' . $this->get( 'user_id', 0, GetterInterface::INT ) );
		}

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime(), GetterInterface::STRING ) );

		if ( ! $new ) {
			$old->load( $this->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'gallery_onBeforeUpdateFolder', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onBeforeCreateFolder', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( $old->get( 'id', 0, GetterInterface::INT ) && ( $old->get( 'user_id', 0, GetterInterface::INT ) != $this->get( 'user_id', 0, GetterInterface::INT ) ) ) {
			$query		=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'folder' ) . " = " . $this->get( 'id', 0, GetterInterface::INT )
						.	"\n AND " . $_CB_database->NameQuote( 'user_id' ) . " != " . $this->get( 'user_id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$items		=	$_CB_database->loadObjectList( null, '\CB\Plugin\Gallery\Table\ItemTable', array( $_CB_database ) );

			/** @var ItemTable[] $items */
			foreach ( $items as $item ) {
				$item->set( 'user_id', $this->get( 'user_id', 0, GetterInterface::INT ) );

				$item->store();
			}
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gallery_onAfterUpdateFolder', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onAfterCreateFolder', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_CB_database, $_PLUGINS;

		$_PLUGINS->trigger( 'gallery_onBeforeDeleteFolder', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		if ( $this->get( 'id', 0, GetterInterface::INT ) ) {
			$query		=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'folder' ) . " = " . $this->get( 'id', 0, GetterInterface::INT );
			$_CB_database->setQuery( $query );
			$items		=	$_CB_database->loadObjectList( null, '\CB\Plugin\Gallery\Table\ItemTable', array( $_CB_database ) );

			/** @var ItemTable[] $items */
			foreach ( $items as $item ) {
				$item->delete();
			}
		}

		$_PLUGINS->trigger( 'gallery_onAfterDeleteFolder', array( $this ) );

		return true;
	}

	/**
	 * @return Registry
	 */
	public function params()
	{
		if ( ! ( $this->get( '_params' ) instanceof Registry ) ) {
			$this->set( '_params', new Registry( $this->get( 'params', null, GetterInterface::RAW ) ) );
		}

		return $this->get( '_params' );
	}

	/**
	 * Returns the items in this folder
	 *
	 * @param bool         $count
	 * @param null|Gallery $gallery
	 * @return int|ItemTable[]
	 */
	public function items( $count = false, $gallery = null )
	{
		global $_CB_database;

		if ( $count && ( $this->get( '_items', null, GetterInterface::RAW ) !== null ) ) {
			return $this->get( '_items', 0, GetterInterface::INT );
		}

		$id									=	$this->get( 'id', 0, GetterInterface::INT );

		if ( ! $id ) {
			return ( $count ? 0 : array() );
		}

		if ( $gallery ) {
			return $gallery->reset()->setFolder( $id )->setPublished( $gallery->get( 'published', null, GetterInterface::RAW ) )->items( $count );
		}

		static $cache						=	array();

		$userId								=	Application::MyUser()->getUserId();

		if ( ! isset( $cache[$id][$userId][$count] ) ) {
			if ( $count ) {
				$query						=	'SELECT COUNT(*)'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' )
											.	"\n WHERE " . $_CB_database->NameQuote( 'folder' ) . " = " . (int) $id
											.	( ( ( $userId != (int) $this->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! Application::User( $userId )->isGlobalModerator() ) ) ? "\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1" : null );
				$_CB_database->setQuery( $query );
				$rows						=	(int) $_CB_database->loadResult();
			} else {
				$query						=	'SELECT *'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' )
											.	"\n WHERE " . $_CB_database->NameQuote( 'folder' ) . " = " . (int) $id
											.	( ( ( $userId != (int) $this->get( 'user_id', 0, GetterInterface::INT ) ) && ( ! Application::User( $userId )->isGlobalModerator() ) ) ? "\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1" : null );
				$_CB_database->setQuery( $query );
				$rows						=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Gallery\Table\ItemTable', array( $_CB_database ) );
			}

			$cache[$id][$userId][$count]	=	$rows;
		}

		return $cache[$id][$userId][$count];
	}

	/**
	 * Returns folders thumbnail
	 *
	 * @param Gallery $gallery
	 * @param int     $size
	 * @return string
	 */
	public function thumbnail( $gallery = null, $size = 0 )
	{
		$thumbnail			=	$this->get( 'thumbnail', 0, GetterInterface::INT );

		if ( $gallery ) {
			if ( $thumbnail ) {
				$item		=	$gallery->item( $thumbnail );

				if ( $item->get( 'id', 0, GetterInterface::INT ) && ( $item->get( 'folder', 0, GetterInterface::INT ) == $this->get( 'id', 0, GetterInterface::INT ) ) && ( ( $item->get( 'published', 1, GetterInterface::INT ) ) || ( ( Application::MyUser()->getUserId() == $item->get( 'user_id', 0, GetterInterface::INT ) ) || CBGallery::canModerate( $gallery ) ) ) ) {
					return $item->thumbnail( $gallery, $size, ( ( $size && ( ( $item->width( true ) >= $size ) || ( $item->height( true ) >= $size ) ) ) || ( $item->mimeType() == 'video/x-youtube' ) ) );
				}
			}
		}

		return '<span class="fa fa-folder-open-o" style="vertical-align: middle;' . ( $size ? ' font-size: ' . ( (int) $size - 25 ) . 'px;' : null ) . '"></span>';
	}
}