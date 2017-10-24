<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJiveWall\Table\WallTable;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveWallActivity
{

	/**
	 * render frontend wall activity
	 *
	 * @param ActivityTable   $row
	 * @param string          $title
	 * @param string          $message
	 * @param Activity        $stream
	 * @param WallTable       $post
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showWallActivity( $row, &$title, &$message, $stream, $post, $plugin )
	{
		global $_CB_framework;

		$title		=	CBTxt::T( 'GROUP_WALL_ACTIVITY_TITLE', 'posted in [group]', array( '[group]' => '<strong><a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $post->group()->get( 'id' ) ) ) . '">' . htmlspecialchars( CBTxt::T( $post->group()->get( 'name' ) ) ) . '</a></strong>' ) );
		$message	=	$post->post();

		return null;
	}
}