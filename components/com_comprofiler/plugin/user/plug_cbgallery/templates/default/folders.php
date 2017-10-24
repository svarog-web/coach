<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\Table\FolderTable;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbgalleryFolders
{

	/**
	 * @param FolderTable[]    $rows
	 * @param cbPageNav        $pageNav
	 * @param UserTable        $viewer
	 * @param Gallery          $gallery
	 * @param CBplug_cbgallery $plugin
	 * @param string           $output
	 * @return string
	 */
	static public function showFolders( $rows, $pageNav, $viewer, $gallery, $plugin, $output = null )
	{
		$return			=	'<div class="galleryFoldersContainer">';

		if ( $rows ) foreach ( $rows as $row ) {
			$return		.=		HTML_cbgalleryFolderContainer::showFolderContainer( $row, $viewer, $gallery, $plugin, $output );
		}

		if ( $gallery->get( 'folders_paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return		.=		'<div class="galleryFoldersPaging text-center">'
						.			$pageNav->getListLinks()
						.		'</div>';
		}

		$return			.=		$pageNav->getLimitBox( false )
						.	'</div>';

		return $return;
	}
}