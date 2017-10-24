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
use CBLib\Input\Get;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\Table\InviteTable;
use CB\Plugin\GroupJive\Table\NotificationTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );
$_PLUGINS->loadPluginGroup( 'user/plug_cbgroupjive/plugins' );

class CBplug_cbgroupjive extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		$format				=	$this->input( 'format', null, GetterInterface::STRING );

		if ( $format != 'raw' ) {
			outputCbJs();
			outputCbTemplate();
		}

		$action				=	$this->input( 'action', null, GetterInterface::STRING );
		$function			=	$this->input( 'func', null, GetterInterface::STRING );
		$id					=	$this->input( 'id', 0, GetterInterface::INT );
		$user				=	CBuser::getMyUserDataInstance();

		if ( $format != 'raw' ) {
			ob_start();
		}

		// TODO: For B/C: remove in 4.0.0
		$cat				=	(int) $this->input( 'cat', null, GetterInterface::INT );
		$grp				=	(int) $this->input( 'grp', null, GetterInterface::INT );

		switch ( $action ) {
			case 'overview': // TODO: For B/C: remove in 4.0.0
			case 'allcategories':
				$action		=	'categories';
				$function	=	'all';
				break;
			case 'allgroups':
				$action		=	'groups';
				$function	=	'all';
				break;
			case 'panel': // TODO: For B/C: remove in 4.0.0
			case 'mygroups':
				$action		=	'groups';
				$function	=	'my';
				break;
			case 'ownedgroups':
				$action		=	'groups';
				$function	=	'owned';
				break;
			case 'joinedgroups':
				$action		=	'groups';
				$function	=	'joined';
				break;
			case 'invitedgroups':
				$action		=	'groups';
				$function	=	'invited';
				break;
			case 'groupsapproval':
				$action		=	'groups';
				$function	=	'approval';
				break;
			case 'newgroup':
				$action		=	'groups';
				$function	=	'new';

				if ( $id ) {
					$this->getInput()->set( 'category', $id );
				}
				break;
			case 'editgroup':
				$action		=	'groups';
				$function	=	'edit';
				break;
			case 'messagegroup':
				$action		=	'groups';
				$function	=	'message';
				break;
			case 'groupnotifications':
				$action		=	'groups';
				$function	=	'notifications';
				break;
			case 'categories': // TODO: For B/C: remove in 4.0.0
				if ( $cat ) {
					$id		=	$cat;
				}
				break;
			case 'groups': // TODO: For B/C: remove in 4.0.0
				if ( $cat ) {
					$this->getInput()->set( 'category', $cat );
				}

				if ( $grp ) {
					$id		=	$grp;
				}
				break;
			default: // TODO: For B/C: remove in 4.0.0
				if ( $cat ) {
					$this->getInput()->set( 'category', $cat );
				}

				if ( $grp ) {
					$this->getInput()->set( 'group', $grp );
				}
				break;
		}

		switch ( $action ) {
			case 'groups':
				switch ( $function ) {
					case 'reject':
						$this->rejectGroupInvites( $id, $user );
						break;
					case 'cancel':
						$this->cancelGroupJoin( $id, $user );
						break;
					case 'join':
						$this->joinGroup( $id, $user );
						break;
					case 'leave':
						$this->leaveGroup( $id, $user );
						break;
					case 'publish':
						$this->stateGroup( 1, $id, $user );
						break;
					case 'unpublish':
						$this->stateGroup( 0, $id, $user );
						break;
					case 'delete':
						$this->deleteGroup( $id, $user );
						break;
					case 'new':
						$this->showGroupEdit( null, $user );
						break;
					case 'edit':
						$this->showGroupEdit( $id, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveGroupEdit( $id, $user );
						break;
					case 'message':
						$this->showGroupMessage( $id, $user );
						break;
					case 'send':
						cbSpoofCheck( 'plugin' );
						$this->sendGroupMessage( $id, $user );
						break;
					case 'notifications':
						$this->showGroupNotifications( $id, $user );
						break;
					case 'all':
					case 'my':
					case 'owned':
					case 'joined':
					case 'invited':
					case 'approval':
						$this->showGroups( $function, $user );
						break;
					case 'show':
					default:
						$this->showGroup( $id, $user );
						break;
				}
				break;
			case 'users':
				switch ( $function ) {
					case 'ban':
						$this->statusUser( -1, $id, $user );
						break;
					case 'active':
						$this->statusUser( 1, $id, $user );
						break;
					case 'moderator':
						$this->statusUser( 2, $id, $user );
						break;
					case 'admin':
						$this->statusUser( 3, $id, $user );
						break;
					case 'owner':
						$this->statusUser( 4, $id, $user );
						break;
					case 'delete':
						$this->deleteUser( $id, $user );
						break;
				}
				break;
			case 'invites':
				switch ( $function ) {
					case 'send':
						$this->sendInvite( $id, $user );
						break;
					case 'new':
						$this->showInviteEdit( null, $user );
						break;
					case 'edit':
						$this->showInviteEdit( $id, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveInviteEdit( $id, $user );
						break;
					case 'delete':
						$this->deleteInvite( $id, $user );
						break;
				}
				break;
			case 'notifications':
				switch ( $function ) {
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveNotifications( $id, $user );
						break;
				}
				break;
			case 'categories':
			default:
				switch ( $function ) {
					case 'all':
						$this->showCategories( $user );
						break;
					case 'show':
					default:
						$this->showCategory( $id, $user );
						break;
				}
				break;
		}

		if ( $format != 'raw' ) {
			$html			=	ob_get_contents();
			ob_end_clean();

			$class			=	$this->params->get( 'general_class', null );

			$return			=	'<div class="cbGroupJive' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
							.		'<div class="cbGroupJiveInner">'
							.			$html
							.		'</div>'
							.	'</div>';

			echo $return;
		}
	}

	/**
	 * prepare frontend categories render
	 *
	 * @param UserTable $user
	 */
	private function showCategories( $user )
	{
		global $_CB_framework, $_CB_database;

		CBGroupJive::getTemplate( 'categories' );

		$isModerator			=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$limit					=	(int) $this->params->get( 'categories_limit', 30 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( 'gj_categories_limitstart{com_comprofiler}', 'gj_categories_limitstart' );
		$search					=	$_CB_framework->getUserStateFromRequest( 'gj_categories_search{com_comprofiler}', 'gj_categories_search' );
		$where					=	null;

		if ( $search && $this->params->get( 'categories_search', 1 ) ) {
			$where				.=	"\n " . ( $isModerator ? "WHERE" : "AND" ) . " ( c." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR c." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( count( $where ) ? true : false );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c";

		if ( ! $isModerator ) {
			$query				.=	"\n WHERE c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.	"\n AND c." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( $user->get( 'id' ) ) );
		}

		$query					.=	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( 'gj_categories_' );

		switch( (int) $this->params->get( 'categories_orderby', 1 ) ) {
			case 2:
				$orderBy		=	'c.' . $_CB_database->NameQuote( 'ordering' ) . ' DESC';
				break;
			case 3:
				$orderBy		=	'c.' . $_CB_database->NameQuote( 'name' ) . ' ASC';
				break;
			case 4:
				$orderBy		=	'c.' . $_CB_database->NameQuote( 'name' ) . ' DESC';
				break;
			case 5:
				$orderBy		=	$_CB_database->NameQuote( '_groups' ) . ' ASC';
				break;
			case 6:
				$orderBy		=	$_CB_database->NameQuote( '_groups' ) . ' DESC';
				break;
			case 1:
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
								.	"\n AND c." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( $user->get( 'id' ) ) );
		}

		$query					.=	$where
								.	"\n ORDER BY " . $orderBy;
		if ( $this->params->get( 'categories_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\CategoryTable', array( $_CB_database ) );

		CBGroupJive::getCategory( $rows );
		CBGroupJive::preFetchUsers( $rows );

		$input['search']		=	'<input type="text" name="gj_categories_search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjCategoriesForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Categories...' ) ) . '" class="form-control" />';

		HTML_groupjiveCategories::showCategories( $rows, $pageNav, $searching, $input, $user, $this );
	}

	/**
	 * prepare frontend category render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showCategory( $id, $user )
	{
		global $_CB_framework, $_CB_database;

		$row					=	CBGroupJive::getCategory( $id );
		$isModerator			=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$returnUrl				=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'categories', 'func' => 'all' ) );

		if ( $row->get( 'id' ) ) {
			if ( ! $isModerator ) {
				if ( ( ! $row->get( 'published' ) ) || ( ! CBGroupJive::canAccess( (int) $row->get( 'access' ), (int) $user->get( 'id' ) ) ) ) {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have access to this category.' ), 'error' );
				}
			}
		} elseif ( ( ! $isModerator ) && ( ! $this->params->get( 'groups_uncategorized', 1 ) ) ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Category does not exist.' ), 'error' );
		}

		CBGroupJive::getTemplate( 'category' );

		$prefix					=	'gj_category_' . $row->get( 'id', 0, GetterInterface::INT ) . '_';
		$limit					=	(int) $this->params->get( 'categories_groups_limit', 30 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );
		$search					=	$_CB_framework->getUserStateFromRequest( $prefix . 'search{com_comprofiler}', $prefix . 'search' );
		$where					=	null;

		if ( $search && $this->params->get( 'categories_groups_search', 1 ) ) {
			$where				.=	"\n AND ( g." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR g." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( $where ? true : false );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' );

		if ( ! $isModerator ) {
			$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
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

		$query					.=	"\n WHERE g." . $_CB_database->NameQuote( 'category' ) . " = " . (int) $row->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $isModerator ) {
			$query				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1 '
								.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
								.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
		}

		$query					.=	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $prefix );

		switch( (int) $this->params->get( 'categories_groups_orderby', 1 ) ) {
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
			$users				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' >= 2'
								.		' OR uc.' . $_CB_database->NameQuote( 'status' ) . ' >= 1 )';
		}

		if ( ! $this->params->get( 'groups_users_owner', 1 ) ) {
			$users				.=	"\n AND uc." . $_CB_database->NameQuote( 'status' ) . " != 4";
		}

		$query					=	'SELECT g.*'
								.	', u.' . $_CB_database->NameQuote( 'status' ) . ' AS _user_status'
								.	', i.' . $_CB_database->NameQuote( 'id' ) . ' AS _invite_id'
								.	', ( ' . $users . ' ) AS _users'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
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

		$query					.=	"\n WHERE g." . $_CB_database->NameQuote( 'category' ) . " = " . (int) $row->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $isModerator ) {
			$query				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
								.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
		}

		$query					.=	$where
								.	"\n ORDER BY " . $orderBy;
		if ( $this->params->get( 'categories_groups_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );

		CBGroupJive::getGroup( $rows );
		CBGroupJive::preFetchUsers( $rows );

		$input['search']		=	'<input type="text" name="' . $prefix . 'search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjCategoryForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Groups...' ) ) . '" class="form-control" />';

		HTML_groupjiveCategory::showCategory( $row, $rows, $pageNav, $searching, $input, $user, $this );
	}

	/**
	 * prepare frontend groups render
	 *
	 * @param int
	 * @param UserTable $user
	 */
	private function showGroups( $mode, $user )
	{
		global $_CB_framework, $_CB_database;

		$isModerator			=	CBGroupJive::isModerator( $user->get( 'id' ) );

		switch ( $mode ) {
			case 'approval':
				if ( ! $isModerator ) {
					CBGroupJive::returnRedirect( 'index.php', CBTxt::T( 'Only moderators can approve groups.' ), 'error' );
				}

				$prefix			=	'gj_groups_approval_';
				break;
			case 'invited':
				if ( ! $user->get( 'id' ) ) {
					cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => base64_encode( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'invited' ) ) ) ) ), CBTxt::T( 'Please Login or Register to view your group invites.' ) );
				}

				$prefix			=	'gj_groups_invited_';
				break;
			case 'joined':
				if ( ! $user->get( 'id' ) ) {
					cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => base64_encode( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'joined' ) ) ) ) ), CBTxt::T( 'Please Login or Register to view your joined groups.' ) );
				}

				$prefix			=	'gj_groups_joined_';
				break;
			case 'owned':
				if ( ! $user->get( 'id' ) ) {
					cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => base64_encode( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'owned' ) ) ) ) ), CBTxt::T( 'Please Login or Register to view your groups.' ) );
				}

				$prefix			=	'gj_groups_owned_';
				break;
			case 'my':
				if ( ! $user->get( 'id' ) ) {
					cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => base64_encode( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'my' ) ) ) ) ), CBTxt::T( 'Please Login or Register to view your groups.' ) );
				}

				$prefix			=	'gj_groups_my_';
				break;
			case 'all':
			default:
				$mode			=	'all';
				$prefix			=	'gj_groups_all_';
				break;
		}

		CBGroupJive::getTemplate( 'groups' );

		$limit					=	(int) $this->params->get( 'groups_limit', 30 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );
		$search					=	$_CB_framework->getUserStateFromRequest( $prefix . 'search{com_comprofiler}', $prefix . 'search' );
		$where					=	null;

		if ( $search && $this->params->get( 'groups_search', 1 ) ) {
			$where				.=	"\n AND ( g." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR g." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( $where ? true : false );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' );

		if ( ! $isModerator ) {
			$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c"
								.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'category' );
		}

		if ( ( ( ! $isModerator ) && ( $mode == 'all' ) ) || in_array( $mode, array( 'joined', 'my' ) ) ) {
			$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
								.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $user->get( 'id' )
								.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' );
		}

		if ( ( ( ! $isModerator ) && ( $mode == 'all' ) ) || in_array( $mode, array( 'invited', 'my' ) ) ) {
			$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
								.	' ON i.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	' AND i.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
								.	' AND ( ( i.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) )
								.	' AND i.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
								.	' OR ( i.' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' )
								.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';
		}

		$query					.=	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		switch ( $mode ) {
			case 'approval':
				$query			.=	"\n AND g." . $_CB_database->NameQuote( 'published' ) . " = -1";
				break;
			case 'invited':
				$query			.=	"\n AND i." . $_CB_database->NameQuote( 'id' ) . " IS NOT NULL";

				if ( ! $isModerator ) {
					$query		.=	"\n AND g." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
				}
				break;
			case 'joined':
				$query			.=	"\n AND u." . $_CB_database->NameQuote( 'status' ) . " IN ( 0, 1, 2, 3 )";

				if ( ! $isModerator ) {
					$query		.=	"\n AND g." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
				}
				break;
			case 'owned':
				$query			.=	"\n AND g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );

				if ( ! $isModerator ) {
					$query		.=	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
				}
				break;
			case 'my':
				$query			.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );

				if ( ! $isModerator ) {
					$query		.=		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND ( u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
				} else {
					$query		.=		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL )';
				}
				break;
			case 'all':
			default:
				if ( ! $isModerator ) {
					$query		.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
								.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )'
								.	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
				}
				break;
		}

		$query					.=	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $prefix );

		switch( (int) $this->params->get( 'groups_orderby', 1 ) ) {
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
			$users				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' >= 2'
								.		' OR uc.' . $_CB_database->NameQuote( 'status' ) . ' >= 1 )';
		}

		if ( ! $this->params->get( 'groups_users_owner', 1 ) ) {
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

		switch ( $mode ) {
			case 'approval':
				$query			.=	"\n AND g." . $_CB_database->NameQuote( 'published' ) . " = -1";
				break;
			case 'invited':
				$query			.=	"\n AND i." . $_CB_database->NameQuote( 'id' ) . " IS NOT NULL";

				if ( ! $isModerator ) {
					$query		.=	"\n AND g." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
				}
				break;
			case 'joined':
				$query			.=	"\n AND u." . $_CB_database->NameQuote( 'status' ) . " IN ( 0, 1, 2, 3 )";

				if ( ! $isModerator ) {
					$query		.=	"\n AND g." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
				}
				break;
			case 'owned':
				$query			.=	"\n AND g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );

				if ( ! $isModerator ) {
					$query		.=	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
				}
				break;
			case 'my':
				$query			.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );

				if ( ! $isModerator ) {
					$query		.=		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND ( u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
				} else {
					$query		.=		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL )';
				}
				break;
			case 'all':
			default:
				if ( ! $isModerator ) {
					$query		.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
								.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
								.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
								.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )'
								.	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
								.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
								.		( $this->params->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
				}
				break;
		}

		$query					.=	$where
								.	"\n ORDER BY " . $orderBy;
		if ( $this->params->get( 'groups_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );

		CBGroupJive::getGroup( $rows );
		CBGroupJive::preFetchUsers( $rows );

		$input['search']		=	'<input type="text" name="' . htmlspecialchars( $prefix ) . 'search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjGroupsForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Groups...' ) ) . '" class="form-control" />';

		HTML_groupjiveGroups::showGroups( $mode, $rows, $pageNav, $searching, $input, $user, $this );
	}

	/**
	 * prepare frontend group render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showGroup( $id, $user )
	{
		global $_CB_framework;

		$row				=	CBGroupJive::getGroup( $id );
		$returnUrl			=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $row->get( 'category' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $row, $user ) ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		}

		CBGroupJive::getTemplate( 'group' );

		$groupInvite		=	CBGroupJive::getGroupInvited( $user, $row );

		$row->set( '_invite_id', $groupInvite );

		$userStatus			=	CBGroupJive::getGroupStatus( $user, $row );

		$row->set( '_user_status', $userStatus );

		$users				=	$this->showGroupUsers( $row, $user );
		$invites			=	$this->showGroupInvites( $row, $user );

		HTML_groupjiveGroup::showGroup( $row, $users, $invites, $user, $this );
	}

	/**
	 * prepare frontend group message render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showGroupMessage( $id, $user )
	{
		global $_CB_framework;

		$row					=	CBGroupJive::getGroup( $id );
		$returnUrl				=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );

		if ( CBGroupJive::canAccessGroup( $row, $user ) ) {
			if ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ! $this->params->get( 'groups_message', 0 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to messaging in this group.' ), 'error' );
				} elseif ( ( $row->get( 'published' ) == -1 ) || ( CBGroupJive::getGroupStatus( $user, $row ) < 3 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to messaging in this group.' ), 'error' );
				} elseif ( $row->params()->get( 'messaged' ) ) {
					$seconds	=	(int) $this->params->get( 'groups_message_delay', 60 );

					if ( $seconds ) {
						$diff	=	Application::Date( 'now', 'UTC' )->diff( $row->get( 'messaged' ) );

						if ( ( $diff === false ) || ( $diff->s < $seconds ) ) {
							cbRedirect( $returnUrl, CBTxt::T( 'You can not send a message to this group at this time. Please wait awhile and try again.' ), 'error' );
						}
					}
				}
			}
		} else {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		}

		CBGroupJive::getTemplate( 'message' );

		$input					=	array();

		$subjectTooltip			=	cbTooltip( null, CBTxt::T( 'Optionally input a message subject.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['subject']		=	'<input type="text" id="subject" name="subject" value="' . htmlspecialchars( $this->input( 'post/subject', null, GetterInterface::STRING ) ) . '" class="form-control input-block"' . $subjectTooltip . ' />';

		$messageTooltip			=	cbTooltip( null, CBTxt::T( 'Input a message to send to this groups users.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['message']		=	'<textarea id="message" name="message" class="form-control input-block required" rows="10"' . $messageTooltip . '>' . htmlspecialchars( $this->input( 'post/message', null, ( $this->params->get( 'groups_message_html', false, GetterInterface::BOOLEAN ) ? GetterInterface::HTML : GetterInterface::STRING ) ) ) . '</textarea>';

		HTML_groupjiveMessage::showMessage( $row, $input, $user, $this );
	}

	/**
	 * prepare frontend group users render
	 *
	 * @param GroupTable $group
	 * @param UserTable  $user
	 * @return mixed
	 */
	private function showGroupUsers( &$group, $user )
	{
		global $_CB_framework, $_CB_database;

		CBGroupJive::getTemplate( 'users' );

		$canModerate			=	( CBGroupJive::isModerator( $user->get( 'id' ) ) || ( CBGroupJive::getGroupStatus( $user, $group ) >= 2 ) );
		$prefix					=	'gj_group_' . $group->get( 'id', 0, GetterInterface::INT ) . '_users_';
		$limit					=	(int) $this->params->get( 'groups_users_limit', 15 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );
		$search					=	$_CB_framework->getUserStateFromRequest( $prefix . 'search{com_comprofiler}', $prefix . 'search' );
		$where					=	null;

		if ( $search && $this->params->get( 'groups_users_search', 0 ) ) {
			$where				.=	"\n AND ( j." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR j." . $_CB_database->NameQuote( 'username' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( $where ? true : false );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = u.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE u." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND u." . $_CB_database->NameQuote( 'status' ) . " >= 1";
		}

		if ( ! $this->params->get( 'groups_users_owner', 1 ) ) {
			$query				.=	"\n AND u." . $_CB_database->NameQuote( 'status' ) . " != 4";
		}

		$query					.=	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		if ( ( ! $total ) && ( ! $searching ) ) {
			return null;
		}

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $prefix );

		$query					=	'SELECT u.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = u.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE u." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND u." . $_CB_database->NameQuote( 'status' ) . " >= 1";
		}

		if ( ! $this->params->get( 'groups_users_owner', 1 ) ) {
			$query				.=	"\n AND u." . $_CB_database->NameQuote( 'status' ) . " != 4";
		}

		$query					.=	$where
								.	"\n ORDER BY IF( u." . $_CB_database->NameQuote( 'status' ) . " = 0, 999, u." . $_CB_database->NameQuote( 'status' ) . " ) DESC, u." . $_CB_database->NameQuote( 'date' ) . " DESC";
		if ( $this->params->get( 'groups_users_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\UserTable', array( $_CB_database ) );

		CBGroupJive::getUser( $rows );
		CBGroupJive::preFetchUsers( $rows );

		$input['search']		=	'<input type="text" name="' . htmlspecialchars( $prefix ) . 'search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjGroupUsersForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Users...' ) ) . '" class="form-control" />';

		$group->set( '_users', $pageNav->total );

		return HTML_groupjiveUsers::showUsers( $rows, $pageNav, $searching, $input, $group, $user, $this );
	}

	/**
	 * prepare frontend group invites render
	 *
	 * @param GroupTable $group
	 * @param UserTable  $user
	 * @return mixed
	 */
	private function showGroupInvites( &$group, $user )
	{
		global $_CB_framework, $_CB_database;

		if ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
			if ( ( ! $this->params->get( 'groups_invites_display', 1 ) ) && ( $group->get( 'type' ) != 3 ) ) {
				return null;
			} elseif ( ( CBGroupJive::getGroupStatus( $user, $group ) < 1 ) ) {
				return null;
			}
		}

		CBGroupJive::getTemplate( 'invites' );

		$prefix					=	'gj_group_' . $group->get( 'id', 0, GetterInterface::INT ) . '_invites_';
		$limit					=	(int) $this->params->get( 'groups_invites_limit', 15 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );
		$search					=	$_CB_framework->getUserStateFromRequest( $prefix . 'search{com_comprofiler}', $prefix . 'search' );
		$where					=	null;

		if ( $search && $this->params->get( 'groups_invites_search', 0 ) ) {
			$where				.=	"\n AND ( i." . $_CB_database->NameQuote( 'email' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR j." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR j." . $_CB_database->NameQuote( 'username' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( $where ? true : false );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = i.' . $_CB_database->NameQuote( 'user' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE i." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.	"\n AND i." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND ( ( cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	' AND cb.' . $_CB_database->NameQuote( 'confirmed' ) . ' = 1'
								.	' AND j.' . $_CB_database->NameQuote( 'block' ) . ' = 0 )'
								.	' OR i.' . $_CB_database->NameQuote( 'user' ) . ' = 0 )'
								.	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		if ( ( ! $total ) && ( ! $searching ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'invites' ) ) ) {
			return null;
		}

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $prefix );

		switch( (int) $this->params->get( 'groups_invites_orderby', 1 ) ) {
			case 1:
				$orderBy		=	'i.' . $_CB_database->NameQuote( 'invited' ) . ' ASC';
				break;
			case 3:
				$orderBy		=	'i.' . $_CB_database->NameQuote( 'accepted' ) . ' ASC';
				break;
			case 4:
				$orderBy		=	'i.' . $_CB_database->NameQuote( 'accepted' ) . ' DESC';
				break;
			case 2:
			default:
				$orderBy		=	'i.' . $_CB_database->NameQuote( 'invited' ) . ' DESC';
				break;
		}

		$query					=	'SELECT i.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = i.' . $_CB_database->NameQuote( 'user' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE i." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.	"\n AND i." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND ( ( cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	' AND cb.' . $_CB_database->NameQuote( 'confirmed' ) . ' = 1'
								.	' AND j.' . $_CB_database->NameQuote( 'block' ) . ' = 0 )'
								.	' OR i.' . $_CB_database->NameQuote( 'user' ) . ' = 0 )'
								.	$where
								.	"\n ORDER BY " . $orderBy;
		if ( $this->params->get( 'groups_invites_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\InviteTable', array( $_CB_database ) );

		CBGroupJive::getInvite( $rows );
		CBGroupJive::preFetchUsers( $rows );

		$input['search']		=	'<input type="text" name="' . htmlspecialchars( $prefix ) . 'search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjGroupInvitesForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Invites...' ) ) . '" class="form-control" />';

		$group->set( '_invites', $pageNav->total );

		return HTML_groupjiveInvites::showInvites( $rows, $pageNav, $searching, $input, $group, $user, $this );
	}

	/**
	 * prepare frontend notifications render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showGroupNotifications( $id, $user )
	{
		global $_CB_framework;

		$row							=	new NotificationTable();

		$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );

		$group							=	CBGroupJive::getGroup( $id );

		$row->load( array( 'user_id' => (int) $user->get( 'id' ), 'group' => (int) $group->get( 'id' ) ) );

		$returnUrl						=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( CBGroupJive::canAccessGroup( $group, $user ) ) {
			if ( ! $this->params->get( 'notifications', 1 ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to notifications in this group.' ), 'error' );
			} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this users notifications.' ), 'error' );
			} elseif ( ! $isModerator ) {
				if ( ! CBGroupJive::canCreateGroupContent( $user, $group ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to notifications in this group.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		}

		CBGroupJive::getTemplate( 'notifications' );

		$input							=	array();

		$listToggle						=	array();
		$listToggle[]					=	moscomprofilerHTML::makeOption( '-1', CBTxt::T( '- Toggle All - ' ) );
		$listToggle[]					=	moscomprofilerHTML::makeOption( '0', CBTxt::T( "Don't Notify" ) );
		$listToggle[]					=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Notify' ) );

		$input['toggle']				=	moscomprofilerHTML::selectList( $listToggle, 'toggle', 'class="gjToggleNotifications form-control"', 'value', 'text', '-1', 1, false, false );

		$input['user_join']				=	moscomprofilerHTML::yesnoSelectList( 'params[user_join]', 'class="form-control"', (int) $this->input( 'post/params.user_join', $row->params()->get( 'user_join', $this->params->get( 'notifications_default_user_join', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );
		$input['user_leave']			=	moscomprofilerHTML::yesnoSelectList( 'params[user_leave]', 'class="form-control"', (int) $this->input( 'post/params.user_leave', $row->params()->get( 'user_leave', $this->params->get( 'notifications_default_user_leave', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );
		$input['user_approve']			=	moscomprofilerHTML::yesnoSelectList( 'params[user_approve]', 'class="form-control"', (int) $this->input( 'post/params.user_approve', $row->params()->get( 'user_approve', $this->params->get( 'notifications_default_user_approve', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );
		$input['user_cancel']			=	moscomprofilerHTML::yesnoSelectList( 'params[user_cancel]', 'class="form-control"', (int) $this->input( 'post/params.user_cancel', $row->params()->get( 'user_cancel', $this->params->get( 'notifications_default_user_cancel', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );
		$input['invite_accept']			=	moscomprofilerHTML::yesnoSelectList( 'params[invite_accept]', 'class="form-control"', (int) $this->input( 'post/params.invite_accept', $row->params()->get( 'invite_accept', $this->params->get( 'notifications_default_invite_accept', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );
		$input['invite_reject']			=	moscomprofilerHTML::yesnoSelectList( 'params[invite_reject]', 'class="form-control"', (int) $this->input( 'post/params.invite_reject', $row->params()->get( 'invite_reject', $this->params->get( 'notifications_default_invite_reject', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );

		HTML_groupjiveNotifications::showNotifications( $row, $input, $group, $user, $this );
	}

	/**
	 * reject all group invites
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function rejectGroupInvites( $id, $user )
	{
		global $_CB_framework, $_CB_database;

		$group				=	CBGroupJive::getGroup( $id );

		if ( ! $user->get( 'id' ) ) {
			$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'reject', 'id' => (int) $group->get( 'id' ) ) );

			cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => base64_encode( $returnUrl ) ) ), CBTxt::T( 'Please Login or Register to reject invites.' ) );
		} else {
			$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

			if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			}
		}

		$query				=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_invites' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
							.	"\n AND " . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
							.	"\n AND ( ( " . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) )
							.	' AND ' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
							.	' OR ( ' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' )
							.	' AND ' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';
		$_CB_database->setQuery( $query );
		$rows				=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\InviteTable', array( $_CB_database ) );

		if ( ! $rows ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You have no invites to this group to reject.' ), 'error' );
		}

		CBGroupJive::getInvite( $rows );
		CBGroupJive::preFetchUsers( $rows );

		/** @var InviteTable[] $rows */
		foreach ( $rows as $row ) {
			if ( ! $row->canDelete() ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_INVITE_FAILED_TO_REJECT', 'Group invites failed to reject. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			if ( ! $row->delete() ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_INVITE_FAILED_TO_REJECT', 'Group invites failed to reject. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			CBGroupJive::sendNotifications( 'invite_reject', CBTxt::T( 'Group invite rejected' ), CBTxt::T( 'Your group [group] invite to [user] has been rejected!' ), $group, $user, (int) $row->get( 'user_id' ) );
		}

		CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group invites rejected successfully!' ) );
	}

	/**
	 * cancel group join request
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function cancelGroupJoin( $id, $user )
	{
		global $_CB_framework;

		$group				=	CBGroupJive::getGroup( $id );

		if ( ! $user->get( 'id' ) ) {
			$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'cancel', 'id' => (int) $group->get( 'id' ) ) );

			cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => base64_encode( $returnUrl ) ) ), CBTxt::T( 'Please Login or Register to cancel join requests.' ) );
		} else {
			$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

			if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			}
		}

		$row				=	new \CB\Plugin\GroupJive\Table\UserTable();

		$row->load( array( 'user_id' => (int) $user->get( 'id' ), 'group' => (int) $group->get( 'id' ), 'status' => 0 ) );

		if ( ! $row->get( 'id' ) ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You have no pending join request to this group to cancel.' ), 'error' );
		}

		if ( ! $row->canDelete() ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_JOIN_FAILED_TO_CANCEL', 'Group join request failed to cancel. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_JOIN_FAILED_TO_CANCEL', 'Group join request failed to cancel. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		CBGroupJive::sendNotifications( 'user_cancel', CBTxt::T( 'User group join request cancelled' ), CBTxt::T( '[user] has cancelled their join request to the group [group]!' ), $group, (int) $row->get( 'user_id' ), null, null, 2 );

		CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group join request cancelled successfully!' ) );
	}

	/**
	 * join group safely
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function joinGroup( $id, $user )
	{
		global $_CB_framework, $_CB_database, $_PLUGINS;

		$group					=	CBGroupJive::getGroup( $id );
		$isModerator			=	CBGroupJive::isModerator( $user->get( 'id' ) );

		if ( ! $user->get( 'id' ) ) {
			$returnUrl			=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'join', 'id' => (int) $group->get( 'id' ) ) );

			cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => base64_encode( $returnUrl ) ) ), CBTxt::T( 'Please Login or Register to join groups.' ) );
		} else {
			$returnUrl			=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

			if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			}
		}

		$row					=	new \CB\Plugin\GroupJive\Table\UserTable();

		$row->load( array( 'user_id' => (int) $user->get( 'id' ), 'group' => (int) $group->get( 'id' ) ) );

		if ( $row->get( 'id' ) ) {
			switch ( (int) $row->get( 'status' ) ) {
				case -1:
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You have been banned from this group.' ), 'error' );
					break;
				case 0:
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Your group join request is currently pending approval.' ) );
					break;
				default:
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You have already joined this group.' ), 'error' );
					break;
			}
		}

		$query					=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_invites' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND " . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
								.	"\n AND ( ( " . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) )
								.	' AND ' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
								.	' OR ( ' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' )
								.	' AND ' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';
		$_CB_database->setQuery( $query );
		$invites				=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\InviteTable', array( $_CB_database ) );

		CBGroupJive::getInvite( $invites );
		CBGroupJive::preFetchUsers( $invites );

		if ( $row->get( 'type' ) == 3 ) {
			if ( ! $invites ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You have not been invited to join this group.' ), 'error' );
			}
		}

		$row->set( 'user_id', (int) $user->get( 'id' ) );
		$row->set( 'group', (int) $group->get( 'id' ) );
		$row->set( 'status', ( $isModerator ? 1 : ( $group->get( 'type' ) == 2 ? 0 : 1 ) ) );

		$_PLUGINS->trigger( 'gj_onBeforeJoinGroup', array( &$row, $group, $user ) );

		if ( $row->getError() || ( ! $row->check() ) ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_JOIN_FAILED', 'Group join failed. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_JOIN_FAILED', 'Group join failed. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		$notified				=	array( $row->get( 'user_id' ) );

		/** @var InviteTable[] $invites */
		foreach ( $invites as $invite ) {
			if ( $invite->accept() && ( ! in_array( $invite->get( 'user_id' ), $notified ) ) ) {
				CBGroupJive::sendNotifications( 'invite_accept', CBTxt::T( 'Group invite accepted' ), CBTxt::T( 'Your group [group] invite to [user] has been accepted!' ), $group, $user, (int) $invite->get( 'user_id' ), $notified );

				$notified[]		=	$invite->get( 'user_id' );
			}
		}

		if ( $row->get( 'status' ) == 0 ) {
			CBGroupJive::sendNotifications( 'user_approve', CBTxt::T( 'User group join request awaiting approval' ), CBTxt::T( '[user] has joined the group [group] and is awaiting approval!' ), $group, (int) $row->get( 'user_id' ), null, $notified, 2 );
		} else {
			CBGroupJive::sendNotifications( 'user_join', CBTxt::T( 'User joined a mutual group' ), CBTxt::T( '[user] has joined the group [group]!' ), $group, (int) $row->get( 'user_id' ), null, $notified );
		}

		$_PLUGINS->trigger( 'gj_onAfterJoinGroup', array( $row, $group, $user ) );

		if ( $row->get( 'status' ) == 0 ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Your group join request is currently pending approval!' ) );
		} else {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group joined successfully!' ) );
		}
	}

	/**
	 * leave group safely
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function leaveGroup( $id, $user )
	{
		global $_CB_framework, $_PLUGINS;

		$group						=	CBGroupJive::getGroup( $id );

		if ( ! $user->get( 'id' ) ) {
			$returnUrl				=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'leave', 'id' => (int) $group->get( 'id' ) ) );

			cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => base64_encode( $returnUrl ) ) ), CBTxt::T( 'Please Login or Register to leave groups.' ) );
		} else {
			$returnUrl				=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

			if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			}
		}

		$row						=	new \CB\Plugin\GroupJive\Table\UserTable();

		$row->load( array( 'user_id' => (int) $user->get( 'id' ), 'group' => (int) $group->get( 'id' ) ) );

		if ( ! $row->get( 'id' ) ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You can not leave a group you have not joined.' ), 'error' );
		} elseif ( $row->get( 'status' ) == 4 ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You are the owner of this group and can not leave your own group.' ), 'error' );
		}

		$_PLUGINS->trigger( 'gj_onBeforeLeaveGroup', array( &$row, $group, $user ) );

		if ( ! $row->canDelete() ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_LEAVE_FAILED', 'Group leave failed. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_LEAVE_FAILED', 'Group leave failed. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		CBGroupJive::sendNotifications( 'user_leave', CBTxt::T( 'User left a mutual group' ), CBTxt::T( '[user] has left the group [group]!' ), $group, (int) $row->get( 'user_id' ) );

		$_PLUGINS->trigger( 'gj_onAfterLeaveGroup', array( $row, $group, $user ) );

		CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Left group successfully!' ) );
	}

	/**
	 * set group publish state status
	 *
	 * @param int       $state
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function stateGroup( $state, $id, $user )
	{
		global $_CB_framework;

		$row				=	CBGroupJive::getGroup( $id );
		$returnUrl			=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );

		if ( CBGroupJive::canAccessGroup( $row, $user ) ) {
			if ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $user->get( 'id' ) == $row->get( 'user_id' ) ) && ( $row->get( 'published' ) == -1 ) && $this->params->get( 'groups_create_approval', 0 ) ) {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Your group is awaiting approval.' ), 'error' );
				} elseif ( $user->get( 'id' ) != $row->get( 'user_id' ) ) {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to publish or unpublish this group.' ), 'error' );
				}
			}
		} else {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		}

		$currentState		=	(int) $row->get( 'published' );

		$row->set( 'published', (int) $state );

		if ( $row->getError() || ( ! $row->store() ) ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_STATE_FAILED_TO_SAVE', 'Group state failed to saved. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $state && ( $currentState == -1 ) && ( $row->get( 'user_id' ) != $user->get( 'id' ) ) ) {
			CBGroupJive::sendNotification( 4, $user, (int) $row->get( 'user_id' ), CBTxt::T( 'Group create request accepted' ), CBTxt::T( 'Your group [group] create request has been accepted!' ), $row );
		}

		CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group state saved successfully!' ) );
	}

	/**
	 * delete group
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteGroup( $id, $user )
	{
		global $_CB_framework;

		$row			=	CBGroupJive::getGroup( $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $row->get( 'category' ) ) );

		if ( CBGroupJive::canAccessGroup( $row, $user ) ) {
			if ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( $user->get( 'id' ) != $row->get( 'user_id' ) ) {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to delete this group.' ), 'error' );
				}
			}
		} else {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		}

		if ( ! $row->canDelete() ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_FAILED_TO_DELETE', 'Group failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'GROUP_FAILED_TO_DELETE', 'Group failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group deleted successfully!' ) );
	}

	/**
	 * prepare frontend group edit render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showGroupEdit( $id, $user )
	{
		global $_CB_framework;

		$row								=	CBGroupJive::getGroup( $id );
		$isModerator						=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$categoryId							=	$this->input( 'category', null, GetterInterface::INT );

		if ( $categoryId === null ) {
			$category						=	$row->category();
		} else {
			$category						=	CBGroupJive::getCategory( $categoryId );
		}

		if ( $row->get( 'id' ) ) {
			$returnUrl						=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );
		} else {
			$returnUrl						=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $category->get( 'id' ) ) );
		}

		if ( ! $isModerator ) {
			if ( ( $categoryId !== null ) && ( ! $category->get( 'id' ) ) && ( ! $this->params->get( 'groups_uncategorized', 1 ) ) ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Category does not exist.' ), 'error' );
			} elseif ( $category->get( 'id' ) && ( ( ! $category->get( 'published' ) ) || ( ! CBGroupJive::canAccess( (int) $category->get( 'access' ), (int) $user->get( 'id' ) ) ) ) ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have access to this category.' ), 'error' );
			} elseif ( $row->get( 'id' ) ) {
				if ( $user->get( 'id' ) != $row->get( 'user_id' ) ) {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this group.' ), 'error' );
				}
			} elseif ( ! CBGroupJive::canCreateGroup( $user, ( $categoryId === null ? null : $category ) ) ) {
				if ( $category->get( 'id' ) ) {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to create a group in this category.' ), 'error' );
				} else {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to create a group.' ), 'error' );
				}
			}
		}

		CBGroupJive::getTemplate( 'group_edit' );

		$input								=	array();

		$publishedTooltip					=	cbTooltip( null, CBTxt::T( 'Select publish state of this group. Unpublished groups will not be visible to the public.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['published']					=	moscomprofilerHTML::yesnoSelectList( 'published', 'class="form-control"' . $publishedTooltip, (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) );

		$categories							=	CBGroupJive::getCategoryOptions();

		if ( $row->get( 'id' ) && $category->get( 'id' ) ) {
			$available						=	array();

			foreach ( $categories as $option ) {
				$available[]				=	(int) $option->value;
			}

			if ( ! in_array( (int) $category->get( 'id' ), $available ) ) {
				array_unshift( $categories, moscomprofilerHTML::makeOption( (int) $category->get( 'id' ), CBTxt::T( $category->get( 'name' ) ) ) );
			}
		}

		if ( $categories ) {
			if ( $this->params->get( 'groups_uncategorized', 1 ) || $isModerator ) {
				array_unshift( $categories, moscomprofilerHTML::makeOption( 0, CBTxt::T( 'Uncategorized' ) ) );
			}

			$categoryTooltip				=	cbTooltip( null, CBTxt::T( 'Select the group category. This is the category a group will belong to and decide its navigation path.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['category']				=	moscomprofilerHTML::selectList( $categories, 'category', 'class="form-control"' . $categoryTooltip, 'value', 'text', (int) $category->get( 'id' ), 1, false, false );
		} else {
			$input['category']				=	null;

			if ( ! $this->params->get( 'groups_uncategorized', 1 ) ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to create a group in any categories.' ), 'error' );
			}
		}

		$types								=	explode( '|*|', $category->get( 'types' ) );
		$listType							=	array();

		if ( in_array( 1, $types ) || $isModerator || ( ! $category->get( 'id' ) ) || ( ! $types ) ) {
			$listType[]						=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Open' ) );
		}

		if ( in_array( 2, $types ) || $isModerator || ( ! $category->get( 'id' ) ) || ( ! $types ) ) {
			$listType[]						=	moscomprofilerHTML::makeOption( '2', CBTxt::T( 'Approval' ) );
		}

		if ( in_array( 3, $types ) || $isModerator || ( ! $category->get( 'id' ) ) || ( ! $types ) ) {
			$listType[]						=	moscomprofilerHTML::makeOption( '3', CBTxt::T( 'Invite' ) );
		}

		$typeTooltip						=	cbTooltip( null, CBTxt::T( 'Select the group type. Type determines the way your group is joined (e.g. Invite requires new users to be invited to join your group).' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['type']						=	moscomprofilerHTML::selectList( $listType, 'type', 'class="form-control"' . $typeTooltip, 'value', 'text', (int) $this->input( 'post/type', $row->get( 'type', 1 ), GetterInterface::INT ), 1, false, false );

		$nameTooltup						=	cbTooltip( null, CBTxt::T( 'Input the group name. This is the name that will distinguish this group from others. Suggested to input something unique and intuitive.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['name']						=	'<input type="text" id="name" name="name" value="' . htmlspecialchars( $this->input( 'post/name', $row->get( 'name' ), GetterInterface::STRING ) ) . '" class="form-control" size="25"' . $nameTooltup . ' />';

		$descriptionTooltip					=	cbTooltip( null, CBTxt::T( 'Optionally input the group description. The group description should be short and to the point; describing what your group is all about.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['description']				=	'<textarea id="description" name="description" class="form-control" cols="40" rows="5"' . $descriptionTooltip . '>' . htmlspecialchars( $this->input( 'post/description', $row->get( 'description' ), GetterInterface::STRING ) ) . '</textarea>';

		$listMethods						=	array();
		$listMethods[]						=	moscomprofilerHTML::makeOption( 0, CBTxt::T( 'No Change' ) );
		$listMethods[]						=	moscomprofilerHTML::makeOption( 1, CBTxt::T( 'Upload' ) );
		$listMethods[]						=	moscomprofilerHTML::makeOption( 2, CBTxt::T( 'Delete' ) );

		$input['canvas_method']				=	moscomprofilerHTML::selectList( $listMethods, 'canvas_method', 'class="form-control"', 'value', 'text', $this->input( 'post/canvas_method', 0, GetterInterface::INT ), 1, false, false );

		$canvasMinFileSize					=	(int) $this->params->get( 'canvas_min_size', 0 );
		$canvasMaxFileSize					=	(int) $this->params->get( 'canvas_max_size', 1024 );

		$canvasValidation					=	array();

		if ( $canvasMinFileSize || $canvasMaxFileSize ) {
			$canvasValidation[]				=	cbValidator::getRuleHtmlAttributes( 'filesize', array( $canvasMinFileSize, $canvasMaxFileSize, 'KB' ) );
		}

		$canvasValidation[]					=	cbValidator::getRuleHtmlAttributes( 'extension', 'jpg,jpeg,gif,png' );

		$canvasTooltip						=	cbTooltip( null, CBTxt::T( 'Optionally select the group canvas. A canvas should represent the topic of your group; please be respectful and tasteful when selecting a canvas.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['canvas']					=	'<input type="file" id="canvas" name="canvas" value="" class="form-control"' . $canvasTooltip . implode( ' ', $canvasValidation ) . ' />';

		$input['canvas_limits']				=	array( CBTxt::T( 'CANVAS_UPLOAD_LIMITS_EXT', 'Your file must be of [ext] type.', array( '[ext]' => 'jpg, jpeg, gif, png' ) ) );

		if ( $canvasMinFileSize ) {
			$input['canvas_limits'][]		=	CBTxt::T( 'CANVAS_UPLOAD_LIMITS_MIN', 'Your file should exceed [size].', array( '[size]' => CBGroupJive::getFormattedFileSize( $canvasMinFileSize * 1024 ) ) );
		}

		if ( $canvasMaxFileSize ) {
			$input['canvas_limits'][]		=	CBTxt::T( 'CANVAS_UPLOAD_LIMITS_MAX', 'Your file should not exceed [size].', array( '[size]' => CBGroupJive::getFormattedFileSize( $canvasMaxFileSize * 1024 ) ) );
		}

		$input['logo_method']				=	moscomprofilerHTML::selectList( $listMethods, 'logo_method', 'class="form-control"', 'value', 'text', $this->input( 'post/logo_method', 0, GetterInterface::INT ), 1, false, false );

		$logoMinFileSize					=	(int) $this->params->get( 'logo_min_size', 0 );
		$logoMaxFileSize					=	(int) $this->params->get( 'logo_max_size', 1024 );

		$logoValidation						=	array();

		if ( $logoMinFileSize || $logoMaxFileSize ) {
			$logoValidation[]				=	cbValidator::getRuleHtmlAttributes( 'filesize', array( $logoMinFileSize, $logoMaxFileSize, 'KB' ) );
		}

		$logoValidation[]					=	cbValidator::getRuleHtmlAttributes( 'extension', 'jpg,jpeg,gif,png' );

		$logoTooltip						=	cbTooltip( null, CBTxt::T( 'Optionally select the group logo. A logo should represent the topic of your group; please be respectful and tasteful when selecting a logo.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['logo']						=	'<input type="file" id="logo" name="logo" value="" class="form-control"' . $logoTooltip . implode( ' ', $logoValidation ) . ' />';

		$input['logo_limits']				=	array( CBTxt::T( 'LOGO_UPLOAD_LIMITS_EXT', 'Your file must be of [ext] type.', array( '[ext]' => 'jpg, jpeg, gif, png' ) ) );

		if ( $logoMinFileSize ) {
			$input['logo_limits'][]			=	CBTxt::T( 'LOGO_UPLOAD_LIMITS_MIN', 'Your file should exceed [size].', array( '[size]' => CBGroupJive::getFormattedFileSize( $logoMinFileSize * 1024 ) ) );
		}

		if ( $logoMaxFileSize ) {
			$input['logo_limits'][]			=	CBTxt::T( 'LOGO_UPLOAD_LIMITS_MAX', 'Your file should not exceed [size].', array( '[size]' => CBGroupJive::getFormattedFileSize( $logoMaxFileSize * 1024 ) ) );
		}

		$invitesTooltip						=	cbTooltip( null, CBTxt::T( 'Optionally enable or disable usage of invites. Invites allow group users to invite other users to join the group. Group owner and group administrators are exempt from this configuration and can always invite users. Note existing invites will still be accessible.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['invites']					=	moscomprofilerHTML::yesnoSelectList( 'params[invites]', 'class="form-control"' . $invitesTooltip, (int) $this->input( 'post/params.invites', $row->params()->get( 'invites', 1 ), GetterInterface::INT ), CBTxt::T( 'Enable' ), CBTxt::T( 'Disable' ), false );

		$ownerTooltip						=	cbTooltip( null, CBTxt::T( 'Input the group owner id. Group owner determines the creator of the group specified as User ID.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['user_id']					=	'<input type="text" id="user_id" name="user_id" value="' . (int) $this->input( 'post/user_id', $this->input( 'user', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ), GetterInterface::INT ) . '" class="digits required form-control" size="6"' . $ownerTooltip . ' />';

		HTML_groupjiveGroupEdit::showGroupEdit( $row, $input, $category, $user, $this );
	}

	/**
	 * save group
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveGroupEdit( $id, $user )
	{
		global $_CB_framework, $_PLUGINS;

		$input							=	$this->getInput();
		$files							=	$input->getNamespaceRegistry( 'files' );

		$row							=	CBGroupJive::getGroup( $id );

		$row->set( '_input', $input );
		$row->set( '_files', $files );

		$isModerator		=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$categoryId			=	$this->input( 'category', null, GetterInterface::INT );

		if ( $categoryId === null ) {
			$category		=	$row->category();
		} else {
			$category		=	CBGroupJive::getCategory( $categoryId );
		}

		if ( $row->get( 'id' ) ) {
			$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );
		} else {
			$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $category->get( 'id' ) ) );
		}

		if ( ! $isModerator ) {
			if ( ( ! $category->get( 'id' ) ) && ( ! $this->params->get( 'groups_uncategorized', 1 ) ) ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Category does not exist.' ), 'error' );
			} elseif ( $category->get( 'id' ) && ( ( ! $category->get( 'published' ) ) || ( ! CBGroupJive::canAccess( (int) $category->get( 'access' ), (int) $user->get( 'id' ) ) ) ) ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have access to this category.' ), 'error' );
			} elseif ( $row->get( 'id' ) ) {
				if ( $user->get( 'id' ) != $row->get( 'user_id' ) ) {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this group.' ), 'error' );
				}
			} elseif ( ! CBGroupJive::canCreateGroup( $user, $category ) ) {
				if ( $category->get( 'id' ) ) {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to create a group in this category.' ), 'error' );
				} else {
					CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to create a group.' ), 'error' );
				}
			}
		}

		if ( $isModerator ) {
			$row->set( 'user_id', (int) $this->input( 'post/user_id', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ) );
		} else {
			$row->set( 'user_id', (int) $row->get( 'user_id', $user->get( 'id' ) ) );
		}

		$row->set( 'published', ( $isModerator || ( $row->get( 'id' ) && ( $row->get( 'published' ) != -1 ) ) || ( ! $this->params->get( 'groups_create_approval', 0 ) ) ? (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) : -1 ) );
		$row->set( 'category', (int) $category->get( 'id' ) );
		$row->set( 'type', (int) $this->input( 'post/type', $row->get( 'type', 1 ), GetterInterface::INT ) );
		$row->set( 'name', $this->input( 'post/name', $row->get( 'name' ), GetterInterface::STRING ) );
		$row->set( 'description', $this->input( 'post/description', $row->get( 'description' ), GetterInterface::STRING ) );
		$row->set( 'ordering', (int) $row->get( 'ordering', 1 ) );

		foreach ( $this->getInput()->subTree( 'params' ) as $k => $v ) {
			if ( is_array( $v ) || is_object( $v ) ) {
				continue;
			}

			$k				=	Get::clean( $k, GetterInterface::COMMAND );

			if ( $k ) {
				if ( is_numeric( $v ) ) {
					$v		=	(int) $this->input( 'post/params.' . $k, null, GetterInterface::INT );
				} else {
					$v		=	$this->input( 'post/params.' . $k, null, GetterInterface::STRING );
				}

				$row->params()->set( $k, $v );
			}
		}

		$row->set( 'params', $row->params()->asJson() );

		if ( ( ! $isModerator ) && $this->params->get( 'groups_create_captcha', 0 ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

			if ( $_PLUGINS->is_errors() ) {
				$row->setError( $_PLUGINS->getErrorMSG() );
			}
		}

		$new				=	( $row->get( 'id' ) ? false : true );

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_FAILED_TO_SAVE', 'Group failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showGroupEdit( $id, $user );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_FAILED_TO_SAVE', 'Group failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showGroupEdit( $id, $user );
			return;
		}

		if ( $row->get( 'published' ) == -1 ) {
			if ( $new ) {
				if ( $this->params->get( 'groups_create_approval_notify', 1 ) ) {
					CBGroupJive::sendNotification( 3, (int) $row->get( 'user_id' ), null, CBTxt::T( 'Group create request awaiting approval' ), CBTxt::T( '[user] has created the group [group] and is awaiting approval!' ), $row );
				}

				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group created successfully and awaiting approval!' ) );
			} else {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group saved successfully and awaiting approval!' ) );
			}
		} else {
			if ( $new ) {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group created successfully!' ) );
			} else {
				CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group saved successfully!' ) );
			}
		}
	}

	/**
	 * send group message
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function sendGroupMessage( $id, $user )
	{
		global $_CB_framework, $_CB_database;

		$row					=	CBGroupJive::getGroup( $id );
		$returnUrl				=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );

		if ( CBGroupJive::canAccessGroup( $row, $user ) ) {
			if ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ! $this->params->get( 'groups_message', 0 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to messaging in this group.' ), 'error' );
				} elseif ( ( $row->get( 'published' ) == -1 ) || ( CBGroupJive::getGroupStatus( $user, $row ) < 3 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to messaging in this group.' ), 'error' );
				} elseif ( $row->params()->get( 'messaged' ) ) {
					$seconds	=	(int) $this->params->get( 'groups_message_delay', 60 );

					if ( $seconds ) {
						$diff	=	Application::Date( 'now', 'UTC' )->diff( $row->get( 'messaged' ) );

						if ( ( $diff === false ) || ( $diff->s < $seconds ) ) {
							cbRedirect( $returnUrl, CBTxt::T( 'You can not send a message to this group at this time. Please wait awhile and try again.' ), 'error' );
						}
					}
				}
			}
		} else {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		}

		$message				=	$this->input( 'post/message', null, ( $this->params->get( 'groups_message_html', false, GetterInterface::BOOLEAN ) ? GetterInterface::HTML : GetterInterface::STRING ) );

		if ( ! $message ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_MESSAGE_FAILED_TO_SEND', 'Group message failed to send! Error: [error]', array( '[error]' => CBTxt::T( 'Message not specified!' ) ) ), 'error' );

			$this->showGroupMessage( $id, $user );
			return;
		}

		$query					=	'SELECT cb.*, j.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = u.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE u." . $_CB_database->NameQuote( 'user_id' ) . " != " . (int) $user->get( 'id' )
								.	"\n AND u." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $row->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
								.	"\n AND u." . $_CB_database->NameQuote( 'status' ) . " > 0";
		$_CB_database->setQuery( $query );
		$users					=	$_CB_database->loadObjectList( null, '\CB\Database\Table\UserTable', array( $_CB_database ) );

		if ( ! $users ) {
			CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'This group has no users to message.' ) );
		} else {
			$subject			=	$this->input( 'post/subject', null, GetterInterface::STRING );

			if ( $subject && ( $this->params->get( 'groups_message_type', 2, GetterInterface::INT ) == 1 ) && $this->params->get( 'groups_message_subject', false, GetterInterface::BOOLEAN ) ) {
				$subject		=	CBTxt::T( 'GROUP_MESSAGE_SUBJECT', 'Group message - [subject]', array( '[subject]' => $subject ) );
			} else {
				$subject		=	CBTxt::T( 'Group message' );
			}

			CBGroupJive::preFetchUsers( $users );

			foreach ( $users as $usr ) {
				CBGroupJive::sendNotification( $this->params->get( 'groups_message_type', 2 ), $user, $usr, $subject, CBTxt::T( 'GROUP_MESSAGE', 'Group [group] has sent the following message.<p>[message]</p>', array( '[message]' => $message ) ), $row );
			}
		}

		$row->params()->set( 'messaged', Application::Database()->getUtcDateTime() );

		$row->set( 'params', $row->params()->asJson() );

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_FAILED_TO_SAVE', 'Group failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showGroupMessage( $id, $user );
			return;
		}

		CBGroupJive::returnRedirect( $returnUrl, CBTxt::T( 'Group messaged successfully!' ) );
	}

	/**
	 * set group user status
	 *
	 * @param int       $status
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function statusUser( $status, $id, $user )
	{
		global $_CB_framework;

		$row						=	CBGroupJive::getUser( $id );
		$returnUrl					=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->group()->get( 'id' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( $row->get( 'user_id' ) == $row->group()->get( 'user_id' ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You can not promote or demote the group owner.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( $user->get( 'id' ) == $row->get( 'user_id' ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You can not promote or demote your self.' ), 'error' );
				} elseif ( $user->get( 'id' ) != $row->group()->get( 'user_id' ) ) {
					$userStatus		=	CBGroupJive::getGroupStatus( $user, $row->group() );

					if ( ( $row->get( 'status' ) > $userStatus ) || ( ( $userStatus <= 1 ) && in_array( $status, array( -1, 1 ) ) ) || ( ( $userStatus <= 2 ) && ( $status == 2 ) ) || in_array( $status, array( 0, 3, 4 ) ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to promote or demote this user.' ), 'error' );
					}
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'User does not exist.' ), 'error' );
		}

		$currentStatus				=	(int) $row->get( 'status' );

		$row->set( 'status', (int) $status );

		if ( $row->getError() || ( ! $row->store() ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_USER_STATUS_FAILED_TO_SAVE', 'User status failed to saved. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $status && ( $currentStatus == 0 ) ) {
			if ( $row->get( 'user_id' ) != $user->get( 'id' ) ) {
				CBGroupJive::sendNotification( 4, $user, (int) $row->get( 'user_id' ), CBTxt::T( 'Group join request accepted' ), CBTxt::T( 'Your join request to group [group] has been accepted!' ), $row->group() );
			}

			CBGroupJive::sendNotifications( 'user_join', CBTxt::T( 'User joined a mutual group' ), CBTxt::T( '[user] has joined the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ) );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'User status saved successfully!' ) );
	}

	/**
	 * delete user
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteUser( $id, $user )
	{
		global $_CB_framework;

		$row						=	CBGroupJive::getUser( $id );
		$returnUrl					=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->group()->get( 'id' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( $row->get( 'user_id' ) == $row->group()->get( 'user_id' ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You can not delete the group owner.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( $user->get( 'id' ) == $row->get( 'user_id' ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You can not delete your self.' ), 'error' );
				} elseif ( $user->get( 'id' ) != $row->group()->get( 'user_id' ) ) {
					$userStatus		=	CBGroupJive::getGroupStatus( $user, $row->group() );

					if ( ( $userStatus < 3 ) || ( $row->get( 'status' ) > $userStatus ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to delete this user.' ), 'error' );
					}
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'User does not exist.' ), 'error' );
		}

		if ( ! $row->canDelete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_USER_FAILED_TO_DELETE', 'User failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_USER_FAILED_TO_DELETE', 'User failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ( $row->get( 'status' ) == 0 ) && ( $row->get( 'user_id' ) != $user->get( 'id' ) ) ) {
			CBGroupJive::sendNotification( 4, $user, (int) $row->get( 'user_id' ), CBTxt::T( 'Group join request rejected' ), CBTxt::T( 'Your join request to group [group] has been rejected!' ), $row->group() );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'User deleted successfully!' ) );
	}

	/**
	 * prepare frontend invite edit render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showInviteEdit( $id, $user )
	{
		global $_CB_framework;

		$row							=	CBGroupJive::getInvite( $id );
		$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$groupId						=	$this->input( 'group', null, GetterInterface::INT );

		if ( $groupId === null ) {
			$group						=	$row->group();
		} else {
			$group						=	CBGroupJive::getGroup( $groupId );
		}

		$returnUrl						=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this invite.' ), 'error' );
		} elseif ( ! $isModerator ) {
			if ( ( $row->get( 'published' ) == -1 ) || ( ( ! $this->params->get( 'groups_invites_display', 1 ) ) && ( $group->get( 'type' ) != 3 ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to invites in this group.' ), 'error' );
			} elseif ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'invites' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to create an invite in this group.' ), 'error' );
			}
		}

		CBGroupJive::getTemplate( 'invite_edit' );

		$input								=	array();

		$inviteBy							=	array();
		$inviteByLimit						=	explode( '|*|', $this->params->get( 'groups_invites_by', '1|*|2|*|3|*|4' ) );

		if ( ! $inviteByLimit ) {
			$inviteByLimit					=	array( 1, 2, 3, 4 );
		}

		if ( in_array( 1, $inviteByLimit ) ) {
			$inviteBy[]						=	CBTxt::T( 'User ID' );
		}

		if ( in_array( 2, $inviteByLimit ) ) {
			$inviteBy[]						=	CBTxt::T( 'Username' );
		}

		if ( in_array( 3, $inviteByLimit ) ) {
			$inviteBy[]						=	CBTxt::T( 'Name' );
		}

		if ( in_array( 4, $inviteByLimit ) ) {
			$inviteBy[]						=	CBTxt::T( 'Email Address' );
		}

		$input['invite_by']					=	$inviteBy;

		$listConnections					=	array();

		if ( Application::Config()->get( 'allowConnections' ) ) {
			$cbConnection					=	new cbConnection( (int) $user->get( 'id' ) );

			foreach( $cbConnection->getConnectedToMe( (int) $user->get( 'id' ) ) as $connection ) {
				$listConnections[]			=	moscomprofilerHTML::makeOption( (string) $connection->id, getNameFormat( $connection->name, $connection->username, Application::Config()->get( 'name_format', 3 ) ) );
			}
		}

		if ( $listConnections ) {
			array_unshift( $listConnections, moscomprofilerHTML::makeOption( '0', CBTxt::T( '- Select Connection -' ) ) );

			$listTooltip					=	cbTooltip( null, CBTxt::T( 'Select a connection to invite.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['list']					=	moscomprofilerHTML::selectList( $listConnections, 'selected', 'class="gjInviteConnection form-control"' . $listTooltip, 'value', 'text', (int) $this->input( 'post/selected', 0, GetterInterface::INT ), 1, false, false );
		} else {
			$input['list']					=	null;
		}

		$toTooltup							=	cbTooltip( null, CBTxt::T( 'GROUP_INVITE_BY', 'Input the recipient as [invite_by].', array( '[invite_by]' => implode( ', ', $inviteBy ) ) ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['to']						=	'<input type="text" id="to" name="to" value="' . htmlspecialchars( $this->input( 'post/to', ( $row->get( 'user' ) ? (int) $row->get( 'user' ) : $row->get( 'email' ) ), GetterInterface::STRING ) ) . '" class="gjInviteOther form-control" size="40"' . $toTooltup . ' />';

		$messageTooltip						=	cbTooltip( null, CBTxt::T( 'Optionally input private message to include with the invite.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['message']					=	'<textarea id="message" name="message" class="form-control" cols="40" rows="5"' . $messageTooltip . '>' . htmlspecialchars( $this->input( 'post/message', $row->get( 'message' ), GetterInterface::STRING ) ) . '</textarea>';

		HTML_groupjiveInviteEdit::showInviteEdit( $row, $input, $group, $user, $this );
	}

	/**
	 * save invite
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveInviteEdit( $id, $user )
	{
		global $_CB_framework, $_CB_database, $_PLUGINS;

		$row							=	CBGroupJive::getInvite( $id );
		$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$groupId						=	$this->input( 'group', null, GetterInterface::INT );

		if ( $groupId === null ) {
			$group						=	$row->group();
		} else {
			$group						=	CBGroupJive::getGroup( $groupId );
		}

		$returnUrl						=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this invite.' ), 'error' );
		} elseif ( ! $isModerator ) {
			if ( ( $group->get( 'published' ) == -1 ) || ( ( ! $this->params->get( 'groups_invites_display', 1 ) ) && ( $group->get( 'type' ) != 3 ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to invites in this group.' ), 'error' );
			} elseif ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'invites' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to create an invite in this group.' ), 'error' );
			}
		}

		$skipCaptcha					=	false;

		$row->set( 'message', $this->input( 'post/message', $row->get( 'message' ), GetterInterface::STRING ) );

		if ( ! $row->get( 'id' ) ) {
			$row->set( 'user_id', (int) $row->get( 'user_id', $user->get( 'id' ) ) );
			$row->set( 'group', (int) $group->get( 'id' ) );

			$to							=	$this->input( 'post/to', null, GetterInterface::STRING );
			$selected					=	(int) $this->input( 'post/selected', 0, GetterInterface::INT );

			if ( $selected ) {
				$token					=	$this->input( 'post/token', null, GetterInterface::STRING );

				if ( $token ) {
					if ( $token == md5( $row->get( 'user_id' ) . $to . $row->get( 'group' ) . $row->get( 'message' ) . $_CB_framework->getCfg( 'secret' ) ) ) {
						$skipCaptcha	=	true;

						$row->set( 'user', (int) $selected );
					}
				} elseif ( $this->params->get( 'groups_invites_list', 0 ) ) {
					$connections		=	array();
					$cbConnection		=	new cbConnection( (int) $user->get( 'id' ) );

					foreach( $cbConnection->getConnectedToMe( (int) $user->get( 'id' ) ) as $connection ) {
						$connections[]	=	(int) $connection->id;
					}

					if ( in_array( $selected, $connections ) ) {
						$row->set( 'user', (int) $selected );
					}
				}
			} elseif ( $to ) {
				$inviteByLimit			=	explode( '|*|', $this->params->get( 'groups_invites_by', '1|*|2|*|3|*|4' ) );

				if ( ! $inviteByLimit ) {
					$inviteByLimit		=	array( 1, 2, 3, 4 );
				}

				$recipient				=	new UserTable();

				if ( in_array( 1, $inviteByLimit ) && $recipient->load( (int) $to ) ) {
					$row->set( 'user', (int) $recipient->get( 'id' ) );
				} elseif ( in_array( 4, $inviteByLimit ) && cbIsValidEmail( $to ) ) {
					if ( $recipient->load( array( 'email' => $to ) ) ) {
						$row->set( 'user', (int) $recipient->get( 'id' ) );
					} else {
						$row->set( 'email', $to );
					}
				} elseif ( in_array( 2, $inviteByLimit ) && $recipient->load( array( 'username' => $to ) ) ) {
					$row->set( 'user', (int) $recipient->get( 'id' ) );
				} elseif ( in_array( 3, $inviteByLimit ) ) {
					$query				=	'SELECT cb.' . $_CB_database->NameQuote( 'id' )
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
										.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
										.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
										.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = ' . (int) $group->get( 'id' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
										.	' ON i.' . $_CB_database->NameQuote( 'group' ) . ' = ' . (int) $group->get( 'id' )
										.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
										.	"\n WHERE j." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $to, true ) . '%', false )
										.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
										.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
										.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
										.	"\n AND u." . $_CB_database->NameQuote( 'id' ) . " IS NULL"
										.	"\n AND i." . $_CB_database->NameQuote( 'id' ) . " IS NULL"
										.	"\n ORDER BY j." . $_CB_database->NameQuote( 'registerDate' ) . " DESC";
					$_CB_database->setQuery( $query, 0, 15 );
					$users				=	$_CB_database->loadResultArray();

					if ( $users ) {
						if ( count( $users ) > 1 ) {
							CBGroupJive::getTemplate( 'invite_list' );

							CBuser::advanceNoticeOfUsersNeeded( $users );

							HTML_groupjiveInviteList::showInviteList( $to, $users, $row, $group, $user, $this );
							return;
						} else {
							$row->set( 'user', (int) $users[0] );
						}
					}
				}
			}
		}

		if ( ( ! $isModerator ) && $this->params->get( 'groups_create_captcha', 0 ) && ( ! $skipCaptcha ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

			if ( $_PLUGINS->is_errors() ) {
				$row->setError( $_PLUGINS->getErrorMSG() );
			}
		}

		$new							=	( $row->get( 'id' ) ? false : true );

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_INVITE_FAILED_TO_SAVE', 'Invite failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showInviteEdit( $id, $user );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_INVITE_FAILED_TO_SAVE', 'Invite failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showInviteEdit( $id, $user );
			return;
		}

		if ( $new ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Invite created successfully!' ) );
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Invite saved successfully!' ) );
		}
	}

	/**
	 * delete invite
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteInvite( $id, $user )
	{
		global $_CB_framework;

		$row			=	CBGroupJive::getInvite( $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( $user->get( 'id' ) != $row->get( 'user_id' ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to delete this invite.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( ! $this->params->get( 'groups_invites_display', 1 ) ) && ( $row->group()->get( 'type' ) != 3 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to invites in this group.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Invite does not exist.' ), 'error' );
		}

		if ( ! $row->canDelete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_INVITE_FAILED_TO_DELETE', 'Invite failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_INVITE_FAILED_TO_DELETE', 'Invite failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Invite deleted successfully!' ) );
	}

	/**
	 * resend invite
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function sendInvite( $id, $user )
	{
		global $_CB_framework;

		$row			=	CBGroupJive::getInvite( $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( $user->get( 'id' ) != $row->get( 'user_id' ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to send this invite.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $row->group()->get( 'published' ) == -1 ) || ( ( ! $this->params->get( 'groups_invites_display', 1 ) ) && ( $row->group()->get( 'type' ) != 3 ) ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to invites in this group.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Invite does not exist.' ), 'error' );
		}

		if ( ! $row->send() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_INVITE_FAILED_TO_SEND', 'Invite failed to send. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Invite sent successfully!' ) );
	}

	/**
	 * save notifications
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveNotifications( $id, $user )
	{
		global $_CB_framework;

		$row				=	new NotificationTable();

		$isModerator		=	CBGroupJive::isModerator( $user->get( 'id' ) );

		$group				=	CBGroupJive::getGroup( $id );

		$row->load( array( 'user_id' => (int) $user->get( 'id' ), 'group' => (int) $group->get( 'id' ) ) );

		$returnUrl			=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		} elseif ( ! $this->params->get( 'notifications', 1 ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to notifications in this group.' ), 'error' );
		} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this users notifications.' ), 'error' );
		} elseif ( ! $isModerator ) {
			if ( ! CBGroupJive::canCreateGroupContent( $user, $group ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to notifications in this group.' ), 'error' );
			}
		}

		$row->set( 'user_id', $row->get( 'user_id', (int) $user->get( 'id' ) ) );
		$row->set( 'group', $row->get( 'group', (int) $group->get( 'id' ) ) );

		foreach ( $this->getInput()->subTree( 'params' ) as $k => $v ) {
			if ( is_array( $v ) || is_object( $v ) ) {
				continue;
			}

			$k				=	Get::clean( $k, GetterInterface::COMMAND );

			if ( $k ) {
				if ( is_numeric( $v ) ) {
					$v		=	(int) $this->input( 'post/params.' . $k, null, GetterInterface::INT );
				} else {
					$v		=	$this->input( 'post/params.' . $k, null, GetterInterface::STRING );
				}

				$row->params()->set( $k, $v );
			}
		}

		$row->set( 'params', $row->params()->asJson() );

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_NOTIFICATIONS_FAILED_TO_SAVE', 'Notifications failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showGroupNotifications( $id, $user );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_NOTIFICATIONS_FAILED_TO_SAVE', 'Notifications failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showGroupNotifications( $id, $user );
			return;
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Notifications saved successfully!' ) );
	}
}