<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\ListTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

if ( class_exists( 'JComponentRouterBase' ) ) {
	abstract class ComprofilerRouterBase extends JComponentRouterBase {}
} else {
	/**
	 * Class ComprofilerRouterBase
	 *
	 * Legacy SEF routing base class for older Joomla releases
	 */
	class ComprofilerRouterBase
	{

		/** @var JApplicationCms  */
		public $app;

		/** @var JMenu|null  */
		public $menu;

		/**
		 * ComprofilerRouterBase constructor.
		 *
		 * @param JApplicationCms $app
		 * @param JMenu           $menu
		 */
		public function __construct( $app = null, $menu = null )
		{
			if ( $app ) {
				$this->app		=	$app;
			} else {
				$this->app		=	JFactory::getApplication( 'site' );
			}

			if ( $menu ) {
				$this->menu		=	$menu;
			} else {
				$this->menu		=	$this->app->getMenu();
			}
		}
	}
}

class ComprofilerRouter extends ComprofilerRouterBase
{

	/**
	 * Loads CB API
	 *
	 * @return bool|null
	 */
	public function loadCB()
	{
		static $loaded		=	null;

		if ( $loaded === null ) {
			if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
				$loaded		=	false;

				return true;
			}

			include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

			cbimport( 'cb.html' );

			$loaded			=	true;
		}

