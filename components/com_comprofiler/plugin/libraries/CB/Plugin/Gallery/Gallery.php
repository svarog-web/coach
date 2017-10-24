<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery;

use CB\Plugin\Gallery\Table\FolderTable;
use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\ParametersStore;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Gallery\Table\ItemTable;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

/**
 * @method int getId()
 * @method Gallery setId( $id )
 * @method array getModerators()
 * @method Gallery setModerators( $moderators )
 * @method int getUserId()
 * @method Gallery setUserId( $folder )
 * @method string|array getType()
 * @method Gallery setType( $type )
 * @method int getFolder()
 * @method Gallery setFolder( $folder )
 * @method string getSearch()
 * @method Gallery setSearch( $search )
 * @method string getFile()
 * @method Gallery setFile( $file )
 * @method string getValue()
 * @method Gallery setValue( $value )
 * @method string getTitle()
 * @method Gallery setTitle( $title )
 * @method string getDescription()
 * @method Gallery setDescription( $description )
 * @method int getPublished()
 * @method Gallery setPublished( $published )
 * @method string getLocation()
 * @method Gallery setLocation( $url )
 */
class Gallery extends ParametersStore implements GalleryInterface
{
	/** @var string $id */
	protected $id						=	null;
	/** @var string $asset */
	protected $asset					=	'profile';
	/** @var UserTable $user */
	protected $user						=	null;
	/** @var array $ini */
	protected $ini						=	array();

	/** @var bool $clearFolderCount */
	protected $clearFolderCount			=	false;
	/** @var bool $clearFolderSelect */
	protected $clearFolderSelect		=	false;

	/** @var bool $clearItemCount */
	protected $clearItemCount			=	false;
	/** @var bool $clearItemSelect */
	protected $clearItemSelect			=	false;

	/** @var array $defaults */
	protected $defaults					=	array(	'moderators'						=>	array(),
													'notify'							=>	null,
													'folders'							=>	true,
													'folders_width'						=>	200,
													'folders_create'					=>	true,
													'folders_create_access'				=>	2,
													'folders_create_limit'				=>	'custom',
													'folders_create_limit_custom'		=>	0,
													'folders_create_approval'			=>	false,
													'folders_create_approval_notify'	=>	true,
													'folders_create_captcha'			=>	false,
													'folders_paging'					=>	true,
													'folders_paging_limit'				=>	15,
													'folders_search'					=>	true,
													'folders_orderby'					=>	'date_desc',
													'items_width'						=>	200,
													'items_create'						=>	true,
													'items_create_captcha'				=>	false,
													'items_create_approval_notify'		=>	true,
													'items_paging'						=>	true,
													'items_paging_limit'				=>	15,
													'items_search'						=>	true,
													'items_orderby'						=>	'date_desc',
													'photos'							=>	true,
													'photos_download'					=>	true,
													'photos_create'						=>	true,
													'photos_create_access'				=>	2,
													'photos_create_limit'				=>	'custom',
													'photos_create_limit_custom'		=>	0,
													'photos_upload'						=>	true,
													'photos_link'						=>	true,
													'photos_create_approval'			=>	false,
													'photos_resample'					=>	1,
													'photos_image_height'				=>	640,
													'photos_image_width'				=>	1280,
													'photos_thumbnail_height'			=>	320,
													'photos_thumbnail_width'			=>	640,
													'photos_maintain_aspect_ratio'		=>	1,
													'photos_min_size'					=>	0,
													'photos_max_size'					=>	1024,
													'videos'							=>	true,
													'videos_download'					=>	false,
													'videos_create'						=>	true,
													'videos_create_access'				=>	2,
													'videos_create_limit'				=>	'custom',
													'videos_create_limit_custom'		=>	0,
													'videos_upload'						=>	true,
													'videos_link'						=>	true,
													'videos_create_approval'			=>	false,
													'videos_min_size'					=>	0,
													'videos_max_size'					=>	1024,
													'files'								=>	true,
													'files_create'						=>	true,
													'files_create_access'				=>	2,
													'files_create_limit'				=>	'custom',
													'files_create_limit_custom'			=>	0,
													'files_upload'						=>	true,
													'files_link'						=>	true,
													'files_create_approval'				=>	false,
													'files_extensions'					=>	'zip,rar,doc,pdf,txt,xls',
													'files_min_size'					=>	0,
													'files_max_size'					=>	1024,
													'music'								=>	true,
													'music_download'					=>	false,
													'music_create'						=>	true,
													'music_create_access'				=>	2,
													'music_create_limit'				=>	'custom',
													'music_create_limit_custom'			=>	0,
													'music_upload'						=>	true,
													'music_link'						=>	true,
													'music_create_approval'				=>	false,
													'music_min_size'					=>	0,
													'music_max_size'					=>	1024,
													'thumbnails'						=>	true,
													'thumbnails_upload'					=>	true,
													'thumbnails_link'					=>	false,
													'thumbnails_resample'				=>	1,
													'thumbnails_image_height'			=>	320,
													'thumbnails_image_width'			=>	640,
													'thumbnails_maintain_aspect_ratio'	=>	1,
													'thumbnails_min_size'				=>	0,
													'thumbnails_max_size'				=>	1024
												);

