<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJiveFile\Table\FileTable;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Activity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveFileActivity
{

	/**
	 * render frontend file activity
	 *
	 * @param ActivityTable   $row
	 * @param string          $title
	 * @param string          $message
	 * @param Activity        $stream
	 * @param FileTable       $file
	 * @param cbPluginHandler $plugin
	 * @return string
	 */
	static function showFileActivity( $row, &$title, &$message, $stream, $file, $plugin )
	{
		global $_CB_framework;

		$title					=	CBTxt::T( 'GROUP_FILE_ACTIVITY_TITLE', 'uploaded a file in [group]', array( '[group]' => '<strong><a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $file->group()->get( 'id' ) ) ) . '">' . htmlspecialchars( CBTxt::T( $file->group()->get( 'name' ) ) ) . '</a></strong>' ) );

		$return					=	'<div class="gjFileActivity">'
								.		'<table class="gjGroupFileRows table table-hover table-responsive">'
								.			'<thead>'
								.				'<tr>'
								.					'<th colspan="2">&nbsp;</th>'
								.					'<th style="width: 15%;" class="text-center">' . CBTxt::T( 'Type' ) . '</th>'
								.					'<th style="width: 15%;" class="text-left">' . CBTxt::T( 'Size' ) . '</th>'
								.				'</tr>'
								.			'</thead>'
								.			'<tbody>';

		$extension				=	null;
		$size					=	0;
		$name					=	( $file->get( 'title' ) ? htmlspecialchars( $file->get( 'title' ) ) : $file->name() );
		$item					=	$name;

		if ( $file->exists() ) {
			$downloadPath		=	$_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'download', 'id' => (int) $file->get( 'id' ) ), 'raw', 0, true );
			$extension			=	$file->extension();
			$size				=	$file->size();

			switch ( $extension ) {
				case 'txt':
				case 'pdf':
				case 'jpg':
				case 'jpeg':
				case 'png':
				case 'gif':
				case 'js':
				case 'css':
				case 'mp4':
				case 'mp3':
				case 'wav':
					$item		=	'<a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'file', 'func' => 'show', 'id' => (int) $file->get( 'id' ) ), 'raw', 0, true ) . '" target="_blank">'
								.		$item
								.	'</a>';
					break;
				default:
					$item		=	'<a href="' . $downloadPath . '" target="_blank">'
								.		$item
								.	'</a>';
					break;
			}

			$download			=	'<a href="' . $downloadPath . '" target="_blank" title="' . htmlspecialchars( CBTxt::T( 'Click to Download' ) ) . '" class="gjGroupDownloadIcon btn btn-xs btn-default">'
								.		'<span class="fa fa-download"></span>'
								.	'</a>';
		} else {
			$download			=	'<button type="button" class="gjButton gjButtonDownloadFile btn btn-xs btn-default disabled">'
								.		'<span class="fa fa-download"></span>'
								.	'</button>';
		}

		if ( $file->get( 'description' ) ) {
			$item				.=	' ' . cbTooltip( 1, $file->get( 'description' ), $name, 400, null, '<span class="fa fa-info-circle text-muted"></span>' );
		}

		$return					.=				'<tr>'
								.					'<td style="width: 1%;" class="text-center">' . $download . '</td>'
								.					'<td style="width: 45%;" class="gjGroupFileItem text-left">' . $item . '</td>'
								.					'<td style="width: 15%;" class="text-center"><span class="gjGroupFileTypeIcon fa fa-' . $file->icon() . '" title="' . htmlspecialchars( ( $extension ? strtoupper( $extension ) : CBTxt::T( 'Unknown' ) ) ) . '"></span></td>'
								.					'<td style="width: 15%;" class="text-left">' . $size . '</td>'
								.				'</tr>'
								.			'</tbody>'
								.		'</table>'
								.	'</div>';

		return $return;
	}
}