<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Connect;

use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\SetterInterface;
use CBLib\Input\Get;

defined('CBLIB') or die();

class Profile implements GetterInterface, SetterInterface
{
	/** @var string  */
	protected $id			=	null;
	/** @var string  */
	protected $username		=	null;
	/** @var string  */
	protected $name			=	null;
	/** @var string  */
	protected $firstname	=	null;
	/** @var string  */
	protected $middlename	=	null;
	/** @var string  */
	protected $lastname		=	null;
	/** @var string  */
	protected $email		=	null;
	/** @var string  */
	protected $avatar		=	null;
	/** @var string  */
	protected $canvas		=	null;

	/** @var Registry  */
	protected $profile		=	null;

	/**
	 * Gets the value of the class variable
	 *
	 * @param string       $var     The name of the class variable
	 * @param mixed        $default The value to return if no value is found
	 * @param string|array $type    [optional] Default: null: GetterInterface::COMMAND. Or const int GetterInterface::COMMAND|GetterInterface::INT|... or array( const ) or array( $key => const )
	 * @return mixed                The value of the class var (or null if no var of that name exists)
	 */
	public function get( $var, $default = null, $type = null )
	{
		if ( ! isset( $this->$var ) ) {
			return $default;
		}

		if ( $type === null ) {
			return $this->$var;
		}

		return Get::clean( $this->$var, $type );
	}

	/**
	 * Sets the new value of the class variable
	 *
	 * @param string $var   The name of the class variable
	 * @param mixed  $value The new value to assign to the variable
	 */
	public function set( $var, $value )
	{
		$this->$var		=	$value;
	}

	/**
	 * Check if a parameters path exists.
	 *
	 * @param string $key The name of the param or sub-param, e.g. a.b.c
	 * @return boolean
	 */
	public function has( $key )
	{
		return ( ( substr( $key, 0, 1 ) != '_' ) && in_array( $key, array_keys( get_class_vars( get_class( $this ) ) ) ) );
	}

	/**
	 * @return Registry
	 */
	public function profile()
	{
		if ( ! $this->profile ) {
			$this->profile		=	new Registry();
		}

		return $this->profile;
	}
}
