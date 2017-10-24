<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJivePhoto\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Input\Get;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Image\Image;
use Exception;

defined('CBLIB') or die();

class PhotoTable extends Table
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
	public $image			=	null;
	/** @var string  */
	public $filename		=	null;
	/** @var string  */
	public $description		=	null;
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
	protected $_tbl			=	'#__groupjive_plugin_photo';

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
		global $_PLUGINS;

		if ( $this->get( 'user_id' ) == '' ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		} elseif ( $this->get( 'group' ) == '' ) {
			$this->setError( CBTxt::T( 'Group not specified!' ) );

			return false;
		} elseif ( ! $this->group()->get( 'id' ) ) {
			$this->setError( CBTxt::T( 'Group does not exist!' ) );

			return false;
		} elseif ( isset( $_FILES['image']['tmp_name'] ) && ( ! empty( $_FILES['image']['tmp_name'] ) ) ) {
			static $params		=	null;

			if ( ! $params ) {
				$plugin			=	$_PLUGINS->getLoadedPlugin( 'user/plug_cbgroupjive/plugins', 'cbgroupjivephoto' );
				$params			=	$_PLUGINS->getPluginParams( $plugin );
			}

			$minFileSize		=	$params->get( 'groups_photo_min_size', 0 );
			$maxFileSize		=	$params->get( 'groups_photo_max_size', 1024 );
			$extensions			=	array( 'jpg', 'jpeg', 'gif', 'png' );

			$fileExtension		=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $_FILES['image']['name'], PATHINFO_EXTENSION ) ) );

			if ( ( ! $fileExtension ) || ( ! in_array( $fileExtension, $extensions ) ) ) {
				$this->setError( CBTxt::T( 'GROUP_PHOTO_UPLOAD_INVALID_EXT', 'Invalid photo extension [ext]. Please upload only [exts]!', array( '[ext]' => $fileExtension, '[exts]' => implode( ', ', $extensions ) ) ) );

				return false;
			}

			$fileSize			=	$_FILES['image']['size'];

			if ( $minFileSize && ( ( $fileSize / 1024 ) < $minFileSize ) ) {
				$this->setError( CBTxt::T( 'GROUP_PHOTO_UPLOAD_TOO_SMALL', 'The photo is too small, the minimum is [size]!', array( '[size]' => CBGroupJive::getFormattedFileSize( $minFileSize * 1024 ) ) ) );

				return false;
			}

			if ( $maxFileSize && ( ( $fileSize / 1024 ) > $maxFileSize ) ) {
				$this->setError( CBTxt::T( 'GROUP_PHOTO_UPLOAD_TOO_LARGE', 'The photo size exceeds the maximum of [size]!', array( '[size]' => CBGroupJive::getFormattedFileSize( $maxFileSize * 1024 ) ) ) );

				return false;
			}
		} elseif ( $this->get( 'image' ) == '' ) {
			$this->setError( CBTxt::T( 'Photo not specified!' ) );

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
		global $_CB_framework, $_PLUGINS;

		$new						=	( $this->get( 'id' ) ? false : true );
		$old						=	new self();

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime() ) );

		if ( isset( $_FILES['image']['tmp_name'] ) && ( ! empty( $_FILES['image']['tmp_name'] ) ) ) {
			static $params		=	null;

			if ( ! $params ) {
				$plugin				=	$_PLUGINS->getLoadedPlugin( 'user/plug_cbgroupjive/plugins', 'cbgroupjivephoto' );
				$params				=	$_PLUGINS->getPluginParams( $plugin );
			}

			$basePath				=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivephoto';
			$filePath				=	$basePath . '/' . (int) $this->group()->get( 'category' ) . '/' . (int) $this->group()->get( 'id' );

			CBGroupJive::createDirectory( $basePath, $this->group()->get( 'category' ), $this->group()->get( 'id' ) );

			$resample				=	$params->get( 'groups_photo_resample', 1 );
			$aspectRatio			=	$params->get( 'groups_photo_maintain_aspect_ratio', 1 );
			$imageHeight			=	(int) $params->get( 'groups_photo_image_height', 640 );

			if ( ! $imageHeight ) {
				$imageHeight		=	640;
			}

			$imageWidth				=	(int) $params->get( 'groups_photo_image_width', 1280 );

			if ( ! $imageWidth ) {
				$imageWidth			=	1280;
			}

			$thumbHeight			=	(int) $params->get( 'groups_photo_thumbnail_height', 320 );

			if ( ! $thumbHeight ) {
				$thumbHeight		=	320;
			}

			$thumbWidth				=	(int) $params->get( 'groups_photo_thumbnail_width', 640 );

			if ( ! $thumbWidth ) {
				$thumbWidth			=	640;
			}

			$conversionType			=	(int) Application::Config()->get( 'conversiontype', 0 );
			$imageSoftware			=	( $conversionType == 5 ? 'gmagick' : ( $conversionType == 1 ? 'imagick' : ( $conversionType == 4 ? 'gd' : 'auto' ) ) );

			$fileExtension			=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $_FILES['image']['name'], PATHINFO_EXTENSION ) ) );
			$fileName				=	Get::clean( pathinfo( $_FILES['image']['name'], PATHINFO_FILENAME ), GetterInterface::STRING ) . '.' . $fileExtension;
			$fileId					=	uniqid();

			try {
				$image				=	new Image( $imageSoftware, $resample, $aspectRatio );

				$image->setName( $fileId );
				$image->setSource( $_FILES['image'] );
				$image->setDestination( $filePath . '/' );

				$image->processImage( $imageWidth, $imageHeight );

				$newFileName		=	$image->getCleanFilename();

				$image->setName( 'tn' . $fileId );

				$image->processImage( $thumbWidth, $thumbHeight );

				if ( $this->get( 'image' ) ) {
					if ( file_exists( $filePath . '/' . $this->get( 'image' ) ) ) {
						@unlink( $filePath . '/' . $this->get( 'image' ) );
					}

					if ( file_exists( $filePath . '/tn' . $this->get( 'image' ) ) ) {
						@unlink( $filePath . '/tn' . $this->get( 'image' ) );
					}
				}

				$this->set( 'image', $newFileName );
				$this->set( 'filename', $fileName );

				$params				=	$this->params();

				$params->unsetEntry( 'name' );
				$params->unsetEntry( 'extension' );
				$params->unsetEntry( 'mimetype' );
				$params->unsetEntry( 'filesize' );
				$params->unsetEntry( 'filesize_thumbnail' );
				$params->unsetEntry( 'height' );
				$params->unsetEntry( 'width' );
				$params->unsetEntry( 'height_thumbnail' );
				$params->unsetEntry( 'width_thumbnail' );

				$params->set( 'name', $this->name() );
				$params->set( 'extension', $this->extension() );
				$params->set( 'mimetype', $this->mimeType() );
				$params->set( 'filesize', $this->size( true ) );
				$params->set( 'filesize_thumbnail', $this->size( true, true ) );
				$params->set( 'height', $this->height() );
				$params->set( 'width', $this->width() );
				$params->set( 'height_thumbnail', $this->height( true ) );
				$params->set( 'width_thumbnail', $this->width( true ) );

				$this->set( 'params', $params->asJson() );
			} catch ( Exception $e ) {
				$this->setError( $e->getMessage() );

				return false;
			}
		} elseif ( ! $this->get( 'filename' ) ) {
			$this->set( 'filename', $this->get( 'image' ) );
		}

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'gj_onBeforeUpdatePhoto', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onBeforeCreatePhoto', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( $old->get( 'id' ) && $this->get( 'image' ) && ( $old->get( 'group' ) != $this->get( 'group' ) ) ) {
			$basePath				=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivephoto';
			$oldPath				=	$basePath . '/' . (int) $old->group()->get( 'category' ) . '/' . (int) $old->group()->get( 'id' );
			$newPath				=	$basePath . '/' . (int) $this->group()->get( 'category' ) . '/' . (int) $this->group()->get( 'id' );

			if ( is_dir( $oldPath ) ) {
				CBGroupJive::createDirectory( $basePath, $this->group()->get( 'category' ), $this->group()->get( 'id' ) );

				if ( file_exists( $oldPath . '/' . $this->get( 'image' ) ) ) {
					@rename( $oldPath . '/' . $this->get( 'image' ), $newPath . '/' . $this->get( 'image' ) );
				}

				if ( file_exists( $oldPath . '/tn' . $this->get( 'image' ) ) ) {
					@rename( $oldPath . '/tn' . $this->get( 'image' ), $newPath . '/tn' . $this->get( 'image' ) );
				}
			}
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gj_onAfterUpdatePhoto', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onAfterCreatePhoto', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_CB_framework, $_PLUGINS;

		$_PLUGINS->trigger( 'gj_onBeforeDeletePhoto', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		if ( $this->get( 'image' ) ) {
			$basePath		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivephoto';
			$filePath		=	$basePath . '/' . (int) $this->group()->get( 'category' ) . '/' . (int) $this->group()->get( 'id' );

			if ( file_exists( $filePath . '/' . $this->get( 'image' ) ) ) {
				@unlink( $filePath . '/' . $this->get( 'image' ) );
			}

			if ( file_exists( $filePath . '/tn' . $this->get( 'image' ) ) ) {
				@unlink( $filePath . '/tn' . $this->get( 'image' ) );
			}
		}

		$_PLUGINS->trigger( 'gj_onAfterDeletePhoto', array( $this ) );

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
	 * Returns the clean absolute path to the image
	 *
	 * @param bool $thumbnail
	 * @return mixed
	 */
	public function path( $thumbnail = false )
	{
		global $_CB_framework;

		static $cache					=	array();

		$id								=	$this->get( 'image' );

		if ( ! isset( $cache[$id][$thumbnail] ) ) {
			$cache[$id][$thumbnail]		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivephoto/' . (int) $this->group()->get( 'category' ) . '/' . (int) $this->group()->get( 'id' ) . '/' . ( $thumbnail ? 'tn' : null ) . preg_replace( '/[^-a-zA-Z0-9_.]/', '', $id );
		}

		return $cache[$id][$thumbnail];
	}

	/**
	 * Checks if the image exists
	 *
	 * @param bool $thumbnail
	 * @return bool
	 */
	public function exists( $thumbnail = false )
	{
		static $cache		=	array();

		$id					=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	@file_exists( $id );
		}

		return $cache[$id];
	}

	/**
	 * Returns the image size raw or formatted to largest increment possible
	 *
	 * @param bool $raw
	 * @param bool $thumbnail
	 * @return string|int
	 */
	public function size( $raw = false, $thumbnail = false )
	{
		static $cache			=	array();

		$id						=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$fileSize			=	(int) $this->params()->get( ( $thumbnail ? 'filesize_thumbnail' : 'filesize' ), 0 );

			if ( ( ! $fileSize ) && $this->exists( $thumbnail ) ) {
				$fileSize		=	@filesize( $id );
			}

			$cache[$id]			=	$fileSize;
		}

		if ( ! $raw ) {
			return CBGroupJive::getFormattedFileSize( $cache[$id] );
		}

		return $cache[$id];
	}

	/**
	 * Returns the file extension
	 *
	 * @return string|null
	 */
	public function extension()
	{
		static $cache		=	array();

		$id					=	$this->path();

		if ( ! isset( $cache[$id] ) ) {
			$extension		=	$this->params()->get( 'extension' );

			if ( ! $extension ) {
				$extension	=	strtolower( pathinfo( preg_replace( '/[^-a-zA-Z0-9_.]/', '', $id ), PATHINFO_EXTENSION ) );
			}

			$cache[$id]		=	$extension;
		}

		return $cache[$id];
	}

	/**
	 * Returns the file mimetype from extension
	 *
	 * @return string
	 */
	public function mimeType()
	{

		static $cache		=	array();

		$id					=	$this->extension();

		if ( ! isset( $cache[$id] ) ) {
			$mimeType		=	$this->params()->get( 'mimetype' );

			if ( ! $mimeType ) {
				$mimeType	=	cbGetMimeFromExt( $id );
			}

			$cache[$id]		=	$mimeType;
		}

		return $cache[$id];
	}

	/**
	 * Returns the file name cleaned of the unique id
	 *
	 * @return string
	 */
	public function name()
	{
		static $cache			=	array();

		$id						=	$this->path();

		if ( ! isset( $cache[$id] ) ) {
			$name				=	$this->params()->get( 'name' );

			if ( ! $name ) {
				$extension		=	$this->extension();

				if ( $this->get( 'filename' ) ) {
					$name		=	Get::clean( pathinfo( $this->get( 'filename' ), PATHINFO_FILENAME ), GetterInterface::STRING ) . '.' . $extension;
				} else {
					$name		=	preg_replace( '/[^-a-zA-Z0-9_.]/', '', pathinfo( $id, PATHINFO_FILENAME ) ) . '.' . $extension;
				}
			}

			$cache[$id]			=	$name;
		}

		return $cache[$id];
	}

	/**
	 * Returns the image height cleaned of the unique id
	 *
	 * @param bool $thumbnail
	 * @return int
	 */
	public function height( $thumbnail = false )
	{
		static $cache			=	array();

		$id						=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$height				=	(int) $this->params()->get( ( $thumbnail ? 'height_thumbnail' : 'height' ), 0 );

			if ( ( ! $height ) && $this->exists( $thumbnail ) ) {
				$size			=	@getimagesize( $id );

				if ( $size !== false ) {
					$height		=	(int) $size[1];
				}
			}

			$cache[$id]			=	$height;
		}

		return $cache[$id];
	}

	/**
	 * Returns the image width cleaned of the unique id
	 *
	 * @param bool $thumbnail
	 * @return int
	 */
	public function width( $thumbnail = false )
	{
		static $cache			=	array();

		$id						=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$width				=	(int) $this->params()->get( ( $thumbnail ? 'width_thumbnail' : 'width' ), 0 );

			if ( ( ! $width ) && $this->exists( $thumbnail ) ) {
				$size			=	@getimagesize( $id );

				if ( $size !== false ) {
					$width		=	(int) $size[0];
				}
			}

			$cache[$id]			=	$width;
		}

		return $cache[$id];
	}

	/**
	 * Previews the file
	 *
	 * @param bool $thumbnail
	 * @return bool
	 */
	public function preview( $thumbnail = false )
	{
		if ( Application::Cms()->getClientId() ) {
			$thumbnail	=	false;
		}

		return $this->output( true, $thumbnail );
	}

	/**
	 * Downloads the file
	 *
	 * @return bool
	 */
	public function download()
	{
		return $this->output( false );
	}

	/**
	 * Outputs file to header
	 *
	 * @param bool $inline
	 * @param bool $thumbnail
	 * @return bool
	 */
	private function output( $inline = false, $thumbnail = false )
	{
		if ( ! $this->get( 'id' ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		if ( ! $this->exists( $thumbnail ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$fileExtension		=	$this->extension();

		if ( ! $fileExtension ) {
			header( 'HTTP/1.0 406 Not Acceptable' );
			exit();
		}

		$fileName			=	$this->name();

		if ( ! $fileName ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$fileMime			=	$this->mimeType();
		$fileSize			=	$this->size( true, $thumbnail );
		$fileModifed		=	date( 'r', filemtime( $this->path( $thumbnail ) ) );

		while ( @ob_end_clean() );

		if ( ini_get( 'zlib.output_compression' ) ) {
			ini_set( 'zlib.output_compression', 'Off' );
		}

		if ( function_exists( 'apache_setenv' ) ) {
			apache_setenv( 'no-gzip', '1' );
		}

		header( "Content-Type: $fileMime" );
		header( 'Content-Disposition: ' . ( $inline ? 'inline' : 'attachment' ) . '; filename="' . $fileName . '"; modification-date="' . $fileModifed . '"; size=' . $fileSize . ';' );
		header( "Content-Transfer-Encoding: binary" );
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Pragma: public" );
		header( "Accept-Ranges: bytes" );

		$offset				=	0;
		$length				=	$fileSize;

		if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
			if ( ! preg_match( '/^bytes=\d*-\d*(,\d*-\d*)*$/i', $_SERVER['HTTP_RANGE'] ) ) {
				header( "HTTP/1.1 416 Requested Range Not Satisfiable" );
				header( "Content-Range: bytes */$fileSize" );
				exit();
			}

			$ranges			=	explode( ',', substr( $_SERVER['HTTP_RANGE'], 6 ) );

			foreach ( $ranges as $range ) {
				$parts		=	explode( '-', $range );
				$offset		=	(int) $parts[0];
				$length		=	(int) $parts[1];
			}

			if ( ! $length ) {
				$length		=	( $fileSize - 1 );
			}

			if ( $offset > $length ) {
				header( "HTTP/1.1 416 Requested Range Not Satisfiable" );
				header( "Content-Range: bytes */$fileSize" );
				exit();
			}

			header( "HTTP/1.1 206 Partial Content" );
			header( "Content-Range: bytes $offset-$length/$fileSize" );
			header( "Content-Length: " . ( ( $length - $offset ) + 1 ) );
		} else {
			header( "HTTP/1.0 200 OK" );
			header( "Content-Length: $fileSize" );
		}

		if ( ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}

		$file				=	fopen( $this->path( $thumbnail ), 'rb' );

		if ( $file === false ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		fseek( $file, $offset );

		$buffer				=	( 1024 * 8 );

		while ( ( ! feof( $file ) ) && ( ( $pos = ftell( $file ) ) <= $length ) ) {
			if ( ( $pos + $buffer ) > $length ) {
				$buffer		=	( ( $length - $pos ) + 1 );
			}

			echo fread( $file, $buffer );
			@ob_flush();
			flush();
		}

		fclose( $file );

		exit();
	}
}