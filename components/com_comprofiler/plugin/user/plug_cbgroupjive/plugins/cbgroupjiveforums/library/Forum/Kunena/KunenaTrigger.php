<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveForums\Forum\Kunena;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJiveForums\CBGroupJiveForums;

defined('CBLIB') or die();

class KunenaTrigger extends \cbPluginHandler
{

	/**
	 * integrates with kunena
	 *
	 * @param string $event
	 * @param $config
	 * @param $params
	 */
	public function kunena( $event, &$config, &$params )
	{
		global $_CB_database;

		if ( ( ! CBGroupJiveForums::getForum() ) || ( CBGroupJiveForums::getForum()->type != 'kunena' ) ) {
			return;
		}

		if ( $event == 'loadGroups' ) {
			$groups									=	CBGroupJive::getGroupOptions();
			$options								=	array();

			foreach ( $groups as $group ) {
				$option								=	new \stdClass();
				$option->id							=	( is_array( $group->value ) ? uniqid() : (int) $group->value );
				$option->parent_id					=	0;
				$option->level						=	( is_array( $group->value ) ? 0 : 1 );
				$option->name						=	$group->text;

				$options[$option->id]				=	$option;
			}

			$params['groups']						=	$options;
		} elseif ( $event == 'getAllowedForumsRead' ) {
			static $cache							=	array();

			$mydId									=	Application::MyUser()->getUserId();

			if ( ! $mydId ) {
				return;
			}

			if ( ! isset( $cache[$mydId] ) ) {
				$user								=	\CBuser::getMyUserDataInstance();
				$isModerator						=	CBGroupJive::isModerator( $user->get( 'id', 0, GetterInterface::INT ) );

				$query								=	'SELECT g.*'
													.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
													.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'user_id' )
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
													.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
													.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . $user->get( 'id', 0, GetterInterface::INT )
													.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
													.	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
													.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
													.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

				if ( ! $isModerator ) {
					$query							.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
													.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
													.		' AND u.' . $_CB_database->NameQuote( 'status' ) . ' > 0 ) )';
				}

				$_CB_database->setQuery( $query );
				$groups								=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );

				$allowed							=	array();

				/** @var GroupTable[] $groups */
				foreach ( $groups as $group ) {
					if ( $group->params()->get( 'forums', true, GetterInterface::BOOLEAN ) ) {
						$froumId					=	$group->params()->get( 'forum_id', 0, GetterInterface::INT );

						if ( $froumId && CBGroupJive::canCreateGroupContent( $user, $group, 'forums' ) ) {
							$allowed[]				=	$froumId;
						}
					}
				}

				$cache[$mydId]						=	$allowed;
			}

			if ( ! $cache[$mydId] ) {
				return;
			}

			$existingAccess							=	explode( ',', $params[1] );
			$cleanAccess							=	array_diff( $cache[$mydId], $existingAccess );
			$newAccess								=	array_merge( $existingAccess, $cleanAccess );

			cbArrayToInts( $newAccess );

			$params[1]								=	implode( ',', $newAccess );
		} elseif ( $event == 'authoriseUsers' ) {
			/** @var \KunenaForumCategory $category */
			$category								=	$params['category'];
			$groupId								=	$category->get( 'access' );

			if ( ( $category->get( 'accesstype' ) != 'communitybuilder' ) || ( ! $groupId ) ) {
				return;
			}

			$users									=	$params['userids'];

			if ( ! $users ) {
				return;
			}

			static $allowed							=	array();

			if ( ! isset( $allowed[$groupId] ) ) {
				$allowed[$groupId]					=	array();

				$group								=	CBGroupJive::getGroup( $groupId );

				if ( $group->get( 'id', 0, GetterInterface::INT ) ) {
					$query							=	'SELECT u.' . $_CB_database->NameQuote( 'user_id' )
													.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
													.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = u.' . $_CB_database->NameQuote( 'user_id' )
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
													.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
													.	"\n WHERE u." . $_CB_database->NameQuote( 'group' ) . " = " . $group->get( 'id', 0, GetterInterface::INT )
													.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
													.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
													.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
													.	"\n AND u." . $_CB_database->NameQuote( 'status' ) . " >= 1";
					$_CB_database->setQuery( $query );
					$allowed[$groupId]				=	$_CB_database->loadResultArray();
				}

				foreach ( $users as $userId ) {
					if ( ( ! in_array( $userId, $allowed[$groupId] ) ) && CBGroupJive::isModerator( $userId ) ) {
						$allowed[$groupId][]		=	$userId;
					}
				}

				cbArrayToInts( $allowed[$groupId] );
			}

			if ( ! $allowed[$groupId] ) {
				return;
			}

			$notify									=	array();

			foreach ( $users as $userId ) {
				if ( in_array( $userId, $allowed[$groupId] ) ) {
					$notify[]						=	(int) $userId;
				}
			}

			if ( ! $notify ) {
				return;
			}

			$params['allow']						=	$notify;
		} elseif ( $this->params->get( 'groups_forums_back', true, GetterInterface::BOOLEAN ) && ( $event == 'onStart' ) && ( $this->input( 'view', null, GetterInterface::STRING ) == 'category' ) ) {
			$categoryId								=	(int) $this->input( 'catid', 0, GetterInterface::INT );

			if ( ! $categoryId ) {
				return;
			}

			$forum									=	CBGroupJiveForums::getForum();

			if ( ! $forum ) {
				return;
			}

			$category								=	$forum->getCategory( $categoryId );

			if ( ! $category->get( 'id', 0, GetterInterface::INT ) ) {
				return;
			}

			$category								=	$category->category();

			if ( ( $category->get( 'accesstype' ) != 'communitybuilder' ) || ( ! $category->get( 'access' ) ) ) {
				return;
			}

			$group									=	CBGroupJive::getGroup( (int) $category->get( 'access' ) );

			if ( ! $group->get( 'id', 0, GetterInterface::INT ) ) {
				return;
			}

			CBGroupJive::getTemplate( 'backlink', true, true, $this->element );

			echo \HTML_groupjiveForumsBacklink::showBacklink( $group, $category, $this );
		}
	}
}