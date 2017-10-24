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
use CB\Database\Table\TabTable;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbantispamLogs
{

	/**
	 * @param cbantispamLogTable[] $rows
	 * @param cbPageNav            $pageNav
	 * @param UserTable            $viewer
	 * @param UserTable            $user
	 * @param TabTable             $tab
	 * @param cbTabHandler         $plugin
	 * @return string
	 */
	static public function showLogs( $rows, $pageNav, $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework;

		/** @var Registry $params */
		$params						=	$tab->params;

		$return						=	'<div class="logsTab">'
									.		'<form action="' . $_CB_framework->userProfileUrl( (int) $user->get( 'id' ), true, (int) $tab->get( 'tabid' ) ) . '" method="post" name="logsForm" id="logsForm" class="logsForm">'
									.			'<table class="logsContainer table table-hover table-responsive">'
									.				'<thead>'
									.					'<tr>'
									.						'<th class="text-left">' . CBTxt::T( 'IP Address' ) . '</th>'
									.						'<th style="width: 25%;" class="text-left hidden-xs">' . CBTxt::T( 'Date' ) . '</th>'
									.						'<th style="width: 10%;" class="text-center hidden-xs">' . CBTxt::T( 'Count' ) . '</th>'
									.						'<th style="width: 1%;" class="text-right">&nbsp;</th>'
									.					'</tr>'
									.				'</thead>'
									.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$return					.=					'<tr>'
									.						'<td class="text-left">' . $row->get( 'ip_address' ) . '</td>'
									.						'<td style="width: 25%;" class="text-left hidden-xs">' . cbFormatDate( $row->get( 'date' ), false ) . '</td>'
									.						'<td style="width: 10%;" class="text-center hidden-xs">' . $row->get( 'count' ) . '</td>'
									.						'<td style="width: 1%;" class="text-right">'
									.							'<a href="javascript: void(0);" onclick="if ( confirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this Log?' ) ) . '\' ) ) { location.href = \'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'log', 'func' => 'delete', 'id' => (int) $row->get( 'id' ), 'usr' => (int) $user->get( 'id' ), 'tab' => (int) $tab->get( 'tabid' ) ) ) . '\'; }" title="' . htmlspecialchars( CBTxt::T( 'Delete' ) ) . '"><span class="fa fa-trash-o"></span></a>'
									.						'</td>'
									.					'</tr>';
		} else {
			$return					.=					'<tr>'
									.						'<td colspan="3" class="text-left">';

			if ( $viewer->get( 'id' ) == $user->get( 'id' ) ) {
				$return				.=							CBTxt::T( 'You have no logs.' );
			} else {
				$return				.=							CBTxt::T( 'This user has no logs.' );
			}

			$return					.=						'</td>'
									.					'</tr>';
		}

		$return						.=				'</tbody>';

		if ( $params->get( 'tab_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return					.=				'<tfoot>'
									.					'<tr>'
									.						'<td colspan="3" class="text-center">'
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