<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\Table\GroupTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveAbout
{

	/**
	 * render frontend about
	 *
	 * @param string          $about
	 * @param GroupTable      $group
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showAbout( $about, $group, $user, $plugin )
	{
		global $_PLUGINS;

		$return			=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayAbout', array( &$return, &$about, $group, $user ) );

		$return			.=	'<div class="gjGroupAbout">'
						.		$about
						.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayAbout', array( &$return, $about, $group, $user ) );

		return $return;
	}
}