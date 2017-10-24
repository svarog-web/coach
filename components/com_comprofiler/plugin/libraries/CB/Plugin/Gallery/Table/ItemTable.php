<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery\Table;

use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Image\Image;
use GuzzleHttp\Client;
use Exception;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;

defined('CBLIB') or die();

class ItemTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var string  */
	public $type			=	null;
	/** @var string  */
	public $asset			=	null;
	/** @var string  */
	public $value			=	null;
	/** @var string  */
	public $file			=	null;
	/** @var int  */
	public $folder			=	null;
	/** @var string  */
	public $title			=	null;
	/** @var string  */
	public $description		=	null;
	/** @var string  */
	public $thumbnail		=	null;
	/** @var string  */
	public $date			=	null;
	/** @var int  */
	public $published		=	null;
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
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_gallery_items';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @param null|Gallery $gallery
	 * @return bool
	 */
	public function check( $gallery = null )
	{
		global $_PLUGINS;

		if ( Application::Cms()->getClientId() ) {
			$files							=	Application::Input()->getNamespaceRegistry( 'files' );
		} else {
			$files							=	$this->get( '_files', new Registry(), GetterInterface::RAW );
		}

		static $params						=	null;

		if ( $params == null ) {
			$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );
			$params							=	$_PLUGINS->getPluginParams( $plugin );
		}

		$new								=	( $this->get( 'id', 0, GetterInterface::INT ) ? false : true );
		$old								=	new self();

		if ( ! $new ) {
			$old->load( $this->get( 'id', 0, GetterInterface::INT ) );
		}

		$type								=	$this->discoverType( $gallery );

		if ( $gallery ) {
			$minFileSize					=	$gallery->get( $type . '_min_size', 0, GetterInterface::INT );
			$maxFileSize					=	$gallery->get( $type . '_max_size', 1024, GetterInterface::INT );

			$thumbnails						=	$gallery->get( 'thumbnails', true, GetterInterface::BOOLEAN );
			$thumbnailsUpload				=	$gallery->get( 'thumbnails_upload', true, GetterInterface::BOOLEAN );
			$thumbnailsLink					=	$gallery->get( 'thumbnails_link', false, GetterInterface::BOOLEAN );

			$minThumbnailSize				=	$gallery->get( 'thumbnails_min_size', 0, GetterInterface::INT );
			$maxThumbnailSize				=	$gallery->get( 'thumbnails_max_size', 1024, GetterInterface::INT );
		} else {
			$minFileSize					=	$params->get( $type . '_min_size', 0, GetterInterface::INT );
			$maxFileSize					=	$params->get( $type . '_max_size', 1024, GetterInterface::INT );

			$thumbnails						=	$params->get( 'thumbnails', true, GetterInterface::BOOLEAN );
			$thumbnailsUpload				=	$params->get( 'thumbnails_upload', true, GetterInterface::BOOLEAN );
			$thumbnailsLink					=	$params->get( 'thumbnails_link', false, GetterInterface::BOOLEAN );

			$minThumbnailSize				=	$params->get( 'thumbnails_min_size', 0, GetterInterface::INT );
			$maxThumbnailSize				=	$params->get( 'thumbnails_max_size', 1024, GetterInterface::INT );
		}

		$upload								=	$files->subTree( 'upload' );
		$uploadFile							=	$upload->get( 'tmp_name', null, GetterInterface::STRING );
		$value								=	$this->get( 'value', null, GetterInterface::STRING );

		if ( ! $this->get( 'user_id', 0, GetterInterface::INT ) ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		} elseif ( $type == '' ) {
			$this->setError( CBTxt::T( 'Type not specified!' ) );

			return false;
		} elseif ( ( ! $this->get( 'id', 0, GetterInterface::INT ) ) && ( ( ! $value ) && ( ! $uploadFile ) ) ) {
			$this->setError( CBTxt::T( 'Nothing to upload or link!' ) );

			return false;
		} elseif ( $uploadFile ) {
			$uploadExtension				=	CBGallery::getUploadExtension( $upload );

			if ( ( ! $uploadExtension ) || ( ! in_array( $uploadExtension, CBGallery::getExtensions( $type, $gallery, 'upload' ) ) ) ) {
				$this->setError( CBTxt::T( 'FILE_UPLOAD_INVALID_EXT', 'Invalid file extension [extension]. Please upload only [extensions]!', array( '[extension]' => $uploadExtension, '[extensions]' => implode( ', ', CBGallery::getExtensions( 'all', $gallery, 'upload' ) ) ) ) );

				return false;
			}

			$uploadSize						=	$upload->get( 'size', 0, GetterInterface::INT );

			if ( $minFileSize && ( ( $uploadSize / 1024 ) < $minFileSize ) ) {
				$this->setError( CBTxt::T( 'FILE_UPLOAD_TOO_SMALL', 'The file is too small. The minimum size is [size]!', array( '[size]' => CBGallery::getFormattedFileSize( $minFileSize * 1024 ) ) ) );

				return false;
			}

			if ( $maxFileSize && ( ( $uploadSize / 1024 ) > $maxFileSize ) ) {
				$this->setError( CBTxt::T( 'FILE_UPLOAD_TOO_LARGE', 'The file is too large. The maximum size is [size]!', array( '[size]' => CBGallery::getFormattedFileSize( $maxFileSize * 1024 ) ) ) );

				return false;
			}
		} elseif ( $value && ( $value != $old->get( 'value', null, GetterInterface::STRING ) ) ) {
			if ( $this->domain() ) {
				if ( ! in_array( $this->domain(), array( 'youtube', 'youtu' ) ) ) {
					$link					=	CBGallery::parseUrl( $value );

					if ( ! $link['exists'] ) {
						$this->setError( CBTxt::T( 'FILE_LINK_INVALID_URL', 'Invalid file URL. Please ensure the URL exists!' ) );

						return false;
					}

					$linkExtension			=	$link['extension'];
				} else {
					$linkExtension			=	'youtube';
				}

				if ( ( ! $linkExtension ) || ( ! in_array( $linkExtension, CBGallery::getExtensions( $type, $gallery, 'link' ) ) ) ) {
					$this->setError( CBTxt::T( 'FILE_LINK_INVALID_EXT', 'Invalid file URL extension [extension]. Please link only [extensions]!', array( '[extension]' => $linkExtension, '[extensions]' => implode( ', ', CBGallery::getExtensions( 'all', $gallery, 'link' ) ) ) ) );

					return false;
				}
			}
		}

		if ( ( $type != 'photos' ) && $thumbnails ) {
			$uploadThumbnail				=	$files->subTree( 'thumbnail_upload' );
			$thumbnail						=	$this->get( 'thumbnail', null, GetterInterface::STRING );

			if ( $thumbnailsUpload && $uploadThumbnail->get( 'tmp_name', null, GetterInterface::STRING ) ) {
				$uploadThumbnailExtension	=	CBGallery::getUploadExtension( $uploadThumbnail );

				if ( ( ! $uploadThumbnailExtension ) || ( ! in_array( $uploadThumbnailExtension, CBGallery::getExtensions( 'photos' ) ) ) ) {
					$this->setError( CBTxt::T( 'THUMBNAIL_UPLOAD_INVALID_EXT', 'Invalid thumbnail file extension [extension]. Please upload only [extensions]!', array( '[extension]' => $uploadThumbnailExtension, '[extensions]' => implode( ', ', CBGallery::getExtensions( 'photos' ) ) ) ) );

					return false;
				}

				$uploadThumbnailSize		=	$uploadThumbnail->get( 'size', 0, GetterInterface::INT );

				if ( $minThumbnailSize && ( ( $uploadThumbnailSize / 1024 ) < $minThumbnailSize ) ) {
					$this->setError( CBTxt::T( 'THUMBNAIL_UPLOAD_TOO_SMALL', 'The thumbnail file is too small. The minimum size is [size]!', array( '[size]' => CBGallery::getFormattedFileSize( $minThumbnailSize * 1024 ) ) ) );

					return false;
				}

				if ( $maxThumbnailSize && ( ( $uploadThumbnailSize / 1024 ) > $maxThumbnailSize ) ) {
					$this->setError( CBTxt::T( 'THUMBNAIL_UPLOAD_TOO_LARGE', 'The thumbnail file is too large. The maximum size is [size]!', array( '[size]' => CBGallery::getFormattedFileSize( $maxThumbnailSize * 1024 ) ) ) );

					return false;
				}
			} elseif ( $thumbnailsLink && $thumbnail && ( $thumbnail != $old->get( 'thumbnail', null, GetterInterface::STRING ) ) ) {
				if ( $this->domain( true ) ) {
					$link					=	CBGallery::parseUrl( $thumbnail );

					if ( ! $link['exists'] ) {
						$this->setError( CBTxt::T( 'THUMBNAIL_LINK_INVALID_URL', 'Invalid thumbnail file URL. Please ensure the URL exists!' ) );

						return false;
					}

					$linkExtension			=	$link['extension'];

					if ( ( ! $linkExtension ) || ( ! in_array( $linkExtension, CBGallery::getExtensions( 'photos' ) ) ) ) {
						$this->setError( CBTxt::T( 'THUMBNAIL_LINK_INVALID_EXT', 'Invalid thumbnail file URL extension [extension]. Please link only [extensions]!', array( '[extension]' => $linkExtension, '[extensions]' => implode( ', ', CBGallery::getExtensions( 'photos' ) ) ) ) );

						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param bool         $updateNulls
	 * @param null|Gallery $gallery
	 * @return bool
	 */
	public function store( $updateNulls = false, $gallery = null )
	{
		global $_CB_framework, $_PLUGINS;

		if ( Application::Cms()->getClientId() ) {
			$input							=	Application::Input();
			$files							=	$input->getNamespaceRegistry( 'files' );
		} else {
			$input							=	$this->get( '_input', new Registry(), GetterInterface::RAW );
			$files							=	$this->get( '_files', new Registry(), GetterInterface::RAW );
		}

		static $params						=	null;

		if ( $params == null ) {
			$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );
			$params							=	$_PLUGINS->getPluginParams( $plugin );
		}

		$new								=	( $this->get( 'id', 0, GetterInterface::INT ) ? false : true );
		$old								=	new self();

		if ( ! $new ) {
			$old->load( $this->get( 'id', 0, GetterInterface::INT ) );

			$this->cache( true );
		}

		if ( ! $this->get( 'asset', null, GetterInterface::STRING ) ) {
			$this->set( 'asset', 'profile.' . $this->get( 'user_id', 0, GetterInterface::INT ) );
		}

		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime(), GetterInterface::STRING ) );

		$type								=	$this->discoverType( $gallery );

		if ( $type != $this->get( 'type', null, GetterInterface::STRING ) ) {
			$this->set( 'type', $type );
		}

		if ( $gallery ) {
			$resample						=	$gallery->get( 'photos_resample', 1, GetterInterface::INT );
			$aspectRatio					=	$gallery->get( 'photos_maintain_aspect_ratio', 1, GetterInterface::INT );
			$imageHeight					=	$gallery->get( 'photos_image_height', 640, GetterInterface::INT );
			$imageWidth						=	$gallery->get( 'photos_image_width', 1280, GetterInterface::INT );
			$thumbHeight					=	$gallery->get( 'photos_thumbnail_height', 320, GetterInterface::INT );
			$thumbWidth						=	$gallery->get( 'photos_thumbnail_width', 640, GetterInterface::INT );

			$thumbnails						=	$gallery->get( 'thumbnails', true, GetterInterface::BOOLEAN );
			$thumbnailsUpload				=	$gallery->get( 'thumbnails_upload', true, GetterInterface::BOOLEAN );
			$thumbnailsLink					=	$gallery->get( 'thumbnails_link', false, GetterInterface::BOOLEAN );
			$thumbnailsResample				=	$gallery->get( 'thumbnails_resample', 1, GetterInterface::INT );
			$thumbnailsAspectRatio			=	$gallery->get( 'thumbnails_maintain_aspect_ratio', 1, GetterInterface::INT );
			$thumbnailsImageHeight			=	$gallery->get( 'thumbnails_image_height', 320, GetterInterface::INT );
			$thumbnailsImageWidth			=	$gallery->get( 'thumbnails_image_width', 640, GetterInterface::INT );
		} else {
			$resample						=	$params->get( 'photos_resample', 1, GetterInterface::INT );
			$aspectRatio					=	$params->get( 'photos_maintain_aspect_ratio', 1, GetterInterface::INT );
			$imageHeight					=	$params->get( 'photos_image_height', 640, GetterInterface::INT );
			$imageWidth						=	$params->get( 'photos_image_width', 1280, GetterInterface::INT );
			$thumbHeight					=	$params->get( 'photos_thumbnail_height', 320, GetterInterface::INT );
			$thumbWidth						=	$params->get( 'photos_thumbnail_width', 640, GetterInterface::INT );

			$thumbnails						=	$params->get( 'thumbnails', true, GetterInterface::BOOLEAN );
			$thumbnailsUpload				=	$params->get( 'thumbnails_upload', true, GetterInterface::BOOLEAN );
			$thumbnailsLink					=	$params->get( 'thumbnails_link', false, GetterInterface::BOOLEAN );
			$thumbnailsResample				=	$params->get( 'thumbnails_resample', 1, GetterInterface::INT );
			$thumbnailsAspectRatio			=	$params->get( 'thumbnails_maintain_aspect_ratio', 1, GetterInterface::INT );
			$thumbnailsImageHeight			=	$params->get( 'thumbnails_image_height', 320, GetterInterface::INT );
			$thumbnailsImageWidth			=	$params->get( 'thumbnails_image_width', 640, GetterInterface::INT );
		}

		if ( ! $imageHeight ) {
			$imageHeight					=	640;
		}

		if ( ! $imageWidth ) {
			$imageWidth						=	1280;
		}

		if ( ! $thumbHeight ) {
			$thumbHeight					=	320;
		}

		if ( ! $thumbWidth ) {
			$thumbWidth						=	640;
		}

		if ( ! $thumbnailsImageHeight ) {
			$thumbnailsImageHeight			=	320;
		}

		if ( ! $thumbnailsImageWidth ) {
			$thumbnailsImageWidth			=	640;
		}

		$conversionType						=	Application::Config()->get( 'conversiontype', 0, GetterInterface::INT );
		$imageSoftware						=	( $conversionType == 5 ? 'gmagick' : ( $conversionType == 1 ? 'imagick' : ( $conversionType == 4 ? 'gd' : 'auto' ) ) );

		$upload								=	$files->subTree( 'upload' );
		$uploadFile							=	$upload->get( 'tmp_name', null, GetterInterface::STRING );
		$value								=	$this->get( 'value', null, GetterInterface::STRING );

		$basePath							=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgallery';
		$filePath							=	$basePath . '/' . $this->get( 'user_id', 0, GetterInterface::INT ) . '/' . $type;

		if ( $uploadFile ) {
			CBGallery::createDirectory( $basePath, $this->get( 'user_id', 0, GetterInterface::INT ), $type );

			$uploadExtension				=	CBGallery::getUploadExtension( $upload );
			$uploadName						=	$upload->get( 'name', null, GetterInterface::STRING );

			if ( ( ! $uploadName ) || ( $uploadName == 'blob' ) ) {
				$uploadName					=	Application::Date( 'now', 'UTC' )->getTimestamp() . '.' . $uploadExtension;
			} else {
				$uploadName					=	pathinfo( $uploadName, PATHINFO_FILENAME ) . '.' . $uploadExtension;
			}

			$upload->set( 'name', $uploadName );

			$uploadId						=	md5( uniqid( $uploadName ) );

			if ( $type == 'photos' ) {
				try {
					$image					=	new Image( $imageSoftware, $resample, $aspectRatio );

					$image->setName( $uploadId );
					$image->setSource( $upload->asArray() );
					$image->setDestination( $filePath . '/' );

					$image->processImage( $imageWidth, $imageHeight );

					$newFileName			=	$image->getCleanFilename();

					$image->setName( 'tn' . $uploadId );

					$image->processImage( $thumbWidth, $thumbHeight );

					if ( $value ) {
						if ( file_exists( $filePath . '/' . $value ) ) {
							@unlink( $filePath . '/' . $value );
						}

						if ( file_exists( $filePath . '/tn' . $value ) ) {
							@unlink( $filePath . '/tn' . $value );
						}
					}

					$this->set( 'value', $newFileName );
					$this->set( 'file', $uploadName );
				} catch ( Exception $e ) {
					$this->setError( $e->getMessage() );

					return false;
				}
			} else {
				$newFileName				=	$uploadId . '.' . $uploadExtension;

				if ( ! move_uploaded_file( $uploadFile, $filePath . '/' . $newFileName ) ) {
					$this->setError( CBTxt::T( 'FILE_FAILED_TO_UPLOAD', 'The file [file] failed to upload!', array( '[file]' => $newFileName ) ) );

					return false;
				} else {
					@chmod( $filePath . '/' . $newFileName, 0755 );
				}

				if ( $value ) {
					if ( file_exists( $filePath . '/' . $value ) ) {
						@unlink( $filePath . '/' . $value );
					}

					if ( file_exists( $filePath . '/tn' . $value ) ) {
						@unlink( $filePath . '/tn' . $value );
					}
				}

				$this->set( 'value', $newFileName );
				$this->set( 'file', $uploadName );
			}
		} elseif ( $this->domain() )  {
			$this->set( 'file', '' );

			if ( $value && ( $value != $old->get( 'value', null, GetterInterface::STRING ) ) ) {
				$link						=	CBGallery::parseUrl( $value );

				if ( $link['exists'] ) {
					$this->set( 'value', $link['url'] );

					if ( $new ) {
						if ( ! $this->get( 'title', null, GetterInterface::STRING ) ) {
							$this->set( 'title', $link['title'] );
						}

						if ( ! $this->get( 'description', null, GetterInterface::STRING ) ) {
							$this->set( 'description', $link['description'] );
						}
					}
				}
			}
		} elseif ( ! $this->get( 'file', null, GetterInterface::STRING ) ) {
			$this->set( 'file', $value );
		}

		if ( ( $type != 'photos' ) && $thumbnails ) {
			$uploadThumbnail				=	$files->subTree( 'thumbnail_upload' );
			$thumbnail						=	$this->get( 'thumbnail', null, GetterInterface::STRING );

			if ( $input->get( 'thumbnail_method', 0, GetterInterface::INT ) == 3 ) {
				if ( $thumbnail ) {
					if ( ! $this->domain( true ) ) {
						if ( file_exists( $filePath . '/' . $thumbnail ) ) {
							@unlink( $filePath . '/' . $thumbnail );
						}
					}

					$this->set( 'thumbnail', '' );
				}
			} elseif ( $thumbnailsUpload && $uploadThumbnail->get( 'tmp_name', null, GetterInterface::STRING ) ) {
				CBGallery::createDirectory( $basePath, $this->get( 'user_id', 0, GetterInterface::INT ), $type );

				$uploadThumbnailExtension	=	CBGallery::getUploadExtension( $uploadThumbnail );
				$uploadThumbnailName		=	$uploadThumbnail->get( 'name', null, GetterInterface::STRING );

				if ( ( ! $uploadThumbnailName ) || ( $uploadThumbnailName == 'blob' ) ) {
					$uploadThumbnailName	=	Application::Date( 'now', 'UTC' )->getTimestamp() . '.' . $uploadThumbnailExtension;
				} else {
					$uploadThumbnailName	=	pathinfo( $uploadThumbnailName, PATHINFO_FILENAME ) . '.' . $uploadThumbnailExtension;
				}

				$uploadThumbnail->set( 'name', $uploadThumbnailName );

				try {
					$image					=	new Image( $imageSoftware, $thumbnailsResample, $thumbnailsAspectRatio );

					$image->setName( md5( uniqid( $uploadThumbnailName ) ) );
					$image->setSource( $uploadThumbnail->asArray() );
					$image->setDestination( $filePath . '/' );

					$image->processImage( $thumbnailsImageWidth, $thumbnailsImageHeight );

					if ( $thumbnail ) {
						if ( file_exists( $filePath . '/' . $thumbnail ) ) {
							@unlink( $filePath . '/' . $thumbnail );
						}
					}

					$this->set( 'thumbnail', $image->getCleanFilename() );
				} catch ( Exception $e ) {
					$this->setError( $e->getMessage() );

					return false;
				}
			} elseif ( $thumbnailsLink && $thumbnail && ( $thumbnail != $old->get( 'thumbnail', null, GetterInterface::STRING ) ) ) {
				if ( $this->domain( true ) ) {
					$linkThumbnail			=	CBGallery::parseUrl( $thumbnail );

					if ( $linkThumbnail['exists'] ) {
						$this->set( 'thumbnail', $linkThumbnail['url'] );
					}
				}
			}
		}

		$this->cache();

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gallery_onBeforeUpdateItem', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onBeforeCreateItem', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( $old->get( 'id', 0, GetterInterface::INT ) && ( ! $old->domain() ) && ( $old->get( 'user_id', 0, GetterInterface::INT ) != $this->get( 'user_id', 0, GetterInterface::INT ) ) ) {
			$basePath						=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgallery';
			$oldPath						=	$basePath . '/' . $old->get( 'user_id', 0, GetterInterface::INT ) . '/' . $old->get( 'type', null, GetterInterface::STRING );
			$newPath						=	$basePath . '/' . $this->get( 'user_id', 0, GetterInterface::INT ) . '/' . $type;

			if ( is_dir( $oldPath ) ) {
				CBGallery::createDirectory( $basePath, $this->get( 'user_id', 0, GetterInterface::INT ), $type );

				if ( $this->get( 'value', null, GetterInterface::STRING ) && ( ! $this->domain() ) ) {
					if ( file_exists( $oldPath . '/' . $this->get( 'value', null, GetterInterface::STRING ) ) ) {
						@rename( $oldPath . '/' . $this->get( 'value', null, GetterInterface::STRING ), $newPath . '/' . $this->get( 'value', null, GetterInterface::STRING ) );
					}

					if ( file_exists( $oldPath . '/tn' . $this->get( 'value', null, GetterInterface::STRING ) ) ) {
						@rename( $oldPath . '/tn' . $this->get( 'value', null, GetterInterface::STRING ), $newPath . '/tn' . $this->get( 'value', null, GetterInterface::STRING ) );
					}
				}

				if ( $this->get( 'thumbnail', null, GetterInterface::STRING ) && ( ! $this->domain( true ) ) ) {
					if ( file_exists( $oldPath . '/' . $this->get( 'thumbnail', null, GetterInterface::STRING ) ) ) {
						@rename( $oldPath . '/' . $this->get( 'thumbnail', null, GetterInterface::STRING ), $newPath . '/' . $this->get( 'thumbnail', null, GetterInterface::STRING ) );
					}
				}
			}
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gallery_onAfterUpdateItem', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gallery_onAfterCreateItem', array( $this ) );
		}

		return true;
	}

	/**
	 * resets or sets the media details cache
	 *
	 * @param bool $resetOnly
	 */
	private function cache( $resetOnly = false )
	{
		$cache		=	$this->params();

		$cache->unsetEntry( 'name' );
		$cache->unsetEntry( 'extension' );
		$cache->unsetEntry( 'mimetype' );
		$cache->unsetEntry( 'modified' );
		$cache->unsetEntry( 'filesize' );
		$cache->unsetEntry( 'height' );
		$cache->unsetEntry( 'width' );

		$cache->unsetEntry( 'name_thumbnail' );
		$cache->unsetEntry( 'extension_thumbnail' );
		$cache->unsetEntry( 'mimetype_thumbnail' );
		$cache->unsetEntry( 'modified_thumbnail' );
		$cache->unsetEntry( 'filesize_thumbnail' );
		$cache->unsetEntry( 'height_thumbnail' );
		$cache->unsetEntry( 'width_thumbnail' );

		if ( $resetOnly ) {
			return;
		}

		$cache->set( 'name', $this->name() );
		$cache->set( 'extension', $this->extension() );
		$cache->set( 'mimetype', $this->mimeType() );
		$cache->set( 'modified', $this->modified() );
		$cache->set( 'filesize', $this->size( false, true ) );
		$cache->set( 'height', $this->height() );
		$cache->set( 'width', $this->width() );

		$cache->set( 'name_thumbnail', $this->name( true ) );
		$cache->set( 'extension_thumbnail', $this->extension( true ) );
		$cache->set( 'mimetype_thumbnail', $this->mimeType( true ) );
		$cache->set( 'modified_thumbnail', $this->modified( true ) );
		$cache->set( 'filesize_thumbnail', $this->size( true, true ) );
		$cache->set( 'height_thumbnail', $this->height( true ) );
		$cache->set( 'width_thumbnail', $this->width( true ) );

		$this->set( 'params', $cache->asJson() );
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global  $_PLUGINS;

		$_PLUGINS->trigger( 'gallery_onBeforeDeleteItem', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		if ( ( ! $this->domain() ) && $this->exists() ) {
			@unlink( $this->path() );
		}

		if ( ( ! $this->domain( true ) ) && $this->exists( true ) ) {
			@unlink( $this->path( true ) );
		}

		if ( $this->get( 'folder', 0, GetterInterface::INT ) ) {
			$folder		=	$this->folder();

			if ( $folder->get( 'id', 0, GetterInterface::INT ) && ( $folder->get( 'thumbnail', 0, GetterInterface::INT ) == $this->get( 'id', 0, GetterInterface::INT ) ) ) {
				$folder->set( 'thumbnail', 0 );

				$folder->store();
			}
		}

		$_PLUGINS->trigger( 'gallery_onAfterDeleteItem', array( $this ) );

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
	 * Returns the domain if the item is a link
	 *
	 * @param bool $thumbnail
	 * @return string
	 */
	public function domain( $thumbnail = false )
	{
		static $cache		=	array();

		$id					=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $id, PHP_URL_HOST ) );
		}

		return $cache[$id];
	}

	/**
	 * Returns the clean absolute path to the items file
	 *
	 * @param bool $thumbnail
	 * @return null|string
	 */
	public function path( $thumbnail = false )
	{
		global $_CB_framework;

		static $cache					=	array();

		$type							=	$this->get( 'type', null, GetterInterface::STRING );
		$id								=	null;

		if ( ( $type != 'photos' ) && $thumbnail ) {
			$id							=	$this->get( 'thumbnail', null, GetterInterface::STRING );
		}

		if ( ! $id ) {
			$id							=	$this->get( 'value', null, GetterInterface::STRING );
		}

		if ( ! isset( $cache[$id][$thumbnail] ) ) {
			$domain						=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $id, PHP_URL_HOST ) );
			$path						=	null;
			$value						=	null;

			if ( $domain ) {
				if ( $thumbnail && in_array( $domain, array( 'youtube', 'youtu' ) ) ) {
					preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:be/))([A-Za-z0-9_-]+)%', $id, $matches );

					$path				=	'https://img.youtube.com/vi/' . ( isset( $matches[1] ) ? htmlspecialchars( $matches[1] ) : 'unknown' ) . '/0.jpg';
				}

				if ( ! $path ) {
					$path				=	$id;
				}
			} else {
				$value					=	pathinfo( $id, PATHINFO_FILENAME ) . '.' . strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $id, PATHINFO_EXTENSION ) ) );

				if ( $type == 'photos' ) {
					$value				=	( $thumbnail ? 'tn' : null ) . $value;
				}
			}

			if ( ! $path ) {
				$path					=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/plug_cbgallery/' . $this->get( 'user_id', 0, GetterInterface::INT ) . '/' . $type . '/' . $value;
			}

			$cache[$id][$thumbnail]		=	$path;
		}

		return $cache[$id][$thumbnail];
	}

	/**
	 * Checks if the file exists
	 *
	 * @param bool $thumbnail
	 * @return bool
	 */
	public function exists( $thumbnail = false )
	{
		static $cache						=	array();

		$id									=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$exists							=	false;

			if ( $id ) {
				$domain						=	$this->domain( $thumbnail );

				if ( $domain ) {
					if ( in_array( $domain, array( 'youtube', 'youtu' ) ) && ( ! $thumbnail ) ) {
						$exists				=	true;
					} else {
						try {
							$request		=	new Client();

							$header			=	$request->head( $id );

							if ( ( $header !== false ) && ( $header->getStatusCode() == 200 ) ) {
								$exists		=	true;
							}
						} catch( Exception $e ) {}
					}
				} else {
					$exists					=	file_exists( $id );
				}
			}

			$cache[$id]						=	$exists;
		}

		return $cache[$id];
	}

	/**
	 * Returns the file size raw or formatted to largest increment possible
	 *
	 * @param bool $thumbnail
	 * @param bool $raw
	 * @return string|int
	 */
	public function size( $thumbnail = false, $raw = false )
	{
		if ( Application::Cms()->getClientId() && is_object( $raw ) ) {
			$raw								=	false;
		}

		static $cache							=	array();

		$id										=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$fileSize							=	$this->params()->get( ( $thumbnail ? 'filesize_thumbnail' : 'filesize' ), null, GetterInterface::INT );

			if ( ( $fileSize === null ) && $this->exists( $thumbnail ) ) {
				$fileSize						=	0;

				if ( $this->exists( $thumbnail ) ) {
					$domain						=	$this->domain( $thumbnail );

					if ( $domain ) {
						if ( ( ! in_array( $domain, array( 'youtube', 'youtu' ) ) ) || $thumbnail ) {
							try {
								$request		=	new Client();

								$header			=	$request->head( $id );

								if ( ( $header !== false ) && ( $header->getStatusCode() == 200 ) ) {
									$fileSize	=	(int) $header->getHeader( 'Content-Length' );
								}
							} catch( Exception $e ) {}
						}
					} else {
						$fileSize				=	@filesize( $id );
					}
				}
			}

			$cache[$id]							=	$fileSize;
		}

		if ( ! $raw ) {
			return CBGallery::getFormattedFileSize( $cache[$id] );
		}

		return $cache[$id];
	}

	/**
	 * Returns the file name cleaned of the unique id
	 *
	 * @param bool $thumbnail
	 * @return string|null
	 */
	public function name( $thumbnail = false)
	{
		static $cache				=	array();

		$id							=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$name					=	$this->params()->get( ( $thumbnail ? 'name_thumbnail' : 'name' ), null, GetterInterface::STRING );

			if ( $name === null ) {
				$domain				=	$this->domain( $thumbnail );

				if ( $domain && in_array( $domain, array( 'youtube', 'youtu' ) ) && ( ! $thumbnail ) ) {
					$name			=	preg_replace( '%^.*(?:v=|v/|/)([\w-]+).*%i', '$1', $id );
				} else {
					if ( $this->get( 'file', null, GetterInterface::STRING ) && ( ! $thumbnail ) ) {
						$name		=	pathinfo( $this->get( 'file', null, GetterInterface::STRING ), PATHINFO_FILENAME ) . '.' . $this->extension( $thumbnail );
					} else {
						$name		=	pathinfo( $id, PATHINFO_FILENAME ) . '.' . $this->extension( $thumbnail );
					}
				}
			}

			$cache[$id]				=	$name;
		}

		return $cache[$id];
	}

	/**
	 * Returns the items file extension
	 *
	 * @param bool $thumbnail
	 * @return string|null
	 */
	public function extension( $thumbnail = false )
	{
		static $cache				=	array();

		$id							=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$extension				=	$this->params()->get( ( $thumbnail ? 'extension_thumbnail' : 'extension' ), null, GetterInterface::STRING );

			if ( $extension === null ) {
				$domain				=	$this->domain( $thumbnail );

				if ( $domain && in_array( $domain, array( 'youtube', 'youtu' ) ) && ( ! $thumbnail ) ) {
					$extension		=	'youtube';
				} else {
					$extension		=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $id, PATHINFO_EXTENSION ) ) );
				}
			}

			$cache[$id]				=	$extension;
		}

		return $cache[$id];
	}

	/**
	 * Returns the files mimetype from extension
	 *
	 * @param bool $thumbnail
	 * @return string|null
	 */
	public function mimeType( $thumbnail = false )
	{
		static $cache							=	array();
		static $mimeTypes						=	array();

		$id										=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$mimeType							=	$this->params()->get( ( $thumbnail ? 'mimetype_thumbnail' : 'mimetype' ), null, GetterInterface::STRING );

			if ( $mimeType === null ) {
				$domain							=	$this->domain( $thumbnail );

				if ( $domain && in_array( $domain, array( 'youtube', 'youtu' ) ) && ( ! $thumbnail ) ) {
					$mimeType					=	'video/x-youtube';
				} else {
					$extension					=	$this->extension( $thumbnail );

					if ( ! isset( $mimeTypes[$extension] ) ) {
						$mimeTypes[$extension]	=	CBGallery::getMimeTypes( $extension );
					}

					$mimeType					=	$mimeTypes[$extension];
				}
			}

			$cache[$id]							=	$mimeType;
		}

		return $cache[$id];
	}

	/**
	 * Returns the files modified timestamp
	 *
	 * @param bool $thumbnail
	 * @return string
	 */
	public function modified( $thumbnail = false )
	{
		static $cache						=	array();

		$id									=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$modified						=	$this->params()->get( ( $thumbnail ? 'modified_thumbnail' : 'modified' ), null, GetterInterface::INT );

			if ( ( $modified === null ) && $this->exists( $thumbnail ) ) {
				if ( $this->domain( $thumbnail ) ) {
					try {
						$request			=	new Client();

						$header				=	$request->head( $id );

						if ( ( $header !== false ) && ( $header->getStatusCode() == 200 ) ) {
							$modified		=	$header->getHeader( 'last-modified' );
						}
					} catch( Exception $e ) {}
				} else {
					$modified				=	filemtime( $id );
				}

				if ( ! $modified ) {
					$modified				=	$this->get( 'date', null, GetterInterface::STRING );
				}

				$modified					=	Application::Date( $modified, 'UTC' )->getTimestamp();
			}

			$cache[$id]						=	$modified;
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
		if ( ( $this->get( 'type', null, GetterInterface::STRING ) != 'photos' ) && ( ! ( in_array( $this->domain(), array( 'youtube', 'youtu' ) ) || $this->get( 'thumbnail', null, GetterInterface::STRING ) ) ) ) {
			return 0;
		}

		static $cache					=	array();

		$id								=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$height						=	$this->params()->get( ( $thumbnail ? 'height_thumbnail' : 'height' ), null, GetterInterface::INT );

			if ( ( $height === null ) && $this->exists( $thumbnail ) ) {
				$height					=	0;
				$size					=	false;

				if ( $this->domain( $thumbnail ) ) {
					if ( version_compare( phpversion(), '5.4.0', '>=' ) ) {
						try {
							$request	=	new Client();

							$request	=	$request->get( $id );

							if ( ( $request !== false ) && ( $request->getStatusCode() == 200 ) ) {
								$size	=	@getimagesizefromstring( (string) $request->getBody() );
							}
						} catch( Exception $e ) {}
					}
				} else {
					$size				=	@getimagesize( $id );
				}

				if ( $size !== false ) {
					$height				=	(int) $size[1];
				}
			}

			$cache[$id]					=	$height;
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
		if ( ( $this->get( 'type', null, GetterInterface::STRING ) != 'photos' ) && ( ! ( in_array( $this->domain(), array( 'youtube', 'youtu' ) ) || $this->get( 'thumbnail', null, GetterInterface::STRING ) ) ) ) {
			return 0;
		}

		static $cache					=	array();

		$id								=	$this->path( $thumbnail );

		if ( ! isset( $cache[$id] ) ) {
			$width						=	$this->params()->get( ( $thumbnail ? 'width_thumbnail' : 'width' ), null, GetterInterface::INT );

			if ( ( $width === null ) && $this->exists( $thumbnail ) ) {
				$width					=	0;
				$size					=	false;

				if ( $this->domain( $thumbnail ) ) {
					if ( version_compare( phpversion(), '5.4.0', '>=' ) ) {
						try {
							$request	=	new Client();

							$request	=	$request->get( $id );

							if ( ( $request !== false ) && ( $request->getStatusCode() == 200 ) ) {
								$size	=	@getimagesizefromstring( (string) $request->getBody() );
							}
						} catch( Exception $e ) {}
					}
				} else {
					$size				=	@getimagesize( $id );
				}

				if ( $size !== false ) {
					$width				=	(int) $size[0];
				}
			}

			$cache[$id]					=	$width;
		}

		return $cache[$id];
	}

	/**
	 * Previews the item
	 *
	 * @param bool $thumbnail
	 * @return bool
	 */
	public function preview( $thumbnail = false )
	{
		return $this->output( true, $thumbnail );
	}

	/**
	 * Downloads the item
	 *
	 * @return bool
	 */
	public function download()
	{
		return $this->output( false, false );
	}

	/**
	 * Outputs item to header
	 *
	 * @param bool $inline
	 * @param bool $thumbnail
	 * @return bool
	 */
	private function output( $inline = false, $thumbnail = false )
	{
		if ( ! $this->get( 'id', 0, GetterInterface::INT ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$filePath			=	$this->path( $thumbnail );

		if ( $this->domain( $thumbnail ) ) {
			cbRedirect( $filePath );
		}

		if ( ! $this->exists( $thumbnail ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$fileExtension		=	$this->extension( $thumbnail );

		if ( ! $fileExtension ) {
			header( 'HTTP/1.0 406 Not Acceptable' );
			exit();
		}

		$fileName			=	$this->name( $thumbnail );

		if ( ! $fileName ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$fileMime			=	$this->mimeType( $thumbnail );
		$fileSize			=	$this->size( $thumbnail, true );
		$fileModifedTime	=	$this->modified( $thumbnail );
		$fileModifedDate	=	Application::Date( $fileModifedTime, 'UTC' )->format( 'r', true, false );
		$fileEtag			=	md5_file( $filePath );

		if ( ! Application::Cms()->getClientId() ) {
			if ( ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) && ( strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) == $fileModifedTime ) ) || isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && ( trim( $_SERVER['HTTP_IF_NONE_MATCH'] ) == $fileEtag ) ) {
				header( 'HTTP/1.1 304 Not Modified' );
				exit();
			}
		}

		while ( @ob_end_clean() );

		if ( ini_get( 'zlib.output_compression' ) ) {
			ini_set( 'zlib.output_compression', 'Off' );
		}

		if ( function_exists( 'apache_setenv' ) ) {
			apache_setenv( 'no-gzip', '1' );
		}

		header( "Content-Type: $fileMime" );
		header( 'Content-Disposition: ' . ( $inline ? 'inline' : 'attachment' ) . '; filename="' . $fileName . '"; modification-date="' . $fileModifedDate . '"; size=' . $fileSize . ';' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Pragma: private' );
		header( 'Cache-Control: max-age=86400' );
		header( "Last-Modified: $fileModifedDate" );
		header( "Etag: $fileEtag" );
		header( 'Accept-Ranges: bytes' );

		$offset				=	0;
		$length				=	$fileSize;

		if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
			if ( ! preg_match( '/^bytes=\d*-\d*(,\d*-\d*)*$/i', $_SERVER['HTTP_RANGE'] ) ) {
				header( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
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
				header( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
				header( "Content-Range: bytes */$fileSize" );
				exit();
			}

			header( 'HTTP/1.1 206 Partial Content' );
			header( "Content-Range: bytes $offset-$length/$fileSize" );
			header( "Content-Length: " . ( ( $length - $offset ) + 1 ) );
		} else {
			header( 'HTTP/1.0 200 OK' );
			header( "Content-Length: $fileSize" );
		}

		if ( ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}

		$file				=	fopen( $filePath, 'rb' );

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
	 * @param null|Gallery $gallery
	 * @return FolderTable
	 */
	public function folder( $gallery = null )
	{
		$id					=	$this->get( 'folder', 0, GetterInterface::INT );

		if ( $gallery ) {
			return $gallery->folder( $id );
		}

		static $folders		=	array();

		if ( ! isset( $folders[$id] ) ) {
			$folder			=	new FolderTable();

			$folder->load( $id );

			$folders[$id]	=	$folder;
		}

		return $folders[$id];
	}

	/**
	 * Try to discover this items type
	 *
	 * @param null|Gallery $gallery
	 * @return string
	 */
	public function discoverType( $gallery = null )
	{
		if ( Application::Cms()->getClientId() ) {
			$input					=	Application::Input();
			$files					=	$input->getNamespaceRegistry( 'files' );
		} else {
			$input					=	$this->get( '_input', new Registry(), GetterInterface::RAW );
			$files					=	$this->get( '_files', new Registry(), GetterInterface::RAW );
		}

		$type						=	$this->get( 'type', null, GetterInterface::STRING );
		$upload						=	$files->subTree( 'upload' );

		if ( $upload->get( 'name', null, GetterInterface::STRING ) ) {
			return CBGallery::getExtensionType( CBGallery::getUploadExtension( $upload ), $gallery, 'upload', false );
		} else {
			$value					=	$input->get( 'value', null, GetterInterface::STRING );

			if ( $value && ( ( $value != $this->get( 'value', null, GetterInterface::STRING ) ) || ( ! $type ) ) ) {
				$domain				=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $value, PHP_URL_HOST ) );

				if ( in_array( $domain, array( 'youtube', 'youtu' ) ) ) {
					return 'videos';
				} else {
					$link			=	CBGallery::parseUrl( $value );

					if ( $link['extension'] && ( ( $link['extension'] != $this->extension() ) || ( ! $type ) ) ) {
						return CBGallery::getExtensionType( $link['extension'], $gallery, 'link', false );
					}
				}
			}
		}

		return $type;
	}

	/**
	 * Returns items thumbnail
	 *
	 * @param Gallery $gallery
	 * @param int     $size
	 * @param bool    $background
	 * @return string
	 */
	public function thumbnail( $gallery = null, $size = 0, $background = false )
	{
		global $_CB_framework;

		$type					=	$this->get( 'type', null, GetterInterface::STRING );

		if ( $gallery ) {
			$thumbnailPath		=	$_CB_framework->pluginClassUrl( 'cbgallery', false, array( 'action' => 'item', 'func' => 'preview', 'id' => $this->get( 'id', 0, GetterInterface::INT ), 'gallery' => $gallery->id() ), 'raw', 0, true );
		} else {
			$thumbnailPath		=	$this->path( true );
		}

		if ( $this->domain( true ) ) {
			$previewPath		=	$this->path( true );
		} else {
			$previewPath		=	$thumbnailPath;
		}

		if ( $type == 'photos' ) {
			switch ( $this->params()->get( 'rotate', 0, GetterInterface::INT ) ) {
				case 90:
					$rotate		=	' galleryRotate90';
					break;
				case 180:
					$rotate		=	' galleryRotate180';
					break;
				case 270:
					$rotate		=	' galleryRotate270';
					break;
				case 360:
				default:
					$rotate		=	null;
					break;
			}

			if ( $background ) {
				return '<div class="galleryImageBackground' . $rotate . '" style="background-image: url(' . htmlspecialchars( $previewPath ) . ');"></div>';
			}

			return '<img src="' . htmlspecialchars( $previewPath ) . '" class="galleryImage cbImgPict cbThumbPict img-thumbnail' . $rotate . '" />';
		}

		if ( $this->get( 'thumbnail', null, GetterInterface::STRING ) ) {
			if ( $background ) {
				return '<div class="galleryImageBackground" style="background-image: url(' . htmlspecialchars( $previewPath ) . ');"></div>';
			}

			return '<img src="' . htmlspecialchars( $thumbnailPath ) . '" class="galleryImage cbImgPict cbThumbPict img-thumbnail" />';
		}

		if ( $type == 'files' ) {
			return '<span class="fa ' . CBGallery::getExtensionIcon( $this->extension() ) . '" style="vertical-align: middle;' . ( $size ? ' font-size: ' . ( (int) $size - 25 ) . 'px;' : null ) . '"></span>';
		} elseif ( $type == 'videos' ) {
			if ( in_array( $this->domain( true ), array( 'youtube', 'youtu' ) ) ) {
				if ( $background ) {
					return '<div class="galleryImageBackground" style="background-image: url(' . htmlspecialchars( $previewPath ) . ');"></div>';
				}

				return '<img src="' . htmlspecialchars( $previewPath ) . '" class="galleryImage cbImgPict cbThumbPict img-thumbnail" />';
			} else {
				return '<video width="100%" height="100%" style="width: 100%; height: 100%;" src="' . htmlspecialchars( $previewPath ) . '" type="' . htmlspecialchars( $this->mimeType() ) . '" preload="auto" class="galleryVideoPlayer"></video>';
			}
		}

		return '<span class="fa ' . CBGallery::getTypeIcon( $type ) . '" style="vertical-align: middle;' . ( $size ? ' font-size: ' . ( (int) $size - 25 ) . 'px;' : null ) . '"></span>';
	}
}