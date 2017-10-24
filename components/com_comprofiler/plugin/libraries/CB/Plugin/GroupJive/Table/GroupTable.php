<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive\Table;

use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Application\Application;
use CBLib\Database\Table\OrderedTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

class GroupTable extends OrderedTable
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var int  */
	public $category		=	null;
	/** @var string  */
	public $canvas			=	null;
	/** @var string  */
	public $logo			=	null;
	/** @var string  */
	public $name			=	null;
	/** @var string  */
	public $description		=	null;
	/** @var int  */
	public $type			=	null;
	/** @var string  */
	public $css				=	null;
	/** @var string  */
	public $date			=	null;
	/** @var int  */
	public $published		=	null;
	/** @var int  */
	public $ordering		=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_input		=	null;
	/** @var Registry  */
	protected $_files		=	null;
	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__groupjive_groups';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * Ordering keys and for each their ordering groups.
	 * E.g.; array( 'ordering' => array( 'tab' ), 'ordering_registration' => array() )
	 * @var array
	 */
	protected $_orderings	=	array( 'ordering' => array( 'category' ) );

	/**
	 * @return bool
	 */
	public function check()
	{
		global $_PLUGINS;

		static $params		=	null;

		if ( ! $params ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params			=	$_PLUGINS->getPluginParams( $plugin );
		}

		$clientId			=	Application::Cms()->getClientId();
		$isModerator		=	CBGroupJive::isModerator();

		if ( $this->get( 'name' ) == '' ) {
			$this->setError( CBTxt::T( 'Name not specified!' ) );

			return false;
		} elseif ( $this->get( 'user_id' ) == '' ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		} elseif ( ( (int) $this->get( 'category' ) == 0 ) && ( ( ! $params->get( 'groups_uncategorized', 1 ) ) && ( ! $clientId ) && ( ! $isModerator ) ) ) {
			$this->setError( CBTxt::T( 'Category not specified!' ) );

			return false;
		} elseif ( $this->get( 'type' ) == '' ) {
			$this->setError( CBTxt::T( 'Type not specified!' ) );

			return false;
		} elseif ( $this->get( 'category' ) ) {
			$category		=	$this->category();

			if ( ! $category->get( 'id' ) ) {
				$this->setError( CBTxt::T( 'Category does not exist!' ) );

				return false;
			} else {
				$types		=	explode( '|*|', $category->get( 'types' ) );

				if ( ( ! in_array( $this->get( 'type' ), $types ) ) && ( ! $clientId ) && ( ! $isModerator ) && $types ) {
					$this->setError( CBTxt::T( 'Type not permitted!' ) );

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_CB_framework, $_PLUGINS;

		$new				=	( $this->get( 'id' ) ? false : true );
		$old				=	new self();

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime() ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'gj_onBeforeUpdateGroup', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onBeforeCreateGroup', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! CBGroupJive::uploadImage( 'canvas', $this ) ) {
			return false;
		}

		if ( ! CBGroupJive::uploadImage( 'logo', $this ) ) {
			return false;
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		// If the new owner doesn't match the previous then demote (frontend) or delete (backend) the previous:
		if ( $old->get( 'id' ) ) {
			if ( $old->get( 'user_id' ) != $this->get( 'user_id' ) ) {
				$previousUser	=	new UserTable();

				$previousUser->load( array( 'user_id' => (int) $old->get( 'user_id' ), 'group' => (int) $this->get( 'id' ) ) );

				if ( $previousUser->get( 'id' ) ) {
					if ( Application::Cms()->getClientId() ) {
						if ( ! $previousUser->delete() ) {
							$this->setError( $previousUser->getError() );

							return false;
						}
					} else {
						$previousUser->set( 'status', 1 );

						if ( ! $previousUser->store() ) {
							$this->setError( $previousUser->getError() );

							return false;
						}
					}
				}
			}
		}

		$user				=	new UserTable();

		$user->load( array( 'user_id' => (int) $this->get( 'user_id' ), 'group' => (int) $this->get( 'id' ) ) );

		// If the owner doesn't exist or isn't marked owner then create them or promote them to owner:
		if ( ( ! $user->get( 'id' ) ) || ( $user->get( 'status' ) != 4 ) ) {
			$user->set( 'user_id', (int) $this->get( 'user_id' ) );
			$user->set( 'group', (int) $this->get( 'id' ) );
			$user->set( 'status', 4 );

			if ( $user->getError() || ( ! $user->store() ) ) {
				$this->setError( $user->getError() );

				return false;
			}
		}

		// If the category is changed be sure to move the canvas and logo as needed:
		if ( $old->get( 'id' ) && ( $this->get( 'canvas' ) || $this->get( 'logo' ) ) && ( $old->get( 'category' ) != $this->get( 'category' ) ) ) {
			$basePath		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgroupjive';
			$oldPath		=	$basePath . '/' . (int) $old->get( 'category' ) . '/' . (int) $this->get( 'id' );
			$newPath		=	$basePath . '/' . (int) $this->get( 'category' ) . '/' . (int) $this->get( 'id' );

			if ( is_dir( $oldPath ) ) {
				CBGroupJive::createDirectory( $basePath, $this->get( 'category' ), $this->get( 'id' ) );
				CBGroupJive::copyDirectory( $oldPath, $newPath, true );
			}
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gj_onAfterUpdateGroup', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onAfterCreateGroup', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|self $object
	 * @return self|bool
	 */
	public function copy( $object = null )
	{
		global $_CB_framework, $_PLUGINS;

		if ( $object === null ) {
			$object			=	clone $this;
		}

		$old				=	new self();

		$old->load( (int) $object->get( 'id' ) );

		$_PLUGINS->trigger( 'gj_onBeforeCopyGroup', array( &$object, $old ) );

		$copy				=	parent::copy( $object );

		if ( ! $copy ) {
			return false;
		}

		// Copy the canvas and logo:
		if ( $object->get( 'canvas' ) || $object->get( 'logo' ) ) {
			$basePath		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgroupjive';
			$oldPath		=	$basePath . '/' . (int) $old->get( 'category' ) . '/' . (int) $old->get( 'id' );
			$newPath		=	$basePath . '/' . (int) $object->get( 'category' ) . '/' . (int) $object->get( 'id' );

			if ( is_dir( $oldPath ) ) {
				CBGroupJive::createDirectory( $basePath, $object->get( 'category' ), $object->get( 'id' ) );
				CBGroupJive::copyDirectory( $oldPath, $newPath );
			}
		}

		$_PLUGINS->trigger( 'gj_onAfterCopyGroup', array( $object, $old ) );

		return $copy;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_CB_framework, $_PLUGINS;

		$_PLUGINS->trigger( 'gj_onBeforeDeleteGroup', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Delete users in this group:
		$query				=	'SELECT *'
							.	"\n FROM " . $this->getDbo()->NameQuote( '#__groupjive_users' )
							.	"\n WHERE " . $this->getDbo()->NameQuote( 'group' ) . " = " . (int) $this->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$users				=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\GroupJive\Table\UserTable', array( $this->getDbo() ) );

		/** @var UserTable[] $users */
		foreach ( $users as $user ) {
			$user->delete();
		}

		// Delete invites in this group:
		$query				=	'SELECT *'
							.	"\n FROM " . $this->getDbo()->NameQuote( '#__groupjive_invites' )
							.	"\n WHERE " . $this->getDbo()->NameQuote( 'group' ) . " = " . (int) $this->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$invites			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\GroupJive\Table\InviteTable', array( $this->getDbo() ) );

		/** @var InviteTable[] $invites */
		foreach ( $invites as $invite ) {
			$invite->delete();
		}

		// Delete canvas and logo:
		if ( $this->get( 'canvas' ) || $this->get( 'logo' ) ) {
			CBGroupJive::deleteDirectory( $_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgroupjive/' . (int) $this->get( 'category' ) . '/' . (int) $this->get( 'id' ) );
		}

		$_PLUGINS->trigger( 'gj_onAfterDeleteGroup', array( $this ) );

		return true;
	}

	/**
	 * @return Registry
	 */
	public function params()
	{
		if ( ! ( $this->get( '_params' ) instanceof Registry ) ) {
			$this->set( '_params', new Registry( $this->get( 'params' ) ) );
		}

		return $this->get( '_params' );
	}

	/**
	 * @return CategoryTable
	 */
	public function category()
	{
		return CBGroupJive::getCategory( (int) $this->get( 'category' ) );
	}

	/**
	 * @return string
	 */
	public function type()
	{
		static $cache				=	array();

		$id							=	(int) $this->get( 'type' );

		if ( ! isset( $cache[$id] ) ) {
			switch ( $id ) {
				case 1:
					$cache[$id]		=	CBTxt::T( 'Open' );
					break;
				case 2:
					$cache[$id]		=	CBTxt::T( 'Approval' );
					break;
				case 3:
					$cache[$id]		=	CBTxt::T( 'Invite' );
					break;
				default:
					$cache[$id]		=	CBTxt::T( 'Unknown' );
					break;
			}
		}

		return $cache[$id];
	}

	/**
	 * @param bool        $thumbnail
	 * @param bool        $html
	 * @param null|string $classes
	 * @return string
	 */
	public function canvas( $thumbnail = false, $html = true, $classes = null )
	{
		global $_CB_framework, $_PLUGINS;

		static $params		=	null;

		if ( ! $params ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params			=	$_PLUGINS->getPluginParams( $plugin );
		}

		static $cache		=	array();

		$id					=	$this->get( 'id' ) . $thumbnail;

		if ( ! isset( $cache[$id] ) ) {
			$default		=	$params->get( 'groups_canvas', 'canvas.png' );

			if ( ! $default ) {
				$default	=	'canvas.png';
			}

			$image			=	null;

			if ( $this->get( 'canvas' ) ) {
				$path		=	'/images/comprofiler/plug_cbgroupjive/' . (int) $this->get( 'category' ) . '/' . (int) $this->get( 'id' ) . '/' . ( $thumbnail ? 'tn' : null ) . preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'canvas' ) );

				if ( file_exists( $_CB_framework->getCfg( 'absolute_path' ) . $path ) ) {
					$image	=	$_CB_framework->getCfg( 'live_site' ) . $path;
				}
			}

			if ( ! $image ) {
				$path		=	'/components/com_comprofiler/plugin/user/plug_cbgroupjive/templates/' . $params->get( 'general_template', 'default' ) . '/images/' . $default;

				if ( ! file_exists( $_CB_framework->getCfg( 'absolute_path' ) . $path ) ) {
					$path	=	'/components/com_comprofiler/plugin/user/plug_cbgroupjive/templates/default/images/' . $default;
				}

				$image		=	$_CB_framework->getCfg( 'live_site' ) . $path;
			}

			$cache[$id]		=	$image;
		}

		$canvas				=	$cache[$id];

		if ( $canvas && $html ) {
			$canvas			=	'<div style="background-image: url(' . htmlspecialchars( $canvas ) . ')" class="cbImgCanvas' . ( $thumbnail ? ' cbThumbCanvas' : ' cbFullCanvas' ) . ' gjCanvas' . ( $this->get( 'canvas' ) ? ' gjCanvasCustom' : ' gjCanvasDefault' ) . ( $classes ? ' ' . htmlspecialchars( $classes ) : null ) . '"></div>';
		}

		return $canvas;
	}

	/**
	 * @param bool        $thumbnail
	 * @param bool        $html
	 * @param bool        $linked
	 * @param null|string $classes
	 * @return string
	 */
	public function logo( $thumbnail = false, $html = true, $linked = false, $classes = null )
	{
		global $_CB_framework, $_PLUGINS;

		static $plugin		=	null;
		static $params		=	null;

		if ( ! $params ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
			$params			=	$_PLUGINS->getPluginParams( $plugin );
		}

		static $cache		=	array();

		$id					=	$this->get( 'id' ) . $thumbnail;

		if ( ! isset( $cache[$id] ) ) {
			$default		=	$params->get( 'groups_logo', 'logo.png' );

			if ( ! $default ) {
				$default	=	'logo.png';
			}

			$image			=	null;

			if ( $this->get( 'logo' ) ) {
				$path		=	'/images/comprofiler/plug_cbgroupjive/' . (int) $this->get( 'category' ) . '/' . (int) $this->get( 'id' ) . '/' . ( $thumbnail ? 'tn' : null ) . preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'logo' ) );

				if ( file_exists( $_CB_framework->getCfg( 'absolute_path' ) . $path ) ) {
					$image	=	$_CB_framework->getCfg( 'live_site' ) . $path;
				}
			}

			if ( ! $image ) {
				$path		=	'/components/com_comprofiler/plugin/user/plug_cbgroupjive/templates/' . $params->get( 'general_template', 'default' ) . '/images/' . $default;

				if ( ! file_exists( $_CB_framework->getCfg( 'absolute_path' ) . $path ) ) {
					$path	=	'/components/com_comprofiler/plugin/user/plug_cbgroupjive/templates/default/images/' . $default;
				}

				$image		=	$_CB_framework->getCfg( 'live_site' ) . $path;
			}

			$cache[$id]		=	$image;
		}

		$logo				=	$cache[$id];

		if ( $logo ) {
			if ( $html ) {
				$logo		=	'<img alt="' . htmlspecialchars( CBTxt::T( 'Logo' ) ) . '" src="' . htmlspecialchars( $logo ) . '" class="cbImgPict' . ( $thumbnail ? ' cbThumbPict' : ' cbFullPict' ) . ' gjLogo' . ( $this->get( 'logo' ) ? ' gjLogoCustom' : ' gjLogoDefault' ) . ( $classes ? ' ' . htmlspecialchars( $classes ) : null ) . ' img-thumbnail" />';
			}

			if ( $linked ) {
				$logo		=	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $this->get( 'id' ) ) ) . '">' . $logo . '</a>';
			}
		}

		return $logo;
	}
}