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
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJiveForums\Forum\ForumInterface;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveForums\Table\PostTableInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveForums
{

	/**
	 * render frontend forums
	 *
	 * @param PostTableInterface[]  $rows
	 * @param cbPageNav             $pageNav
	 * @param bool                  $searching
	 * @param array                 $input
	 * @param array                 $counters
	 * @param GroupTable            $group
	 * @param UserTable             $user
	 * @param ForumInterface        $forum
	 * @return string
	 */
	static function showForums( $rows, $pageNav, $searching, $input, &$counters, $group, $user, $forum )
	{
		global $_CB_framework, $_PLUGINS;

		$counters[]							=	'<span class="gjGroupForumsIcon fa-before fa-comments-o"> ' . CBTxt::T( 'GROUP_FORUMS_COUNT', '%%COUNT%% Discussion|%%COUNT%% Discussions', array( '%%COUNT%%' => (int) $pageNav->total ) ) . '</span>';

		$canCreate							=	CBGroupJive::canCreateGroupContent( $user, $group, 'forums' );
		$canSearch							=	( $forum->params->get( 'groups_forums_search', 1 ) && ( $searching || $pageNav->total ) );
		$return								=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayForums', array( &$return, &$rows, $group, $user ) );

		$return								.=	'<div class="gjGroupForums">'
											.		'<form action="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) . '" method="post" name="gjGroupForumsForm" id="gjGroupForumsForm" class="gjGroupForumsForm">';

		if ( $canCreate || $canSearch ) {
			$return							.=			'<div class="gjHeader gjGroupForumsHeader row">';

			if ( $canCreate ) {
				$return						.=				'<div class="' . ( ! $canSearch ? 'col-sm-12' : 'col-sm-8' ) . ' text-left">'
											.					'<button type="button" onclick="window.location.href=\'' . $forum->getCategory( (int) $group->params()->get( 'forum_id' ) )->url() . '\';" class="gjButton gjButtonNewPost btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New Post' ) . '</button>'
											.				'</div>';
			}

			if ( $canSearch ) {
				$return						.=				'<div class="' . ( ! $canCreate ? 'col-sm-offset-8 ' : null ) . 'col-sm-4 text-right">'
											.					'<div class="input-group">'
											.						'<span class="input-group-addon"><span class="fa fa-search"></span></span>'
											.						$input['search']
											.					'</div>'
											.				'</div>';
			}

			$return							.=			'</div>';
		}

		$return								.=			'<table class="gjGroupForumsRows table table-hover table-responsive">'
											.				'<thead>'
											.					'<tr>'
											.						'<th style="width: 50%;" class="text-left">' . CBTxt::T( 'Subject' ) . '</th>'
											.						'<th style="width: 25%;" class="text-left hidden-xs">' . CBTxt::T( 'By' ) . '</th>'
											.						'<th style="width: 25%;" class="text-left hidden-xs">' . CBTxt::T( 'Date' ) . '</th>'
											.					'</tr>'
											.				'</thead>'
											.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$return							.=					'<tr>'
											.						'<td style="width: 50%;" class="text-left"><a href="' . $row->url() . '">' . $row->get( 'subject' ) . '</a></td>'
											.						'<td style="width: 25%;" class="text-left hidden-xs">' . CBuser::getInstance( (int) $row->get( 'user_id' ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</td>'
											.						'<td style="width: 25%;" class="text-left hidden-xs">' . cbFormatDate( $row->get( 'date' ) ) . '</td>'
											.					'</tr>';
		} else {
			$return							.=					'<tr>'
											.						'<td colspan="3" class="text-left">';

			if ( $searching ) {
				$return						.=							CBTxt::T( 'No group post search results found.' );
			} else {
				$return						.=							CBTxt::T( 'This group currently has no posts.' );
			}

			$return							.=						'</td>'
											.					'</tr>';
		}

		$return								.=				'</tbody>';

		if ( $forum->params->get( 'groups_forums_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return							.=				'<tfoot>'
											.					'<tr>'
											.						'<td colspan="3" class="gjGroupForumsPaging text-center">'
											.							$pageNav->getListLinks()
											.						'</td>'
											.					'</tr>'
											.				'</tfoot>';
		}

		$return								.=			'</table>'
											.			$pageNav->getLimitBox( false )
											.		'</form>'
											.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayForums', array( &$return, $rows, $group, $user ) );

		return $return;
	}
}