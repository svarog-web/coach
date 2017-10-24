<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveWall;

use CB\Plugin\GroupJiveWall\Table\WallTable;

defined('CBLIB') or die();

class CBGroupJiveWall
{

	/**
	 * returns a cached post object or adds existing posts to the cache
	 *
	 * @param int|WallTable[] $id
	 * @return WallTable|null
	 */
	static public function getPost( $id )
	{
		static $cache			=	array();

		if ( is_array( $id ) ) {
			foreach ( $id as $row ) {
				/** @var WallTable $row */
				$rowId			=	(int) $row->get( 'id' );

				if ( ! $rowId ) {
					continue;
				}

				$cache[$rowId]	=	$row;
			}

			return null;
		} elseif ( ! $id ) {
			return new WallTable();
		} elseif ( ! isset( $cache[$id] ) ) {
			$row				=	new WallTable();

			$row->load( (int) $id );

			$cache[$id]			=	$row;
		}

		return $cache[$id];
	}
}