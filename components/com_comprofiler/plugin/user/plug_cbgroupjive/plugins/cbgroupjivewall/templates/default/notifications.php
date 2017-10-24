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
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\Table\NotificationTable;
use CB\Plugin\GroupJive\CBGroupJive;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveWallNotifications
{

	/**
	 * render frontend group wall notify params
	 *
	 * @param NotificationTable $row
	 * @param array             $input
	 * @param GroupTable        $group
	 * @param UserTable         $user
	 * @param cbPluginHandler   $plugin
	 * @return string
	 */
	static function showWallNotifications( $row, $input, $group, $user, $plugin )
	{
		$isModerator	=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$isOwner		=	( $user->get( 'id' ) == $group->get( 'user_id' ) );
		$status			=	CBGroupJive::getGroupStatus( $user, $group );

		$return			=	'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
						.		'<label for="params__wall_new" class="col-sm-9 control-label">' . CBTxt::T( 'Create of new post' ) . '</label>'
						.		'<div class="cb_field col-sm-3 text-right">'
						.			$input['wall_new']
						.		'</div>'
						.	'</div>';

		if ( ( $isModerator || $isOwner || ( $status >= 2 ) ) && ( $group->params()->get( 'wall', 1 ) == 2 ) ) {
			$return		.=	'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
						.		'<label for="params__wall_approve" class="col-sm-9 control-label">' . CBTxt::T( 'New post requires approval' ) . '</label>'
						.		'<div class="cb_field col-sm-3 text-right">'
						.			$input['wall_approve']
						.		'</div>'
						.	'</div>';
		}

		if ( $plugin->params->get( 'groups_wall_replies', 1 ) ) {
			$return		.=	'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
						.		'<label for="params__wall_reply" class="col-sm-9 control-label">' . CBTxt::T( 'User reply to my existing posts' ) . '</label>'
						.		'<div class="cb_field col-sm-3 text-right">'
						.			$input['wall_reply']
						.		'</div>'
						.	'</div>';
		}

		return $return;
	}
}