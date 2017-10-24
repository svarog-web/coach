<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive\Trigger;

use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;

defined('CBLIB') or die();

class RouterTrigger extends \cbPluginHandler
{

	/**
	 * @param \ComprofilerRouter $router
	 * @param string             $plugin
	 * @param array              $segments
	 * @param array              $query
	 * @param \JMenuSite         $menuItem
	 */
	public function build( $router, $plugin, &$segments, &$query, &$menuItem )
	{
		if ( ( $plugin != 'cbgroupjive' ) || ( ! $query ) || ( ! isset( $query['action'] ) ) ) {
			return;
		}

		$action						=	$query['action'];

		unset( $query['action'] );

		$category					=	null;
		$group						=	null;
		$id							=	null;

		switch ( $action ) {
			case 'categories':
				if ( isset( $query['id'] ) ) {
					$category		=	CBGroupJive::getCategory( $query['id'] );

					unset( $query['id'] );
				}
				break;
			case 'groups':
				if ( isset( $query['id'] ) ) {
					$group			=	CBGroupJive::getGroup( $query['id'] );

					unset( $query['id'] );
				} elseif ( isset( $query['category'] ) ) {
					$category		=	CBGroupJive::getCategory( $query['category'] );

					unset( $query['category'] );
				}
				break;
			case 'invites':
				if ( isset( $query['group'] ) ) {
					$group			=	CBGroupJive::getGroup( $query['group'] );

					unset( $query['group'] );
				} elseif ( isset( $query['id'] ) ) {
					$id				=	$query['id'];
					$group			=	CBGroupJive::getInvite( $id )->group();

					unset( $query['id'] );
				}
				break;
			case 'users':
				if ( isset( $query['id'] ) ) {
					$id				=	$query['id'];
					$group			=	CBGroupJive::getUser( $id )->group();

					unset( $query['id'] );
				}
				break;
		}

		if ( $category || $group ) {
			if ( $category ) {
				$categoryId			=	$category->get( 'id', 0, GetterInterface::INT );
				$segments[]			=	$categoryId . '-' . Application::Router()->stringToAlias( CBTxt::T( $category->get( 'name', $categoryId, GetterInterface::STRING ) ) );
			} elseif ( $group ) {
				$groupId			=	$group->get( 'id', 0, GetterInterface::INT );
				$categoryId			=	$group->category()->get( 'id', 0, GetterInterface::INT );

				if ( $categoryId ) {
					$segments[]		=	$categoryId . '-' . Application::Router()->stringToAlias( CBTxt::T( $group->category()->get( 'name', $categoryId, GetterInterface::STRING ) ) );
				}

				$segments[]			=	$groupId . '-' . Application::Router()->stringToAlias( CBTxt::T( $group->get( 'name', $groupId, GetterInterface::STRING ) ) );

				if ( $action != 'groups' ) {
					$segments[]		=	$action;
				}

				if ( $id ) {
					$segments[]		=	$id;
				}
			}

			if ( isset( $query['func'] ) ) {
				$func				=	$query['func'];

				unset( $query['func'] );

				if ( $func != 'show' ) {
					$segments[]		=	$func;
				}
			}
		} else {
			$segments[]				=	$action;

			if ( isset( $query['func'] ) ) {
				$segments[]			=	$query['func'];

				unset( $query['func'] );

				if ( isset( $query['id'] ) ) {
					$segments[]		=	$query['id'];

					unset( $query['id'] );
				}
			}
		}
	}

	/**
	 * @param \ComprofilerRouter $router
	 * @param string             $plugin
	 * @param array              $segments
	 * @param array              $vars
	 * @param \JMenuSite         $menuItem
	 */
	public function parse( $router, $plugin, $segments, &$vars, $menuItem )
	{
		if ( ( $plugin != 'cbgroupjive' ) || ( ! $segments ) ) {
			return;
		}

		$count										=	count( $segments );

		if ( isset( $segments[0] ) && ( strpos( $segments[0], '-' ) !== false ) ) {
			list( $categoryId, $categoryAlias )		=	explode( '-', $segments[0], 2 );
		} else {
			$categoryId								=	( isset( $segments[0] ) ? $segments[0] : null );
			$categoryAlias							=	null;
		}

		if ( is_numeric( $categoryId ) ) {
			if ( isset( $segments[1] ) && ( strpos( $segments[1], '-' ) !== false ) ) {
				list( $groupId, $groupAlias )		=	explode( '-', $segments[1], 2 );
			} else {
				$groupId							=	( isset( $segments[1] ) ? $segments[1] : null );
				$groupAlias							=	null;
			}

			if ( is_numeric( $groupId ) ) {
				$vars['action']						=	'groups';

				if ( $count > 2 ) {
					$vars['func']					=	$segments[2];

					if ( $vars['func'] == 'invites' ) {
						$vars['group']				=	$groupId;
					} elseif ( $vars['func'] != 'users' ) {
						$vars['id']					=	$groupId;
					}

					if ( ( $count > 3 ) && in_array( $vars['func'], array( 'notifications', 'users', 'invites' ) ) ) {
						$vars['action']				=	$vars['func'];

						if ( ( $count > 4 ) && in_array( $vars['func'], array( 'users', 'invites' ) ) ) {
							$vars['func']			=	$segments[4];
							$vars['id']				=	$segments[3];
						} else {
							$vars['func']			=	$segments[3];
						}
					}
				} else {
					$vars['id']						=	$groupId;
				}
			} else {
				$group								=	CBGroupJive::getGroup( $categoryId );

				if ( $group->get( 'id', 0, GetterInterface::INT ) && ( ! $group->category()->get( 'id', 0, GetterInterface::INT ) ) && ( ( ! $categoryAlias ) || ( $categoryAlias == Application::Router()->stringToAlias( CBTxt::T( $group->get( 'name', null, GetterInterface::STRING ) ) ) ) ) ) {
					$vars['action']					=	'groups';

					if ( $count > 1 ) {
						$vars['func']				=	$segments[1];

						if ( $vars['func'] == 'invites' ) {
							$vars['group']			=	$categoryId;
						} elseif ( $vars['func'] != 'users' ) {
							$vars['id']				=	$categoryId;
						}

						if ( ( $count > 2 ) && in_array( $vars['func'], array( 'notifications', 'users', 'invites' ) ) ) {
							$vars['action']			=	$vars['func'];

							if ( ( $count > 3 ) && in_array( $vars['func'], array( 'users', 'invites' ) ) ) {
								$vars['func']		=	$segments[3];
								$vars['id']			=	$segments[2];
							} else {
								$vars['func']		=	$segments[2];
							}
						}
					} else {
						$vars['id']					=	$categoryId;
					}
				} else {
					$vars['action']					=	'categories';

					if ( $count > 1 ) {
						if ( $segments[1] == 'new' ) {
							$vars['action']			=	'groups';
							$vars['func']			=	$segments[1];
							$vars['category']		=	$categoryId;
						} else {
							$vars['func']			=	$segments[1];
							$vars['id']				=	$categoryId;
						}
					} else {
						$vars['id']					=	$categoryId;
					}
				}
			}
		} elseif ( $count > 0 ) {
			$vars['action']							=	$segments[0];

			if ( $count > 1 ) {
				$vars['func']						=	$segments[1];

				if ( $count > 2 ) {
					$vars['id']						=	$segments[2];
				}
			}
		}
	}
}