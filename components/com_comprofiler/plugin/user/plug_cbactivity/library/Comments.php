<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\Activity\Table\CommentTable;

defined('CBLIB') or die();

class Comments extends StreamDirection implements CommentsInterface
{
	/** @var string $endpoint */
	protected $endpoint		=	'comments';

	/**
	 * Constructor for stream object
	 *
	 * @param null|string    $source
	 * @param null|UserTable $user
	 * @param null|int       $direction 0: down, 1: up
	 */
	public function __construct( $source = null, $user = null, $direction = null )
	{
		global $_PLUGINS;

		parent::__construct( $source, $user, $direction );

		static $params		=	null;

		if ( ! $params ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );
			$params			=	$_PLUGINS->getPluginParams( $plugin );
		}

		// Comments
		$this->set( 'paging', (int) $params->get( 'comments_paging', 1 ) );
		$this->set( 'limit', (int) $params->get( 'comments_limit', 30 ) );
		$this->set( 'create_access', (int) $params->get( 'comments_create_access', 2 ) );
		$this->set( 'message_limit', (int) $params->get( 'comments_message_limit', 400 ) );

		// Replies
		$this->set( 'replies', (int) $params->get( 'comments_replies', 0 ) );
		$this->set( 'replies_paging', (int) $params->get( 'comments_replies_paging', 1 ) );
		$this->set( 'replies_limit', (int) $params->get( 'comments_replies_limit', 4 ) );
		$this->set( 'replies_create_access', (int) $params->get( 'comments_replies_create_access', 2 ) );
		$this->set( 'replies_message_limit', (int) $params->get( 'comments_replies_message_limit', 400 ) );
	}

	/**
	 * Retrieves comment stream data rows or row count
	 *
	 * @param bool  $count
	 * @param array $where
	 * @param array $join
	 * @return CommentTable[]|int
	 */
	public function data( $count = false, $where = array(), $join = array() )
	{
		global $_CB_database, $_PLUGINS;

		static $cache					=	array();

		$whereCache						=	$where;
		$joinCache						=	$join;

		if ( $count ) {
			$select						=	'COUNT( a.' . $_CB_database->NameQuote( 'id' ) . ' )';
		} else {
			$select						=	'a.*';
		}

		$_PLUGINS->trigger( 'activity_onQueryComments', array( $count, &$select, &$where, &$join, &$this ) );

		$query							=	'SELECT ' . $select
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' ) . " AS a"
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_hidden' ) . " AS b"
										.	' ON b.' . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'comment' )
										.	' AND b.' . $_CB_database->NameQuote( 'item' ) . ' = a.' . $_CB_database->NameQuote( 'id' );

		if ( $this->source != 'hidden' ) {
			$query						.=	' AND b.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) Application::MyUser()->getUserId();
		}

		$query							.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
										.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'user_id' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS d"
										.	' ON d.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
										.	( $join ? "\n " . implode( "\n ", $join ) : null );

		if ( $this->source == 'hidden' ) {
			$query						.=	"\n WHERE b." . $_CB_database->NameQuote( 'id' ) . " IS NOT NULL"
										.	"\n AND b." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) Application::MyUser()->getUserId();
		} else {
			$query						.=	"\n WHERE b." . $_CB_database->NameQuote( 'id' ) . " IS NULL";
		}

		$query							.=	"\n AND c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
										.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
										.	"\n AND d." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( $this->get( 'id' ) ) {
			$query						.=	"\n AND a." . $_CB_database->NameQuote( 'id' ) . " = " . (int) $this->get( 'id', null, GetterInterface::INT );
		}

		if ( $this->get( 'type' ) ) {
			$query						.=	"\n AND a." . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $this->get( 'type', null, GetterInterface::STRING ) );
		}

		if ( $this->get( 'subtype' ) ) {
			$query						.=	"\n AND a." . $_CB_database->NameQuote( 'subtype' ) . " = " . $_CB_database->Quote( $this->get( 'subtype', null, GetterInterface::STRING ) );
		}

		if ( $this->get( 'item' ) ) {
			$query						.=	"\n AND a." . $_CB_database->NameQuote( 'item' ) . " = " . $_CB_database->Quote( $this->get( 'item', null, GetterInterface::STRING ) );
		}

		if ( $this->get( 'parent' ) ) {
			$query						.=	"\n AND a." . $_CB_database->NameQuote( 'parent' ) . " = " . $_CB_database->Quote( $this->get( 'parent', null, GetterInterface::STRING ) );
		}

		if ( $this->get( 'filter' ) ) {
			$query						.=	"\n AND a." . $_CB_database->NameQuote( 'message' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'filter', null, GetterInterface::STRING ), true ) . '%', false );
		}

		$query							.=	( $where ? "\n AND " . implode( "\n AND ", $where ) : null )
										.	( ! $count ? "\n ORDER BY a." . $_CB_database->NameQuote( 'date' ) . " DESC" : null );

		$cacheId						=	md5( $query . ( $count ? 'count' : (int) $this->get( 'limitstart', null, GetterInterface::INT ) . (int) $this->get( 'limit', null, GetterInterface::INT ) ) );

		if ( ( ! isset( $cache[$cacheId] ) ) || ( ( $count && $this->resetCount ) || $this->resetSelect ) ) {
			if ( $count ) {
				$this->resetCount		=	false;

				$_CB_database->setQuery( $query );

				$cache[$cacheId]		=	(int) $_CB_database->loadResult();
			} else {
				$this->resetSelect		=	false;

				if ( $this->get( 'limit' ) ) {
					$_CB_database->setQuery( $query, (int) $this->get( 'limitstart', null, GetterInterface::INT ), (int) $this->get( 'limit', null, GetterInterface::INT ) );
				} else {
					$_CB_database->setQuery( $query );
				}

				$rows					=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\CommentTable', array( $_CB_database ) );
				$rowsCount				=	count( $rows );

				$_PLUGINS->trigger( 'activity_onLoadComments', array( &$rows, $this ) );

				if ( $this->get( 'limit' ) && $rowsCount && ( count( $rows ) < $rowsCount ) ) {
					$directionCache		=	$this->direction;
					$limitCache			=	(int) $this->get( 'limit', null, GetterInterface::INT );
					$nextLimit			=	( $limitCache - count( $rows ) );

					if ( $nextLimit <= 0 ) {
						$nextLimit		=	1;
					}

					$this->set( 'limitstart', ( (int) $this->get( 'limitstart', null, GetterInterface::INT ) + $limitCache ) );
					$this->set( 'limit', $nextLimit );

					$this->direction	=	0;

					$cache[$cacheId]	=	( $rows + $this->data( $whereCache, $joinCache ) );

					$this->direction	=	$directionCache;

					$this->set( 'limit', $limitCache );
				} else {
					$cache[$cacheId]	=	$rows;
				}
			}
		}

		$rows							=	$cache[$cacheId];

		if ( $this->direction && ( ! $count ) ) {
			$rows						=	array_reverse( $rows );
		}

		return $rows;
	}
}