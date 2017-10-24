<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 5/1/14 4:42 PM $
* @package CB\Database\Table
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Database\Table;

use CBLib\Database\Table\CheckedOrderedTable;
use CBLib\Input\Get;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\RegistryInterface;

defined('CBLIB') or die();

/**
 * CB\Database\Table\PluginTable Class implementation
 * @see \moscomprofilerPlugin (deprecated by this new class)
 */
class PluginTable extends CheckedOrderedTable
{
	/** @var int */
	public $id					=	null;
	/** @var string */
	public $name				=	null;
	/** @var string */
	public $element				=	null;
	/** @var string */
	public $type				=	null;
	/** @var string */
	public $folder				=	null;
	/** @var int */
	public $viewaccesslevel		=	null;
	/** @var string */
	public $backend_menu		=	null;
	/** @var int */
	public $ordering			=	null;
	/** @var int */
	public $published			=	null;
	/** @var int */
	public $iscore				=	null;
	/** @var int */
	public $client_id			=	null;
	/** @var int */
	public $checked_out			=	null;
	/** @var string datetime */
	public $checked_out_time	=	null;
	/** @var string */
	public $params				=	null;

	/** @var array */
	protected $_langoverrides	=	null;
	/** @var string */
	protected $_cssoverrides	=	null;

	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl				=	'#__comprofiler_plugin';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key			=	'id';

	/**
	 * Ordering keys and for each their ordering groups.
	 * E.g.; array( 'ordering' => array( 'tab' ), 'ordering_registration' => array() )
	 * @var array
	 */
	protected $_orderings	=	array( 'ordering' => array( ) );

	/**
	 *	Loads a row from the database into $this object by primary key
	 *
	 * @param  int|array  $keys   [Optional]: Primary key value or array of primary keys to match. If not specified, the value of current key is used
	 * @return boolean            Result from the database operation
	 *
	 * @throws  \InvalidArgumentException
	 * @throws  \RuntimeException
	 * @throws  \UnexpectedValueException
	 */
	public function load( $keys = null )
	{
		global $_CB_framework, $ueConfig;

		$plugin								=	parent::load( $keys );

		if ( ( $this->id == 1 ) && ( ! $this->params ) ) {
			$oldConfig						=	$_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_comprofiler/ue_config.php';

			if ( file_exists( $oldConfig ) ) {
				$currentVersion				=	( isset( $ueConfig['version'] ) ? $ueConfig['version'] : null );

				/** @noinspection PhpIncludeInspection */
				include_once( $oldConfig );

				if ( $currentVersion && isset( $ueConfig['version'] ) ) {
					$ueConfig['version']	=	$currentVersion;
				}

				$this->params				=	json_encode( $ueConfig );
			}
		}

		return $plugin;
	}

	/**
	 * If table key (id) is NULL : inserts a new row
	 * otherwise updates existing row in the database table
	 *
	 * Can be overridden or overloaded by the child class
	 *
	 * @param  boolean  $updateNulls  TRUE: null object variables are also updated, FALSE: not.
	 * @return boolean                TRUE if successful otherwise FALSE
	 *
	 * @throws \RuntimeException
	 */
	public function store( $updateNulls = false )
	{
		global $_CB_database, $_PLUGINS;

		$return								=	parent::store( $updateNulls );

		if ( $return ) {
			if ( $this->id == 1 ) {
				$config						=	json_decode( $this->params, true );

				if ( isset( $config['name_style'] ) ) {
					switch ( (int) $config['name_style'] ) {
						case 2:
							$nameArray		=	array( 'name' => 0, 'firstname' => 1, 'middlename' => 0, 'lastname' => 1 );
							break;
						case 3:
							$nameArray		=	array( 'name' => 0, 'firstname' => 1, 'middlename' => 1, 'lastname' => 1 );
							break;
						case 1:
						default:
							$nameArray		=	array( 'name' => 1, 'firstname' => 0, 'middlename' => 0, 'lastname' => 0 );
							break;
					}

					foreach ( $nameArray as $name => $published ) {
						$query				=	'UPDATE ' . $_CB_database->NameQuote( '#__comprofiler_fields' )
							.	"\n SET " . $_CB_database->NameQuote( 'published' ) . " = " . (int) $published
							.	"\n WHERE " . $_CB_database->NameQuote( 'name' ) . " = " . $_CB_database->Quote( $name );
						$_CB_database->setQuery( $query );
						$_CB_database->query();
					}
				}
			} elseif ( ( $this->type == 'language' ) && ( $this->_langoverrides !== null ) ) {
				$adminFileSystem						=	\cbAdminFileSystem::getInstance();
				$langOverrides							=	( is_string( $this->_langoverrides ) ? json_decode( $this->_langoverrides, true ) : $this->_langoverrides );
				$newLangOverrides						=	array();

				foreach ( $langOverrides as $langOverride ) {
					$langKey							=	trim( Get::clean( $langOverride['key'], GetterInterface::STRING ) );
					$langString							=	$langOverride['text'];

					if ( $langKey ) {
						$newLangOverrides[$langKey]		=	$langString;
					}
				}

				$langOverrideFilePath					=	$_PLUGINS->getPluginPath( $this ) . '/override.php';

				if ( $newLangOverrides ) {
					$langOverrideFile					=	"<?php\n\n"
														.	"defined('CBLIB') or die();\n\n"
														.	"return " . var_export( $newLangOverrides, true ) . ";";

					$adminFileSystem->file_put_contents( $langOverrideFilePath, $langOverrideFile );
				} elseif ( $adminFileSystem->file_exists( $langOverrideFilePath ) ) {
					$adminFileSystem->unlink( $langOverrideFilePath );
				}
			} elseif ( ( $this->type == 'templates' ) && ( $this->_cssoverrides !== null ) ) {
				$adminFileSystem						=	\cbAdminFileSystem::getInstance();
				$cssOverrideFilePath					=	$_PLUGINS->getPluginPath( $this ) . '/override.css';

				if ( $this->_cssoverrides ) {
					$adminFileSystem->file_put_contents( $cssOverrideFilePath, Get::clean( $this->_cssoverrides, GetterInterface::STRING ) );
				} elseif ( $adminFileSystem->file_exists( $cssOverrideFilePath ) ) {
					$adminFileSystem->unlink( $cssOverrideFilePath );
				}
			}
		}

		return $return;
	}

