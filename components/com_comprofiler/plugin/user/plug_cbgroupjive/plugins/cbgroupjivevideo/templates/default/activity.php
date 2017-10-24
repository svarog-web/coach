<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Plugin\GroupJiveVideo\Table\VideoTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_groupjiveVideoActivity
{

	/**
	 * render frontend event activity
	 *
	 * @param CB\Plugin\Activity\Table\ActivityTable $row
	 * @param string                                 $title
	 * @param string                                 $message
	 * @param CB\Plugin\Activity\Activity            $stream
	 * @param VideoTable                             $video
	 * @param cbPluginHandler                        $plugin
	 * @return string
	 */
	static function showVideoActivity( $row, &$title, &$message, $stream, $video, $plugin )
	{
		global $_CB_framework;

		$title				=	CBTxt::T( 'GROUP_VIDEO_ACTIVITY_TITLE', 'published a video in [group]', array( '[group]' => '<strong><a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $video->group()->get( 'id' ) ) ) . '">' . htmlspecialchars( CBTxt::T( $video->group()->get( 'name' ) ) ) . '</a></strong>' ) );

		$return				=	'<div class="gjVideoActivity">'
							.		'<div class="gjGroupVideoRow gjCanvasBox cbCanvasBox cbCanvasBoxInline">'
							.			'<div class="gjCanvasBoxTop cbCanvasBoxTop">';

		if ( $video->mimeType() == 'video/youtube' ) {
			if ( preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:be/))([A-Za-z0-9_-]+)%', $video->get( 'url' ), $matches ) ) {
				$return		.=				'<iframe width="100%" height="360" src="https://www.youtube.com/embed/' . htmlspecialchars( $matches[1] ) . '" frameborder="0" allowfullscreen class="gjVideoPlayer"></iframe>';
			}
		} else {
			$return			.=				'<video width="100%" height="100%" style="width: 100%; height: 100%;" src="' . htmlspecialchars( $video->get( 'url' ) ) . '" type="' . htmlspecialchars( $video->mimeType() ) . '" controls="controls" preload="auto" class="gjVideoPlayer"></video>';
		}

		$return				.=			'</div>'
							.		'</div>'
							.	'</div>';

		return $return;
	}
}