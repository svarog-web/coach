<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveForums\Table\Kunena;

use CB\Plugin\GroupJive\Table\GroupTable;
use CBLib\Database\DatabaseDriverInterface;
use CBLib\Database\Table\Table;
use CB\Plugin\GroupJiveForums\Table\CategoryTableInterface;

defined('CBLIB') or die();

class CategoryTable extends Table implements CategoryTableInterface
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $parent			=	null;
	/** @var string  */
	public $name			=	null;
	/** @var string  */
	public $alias			=	null;
	/** @var string  */
	public $description		=	null;
	/** @var int  */
	public $published		=	null;

	/** @var \KunenaForumCategory  */
	protected $_category	=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__kunena_categories';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @param DatabaseDriverInterface|null $db
	 * @param string                       $table
	 * @param string|array                 $key
	 */
	public function __construct( DatabaseDriverInterface $db = null, $table = null, $key = null )
	{
		parent::__construct( $db, $table, $key );

		$this->_category	=	new \KunenaForumCategory();

		$this->_category->load();
	}

	/**
	 * @param int|array $keys
	 * @return bool
	 */
	public function load( $keys = null )
	{
		if ( is_array( $keys ) ) {
			return false;
		}

		$this->_category->load( (int) $keys );

		if ( ! $this->_category->exists() ) {
			$this->_category->set( 'id', 0 );
		}

		$this->category( $this->_category );

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		if ( ! $this->_category ) {
			return false;
		}

		$store	=	$this->_category->save();

		$this->category( $this->_category );

		return $store;
	}

	/**
	 * @param string $var
	 * @param mixed  $value
	 */
	public function set( $var, $value )
	{
		if ( ! $this->_category ) {
			return;
		}

		parent::set( $var, $value );

		if ( $var == 'parent' ) {
			$var	=	'parent_id';
		}

		$this->_category->set( $var, $value );
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		if ( ( ! $this->_category ) || $id ) {
			return false;
		}

		return $this->_category->delete();
	}

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( ! $this->_category ) {
			return false;
		}

		return $this->_category->check();
	}

	/**
	 * @param array|object $array
	 * @param string       $ignore
	 * @param string       $prefix
	 * @return boolean
	 */
	public function bind( $array, $ignore = '', $prefix = null )
	{
		$bind				=	parent::bind( $array, $ignore, $prefix );

		if ( $bind ) {
			$data			=	array();

			foreach ( $this as $k => $v ) {
				if ( $k == 'parent' ) {
					$k		=	'parent_id';
				}

				$data[$k]	=	$v;
			}

			$bind			=	$this->_category->bind( $data, explode( ' ', $ignore ), false );
		}

		return $bind;
	}

	/**
	 * get or set the kunena category object
	 *
	 * @param \KunenaForumCategory|null $category
	 * @return \KunenaForumCategory|null
	 */
	public function category( $category = null )
	{
		if ( $category !== null ) {
			$this->_category	=	$category;

			$data				=	array();

			foreach ( $this->_category as $k => $v ) {
				if ( $k == 'parent_id' ) {
					$k			=	'parent';
				}

				$data[$k]		=	$v;
			}

			parent::bind( $data );
		}

		return $this->_category;
	}

	/**
	 * sets the forum access based off category or group object access
	 *
	 * @param \CB\Plugin\GroupJive\Table\CategoryTable|GroupTable $row
	 */
	public function access( $row )
	{
		if ( $row instanceof \CB\Plugin\GroupJive\Table\CategoryTable ) {
			$this->_category->set( 'accesstype', 'joomla.level' );
			$this->_category->set( 'access', $row->get( 'access' ) );
		} elseif ( $row instanceof GroupTable ) {
			$this->_category->set( 'accesstype', 'communitybuilder' );
			$this->_category->set( 'access', $row->get( 'id' ) );
		}
	}

	/**
	 * get, add, or delete moderators for this category
	 *
	 * @param null|array $addUsers
	 * @param null|array $deleteUsers
	 * @return array
	 */
	public function moderators( $addUsers = null, $deleteUsers = null )
	{
		if ( ! $this->_category ) {
			return array();
		}

		if ( $addUsers !== null ) {
			if ( is_array( $addUsers ) ) {
				$this->_category->addModerators( $addUsers );
			} else {
				$this->_category->addModerator( $addUsers );
			}
		}

		if ( $deleteUsers !== null ) {
			if ( is_array( $deleteUsers ) ) {
				foreach ( $deleteUsers as $deleteUser ) {
					$this->_category->removeModerator( $deleteUser );
				}
			} else {
				$this->_category->removeModerator( $deleteUsers );
			}
		}

		return $this->_category->getModerators( false, false );
	}

	/**
	 * returns the forum category url
	 *
	 * @return null|string
	 */
	public function url()
	{
		if ( ! $this->_category ) {
			return null;
		}

		return $this->_category->getUrl();
	}
}