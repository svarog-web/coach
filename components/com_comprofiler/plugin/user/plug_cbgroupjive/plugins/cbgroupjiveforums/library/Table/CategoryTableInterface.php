<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveForums\Table;

use CBLib\Database\Table\TableInterface;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\GroupTable;

defined('CBLIB') or die();

/**
 * Interface CategoryTableInterface
 *
 * @property int    $id
 * @property int    $parent
 * @property string $name
 * @property string $alias
 * @property string $description
 * @property int    $published
 *
 * @package CB\Plugin\GroupJiveForums\Table
 */
interface CategoryTableInterface extends TableInterface
{

	/**
	 * get or set the category object
	 *
	 * @param object|null $category
	 * @return object|null
	 */
	public function category( $category = null );

	/**
	 * sets the forum access based off category or group object access
	 *
	 * @param CategoryTable|GroupTable $row
	 */
	public function access( $row );

	/**
	 * get, add, or delete moderators for this category
	 *
	 * @param null|array $addUsers
	 * @param null|array $deleteUsers
	 * @return array
	 */
	public function moderators( $addUsers = null, $deleteUsers = null );

	/**
	 * returns the forum category url
	 *
	 * @return null|string
	 */
	public function url();
}