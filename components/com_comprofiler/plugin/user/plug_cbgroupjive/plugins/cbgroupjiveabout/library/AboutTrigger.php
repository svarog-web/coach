<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveAbout;

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\GroupTable;

defined('CBLIB') or die();

class AboutTrigger extends \cbPluginHandler
{

	/**
	 * prepare frontend about render
	 *
	 * @param string     $return
	 * @param GroupTable $group
	 * @param string     $users
	 * @param string     $invites
	 * @param array      $counters
	 * @param array      $buttons
	 * @param array      $menu
	 * @param \cbTabs    $tabs
	 * @param UserTable  $user
	 * @return array|null
	 */
	public function showAbout( &$return, &$group, &$users, &$invites, &$counters, &$buttons, &$menu, &$tabs, $user )
	{
		global $_CB_framework;

		if ( CBGroupJive::isModerator( $user->get( 'id' ) ) || ( ( $group->get( 'published' ) == 1 ) && ( CBGroupJive::getGroupStatus( $user, $group ) >= 3 ) ) ) {
			$menu[]		=	'<a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'about', 'func' => 'edit', 'id' => (int) $group->get( 'id' ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'About' ) . '</a>';
		}

		$about			=	trim( $group->params()->get( 'about_content' ) );

		if ( ( ! $about ) || ( $about == '<p></p>' ) ) {
			return null;
		}

		CBGroupJive::getTemplate( 'about', true, true, $this->element );

		if ( $this->params->get( 'groups_about_content_plugins', 0 ) ) {
			$about		=	Application::Cms()->prepareHtmlContentPlugins( $about, 'groupjive.about', $user->get( 'id', 0, GetterInterface::INT ) );
		}

		if ( $this->params->get( 'groups_about_substitutions', 0 ) ) {
			$about		=	\CBuser::getInstance( (int) $user->get( 'id' ), false )->replaceUserVars( $about, false, false, null, false );
		}

		return array(	'id'		=>	'about',
						'title'		=>	CBTxt::T( 'About' ),
						'content'	=>	\HTML_groupjiveAbout::showAbout( $about, $group, $user, $this )
					);
	}
}