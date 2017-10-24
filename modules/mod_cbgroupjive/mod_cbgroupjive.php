<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CB\Plugin\GroupJive\CBGroupJive;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_database, $_PLUGINS;

if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
	echo 'CB not installed'; return;
}

include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

cbimport( 'cb.html' );
cbimport( 'language.front' );

$_PLUGINS->loadPluginGroup( 'user' );

$gjPlugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );

if ( ! $gjPlugin ) {
	echo 'CB GroupJive not installed'; return;
}

$gjParams							=	$_PLUGINS->getPluginParams( $gjPlugin );
$integrations						=	$_PLUGINS->trigger( 'gj_onModuleDisplay', array( $params ) );
$return								=	null;

if ( is_array( $integrations ) && $integrations ) {
	$return							=	implode( '', $integrations );
} else {
	$user							=	CBuser::getMyUserDataInstance();
	$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );

	CBGroupJive::getTemplate( 'module' );

	$mode							=	$params->get( 'groupjive_mode', 'latest_groups' );
	$limit							=	(int) $params->get( 'groupjive_limit', 10 );

	$excludeCategories				=	$params->get( 'groupjive_exclude_categories' );
	$excludeGroups					=	$params->get( 'groupjive_exclude_groups' );

	if ( ! is_array( $excludeCategories ) ) {
		if ( $excludeCategories ) {
			$excludeCategories		=	explode( ',', $excludeCategories );
		} else {
			$excludeCategories		=	array();
		}
	}

	if ( ! is_array( $excludeGroups ) ) {
		if ( $excludeGroups ) {
			$excludeGroups			=	explode( ',', $excludeGroups );
		} else {
			$excludeGroups			=	array();
		}
	}

	switch( $mode ) {
		case 'categories_asc':
		case 'categories_desc':
		case 'popular_categories':
			switch ( $mode ) {
				case 'popular_categories':
					$orderBy		=	$_CB_database->NameQuote( '_groups' ) . ' DESC';
					break;
				case 'categories_desc':
					$orderBy		=	'c.' . $_CB_database->NameQuote( 'ordering' ) . ' DESC';
					break;
				case 'categories_asc':
				default:
					$orderBy		=	'c.' . $_CB_database->NameQuote( 'ordering' ) . ' ASC';
					break;
			}

			$groups					=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
									.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'user_id' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
									.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' );

			if ( ! $isModerator ) {
				$groups				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
									.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $user->get( 'id' )
									.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
									.	' ON i.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
									.	' AND i.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
									.	' AND ( ( i.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) )
									.	' AND i.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
									.	' OR ( i.' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' )
									.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';
			}

			$groups					.=	"\n WHERE g." . $_CB_database->NameQuote( 'category' ) . " = c." . $_CB_database->NameQuote( 'id' )
									.	( $excludeGroups ? "\n AND g." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeGroups ) : null )
									.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
									.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
									.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

			if ( ! $isModerator ) {
				$groups				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
									.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
									.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
									.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
									.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
			}

			$query					=	'SELECT c.*'
									.	', ( ' . $groups . ' ) AS _groups'
									.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c";

			if ( ! $isModerator ) {
				$query				.=	"\n WHERE c." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND c." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( $user->get( 'id' ) ) )
									.	( $excludeCategories ? "\n AND c." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeCategories ) : null );
			} elseif ( $excludeCategories ) {
				$query				.=	"\n WHERE c." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeCategories );
			}

			$query					.=	"\n ORDER BY " . $orderBy;
			if ( $limit ) {
				$_CB_database->setQuery( $query, 0, $limit );
			} else {
				$_CB_database->setQuery( $query );
			}
			$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\CategoryTable', array( $_CB_database ) );

			if ( $rows ) {
				CBGroupJive::getCategory( $rows );
				CBGroupJive::preFetchUsers( $rows );

				$return				=	HTML_groupjiveModule::showCategories( $rows, $user, $params, $gjPlugin );
			}
			break;
		case 'popular_groups':
		case 'latest_groups':
		case 'oldest_groups':
		default:
			switch ( $mode ) {
				case 'popular_groups':
					$orderBy		=	$_CB_database->NameQuote( '_users' ) . ' DESC';
					break;
				case 'oldest_groups':
					$orderBy		=	'g.' . $_CB_database->NameQuote( 'date' ) . ' ASC';
					break;
				case 'latest_groups':
				default:
					$orderBy		=	'g.' . $_CB_database->NameQuote( 'date' ) . ' DESC';
					break;
			}

			$users					=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS uc"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS uccb"
									.	' ON uccb.' . $_CB_database->NameQuote( 'id' ) . ' = uc.' . $_CB_database->NameQuote( 'user_id' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS ucj"
									.	' ON ucj.' . $_CB_database->NameQuote( 'id' ) . ' = uccb.' . $_CB_database->NameQuote( 'id' )
									.	"\n WHERE uc." . $_CB_database->NameQuote( 'group' ) . " = g." . $_CB_database->NameQuote( 'id' )
									.	"\n AND uccb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
									.	"\n AND uccb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
									.	"\n AND ucj." . $_CB_database->NameQuote( 'block' ) . " = 0";

			if ( ! $isModerator ) {
				$users				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
									.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' >= 2'
									.		' OR uc.' . $_CB_database->NameQuote( 'status' ) . ' >= 1 )';
			}

			if ( ! $gjParams->get( 'groups_users_owner', 1 ) ) {
				$users				.=	"\n AND uc." . $_CB_database->NameQuote( 'status' ) . " != 4";
			}

			$query					=	'SELECT g.*'
									.	', c.' . $_CB_database->NameQuote( 'name' ) . ' AS _category_name'
									.	', u.' . $_CB_database->NameQuote( 'status' ) . ' AS _user_status'
									.	', i.' . $_CB_database->NameQuote( 'id' ) . ' AS _invite_id'
									.	', ( ' . $users . ' ) AS _users'
									.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
									.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'user_id' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
									.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c"
									.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'category' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
									.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $user->get( 'id' )
									.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
									.	' ON i.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
									.	' AND i.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
									.	' AND ( ( i.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) )
									.	' AND i.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
									.	' OR ( i.' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' )
									.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )'
									.	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
									.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
									.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

			if ( ! $isModerator ) {
				$query				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
									.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
									.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
									.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
									.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )'
									.	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
									.		( $gjParams->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' )
									.	( $excludeCategories ? "\n AND c." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeCategories ) : null )
									.	( $excludeGroups ? "\n AND g." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeGroups ) : null );
			}

			$query					.=	"\n ORDER BY " . $orderBy;
			if ( $limit ) {
				$_CB_database->setQuery( $query, 0, $limit );
			} else {
				$_CB_database->setQuery( $query );
			}
			$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );

			if ( $rows ) {
				CBGroupJive::getGroup( $rows );
				CBGroupJive::preFetchUsers( $rows );

				$return				=	HTML_groupjiveModule::showGroups( $rows, $user, $params, $gjPlugin );
			}
			break;
	}
}

if ( ! $return ) {
	return;
}

$class								=	$gjParams->get( 'general_class', null );

require JModuleHelper::getLayoutPath( 'mod_cbgroupjive', $params->get( 'layout', 'default' ) );