<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJivePhoto;

use CB\Plugin\GroupJivePhoto\Table\PhotoTable;

defined('CBLIB') or die();

class CBGroupJivePhoto
{

	/**
	 * returns a cached photo object or adds existing photos to the cache
	 *
	 * @param int|PhotoTable[] $id
	 * @return PhotoTable|null
	 */
	static public function getPhoto( $id )
	{
		static $cache			=	array();

		if ( is_array( $id ) ) {
			foreach ( $id as $row ) {
				/** @var PhotoTable $row */
				$rowId			=	(int) $row->get( 'id' );

				if ( ! $rowId ) {
					continue;
				}

				$cache[$rowId]	=	$row;
			}

			return null;
		} elseif ( ! $id ) {
			return new PhotoTable();
		} elseif ( ! isset( $cache[$id] ) ) {
			$row				=	new PhotoTable();

			$row->load( (int) $id );

			$cache[$id]			=	$row;
		}

		return $cache[$id];
	}
}