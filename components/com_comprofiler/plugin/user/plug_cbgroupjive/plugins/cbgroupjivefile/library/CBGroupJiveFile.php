<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveFile;

use CB\Plugin\GroupJiveFile\Table\FileTable;

defined('CBLIB') or die();

class CBGroupJiveFile
{

	/**
	 * returns a cached file object or adds existing files to the cache
	 *
	 * @param int|FileTable[] $id
	 * @return FileTable|null
	 */
	static public function getFile( $id )
	{
		static $cache			=	array();

		if ( is_array( $id ) ) {
			foreach ( $id as $row ) {
				/** @var FileTable $row */
				$rowId			=	(int) $row->get( 'id' );

				if ( ! $rowId ) {
					continue;
				}

				$cache[$rowId]	=	$row;
			}

			return null;
		} elseif ( ! $id ) {
			return new FileTable();
		} elseif ( ! isset( $cache[$id] ) ) {
			$row				=	new FileTable();

			$row->load( (int) $id );

			$cache[$id]			=	$row;
		}

		return $cache[$id];
	}
}