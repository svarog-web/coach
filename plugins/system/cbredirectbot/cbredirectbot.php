<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

if ( ! defined( '_JEXEC' ) ) { die( 'Direct Access to this location is not allowed.' ); }

JPluginHelper::importPlugin( 'system', 'redirect' );

class plgSystemcbredirectbot extends JPlugin
{

	/** @var bool  */
	protected $autoloadLanguage			=	false;
	/** @var array  */
	static $redirectsFromRegexp			=	array();
	/** @var array  */
	static $redirectsToRegexp			=	array();
	/** @var array  */
	static $redirectsFrom				=	array();
	/** @var array  */
	static $redirectsTo					=	array();
	/** @var int  */
	static $redirectHeader				=	301;
	/** @var callable  */
	static $previousExceptionHandler	=	null;

	/**
	 * plgSystemcbredirectbot constructor
	 *
	 * @param object $subject
	 * @param array  $config
	 */
	public function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );

		JError::setErrorHandling( E_ERROR, 'callback', array( 'plgSystemcbredirectbot', 'handleError' ) );

		self::$previousExceptionHandler				=	set_exception_handler( array( 'plgSystemcbredirectbot', 'handleException' ) );

		if ( ( ! self::$redirectsFromRegexp ) && ( ! self::$redirectsFrom ) ) {
			$redirects								=	$this->params->get( 'redirects' );
			$header									=	(int) $this->params->get( 'header' );

			if ( $header ) {
				self::$redirectHeader				=	$header;
			}

			if ( ! $redirects ) {
				return;
			}

			$redirects								=	json_decode( $redirects, true );

			if ( ( ! $redirects ) || ( ! isset( $redirects['from'] ) ) ) {
				return;
			}

			foreach ( $redirects['from'] as $k => $from ) {
				if ( ! $from ) {
					continue;
				}

				if ( ( isset( $redirects['regexp'][$k] ) || ( $redirects['regexp'][$k] === null ) ? (int) $redirects['regexp'][$k] : 1 ) ) {
					self::$redirectsFromRegexp[]	=	$from;
					self::$redirectsToRegexp[]		=	( isset( $redirects['to'][$k] ) ? $redirects['to'][$k] : '' );
				} else {
					self::$redirectsFrom[]			=	$from;
					self::$redirectsTo[]			=	( isset( $redirects['to'][$k] ) ? $redirects['to'][$k] : '' );
				}
			}
		}
	}

	/**
	 * Method to handle an error condition from JError
	 *
	 * @param JException $error
	 */
	public static function handleError( JException &$error )
	{
		if ( ! self::doErrorHandling( $error ) ) {
			if ( class_exists( 'PlgSystemRedirect' ) ) {
				PlgSystemRedirect::handleError( $error );
			} elseif ( self::$previousExceptionHandler ) {
				call_user_func_array( self::$previousExceptionHandler, array( $error ) );
			}
		}
	}

	/**
	 * Method to handle an uncaught exception
	 *
	 * @param Exception|Throwable $exception
	 * @throws InvalidArgumentException
	 */
	public static function handleException( $exception )
	{
		if ( ( ! $exception instanceof Throwable ) && ( ! $exception instanceof Exception ) ) {
			throw new InvalidArgumentException( sprintf( 'The error handler requires a Exception or Throwable object, a "%s" object was given instead.', get_class( $exception ) ) );
		}

		if ( ! self::doErrorHandling( $exception ) ) {
			if ( class_exists( 'PlgSystemRedirect' ) ) {
				PlgSystemRedirect::handleException( $exception );
			} elseif ( self::$previousExceptionHandler ) {
				call_user_func_array( self::$previousExceptionHandler, array( $exception ) );
			}
		}
	}

	/**
	 * Internal processor for all error handlers
	 *
	 * @param Exception|Throwable $error
	 * @return bool
	 */
	private static function doErrorHandling( $error )
	{
		if ( ( ! self::$redirectsFromRegexp ) && ( ! self::$redirectsFrom ) ) {
			return false;
		}

		$app		=	JFactory::getApplication();

		if ( $app->isAdmin() || ( $error->getCode() != 404 ) ) {
			return false;
		}

		$uri		=	JUri::getInstance();
		$current	=	rawurldecode( $uri->toString( array( 'scheme', 'host', 'port', 'path', 'query', 'fragment' ) ) );

		if ( ( strpos( $current, 'mosConfig_' ) !== false ) || ( strpos( $current, '=http://' ) !== false ) ) {
			return false;
		}

		$new		=	$current;

		if ( self::$redirectsFromRegexp ) {
			$new	=	preg_replace( self::$redirectsFromRegexp, self::$redirectsToRegexp, $new );
		}

		if ( self::$redirectsFrom ) {
			$new	=	str_ireplace( self::$redirectsFrom, self::$redirectsTo, $new );
		}

		if ( ! $new ) {
			return false;
		}

		if ( $current != $new ) {
			if ( ( self::$redirectHeader < 400 ) && ( self::$redirectHeader >= 300 ) ) {
				$app->redirect( ( JUri::isInternal( $new ) ? JRoute::_( $new ) : $new ), self::$redirectHeader );
			}

			JErrorPage::render( new RuntimeException( $error->getMessage(), self::$redirectHeader, $error ) );
		}

		return false;
	}
}