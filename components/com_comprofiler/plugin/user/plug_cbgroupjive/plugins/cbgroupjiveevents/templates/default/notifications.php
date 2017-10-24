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

class HTML_groupjiveEventNotifications
{

	/**
	 * render frontend group event notify params
	 *
	 * @param NotificationTable $row
	 * @param array             $input
	 * @param GroupTable        $group
	 * @param UserTable         $user
	 * @param cbPluginHandler   $plugin
	 * @return string
	 */
	static function showEventNotifications( $row, $input, $group, $user, $plugin )
	{
		$isModerator	=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$isOwner		=	( $user->get( 'id' ) == $group->get( 'user_id' ) );
		$status			=	CBGroupJive::getGroupStatus( $user, $group );

		$return			=	'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
						.		'<label for="params__event_new" class="col-sm-9 control-label">' . CBTxt::T( 'Schedule of new event' ) . '</label>'
						.		'<div class="cb_field col-sm-3 text-right">'
						.			$input['event_new']
						.		'</div>'
						.	'</div>'
						.	'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
						.		'<label for="params__event_edit" class="col-sm-9 control-label">' . CBTxt::T( 'Edit of existing event' ) . '</label>'
						.		'<div class="cb_field col-sm-3 text-right">'
						.			$input['event_edit']
						.		'</div>'
						.	'</div>';

		if ( ( $isModerator || $isOwner || ( $status >= 2 ) ) && ( $group->params()->get( 'events', 1 ) == 2 ) ) {
			$return		.=	'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
						.		'<label for="params__event_approve" class="col-sm-9 control-label">' . CBTxt::T( 'New event requires approval' ) . '</label>'
						.		'<div class="cb_field col-sm-3 text-right">'
						.			$input['event_approve']
						.		'</div>'
						.	'</div>';
		}

		$return			.=	'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
						.		'<label for="params__event_attend" class="col-sm-9 control-label">' . CBTxt::T( 'User attends my existing events' ) . '</label>'
						.		'<div class="cb_field col-sm-3 text-right">'
						.			$input['event_attend']
						.		'</div>'
						.	'</div>'
						.	'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
						.		'<label for="params__event_unattend" class="col-sm-9 control-label">' . CBTxt::T( 'User unattends my existing events' ) . '</label>'
						.		'<div class="cb_field col-sm-3 text-right">'
						.			$input['event_unattend']
						.		'</div>'
						.	'</div>';

		return $return;
	}
}