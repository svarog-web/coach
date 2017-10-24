<?php
/**
 * Joomla Community Builder User Plugin: plug_cbprofilebook
 * @version $Id: $
 * @package CommunityBuilder ProfileBook
 * @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CBLib\Registry\Registry;

/** ensure this file is being included by a parent file */
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

function plug_cb_profilebook_uninstall()
{
	global $_CB_database;

	$html_return			=	'';
	
	// if needed get cb profilebook plugin parameters
	$plugparms_query		=	"SELECT params"
							.	"\n FROM #__comprofiler_plugin"
							.	"\n WHERE element='cb.profilebook'";
	$_CB_database->setQuery( $plugparms_query );
	$cbpbplugparms			=	$_CB_database->loadResult();

	$params					=	new Registry( $cbpbplugparms );

	if ( $params->get( 'pbUnistallMode' ) ) {			// if full unistall mode parameter selected then purge everything
		$drop_table_query	=	'DROP TABLE `#__comprofiler_plug_profilebook`';
		$_CB_database->setQuery( $drop_table_query );
		$ret				=	$_CB_database->query();
		if( ! $ret ) {
			$html_return	.=	'<div style="font-size:14px;color:red;margin-bottom:10px;">Failed to drop table #__comprofiler_plug_profilebook</div>';
		} else {
			$html_return	.=	'<div style="font-size:14px;color:green;margin-bottom:10px;">Table #__comprofiler_plug_profilebook deleted (all items lost)</div>';
		}
		$drop_fields_query	=	"ALTER TABLE `#__comprofiler` DROP COLUMN `cb_pb_enable`,"
							.	"\n DROP COLUMN `cb_pb_autopublish`,"
							.	"\n DROP COLUMN `cb_pb_notifyme`";	
		$_CB_database->setQuery( $drop_fields_query );
		$ret				=	$_CB_database->query();
		if( ! $ret ) {
			$html_return	.=	'<div style="font-size:14px;color:red;margin-bottom:10px;">Failed to delete Plugin fields from #__comprofiler table</div>';
		} else {
			$html_return	.=	'<div style="font-size:14px;color:green;margin-bottom:10px;">Plugin fields deleted from #__comprofiler table (all personalization lost)</div>';
		}
	} else {
		// just unistall plugin code - keep all data
		$html_return		.=	'<div style="font-size:14px;color:green;margin-bottom:10px;">The profilebook plugin has been deleted but data remains so upgrade is possible</div>';
	}
	# Show installation result to user
	echo 'Plugin successfully uninstalled. See bellow for extra status messages';
	return $html_return;
}
