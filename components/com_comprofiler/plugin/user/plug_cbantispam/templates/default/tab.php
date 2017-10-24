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

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbantispamTab
{

	/**
	 * @param string       $blocks
	 * @param string       $whitelists
	 * @param string       $attempts
	 * @param string       $logs
	 * @param UserTable    $viewer
	 * @param UserTable    $user
	 * @param TabTable     $tab
	 * @param cbTabHandler $plugin
	 * @return string
	 */
	static public function showTab( $blocks, $whitelists, $attempts, $logs, $viewer, $user, $tab, $plugin )
	{
		$tabs				=	new cbTabs( 1, 1 );
		$return				=	null;
		$count				=	0;

		if ( $blocks ) {
			$count++;
		}

		if ( $whitelists ) {
			$count++;
		}

		if ( $attempts ) {
			$count++;
		}

		if ( $logs ) {
			$count++;
		}

		$tabbed				=	( $count > 1 );

		if ( $tabbed ) {
			$return			.=	$tabs->startPane( 'blocksTabs' );
		}

		if ( $blocks ) {
			if ( $tabbed ) {
				$return		.=		$tabs->startTab( null, htmlspecialchars( CBTxt::T( 'TAB_BLOCKS', 'Blocks' ) ), 'blocksTabBlocks' )
							.			$blocks
							.		$tabs->endTab();
			} else {
				$return		.=	$blocks;
			}
		}

		if ( $whitelists ) {
			if ( $tabbed ) {
				$return		.=		$tabs->startTab( null, htmlspecialchars( CBTxt::T( 'TAB_WHITELISTS', 'Whitelists' ) ), 'blocksTabWhitelists' )
							.			$whitelists
							.		$tabs->endTab();
			} else {
				$return		.=	$whitelists;
			}
		}

		if ( $attempts ) {
			if ( $tabbed ) {
				$return		.=		$tabs->startTab( null, htmlspecialchars( CBTxt::T( 'TAB_ATTEMPTS', 'Attempts' ) ), 'blocksTabAttempts' )
							.			$attempts
							.		$tabs->endTab();
			} else {
				$return		.=	$attempts;
			}
		}

		if ( $logs ) {
			if ( $tabbed ) {
				$return		.=		$tabs->startTab( null, htmlspecialchars( CBTxt::T( 'TAB_LOGS', 'Logs' ) ), 'blocksTabLogs' )
							.			$logs
							.		$tabs->endTab();
			} else {
				$return		.=	$logs;
			}
		}

		if ( $tabbed ) {
			$return			.=	$tabs->endPane();
		}

		return $return;
	}
}
?>