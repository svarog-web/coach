<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveVideo;

use CB\Plugin\GroupJiveVideo\Table\VideoTable;

defined('CBLIB') or die();

class CBGroupJiveVideo
{

	/**
	 * returns a cached video object or adds existing videos to the cache
	 *
	 * @param int|VideoTable[] $id
	 * @return VideoTable|null
	 */
	static public function getVideo( $id )
	{
		static $cache			=	array();

		if ( is_array( $id ) ) {
			foreach ( $id as $row ) {
				/** @var VideoTable $row */
				$rowId			=	(int) $row->get( 'id' );

				if ( ! $rowId ) {
					continue;
				}

				$cache[$rowId]	=	$row;
			}

			return null;
		} elseif ( ! $id ) {
			return new VideoTable();
		} elseif ( ! isset( $cache[$id] ) ) {
			$row				=	new VideoTable();

			$row->load( (int) $id );

			$cache[$id]			=	$row;
		}

		return $cache[$id];
	}
}