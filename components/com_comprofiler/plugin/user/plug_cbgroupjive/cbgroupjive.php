<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\GroupJive\CBGroupJive;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );
$_PLUGINS->loadPluginGroup( 'user/plug_cbgroupjive/plugins' );

$_PLUGINS->registerFunction( 'mod_onCBAdminMenu', 'adminMenu', '\CB\Plugin\GroupJive\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'deleteGroups', '\CB\Plugin\GroupJive\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterUserRegistration', 'acceptInvites', '\CB\Plugin\GroupJive\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterNewUser', 'acceptInvites', '\CB\Plugin\GroupJive\Trigger\UserTrigger' );

$_PLUGINS->registerFunction( 'onBuildRoute', 'build', '\CB\Plugin\GroupJive\Trigger\RouterTrigger' );
$_PLUGINS->registerFunction( 'onParseRoute', 'parse', '\CB\Plugin\GroupJive\Trigger\RouterTrigger' );

$_PLUGINS->registerFunction( 'activity_onQueryActivity', 'activityQuery', '\CB\Plugin\GroupJive\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onBeforeDisplayActivity', 'activityPrefetch', '\CB\Plugin\GroupJive\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayActivity', 'activityDisplay', '\CB\Plugin\GroupJive\Trigger\ActivityTrigger' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array(	'groupautojoin'			=>	'\CB\Plugin\GroupJive\Field\AutoJoinField',
											'groupmultiautojoin'	=>	'\CB\Plugin\GroupJive\Field\AutoJoinField'
										));

class cbgjTab extends cbTabHandler
{

	/**
	 * prepare frontend tab render
	 *
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_framework, $_CB_database;

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params		=	new Registry( $tab->params );
		}

		$viewer					=	CBuser::getMyUserDataInstance();
		$isModerator			=	CBGroupJive::isModerator( $viewer->get( 'id' ) );
		$isOwner				=	( $viewer->get( 'id' ) == $user->get( 'id' ) );

		CBGroupJive::getTemplate( 'tab' );

		$limit					=	(int) $tab->params->get( 'tab_limit', 30 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( 'gj_tab_limitstart{com_comprofiler}', 'gj_tab_limitstart' );
		$search					=	$_CB_framework->getUserStateFromRequest( 'gj_tab_search{com_comprofiler}', 'gj_tab_search' );
		$where					=	null;

		if ( $search && $tab->params->get( 'tab_search', 1 ) ) {
			$where				.=	"\n AND ( g." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR g." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( $where ? true : false );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g";

		if ( ! $isModerator ) {
			$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c"
								.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'category' );
		}

		$query					.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
								.	' ON u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	' AND u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $user->get( 'id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
								.	' ON i.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	' AND i.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
								.	' AND ( ( i.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) )
								.	' AND i.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
								.	' OR ( i.' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' )
								.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';

		if ( $isOwner ) {
			$query				.=	"\n WHERE ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );

			if ( ! $isModerator ) {
				$query			.=		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND ( u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
			} else {
				$query			.=		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL )';
			}
		} else {
			$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS mu"
								.	' ON mu.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	' AND mu.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $viewer->get( 'id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS mi"
								.	' ON mi.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	' AND mi.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
								.	' AND ( ( mi.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $viewer->get( 'email' ) )
								.	' AND mi.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
								.	' OR ( mi.' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $viewer->get( 'id' )
								.	' AND mi.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )'
								.	"\n WHERE ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );

			if ( ! $isModerator ) {
				$query			.=		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 1, 2, 3 ) ) )'
								.	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $viewer->get( 'id' )
								.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
								.		' OR mu.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR mi.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
			} else {
				$query			.=		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 1, 2, 3 ) )';
			}
		}

		if ( ! $isModerator ) {
			$query				.=	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) )
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $viewer->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
		}

		$query					.=	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		if ( ( ! $total ) && ( ! $searching ) && ( ( ! $isOwner ) || ( $isOwner && ( ! CBGroupJive::canCreateGroup( $user ) ) ) ) && ( ! Application::Config()->get( 'showEmptyTabs', 1 ) ) ) {
			return null;
		}

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( 'gj_tab_' );

		switch( (int) $tab->params->get( 'tab_orderby', 1 ) ) {
			case 2:
				$orderBy		=	'g.' . $_CB_database->NameQuote( 'ordering' ) . ' DESC';
				break;
			case 3:
				$orderBy		=	'g.' . $_CB_database->NameQuote( 'date' ) . ' ASC';
				break;
			case 4:
				$orderBy		=	'g.' . $_CB_database->NameQuote( 'date' ) . ' DESC';
				break;
			case 5:
				$orderBy		=	'g.' . $_CB_database->NameQuote( 'name' ) . ' ASC';
				break;
			case 6:
				$orderBy		=	'g.' . $_CB_database->NameQuote( 'name' ) . ' DESC';
				break;
			case 7:
				$orderBy		=	$_CB_database->NameQuote( '_users' ) . ' ASC';
				break;
			case 8:
				$orderBy		=	$_CB_database->NameQuote( '_users' ) . ' DESC';
				break;
			case 1:
			default:
				$orderBy		=	'g.' . $_CB_database->NameQuote( 'ordering' ) . ' ASC';
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
			$users				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $viewer->get( 'id' )
								.		( ! $isOwner ? ' OR mu.' . $_CB_database->NameQuote( 'status' ) . ' >= 2' : null )
								.		' OR uc.' . $_CB_database->NameQuote( 'status' ) . ' >= 1 )';
		}

		if ( ! $this->params->get( 'groups_users_owner', 1 ) ) {
			$users				.=	"\n AND uc." . $_CB_database->NameQuote( 'status' ) . " != 4";
		}

		$query					=	'SELECT g.*'
								.	', c.' . $_CB_database->NameQuote( 'name' ) . ' AS _category_name';

		if ( $isOwner ) {
			$query				.=	', u.' . $_CB_database->NameQuote( 'status' ) . ' AS _user_status'
								.	', i.' . $_CB_database->NameQuote( 'id' ) . ' AS _invite_id';
		} else {
			$query				.=	', mu.' . $_CB_database->NameQuote( 'status' ) . ' AS _user_status'
								.	', mi.' . $_CB_database->NameQuote( 'id' ) . ' AS _invite_id';
		}

		$query					.=	', ( ' . $users . ' ) AS _users'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
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
								.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';

		if ( $isOwner ) {
			$query				.=	"\n WHERE ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );

			if ( ! $isModerator ) {
				$query			.=		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND ( u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
			} else {
				$query			.=		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL )';
			}
		} else {
			$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS mu"
								.	' ON mu.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $viewer->get( 'id' )
								.	' AND mu.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS mi"
								.	' ON mi.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	' AND mi.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
								.	' AND ( ( mi.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $viewer->get( 'email' ) )
								.	' AND mi.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
								.	' OR ( mi.' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $viewer->get( 'id' )
								.	' AND mi.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )'
								.	"\n WHERE ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );

			if ( ! $isModerator ) {
				$query			.=		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 1, 2, 3 ) ) )'
								.	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $viewer->get( 'id' )
								.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
								.		' OR mu.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR mi.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
			} else {
				$query			.=		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 1, 2, 3 ) )';
			}
		}

		if ( ! $isModerator ) {
			$query				.=	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) )
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $viewer->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
		}

		$query					.=	$where
								.	"\n ORDER BY " . $orderBy;
		if ( $tab->params->get( 'tab_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );

		$input['search']		=	'<input type="text" name="gj_tab_search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjTabForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Groups...' ) ) . '" class="form-control" />';

		CBGroupJive::getGroup( $rows );
		CBGroupJive::preFetchUsers( $rows );

		$class					=	$this->params->get( 'general_class', null );

		$return					=	'<div class="cbGroupJive' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
								.		'<div class="cbGroupJiveInner">'
								.			HTML_groupjiveTab::showTab( $rows, $pageNav, $searching, $input, $viewer, $user, $tab, $this )
								.		'</div>'
								.	'</div>';

		return $return;
	}
}