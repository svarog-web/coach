<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\Table\ItemTable;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryItems
{

	/**
	 * @param ItemTable[]      $rows
	 * @param cbPageNav        $pageNav
	 * @param UserTable        $viewer
	 * @param Gallery          $gallery
	 * @param CBplug_cbgallery $plugin
	 * @param string           $output
	 * @return string
	 */
	static public function showItems( $rows, $pageNav, $viewer, $gallery, $plugin, $output = null )
	{
		$return					=	'<div class="galleryItemsContainer">';

		if ( $rows ) {
			$rows				=	array_values( $rows );

			/** @var ItemTable[] $rows */
			foreach ( $rows as $i => $row ) {
				if ( $pageNav->total > 1 ) {
					$previous	=	( $i == 0 ? ( count( $rows ) - 1 ) : ( $i - 1 ) );

					if ( isset( $rows[$previous] ) ) {
						$row->set( '_previous', '.galleryContainer' . htmlspecialchars( $gallery->id() ) . $rows[$previous]->get( 'id', 0, GetterInterface::INT ) );
					}

					$next		=	( ( $i + 1 ) <= ( count( $rows ) - 1 )  ? ( $i + 1 ) : 0 );

					if ( isset( $rows[$next] ) ) {
						$row->set( '_next', '.galleryContainer' . htmlspecialchars( $gallery->id() ) . $rows[$next]->get( 'id', 0, GetterInterface::INT ) );
					}
				}

				$return			.=		HTML_cbgalleryItemContainer::showItemContainer( $row, $viewer, $gallery, $plugin, $output );
			}
		} else {
			$return				.=		'<div class="galleryItemsEmpty">';

			if ( $gallery->get( 'search', null, GetterInterface::STRING ) != '' ) {
				$return			.=			CBTxt::T( 'No gallery search results found.' );
			} else {
				if ( $gallery->get( 'folder', 0, GetterInterface::INT ) ) {
					$return		.=			CBTxt::T( 'This album is currently empty.' );
				} else {
					$return		.=			CBTxt::T( 'This gallery is currently empty.' );
				}
			}

			$return				.=		'</div>';
		}

		if ( $gallery->get( 'items_paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return				.=		'<div class="galleryItemsPaging text-center">'
								.			$pageNav->getListLinks()
								.		'</div>';
		}

		$return					.=		$pageNav->getLimitBox( false )
								.	'</div>';

		return $return;
	}
}