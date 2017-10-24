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
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\GroupTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjivePhotoParams
{

	/**
	 * render frontend group edit photo params
	 *
	 * @param GroupTable      $row
	 * @param array           $input
	 * @param CategoryTable   $category
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showPhotoParams( $row, $input, $category, $user, $plugin )
	{
		$return		=	'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
					.		'<label for="params__photo" class="col-sm-3 control-label">' . CBTxt::T( 'Photos' ) . '</label>'
					.		'<div class="cb_field col-sm-9">'
					.			$input['photo']
					.			getFieldIcons( null, 0, null, CBTxt::T( 'Optionally enable or disable usage of photos. Group owner and group administrators are exempt from this configuration and can always upload photos. Note existing photos will still be accessible.' ) )
					.		'</div>'
					.	'</div>';

		return $return;
	}
}