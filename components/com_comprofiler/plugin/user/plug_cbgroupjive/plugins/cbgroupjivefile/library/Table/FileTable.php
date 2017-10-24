<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveFile\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Input\Get;
use CBLib\Registry\GetterInterface;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\CBGroupJive;

defined('CBLIB') or die();

class FileTable extends Table
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
	public $file			=	null;
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
	protected $_tbl			=	'#__groupjive_plugin_file';

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
		} elseif ( isset( $_FILES['file']['tmp_name'] ) && ( ! empty( $_FILES['file']['tmp_name'] ) ) ) {
			static $params		=	null;

			if ( ! $params ) {
				$plugin			=	$_PLUGINS->getLoadedPlugin( 'user/plug_cbgroupjive/plugins', 'cbgroupjivefile' );
				$params			=	$_PLUGINS->getPluginParams( $plugin );
			}

			$minFileSize		=	$params->get( 'groups_file_min_size', 0 );
			$maxFileSize		=	$params->get( 'groups_file_max_size', 1024 );
			$extensions			=	explode( ',', $params->get( 'groups_file_extensions', 'zip,rar,doc,pdf,txt,xls' ) );

			$fileExtension		=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $_FILES['file']['name'], PATHINFO_EXTENSION ) ) );

			if ( ( ! $fileExtension ) || ( ! in_array( $fileExtension, $extensions ) ) ) {
				$this->setError( CBTxt::T( 'GROUP_FILE_UPLOAD_INVALID_EXT', 'Invalid file extension [ext]. Please upload only [exts]!', array( '[ext]' => $fileExtension, '[exts]' => implode( ', ', $extensions ) ) ) );

				return false;
			}

			$fileSize			=	$_FILES['file']['size'];

			if ( $minFileSize && ( ( $fileSize / 1024 ) < $minFileSize ) ) {
				$this->setError( CBTxt::T( 'GROUP_FILE_UPLOAD_TOO_SMALL', 'The file is too small, the minimum is [size]!', array( '[size]' => CBGroupJive::getFormattedFileSize( $minFileSize * 1024 ) ) ) );

				return false;
			}

			if ( $maxFileSize && ( ( $fileSize / 1024 ) > $maxFileSize ) ) {
				$this->setError( CBTxt::T( 'GROUP_FILE_UPLOAD_TOO_LARGE', 'The file size exceeds the maximum of [size]!', array( '[size]' => CBGroupJive::getFormattedFileSize( $maxFileSize * 1024 ) ) ) );

				return false;
			}
		} elseif ( $this->get( 'file' ) == '' ) {
			$this->setError( CBTxt::T( 'File not specified!' ) );

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

		$new					=	( $this->get( 'id' ) ? false : true );
		$old					=	new self();

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime() ) );

		if ( isset( $_FILES['file']['tmp_name'] ) && ( ! empty( $_FILES['file']['tmp_name'] ) ) ) {
			$basePath			=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivefile';
			$filePath			=	$basePath . '/' . (int) $this->group()->get( 'category' ) . '/' . (int) $this->group()->get( 'id' );

			CBGroupJive::createDirectory( $basePath, $this->group()->get( 'category' ), $this->group()->get( 'id' ) );

			$fileExtension		=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $_FILES['file']['name'], PATHINFO_EXTENSION ) ) );
			$fileName			=	Get::clean( pathinfo( $_FILES['file']['name'], PATHINFO_FILENAME ), GetterInterface::STRING ) . '.' . $fileExtension;
			$fileId				=	uniqid();

			$newFileName		=	$fileId . '.' . $fileExtension;

			if ( ! move_uploaded_file( $_FILES['file']['tmp_name'], $filePath . '/' . $newFileName ) ) {
				$this->setError( CBTxt::T( 'GROUP_FILE_UPLOAD_FAILED', 'The file [file] failed to upload!', array( '[file]' => $newFileName ) ) );

				return false;
			} else {
				@chmod( $filePath . '/' . $newFileName, 0755 );
			}

			if ( $this->get( 'file' ) && file_exists( $filePath . '/' . $this->get( 'file' ) ) ) {
				@unlink( $filePath . '/' . $this->get( 'file' ) );
			}

			$this->set( 'file', $newFileName );
			$this->set( 'filename', $fileName );

			$params					=	$this->params();

			$params->unsetEntry( 'name' );
			$params->unsetEntry( 'extension' );
			$params->unsetEntry( 'mimetype' );
			$params->unsetEntry( 'filesize' );

			$params->set( 'name', $this->name() );
			$params->set( 'extension', $this->extension() );
			$params->set( 'mimetype', $this->mimeType() );
			$params->set( 'filesize', $this->size( true ) );

			$this->set( 'params', $params->asJson() );
		} elseif ( ! $this->get( 'filename' ) ) {
			$this->set( 'filename', $this->get( 'file' ) );
		}

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'gj_onBeforeUpdateFile', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onBeforeCreateFile', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( $old->get( 'id' ) && $this->get( 'file' ) && ( $old->get( 'group' ) != $this->get( 'group' ) ) ) {
			$basePath			=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivefile';
			$oldPath			=	$basePath . '/' . (int) $old->group()->get( 'category' ) . '/' . (int) $old->group()->get( 'id' );
			$newPath			=	$basePath . '/' . (int) $this->group()->get( 'category' ) . '/' . (int) $this->group()->get( 'id' );

			if ( is_dir( $oldPath ) ) {
				CBGroupJive::createDirectory( $basePath, $this->group()->get( 'category' ), $this->group()->get( 'id' ) );

				if ( file_exists( $oldPath . '/' . $this->get( 'file' ) ) ) {
					@rename( $oldPath . '/' . $this->get( 'file' ), $newPath . '/' . $this->get( 'file' ) );
				}
			}
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gj_onAfterUpdateFile', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onAfterCreateFile', array( $this ) );
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

		$_PLUGINS->trigger( 'gj_onBeforeDeleteFile', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		if ( $this->get( 'file' ) ) {
			$basePath		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivefile';
			$filePath		=	$basePath . '/' . (int) $this->group()->get( 'category' ) . '/' . (int) $this->group()->get( 'id' );

			if ( file_exists( $filePath . '/' . $this->get( 'file' ) ) ) {
				@unlink( $filePath . '/' . $this->get( 'file' ) );
			}
		}

		$_PLUGINS->trigger( 'gj_onAfterDeleteFile', array( $this ) );

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
	 * Returns the clean absolute path to the file
	 *
	 * @return string
	 */
	public function path()
	{
		global $_CB_framework;

		static $cache		=	array();

		$id					=	$this->get( 'file' );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivefile/' . (int) $this->group()->get( 'category' ) . '/' . (int) $this->group()->get( 'id' ) . '/' . preg_replace( '/[^-a-zA-Z0-9_.]/', '', $id );
		}

		return $cache[$id];
	}

	/**
	 * Checks if the file exists
	 *
	 * @return bool
	 */
	public function exists()
	{
		static $cache		=	array();

		$id					=	$this->path();

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	@file_exists( $id );
		}

		return $cache[$id];
	}

	/**
	 * Returns the file size raw or formatted to largest increment possible
	 *
	 * @param bool $raw
	 * @return string|int
	 */
	public function size( $raw = false )
	{
		static $cache			=	array();

		$id						=	$this->path();

		if ( ! isset( $cache[$id] ) ) {
			$fileSize			=	(int) $this->params()->get( 'filesize', 0 );

			if ( ( ! $fileSize ) && $this->exists() ) {
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
	 * Previews the file
	 *
	 * @return bool
	 */
	public function preview()
	{
		return $this->output( true );
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
	 * @return bool
	 */
	private function output( $inline = false )
	{
		if ( ! $this->get( 'id' ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		if ( ! $this->exists() ) {
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
		$fileSize			=	$this->size( true );
		$fileModifed		=	date( 'r', filemtime( $this->path() ) );

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

		$file				=	fopen( $this->path(), 'rb' );

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

	/**
	 * Returns the fontawesome icon based off extension and that extensions mimetype
	 *
	 * @return string
	 */
	public function icon()
	{
		$extension						=	$this->extension();
		$type							=	'file-o';

		if ( ! $extension ) {
			return $type;
		}

		static $cache					=	array();

		if ( ! isset( $cache[$extension] ) ) {
			$mimeParts					=	explode( '/', $this->mimeType() );

			switch ( $mimeParts[0] ) {
				case 'text':
					switch ( $extension ) {
						case 'csv':
							$type		=	'file-excel-o';
							break;
						case 'css':
						case 'html':
						case 'htm':
							$type		=	'file-code-o';
							break;
						default:
							$type		=	'file-text-o';
							break;
					}
					break;
				case 'video':
					$type				=	'file-video-o';
					break;
				case 'audio':
					$type				=	'file-audio-o';
					break;
				case 'image':
					$type				=	'file-image-o';
					break;
				default:
					switch ( $extension ) {
						case 'pdf':
							$type		=	'file-pdf-o';
							break;
						case 'zip':
						case '7z':
						case 'rar':
						case 'tar':
						case 'iso':
							$type		=	'file-archive-o';
							break;
						case 'js':
						case 'php':
						case 'xml':
						case 'java':
							$type		=	'file-code-o';
							break;
						case 'ods':
						case 'xls':
						case 'xlsx':
						case 'xlt':
							$type		=	'file-excel-o';
							break;
						case 'doc':
						case 'docx':
						case 'odt':
						case 'dot':
							$type		=	'file-word-o';
							break;
					}
					break;
			}

			$cache[$extension]			=	$type;
		}

		return $cache[$extension];
	}
}