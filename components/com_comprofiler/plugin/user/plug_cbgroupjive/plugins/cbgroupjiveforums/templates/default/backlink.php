<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJive\Table\GroupTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveForumsBacklink
{

	/**
	 * render frontend forum backlink
	 *
	 * @param GroupTable      $group
	 * @param object          $category
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showBacklink( $group, $category, $plugin )
	{
		global $_CB_framework;

		$return		=	'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
					.		'<div class="gjBacklink text-right" style="margin-bottom: 5px;">'
					.			'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) . '\';" class="gjButton gjButtonBacklink btn btn-xs btn-default"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'Back to Group' ) . '</button>'
					.		'</div>'
					.	'</div>';

		return $return;
	}
}