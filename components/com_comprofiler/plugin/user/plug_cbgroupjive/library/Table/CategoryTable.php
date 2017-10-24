<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive\Table;

use CBLib\Database\Table\OrderedTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CB\Plugin\GroupJive\CBGroupJive;

defined('CBLIB') or die();

class CategoryTable extends OrderedTable
{
	/** @var int  */
	public $id				=	null;
	/** @var string  */
	public $canvas			=	null;
	/** @var string  */
	public $logo			=	null;
	/** @var string  */
	public $name			=	null;
	/** @var string  */
	public $description		=	null;
	/** @var string  */
	public $types			=	null;
	/** @var int  */
	public $access			=	null;
	/** @var int  */
	public $create_access	=	null;
	/** @var string  */
	public $css				=	null;
	/** @var int  */
	public $published		=	null;
	/** @var int  */
	public $ordering		=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__groupjive_categories';

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
	protected $_orderings	=	array( 'ordering' );

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( $this->get( 'name' ) == '' ) {
			$this->setError( CBTxt::T( 'Name not specified!' ) );

			return false;
		} elseif ( $this->get( 'types' ) == '' ) {
			$this->setError( CBTxt::T( 'Types not specified!' ) );

			return false;
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'gj_onBeforeUpdateCategory', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onBeforeCreateCategory', array( &$this ) );
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

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gj_onAfterUpdateCategory', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onAfterCreateCategory', array( $this ) );
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

		$_PLUGINS->trigger( 'gj_onBeforeCopyCategory', array( &$object, $old ) );

		$copy				=	parent::copy( $object );

		if ( ! $copy ) {
			return false;
		}

		// Copy the groups in this category:
		$query				=	'SELECT *'
							.	"\n FROM " . $this->getDbo()->NameQuote( '#__groupjive_groups' )
							.	"\n WHERE " . $this->getDbo()->NameQuote( 'category' ) . " = " . (int) $old->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$groups				=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $this->getDbo() ) );

		/** @var GroupTable[] $groups */
		foreach ( $groups as $group ) {
			$group->set( 'category', (int) $object->get( 'id' ) );

			$group->copy();
		}

		// Copy the canvas and logo:
		if ( $object->get( 'canvas' ) || $object->get( 'logo' ) ) {
			$basePath		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgroupjive';
			$oldPath		=	$basePath . '/' . (int) $old->get( 'id' );
			$newPath		=	$basePath . '/' . (int) $object->get( 'id' );

			if ( is_dir( $oldPath ) ) {
				CBGroupJive::createDirectory( $basePath, $object->get( 'id' ) );
				CBGroupJive::copyDirectory( $oldPath, $newPath );
			}
		}

		$_PLUGINS->trigger( 'gj_onAfterCopyCategory', array( $object, $old ) );

		return $copy;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_CB_framework, $_PLUGINS;

		$_PLUGINS->trigger( 'gj_onBeforeDeleteCategory', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Delete groups in this category:
		$query				=	'SELECT *'
							.	"\n FROM " . $this->getDbo()->NameQuote( '#__groupjive_groups' )
							.	"\n WHERE " . $this->getDbo()->NameQuote( 'category' ) . " = " . (int) $this->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$groups				=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $this->getDbo() ) );

		/** @var GroupTable[] $groups */
		foreach ( $groups as $group ) {
			$group->delete();
		}

		// Delete canvas and logo:
		if ( $this->get( 'canvas' ) || $this->get( 'logo' ) ) {
			CBGroupJive::deleteDirectory( $_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgroupjive/' . (int) $this->get( 'id' ) );
		}

		$_PLUGINS->trigger( 'gj_onAfterDeleteCategory', array( $this ) );

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
			$default		=	$params->get( 'categories_canvas', 'canvas.png' );

			if ( ! $default ) {
				$default	=	'canvas.png';
			}

			$image			=	null;

			if ( $this->get( 'canvas' ) ) {
				$path		=	'/images/comprofiler/plug_cbgroupjive/' . (int) $this->get( 'id' ) . '/' . ( $thumbnail ? 'tn' : null ) . preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'canvas' ) );

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
			$default		=	$params->get( 'categories_logo', 'logo.png' );

			if ( ! $default ) {
				$default	=	'logo.png';
			}

			$image			=	null;

			if ( $this->get( 'logo' ) ) {
				$path		=	'/images/comprofiler/plug_cbgroupjive/' . (int) $this->get( 'id' ) . '/' . ( $thumbnail ? 'tn' : null ) . preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'logo' ) );

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
				$logo		=	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $this->get( 'id' ) ) ) . '">' . $logo . '</a>';
			}
		}

		return $logo;
	}
}