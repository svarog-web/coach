<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Database\Table\UserTable;
use CBLib\Registry\RegistryInterface;

defined('CBLIB') or die();

interface StreamInterface extends RegistryInterface
{

	/**
	 * Gets the stream id
	 *
	 * @return string
	 */
	public function id();

	/**
	 * Gets or sets the stream source
	 *
	 * @param string|null $source
	 * @return string
	 */
	public function source( $source = null );

	/**
	 * Gets or sets the stream target user (owner)
	 *
	 * @param UserTable|null $user
	 * @return UserTable|null
	 */
	public function user( $user = null );

	/**
	 * Resets the data cache for this stream (forces data to requery)
	 */
	public function resetData();

	/**
	 * Retrieves stream data rows or row count
	 *
	 * @param bool  $count
	 * @param array $where
	 * @param array $join
	 * @return array
	 */
	public function data( $count = false, $where = array(), $join = array() );

	/**
	 * Outputs stream HTML
	 *
	 * @param bool $inline
	 * @param bool $data
	 * @return string
	 */
	public function stream( $inline = false, $data = true );

	/**
	 * Returns the stream validation token
	 *
	 * @param array $data
	 * @return string
	 */
	public function token( $data = array() );

	/**
	 * Outputs stream URL endpoint
	 *
	 * @param string $view
	 * @param array  $data
	 * @return string
	 */
	public function endpoint( $view = null, $data = array() );

	/**
	 * Returns a parser object for parsing stream content
	 *
	 * @param string $string
	 * @return Parser
	 */
	public function parser( $string = '' );
}