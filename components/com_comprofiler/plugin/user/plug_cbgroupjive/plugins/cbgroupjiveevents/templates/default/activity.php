<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveEvents\Table\EventTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveEventActivity
{

	/**
	 * render frontend event activity
	 *
	 * @param ActivityTable   $row
	 * @param string          $title
	 * @param string          $message
	 * @param Activity        $stream
	 * @param EventTable      $event
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showEventActivity( $row, &$title, &$message, $stream, $event, $plugin )
	{
		global $_CB_framework;

		$title					=	CBTxt::T( 'GROUP_EVENT_ACTIVITY_TITLE', 'scheduled an event in [group]', array( '[group]' => '<strong><a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $event->group()->get( 'id' ) ) ) . '">' . htmlspecialchars( CBTxt::T( $event->group()->get( 'name' ) ) ) . '</a></strong>' ) );
		$user					=	CBuser::getMyUserDataInstance();
		$userStatus				=	CBGroupJive::getGroupStatus( $user, $event->group() );
		$eventOwner				=	( $user->get( 'id' ) == $event->get( 'user_id' ) );
		$showAddress			=	$plugin->params->get( 'groups_events_address', 1 );
		$address				=	htmlspecialchars( $event->get( 'location' ) );

		if ( $showAddress ) {
			if ( $event->get( 'address' ) ) {
				$mapUrl			=	CBTxt::T( 'GROUP_EVENT_ADDRESS_MAP_URL', 'https://www.google.com/maps/place/[address]', array( '[location]' => urlencode( $event->get( 'location' ) ), '[address]' => urlencode( $event->get( 'address' ) ) ) );
			} else {
				$mapUrl			=	CBTxt::T( 'GROUP_EVENT_LOCATION_MAP_URL', 'https://www.google.com/maps/search/[location]', array( '[location]' => urlencode( $event->get( 'location' ) ), '[address]' => urlencode( $event->get( 'address' ) ) ) );
			}

			if ( $mapUrl ) {
				$address		=	'<a href="' . htmlspecialchars( $mapUrl ) . '" target="_blank" rel="nofollow">' . $address . '</a>';
			}
		}

		$canAttend				=	( ( ! $eventOwner ) && ( $event->status() != 1 ) && ( ! $event->get( '_attending' ) ) && ( $userStatus >= 1 ) && ( ( ! $event->get( 'limit' ) ) || ( $event->get( 'limit' ) && ( $event->get( '_guests' ) < $event->get( 'limit' ) ) ) ) );

		$return					=	'<div class="gjEventActivity">'
								.		'<div class="gjGroupEventsRow row' . ( $event->status() == 1 ? ' gjGroupEventExpired' : ( $event->status() == 2 ? ' gjGroupEventActive' : null ) ) . '">'
								.			'<div class="gjGroupEventCalendar col-md-2 hidden-sm hidden-xs">'
								.				'<div class="panel panel-default text-center">'
								.					'<div class="gjGroupEventMonth panel-body">' . cbFormatDate( $event->get( 'start' ), true, false, 'M' ) . '</div>'
								.					'<div class="gjGroupEventDay panel-footer">' . cbFormatDate( $event->get( 'start' ), true, false, 'j' ) . '</div>'
								.				'</div>'
								.			'</div>'
								.			'<div class="gjGroupEventContainer col-md-10 col-sm-12 col-xs-12">'
								.				'<div class="panel ' . ( $event->status() == 1 ? 'panel-warning' : ( $event->status() == 2 ? 'panel-primary' : 'panel-default' ) ) . '">'
								.					'<div class="gjGroupEventHeader panel-heading">'
								.						'<div class="row">'
								.							'<div class="gjGroupEventTitle ' . ( $canAttend ? 'col-sm-8' : 'col-sm-12' ) . '">' . htmlspecialchars( $event->get( 'title' ) ) . '</div>';

		if ( $canAttend ) {
			$return				.=							'<div class="gjGroupEventMenu col-sm-4 text-right">'
								.								'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'attend', 'id' => (int) $event->get( 'id' ) ) ) . '\';" class="gjButton gjButtonAttend btn btn-xs btn-success">' . CBTxt::T( 'Attend' ) . '</button>'
								.							'</div>';
		}

		$return					.=						'</div>'
								.					'</div>'
								.					'<div class="gjGroupEventDetails panel-body small">';

		if ( $event->status() == 1 ) {
			$return				.=						'<div class="gjGroupEventNotice text-warning text-right">' . CBTxt::T( 'This event has ended.' ) . '</div>';
		} elseif ( $event->status() == 2 ) {
			if ( $event->get( 'end' ) ) {
				$return			.=						'<div class="gjGroupEventNotice text-primary text-right">' . CBTxt::T( 'GROUP_EVENT_ENDS_IN', 'This event is currently in progress and ends in [timeago].', array( '[timeago]' => cbFormatDate( $event->get( 'end' ), true, 'exacttimeago' ) ) ) . '</div>';
			} else {
				$return			.=						'<div class="gjGroupEventNotice text-primary text-right">' . CBTxt::T( 'This event is currently in progress.' ) . '</div>';
			}
		} else {
			$return				.=						'<div class="gjGroupEventNotice text-right">' . CBTxt::T( 'GROUP_EVENT_STARTS_IN', 'This event starts in [timeago].', array( '[timeago]' => cbFormatDate( $event->get( 'start' ), true, 'exacttimeago' ) ) ) . '</div>';
		}

		$return					.=						'<div class="gjGroupEventDate">'
								.							'<span class="gjGroupEventIcon fa fa-clock-o text-center"></span> ' . $event->date()
								.						'</div>'
								.						'<div class="gjGroupEventLocation">'
								.							'<span class="gjGroupEventIcon fa fa-map-marker text-center"></span> ' . $address
								.						'</div>'
								.						'<div class="gjGroupEventAttending">'
								.							'<div class="gjGroupEventGuests">'
								.								'<span class="gjGroupEventIcon fa fa-users text-center"></span> '
								.								'<a href="' . htmlspecialchars( $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'events', 'func' => 'attending', 'id' => (int) $event->get( 'id' ), 'return' => CBGroupJive::getReturn() ) ) ) . '">'
								.									( $event->get( 'limit' ) ? CBTxt::T( 'GROUP_GUESTS_COUNT_LIMITED', '%%COUNT%% of [limit] Guest|%%COUNT%% of [limit] Guests', array( '%%COUNT%%' => (int) $event->get( '_guests', 0 ), '[limit]' => (int) $event->get( 'limit' ) ) ) : CBTxt::T( 'GROUP_GUESTS_COUNT', '%%COUNT%% Guest|%%COUNT%% Guests', array( '%%COUNT%%' => (int) $event->get( '_guests', 0 ) ) ) )
								.								'</a>'
								.							'</div>'
								.						'</div>'
								.					'</div>'
								.					'<div class="gjGroupEventDescription panel-footer">'
								.						'<div class="cbMoreLess">'
								.							'<div class="cbMoreLessContent">'
								.								( $plugin->params->get( 'groups_events_content_plugins', 0 ) ? Application::Cms()->prepareHtmlContentPlugins( $event->get( 'event' ), 'groupjive.event', $event->get( 'user_id', 0, GetterInterface::INT ) ) : $event->get( 'event' ) )
								.							'</div>'
								.							'<div class="cbMoreLessOpen fade-edge hidden">'
								.								'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
								.							'</div>'
								.						'</div>'
								.					'</div>'
								.				'</div>'
								.			'</div>'
								.		'</div>'
								.	'</div>';

		return $return;
	}
}