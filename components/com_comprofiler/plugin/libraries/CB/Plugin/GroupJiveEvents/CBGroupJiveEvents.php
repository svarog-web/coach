<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveEvents;

use CB\Plugin\GroupJiveEvents\Table\EventTable;

defined('CBLIB') or die();

class CBGroupJiveEvents
{

	/**
	 * returns a cached event object or adds existing events to the cache
	 *
	 * @param int|EventTable[] $id
	 * @return EventTable|null
	 */
	static public function getEvent( $id )
	{
		static $cache			=	array();

		if ( is_array( $id ) ) {
			foreach ( $id as $row ) {
				/** @var EventTable $row */
				$rowId			=	(int) $row->get( 'id' );

				if ( ! $rowId ) {
					continue;
				}

				$cache[$rowId]	=	$row;
			}

			return null;
		} elseif ( ! $id ) {
			return new EventTable();
		} elseif ( ! isset( $cache[$id] ) ) {
			$row				=	new EventTable();

			$row->load( (int) $id );

			$cache[$id]			=	$row;
		}

		return $cache[$id];
	}
}