	/**
	 * Copy the named array or object content into this object as vars
	 * only existing vars of object are filled.
	 * When undefined in array, object variables are kept.
	 *
	 * WARNING: DOES addslashes / escape BY DEFAULT
	 *
	 * Can be overridden or overloaded.
	 *
	 * @param  array|object  $array         The input array or object
	 * @param  string        $ignore        Fields to ignore
	 * @param  string        $prefix        Prefix for the array keys
	 * @return boolean                      TRUE: ok, FALSE: error on array binding
	 */
	public function bind( $array, $ignore = '', $prefix = null ) {
		$bind						=	parent::bind( $array, $ignore, $prefix );

		if ( $bind ) {
			// List of custom private plugin variables to parse for and bind:
			$custom					=	array( '_langoverrides', '_cssoverrides' );

			// Set the ignore variable up like bind does encase this bind call was told to ignore custom variables:
			$ignore					=	' ' . $ignore . ' ';

			foreach ( $custom as $k ) {
				// Use the same behavior as a normal bind excluding the _ ignore check for consistency:
				if ( strpos( $ignore, ' ' . $k . ' ') === false ) {
					$ak				=	$prefix . $k;

					if ( is_array( $array ) && isset( $array[$ak] ) ) {
						$this->$k	=	$array[$ak];
					} elseif ( isset( $array->$ak ) ) {
						$this->$k	=	$array->$ak;
					}
				}
			}
		}

		return $bind;
	}

	/**
	 *	Check values before store method  (override if needed)
	 *
	 *	@return boolean  TRUE if the object is safe for saving
	 */
	public function check()
	{
		if ( ! $this->name ) {
			$this->_error	=	CBTxt::T( 'Name missing!' );

			return false;
		} elseif ( ( $this->type == 'language' ) && ( $this->published == 0 ) ) {
			$this->_error	=	CBTxt::T( 'Language plugins cannot be unpublished!' );

			return false;
		} elseif ( ( $this->type == 'templates' ) && ( $this->published == 0 ) ) {
			$this->_error	=	CBTxt::T( 'Template plugins cannot be unpublished!' );

			return false;
		} elseif ( ( $this->id == 1 ) && ( $this->published == 0 ) ) {
			$this->_error	=	CBTxt::T( 'Core plugins cannot be unpublished!' );

			return false;
		}

		return parent::check();
	}

	/**
	 * Generic check for whether dependancies exist for this object in the db schema
	 *
	 * @param  int  $oid  key index (only int supported here)
	 * @return boolean
	 */
	public function canDelete( $oid = null )
	{
		if ( $this->iscore ) {
			$this->_error	=	CBTxt::T( 'Core plugins cannot be deleted!' );

			return false;
		}

		return parent::canDelete( $oid );
	}

	/**
	 * Deletes this record (no checks)
	 *
	 * @param  int      $oid   Key id of row to delete (otherwise it's the one of $this) (only int supported here)
	 * @return boolean         TRUE if OK, FALSE if error
	 */
	public function delete( $oid = null )
	{
		$k					=	$this->_tbl_key;

		if ( $oid ) {
			$this->$k		=	(int) $oid;
		}

		cbimport( 'cb.installer' );

		ob_start();
		$plgInstaller		=	new \cbInstallerPlugin();		//TODO: Move
		$installed			=	$plgInstaller->uninstall( $this->$k, 'com_comprofiler' );
		ob_end_clean();

		if ( ! $installed ) {
			$this->_error	=	$plgInstaller->getError();

			return false;
		}

		return true;
	}

