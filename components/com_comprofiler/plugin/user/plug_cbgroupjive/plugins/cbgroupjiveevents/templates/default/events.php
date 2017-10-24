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
use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJiveEvents\Table\EventTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveEvents
{

	/**
	 * render frontend events
	 *
	 * @param EventTable[]    $rows
	 * @param cbPageNav       $pageNav
	 * @param bool            $searching
	 * @param array           $input
	 * @param array           $counters
	 * @param GroupTable      $group
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showEvents( $rows, $pageNav, $searching, $input, &$counters, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$_CB_framework->outputCbJQuery( "$( '.gjGroupEventDescription .cbMoreLess' ).cbmoreless();", 'cbmoreless' );

		$counters[]							=	'<span class="gjGroupEventsIcon fa-before fa-calendar"> ' . CBTxt::T( 'GROUP_EVENTS_COUNT', '%%COUNT%% Event|%%COUNT%% Events', array( '%%COUNT%%' => (int) $pageNav->total ) ) . '</span>';

		initToolTip();

		$isModerator						=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$isOwner							=	( $user->get( 'id' ) == $group->get( 'user_id' ) );
		$userStatus							=	CBGroupJive::getGroupStatus( $user, $group );
		$canCreate							=	CBGroupJive::canCreateGroupContent( $user, $group, 'events' );
		$canSearch							=	( $plugin->params->get( 'groups_events_search', 1 ) && ( $searching || $pageNav->total ) );
		$showAddress						=	$plugin->params->get( 'groups_events_address', 1 );
		$return								=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayEvents', array( &$return, &$rows, $group, $user ) );

		$return								.=	'<div class="gjGroupEvents">'
											.		'<form action="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) . '" method="post" name="gjGroupEventsForm" id="gjGroupEventsForm" class="gjGroupEventsForm">';

		if ( $canCreate || $canSearch ) {
			$return							.=			'<div class="gjHeader gjGroupEventsHeader row">';

			if ( $canCreate ) {
				$return						.=				'<div class="' . ( ! $canSearch ? 'col-sm-12' : 'col-sm-8' ) . ' text-left">'
											.					'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'events', 'func' => 'new', 'group' => (int) $group->get( 'id' ) ) ) . '\';" class="gjButton gjButtonNewEvent btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New Event' ) . '</button>'
											.				'</div>';
			}

			if ( $canSearch ) {
				$return						.=				'<div class="' . ( ! $canCreate ? 'col-sm-offset-8 ' : null ) . 'col-sm-4 text-right">'
											.					'<div class="input-group">'
											.						'<span class="input-group-addon"><span class="fa fa-search"></span></span>'
											.						$input['search']
											.					'</div>'
											.				'</div>';
			}

			$return							.=			'</div>';
		}

		$return								.=			'<div class="gjGroupEventsRows">';

		if ( $rows ) foreach ( $rows as $row ) {
			$rowOwner						=	( $user->get( 'id' ) == $row->get( 'user_id' ) );
			$address						=	htmlspecialchars( $row->get( 'location' ) );

			if ( $showAddress ) {
				if ( $row->get( 'address' ) ) {
					$mapUrl					=	CBTxt::T( 'GROUP_EVENT_ADDRESS_MAP_URL', 'https://www.google.com/maps/place/[address]', array( '[location]' => urlencode( $row->get( 'location' ) ), '[address]' => urlencode( $row->get( 'address' ) ) ) );
				} else {
					$mapUrl					=	CBTxt::T( 'GROUP_EVENT_LOCATION_MAP_URL', 'https://www.google.com/maps/search/[location]', array( '[location]' => urlencode( $row->get( 'location' ) ), '[address]' => urlencode( $row->get( 'address' ) ) ) );
				}

				if ( $mapUrl ) {
					$address				=	'<a href="' . htmlspecialchars( $mapUrl ) . '" target="_blank" rel="nofollow">' . $address . '</a>';
				}
			}

			$menu							=	array();

			$integrations					=	$_PLUGINS->trigger( 'gj_onDisplayEvent', array( &$row, &$menu, $group, $user ) );

			$canAttend						=	( ( ! $rowOwner ) && ( $row->status() != 1 ) && ( ! $row->get( '_attending' ) ) && ( $userStatus >= 1 ) && ( ( ! $row->get( 'limit' ) ) || ( $row->get( 'limit' ) && ( $row->get( '_guests' ) < $row->get( 'limit' ) ) ) ) );
			$canMenu						=	( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) || ( ( ! $rowOwner ) && ( $row->status() != 1 ) && ( $userStatus >= 1 ) && $row->get( '_attending' ) ) || $menu );

			$return							.=				'<div class="gjGroupEventsRow row' . ( $row->status() == 1 ? ' gjGroupEventExpired' : ( $row->status() == 2 ? ' gjGroupEventActive' : null ) ) . '">'
											.					'<div class="gjGroupEventCalendar col-md-2 hidden-sm hidden-xs">'
											.						'<div class="panel panel-default text-center">'
											.							'<div class="gjGroupEventMonth panel-body">' . cbFormatDate( $row->get( 'start' ), true, false, 'M' ) . '</div>'
											.							'<div class="gjGroupEventDay panel-footer">' . cbFormatDate( $row->get( 'start' ), true, false, 'j' ) . '</div>'
											.						'</div>'
											.					'</div>'
											.					'<div class="gjGroupEventContainer col-md-10 col-sm-12 col-xs-12">'
											.						'<div class="panel ' . ( $row->status() == 1 ? 'panel-warning' : ( $row->status() == 2 ? 'panel-primary' : 'panel-default' ) ) . '">'
											.							'<div class="gjGroupEventHeader panel-heading">'
											.								'<div class="row">'
											.									'<div class="gjGroupEventTitle ' . ( $canAttend || $canMenu ? 'col-sm-8' : 'col-sm-12' ) . '">' . htmlspecialchars( $row->get( 'title' ) ) . '</div>';

			if ( $canAttend || $canMenu ) {
				$return						.=									'<div class="gjGroupEventMenu col-sm-4 text-right">';

				if ( ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) && ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'events', 1 ) == 2 ) ) ) {
					$return					.=										'<span class="gjGroupPendingIcon fa fa-lg fa-clock-o text-warning" title="' . htmlspecialchars( CBTxt::T( 'Awaiting Approval' ) ) . '"></span> ';
				}

				if ( $canAttend ) {
					$return					.=										'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'attend', 'id' => (int) $row->get( 'id' ) ) ) . '\';" class="gjButton gjButtonAttend btn btn-xs btn-success">' . CBTxt::T( 'Attend' ) . '</button> ';
				}

				if ( $canMenu ) {
					$menuItems				=	'<ul class="gjEventMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

					if ( ( ! $rowOwner ) && ( $row->status() != 1 ) && ( $userStatus >= 1 ) && $row->get( '_attending' ) ) {
						$menuItems			.=		'<li class="gjEventMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you do not want to attend this Event?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'unattend', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unattend' ) . '</a></li>';
					}

					if ( $row->get( '_guests', 0 ) && ( $row->status() != 1 ) && ( $isModerator || ( ( $row->get( 'published' ) == 1 ) && $plugin->params->get( 'groups_events_message', 0 ) && ( $isOwner || ( $userStatus >= 3 ) ) ) ) ) {
						$delay				=	false;

						if ( ( ! $isModerator ) && $row->params()->get( 'messaged' ) && $plugin->params->get( 'groups_events_message_delay', 60 ) ) {
							$seconds		=	(int) $plugin->params->get( 'groups_events_message_delay', 60 );

							if ( $seconds ) {
								$diff		=	Application::Date( 'now', 'UTC' )->diff( $row->get( 'messaged' ) );

								if ( ( $diff === false ) || ( $diff->s < $seconds ) ) {
									$delay	=	true;
								}
							}
						}

						if ( ! $delay ) {
							$menuItems		.=		'<li class="gjEventMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'message', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-comment"></span> ' . CBTxt::T( 'Message' ) . '</a></li>';
						}
					}

					if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
						$menuItems			.=		'<li class="gjEventMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'edit', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

						if ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'events', 1 ) == 2 ) ) {
							if ( $isModerator || $isOwner || ( $userStatus >= 2 ) ) {
								$menuItems	.=		'<li class="gjEventMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
							}
						} elseif ( $row->get( 'published' ) == 1 ) {
							$menuItems		.=		'<li class="gjEventMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this Event?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'unpublish', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
						} else {
							$menuItems		.=		'<li class="gjEventMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
						}
					}

					if ( $menu ) {
						$menuItems			.=		'<li class="gjEventMenuItem">' . implode( '</li><li class="gjEventMenuItem">', $menu ) . '</li>';
					}

					if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
						$menuItems			.=		'<li class="gjEventMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this Event?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'events', 'func' => 'delete', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
					}

					$menuItems				.=	'</ul>';

					$menuAttr				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

					$return					.=										'<span class="gjEventMenu btn-group">'
											.											'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
											.										'</span>';
				}

				$return						.=									'</div>';
			}

			$return							.=								'</div>'
											.							'</div>'
											.							'<div class="gjGroupEventDetails panel-body small">';

			if ( $row->status() == 1 ) {
				$return						.=								'<div class="gjGroupEventNotice text-warning text-right">' . CBTxt::T( 'This event has ended.' ) . '</div>';
			} elseif ( $row->status() == 2 ) {
				if ( $row->get( 'end' ) ) {
					$return					.=								'<div class="gjGroupEventNotice text-primary text-right">' . CBTxt::T( 'GROUP_EVENT_ENDS_IN', 'This event is currently in progress and ends in [timeago].', array( '[timeago]' => cbFormatDate( $row->get( 'end' ), true, 'exacttimeago' ) ) ) . '</div>';
				} else {
					$return					.=								'<div class="gjGroupEventNotice text-primary text-right">' . CBTxt::T( 'This event is currently in progress.' ) . '</div>';
				}
			} else {
				$return						.=								'<div class="gjGroupEventNotice text-right">' . CBTxt::T( 'GROUP_EVENT_STARTS_IN', 'This event starts in [timeago].', array( '[timeago]' => cbFormatDate( $row->get( 'start' ), true, 'exacttimeago' ) ) ) . '</div>';
			}

			$return							.=								'<div class="gjGroupEventDate">'
											.									'<span class="gjGroupEventIcon fa fa-clock-o text-center"></span> ' . $row->date()
											.								'</div>'
											.								'<div class="gjGroupEventLocation">'
											.									'<span class="gjGroupEventIcon fa fa-map-marker text-center"></span> ' . $address
											.								'</div>'
											.								'<div class="gjGroupEventAttending row">'
											.									'<div class="gjGroupEventGuests col-sm-6">'
											.										'<span class="gjGroupEventIcon fa fa-users text-center"></span> '
											.										'<a href="' . htmlspecialchars( $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'events', 'func' => 'attending', 'id' => (int) $row->get( 'id' ) ) ) ) . '">'
											.											( $row->get( 'limit' ) ? CBTxt::T( 'GROUP_GUESTS_COUNT_LIMITED', '%%COUNT%% of [limit] Guest|%%COUNT%% of [limit] Guests', array( '%%COUNT%%' => (int) $row->get( '_guests', 0 ), '[limit]' => (int) $row->get( 'limit' ) ) ) : CBTxt::T( 'GROUP_GUESTS_COUNT', '%%COUNT%% Guest|%%COUNT%% Guests', array( '%%COUNT%%' => (int) $row->get( '_guests', 0 ) ) ) )
											.										'</a>'
											.									'</div>'
											.									'<div class="gjGroupEventHost col-sm-6 text-right">'
											.										CBuser::getInstance( (int) $row->get( 'user_id' ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true )
											.									'</div>'
											.								'</div>'
											.							'</div>'
											.							'<div class="gjGroupEventDescription panel-footer">'
											.								'<div class="cbMoreLess">'
											.									'<div class="cbMoreLessContent">'
											.										( $plugin->params->get( 'groups_events_content_plugins', 0 ) ? Application::Cms()->prepareHtmlContentPlugins( $row->get( 'event' ), 'groupjive.event', $row->get( 'user_id', 0, GetterInterface::INT ) ) : $row->get( 'event' ) )
											.									'</div>'
											.									'<div class="cbMoreLessOpen fade-edge hidden">'
											.										'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
											.									'</div>'
											.								'</div>'
											.							'</div>'
											.							( is_array( $integrations ) && $integrations ? '<div class="gjGroupEventFooter panel-footer">' . implode( '', $integrations ) . '</div>' : null )
											.						'</div>'
											.					'</div>'
											.				'</div>';
		} else {
			if ( $searching ) {
				$return						.=				CBTxt::T( 'No group event search results found.' );
			} else {
				$return						.=				CBTxt::T( 'This group currently has no events.' );
			}
		}

		$return								.=			'</div>';

		if ( $plugin->params->get( 'groups_events_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return							.=			'<div class="gjGroupEventsPaging text-center">'
											.				$pageNav->getListLinks()
											.			'</div>';
		}

		$return								.=			$pageNav->getLimitBox( false )
											.		'</form>'
											.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayEvents', array( &$return, $rows, $group, $user ) );

		return $return;
	}
}