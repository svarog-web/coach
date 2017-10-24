<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveFile\Trigger;

use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveFile\CBGroupJiveFile;

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
		if ( ( $plugin != 'cbgroupjivefile' ) || ( ! $query ) || ( ! isset( $query['action'] ) ) || ( $query['action'] != 'file' ) ) {
			return;
		}

		unset( $query['action'] );

		$group						=	null;
		$file						=	null;

		if ( isset( $query['group'] ) ) {
			$group					=	CBGroupJive::getGroup( $query['group'] );

			unset( $query['group'] );
		} elseif ( isset( $query['id'] ) ) {
			$file					=	CBGroupJiveFile::getFile( $query['id'] );
			$group					=	$file->group();

			unset( $query['id'] );
		}

		if ( $group ) {
			$groupId				=	$group->get( 'id', 0, GetterInterface::INT );
			$segments[]				=	$groupId . '-' . Application::Router()->stringToAlias( CBTxt::T( $group->get( 'name', $groupId, GetterInterface::STRING ) ) );

			if ( $file ) {
				$fileId				=	$file->get( 'id', 0, GetterInterface::INT );
				$segments[]			=	$fileId . '-' . Application::Router()->stringToAlias( ( $file->get( 'title' ) ? $file->get( 'title', $fileId, GetterInterface::STRING ) : $file->name() ) );
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
		if ( ( $plugin != 'cbgroupjivefile' ) || ( ! $segments ) ) {
			return;
		}

		$count									=	count( $segments );

		if ( isset( $segments[0] ) && ( strpos( $segments[0], '-' ) !== false ) ) {
			list( $groupId, $groupAlias )		=	explode( '-', $segments[0], 2 );
		} else {
			$groupId							=	( isset( $segments[0] ) ? $segments[0] : null );
			$groupAlias							=	null;
		}

		$vars['action']							=	'file';

		if ( is_numeric( $groupId ) ) {
			if ( isset( $segments[1] ) && ( strpos( $segments[1], '-' ) !== false ) ) {
				list( $fileId, $fileAlias )		=	explode( '-', $segments[1], 2 );
			} else {
				$fileId							=	( isset( $segments[1] ) ? $segments[1] : null );
				$fileAlias						=	null;
			}

			if ( is_numeric( $fileId ) ) {
				if ( $count > 2 ) {
					$vars['func']				=	$segments[2];
				}

				$vars['id']						=	$fileId;
			} else {
				$vars['group']					=	$groupId;
				$vars['func']					=	$segments[1];
			}
		} elseif ( $count > 0 ) {
			$vars['func']						=	$segments[0];

			if ( $count > 1 ) {
				$vars['id']						=	$segments[1];
			}
		}
	}
}