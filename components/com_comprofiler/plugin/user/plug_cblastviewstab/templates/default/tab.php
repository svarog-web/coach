<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CB\Database\Table\UserViewTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cblastviewsTab
{

	/**
	 * @param UserViewTable[] $rows
	 * @param int             $viewsCount
	 * @param int             $guestCount
	 * @param UserTable       $viewer
	 * @param UserTable       $user
	 * @param TabTable        $tab
	 * @param cbTabHandler    $plugin
	 * @return string
	 */
	static function showViews( $rows, $viewsCount, $guestCount, $viewer, $user, $tab, $plugin )
	{
		$cbModerator				=	Application::User( (int) $viewer->get( 'id' ) )->isGlobalModerator();
		$profileOwner				=	( $user->get( 'id' ) == $viewer->get( 'id' ) );

		$return						=	'<div class="lastViewsTab">';

		if ( $viewsCount || $guestCount ) {
			$userViews				=	CBTxt::T( 'LAST_VIEWS_USERS', '%%COUNT%% user|%%COUNT%% users', array( '%%COUNT%%' => $viewsCount ) );
			$guestViews				=	CBTxt::T( 'LAST_VIEWS_GUESTS', '%%COUNT%% guest|%%COUNT%% guests', array( '%%COUNT%%' => $guestCount ) );

			$return					.=		'<div class="lastViewsHeader" style="margin-bottom: 10px;">';

			if ( $profileOwner ) {
				if ( $viewsCount && $guestCount ) {
					$return			.=			CBTxt::T( 'LAST_VIEWS_YOUR_PROFILE_USERS_AND_GUESTS', 'Your profile has been viewed by [user_views] and [guest_views].', array( '[user_views]' => $userViews, '[guest_views]' => $guestViews ) );
				} else {
					$return			.=			CBTxt::T( 'LAST_VIEWS_YOUR_PROFILE_USERS_OR_GUESTS', 'Your profile has been viewed by [views].', array( '[views]' => ( $viewsCount ? $userViews : '' ) . ( $guestCount ? $guestViews : '' ) ) );
				}
			} else {
				if ( $viewsCount && $guestCount ) {
					$return			.=			CBTxt::T( 'LAST_VIEWS_THIS_PROFILE_USERS_AND_GUESTS', 'This profile has been viewed by [user_views] and [guest_views].', array( '[user_views]' => $userViews, '[guest_views]' => $guestViews ) );
				} else {
					$return			.=			CBTxt::T( 'LAST_VIEWS_THIS_PROFILE_USERS_OR_GUESTS', 'This profile has been viewed by [views].', array( '[views]' => ( $viewsCount ? $userViews : '' ) . ( $guestCount ? $guestViews : '' ) ) );
				}
			}

			$return					.=		'</div>';
		}

		$return						.=		'<div class="lastViewsContainers clearfix">';

		if ( $rows ) foreach ( $rows as $row ) {
			$visitor				=	CBuser::getInstance( (int) $row->get( 'viewer_id' ), false );

			$tipField				=	CBTxt::T( 'VIEWER_VIEWED_DATE', 'Viewed: [date]', array( '[date]' => cbFormatDate( $row->get( 'lastview' ) ) ) )
									.	'<br />' . CBTxt::T( 'VIEWER_VIEWS_COUNT', 'Views: %%COUNT%%', array( '%%COUNT%%' => (int) $row->get( 'viewscount' ) ) );

			if ( $cbModerator ) {
				$tipField			.=	'<br />' . CBTxt::T( 'VIEWER_IP_ADDRESS', 'IP Address: [ip]', array( '[ip]' => $row->get( 'lastip' ) ) );
			}

			$tipTitle				=	CBTxt::T( 'VIEWER_DETAILS', 'Viewer Details');
			$htmlText				=	$visitor->getField( 'avatar', null, 'html', 'none', 'list', 0, true );
			$tooltip				=	cbTooltip( 1, $tipField, $tipTitle, 300, null, $htmlText, null, 'style="display: inline-block; padding: 5px;"' );

			$return					.=			'<div class="containerBox img-thumbnail">'
									.				'<div class="containerBoxInner" style="min-height: 100px; min-width: 90px;">'
									.					$visitor->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( '_imgMode' => 1 ) )
									.					' ' . $visitor->getField( 'formatname', null, 'html', 'none', 'list', 0, true )
									.					'<br />'
									.					$tooltip
									.				'</div>'
									.			'</div>';
		} else {
			if ( $profileOwner ) {
				$return				.=			CBTxt::T( 'You have no views.' );
			} else {
				$return				.=			CBTxt::T( 'This user has no views.' );
			}
		}

		$return						.=		'</div>'
									.	'</div>';

		return $return;
	}
}