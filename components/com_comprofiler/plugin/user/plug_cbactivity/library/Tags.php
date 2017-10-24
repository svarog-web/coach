<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Table\TagTable;

defined('CBLIB') or die();

class Tags extends Stream implements TagsInterface
{
	/** @var string $endpoint */
	protected $endpoint		=	'tags';

	/**
	 * Retrieves tag stream data rows or row count
	 *
	 * @param bool  $count
	 * @param array $where
	 * @param array $join
	 * @return TagTable[]|int
	 */
	public function data( $count = false, $where = array(), $join = array() )
	{
		global $_CB_database, $_PLUGINS;

		static $cache					=	array();

		$_PLUGINS->trigger( 'activity_onQueryTags', array( $count, &$where, &$join, &$this ) );

		$useWhere						=	true;

		$query							=	'SELECT ' . ( $count ? 'COUNT( a.' . $_CB_database->NameQuote( 'id' ) . ' )' : 'a.*' )
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_tags' ) . " AS a"
										.	( $join ? "\n " . implode( "\n ", $join ) : null );

		if ( $this->get( 'id' ) ) {
			$query						.=	( $useWhere ? "\n WHERE " : "\n AND " ) . "a." . $_CB_database->NameQuote( 'id' ) . " = " . (int) $this->get( 'id', null, GetterInterface::INT );

			$useWhere					=	false;
		}

		if ( $this->get( 'type' ) ) {
			$query						.=	( $useWhere ? "\n WHERE " : "\n AND " ) . "a." . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $this->get( 'type', null, GetterInterface::STRING ) );

			$useWhere					=	false;
		}

		if ( $this->get( 'subtype' ) ) {
			$query						.=	( $useWhere ? "\n WHERE " : "\n AND " ) . "a." . $_CB_database->NameQuote( 'subtype' ) . " = " . $_CB_database->Quote( $this->get( 'subtype', null, GetterInterface::STRING ) );

			$useWhere					=	false;
		}

		if ( $this->get( 'item' ) ) {
			$query						.=	( $useWhere ? "\n WHERE " : "\n AND " ) . "a." . $_CB_database->NameQuote( 'item' ) . " = " . $_CB_database->Quote( $this->get( 'item', null, GetterInterface::STRING ) );

			$useWhere					=	false;
		}

		if ( $this->get( 'parent' ) ) {
			$query						.=	( $useWhere ? "\n WHERE " : "\n AND " ) . "a." . $_CB_database->NameQuote( 'parent' ) . " = " . $_CB_database->Quote( $this->get( 'parent', null, GetterInterface::STRING ) );

			$useWhere					=	false;
		}

		$query							.=	( $where ? ( $useWhere ? "\n WHERE " : "\n AND " ) . implode( "\n AND ", $where ) : null )
										.	( ! $count ? "\n ORDER BY a." . $_CB_database->NameQuote( 'date' ) . " ASC" : null );

		$cacheId						=	md5( $query . ( $count ? 'count' : null ) );

		if ( ( ! isset( $cache[$cacheId] ) ) || ( ( $count && $this->resetCount ) || $this->resetSelect ) ) {
			if ( $count ) {
				$this->resetCount		=	false;

				$_CB_database->setQuery( $query );

				$cache[$cacheId]		=	(int) $_CB_database->loadResult();
			} else {
				$this->resetSelect		=	false;

				$_CB_database->setQuery( $query );

				$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\TagTable', array( $_CB_database ) );

				$_PLUGINS->trigger( 'activity_onLoadTags', array( &$rows, $this ) );

				$cache[$cacheId]		=	$rows;
			}
		}

		return $cache[$cacheId];
	}
}