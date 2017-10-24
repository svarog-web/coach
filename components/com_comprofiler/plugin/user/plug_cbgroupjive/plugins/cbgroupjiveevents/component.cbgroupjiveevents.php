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
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveEvents\CBGroupJiveEvents;
use CB\Plugin\GroupJiveEvents\Table\AttendanceTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

class CBplug_cbgroupjiveevents extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_PLUGINS;

		$format					=	$this->input( 'format', null, GetterInterface::STRING );

		if ( $format != 'raw' ) {
			outputCbJs();
			outputCbTemplate();
		}

		$action					=	$this->input( 'action', null, GetterInterface::STRING );
		$function				=	$this->input( 'func', null, GetterInterface::STRING );
		$id						=	$this->input( 'id', 0, GetterInterface::INT );
		$user					=	CBuser::getMyUserDataInstance();

		if ( $format != 'raw' ) {
			ob_start();
		}

		switch ( $action ) {
			case 'events':
				switch ( $function ) {
					case 'attending':
						$this->showEventAttending( $id, $user );
						break;
					case 'attend':
						$this->attendEvent( $id, $user );
						break;
					case 'unattend':
						$this->unattendEvent( $id, $user );
						break;
					case 'publish':
						$this->stateEvent( 1, $id, $user );
						break;
					case 'unpublish':
						$this->stateEvent( 0, $id, $user );
						break;
					case 'delete':
						$this->deleteEvent( $id, $user );
						break;
					case 'new':
						$this->showEventEdit( null, $user );
						break;
					case 'edit':
						$this->showEventEdit( $id, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveEventEdit( $id, $user );
						break;
					case 'message':
						$this->showEventMessage( $id, $user );
						break;
					case 'send':
						cbSpoofCheck( 'plugin' );
						$this->sendEventMessage( $id, $user );
						break;
				}
				break;
		}

		if ( $format != 'raw' ) {
			$html				=	ob_get_contents();
			ob_end_clean();

			static $gjClass		=	null;

			if ( $gjClass === null ) {
				$gjPlugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
				$gjParams		=	$_PLUGINS->getPluginParams( $gjPlugin );
				$gjClass		=	$gjParams->get( 'general_class', '', GetterInterface::STRING );
			}

			$return				=	'<div class="cbGroupJive' . ( $gjClass ? ' ' . htmlspecialchars( $gjClass ) : null ) . '">'
								.		'<div class="cbGroupJiveInner">'
								.			$html
								.		'</div>'
								.	'</div>';

			echo $return;
		}
	}

	/**
	 * prepare frontend event attending render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showEventAttending( $id, $user )
	{
		global $_CB_framework, $_CB_database;

		$event			=	CBGroupJiveEvents::getEvent( (int) $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $event->get( 'group' ) ) );

		if ( $event->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $event->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $event->get( 'published' ) != 1 ) && ( CBGroupJive::getGroupStatus( $user, $event->group() ) < 2 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to this event.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Event does not exist.' ), 'error' );
		}

		CBGroupJive::getTemplate( 'attending', true, true, $this->element );

		$canModerate			=	( CBGroupJive::isModerator( $user->get( 'id' ) ) || ( CBGroupJive::getGroupStatus( $user, $event->group() ) >= 2 ) );
		$prefix					=	'gj_event_' . $event->get( 'id', 0, GetterInterface::INT ) . '_attending_';
		$limit					=	(int) $this->params->get( 'groups_events_attending_limit', 30 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );
		$search					=	$_CB_framework->getUserStateFromRequest( $prefix . 'search{com_comprofiler}', $prefix . 'search' );
		$where					=	null;

		if ( $search && $this->params->get( 'groups_events_attending_search', 0 ) ) {
			$where				.=	"\n AND ( j." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR j." . $_CB_database->NameQuote( 'username' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( $where ? true : false );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_events_attendance' ) . " AS a"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_plugin_events' ) . " AS e"
								.	' ON e.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'event' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE a." . $_CB_database->NameQuote( 'event' ) . " = " . (int) $event->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND ( e." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR e.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
		}

		$query					.=	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $prefix );

		$query					=	'SELECT a.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_events_attendance' ) . " AS a"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_plugin_events' ) . " AS e"
								.	' ON e.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'event' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE a." . $_CB_database->NameQuote( 'event' ) . " = " . (int) $event->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND ( e." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR e.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
		}

		$query					.=	$where
								.	"\n ORDER BY a." . $_CB_database->NameQuote( 'date' ) . " DESC";
		if ( $this->params->get( 'groups_events_attending_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveEvents\Table\AttendanceTable', array( $_CB_database ) );

		$input					=	array();

		$input['search']		=	'<input type="text" name="' . htmlspecialchars( $prefix ) . 'search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjEventAttendingForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Attending...' ) ) . '" class="form-control" />';

		CBGroupJive::preFetchUsers( $rows );

		HTML_groupjiveAttending::showAttending( $rows, $pageNav, $searching, $input, $event, $user, $this );
	}

	/**
	 * prepare frontend event message render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showEventMessage( $id, $user )
	{
		global $_CB_framework;

		$row					=	CBGroupJiveEvents::getEvent( (int) $id );
		$returnUrl				=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ! $this->params->get( 'groups_events_message', 0 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to messaging for this event.' ), 'error' );
				} elseif ( ( $row->status() == 1 ) || ( $row->get( 'published' ) == -1 ) || ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 3 ) ) || ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 1 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to messaging for this event.' ), 'error' );
				} elseif ( $row->params()->get( 'messaged' ) ) {
					$seconds	=	(int) $this->params->get( 'groups_events_message_delay', 60 );

					if ( $seconds ) {
						$diff	=	Application::Date( 'now', 'UTC' )->diff( $row->get( 'messaged' ) );

						if ( ( $diff === false ) || ( $diff->s < $seconds ) ) {
							cbRedirect( $returnUrl, CBTxt::T( 'You can not send a message to this event at this time. Please wait awhile and try again.' ), 'error' );
						}
					}
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Event does not exist.' ), 'error' );
		}

		CBGroupJive::getTemplate( 'message', true, true, $this->element );

		$input					=	array();

		$subjectTooltip			=	cbTooltip( null, CBTxt::T( 'Optionally input a message subject.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['subject']		=	'<input type="text" id="subject" name="subject" value="' . htmlspecialchars( $this->input( 'post/subject', null, GetterInterface::STRING ) ) . '" class="form-control input-block"' . $subjectTooltip . ' />';

		$messageTooltip			=	cbTooltip( null, CBTxt::T( 'Input a message to send to this events guests.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['message']		=	'<textarea id="message" name="message" class="form-control input-block required" rows="10"' . $messageTooltip . '>' . htmlspecialchars( $this->input( 'post/message', null, ( $this->params->get( 'groups_message_html', false, GetterInterface::BOOLEAN ) ? GetterInterface::HTML : GetterInterface::STRING ) ) ) . '</textarea>';

		HTML_groupjiveEventMessage::showMessage( $row, $input, $row->group(), $user, $this );
	}

	/**
	 * prepare frontend event edit render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showEventEdit( $id, $user )
	{
		global $_CB_framework;

		$row					=	CBGroupJiveEvents::getEvent( (int) $id );
		$isModerator			=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$groupId				=	$this->input( 'group', null, GetterInterface::INT );

		if ( $groupId === null ) {
			$group				=	$row->group();
		} else {
			$group				=	CBGroupJive::getGroup( $groupId );
		}

		$returnUrl				=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		} elseif ( ! $isModerator ) {
			if ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'events' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to schedule an event in this group.' ), 'error' );
			} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $group ) < 2 ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this event.' ), 'error' );
			}
		}

		CBGroupJive::getTemplate( 'event_edit', true, true, $this->element );

		$input					=	array();

		$publishedTooltip		=	cbTooltip( null, CBTxt::T( 'Select publish state of this event. Unpublished events will not be visible to the public.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['published']		=	moscomprofilerHTML::yesnoSelectList( 'published', 'class="form-control"' . $publishedTooltip, (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) );

		$titleTooltup			=	cbTooltip( null, CBTxt::T( 'Input the event title. This is the title that will distinguish this event from others. Suggested to input something to uniquely identify your event.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['title']			=	'<input type="text" id="title" name="title" value="' . htmlspecialchars( $this->input( 'post/title', $row->get( 'title' ), GetterInterface::STRING ) ) . '" class="form-control required" size="35"' . $titleTooltup . ' />';

		$event					=	$_CB_framework->displayCmsEditor( 'event', $this->input( 'post/event', $row->get( 'event' ), GetterInterface::HTML ), '100%', null, 40, 10 );

		$input['event']			=	cbTooltip( null, CBTxt::T( 'Input a detailed description about this event.' ), null, null, null, $event, null, 'style="display:block;"' );

		$locationTooltup		=	cbTooltip( null, CBTxt::T( 'Input the location for this event (e.g. My House, The Park, Restaurant Name, etc..).' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['location']		=	'<input type="text" id="location" name="location" value="' . htmlspecialchars( $this->input( 'post/location', $row->get( 'location' ), GetterInterface::STRING ) ) . '" class="form-control required" size="35"' . $locationTooltup . ' />';

		$addressTooltup			=	cbTooltip( null, CBTxt::T( 'Optionally input the address for this event or click the map button to attempt to find your current location.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['address']		=	'<input type="text" id="address" name="address" value="' . htmlspecialchars( $this->input( 'post/address', $row->get( 'address' ), GetterInterface::STRING ) ) . '" class="form-control" size="45"' . $addressTooltup . ' />';

		$calendars				=	new cbCalendars( 1 );
		$minYear				=	(int) Application::Date( ( $row->get( 'id' ) ? $row->get( 'start' ) : 'now' ), 'UTC' )->format( 'Y' );

		$startTooltup			=	cbTooltip( null, CBTxt::T( 'Select the date and time this event starts.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['start']			=	$calendars->cbAddCalendar( 'start', null, true, $this->input( 'post/start', $row->get( 'start' ), GetterInterface::STRING ), false, true, $minYear, ( $minYear + 30 ), $startTooltup );

		$endTooltup				=	cbTooltip( null, CBTxt::T( 'Optionally select the end date and time for this event.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['end']			=	$calendars->cbAddCalendar( 'end', null, false, $this->input( 'post/end', $row->get( 'end' ), GetterInterface::STRING ), false, true, $minYear, ( $minYear + 30 ), $endTooltup );

		$limitTooltip			=	cbTooltip( null, CBTxt::T( 'Optionally input a guest limit for this event.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['limit']			=	'<input type="text" id="limit" name="limit" value="' . (int) $this->input( 'post/limit', $row->get( 'limit' ), GetterInterface::INT ) . '" class="digits form-control" size="6"' . $limitTooltip . ' />';

		$ownerTooltip			=	cbTooltip( null, CBTxt::T( 'Input the event owner id. Event owner determines the creator of the event specified as User ID.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['user_id']		=	'<input type="text" id="user_id" name="user_id" value="' . (int) $this->input( 'post/user_id', $this->input( 'user', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ), GetterInterface::INT ) . '" class="digits required form-control" size="6"' . $ownerTooltip . ' />';

		HTML_groupjiveEventEdit::showEventEdit( $row, $input, $group, $user, $this );
	}

	/**
	 * save event
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveEventEdit( $id, $user )
	{
		global $_CB_framework, $_PLUGINS;

		$row					=	CBGroupJiveEvents::getEvent( (int) $id );
		$isModerator			=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$groupId				=	$this->input( 'group', null, GetterInterface::INT );

		if ( $groupId === null ) {
			$group				=	$row->group();
		} else {
			$group				=	CBGroupJive::getGroup( $groupId );
		}

		$returnUrl				=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		} elseif ( ! $isModerator ) {
			if ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'events' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to schedule an event in this group.' ), 'error' );
			} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $group ) < 2 ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this event.' ), 'error' );
			}
		}

		if ( $isModerator ) {
			$row->set( 'user_id', (int) $this->input( 'post/user_id', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ) );
		} else {
			$row->set( 'user_id', (int) $row->get( 'user_id', $user->get( 'id' ) ) );
		}

		$canModerate			=	( CBGroupJive::getGroupStatus( $user, $group ) >= 2 );

		$currentTitle			=	$row->get( 'title' );
		$currentEvent			=	$row->get( 'event' );
		$currentLocation		=	$row->get( 'location' );
		$currentAddress			=	$row->get( 'address' );
		$currentStart			=	$row->get( 'start', '0000-00-00 00:00:00' );
		$currentEnd				=	$row->get( 'end', '0000-00-00 00:00:00' );

		$newStart				=	$this->input( 'post/start', $currentStart, GetterInterface::STRING );
		$newEnd					=	$this->input( 'post/end', $currentEnd, GetterInterface::STRING );

		$row->set( 'published', ( $isModerator || $canModerate || ( $row->get( 'id' ) && ( $row->get( 'published' ) != -1 ) ) || ( $group->params()->get( 'events', 1 ) != 2 ) ? (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) : -1 ) );
		$row->set( 'group', (int) $group->get( 'id' ) );
		$row->set( 'title', $this->input( 'post/title', $currentTitle, GetterInterface::STRING ) );
		$row->set( 'event', $this->input( 'post/event', $currentEvent, GetterInterface::HTML ) );
		$row->set( 'location', $this->input( 'post/location', $currentLocation, GetterInterface::STRING ) );
		$row->set( 'address', $this->input( 'post/address', $currentAddress, GetterInterface::STRING ) );
		$row->set( 'start', ( $newStart ? $newStart : '0000-00-00 00:00:00' ) );
		$row->set( 'end', ( $newEnd ? $newEnd : '0000-00-00 00:00:00' ) );
		$row->set( 'limit', (int) $this->input( 'post/limit', $row->get( 'limit' ), GetterInterface::INT ) );

		if ( ( ! $isModerator ) && $this->params->get( 'groups_events_captcha', 0 ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

			if ( $_PLUGINS->is_errors() ) {
				$row->setError( $_PLUGINS->getErrorMSG() );
			}
		}

		$new					=	( $row->get( 'id' ) ? false : true );

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_EVENT_FAILED_TO_SAVE', 'Event failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showEventEdit( $id, $user );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_EVENT_FAILED_TO_SAVE', 'Event failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showEventEdit( $id, $user );
			return;
		}

		$extras					=	array(	'event_id'			=>	(int) $row->get( 'id' ),
											'event_title'		=>	htmlspecialchars( $row->get( 'title' ) ),
											'event_details'		=>	htmlspecialchars( $row->get( 'event' ) ),
											'event_address'		=>	htmlspecialchars( $row->get( 'address' ) ),
											'event_location'	=>	htmlspecialchars( $row->get( 'location' ) ),
											'event_date'		=>	htmlspecialchars( $row->date() ),
											'event'				=>	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ), 'tab' => 'grouptabevents' ) ) . '">' . htmlspecialchars( $row->get( 'title' ) ) . '</a>' );

		if ( $new ) {
			if ( $row->get( 'published' ) == 1 ) {
				CBGroupJive::sendNotifications( 'event_new', CBTxt::T( 'New group event' ), CBTxt::T( '[user] has scheduled the event [event] in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 1, $extras );
			} elseif ( ( $row->get( 'published' ) == -1 ) && ( $row->group()->params()->get( 'events', 1 ) == 2 ) ) {
				CBGroupJive::sendNotifications( 'event_approve', CBTxt::T( 'New group event awaiting approval' ), CBTxt::T( '[user] has scheduled the event [event] in the group [group] and is awaiting approval!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 2, $extras );

				cbRedirect( $returnUrl, CBTxt::T( 'Event scheduled successfully and awaiting approval!' ) );
			}

			cbRedirect( $returnUrl, CBTxt::T( 'Event scheduled successfully!' ) );
		} else {
			if ( ( $row->get( 'published' ) == 1 ) && ( ( $row->get( 'title' ) != $currentTitle ) || ( $row->get( 'event' ) != $currentEvent ) || ( $row->get( 'location' ) != $currentLocation ) || ( $row->get( 'address' ) != $currentAddress ) || ( $row->get( 'start' ) != $currentStart ) || ( $row->get( 'end' ) != $currentEnd ) ) ) {
				CBGroupJive::sendNotifications( 'event_edit', CBTxt::T( 'Group event changed' ), CBTxt::T( '[user] has changed the scheduled event [event] in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 1, $extras );
			}

			cbRedirect( $returnUrl, CBTxt::T( 'Event saved successfully!' ) );
		}
	}

	/**
	 * set event publish state status
	 *
	 * @param int       $state
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function stateEvent( $state, $id, $user )
	{
		global $_CB_framework;

		$row				=	CBGroupJiveEvents::getEvent( (int) $id );
		$returnUrl			=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 2 ) {
					if ( ( $user->get( 'id' ) == $row->get( 'user_id' ) ) && ( $row->get( 'published' ) == -1 ) && ( $row->group()->params()->get( 'events', 1 ) == 2 ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'Your event is awaiting approval.' ), 'error' );
					} elseif ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to publish or unpublish this event.' ), 'error' );
					}
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Event does not exist.' ), 'error' );
		}

		$currentState		=	(int) $row->get( 'published' );

		$row->set( 'published', (int) $state );

		if ( $row->getError() || ( ! $row->store() ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_EVENT_STATE_FAILED_TO_SAVE', 'Event state failed to saved. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $state && ( $currentState == -1 ) ) {
			$extras			=	array(	'event_id'			=>	(int) $row->get( 'id' ),
										'event_title'		=>	htmlspecialchars( $row->get( 'title' ) ),
										'event_details'		=>	htmlspecialchars( $row->get( 'event' ) ),
										'event_address'		=>	htmlspecialchars( $row->get( 'address' ) ),
										'event_location'	=>	htmlspecialchars( $row->get( 'location' ) ),
										'event_date'		=>	htmlspecialchars( $row->date() ),
										'event'				=>	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ), 'tab' => 'grouptabevents' ) ) . '">' . htmlspecialchars( $row->get( 'title' ) ) . '</a>' );

			if ( $row->get( 'user_id' ) != $user->get( 'id' ) ) {
				CBGroupJive::sendNotification( 4, $user, (int) $row->get( 'user_id' ), CBTxt::T( 'Event schedule request accepted' ), CBTxt::T( 'Your event [event] schedule request in the group [group] has been accepted!' ), $row->group(), $extras );
			}

			CBGroupJive::sendNotifications( 'event_new', CBTxt::T( 'New group event' ), CBTxt::T( '[user] has scheduled the event [event] in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 1, $extras );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Event state saved successfully!' ) );
	}

	/**
	 * delete event
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteEvent( $id, $user )
	{
		global $_CB_framework;

		$row			=	CBGroupJiveEvents::getEvent( (int) $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 2 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to delete this event.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Event does not exist.' ), 'error' );
		}

		if ( ! $row->canDelete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_EVENT_FAILED_TO_DELETE', 'Event failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_EVENT_FAILED_TO_DELETE', 'Event failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Event deleted successfully!' ) );
	}

	/**
	 * attend event
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function attendEvent( $id, $user )
	{
		global $_CB_framework, $_CB_database;

		$event						=	CBGroupJiveEvents::getEvent( (int) $id );
		$returnUrl					=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $event->get( 'group' ) ) );

		if ( $event->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $event->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $event->get( 'published' ) != 1 ) && ( CBGroupJive::getGroupStatus( $user, $event->group() ) < 2 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to this event.' ), 'error' );
				} elseif ( CBGroupJive::getGroupStatus( $user, $event->group() ) < 1 ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to attend this event.' ), 'error' );
				} elseif ( $event->status() == 1 ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You can not attend an expired event.' ), 'error' );
				} elseif ( $event->get( 'limit' ) ) {
					$query			=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_events_attendance' ) . " AS a"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
									.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'user_id' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
									.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
									.	"\n WHERE a." . $_CB_database->NameQuote( 'event' ) . " = " . (int) $event->get( 'id' )
									.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
									.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
									.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";
					$_CB_database->setQuery( $query );
					$guests			=	(int) $_CB_database->loadResult();

					if ( $guests >= (int) $event->get( 'limit' ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'This event is full.' ), 'error' );
					}
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Event does not exist.' ), 'error' );
		}

		$row						=	new AttendanceTable();

		$row->load( array( 'user_id' => (int) $user->get( 'id' ), 'event' => (int) $event->get( 'id' ) ) );

		if ( $row->get( 'id' ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'You are already attending this event.' ), 'error' );
		}

		$row->set( 'user_id', (int) $user->get( 'id' ) );
		$row->set( 'event', (int) $event->get( 'id' ) );

		if ( $row->getError() || ( ! $row->check() ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_EVENT_ATTEND_FAILED', 'Event attend failed. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_EVENT_ATTEND_FAILED', 'Event attend failed. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$extras						=	array(	'event_id'			=>	(int) $event->get( 'id' ),
												'event_title'		=>	htmlspecialchars( $event->get( 'title' ) ),
												'event_details'		=>	htmlspecialchars( $event->get( 'event' ) ),
												'event_address'		=>	htmlspecialchars( $event->get( 'address' ) ),
												'event_location'	=>	htmlspecialchars( $event->get( 'location' ) ),
												'event_date'		=>	htmlspecialchars( $event->date() ),
												'event'				=>	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $event->get( 'group' ), 'tab' => 'grouptabevents' ) ) . '">' . htmlspecialchars( CBTxt::T( $event->get( 'title' ) ) ) . '</a>' );

		CBGroupJive::sendNotifications( 'event_attend', CBTxt::T( 'User attending your group event' ), CBTxt::T( '[user] will be attending your event [event] in the group [group]!' ), $event->group(), $user, (int) $event->get( 'user_id' ), array(), 1, $extras );

		cbRedirect( $returnUrl, CBTxt::T( 'Event attended successfully!' ) );
	}

	/**
	 * unattend event
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function unattendEvent( $id, $user )
	{
		global $_CB_framework;

		$event				=	CBGroupJiveEvents::getEvent( (int) $id );
		$returnUrl			=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $event->get( 'group' ) ) );

		if ( $event->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $event->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $event->get( 'published' ) != 1 ) && ( CBGroupJive::getGroupStatus( $user, $event->group() ) < 2 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to this event.' ), 'error' );
				} elseif ( CBGroupJive::getGroupStatus( $user, $event->group() ) < 1 ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to unattend this event.' ), 'error' );
				} elseif ( $event->status() == 1 ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You can not unattend an expired event.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Event does not exist.' ), 'error' );
		}

		$row				=	new AttendanceTable();

		$row->load( array( 'user_id' => (int) $user->get( 'id' ), 'event' => (int) $event->get( 'id' ) ) );

		if ( ! $row->get( 'id' ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'You can not unattend an event you are not attending.' ), 'error' );
		}

		if ( ! $row->canDelete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_EVENT_FAILED_TO_UNATTEND', 'Event failed to unattend. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_EVENT_FAILED_TO_UNATTEND', 'Event failed to unattend. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$extras				=	array(	'event_id'			=>	(int) $event->get( 'id' ),
										'event_title'		=>	htmlspecialchars( $event->get( 'title' ) ),
										'event_details'		=>	htmlspecialchars( $event->get( 'event' ) ),
										'event_address'		=>	htmlspecialchars( $event->get( 'address' ) ),
										'event_location'	=>	htmlspecialchars( $event->get( 'location' ) ),
										'event_date'		=>	htmlspecialchars( $event->date() ),
										'event'				=>	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $event->get( 'group' ), 'tab' => 'grouptabevents' ) ) . '">' . htmlspecialchars( CBTxt::T( $event->get( 'title' ) ) ) . '</a>' );

		CBGroupJive::sendNotifications( 'event_unattend', CBTxt::T( 'User unattended your group event' ), CBTxt::T( '[user] will no longer be attending your event [event] in the group [group]!' ), $event->group(), $user, (int) $event->get( 'user_id' ), array(), 1, $extras );

		cbRedirect( $returnUrl, CBTxt::T( 'Event unattended successfully!' ) );
	}

	/**
	 * send event message
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function sendEventMessage( $id, $user )
	{
		global $_CB_framework, $_CB_database;

		$row					=	CBGroupJiveEvents::getEvent( (int) $id );
		$returnUrl				=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ! $this->params->get( 'groups_events_message', 0 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to messaging for this event.' ), 'error' );
				} elseif ( ( $row->status() == 1 ) || ( $row->get( 'published' ) == -1 ) || ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 3 ) ) || ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 1 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to messaging for this event.' ), 'error' );
				} elseif ( $row->params()->get( 'messaged' ) ) {
					$seconds	=	(int) $this->params->get( 'groups_events_message_delay', 60 );

					if ( $seconds ) {
						$diff	=	Application::Date( 'now', 'UTC' )->diff( $row->get( 'messaged' ) );

						if ( ( $diff === false ) || ( $diff->s < $seconds ) ) {
							cbRedirect( $returnUrl, CBTxt::T( 'You can not send a message to this event at this time. Please wait awhile and try again.' ), 'error' );
						}
					}
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Event does not exist.' ), 'error' );
		}

		$message				=	$this->input( 'post/message', null, ( $this->params->get( 'groups_events_message_html', false, GetterInterface::BOOLEAN ) ? GetterInterface::HTML : GetterInterface::STRING ) );

		if ( ! $message ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_EVENT_MESSAGE_FAILED_TO_SEND', 'Event message failed to send! Error: [error]', array( '[error]' => CBTxt::T( 'Message not specified!' ) ) ), 'error' );

			$this->showEventMessage( $id, $user );
			return;
		}

		$query					=	'SELECT cb.*, j.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_events_attendance' ) . " AS a"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_plugin_events' ) . " AS e"
								.	' ON e.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'event' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE a." . $_CB_database->NameQuote( 'event' ) . " = " . (int) $row->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";
		$_CB_database->setQuery( $query );
		$users					=	$_CB_database->loadObjectList( null, '\CB\Database\Table\UserTable', array( $_CB_database ) );

		if ( ! $users ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'This event has no guests to message.' ) );
		} else {
			$subject			=	$this->input( 'post/subject', null, GetterInterface::STRING );

			if ( $subject && ( $this->params->get( 'groups_events_message_type', 2, GetterInterface::INT ) == 1 ) && $this->params->get( 'groups_events_message_subject', false, GetterInterface::BOOLEAN ) ) {
				$subject		=	CBTxt::T( 'GROUP_EVENT_MESSAGE_SUBJECT', 'Event message - [subject]', array( '[subject]' => $subject ) );
			} else {
				$subject		=	CBTxt::T( 'Event message' );
			}

			$extras				=	array(	'event_id'			=>	(int) $row->get( 'id' ),
											'event_title'		=>	htmlspecialchars( $row->get( 'title' ) ),
											'event_details'		=>	htmlspecialchars( $row->get( 'event' ) ),
											'event_address'		=>	htmlspecialchars( $row->get( 'address' ) ),
											'event_location'	=>	htmlspecialchars( $row->get( 'location' ) ),
											'event_date'		=>	htmlspecialchars( $row->date() ),
											'event'				=>	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ), 'tab' => 'grouptabevents' ) ) . '">' . htmlspecialchars( $row->get( 'title' ) ) . '</a>' );

			CBGroupJive::preFetchUsers( $users );

			foreach ( $users as $usr ) {
				CBGroupJive::sendNotification( $this->params->get( 'groups_events_message_type', 2 ), $user, $usr, $subject, CBTxt::T( 'GROUP_EVENT_MESSAGE', 'Event [event] has sent the following message.<p>[message]</p>', array( '[message]' => $message ) ), $row->group(), $extras );
			}
		}

		$row->params()->set( 'messaged', Application::Database()->getUtcDateTime() );

		$row->set( 'params', $row->params()->asJson() );

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_EVENT_FAILED_TO_SAVE', 'Event failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showEventMessage( $id, $user );
			return;
		}

		CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Event messaged successfully!' ) );
	}
}