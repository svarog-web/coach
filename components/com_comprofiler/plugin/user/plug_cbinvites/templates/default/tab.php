<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbinvitesTab
{

	/**
	 * @param cbinvitesInviteTable[] $rows
	 * @param cbPageNav              $pageNav
	 * @param bool                   $searching
	 * @param array                  $input
	 * @param UserTable              $viewer
	 * @param UserTable              $user
	 * @param TabTable               $tab
	 * @param cbTabHandler           $plugin
	 * @return string
	 */
	static function showTab( $rows, $pageNav, $searching, $input, $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework, $_CB_database;

		/** @var Registry $params */
		$params						=	$tab->params;
		$profileOwner				=	( $viewer->get( 'id' ) == $user->get( 'id' ) );
		$cbModerator				=	Application::User( (int) $viewer->get( 'id' ) )->isGlobalModerator();

		$tabPaging					=	$params->get( 'tab_paging', 1 );
		$canSearch					=	( $params->get( 'tab_search', 1 ) && ( $searching || $pageNav->total ) );

		$inviteLimit				=	(int) $plugin->params->get( 'invite_limit', null );
		$canCreate					=	false;

		if ( $profileOwner ) {
			if ( $cbModerator ) {
				$canCreate			=	true;
			} elseif ( $user->get( 'id' ) && Application::MyUser()->canViewAccessLevel( $plugin->params->get( 'invite_create_access', 2 ) ) ) {
				if ( $inviteLimit ) {
					$query			=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
									.	"\n AND ( " . $_CB_database->NameQuote( 'user' ) . " IS NULL OR " . $_CB_database->NameQuote( 'user' ) . " = " . $_CB_database->Quote( '' ) . " )";
					$_CB_database->setQuery( $query );
					$inviteCount	=	(int) $_CB_database->loadResult();

					if ( $inviteCount < $inviteLimit ) {
						$canCreate	=	true;
					}
				} else {
					$canCreate		=	true;
				}
			}
		}

		$return						=	'<div class="invitesTab">'
									.		'<form action="' . $_CB_framework->userProfileUrl( $user->get( 'id' ), true, $tab->tabid ) . '" method="post" name="inviteForm" id="inviteForm" class="inviteForm">';

		if ( $canCreate || $canSearch ) {
			$return					.=			'<div class="invitesHeader row" style="margin-bottom: 10px;">';

			if ( $canCreate ) {
				$return				.=				'<div class="' . ( ! $canSearch ? 'col-sm-12' : 'col-sm-8' ) . ' text-left">'
									.					'<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'invites', 'func' => 'new' ) ) . '\';" class="invitesButton invitesButtonNew btn btn-success"><span class="fa fa-plus-circle"></span> ' . CBTxt::T( 'New Invite' ) . '</button>'
									.				'</div>';
			}

			if ( $canSearch ) {
				$return				.=				'<div class="' . ( ! $canCreate ? 'col-sm-offset-8 ' : null ) . 'col-sm-4 text-right">'
									.					'<div class="input-group">'
									.						'<span class="input-group-addon"><span class="fa fa-search"></span></span>'
									.						$input['search']
									.					'</div>'
									.				'</div>';
			}

			$return					.=			'</div>';
		}

		$menuAccess					=	( $cbModerator || $profileOwner );

		if ( $menuAccess ) {
			if ( $rows ) foreach ( $rows as $row ) {
				if ( $row->canResend() || ( ! $row->isAccepted() ) ) {
					$menuAccess		=	true;
					break;
				}
			}
		}

		$return						.=			'<table class="invitesContainer table table-hover table-responsive">'
									.				'<thead>'
									.					'<tr>'
									.						'<th class="text-left">' . CBTxt::T( 'To' ) . '</th>'
									.						'<th style="width: 25%;" class="text-left hidden-xs">' . CBTxt::T( 'Date' ) . '</th>'
									.						'<th style="width: 5%;" class="text-center hidden-xs">' . CBTxt::T( 'Status' ) . '</th>'
									.						( $menuAccess ? '<th style="width: 1%;" class="text-right">&nbsp;</th>' : null )
									.					'</tr>'
									.				'</thead>'
									.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			if ( $row->get( 'user' ) ) {
				$toUser				=	CBuser::getInstance( (int) $row->get( 'user' ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
			} else {
				$toUser				=	'<a href="mailto:' . htmlspecialchars( $row->get( 'to' ) ) . '">' . $row->get( 'to' ) . '</a>';
			}

			$return					.=					'<tr>'
									.						'<td class="text-left">' . $toUser . '</td>'
									.						'<td style="width: 25%;" class="text-left hidden-xs">'
									.							( $row->isSent() ? '<div class="text-info" title="' . htmlspecialchars( CBTxt::T( 'Sent' ) ) . '">' . cbFormatDate( $row->get( 'sent' ) ) . '</div>' : '<div class="text-warning">' . CBTxt::T( 'Please Resend' ) . '</div>' )
									.							( $row->isAccepted() ? '<div class="text-success" title="' . htmlspecialchars( CBTxt::T( 'Accepted' ) ) . '">' . cbFormatDate( $row->get( 'accepted' ) ) . '</div>' : null )
									.						'</td>'
									.						'<td style="width: 5%;" class="text-center hidden-xs">';

			if ( $row->isAccepted() ) {
				$return				.=							'<span class="fa fa-check text-success" title="' . htmlspecialchars( CBTxt::T( 'Accepted' ) ) . '"></span>';
			} elseif ( $row->isSent() ) {
				$return				.=							'<span class="fa fa-clock-o text-warning" title="' . htmlspecialchars( CBTxt::T( 'Pending' ) ) . '"></span>';
			} else {
				$return				.=							'<span class="fa fa-warning text-danger" title="' . htmlspecialchars( CBTxt::T( 'Resend' ) ) . '"></span>';
			}

			$return					.=						'</td>';

			if ( ( $cbModerator || $profileOwner ) && ( $row->canResend() || ( ! $row->isAccepted() ) ) ) {
				$menuItems			=	'<ul class="invitesMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

				if ( $row->canResend() ) {
					$menuItems		.=		'<li class="invitesMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'invites', 'func' => 'send', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-share-square-o"></span> ' . CBTxt::T( 'Resend' ) . '</a></li>';
				}

				if ( ! $row->isAccepted() ) {
					$menuItems		.=		'<li class="invitesMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'invites', 'func' => 'edit', 'id' => (int) $row->get( 'id' ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>'
									.		'<li class="invitesMenuItem"><a href="javascript: void(0);" onclick="if ( confirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this Invite?' ) ) . '\' ) ) { location.href = \'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'invites', 'func' => 'delete', 'id' => (int) $row->get( 'id' ) ) ) . '\'; }"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
				}

				$menuItems			.=	'</ul>';

				$menuAttr			=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

				$return				.=						'<td style="width: 1%;" class="text-right">'
									.							'<div class="invitesMenu btn-group">'
									.								'<button type="button"' . $menuAttr . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
									.							'</div>'
									.						'</td>';
			} elseif ( $menuAccess ) {
				$return				.=						'<td style="width: 1%;" class="text-right"></td>';
			}

			$return					.=					'</tr>';
		} else {
			$return					.=					'<tr>'
									.						'<td colspan="' . ( $menuAccess ? 4 : 3 ) . '" class="text-left">';

			if ( $searching ) {
				$return				.=							CBTxt::T( 'No invite search results found.' );
			} else {
				if ( $viewer->id == $user->id ) {
					$return			.=							CBTxt::T( 'You have no invites.' );
				} else {
					$return			.=							CBTxt::T( 'This user has no invites.' );
				}
			}

			$return					.=						'</td>'
									.					'</tr>';
		}

		$return						.=				'</tbody>';

		if ( $tabPaging && ( $pageNav->total > $pageNav->limit ) ) {
			$return					.=				'<tfoot>'
									.					'<tr>'
									.						'<td colspan="' . ( $menuAccess ? 4 : 3 ) . '" class="text-center">'
									.							$pageNav->getListLinks()
									.						'</td>'
									.					'</tr>'
									.				'</tfoot>';
		}

		$return						.=			'</table>'
									.			$pageNav->getLimitBox( false )
									.		'</form>'
									.	'</div>';

		return $return;
	}
}
?>