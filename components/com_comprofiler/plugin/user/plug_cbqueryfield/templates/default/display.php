<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Database\DatabaseDriverInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_queryfieldDisplay
{

	/**
	 * render query display
	 *
	 * @param cbDatabase|DatabaseDriverInterface $database
	 * @param FieldTable                         $field
	 * @param UserTable                          $user
	 * @param string                             $output 'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param string                             $reason 'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @return mixed
	 */
	static function showField( $database, $field, $user, $output, $reason )
	{
		return $database->loadResult();
	}
}