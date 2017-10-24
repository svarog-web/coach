<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Table;

use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CB\Database\Table\UserTable;
use CB\Plugin\Activity\Comments;
use CBLib\Application\Application;

defined('CBLIB') or die();

class CommentTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var string  */
	public $type			=	null;
	/** @var string  */
	public $subtype			=	null;
	/** @var string  */
	public $item			=	null;
	/** @var string  */
	public $parent			=	null;
	/** @var string  */
	public $message			=	null;
	/** @var string  */
	public $date			=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_activity_comments';

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
		} elseif ( trim( $this->get( 'message' ) ) == '' ) {
			$this->setError( CBTxt::T( 'Message not specified!' ) );

			return false;
		} elseif ( preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'type' ) ) == '' ) {
			$this->setError( CBTxt::T( 'Type not specified!' ) );

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
		global $_CB_framework, $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		$this->set( 'type', preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'type' ) ) );
		$this->set( 'subtype', preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'subtype' ) ) );
		$this->set( 'date', $this->get( 'date', $_CB_framework->getUTCDate() ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'activity_onBeforeUpdateComment', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateComment', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateComment', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateComment', array( $this ) );
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

		$_PLUGINS->trigger( 'activity_onBeforeDeleteComment', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Deletes activity about this comment:
		$query				=	'SELECT *'
							.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity' )
							.	"\n WHERE " . $this->getDbo()->NameQuote( 'type' ) . " = " . $this->getDbo()->Quote( 'activity' )
							.	"\n AND " . $this->getDbo()->NameQuote( 'subtype' ) . " = " . $this->getDbo()->Quote( 'comment' )
							.	"\n AND " . $this->getDbo()->NameQuote( 'item' ) . " = " . (int) $this->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$activities			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\ActivityTable', array( $this->getDbo() ) );

		/** @var ActivityTable[] $activities */
		foreach ( $activities as $activity ) {
			$activity->delete();
		}

		// Deletes comment replies:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_comments' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'type' ) . " = " . $this->getDbo()->Quote( 'comment' )
						.	"\n AND " . $this->getDbo()->NameQuote( 'item' ) . " = " . (int) $this->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$replies		=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\CommentTable', array( $this->getDbo() ) );

		/** @var CommentTable[] $replies */
		foreach ( $replies as $reply ) {
			$reply->delete();
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteComment', array( $this ) );

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
	 * @param string         $source
	 * @param null|UserTable $user
	 * @param int            $direction
	 * @return Comments|null
	 */
	public function replies( $source = 'stream', $user = null, $direction = 1 )
	{
		$params				=	$this->params()->subTree( 'replies' );

		if ( ! $params->get( 'display', 1 ) ) {
			return null;
		}

		/** @var Comments[] $cache */
		static $cache		=	array();

		$id					=	md5( $this->get( 'id' ) . $source . ( $user ? $user->get( 'id' ) : null ) . $direction . Application::MyUser()->getUserId() );

		if ( ! isset( $cache[$id] ) ) {
			$stream			=	new Comments( $source, $user, $direction );

			$stream->set( 'type', 'comment' );
			$stream->set( 'item', (int) $this->get( 'id' ) );

			$object			=	array(	'source'	=>	'comment',
										'id'		=>	(int) $this->get( 'id' ),
										'user_id'	=>	(int) $this->get( 'user_id' ),
										'type'		=>	$this->get( 'type' ),
										'subtype'	=>	$this->get( 'subtype' ),
										'item'		=>	$this->get( 'item' ),
										'parent'	=>	$this->get( 'parent' )
									);

			$stream->set( 'object', $object );

			$cache[$id]		=	$stream;
		}

		return $cache[$id];
	}
}