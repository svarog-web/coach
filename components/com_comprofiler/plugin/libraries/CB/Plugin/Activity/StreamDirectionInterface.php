<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

defined('CBLIB') or die();

interface StreamDirectionInterface extends StreamInterface
{

	/**
	 * Gets or sets the stream display direction
	 *
	 * @param int|null $direction
	 * @return int|null
	 */
	public function direction( $direction = null );
}