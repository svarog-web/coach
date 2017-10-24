<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Connect;

use CBLib\Application\Application;
use CBLib\Session\Session;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\SetterInterface;
use CBLib\Input\Get;
use CBLib\Xml\SimpleXMLElement;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;
use Exception;

defined('CBLIB') or die();

abstract class Provider implements GetterInterface, SetterInterface
{
	/** @var Session  */
	protected $session			=	null;
	/** @var string  */
	protected $clientId			=	null;
	/** @var string  */
	protected $clientSecret		=	null;
	/** @var string  */
	protected $callback			=	null;
	/** @var string  */
	protected $scope			=	null;
	/** @var string  */
	protected $scopeSeparator	=	',';
	/** @var string  */
	protected $fields			=	null;
	/** @var string  */
	protected $fieldsSeparator	=	',';
	/** @var array  */
	protected $urls				=	array();

	/** @var int  */
	protected $debug			=	0;

	/**
	 * Provider constructor.
	 *
	 * @param string       $clientId
	 * @param string       $clientSecret
	 * @param string       $callback
	 * @param string|array $scope
	 * @param string|array $fields
	 * @param int          $debug
	 */
	public function __construct( $clientId = null, $clientSecret = null, $callback = null, $scope = null, $fields = null, $debug = null )
	{
		if ( $clientId ) {
			$this->clientId			=	trim( $clientId );
		}

		if ( $clientSecret ) {
			$this->clientSecret		=	trim( $clientSecret );
		}

		if ( $callback ) {
			$this->callback			=	trim( $callback );
		}

		if ( $scope ) {
			$this->scope			=	( is_array( $scope ) ? implode( $this->scopeSeparator, $scope ) : $scope );
		}

		if ( $fields ) {
			$this->fields			=	( is_array( $fields ) ? implode( $this->fieldsSeparator, $fields ) : $fields );
		}

		if ( $debug !== null ) {
			$this->debug			=	(int) $debug;
		}
	}

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
	 * @return Session
	 */
	public function session()
	{
		if ( ! $this->session ) {
			$this->session			=	Application::Session();
		}

		return $this->session;
	}

	/**
	 * @throws Exception
	 */
	abstract function authenticate();

	/**
	 * @return bool
	 */
	abstract function authorized();

	/**
	 * @param null|string|array $fields
	 * @return Profile
	 * @throws Exception
	 */
	abstract function profile( $fields = null );

	/**
	 * Make a custom Facebook API request
	 *
	 * @param string $api
	 * @param string $type
	 * @param array  $params
	 * @param array  $headers
	 * @return string|Registry
	 * @throws Exception
	 */
	abstract function api( $api, $type = 'GET', $params = array(), $headers = array() );

	/**
	 * Parses a request response
	 *
	 * @param ResponseInterface $result
	 * @return string|Registry|SimpleXMLElement
	 */
	public function response( $result )
	{
		$response			=	(string) $result->getBody();

		if ( ! $response ) {
			return null;
		}

		if ( ( $response[0] === '{' ) || ( $response[0] === '[' ) ) {
			$response		=	new Registry( $response );
		} elseif ( strpos( $response, '<?xml' ) === 0 ) {
			$response		=	new SimpleXMLElement( $response );
		} elseif ( preg_match( '/^\??(&?[][\w]+=[^&]+)+$/', $response ) ) {
			$parts			=	array();

			parse_str( $response, $parts );

			$response		=	new Registry( $parts );
		}

		return $response;
	}

	/**
	 * Outputs HTTP request debug information
	 *
	 * @param ClientException|ResponseInterface $result
	 * @param string|Registry|SimpleXMLElement  $response
	 */
	public function debug( $result, $response = null )
	{
		if ( ! $this->debug ) {
			return;
		}

		if ( $result instanceof ClientException ) {
			ob_start();
			var_dump( $result->getRequest() );
			$requestDump		=	ob_get_contents();
			ob_end_clean();

			ob_start();
			var_dump( $result->getResponse() );
			$responseDump		=	ob_get_contents();
			ob_end_clean();

			$debug				=	'<pre style="display: block; padding: 9.5px; margin: 0 0 10px; font-size: 13px; line-height: 1.42857143; word-break: break-all; word-wrap: break-word; color: #333333; background-color: #f5f5f5; border: 1px solid #cccccc; border-radius: 4px;">'
								.		'<div style="border-bottom: 1px black solid; padding-bottom: 3px; font-weight: bold; margin-bottom: 5px;">Request</div>'
								.		$requestDump
								.		'<div style="border-bottom: 1px black solid; padding-bottom: 3px; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">Response</div>'
								.		$responseDump
								.		'<div style="border-bottom: 1px black solid; padding-bottom: 3px; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">Response Body</div>'
								.		(string) $result->getResponse()->getBody()
								.	'</pre>';
		} else {
			ob_start();
			var_dump( $response );
			$responseDump		=	ob_get_contents();
			ob_end_clean();

			$debug				=	'<pre style="display: block; padding: 9.5px; margin: 0 0 10px; font-size: 13px; line-height: 1.42857143; word-break: break-all; word-wrap: break-word; color: #333333; background-color: #f5f5f5; border: 1px solid #cccccc; border-radius: 4px;">'
								.		'<div style="border-bottom: 1px black solid; padding-bottom: 3px; font-weight: bold; margin-bottom: 5px;">Response Body</div>'
								.		(string) $result->getBody()
								.		'<div style="border-bottom: 1px black solid; padding-bottom: 3px; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">Response (Parsed)</div>'
								.		$responseDump
								.	'</pre>';
		}

		echo $debug;
	}
}
