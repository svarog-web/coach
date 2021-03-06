<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJivePhoto\Trigger;

use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class AdminTrigger extends \cbPluginHandler
{

	/**
	 * Displays backend menu items
	 *
	 * @param array $menu
	 */
	public function adminMenu( &$menu )
	{
		global $_CB_framework;

		$prevStateBase		=	'option=com_comprofiler&view=editPlugin&pluginid=' . $this->getPluginId();

		$menu[]				=	array(	'title' => CBTxt::T( 'Photos' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showgjphotos', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbgj-photos',
										'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Photo to Group' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'gjphotosbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showgjphotos' ) ) ), 'icon' => 'cb-new' ),
															array( 'title' => CBTxt::T( 'Configuration' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'pluginsbrowser', 'action' => 'editrow', 'cid' => $this->getPluginId(), 'cbprevstate' => base64_encode( 'option=com_comprofiler&view=showPlugins' ) ) ), 'icon' => 'cbgj-photosconfig' )
										)
									);
	}
}