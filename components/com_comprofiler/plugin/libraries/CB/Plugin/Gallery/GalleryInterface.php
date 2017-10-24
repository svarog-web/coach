<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery;

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\Gallery\Table\FolderTable;
use CB\Plugin\Gallery\Table\ItemTable;

defined('CBLIB') or die();

interface GalleryInterface extends ParamsInterface
{
	/**
	 * Reloads the gallery from session by id
	 *
	 * @param string $id
	 * @return bool
	 */
	public function load( $id );

	/**
	 * Parses parameters into the gallery
	 *
	 * @param ParamsInterface|array $params
	 * @param null|string           $prefix
	 * @return self
	 */
	public function parse( $params, $prefix = null );

	/**
	 * Gets the gallery location
	 *
	 * @return string
	 */
	public function location();

	/**
	 * Gets the gallery id
	 *
	 * @return string
	 */
	public function id();

	/**
	 * Gets the gallery asset
	 *
	 * @param bool $raw
	 * @return string
	 */
	public function asset( $raw = false );

	/**
	 * Gets the gallery target user (owner)
	 *
	 * @return UserTable|null
	 */
	public function user();

	/**
	 * Gets the types allowed in this gallery
	 *
	 * @return array
	 */
	public function types();

	/**
	 * Clears the data cache
	 *
	 * @return self
	 */
	public function clear();

	/**
	 * Resets the gallery filters
	 *
	 * @return self
	 */
	public function reset();

	/**
	 * Retrieves gallery folder rows or row count
	 *
	 * @param bool $count
	 * @return FolderTable[]|int
	 */
	public function folders( $count = false );

	/**
	 * Retrieves gallery folder row
	 *
	 * @param int $id
	 * @return FolderTable
	 */
	public function folder( $id );

	/**
	 * Retrieves gallery item rows or row count
	 *
	 * @param bool $count
	 * @return ItemTable[]|int
	 */
	public function items( $count = false );

	/**
	 * Retrieves gallery item row
	 *
	 * @param int $id
	 * @return ItemTable
	 */
	public function item( $id );

	/**
	 * Outputs gallery HTML
	 *
	 * @return string
	 */
	public function gallery();

	/**
	 * Caches the gallery into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless gallery parameters have changed after creation and desired result is for them to persist
	 *
	 * @return self
	 */
	public function cache();
}