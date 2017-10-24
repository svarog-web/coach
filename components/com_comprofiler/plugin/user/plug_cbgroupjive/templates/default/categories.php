<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveCategories
{

	/**
	 * render frontend categories
	 *
	 * @param CategoryTable[]    $rows
	 * @param cbPageNav          $pageNav
	 * @param bool               $searching
	 * @param array              $input
	 * @param UserTable          $user
	 * @param CBplug_cbgroupjive $plugin
	 */
	static function showCategories( $rows, $pageNav, $searching, $input, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$_CB_framework->setPageTitle( CBTxt::T( 'Categories' ) );

		initToolTip();

		$canCreateGroup					=	CBGroupJive::canCreateGroup( $user );
		$canSearch						=	( $plugin->params->get( 'categories_search', 1 ) && ( $searching || $pageNav->total ) );
		$return							=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayCategories', array( &$return, &$rows, $user ) );

		$return							.=	'<div class="gjCategories">'
										.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'categories', 'func' => 'all' ) ) . '" method="post" name="gjCategoriesForm" id="gjCategoriesForm" class="gjCategoriesForm">';

		if ( $canCreateGroup || $canSearch ) {
			$return						.=			'<div class="gjHeader gjCategoriesHeader row">';

			if ( $canCreateGroup ) {
				$return					.=				'<div class="' . ( ! $canSearch ? 'col-sm-12' : 'col-sm-8' ) . ' text-left">'
										.					'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'groups', 'func' => 'new', 'return' => CBGroupJive::getReturn() ) ) . '\';" class="gjButton gjButtonNewGroup btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New Group' ) . '</button>'
										.				'</div>';
			}

			if ( $canSearch ) {
				$return					.=				'<div class="' . ( ! $canCreateGroup ? 'col-sm-offset-8 ' : null ) . 'col-sm-4 text-right">'
										.					'<div class="input-group">'
										.						'<span class="input-group-addon"><span class="fa fa-search"></span></span>'
										.						$input['search']
										.					'</div>'
										.				'</div>';
			}

			$return						.=			'</div>';
		}

		$return							.=			'<div class="gjCategoriesRows">';

		if ( $rows ) foreach ( $rows as $row ) {
			$cssClass					=	$row->get( 'css', null, GetterInterface::STRING );
			$counters					=	array();
			$content					=	null;

			$_PLUGINS->trigger( 'gj_onDisplayCategory', array( &$row, &$counters, &$content, $user ) );

			$return						.=				'<div class="gjCategoriesCategory gjCategoriesCategory' . $row->get( 'id', 0, GetterInterface::INT ) . ' gjCanvasBox cbCanvasBox img-thumbnail' . ( $cssClass ? ' ' . htmlspecialchars( $cssClass ) : null ) . '">'
										.					'<div class="gjCanvasBoxTop cbCanvasBoxTop bg-muted">'
										.						'<div class="gjCanvasBoxBackground cbCanvasBoxBackground">'
										.							$row->canvas( true, true )
										.						'</div>'
										.						'<div class="gjCanvasBoxPhoto cbCanvasBoxPhoto cbCanvasBoxPhotoLeft">'
										.							$row->logo( true, true, true )
										.						'</div>'
										.					'</div>'
										.					'<div class="gjCanvasBoxBottom cbCanvasBoxBottom bg-default">'
										.						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-nowrap text-overflow">'
										.							'<strong><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) ) . '">' . CBTxt::T( $row->get( 'name' ) ) . '</a></strong>'
										.							( $row->get( 'description' ) ? '<div class="gjCanvasBoxDescription">' . cbTooltip( 1, CBTxt::T( $row->get( 'description' ) ), CBTxt::T( $row->get( 'name' ) ), 400, null, '<span class="fa fa-info-circle text-muted"></span>' ) . '</div>' : null )
										.						'</div>'
										.						'<div class="gjCanvasBoxRow cbCanvasBoxRow text-muted small row">'
										.							'<div class="gjCanvasBoxCounter text-nowrap text-overflow ' . ( $counters ? 'col-sm-6' : 'col-sm-12' ) . '"><span class="gjCategoryGroupsIcon fa-before fa-users"> ' . CBTxt::T( 'GROUPS_COUNT', '%%COUNT%% Group|%%COUNT%% Groups', array( '%%COUNT%%' => (int) $row->get( '_groups', 0 ) ) ) . '</span></div>'
										.							( $counters ? '<div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">' . implode( '</div><div class="gjCanvasBoxCounter text-nowrap text-overflow col-sm-6">', $counters ) . '</div>' : null )
										.						'</div>'
										.						( $content ? '<div class="gjCanvasBoxRow cbCanvasBoxRow">' . $content . '</div>' : null )
										.					'</div>'
										.				'</div>';
		} else {
			if ( $searching ) {
				$return					.=				CBTxt::T( 'No category search results found.' );
			} else {
				$return					.=				CBTxt::T( 'There are no categories available.' );
			}
		}

		$return							.=			'</div>';

		if ( $plugin->params->get( 'categories_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return						.=			'<div class="gjCategoriesPaging text-center">'
										.				$pageNav->getListLinks()
										.			'</div>';
		}

		$return							.=			$pageNav->getLimitBox( false )
										.		'</form>'
										.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayCategories', array( &$return, &$rows, $user ) );

		$_CB_framework->setMenuMeta();

		echo $return;
	}
}