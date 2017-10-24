<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive\Trigger;

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
		global $_CB_framework, $_PLUGINS;

		if ( ! $this->params->get( 'general_menu', true, GetterInterface::BOOLEAN ) ) {
			return;
		}

		$prevStateBase			=	'option=com_comprofiler&view=editPlugin&pluginid=' . $this->getPluginId();

		$gjMenu					=	array();

		$gjMenu['component']	=	array(	'title' => CBTxt::T( 'GroupJive' ) );
		$gjMenu['menu']			=	array(	array(	'title' => CBTxt::T( 'Categories' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showgjcategories', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbgj-categories',
													'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Category' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'gjcategoriesbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showgjcategories' ) ) ), 'icon' => 'cb-new' ) )
											),
											array(	'title' => CBTxt::T( 'Groups' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showgjgroups', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbgj-groups',
													'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Group' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'gjgroupsbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showgjgroups' ) ) ), 'icon' => 'cb-new' ) )
											),
											array(	'title' => CBTxt::T( 'GROUP_USERS', 'Users' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showgjusers', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbgj-users',
													'submenu' => array( array( 'title' => CBTxt::Th( 'Add New User to Group' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'gjusersbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showgjusers' ) ) ), 'icon' => 'cb-new' ) )
											),
											array(	'title' => CBTxt::T( 'Invites' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showgjinvites', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbgj-invites',
													'submenu' => array( array( 'title' => CBTxt::Th( 'Invite New User to Group' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'gjinvitesbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showgjinvites' ) ) ), 'icon' => 'cb-new' ) )
											)
										);

		$_PLUGINS->trigger( 'gj_onAdminMenu', array( &$gjMenu['menu'] ) );

		$gjMenu['menu'][]		=	array(	'title' => CBTxt::T( 'Notifications' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showgjnotifications', 'cid' => $this->getPluginId() ) ), 'icon' => 'cbgj-invites' );
		$gjMenu['menu'][]		=	array(	'title' => CBTxt::T( 'Configuration' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'pluginsbrowser', 'action' => 'editrow', 'cid' => $this->getPluginId(), 'cbprevstate' => base64_encode( 'option=com_comprofiler&view=showPlugins' ) ) ), 'icon' => 'cbgj-config' );

		$menu['gj']				=	$gjMenu;
	}
}