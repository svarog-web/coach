<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 10.06.13 15:47 $
* @package CBLib\Cms\Joomla\Joomla3
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Cms\Joomla\Joomla3;

use CBLib\Controller\RouterInterface;
use CBLib\Input\InputInterface;
use CBLib\Registry\GetterInterface;

defined('CBLIB') or die();

/**
 * CBLib\Cms\Joomla\Joomla3\CmsRouter Class implementation
 * 
 */
class CmsRouter implements RouterInterface
{
	/**
	 * List of core CMS views
	 *
	 * @var array
	 */
	protected static $views		=	array(	'userdetails', 'saveuseredit', 'userprofile', 'userslist', 'lostpassword',
											'sendnewpass', 'registers', 'saveregisters', 'login', 'logout', 'confirm',
											'moderateimages', 'moderatereports', 'moderatebans', 'approveimage', 'reportuser',
											'processreports', 'banprofile', 'viewreports', 'emailuser', 'pendingapprovaluser',
											'approveuser', 'rejectuser', 'senduseremail', 'addconnection', 'removeconnection',
											'denyconnection', 'acceptconnection', 'manageconnections', 'saveconnections',
											'processconnectionactions', 'teamcredits', 'fieldclass', 'tabclass', 'pluginclass',
											'done', 'performcheckusername', 'performcheckemail'
										);

	/**
	 * Parsed routed
	 *
	 * @var array
	 */
	protected $mainRoutingArgs	=	array();

	/**
	 * This is the Joomla 3.0 (and 2.5) specific implementation of the default router.
	 *
	 * @param   InputInterface  $input  Input
	 * @return  callable|null           array( 'className', 'methodName' )
	 */
	public function parseRoute( InputInterface $input )
	{
		$this->mainRoutingArgs				=	$this->parseRoutingArgs( $input );

		list( $option, $view, $task )		=	array_values( $this->mainRoutingArgs );

		// Backwards compatibility of URLs with task but no view:
		if ( ( $view === null ) && $task ) {
			$view		=	$task;
		}

		// Default to view=default
		if ( $view === null ) {
			$view		=	'default';
		}

		// Remove 'com_' from 'com_component':
		if ( strncmp( $option, 'com_', 4 ) === 0 ) {
			$option		=	substr( $option, 4 );
		}

		$class			=	'\\CBApps\\' . $option . '\\' . ucfirst( $option ) . 'Controller';
		$method			=	$view . 'Task';

		return array( $class, $method );
	}

	/**
	 * Parses the $input for the main routing arguments
	 *
	 * @return  array                   Keyed array with the 3 main routing arguments
	 */
	public function getMainRoutingArgs( )
	{
		return $this->mainRoutingArgs;
	}

	/**
	 * Parses the $input for the main routing arguments
	 *
	 * @param   InputInterface  $input  Input
	 * @return  array                   Keyed array with the 3 main routing arguments
	 */
	protected function parseRoutingArgs( InputInterface $input )
	{
		return $input->get( array( 'option', 'view', 'task' ), null, GetterInterface::COMMAND );
	}

	/**
	 * Returns the core views for this CMS
	 *
	 * @return array
	 */
	public function getViews( )
	{
		return self::$views;
	}

	/**
	 * Converts a string to a url safe alias
	 *
	 * @param string $string
	 * @param int    $length
	 * @return string
	 */
	public function stringToAlias( $string, $length = 400 )
	{
		if ( \JFactory::getConfig()->get( 'unicodeslugs' ) == 1 ) {
			$alias	=	\JFilterOutput::stringURLUnicodeSlug( $string );
		} else {
			$alias	=	\JFilterOutput::stringURLSafe( $string );
		}

		if ( $length && ( cbutf8_strlen( $alias ) > $length ) ) {
			$alias	=	trim( cbutf8_substr( $alias, 0, $length ), '-' );
		}

		return $alias;
	}
}
