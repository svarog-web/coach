<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveForums\Table;

use CBLib\Database\Table\TableInterface;

defined('CBLIB') or die();

interface PostTableInterface extends TableInterface
{

	/**
	 * get or set the kunena post object
	 *
	 * @param \KunenaForumMessage|null $post
	 * @return \KunenaForumMessage|null
	 */
	public function post( $post = null );

	/**
	 * returns the forum category url
	 *
	 * @return null|string
	 */
	public function url();
}