	/**
	 * returns plugin version string
	 * Used by Backend XML only
	 * @deprecated Do not use directly, only for XML tabs backend
	 *
	 * @param  string                             $value         The value of the element
	 * @param  \CBLib\Registry\RegistryInterface  $pluginParams
	 * @param  string                             $name          The name of the form element
	 * @param  \SimpleXMLElement                  $node          The xml element for the parameter
	 * @return string
	 */
	public function getPluginVersion( /** @noinspection PhpUnusedParameterInspection */ $value, $pluginParams, $name, $node )
	{
		global $_PLUGINS;

		$size	=	(int) $node->attributes( 'size' );

		return $_PLUGINS->getPluginVersion( $this->id, false, 24, $size );
	}

	/**
	 * returns plugin menu items string
	 * Used by Backend XML only
	 * @deprecated Do not use directly, only for XML tabs backend
	 *
	 * @return string
	 */
	public function getPluginMenu()
	{
		global $_PLUGINS;

		$menuItems		=	$_PLUGINS->getPluginBackendMenu( $this->id );
		$menuIndex		=	1;
		$return			=	null;

		if ( $menuItems ) {
			$return		.=	'<div><small>[ ';

			foreach ( $menuItems as $menuItem ) {
				$return	.=	( $menuIndex > 1 ? ' - ' : null ) . '<a href="' . $menuItem[1] . '">' . $menuItem[0] . '</a>';

				$menuIndex++;
			}

			$return		.=	' ]</small></div>';
		}

		return $return;
	}

	/**
	 * returns true or false if plugin is compatible with current major CB release
	 * Used by Backend XML only
	 * @deprecated Do not use directly, only for XML tabs backend
	 *
	 * @return boolean
	 */
	public function checkPluginCompatibility()
	{
		global $_PLUGINS;

		return $_PLUGINS->checkPluginCompatibility( $this->id );
	}

	/**
	 * returns true or false if plugin is installed (main XML exists)
	 * Used by Backend XML only
	 * @deprecated Do not use directly, only for XML tabs backend
	 *
	 * @return boolean
	 */
	public function checkPluginInstalled()
	{
		global $_PLUGINS;

		return file_exists( $_PLUGINS->getPluginXmlPath( $this->id ) );
	}

	/**
	 * returns true or false if the plugin has a custom edit display
	 * Used by Backend XML only
	 * @deprecated Do not use directly, only for XML plugins backend
	 *
	 * @return bool
	 */
	public function checkCustomPluginEdit()
	{
		global $_PLUGINS;

		$_PLUGINS->loadPluginGroup( 'user' );

		$pluginXML					=	$_PLUGINS->loadPluginXML( 'action', null, $this->id );

		if ( $pluginXML ) {
			$adminActionsModel		=&	$pluginXML->getChildByNameAttr( 'actions', 'ui', 'admin' );

			if ( $adminActionsModel ) {
				$defaultAction		=&	$adminActionsModel->getChildByNameAttr( 'action', 'name', 'default' );
				$actionRequest		=	$defaultAction->attributes( 'request' );
				$actionAction		=	$defaultAction->attributes( 'action' );

				if ( ( $actionRequest === '' ) && ( $actionAction === '' ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * adds css or language overrides to xml data
	 * Used by Backend XML only
	 * @deprecated Do not use directly, only for XML tabs backend
	 *
	 * @param string             $value
	 * @param RegistryInterface  $pluginParams
	 * @param string             $name
	 * @param \SimpleXMLElement  $node
	 * @param string             $control_name
	 * @param string             $control_name_name
	 * @param boolean            $view
	 * @param RegistryInterface  $data
	 */
	public function fetchOverrides( /** @noinspection PhpUnusedParameterInspection */ $value, $pluginParams, $name, $node, $control_name, $control_name_name, $view, $data ) {
		global $_PLUGINS;

		if ( $this->type == 'language' ) {
			$langOverrides					=	array();
			$langOverrideFilePath			=	$_PLUGINS->getPluginPath( $this ) . '/override.php';

			if ( file_exists( $langOverrideFilePath ) ) {
				$langOverrideStrings		=	include $langOverrideFilePath;

				if ( is_array( $langOverrideStrings ) ) {
					foreach ( $langOverrideStrings as $langKey => $langValue ) {
						$langOverrides[]	=	array( 'key' => $langKey, 'text' => $langValue );
					}
				}
			}

			$data->set( '_langoverrides', $langOverrides );
		} elseif ( $this->type == 'templates' ) {
			$cssOverrides					=	null;
			$cssOverrideFilePath			=	$_PLUGINS->getPluginPath( $this ) . '/override.css';

			if ( is_readable( $cssOverrideFilePath ) ) {
				$cssOverrideString			=	file_get_contents( $cssOverrideFilePath );

				if ( $cssOverrideString !== false ) {
					$cssOverrides			=	$cssOverrideString;
				}
			}

			$data->set( '_cssoverrides', $cssOverrides );
		}
	}
}