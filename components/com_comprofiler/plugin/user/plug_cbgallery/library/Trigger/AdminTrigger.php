<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery\Trigger;

use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class AdminTrigger extends \cbPluginHandler
{

	/**
	 * Displays backend menu items
	 *
	 * @param array $menu
	 * @param bool  $disabled
	 */
	public function adminMenu( &$menu, $disabled )
	{
		global $_CB_framework;

		if ( ! $this->params->get( 'general_menu', true, GetterInterface::BOOLEAN ) ) {
			return;
		}

		$prevStateBase				=	'option=com_comprofiler&view=editPlugin&pluginid=' . $this->getPluginId();

		$galleryMenu				=	array();

		$galleryMenu['component']	=	array(	'title' => CBTxt::T( 'Gallery' ) );
		$galleryMenu['menu']		=	array(	array(	'title' => CBTxt::T( 'Albums' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showfolders', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbgallery-folders',
														'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Album' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'folderbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showfolders' ) ) ), 'icon' => 'cb-new' ) )
												),
												array(	'title' => CBTxt::T( 'Media' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showitems', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbgallery-items',
														'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Media' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'itembrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showitems' ) ) ), 'icon' => 'cb-new' ) )
												),
												array(	'title' => CBTxt::T( 'Configuration' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'pluginsbrowser', 'action' => 'editrow', 'cid' => $this->getPluginId(), 'cbprevstate' => base64_encode( 'option=com_comprofiler&view=showPlugins' ) ) ), 'icon' => 'cbgj-config' )
											);

		$menu['gallery']			=	$galleryMenu;
	}
}