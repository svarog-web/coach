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
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveEvents\Table\EventTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveEventEdit
{

	/**
	 * render frontend event edit
	 *
	 * @param EventTable               $row
	 * @param array                    $input
	 * @param GroupTable               $group
	 * @param UserTable                $user
	 * @param CBplug_cbgroupjiveevents $plugin
	 */
	static function showEventEdit( $row, $input, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$pageTitle					=	( $row->get( 'id' ) ? CBTxt::T( 'Edit Event' ) : CBTxt::T( 'New Event' ) );

		$_CB_framework->setPageTitle( $pageTitle );

		cbValidator::loadValidation();
		initToolTip();

		$isModerator				=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$canModerate				=	( CBGroupJive::getGroupStatus( $user, $group ) >= 2 );
		$return						=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayEventEdit', array( &$return, &$row, &$input, $group, $user ) );

		$return						.=	'<div class="gjEventEdit">'
									.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'save', 'id' => $row->get( 'id', null, GetterInterface::INT ) ) ) . '" method="post" enctype="multipart/form-data" name="gjEventEditForm" id="gjEventEditForm" class="cb_form gjEventEditForm form-auto cbValidation">'
									.			( $pageTitle ? '<div class="gjEventEditTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null );

		if ( $isModerator || $canModerate || ( $row->get( 'id' ) && ( $row->get( 'published' ) != -1 ) ) || ( $group->params()->get( 'events', 1 ) != 2 ) ) {
			$return					.=			'<div class="cbft_select cbtt_select form-group cb_form_line clearfix">'
									.				'<label for="published" class="col-sm-3 control-label">' . CBTxt::T( 'Published' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['published']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Select publish state of this event. Unpublished events will not be visible to the public.' ) )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="title" class="col-sm-3 control-label">' . CBTxt::T( 'Title' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['title']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Input the event title. This is the title that will distinguish this event from others. Suggested to input something to uniquely identify your event.' ) )
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
									.				'<label for="event" class="col-sm-3 control-label">' . CBTxt::T( 'Event' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['event']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Input a detailed description about this event.' ) )
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="location" class="col-sm-3 control-label">' . CBTxt::T( 'Location' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['location']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Input the location for this event (e.g. My House, The Park, Restaurant Name, etc..).' ) )
									.				'</div>'
									.			'</div>';

		if ( $plugin->params->get( 'groups_events_address', 1 ) ) {
			$js						=	"$( '.gjButtonLocation' ).on( 'click', function() {"
									.		"if ( typeof navigator.geolocation == 'undefined' ) {"
									.			"return null;"
									.		"}"
									.		"var input = $( this ).siblings( 'input' );"
									.		"navigator.geolocation.getCurrentPosition( function( position ) {"
									.			"input.val( position.coords.latitude + ',' + position.coords.longitude );"
									.		"});"
									.	"});";

			$_CB_framework->outputCbJQuery( $js );

			$return					.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="address" class="col-sm-3 control-label">' . CBTxt::T( 'Address' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					'<span class="gjEventEditAddress">'
									.						$input['address']
									.						'<button type="button" class="gjButton gjButtonLocation btn btn-default"><span class="fa fa-map-marker"></span></button>'
									.					'</span>'
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally input the address for this event or click the map button to attempt to find your current location.' ) )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<div class="cbft_datetime cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="start" class="col-sm-3 control-label">' . CBTxt::T( 'Start Date' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['start']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Select the date and time this event starts.' ) )
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_datetime cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="end" class="col-sm-3 control-label">' . CBTxt::T( 'End Date' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['end']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally select the end date and time for this event.' ) )
									.				'</div>'
									.			'</div>'
									.			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="limit" class="col-sm-3 control-label">' . CBTxt::T( 'Guest Limit' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['limit']
									.					getFieldIcons( null, 0, null, CBTxt::T( 'Optionally input a guest limit for this event.' ) )
									.				'</div>'
									.			'</div>';

		if ( $isModerator ) {
			$return					.=			'<div class="cbft_text cbtt_input form-group cb_form_line clearfix">'
									.				'<label for="user_id" class="col-sm-3 control-label">' . CBTxt::T( 'Owner' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					$input['user_id']
									.					getFieldIcons( null, 1, null, CBTxt::T( 'Input the event owner id. Event owner determines the creator of the event specified as User ID.' ) )
									.				'</div>'
									.			'</div>';
		}

		if ( ( ! $isModerator ) && $plugin->params->get( 'groups_events_captcha', 0 ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$captcha				=	$_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( false ) );

			if ( ! empty( $captcha ) ) {
				$captcha			=	$captcha[0];

				$return				.=			'<div class="form-group cb_form_line clearfix">'
									.				'<label class="col-sm-3 control-label">' . CBTxt::T( 'Captcha' ) . '</label>'
									.				'<div class="cb_field col-sm-9">'
									.					( isset( $captcha[0] ) ? $captcha[0] : null )
									.				'</div>'
									.			'</div>'
									.			'<div class="form-group cb_form_line clearfix">'
									.				'<div class="cb_field col-sm-offset-3 col-sm-9">'
									.					str_replace( 'inputbox', 'form-control', ( isset( $captcha[1] ) ? $captcha[1] : null ) )
									.					getFieldIcons( null, 1, null )
									.				'</div>'
									.			'</div>';
			}
		}

		$return						.=			'<div class="form-group cb_form_line clearfix cbtwolines">'
									.				'<div class="col-sm-offset-3 col-sm-9">'
									.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id' ) ? CBTxt::T( 'Update Event' ) : CBTxt::T( 'Schedule Event' ) ) ) . '" class="gjButton gjButtonSubmit btn btn-primary" ' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="gjButton gjButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) ) . '\'; })" />'
									.				'</div>'
									.			'</div>'
									.			( ! $row->get( 'id' ) ? '<input type="hidden" id="group" name="group" value="' . (int) $group->get( 'id' ) . '" />' : null )
									.			cbGetSpoofInputTag( 'plugin' )
									.		'</form>'
									.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayEventEdit', array( &$return, $row, $input, $group, $user ) );

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}