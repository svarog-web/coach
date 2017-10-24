<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveVideo\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\CBGroupJive;
use GuzzleHttp;
use Exception;

defined('CBLIB') or die();

class VideoTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var int  */
	public $group			=	null;
	/** @var string  */
	public $title			=	null;
	/** @var string  */
	public $url				=	null;
	/** @var string  */
	public $caption			=	null;
	/** @var string  */
	public $date			=	null;
	/** @var int  */
	public $published		=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__groupjive_plugin_video';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( $this->get( 'user_id' ) == '' ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		} elseif ( $this->get( 'url' ) == '' ) {
			$this->setError( CBTxt::T( 'URL not specified!' ) );

			return false;
		} elseif ( $this->get( 'group' ) == '' ) {
			$this->setError( CBTxt::T( 'Group not specified!' ) );

			return false;
		} elseif ( ! $this->group()->get( 'id' ) ) {
			$this->setError( CBTxt::T( 'Group does not exist!' ) );

			return false;
		} else {
			if ( $this->domain() && ( ! in_array( $this->domain(), array( 'youtube', 'youtu' ) ) ) ) {
				if ( ! $this->exists() ) {
					$this->setError( CBTxt::T( 'GROUP_VIDEO_INVALID_URL', 'Invalid URL. Please ensure the URL exists!' ) );

					return false;
				}

				$extensions		=	array( 'youtube', 'mp4', 'ogv', 'ogg', 'webm', 'm4v' );

				if ( ( ! $this->extension() ) || ( ! in_array( $this->extension(), $extensions ) ) ) {
					$this->setError( CBTxt::T( 'GROUP_VIDEO_INVALID_EXT', 'Invalid url extension [ext]. Please link only [exts]!', array( '[ext]' => $this->extension(), '[exts]' => implode( ', ', $extensions ) ) ) );

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
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime() ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'gj_onBeforeUpdateVideo', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onBeforeCreateVideo', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gj_onAfterUpdateVideo', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onAfterCreateVideo', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'gj_onBeforeDeleteVideo', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'gj_onAfterDeleteVideo', array( $this ) );

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
	 * @return GroupTable
	 */
	public function group()
	{
		return CBGroupJive::getGroup( (int) $this->get( 'group' ) );
	}

	/**
	 * Returns the clean domain to the video
	 *
	 * @return string
	 */
	private function domain()
	{
		static $cache		=	array();

		$id					=	$this->get( 'url' );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $id, PHP_URL_HOST ) );
		}

		return $cache[$id];
	}

	/**
	 * Checks if the video exists
	 *
	 * @return bool
	 */
	public function exists()
	{
		if ( in_array( $this->domain(), array( 'youtube', 'youtu' ) ) ) {
			return true;
		}

		static $cache			=	array();

		$id						=	$this->get( 'url' );

		if ( ! isset( $cache[$id] ) ) {
			$exists				=	false;

			try {
				$request		=	new GuzzleHttp\Client();

				$header			=	$request->head( $id );

				if ( ( $header !== false ) && ( $header->getStatusCode() == 200 ) ) {
					$exists		=	true;
				}
			} catch( Exception $e ) {}

			$cache[$id]			=	$exists;
		}

		return $cache[$id];
	}

	/**
	 * Returns the video size raw or formatted to largest increment possible
	 *
	 * @param bool $raw
	 * @return string|int
	 */
	public function size( $raw = false )
	{
		if ( in_array( $this->domain(), array( 'youtube', 'youtu' ) ) ) {
			return 0;
		}

		static $cache				=	array();

		$id							=	$this->get( 'url' );

		if ( ! isset( $cache[$id] ) ) {
			$fileSize				=	0;

			if ( $this->exists() ) {
				try {
					$request		=	new GuzzleHttp\Client();

					$header			=	$request->head( $id );

					if ( ( $header !== false ) && ( $header->getStatusCode() == 200 ) ) {
						$fileSize	=	(int) $header->getHeader( 'Content-Length' );
					}
				} catch( Exception $e ) {}
			}

			$cache[$id]				=	$fileSize;
		}

		if ( ! $raw ) {
			return CBGroupJive::getFormattedFileSize( $cache[$id] );
		}

		return $cache[$id];
	}

	/**
	 * Returns the video extension
	 *
	 * @return string|null
	 */
	public function extension()
	{
		if ( in_array( $this->domain(), array( 'youtube', 'youtu' ) ) ) {
			return null;
		}

		static $cache		=	array();

		$id					=	$this->get( 'url' );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	strtolower( pathinfo( preg_replace( '/[^-a-zA-Z0-9_.]/', '', $id ), PATHINFO_EXTENSION ) );
		}

		return $cache[$id];
	}

	/**
	 * Returns the video mimetype from extension
	 *
	 * @return string
	 */
	public function mimeType()
	{
		if ( in_array( $this->domain(), array( 'youtube', 'youtu' ) ) ) {
			return 'video/youtube';
		}

		static $cache		=	array();

		$id					=	$this->extension();

		if ( $id == 'm4v' ) {
			return 'video/mp4';
		}

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	cbGetMimeFromExt( $id );
		}

		return $cache[$id];
	}

	/**
	 * Returns the video name cleaned of the unique id
	 *
	 * @return string
	 */
	public function name()
	{
		static $cache			=	array();

		$id						=	$this->get( 'url' );

		if ( ! isset( $cache[$id] ) ) {
			if ( in_array( $this->domain(), array( 'youtube', 'youtu' ) ) ) {
				$name			=	preg_replace( '%^.*(?:v=|v/|/)([\w-]+).*%i', '$1', $id );
			} else {
				$name			=	pathinfo( $id, PATHINFO_FILENAME ) . '.' . $this->extension();
			}

			$cache[$id]			=	$name;
		}

		return $cache[$id];
	}
}