		return $loaded;
	}

	/**
	 * Builds the sef url segments
	 *
	 * @param array $query
	 * @return array
	 */
	public function build( &$query )
	{
		global $_PLUGINS;

		$segments									=	array();

		if ( empty( $query['Itemid'] ) ) {
			$menuItem								=	$this->menu->getActive();
		} else {
			$menuItem								=	$this->menu->getItem( $query['Itemid'] );
		}

		if ( $menuItem && ( ( $menuItem->component != 'com_comprofiler' ) || ( $menuItem->type != 'component' ) ) ) {
			// The menu component doesn't match or it's not a component menu item so void the menu path parameters:
			$menuItem								=	null;
		}

		// We don't use task; lets see if it's present and view is missing:
		if ( isset( $query['task'] ) && ( ! isset( $query['view'] ) ) ) {
			$query['view']							=	$query['task'];

			unset( $query['task'] );
		}

		if ( isset( $query['view'] ) ) {
			$view									=	strtolower( $query['view'] );
		} else {
			return $segments;
		}

		// Load as little of CB as possible so we can utilize CB API for routing:
		if ( ! $this->loadCB() ) {
			return $segments;
		}

		// No view was found so lets fallback to default (profile):
		if ( ! $view ) {
			$view									=	'userprofile';
		}

		$viewMismatch								=	false;

		if ( $menuItem && isset( $menuItem->query['view'] ) && ( $menuItem->query['view'] != $view ) ) {
			if ( ! ( ( $menuItem->query['view'] == 'userprofile' ) && in_array( $view, Application::Router()->getViews() ) ) ) {
				// The views don't match and it's not a legacy usage so turn off menu usage:
				$menuItem							=	null;
			}

			$viewMismatch							=	true;
		}

		unset( $query['view'] );

		switch ( $view ) {
			case 'userslist':
				if ( isset( $query['listid'] ) ) {
					$listId							=	$query['listid'];
					$menuListId						=	( $menuItem && isset( $menuItem->query['listid'] ) ? $menuItem->query['listid'] : null );

					if ( $menuListId && ( $menuListId != $listId ) ) {
						// The listid don't match so turn off menu usage:
						$menuItem					=	null;
					}

					if ( is_numeric( $listId ) ) {
						$userList					=	new ListTable();

						$userList->load( (int) $listId );

						$listTitle					=	$userList->get( 'title', null, GetterInterface::STRING );

						if ( $listTitle ) {
							$listId					=	$userList->get( 'listid', 0, GetterInterface::INT ) . '-' . Application::Router()->stringToAlias( $listTitle );
						}
					}

					if ( $listId && ( ( ! $menuItem ) || ( ! $menuListId ) ) ) {
						// We have a list id, but it doesn't exist in the menu so lets add it:
						$segments[]					=	$listId;
					}

					unset( $query['listid'] );
				} else {
					if ( $menuItem && isset( $menuItem->query['listid'] ) && ( $menuItem->query['listid'] != '' ) ) {
						// The menu is for a specific userlist, but this is the default userlist so turn off menu usage:
						$menuItem					=	null;
					}
				}

				if ( isset( $query['searchmode'] ) ) {
					if ( $query['searchmode'] ) {
						if ( $query['searchmode'] == 1 ) {
							$segments[]				=	'search';
						} else {
							$segments[]				=	'searching';
						}
					}

					unset( $query['searchmode'] );
				}
				break;
			case 'pluginclass':
				if ( isset( $query['plugin'] ) ) {
					$plugin							=	$query['plugin'];
					$menuPlugin						=	( $menuItem && isset( $menuItem->query['plugin'] ) ? $menuItem->query['plugin'] : null );

					if ( $menuPlugin && ( $menuPlugin != $plugin ) ) {
						// The plugins don't match so turn off menu usage:
						$menuItem					=	null;
					}

					// Try and load the plugin to let it act on its URLs:
					if ( $_PLUGINS->loadPluginGroup( null, $plugin ) ) {
						$_PLUGINS->trigger( 'onBuildRoute', array( $this, $plugin, &$segments, &$query, &$menuItem ) );
					}

					if ( $plugin && ( ( ! $menuItem ) || ( ! $menuPlugin ) ) ) {
						// We have a plugin element, but it doesn't exist in the menu so lets add it:
						array_unshift( $segments, $plugin );
					}

					unset( $query['plugin'] );
				}
				break;
			case 'userprofile':
			case 'userdetails':
			case 'emailuser':
			case 'reportuser':
			case 'banprofile':
			case 'moderatebans':
			case 'viewreports':
			case 'addconnection':
			case 'removeconnection':
			case 'denyconnection':
			case 'acceptconnection':
				if ( in_array( $view, array( 'addconnection', 'removeconnection', 'denyconnection', 'acceptconnection' ) ) ) {
					$userVar						=	'connectionid';
				} elseif ( $view != 'userprofile' ) {
					$userVar						=	'uid';
				} else {
					$userVar						=	'user';
				}

				if ( isset( $query[$userVar] ) ) {
					$user							=	$query[$userVar];
					$menuUser						=	( $menuItem && isset( $menuItem->query[$userVar] ) ? $menuItem->query[$userVar] : null );

					if ( $menuUser && ( $menuUser != $user ) ) {
						// The users don't match so turn off menu usage:
						$menuItem					=	null;
					}

					if ( is_numeric( $user ) ) {
						$alias						=	CBuser::getUserDataInstance( $user )->get( 'alias', null, GetterInterface::STRING );

						if ( ! $alias ) {
							$alias					=	CBuser::getUserDataInstance( $user )->get( 'username', null, GetterInterface::STRING );
						}

						// Ensure the username isn't numeric and that it's alias safe otherwise prefix with user id:
						if ( ( ! is_numeric( $alias ) ) && ( $alias == Application::Router()->stringToAlias( $alias ) ) && ( ! in_array( $alias, Application::Router()->getViews() ) ) ) {
							$user					=	$alias;
						} else {
							$user					=	$user . '-' . Application::Router()->stringToAlias( $alias );
						}
					}

					if ( $user && ( ( ! $menuItem ) || ( ! $menuUser ) ) ) {
						// We have a user id, but it doesn't exist in the menu so lets add it:
						$segments[]					=	$user;
					}

					unset( $query[$userVar] );
				} else {
					if ( $menuItem && isset( $menuItem->query[$userVar] ) && ( $menuItem->query[$userVar] != '' ) ) {
						// The menu is for a specific profile, but this viewing users profile so turn off menu usage:
						$menuItem					=	null;
					}
				}
				break;
		}

		if ( ( ! $menuItem ) || $viewMismatch ) {
			// The views don't match so prepend it to the segments:
			array_unshift( $segments, $view );

			if ( ! $menuItem ) {
				unset( $query['Itemid'] );
			}
		}

		return $segments;
	}

	/**
	 * Builds the query from sef segments
	 *
	 * @param array $segments
	 * @return array
	 */
	public function parse( &$segments )
	{
		global $_CB_database, $_PLUGINS;

		$vars										=	array();

		// Load as little of CB as possible so we can utilize CB API for routing:
		if ( ! $this->loadCB() ) {
			return $vars;
		}

		$menuItem									=	$this->menu->getActive();

		if ( ! $menuItem ) {
			// If no menu item then shift off the first segment to treat it as the view:
			$view									=	array_shift( $segments );
		} else {
			$view									=	( $menuItem && isset( $menuItem->query['view'] ) ? $menuItem->query['view'] : null );

			if ( isset( $segments[0] ) && ( ( $segments[0] == $view ) || ( ( $segments[0] != $view ) && in_array( $segments[0], Application::Router()->getViews() ) ) ) ) {
				if ( $segments[0] != $view ) {
					// Looks like the menu view and the url view are different so we need to turn off menu usage of legacy URL B/C (e.g. /profile-menu-alias/userslist):
					$menuItem						=	null;
				}

				// We already have the view, but the URL still contains it; remove it for legacy URL support (e.g. /menu-alias/userprofile/username, /profile-menu-alias/userslist, /profile-menu-alias/pluginclass?plugin=XYZ, etc..):
				$view								=	array_shift( $segments );
			}
		}

		// No view was found so lets fallback to default (profile):
		if ( ! $view ) {
			$view									=	'userprofile';
		}

		$vars['view']								=	$view;

		switch ( $view ) {
			case 'userslist':
				if ( $menuItem && isset( $menuItem->query['listid'] ) ) {
					$listId							=	( $menuItem && isset( $menuItem->query['listid'] ) ? $menuItem->query['listid'] : 0 );
					$searchMode						=	( isset( $segments[0] ) ? $segments[0] : null );
				} else {
					$listId							=	( isset( $segments[0] ) ? preg_replace( '/-/', ':', $segments[0], 1 ) : 0 );
					$searchMode						=	( isset( $segments[1] ) ? $segments[1] : null );

					if ( strpos( $listId, ':' ) !== false ) {
						list( $listId, $listAlias )	=	explode( ':', $listId, 2 );
					} else {
						$listAlias					=	null;
					}

					if ( $listId && ( ! is_numeric( $listId ) ) ) {
						// Legacy URLs for B/C:
						$userList					=	new ListTable();

						$userList->load( array( 'title' => $segments[0] ) );

						$listId						=	$userList->get( 'listid', 0, GetterInterface::INT );
					}
				}

				if ( $listId ) {
					$vars['listid']					=	$listId;
				}

				if ( $searchMode == 'search' ) {
					$vars['searchmode']				=	1;
				} elseif ( $searchMode == 'searching' ) {
					$vars['searchmode']				=	2;
				}
				break;
			case 'pluginclass':
				if ( ! $menuItem ) {
					// If no menu item then shift off the first segment to treat it as the plugin (we send plugin as part of the trigger so we don't need to also send it as a segment):
					$plugin							=	array_shift( $segments );
				} else {
					$plugin							=	( $menuItem && isset( $menuItem->query['plugin'] ) ? $menuItem->query['plugin'] : null );
				}

				if ( $plugin ) {
					$vars['plugin']					=	$plugin;

					if ( $_PLUGINS->loadPluginGroup( null, $plugin ) ) {
						$_PLUGINS->trigger( 'onParseRoute', array( $this, $plugin, $segments, &$vars, $menuItem ) );
					}
				}
				break;
			case 'userprofile':
			case 'userdetails':
			case 'emailuser':
			case 'reportuser':
			case 'banprofile':
			case 'moderatebans':
			case 'viewreports':
			case 'addconnection':
			case 'removeconnection':
			case 'denyconnection':
			case 'acceptconnection':
				if ( in_array( $view, array( 'addconnection', 'removeconnection', 'denyconnection', 'acceptconnection' ) ) ) {
					$userVar						=	'connectionid';
				} elseif ( $view != 'userprofile' ) {
					$userVar						=	'uid';
				} else {
					$userVar						=	'user';
				}

				if ( $menuItem && isset( $menuItem->query[$userVar] ) ) {
					$user							=	$menuItem->query[$userVar];
					$userAlias						=	null;
				} else {
					$user							=	( isset( $segments[0] ) ? preg_replace( '/-/', ':', $segments[0], 1 ) : 0 );

					if ( strpos( $user, ':' ) !== false ) {
						list( $user, $userAlias )	=	explode( ':', $user, 2 );
					} else {
						$userAlias					=	null;
					}

					if ( $user && ( ! is_numeric( $user ) ) ) {
						// No user id was found (e.g. 42-username or 42) so lets find user from username or alias:
						$user21						=	$segments[0];
						$user20						=	rawurldecode( str_replace( array( ':', ',' ), array( '-', '.' ), $user21 ) );
						$user10						=	str_replace( array( ':', '_' ), array( '-', '.' ), $user21 );

						$sql						=	'SELECT DISTINCT u.' . $_CB_database->NameQuote( 'id' )
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
													.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
													.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
													.	"\n WHERE ( u." . $_CB_database->NameQuote( 'username' ) . " = " . $_CB_database->Quote( $user21 );

						if ( $user21 != $user20 ) {
							// CB 2.x Legacy rewriting B/C:
							$sql					.=	' OR u.' . $_CB_database->NameQuote( 'username' ) . ' = ' . $_CB_database->Quote( $user20 );
						}

						if ( $user21 != $user10 ) {
							// CB 1.x Legacy rewriting B/C:
							$sql					.=	' OR u.' . $_CB_database->NameQuote( 'username' ) . ' = ' . $_CB_database->Quote( $user10 );
						}

						$sql						.=	' OR c.' . $_CB_database->NameQuote( 'alias' ) . ' = ' . $_CB_database->Quote( $user21 ) . ' )';

						$_CB_database->setQuery( $sql, 0, 1 );
						$user						=	(int) $_CB_database->loadResult();
					}
				}

				if ( $user ) {
					$vars[$userVar]					=	$user;
				}
				break;
		}

		return $vars;
	}
}

/**
 * Legacy SEF class for B/C
 *
 * @param array $query
 * @return array
 */
function comprofilerBuildRoute( &$query )
{
	$router = new ComprofilerRouter();

	return $router->build( $query );
}

/**
 * Legacy SEF class for B/C
 *
 * @param array $segments
 * @return array
 */
function comprofilerParseRoute( $segments )
{
	$router = new ComprofilerRouter();

	return $router->parse( $segments );
}