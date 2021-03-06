<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveEvents\Trigger;

use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveEvents\CBGroupJiveEvents;

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
		if ( ( $plugin != 'cbgroupjiveevents' ) || ( ! $query ) || ( ! isset( $query['action'] ) ) || ( $query['action'] != 'events' ) ) {
			return;
		}

		unset( $query['action'] );

		$group						=	null;
		$event						=	null;

		if ( isset( $query['group'] ) ) {
			$group					=	CBGroupJive::getGroup( $query['group'] );

			unset( $query['group'] );
		} elseif ( isset( $query['id'] ) ) {
			$event					=	CBGroupJiveEvents::getEvent( $query['id'] );
			$group					=	$event->group();

			unset( $query['id'] );
		}

		if ( $group ) {
			$groupId				=	$group->get( 'id', 0, GetterInterface::INT );
			$segments[]				=	$groupId . '-' . Application::Router()->stringToAlias( CBTxt::T( $group->get( 'name', $groupId, GetterInterface::STRING ) ) );

			if ( $event ) {
				$eventId			=	$event->get( 'id', 0, GetterInterface::INT );
				$segments[]			=	$eventId . '-' . Application::Router()->stringToAlias( $event->get( 'title', $eventId, GetterInterface::STRING ) );
			}

			if ( isset( $query['func'] ) ) {
				$segments[]			=	$query['func'];

				unset( $query['func'] );
			}
		} else {
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
		if ( ( $plugin != 'cbgroupjiveevents' ) || ( ! $segments ) ) {
			return;
		}

		$count										=	count( $segments );

		if ( isset( $segments[0] ) && ( strpos( $segments[0], '-' ) !== false ) ) {
			list( $groupId, $groupAlias )			=	explode( '-', $segments[0], 2 );
		} else {
			$groupId								=	( isset( $segments[0] ) ? $segments[0] : null );
			$groupAlias								=	null;
		}

		$vars['action']								=	'events';

		if ( is_numeric( $groupId ) ) {
			if ( isset( $segments[1] ) && ( strpos( $segments[1], '-' ) !== false ) ) {
				list( $eventId, $eventAlias )		=	explode( '-', $segments[1], 2 );
			} else {
				$eventId							=	( isset( $segments[1] ) ? $segments[1] : null );
				$eventAlias							=	null;
			}

			if ( is_numeric( $eventId ) ) {
				if ( $count > 2 ) {
					$vars['func']					=	$segments[2];
				}

				$vars['id']							=	$eventId;
			} else {
				$vars['group']						=	$groupId;
				$vars['func']						=	$segments[1];
			}
		} elseif ( $count > 0 ) {
			$vars['func']							=	$segments[0];

			if ( $count > 1 ) {
				$vars['id']							=	$segments[1];
			}
		}
	}
}