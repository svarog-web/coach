<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_framework, $_CB_database;

if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
	echo 'CB not installed'; return;
}

include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

cbimport( 'cb.html' );
cbimport( 'language.front' );

outputCbTemplate();

require_once( dirname( __FILE__ ) . '/helper.php' );

if ( (int) $params->get( 'cb_plugins', 1 ) ) {
	global $_PLUGINS;

	$_PLUGINS->loadPluginGroup( 'user' );
}

$cbUser					=	CBuser::getMyInstance();
$user					=	$cbUser->getUserData();
$templateClass			=	'cb_template cb_template_' . selectTemplate( 'dir' );

$mode					=	(int) $params->get( 'mode', 1 );

if ( $params->get( 'pretext' ) ) {
	$preText			=	$cbUser->replaceUserVars( $params->get( 'pretext' ) );
} else {
	$preText			=	null;
}

if ( $params->get( 'posttext' ) ) {
	$postText			=	$cbUser->replaceUserVars( $params->get( 'posttext' ) );
} else {
	$postText			=	null;
}

$exclude				=	$params->get( 'exclude' );

if ( $exclude ) {
	$exclude			=	explode( ',', $exclude );
}

$limit					=	(int) $params->get( 'limit', 30 );
$label					=	(int) $params->get( 'label', 1 );
$separator				=	$params->get( 'separator', ',' );
$layout					=	$params->get( 'layout', 'default' );
$userIds				=	array();