	/** @var FolderTable[] $loadedFolders */
	protected static $loadedFolders		=	array();
	/** @var ItemTable[] $loadedItems */
	protected static $loadedItems		=	array();

	/**
	 * Constructor for gallery object
	 *
	 * @param null|string        $asset
	 * @param null|int|UserTable $user
	 */
	public function __construct( $asset = null, $user = null )
	{
		global $_CB_framework, $_PLUGINS;

		$_CB_framework->addJQueryPlugin( 'cbgallery', '/components/com_comprofiler/plugin/user/plug_cbgallery/js/cbgallery.js', array( -1 => array( 'ui-all', 'iframe-transport', 'fileupload', 'form', 'cbmoreless', 'livestamp', 'cbtimeago', 'qtip', 'cbtooltip' ) ) );

		$_PLUGINS->loadPluginGroup( 'user' );

		if ( ( $user === null ) || in_array( $asset, array( 'self', 'self.connections', 'self.connectionsonly', 'user', 'user.connections', 'user.connectionsonly' ) ) ) {
			$user				=	\CBuser::getMyUserDataInstance();
		} elseif ( is_int( $user ) ) {
			$user				=	\CBuser::getUserDataInstance( $user );
		}

		if ( in_array( $asset, array( 'user', 'user.connections', 'user.connectionsonly', 'displayed', 'displayed.connections', 'displayed.connectionsonly' ) ) ) {
			if ( $_CB_framework->displayedUser() ) {
				$user			=	\CBuser::getUserDataInstance( $_CB_framework->displayedUser() );
			} elseif ( ! in_array( $asset, array( 'user', 'user.connections', 'user.connectionsonly' ) ) ) {
				$user			=	\CBuser::getUserDataInstance( 0 );
			}
		}

		if ( ( $asset === null ) || in_array( $asset, array( 'profile', 'connections', 'connectionsonly', 'self', 'self.connections', 'self.connectionsonly', 'user', 'user.connections', 'user.connectionsonly', 'displayed', 'displayed.connections', 'displayed.connectionsonly' ) ) ) {
			$newAsset			=	'profile.' . $user->get( 'id', 0, GetterInterface::INT );

			if ( Application::Config()->get( 'allowConnections', true, GetterInterface::BOOLEAN ) ) {
				if ( strpos( $asset, 'connectionsonly' ) !== false ) {
					$newAsset	.=	'.connectionsonly';
				} elseif ( strpos( $asset, 'connections' ) !== false ) {
					$newAsset	.=	'.connections';
				}
			}

			$asset				=	$newAsset;
		}

		$asset					=	str_replace( '*', '%', $asset );

		$extras					=	array(	'displayed_id'	=>	$_CB_framework->displayedUser(),
											'viewer_id'		=>	Application::MyUser()->getUserId()
										);

		$this->asset			=	\CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ), false )->replaceUserVars( $asset, true, false, $extras, false );
		$this->user				=	$user;

		static $pluginParams	=	null;

		if ( ! $pluginParams ) {
			$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );
			$pluginParams		=	$_PLUGINS->getPluginParams( $plugin );
		}

		foreach ( $this->defaults as $param => $default ) {
			$value				=	$pluginParams->get( $param, $default, GetterInterface::STRING );

			if ( is_int( $default ) ) {
				$value			=	(int) $value;
			} elseif ( is_bool( $default ) ) {
				$value			=	(bool) $value;
			}

			$this->set( $param, $value );
		}
	}

	/**
	 * @param string $name
	 * @param array  $arguments
	 * @return self|string|int|array|null
	 */
	public function __call( $name, $arguments )
	{
		$method									=	substr( $name, 0, 3 );

		if ( in_array( $method, array( 'get', 'set' ) ) ) {
			$variables							=	array( 'id', 'moderators', 'user_id', 'type', 'folder', 'search', 'file', 'value', 'title', 'description', 'published', 'location' );
			$variable							=	strtolower( substr( $name, 3 ) );

			switch ( $variable ) {
				case 'userid':
					$variable					=	'user_id';
					break;
			}

			if ( in_array( $variable, $variables ) ) {
				switch ( $method ) {
					case 'get':
						switch ( $variable ) {
							case 'id':
							case 'user_id':
								if ( is_array( $this->get( $variable, null, GetterInterface::RAW ) ) ) {
									$default	=	array();
									$type		=	GetterInterface::RAW;
								} else {
									$default	=	0;
									$type		=	GetterInterface::INT;
								}
								break;
							case 'folder':
							case 'published':
								$default		=	0;
								$type			=	GetterInterface::INT;
								break;
							default:
								if ( is_array( $this->get( $variable, null, GetterInterface::RAW ) ) ) {
									$default	=	array();
									$type		=	GetterInterface::RAW;
								} else {
									$default	=	null;
									$type		=	GetterInterface::STRING;
								}
								break;
						}

						return $this->get( $variable, $default, $type );
						break;
					case 'set':
						$this->set( $variable, ( $arguments ? $arguments[0] : null ) );

						return $this;
						break;
				}
			}
		}

		trigger_error( 'Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR );

		return null;
	}

	/**
	 * Reloads the gallery from session by id
	 *
	 * @param string $id
	 * @return bool
	 */
	public function load( $id )
	{
		$session			=	Application::Session()->subTree( 'gallery.' . $id );

		if ( $session ) {
			$this->id		=	$id;
			$this->asset	=	$session->get( 'asset', null, GetterInterface::STRING );
			$this->user		=	\CBuser::getUserDataInstance( $session->get( 'user', 0, GetterInterface::INT ) );
			$this->ini		=	$session->asArray();

			parent::load( $session );

			return true;
		}

		return false;
	}

	/**
	 * Parses parameters into the gallery
	 *
	 * @param ParamsInterface|array|string $params
	 * @param null|string                  $prefix
	 * @return self
	 */
	public function parse( $params, $prefix = null )
	{
		if ( $params instanceof self ) {
			$this->id			=	$params->id;
			$this->asset		=	$params->asset;
			$this->user			=	$params->user;
			$this->ini			=	$params->ini;

			parent::load( $params->ini );
		} else {
			if ( is_array( $params ) ) {
				$params			=	new Registry( $params );
			}

			foreach ( $this->defaults as $param => $default ) {
				$value			=	$params->get( $prefix . $param, null, GetterInterface::STRING );

				if ( ( $value !== '' ) && ( $value !== null ) && ( $value !== '-1' ) ) {
					if ( is_int( $default ) ) {
						$value	=	(int) $value;
					} elseif ( is_bool( $default ) ) {
						$value	=	(bool) $value;
					}

					$this->set( $param, $value );
				}
			}

			$this->cache();
		}

		return $this;
	}

	/**
	 * Gets the gallery location
	 *
	 * @return string
	 */
	public function location()
	{
		global $_CB_framework;

		if ( $this->get( 'location', null, GetterInterface::STRING ) ) {
			$location					=	$this->get( 'location', null, GetterInterface::STRING );

			if ( $location == 'plugin' ) {
				$location				=	$_CB_framework->pluginClassUrl( 'cbgallery', false, array( 'action' => 'gallery', 'gallery' => $this->id() ) );
			} elseif ( $location == 'current' ) {
				$location				=	CBGallery::getReturn( false, true );

				$this->set( 'location', $location );
			}
		} elseif ( preg_match( '/^profile/', $this->asset(), $matches ) ) {
			if ( preg_match( '/^profile\.(\d+)\.field\.(\d+)/', $this->asset(), $matches ) ) {
				$fieldId				=	(int) $matches[2];

				static $fields			=	array();

				if ( ! isset( $fields[$fieldId] ) ) {
					$field				=	new FieldTable();

					$field->load( $fieldId );

					$fields[$fieldId]	=	$field;
				}

				$location				=	$_CB_framework->userProfileUrl( (int) $matches[1], false, $fields[$fieldId]->get( 'tabid', 0, GetterInterface::INT ) );
			} elseif ( preg_match( '/^profile\.(\d+)/', $this->asset(), $matches ) ) {
				$location				=	$_CB_framework->userProfileUrl( (int) $matches[1], false, 'cbgalleryTab' );
			} else {
				$location				=	$_CB_framework->userProfileUrl( $this->user()->get( 'id', 0, GetterInterface::INT ), false, 'cbgalleryTab' );
			}
		} else {
			$location					=	CBGallery::getReturn( false, true );
		}

		if ( $this->get( 'folder', 0, GetterInterface::INT ) ) {
			$location					=	$_CB_framework->pluginClassUrl( 'cbgallery', false, array( 'action' => 'folder', 'func' => 'show', 'id' => $this->get( 'folder', 0, GetterInterface::INT ), 'gallery' => $this->id(), 'return' => ( $location ? base64_encode( $location ) : null ) ) );
		}

		return $location;
	}

	/**
	 * Gets the gallery id
	 *
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Gets the gallery asset
	 *
	 * @param bool $raw
	 * @return string
	 */
	public function asset( $raw = false )
	{
		$asset			=	( $raw ? $this->asset : strtolower( trim( preg_replace( '/[^a-zA-Z0-9.]/i', '', $this->asset ) ) ) );

		if ( ( ( ! $this->asset ) || in_array( $this->asset, array( 'all', 'profile.%' ) ) ) && ( ! $raw ) ) {
			$asset		=	'profile.' . $this->user->get( 'id', 0, GetterInterface::INT );
		}

		return $asset;
	}

	/**
	 * Gets the gallery target user (owner)
	 *
	 * @return UserTable|null
	 */
	public function user()
	{
		return $this->user;
	}

	/**
	 * Gets the types allowed in this gallery
	 *
	 * @return array
	 */
	public function types()
	{
		$types			=	array();

		if ( $this->get( 'photos', true, GetterInterface::BOOLEAN ) ) {
			$types[]	=	'photos';
		}

		if ( $this->get( 'videos', true, GetterInterface::BOOLEAN ) ) {
			$types[]	=	'videos';
		}

		if ( $this->get( 'files', true, GetterInterface::BOOLEAN ) ) {
			$types[]	=	'files';
		}

		if ( $this->get( 'music', true, GetterInterface::BOOLEAN ) ) {
			$types[]	=	'music';
		}

		return $types;
	}

	/**
	 * Clears the data cache
	 *
	 * @return self
	 */
	public function clear()
	{
		$this->clearFolderCount		=	true;
		$this->clearFolderSelect	=	true;

		$this->clearItemCount		=	true;
		$this->clearItemSelect		=	true;

		return $this;
	}

	/**
	 * Resets the gallery filters
	 *
	 * @return self
	 */
	public function reset()
	{
		$gallery	=	new self( $this->asset, $this->user );

		return $gallery->parse( $this );
	}

	/**
	 * Retrieves gallery folder rows or row count
	 *
	 * @param bool $count
	 * @return FolderTable[]|int
	 */
	public function folders( $count = false )
	{
		global $_CB_database, $_PLUGINS;

		if ( ! $this->get( 'folders', true, GetterInterface::BOOLEAN ) ) {
			if ( $count ) {
				return 0;
			} else {
				return array();
			}
		}

		static $cache						=	array();

		$id									=	$this->get( 'id', null, GetterInterface::RAW );
		$hasId								=	( ( ( $id !== '' ) && ( $id !== null ) ) || ( is_array( $id ) && $id ) );

		$select								=	array();
		$join								=	array();
		$where								=	array();

		if ( $count ) {
			$select[]						=	'COUNT( a.' . $_CB_database->NameQuote( 'id' ) . ' )';
		} else {
			$itemsSelect					=	array( 'COUNT( b.' . $_CB_database->NameQuote( 'id' ) . ' )' );
			$itemsJoin						=	array();
			$itemsWhere						=	array();

			if ( $this->asset && ( $this->asset != 'all' ) )  {
				if ( strpos( $this->asset(), 'connections' ) !== false ) {
					$assets					=	array();

					if ( preg_match( '/^profile\.(\d+)\.connections/', $this->asset(), $matches ) ) {
						$profileId			=	(int) $matches[1];
					} else {
						$profileId			=	$this->user->get( 'id', 0, GetterInterface::INT );
					}

					if ( $profileId ) {
						if ( strpos( $this->asset(), 'connectionsonly' ) === false ) {
							$assets[]		=	'profile.' . $profileId;
						}

						foreach( CBGallery::getConnections( $profileId ) as $connection ) {
							$assets[]		=	'profile.' . (int) $connection->id;
						}
					}

					if ( $assets ) {
						$itemsWhere[]		=	"b." . $_CB_database->NameQuote( 'asset' ) . " IN " . $_CB_database->safeArrayOfStrings( $assets );
					} else {
						$itemsWhere[]		=	"b." . $_CB_database->NameQuote( 'asset' ) . " = " . $_CB_database->Quote( 'none' );
					}
				} else {
					if ( ( strpos( $this->asset, '%' ) !== false ) || ( strpos( $this->asset, '_' ) !== false ) ) {
						$itemsWhere[]		=	"b." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $this->asset );
					} else {
						$itemsWhere[]		=	"b." . $_CB_database->NameQuote( 'asset' ) . " = " . $_CB_database->Quote( $this->asset );
					}
				}
			}

			if ( $this->types() && ( count( $this->types() ) < 4 ) ) {
				$itemsWhere[]				=	"b." . $_CB_database->NameQuote( 'type' ) . " IN " . $_CB_database->safeArrayOfStrings( $this->types() );
			}

			$itemsWhere[]					=	"b." . $_CB_database->NameQuote( 'folder' ) . " = a." . $_CB_database->NameQuote( 'id' );

			if ( ( $this->get( 'published', null, GetterInterface::RAW ) !== '' ) && ( $this->get( 'published', null, GetterInterface::RAW ) !== null ) ) {
				if ( ( $this->get( 'published', null, GetterInterface::INT ) == 1 ) && Application::MyUser()->getUserId() ) {
					$itemsWhere[]			=	"( b." . $_CB_database->NameQuote( 'published' ) . " = 1"
											.	" OR b." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) Application::MyUser()->getUserId() . " )";
				} else {
					$itemsWhere[]			=	"b." . $_CB_database->NameQuote( 'published' ) . " = " . $this->get( 'published', null, GetterInterface::INT );
				}
			}

			$_PLUGINS->trigger( 'gallery_onQueryFolderItems', array( $count, &$itemsSelect, &$itemsWhere, &$itemsJoin, &$this ) );

			$items							=	'SELECT ' . implode( ', ', $itemsSelect )
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' ) . " AS b"
											.	( $itemsJoin ? "\n " . implode( "\n ", $itemsJoin ) : null )
											.	( $itemsWhere ? "\n WHERE " . implode( "\n AND ", $itemsWhere ) : null );

			$select[]						=	'a.*';
			$select[]						=	'( ' . $items . ' ) AS _items';
		}

		if ( $hasId ) {
			if ( is_array( $this->get( 'id', null, GetterInterface::RAW ) ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $id );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
			}
		}

		$userId								=	$this->get( 'user_id', null, GetterInterface::RAW );

		if ( ( ( $userId !== '' ) && ( $userId !== null ) ) || ( is_array( $userId ) && $userId ) ) {
			if ( is_array( $userId ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $userId );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId;
			}
		}

		if ( $this->asset && ( $this->asset != 'all' ) )  {
			if ( strpos( $this->asset(), 'connections' ) !== false ) {
				$assets						=	array();

				if ( preg_match( '/^profile\.(\d+)\.connections/', $this->asset(), $matches ) ) {
					$profileId				=	(int) $matches[1];
				} else {
					$profileId				=	$this->user->get( 'id', 0, GetterInterface::INT );
				}

				if ( $profileId ) {
					if ( strpos( $this->asset(), 'connectionsonly' ) === false ) {
						$assets[]			=	'profile.' . $profileId;
					}

					foreach( CBGallery::getConnections( $profileId ) as $connection ) {
						$assets[]			=	'profile.' . (int) $connection->id;
					}
				}

				if ( $assets ) {
					$where[]				=	"a." . $_CB_database->NameQuote( 'asset' ) . " IN " . $_CB_database->safeArrayOfStrings( $assets );
				} else {
					if ( $count ) {
						return 0;
					} else {
						return array();
					}
				}
			} else {
				if ( ( strpos( $this->asset, '%' ) !== false ) || ( strpos( $this->asset, '_' ) !== false ) ) {
					$where[]				=	"a." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $this->asset );
				} else {
					$where[]				=	"a." . $_CB_database->NameQuote( 'asset' ) . " = " . $_CB_database->Quote( $this->asset );
				}
			}
		}

		if ( $this->get( 'title', null, GetterInterface::STRING ) != ''  ) {
			if ( strpos( $this->get( 'title', null, GetterInterface::STRING ), '%' ) !== false ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'title' ) . " = " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
			}
		}

		if ( $this->get( 'description', null, GetterInterface::STRING ) != ''  ) {
			if ( strpos( $this->get( 'description', null, GetterInterface::STRING ), '%' ) !== false ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( $this->get( 'description', null, GetterInterface::STRING ) );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'description' ) . " = " . $_CB_database->Quote( $this->get( 'description', null, GetterInterface::STRING ) );
			}
		}

		if ( ( ! $hasId ) && $this->get( 'folders_search', true, GetterInterface::BOOLEAN ) ) {
			if ( $this->get( 'search', null, GetterInterface::STRING ) != '' ) {
				$where[]					=	"( a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
											.	" OR a." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false ) . " )";
			}
		}

		if ( ( $this->get( 'published', null, GetterInterface::RAW ) !== '' ) && ( $this->get( 'published', null, GetterInterface::RAW ) !== null ) ) {
			if ( ( $this->get( 'published', null, GetterInterface::INT ) == 1 ) && Application::MyUser()->getUserId() ) {
				$where[]					=	"( a." . $_CB_database->NameQuote( 'published' ) . " = 1"
											.	" OR a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) Application::MyUser()->getUserId() . " )";
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'published' ) . " = " . $this->get( 'published', null, GetterInterface::INT );
			}
		}

		$_PLUGINS->trigger( 'gallery_onQueryFolders', array( $count, &$select, &$join, &$where, &$this ) );

		$query								=	'SELECT ' . implode( ', ', $select )
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_folders' ) . " AS a"
											.	( $join ? "\n " . implode( "\n ", $join ) : null )
											.	( $where ? "\n WHERE " . implode( "\n AND ", $where ) : null );

		if ( ! $count ) {
			$orderBy						=	$this->get( 'folders_orderby', 'date_desc', GetterInterface::STRING );

			if ( ! $orderBy ) {
				$orderBy					=	'date_desc';
			}

			if ( $orderBy == 'random' ) {
				$query						.=	"\n ORDER BY RAND( " . substr( preg_replace( '/[^\d]*/', '', $this->id ), 0, 5 ) . " )";
			} else {
				$orderBy					=	explode( '_', $orderBy );

				$query						.=	"\n ORDER BY a." . $_CB_database->NameQuote( $orderBy[0] ) . ( $orderBy[1] == 'asc' ? " ASC" : ( $orderBy[1] == 'desc' ? " DESC" : null ) );
			}
		}

		$paging								=	( ( ! $hasId ) && $this->get( 'folders_paging_limit', 15, GetterInterface::INT ) );
		$cacheId							=	md5( $query . ( $count ? 'count' : ( $paging ? $this->get( 'folders_paging_limitstart', 0, GetterInterface::INT ) . $this->get( 'folders_paging_limit', 15, GetterInterface::INT ) : null ) ) );

		if ( ( ! isset( $cache[$cacheId] ) ) || ( ( $count && $this->clearFolderCount ) || $this->clearFolderSelect ) ) {
			if ( $count ) {
				$this->clearFolderCount		=	false;

				$_CB_database->setQuery( $query );

				$cache[$cacheId]			=	(int) $_CB_database->loadResult();
			} else {
				$this->clearFolderSelect	=	false;

				if ( $paging ) {
					$_CB_database->setQuery( $query, $this->get( 'folders_paging_limitstart', 0, GetterInterface::INT ), $this->get( 'folders_paging_limit', 15, GetterInterface::INT ) );
				} else {
					$_CB_database->setQuery( $query );
				}

				$rows						=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Gallery\Table\FolderTable', array( $_CB_database ) );
				$rowsCount					=	count( $rows );

				$_PLUGINS->trigger( 'gallery_onLoadFolders', array( &$rows, $this ) );

				if ( $rows ) {
					self::$loadedFolders	=	( self::$loadedFolders + $rows );
				}

				$userIds					=	array();

				/** @var ItemTable[] $rows */
				foreach ( $rows as $row ) {
					$userIds[]				=	$row->get( 'user_id', 0, GetterInterface::INT );
				}

				if ( $userIds ) {
					\CBuser::advanceNoticeOfUsersNeeded( $userIds );
				}

				if ( $paging && $rowsCount && ( count( $rows ) < $rowsCount ) ) {
					$limitCache				=	$this->get( 'folders_paging_limit', 15, GetterInterface::INT );
					$nextLimit				=	( $limitCache - count( $rows ) );

					if ( $nextLimit <= 0 ) {
						$nextLimit			=	1;
					}

					$this->set( 'folders_paging_limitstart', ( $this->get( 'folders_paging_limitstart', 0, GetterInterface::INT ) + $limitCache ) );
					$this->set( 'folders_paging_limit', $nextLimit );

					$cache[$cacheId]		=	( $rows + $this->folders( false ) );

					$this->set( 'folders_paging_limit', $limitCache );
				} else {
					$cache[$cacheId]		=	$rows;
				}
			}
		}

		return $cache[$cacheId];
	}

	/**
	 * Retrieves gallery folder row
	 *
	 * @param int $id
	 * @return FolderTable
	 */
	public function folder( $id )
	{
		if ( ! $id ) {
			return new FolderTable();
		}

		if ( isset( self::$loadedFolders[$id] ) ) {
			return self::$loadedFolders[$id];
		}

		static $cache		=	array();

		if ( ! isset( $cache[$id] ) ) {
			$folders		=	$this->reset()->setId( $id )->folders();

			if ( isset( $folders[$id] ) ) {
				$folder		=	$folders[$id];
			} else {
				$folder		=	new FolderTable();
			}

			$cache[$id]		=	$folder;
		}

		return $cache[$id];
	}

	/**
	 * Retrieves gallery item rows or row count
	 *
	 * @param bool $count
	 * @return ItemTable[]|int
	 */
	public function items( $count = false )
	{
		global $_CB_database, $_PLUGINS;

		static $cache						=	array();

		$id									=	$this->get( 'id', null, GetterInterface::RAW );
		$hasId								=	( ( ( $id !== '' ) && ( $id !== null ) ) || ( is_array( $id ) && $id ) );

		$select								=	array();
		$join								=	array();
		$where								=	array();

		if ( $count ) {
			$select[]						=	'COUNT( a.' . $_CB_database->NameQuote( 'id' ) . ' )';
		} else {
			$select[]						=	'a.*';
		}

		if ( $hasId ) {
			if ( is_array( $this->get( 'id', null, GetterInterface::RAW ) ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $id );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
			}
		}

		$userId								=	$this->get( 'user_id', null, GetterInterface::RAW );

		if ( ( ( $userId !== '' ) && ( $userId !== null ) ) || ( is_array( $userId ) && $userId ) ) {
			if ( is_array( $userId ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $userId );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId;
			}
		}

		if ( $this->asset && ( $this->asset != 'all' ) )  {
			if ( strpos( $this->asset(), 'connections' ) !== false ) {
				$assets						=	array();

				if ( preg_match( '/^profile\.(\d+)\.connections/', $this->asset(), $matches ) ) {
					$profileId				=	(int) $matches[1];
				} else {
					$profileId				=	$this->user->get( 'id', 0, GetterInterface::INT );
				}

				if ( $profileId ) {
					if ( strpos( $this->asset(), 'connectionsonly' ) === false ) {
						$assets[]			=	'profile.' . $profileId;
					}

					foreach( CBGallery::getConnections( $profileId ) as $connection ) {
						$assets[]			=	'profile.' . (int) $connection->id;
					}
				}

				if ( $assets ) {
					$where[]				=	"a." . $_CB_database->NameQuote( 'asset' ) . " IN " . $_CB_database->safeArrayOfStrings( $assets );
				} else {
					if ( $count ) {
						return 0;
					} else {
						return array();
					}
				}
			} else {
				if ( ( strpos( $this->asset, '%' ) !== false ) || ( strpos( $this->asset, '_' ) !== false ) ) {
					$where[]				=	"a." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $this->asset );
				} else {
					$where[]				=	"a." . $_CB_database->NameQuote( 'asset' ) . " = " . $_CB_database->Quote( $this->asset );
				}
			}
		}

		$type								=	$this->get( 'type', null, GetterInterface::RAW );

		if ( $type ) {
			if ( is_array( $type ) ) {
				$allowedTypes				=	array();

				foreach ( $type as $allowedType ) {
					if ( in_array( $allowedType, $this->types() ) ) {
						$allowedTypes[]		=	$allowedType;
					}
				}

				if ( ! $allowedTypes ) {
					if ( $count ) {
						return 0;
					} else {
						return array();
					}
				}

				$where[]					=	"a." . $_CB_database->NameQuote( 'type' ) . " IN " . $_CB_database->safeArrayOfStrings( $allowedTypes );
			} else {
				if ( ! in_array( $type, $this->types() ) ) {
					if ( $count ) {
						return 0;
					} else {
						return array();
					}
				}

				$where[]					=	"a." . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type );
			}
		} else {
			if ( $this->types() && ( count( $this->types() ) < 4 ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'type' ) . " IN " . $_CB_database->safeArrayOfStrings( $this->types() );
			}
		}

		if ( $this->get( 'value', null, GetterInterface::STRING ) != '' ) {
			if ( strpos( $this->get( 'value', null, GetterInterface::STRING ), '%' ) !== false ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( $this->get( 'value', null, GetterInterface::STRING ) );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $this->get( 'value', null, GetterInterface::STRING ) );
			}
		}

		if ( $this->get( 'file', null, GetterInterface::STRING ) != '' ) {
			if ( strpos( $this->get( 'file', null, GetterInterface::STRING ), '%' ) !== false ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'file' ) . " LIKE " . $_CB_database->Quote( $this->get( 'file', null, GetterInterface::STRING ) );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'file' ) . " = " . $_CB_database->Quote( $this->get( 'file', null, GetterInterface::STRING ) );
			}
		}

		if ( $this->get( 'folders', true, GetterInterface::BOOLEAN ) ) {
			$folder							=	$this->get( 'folder', null, GetterInterface::RAW );

			if ( ( ( $folder !== '' ) && ( $folder !== null ) ) || ( is_array( $folder ) && $folder ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'folder' ) . " = " . (int) $folder;
			}
		}

		if ( $this->get( 'title', null, GetterInterface::STRING ) != '' ) {
			if ( strpos( $this->get( 'title', null, GetterInterface::STRING ), '%' ) !== false ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'title' ) . " = " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
			}
		}

		if ( $this->get( 'description', null, GetterInterface::STRING ) != '' ) {
			if ( strpos( $this->get( 'description', null, GetterInterface::STRING ), '%' ) !== false ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( $this->get( 'description', null, GetterInterface::STRING ) );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'description' ) . " = " . $_CB_database->Quote( $this->get( 'description', null, GetterInterface::STRING ) );
			}
		}

		if ( ( ! $hasId ) && $this->get( 'items_search', true, GetterInterface::BOOLEAN ) ) {
			if ( $this->get( 'search', null, GetterInterface::STRING ) != '' ) {
				$where[]					=	"( a." . $_CB_database->NameQuote( 'file' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
											.	" OR a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
											.	" OR a." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
											.	" OR a." . $_CB_database->NameQuote( 'date' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false ) . " )";
			}
		}

		if ( ( $this->get( 'published', null, GetterInterface::RAW ) !== '' ) && ( $this->get( 'published', null, GetterInterface::RAW ) !== null ) ) {
			if ( ( $this->get( 'published', null, GetterInterface::INT ) == 1 ) && Application::MyUser()->getUserId() ) {
				$where[]					=	"( a." . $_CB_database->NameQuote( 'published' ) . " = 1"
											.	" OR a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) Application::MyUser()->getUserId() . " )";
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'published' ) . " = " . $this->get( 'published', null, GetterInterface::INT );
			}
		}

		$_PLUGINS->trigger( 'gallery_onQueryItems', array( $count, &$select, &$join, &$where, &$this ) );

		$query								=	'SELECT ' . implode( ', ', $select )
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_gallery_items' ) . " AS a"
											.	( $join ? "\n " . implode( "\n ", $join ) : null )
											.	( $where ? "\n WHERE " . implode( "\n AND ", $where ) : null );

		if ( ! $count ) {
			$orderBy						=	$this->get( 'items_orderby', 'date_desc', GetterInterface::STRING );

			if ( ! $orderBy ) {
				$orderBy					=	'date_desc';
			}

			if ( $orderBy == 'random' ) {
				$query						.=	"\n ORDER BY RAND( " . substr( preg_replace( '/[^\d]*/', '', $this->id ), 0, 5 ) . " )";
			} else {
				$orderBy					=	explode( '_', $orderBy );

				$query						.=	"\n ORDER BY a." . $_CB_database->NameQuote( $orderBy[0] ) . ( $orderBy[1] == 'asc' ? " ASC" : ( $orderBy[1] == 'desc' ? " DESC" : null ) );
			}
		}

		$paging								=	( ( ! $hasId ) && $this->get( 'items_paging_limit', 15, GetterInterface::INT ) );
		$cacheId							=	md5( $query . ( $count ? 'count' : ( $paging ? $this->get( 'items_paging_limitstart', 0, GetterInterface::INT ) . $this->get( 'items_paging_limit', 15, GetterInterface::INT ) : null ) ) );

		if ( ( ! isset( $cache[$cacheId] ) ) || ( ( $count && $this->clearItemCount ) || $this->clearItemSelect ) ) {
			if ( $count ) {
				$this->clearItemCount		=	false;

				$_CB_database->setQuery( $query );

				$cache[$cacheId]			=	(int) $_CB_database->loadResult();
			} else {
				$this->clearItemSelect		=	false;

				if ( $paging ) {
					$_CB_database->setQuery( $query, $this->get( 'items_paging_limitstart', 0, GetterInterface::INT ), $this->get( 'items_paging_limit', 15, GetterInterface::INT ) );
				} else {
					$_CB_database->setQuery( $query );
				}

				$rows						=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Gallery\Table\ItemTable', array( $_CB_database ) );
				$rowsCount					=	count( $rows );

				$_PLUGINS->trigger( 'gallery_onLoadItems', array( &$rows, $this ) );

				if ( $rows ) {
					self::$loadedItems		=	( self::$loadedItems + $rows );
				}

				$userIds					=	array();

				/** @var ItemTable[] $rows */
				foreach ( $rows as $row ) {
					$userIds[]				=	$row->get( 'user_id', 0, GetterInterface::INT );
				}

				if ( $userIds ) {
					\CBuser::advanceNoticeOfUsersNeeded( $userIds );
				}

				if ( $paging && $rowsCount && ( count( $rows ) < $rowsCount ) ) {
					$limitCache				=	$this->get( 'items_paging_limit', 15, GetterInterface::INT );
					$nextLimit				=	( $limitCache - count( $rows ) );

					if ( $nextLimit <= 0 ) {
						$nextLimit			=	1;
					}

					$this->set( 'items_paging_limitstart', ( $this->get( 'items_paging_limitstart', 0, GetterInterface::INT ) + $limitCache ) );
					$this->set( 'items_paging_limit', $nextLimit );

					$cache[$cacheId]		=	( $rows + $this->items( false ) );

					$this->set( 'items_paging_limit', $limitCache );
				} else {
					$cache[$cacheId]		=	$rows;
				}
			}
		}

		return $cache[$cacheId];
	}

	/**
	 * Retrieves gallery item row
	 *
	 * @param int $id
	 * @return ItemTable
	 */
	public function item( $id )
	{
		if ( ! $id ) {
			return new ItemTable();
		}

		if ( isset( self::$loadedItems[$id] ) ) {
			return self::$loadedItems[$id];
		}

		static $cache		=	array();

		if ( ! isset( $cache[$id] ) ) {
			$items			=	$this->reset()->setId( $id )->items();

			if ( isset( $items[$id] ) ) {
				$item		=	$items[$id];
			} else {
				$item		=	new ItemTable();
			}

			$cache[$id]		=	$item;
		}

		return $cache[$id];
	}

	/**
	 * Outputs gallery HTML
	 *
	 * @return string
	 */
	public function gallery()
	{
		global $_CB_framework, $_PLUGINS;

		static $plugin		=	null;

		if ( ! $plugin ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );
		}

		if ( ! $plugin ) {
			return null;
		}

		if ( ! class_exists( 'CBplug_cbgallery' ) ) {
			$component		=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbgallery/component.cbgallery.php';

			if ( file_exists( $component ) ) {
				include_once( $component );
			}
		}

		$this->cache();

		ob_start();
		$pluginTab			=	null;
		$pluginData			=	array( 'gallery' => $this );
		$pluginArguements	=	array( &$pluginTab, &$this->user, ( Application::Cms()->getClientId() ? 2 : 1 ), &$pluginData );

		$_PLUGINS->call( $plugin->id, 'getCBpluginComponent', 'CBplug_cbgallery', $pluginArguements, $pluginTab );
		$return				=	ob_get_contents();
		ob_end_clean();

		return $return;
	}

	/**
	 * Returns an array of the galleries variables
	 *
	 * @return array
	 */
	public function asArray()
	{
		$params				=	parent::asArray();

		if ( isset( $params['folders_paging_limitstart'] ) ) {
			unset( $params['folders_paging_limitstart'] );
		}

		if ( isset( $params['items_paging_limitstart'] ) ) {
			unset( $params['items_paging_limitstart'] );
		}

		if ( isset( $params['search'] ) ) {
			unset( $params['search'] );
		}

		$params['asset']	=	$this->asset;
		$params['user']		=	$this->user->get( 'id', 0, GetterInterface::INT );

		return $params;
	}

	/**
	 * Caches the gallery into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless gallery parameters have changed after creation and desired result is for them to persist
	 *
	 * @return self
	 */
	public function cache()
	{
		$newId				=	md5( self::asJson() );

		if ( $this->id != $newId ) {
			$session		=	Application::Session();
			$galleries		=	$session->subTree( 'gallery' );

			if ( $this->id ) {
				$galleries->unsetEntry( $this->id );
			}

			$this->id		=	$newId;
			$this->ini		=	$this->asArray();

			$galleries->set( $this->id, $this->ini );

			$session->set( 'gallery', $galleries->asArray() );
		}

		return $this;
	}
}