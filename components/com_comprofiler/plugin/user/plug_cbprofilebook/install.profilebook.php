<?php
/**
 * Joomla Community Builder User Plugin: plug_cbprofilebook
 * @version $Id: $
 * @package CommunityBuilder ProfileBook
 * @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

/** ensure this file is being included by a parent file */
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * Show installation result to user
 *
 * @return string
 */
function plug_cb_profilebook_install( )
{
  	?>
	Copyright 2006-2014 CB team on joomlapolis.com . This component is released under the GNU/GPL License. All copyright statements must be kept. Derivate work must prominently duly acknowledge original work and include visible online links. Official site: <a href="http://www.joomlapolis.com">www.joomlapolis.com</a>

	<div style="font-size:14px; color:green;margin-top:30px; margin-bottom:10px; font-weight:bold">Installation finished.</div>
	<?php
	return '';
}
