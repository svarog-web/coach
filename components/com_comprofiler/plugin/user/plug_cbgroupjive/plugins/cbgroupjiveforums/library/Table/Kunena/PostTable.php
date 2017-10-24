<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveForums\Table\Kunena;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Database\DatabaseDriverInterface;
use CB\Plugin\GroupJiveForums\Table\PostTableInterface;

defined('CBLIB') or die();

class PostTable extends Table implements PostTableInterface
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var int  */
	public $category		=	null;
	/** @var string  */
	public $subject			=	null;
	/** @var string  */
	public $message			=	null;
	/** @var string  */
	public $date			=	null;
	/** @var int  */
	public $published		=	null;

	/** @var \KunenaForumMessage  */
	protected $_post		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__kunena_messages';

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

		$this->_post	=	new \KunenaForumMessage();

		$this->_post->load();
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

		$this->_post->load( (int) $keys );

		if ( ! $this->_post->exists() ) {
			$this->_post->set( 'id', 0 );
		}

		$this->post( $this->_post );

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		if ( ! $this->_post ) {
			return false;
		}

		$store	=	$this->_post->save();

		$this->post( $this->_post );

		return $store;
	}

	/**
	 * @param string $var
	 * @param mixed  $value
	 */
	public function set( $var, $value )
	{
		if ( ! $this->_post ) {
			return;
		}

		parent::set( $var, $value );

		switch ( $var ) {
			case 'user_id':
				$var		=	'userid';
				break;
			case 'category':
				$var		=	'catid';
				break;
			case 'date':
				$var		=	'time';
				$value		=	Application::Date( $value, 'UTC' )->getTimestamp();
				break;
			case 'published':
				$var		=	'hold';
				$value		=	( $value == 1 ? 0 : 1 );
				break;
		}

		$this->_post->set( $var, $value );
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		if ( ( ! $this->_post ) || $id ) {
			return false;
		}

		return $this->_post->delete();
	}

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( ! $this->_post ) {
			return false;
		}

		return $this->_post->check();
	}

	/**
	 * @param array|object $array
	 * @param string       $ignore
	 * @param string       $prefix
	 * @return boolean
	 */
	public function bind( $array, $ignore = '', $prefix = null )
	{
		$bind					=	parent::bind( $array, $ignore, $prefix );

		if ( $bind ) {
			$data				=	array();

			foreach ( $this as $k => $v ) {
				switch ( $k ) {
					case 'user_id':
						$k		=	'userid';
						break;
					case 'category':
						$k		=	'catid';
						break;
					case 'date':
						$k		=	'time';
						$v		=	Application::Date( $v, 'UTC' )->getTimestamp();
						break;
					case 'published':
						$k		=	'hold';
						$v		=	( $v == 1 ? 0 : 1 );
						break;
				}

				$data[$k]		=	$v;
			}

			$bind				=	$this->_post->bind( $data, explode( ' ', $ignore ), false );
		}

		return $bind;
	}

	/**
	 * get or set the kunena post object
	 *
	 * @param \KunenaForumMessage|null $post
	 * @return \KunenaForumMessage|null
	 */
	public function post( $post = null )
	{
		if ( $post !== null ) {
			$this->_post		=	$post;

			$data				=	array();

			foreach ( $this->_post as $k => $v ) {
				switch ( $k ) {
					case 'userid':
						$k		=	'user_id';
						break;
					case 'catid':
						$k		=	'category';
						break;
					case 'time':
						$k		=	'date';
						$v		=	Application::Date( $v, 'UTC' )->format( 'Y-m-d H:i:s' );
						break;
					case 'hold':
						$k		=	'published';
						$v		=	( $v == 0 ? 1 : 0 );
						break;
				}

				$data[$k]		=	$v;
			}

			parent::bind( $data );
		}

		return $this->_post;
	}

	/**
	 * returns the forum post url
	 *
	 * @return null|string
	 */
	public function url()
	{
		if ( ! $this->_post ) {
			return null;
		}

		return $this->_post->getUrl();
	}
}