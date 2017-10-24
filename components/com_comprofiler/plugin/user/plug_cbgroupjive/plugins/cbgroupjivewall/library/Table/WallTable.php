<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveWall\Table;

use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveWall\CBGroupJiveWall;
use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CB\Plugin\GroupJive\Table\GroupTable;

defined('CBLIB') or die();

class WallTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var int  */
	public $group			=	null;
	/** @var int  */
	public $reply			=	null;
	/** @var string  */
	public $post			=	null;
	/** @var string  */
	public $date			=	null;
	/** @var int  */
	public $published		=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/** @var array  */
	protected $_regexp		=	array(	'link'		=>	'#^((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?������]))$#i',
										'email'		=>	'/^[a-z0-9!#$%&\'*+\\\\\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\\\\\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i'
									);

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__groupjive_plugin_wall';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( $this->get( 'user_id' ) == '' ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		} elseif ( $this->get( 'group' ) == '' ) {
			$this->setError( CBTxt::T( 'Group not specified!' ) );

			return false;
		} elseif ( $this->get( 'post' ) == '' ) {
			$this->setError( CBTxt::T( 'Post not specified!' ) );

			return false;
		} elseif ( ! $this->group()->get( 'id' ) ) {
			$this->setError( CBTxt::T( 'Group does not exist!' ) );

			return false;
		} elseif ( $this->get( 'reply' ) ) {
			if ( ! $this->reply()->get( 'id' ) ) {
				$this->setError( CBTxt::T( 'Reply does not exist!' ) );

				return false;
			}
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime() ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'gj_onBeforeUpdateWall', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onBeforeCreateWall', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gj_onAfterUpdateWall', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onAfterCreateWall', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'gj_onBeforeDeleteWall', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Delete replies to this post:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__groupjive_plugin_wall' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'reply' ) . " = " . (int) $this->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$posts			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\GroupJiveWall\Table\WallTable', array( $this->getDbo() ) );

		/** @var WallTable[] $posts */
		foreach ( $posts as $post ) {
			$post->delete();
		}

		$_PLUGINS->trigger( 'gj_onAfterDeleteWall', array( $this ) );

		return true;
	}

	/**
	 * @return Registry
	 */
	public function params()
	{
		if ( ! ( $this->get( '_params' ) instanceof Registry ) ) {
			$this->set( '_params', new Registry( $this->get( 'params' ) ) );
		}

		return $this->get( '_params' );
	}

	/**
	 * @return GroupTable
	 */
	public function group()
	{
		return CBGroupJive::getGroup( (int) $this->get( 'group' ) );
	}

	/**
	 * @return WallTable
	 */
	public function reply()
	{
		return CBGroupJiveWall::getPost( (int) $this->get( 'reply' ) );
	}

	/**
	 * @return string
	 */
	public function post()
	{
		static $cache		=	array();

		$id					=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$post			=	$this->get( 'post' );
			$words			=	preg_split( '/\s/i', $post );

			// Replaces URLs with clickable html URLs:
			foreach ( $words as $word ) {
				if ( preg_match( $this->_regexp['link'], $word, $match ) ) {
					$post	=	str_replace( $word, '<a href="' . htmlspecialchars( $match[0] ) . '" rel="nofollow"' . ( ! \JUri::isInternal( $match[0] ) ? ' target="_blank"' : null ) . '>' . htmlspecialchars( $match[0] ) . '</a>', $post );
				} elseif ( preg_match( $this->_regexp['email'], $word, $match ) ) {
					$post	=	str_replace( $word, '<a href="mailto:' . htmlspecialchars( $match[0] ) . '" rel="nofollow" target="_blank">' . htmlspecialchars( $match[0] ) . '</a>', $post );
				}
			}

			// Remove duplicate spaces:
			$post			=	preg_replace( '/ {2,}/i', ' ', $post );

			// Remove duplicate tabs:
			$post			=	preg_replace( '/\t{2,}/i', "\t", $post );

			// Remove duplicate linebreaks:
			$post			=	preg_replace( '/(\r\n|\r|\n){2,}/i', '$1', $post );

			// Replaces linebreaks with html breaks:
			$post			=	str_replace( array( "\n", "\r\n" ), '<br />', $post );

			$cache[$id]		=	$post;
		}

		return $cache[$id];
	}
}