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
use CB\Plugin\GroupJiveFile\Table\FileTable;
use CB\Plugin\GroupJive\CBGroupJive;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveFile
{

	/**
	 * render frontend files
	 *
	 * @param FileTable[]     $rows
	 * @param cbPageNav       $pageNav
	 * @param bool            $searching
	 * @param array           $input
	 * @param array           $counters
	 * @param GroupTable      $group
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showFiles( $rows, $pageNav, $searching, $input, &$counters, $group, $user, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$counters[]							=	'<span class="gjGroupFileIcon fa-before fa-file-o"> ' . CBTxt::T( 'GROUP_FILES_COUNT', '%%COUNT%% File|%%COUNT%% Files', array( '%%COUNT%%' => (int) $pageNav->total ) ) . '</span>';

		initToolTip();

		$isModerator						=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$isOwner							=	( $user->get( 'id' ) == $group->get( 'user_id' ) );
		$userStatus							=	CBGroupJive::getGroupStatus( $user, $group );
		$canCreate							=	CBGroupJive::canCreateGroupContent( $user, $group, 'file' );
		$canSearch							=	( $plugin->params->get( 'groups_file_search', 1 ) && ( $searching || $pageNav->total ) );
		$return								=	null;

		$_PLUGINS->trigger( 'gj_onBeforeDisplayFiles', array( &$return, &$rows, $group, $user ) );

		$return								.=	'<div class="gjGroupFile">'
											.		'<form action="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) ) . '" method="post" name="gjGroupFileForm" id="gjGroupFileForm" class="gjGroupFileForm">';

		if ( $canCreate || $canSearch ) {
			$return							.=			'<div class="gjHeader gjGroupFileHeader row">';

			if ( $canCreate ) {
				$return						.=				'<div class="' . ( ! $canSearch ? 'col-sm-12' : 'col-sm-8' ) . ' text-left">'
											.					'<button type="button" onclick="window.location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'file', 'func' => 'new', 'group' => (int) $group->get( 'id' ) ) ) . '\';" class="gjButton gjButtonNewFile btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New File' ) . '</button>'
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

		$return								.=			'<table class="gjGroupFileRows table table-hover table-responsive">'
											.				'<thead>'
											.					'<tr>'
											.						'<th colspan="2">&nbsp;</th>'
											.						'<th style="width: 15%;" class="text-center">' . CBTxt::T( 'Type' ) . '</th>'
											.						'<th style="width: 15%;" class="text-left">' . CBTxt::T( 'Size' ) . '</th>'
											.						'<th style="width: 20%;" class="text-left hidden-xs">' . CBTxt::T( 'Date' ) . '</th>'
											.						'<th style="width: 1%;" class="text-right">&nbsp;</th>'
											.					'</tr>'
											.				'</thead>'
											.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$rowOwner						=	( $user->get( 'id' ) == $row->get( 'user_id' ) );
			$extension						=	null;
			$size							=	0;
			$title							=	( $row->get( 'title' ) ? htmlspecialchars( $row->get( 'title' ) ) : $row->name() );
			$item							=	$title;

			if ( $row->exists() ) {
				$downloadPath				=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'download', 'id' => (int) $row->get( 'id' ) ), 'raw', 0, true );
				$extension					=	$row->extension();
				$size						=	$row->size();

				switch ( $extension ) {
					case 'txt':
					case 'pdf':
					case 'jpg':
					case 'jpeg':
					case 'png':
					case 'gif':
					case 'js':
					case 'css':
					case 'mp4':
					case 'mp3':
					case 'wav':
						$item				=	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ), 'raw', 0, true ) . '" target="_blank">'
											.		$item
											.	'</a>';
						break;
					default:
						$item				=	'<a href="' . $downloadPath . '" target="_blank">'
											.		$item
											.	'</a>';
						break;
				}

				$download					=	'<a href="' . $downloadPath . '" target="_blank" title="' . htmlspecialchars( CBTxt::T( 'Click to Download' ) ) . '" class="gjGroupDownloadIcon btn btn-xs btn-default">'
											.		'<span class="fa fa-download"></span>'
											.	'</a>';
			} else {
				$download					=	'<button type="button" class="gjButton gjButtonDownloadFile btn btn-xs btn-default disabled">'
											.		'<span class="fa fa-download"></span>'
											.	'</button>';
			}

			if ( $row->get( 'description' ) ) {
				$item						.=	' ' . cbTooltip( 1, $row->get( 'description' ), $title, 400, null, '<span class="fa fa-info-circle text-muted"></span>' );
			}

			$menu							=	array();

			$_PLUGINS->trigger( 'gj_onDisplayFile', array( &$row, &$menu, $group, $user ) );

			$return							.=					'<tr>'
											.						'<td style="width: 1%;" class="text-center">' . $download . '</td>'
											.						'<td style="width: 45%;" class="gjGroupFileItem text-left">'
											.							$item;

			if ( ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) && ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'file', 1 ) == 2 ) ) ) {
				$return						.=							' <span class="gjGroupPendingIcon fa fa-clock-o text-warning" title="' . htmlspecialchars( CBTxt::T( 'Awaiting Approval' ) ) . '"></span>';
			}

			$return							.=							'<div class="gjGroupFileUploader small">' . CBuser::getInstance( (int) $row->get( 'user_id' ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</div>'
											.						'</td>'
											.						'<td style="width: 15%;" class="text-center"><span class="gjGroupFileTypeIcon fa fa-' . $row->icon() . '" title="' . htmlspecialchars( ( $extension ? strtoupper( $extension ) : CBTxt::T( 'Unknown' ) ) ) . '"></span></td>'
											.						'<td style="width: 15%;" class="text-left">' . $size . '</td>'
											.						'<td style="width: 20%;" class="text-left hidden-xs">'
											.							'<span title="' . htmlspecialchars( $row->get( 'date' ) ) . '">'
											.								cbFormatDate( $row->get( 'date' ), true, false, CBTxt::T( 'GROUP_FILE_DATE_FORMAT', 'M j, Y' ) )
											.							'</span>'
											.						'</td>';

			if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) || $menu ) {
				$menuItems					=	'<ul class="gjFileMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

				if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
					$menuItems				.=		'<li class="gjFileMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'edit', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

					if ( ( $row->get( 'published' ) == -1 ) && ( $group->params()->get( 'file', 1 ) == 2 ) ) {
						if ( $isModerator || $isOwner || ( $userStatus >= 2 ) ) {
							$menuItems		.=		'<li class="gjFileMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
						}
					} elseif ( $row->get( 'published' ) == 1 ) {
						$menuItems			.=		'<li class="gjFileMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this File?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'unpublish', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
					} else {
						$menuItems			.=		'<li class="gjFileMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'publish', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
					}
				}

				if ( $menu ) {
					$menuItems				.=		'<li class="gjFileMenuItem">' . implode( '</li><li class="gjFileMenuItem">', $menu ) . '</li>';
				}

				if ( $isModerator || $isOwner || $rowOwner || ( $userStatus >= 2 ) ) {
					$menuItems				.=		'<li class="gjFileMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this File?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'delete', 'id' => (int) $row->get( 'id' ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
				}

				$menuItems					.=	'</ul>';

				$menuAttr					=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

				$return						.=						'<td style="width: 1%;" class="text-right">'
											.							'<div class="gjFileMenu btn-group">'
											.								'<button type="button" ' . trim( $menuAttr ) . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
											.							'</div>'
											.						'</td>';
			} else{
				$return						.=						'<td style="width: 1%;"></td>';
			}

			$return							.=					'</tr>';
		} else {
			$return							.=					'<tr>'
											.						'<td colspan="6" class="text-left">';

			if ( $searching ) {
				$return						.=							CBTxt::T( 'No group file search results found.' );
			} else {
				$return						.=							CBTxt::T( 'This group currently has no files.' );
			}

			$return							.=						'</td>'
											.					'</tr>';
		}

		$return								.=				'</tbody>';

		if ( $plugin->params->get( 'groups_file_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return							.=				'<tfoot>'
											.					'<tr>'
											.						'<td colspan="6" class="gjGroupFilePaging text-center">'
											.							$pageNav->getListLinks()
											.						'</td>'
											.					'</tr>'
											.				'</tfoot>';
		}

		$return								.=			'</table>'
											.			$pageNav->getLimitBox( false )
											.		'</form>'
											.	'</div>';

		$_PLUGINS->trigger( 'gj_onAfterDisplayFiles', array( &$return, $rows, $group, $user ) );

		return $return;
	}
}