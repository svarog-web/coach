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
use CB\Plugin\GroupJiveEvents\Table\EventTable;
use CB\Plugin\GroupJiveEvents\Table\AttendanceTable;
use CB\Plugin\GroupJive\CBGroupJive;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveAttending
{

	/**
	 * render frontend event attending
	 *
	 * @param AttendanceTable[]        $rows
	 * @param cbPageNav                $pageNav
	 * @param bool                     $searching
	 * @param array                    $input
	 * @param EventTable               $event
	 * @param UserTable                $user
	 * @param CBplug_cbgroupjiveevents $plugin
	 */
	static function showAttending( $rows, $pageNav, $searching, $input, $event, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		initToolTip();

		$canSearch						=	( $plugin->params->get( 'groups_events_attending_search', 0 ) && ( $searching || $pageNav->total ) );
		$returnUrl						=	CBGroupJive::getReturn( true, true );

		if ( ! $returnUrl ) {
			$returnUrl					=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $event->group()->get( 'id' ) ) );
		}

		$return							=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayAttending', array( &$return, &$rows, $event, $user ) );

		$return							.=	'<div class="gjEventAttending">'
										.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'attending', 'id' => (int) $event->get( 'id' ) ) ) . '" method="post" name="gjEventAttendingForm" id="gjEventAttendingForm" class="gjEventAttendingForm">'
										.			'<div class="gjEventAttendingTitle page-header"><h3>' . htmlspecialchars( $event->get( 'title' ) ) . '</h3></div>';

		if ( $canSearch ) {
			$return						.=			'<div class="gjHeader gjEventAttendingHeader row">'
										.				'<div class="col-sm-offset-8 col-sm-4 text-right">'
										.					'<div class="input-group">'
										.						'<span class="input-group-addon"><span class="fa fa-search"></span></span>'
										.						$input['search']
										.					'</div>'
										.				'</div>'
										.			'</div>';
		}

		$return							.=			'<div class="gjEventAttendingRows">';

		if ( $rows ) foreach ( $rows as $row ) {
			$cbUser						=	CBuser::getInstance( (int) $row->get( 'user_id' ), false );

			$return						.=				'<div class="gjEventAttendingUser gjCanvasBox cbCanvasBox cbCanvasBoxSm img-thumbnail">'
										.					'<div class="gjCanvasBoxTop cbCanvasBoxTop bg-muted">'
										.						'<div class="gjCanvasBoxBackground cbCanvasBoxBackground">'
										.							$cbUser->getField( 'canvas', null, 'html', 'none', 'profile', 0, true )
										.						'</div>'
										.						'<div class="gjCanvasBoxPhoto cbCanvasBoxPhoto cbCanvasBoxPhotoLeft">'
										.							$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
										.						'</div>'
										.					'</div>'
										.					'<div class="gjCanvasBoxBottom cbCanvasBoxBottom bg-default">'
										.						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-nowrap text-overflow">'
										.							$cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) )
										.							' ' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) )
										.						'</div>'
										.					'</div>'
										.				'</div>';
		} else {
			if ( $searching ) {
				$return					.=				CBTxt::T( 'No event guest search results found.' );
			} else {
				$return					.=				CBTxt::T( 'This event currently has no guests.' );
			}
		}

		$return							.=			'</div>';

		if ( $plugin->params->get( 'groups_events_attending_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return						.=			'<div class="gjEventAttendingPaging text-center">'
										.				$pageNav->getListLinks()
										.			'</div>';
		}

		$return							.=			$pageNav->getLimitBox( false )
										.			'<div class="form-group cb_form_line clearfix text-right">'
										.				'<input type="button" value="' . htmlspecialchars( CBTxt::T( 'Back' ) ) . '" class="gjButton gjButtonCancel btn btn-default" onclick="window.location.href = \'' . addslashes( htmlspecialchars( $returnUrl ) ) . '\';" />'
										.			'</div>'
										.		'</form>'
										.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayAttending', array( &$return, &$rows, $event, $user ) );

		echo $return;
	}
}