switch( $mode ) {
	case 9: // Online Connections
		if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
			$query		=	'SELECT DISTINCT s.' . $_CB_database->NameQuote( 'userid' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__session' ) . " AS s"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__comprofiler_members' ) . " AS m"
						.	' ON m.' . $_CB_database->NameQuote( 'referenceid' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
						.	' AND m.' . $_CB_database->NameQuote( 'memberid' ) . ' = s.' . $_CB_database->NameQuote( 'userid' )
						.	' AND m.' . $_CB_database->NameQuote( 'accepted' ) . ' = 1'
						.	' AND m.' . $_CB_database->NameQuote( 'pending' ) . ' = 0'
						.	"\n WHERE s." . $_CB_database->NameQuote( 'client_id' ) . " = 0"
						.	"\n AND s." . $_CB_database->NameQuote( 'guest' ) . " = 0"
						.	( $exclude ? "\n AND s." . $_CB_database->NameQuote( 'userid' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY s." . $_CB_database->NameQuote( 'time' ) . " DESC";
			$_CB_database->setQuery( $query, 0, $limit );
			$userIds	=	$_CB_database->loadResultArray();
		}
		break;
	case 8: // Registered Users
		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null );
		$_CB_database->setQuery( $query );
		$registered		=	$_CB_database->loadResult();

		$layout			=	'_registered';
		break;
	case 7: // User Census
		$thisDay		=	Application::Date( 'today', 'UTC' )->format( 'Y-m-d' );
		$nextDay		=	Application::Date( 'tomorrow', 'UTC' )->format( 'Y-m-d' );
		$thisWeek		=	Application::Date( 'Monday this week', 'UTC' )->format( 'Y-m-d' );
		$nextWeek		=	Application::Date( 'Monday next week', 'UTC' )->format( 'Y-m-d' );
		$thisMonth		=	Application::Date( 'last day of last month', 'UTC' )->format( 'Y-m-d' );
		$nextMonth		=	Application::Date( 'last day of this month', 'UTC' )->format( 'Y-m-d' );
		$thisYear		=	Application::Date( 'last day of December last year', 'UTC' )->format( 'Y-m-d' );
		$nextYear		=	Application::Date( 'last day of December this year', 'UTC' )->format( 'Y-m-d' );

		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null );
		$_CB_database->setQuery( $query );
		$totalUsers		=	$_CB_database->loadResult();

		$query			=	'SELECT u.' . $_CB_database->NameQuote( 'id' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY u." . $_CB_database->NameQuote( 'registerDate' ) . " DESC";
		$_CB_database->setQuery( $query, 0, 1 );
		$userId			=	$_CB_database->loadResult();

		$latestUser		=	CBuser::getInstance( (int) $userId );

		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__session' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'client_id' ) . " = 0"
						.	"\n AND " . $_CB_database->NameQuote( 'guest' ) . " = 0"
						.	( $exclude ? "\n AND " . $_CB_database->NameQuote( 'userid' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null );
		$_CB_database->setQuery( $query, 0, $limit );
		$onlineUsers	=	$_CB_database->loadResult();

		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	"\n AND u." . $_CB_database->NameQuote( 'registerDate' ) . " BETWEEN " . $_CB_database->Quote( $thisDay ) . " AND " . $_CB_database->Quote( $nextDay )
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY u." . $_CB_database->NameQuote( 'registerDate' ) . " DESC";
		$_CB_database->setQuery( $query );
		$usersToday		=	$_CB_database->loadResult();

		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	"\n AND u." . $_CB_database->NameQuote( 'registerDate' ) . " BETWEEN " . $_CB_database->Quote( $thisWeek ) . " AND " . $_CB_database->Quote( $nextWeek )
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY u." . $_CB_database->NameQuote( 'registerDate' ) . " DESC";
		$_CB_database->setQuery( $query );
		$usersWeek		=	$_CB_database->loadResult();

		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	"\n AND u." . $_CB_database->NameQuote( 'registerDate' ) . " BETWEEN " . $_CB_database->Quote( $thisMonth ) . " AND " . $_CB_database->Quote( $nextMonth )
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY u." . $_CB_database->NameQuote( 'registerDate' ) . " DESC";
		$_CB_database->setQuery( $query );
		$usersMonth		=	$_CB_database->loadResult();

		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	"\n AND u." . $_CB_database->NameQuote( 'registerDate' ) . " BETWEEN " . $_CB_database->Quote( $thisYear ) . " AND " . $_CB_database->Quote( $nextYear )
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY u." . $_CB_database->NameQuote( 'registerDate' ) . " DESC";
		$_CB_database->setQuery( $query );
		$usersYear		=	$_CB_database->loadResult();

		$layout			=	'_census';
		break;
	case 6: // Online Statistics
		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__session' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'client_id' ) . " = 0"
						.	"\n AND " . $_CB_database->NameQuote( 'guest' ) . " = 0"
						.	( $exclude ? "\n AND " . $_CB_database->NameQuote( 'userid' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null );
		$_CB_database->setQuery( $query, 0, $limit );
		$onlineUsers	=	$_CB_database->loadResult();

		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__session' ) . " AS s"
						.	' ON s.' . $_CB_database->NameQuote( 'userid' ) . ' = u.' . $_CB_database->NameQuote( 'id' )
						.	' AND s.' . $_CB_database->NameQuote( 'client_id' ) . ' = 0'
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	"\n AND s." . $_CB_database->NameQuote( 'session_id' ) . " IS NULL"
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null );
		$_CB_database->setQuery( $query );
		$offlineUsers	=	$_CB_database->loadResult();

		$query			=	'SELECT COUNT(*)'
						.	"\n FROM " . $_CB_database->NameQuote( '#__session' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'client_id' ) . " = 0"
						.	"\n AND " . $_CB_database->NameQuote( 'guest' ) . " = 1";
		$_CB_database->setQuery( $query, 0, $limit );
		$guestUsers		=	$_CB_database->loadResult();

		$layout			=	'_statistics';
		break;
	case 5: // Custom Latest
		$field			=	$params->get( 'custom_field' );
		$direction		=	$params->get( 'custom_direction', 'ASC' );

		if ( $field ) {
			if ( in_array( $field, array( 'id', 'name', 'username', 'email', 'registerDate', 'lastvisitDate', 'params' ) ) ) {
				$table	=	'u.';
			} else {
				$table	=	'c.';
			}

			$query		=	'SELECT u.' . $_CB_database->NameQuote( 'id' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY " . $table . $_CB_database->NameQuote( $field ) . " " . $direction;
			$_CB_database->setQuery( $query, 0, $limit );
			$userIds	=	$_CB_database->loadResultArray();
		}
		break;
	case 4: // Latest Profile Updates
		$query			=	'SELECT u.' . $_CB_database->NameQuote( 'id' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY c." . $_CB_database->NameQuote( 'lastupdatedate' ) . " DESC";
		$_CB_database->setQuery( $query, 0, $limit );
		$userIds		=	$_CB_database->loadResultArray();
		break;
	case 3: // Latest Registrations
		$query			=	'SELECT u.' . $_CB_database->NameQuote( 'id' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY u." . $_CB_database->NameQuote( 'registerDate' ) . " DESC";
		$_CB_database->setQuery( $query, 0, $limit );
		$userIds		=	$_CB_database->loadResultArray();
		break;
	case 2: // Latest Visitors
		$query			=	'SELECT u.' . $_CB_database->NameQuote( 'id' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
						.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
						.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
						.	"\n WHERE c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
						.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
						.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
						.	( $exclude ? "\n AND u." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY u." . $_CB_database->NameQuote( 'lastvisitDate' ) . " DESC";
		$_CB_database->setQuery( $query, 0, $limit );
		$userIds		=	$_CB_database->loadResultArray();
		break;
	default: // Online Users
		$query			=	'SELECT DISTINCT ' . $_CB_database->NameQuote( 'userid' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__session' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'client_id' ) . " = 0"
						.	"\n AND " . $_CB_database->NameQuote( 'guest' ) . " = 0"
						.	( $exclude ? "\n AND " . $_CB_database->NameQuote( 'userid' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $exclude ) : null )
						.	"\n ORDER BY " . $_CB_database->NameQuote( 'time' ) . " DESC";
		$_CB_database->setQuery( $query, 0, $limit );
		$userIds		=	$_CB_database->loadResultArray();
		break;
}

$cbUsers				=	array();

if ( $userIds ) {
	CBuser::advanceNoticeOfUsersNeeded( $userIds );

	foreach ( $userIds as $userId ) {
		$cbUser			=	CBuser::getInstance( (int) $userId );

		if ( $cbUser !== null ) {
			$cbUsers[]	=	$cbUser;
		}
	}
}

require JModuleHelper::getLayoutPath( 'mod_comprofileronline', $layout );