<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveVideo\CBGroupJiveVideo;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

class CBplug_cbgroupjivevideo extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_PLUGINS;

		$format					=	$this->input( 'format', null, GetterInterface::STRING );

		if ( $format != 'raw' ) {
			outputCbJs();
			outputCbTemplate();
		}

		$action					=	$this->input( 'action', null, GetterInterface::STRING );
		$function				=	$this->input( 'func', null, GetterInterface::STRING );
		$id						=	$this->input( 'id', 0, GetterInterface::INT );
		$user					=	CBuser::getMyUserDataInstance();

		if ( $format != 'raw' ) {
			ob_start();
		}

		switch ( $action ) {
			case 'video':
				switch ( $function ) {
					case 'publish':
						$this->stateVideo( 1, $id, $user );
						break;
					case 'unpublish':
						$this->stateVideo( 0, $id, $user );
						break;
					case 'delete':
						$this->deleteVideo( $id, $user );
						break;
					case 'new':
						$this->showVideoEdit( null, $user );
						break;
					case 'edit':
						$this->showVideoEdit( $id, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveVideoEdit( $id, $user );
						break;
				}
				break;
		}

		if ( $format != 'raw' ) {
			$html				=	ob_get_contents();
			ob_end_clean();

			static $gjClass		=	null;

			if ( $gjClass === null ) {
				$gjPlugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
				$gjParams		=	$_PLUGINS->getPluginParams( $gjPlugin );
				$gjClass		=	$gjParams->get( 'general_class', '', GetterInterface::STRING );
			}

			$return				=	'<div class="cbGroupJive' . ( $gjClass ? ' ' . htmlspecialchars( $gjClass ) : null ) . '">'
								.		'<div class="cbGroupJiveInner">'
								.			$html
								.		'</div>'
								.	'</div>';

			echo $return;
		}
	}

	/**
	 * prepare frontend video edit render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showVideoEdit( $id, $user )
	{
		global $_CB_framework;

		$row							=	CBGroupJiveVideo::getVideo( (int) $id );
		$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$groupId						=	$this->input( 'group', null, GetterInterface::INT );

		if ( $groupId === null ) {
			$group						=	$row->group();
		} else {
			$group						=	CBGroupJive::getGroup( $groupId );
		}

		$returnUrl						=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		} elseif ( ! $isModerator ) {
			if ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'video' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to publish a video in this group.' ), 'error' );
			} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $group ) < 2 ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this video.' ), 'error' );
			}
		}

		CBGroupJive::getTemplate( 'video_edit', true, true, $this->element );

		$input							=	array();

		$publishedTooltip				=	cbTooltip( null, CBTxt::T( 'Select publish state of this video. Unpublished videos will not be visible to the public.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['published']				=	moscomprofilerHTML::yesnoSelectList( 'published', 'class="form-control"' . $publishedTooltip, (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) );

		$titleTooltup					=	cbTooltip( null, CBTxt::T( 'Optionally input a video title to display instead of url.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['title']					=	'<input type="text" id="title" name="title" value="' . htmlspecialchars( $this->input( 'post/title', $row->get( 'title' ), GetterInterface::STRING ) ) . '" class="form-control" size="35"' . $titleTooltup . ' />';

		$urlTooltip						=	cbTooltip( null, CBTxt::T( 'Input the URL to the video to publish.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['url']					=	'<input type="text" id="url" name="url" value="' . htmlspecialchars( $this->input( 'post/url', $row->get( 'url' ), GetterInterface::STRING ) ) . '" class="form-control required" size="45"' . $urlTooltip . ' />';

		$input['url_limits']			=	CBTxt::T( 'GROUP_VIDEO_LIMITS_EXT', 'Your url must be of [ext] type.', array( '[ext]' => implode( ', ', array( 'youtube', 'mp4', 'ogv', 'ogg', 'webm', 'm4v' ) ) ) );

		$captionTooltip					=	cbTooltip( null, CBTxt::T( 'Optionally input a video caption.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['caption']				=	'<textarea id="caption" name="caption" class="form-control" cols="40" rows="5"' . $captionTooltip . '>' . htmlspecialchars( $this->input( 'post/caption', $row->get( 'caption' ), GetterInterface::STRING ) ) . '</textarea>';

		$ownerTooltip					=	cbTooltip( null, CBTxt::T( 'Input the video owner id. Video owner determines the creator of the video specified as User ID.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['user_id']				=	'<input type="text" id="user_id" name="user_id" value="' . (int) $this->input( 'post/user_id', $this->input( 'user', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ), GetterInterface::INT ) . '" class="digits required form-control" size="6"' . $ownerTooltip . ' />';

		HTML_groupjiveVideoEdit::showVideoEdit( $row, $input, $group, $user, $this );
	}

	/**
	 * save video
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveVideoEdit( $id, $user )
	{
		global $_CB_framework, $_PLUGINS;

		$row					=	CBGroupJiveVideo::getVideo( (int) $id );
		$isModerator			=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$groupId				=	$this->input( 'group', null, GetterInterface::INT );

		if ( $groupId === null ) {
			$group				=	$row->group();
		} else {
			$group				=	CBGroupJive::getGroup( $groupId );
		}

		$returnUrl				=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		} elseif ( ! $isModerator ) {
			if ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'video' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to publish a video in this group.' ), 'error' );
			} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $group ) < 2 ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this video.' ), 'error' );
			}
		}

		if ( $isModerator ) {
			$row->set( 'user_id', (int) $this->input( 'post/user_id', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ) );
		} else {
			$row->set( 'user_id', (int) $row->get( 'user_id', $user->get( 'id' ) ) );
		}

		$canModerate			=	( CBGroupJive::getGroupStatus( $user, $group ) >= 2 );

		$row->set( 'published', ( $isModerator || $canModerate || ( $row->get( 'id' ) && ( $row->get( 'published' ) != -1 ) ) || ( $group->params()->get( 'video', 1 ) != 2 ) ? (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) : -1 ) );
		$row->set( 'group', (int) $group->get( 'id' ) );
		$row->set( 'title', $this->input( 'post/title', $row->get( 'title' ), GetterInterface::STRING ) );
		$row->set( 'url', $this->input( 'post/url', $row->get( 'url' ), GetterInterface::STRING ) );
		$row->set( 'caption', $this->input( 'post/caption', $row->get( 'caption' ), GetterInterface::STRING ) );

		if ( ( ! $isModerator ) && $this->params->get( 'groups_video_captcha', 0 ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

			if ( $_PLUGINS->is_errors() ) {
				$row->setError( $_PLUGINS->getErrorMSG() );
			}
		}

		$new					=	( $row->get( 'id' ) ? false : true );

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_VIDEO_FAILED_TO_SAVE', 'Video failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showVideoEdit( $id, $user );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_VIDEO_FAILED_TO_SAVE', 'Video failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showVideoEdit( $id, $user );
			return;
		}

		if ( $new ) {
			$extras				=	array(	'video_id'		=>	(int) $row->get( 'id' ),
											'video_title'	=>	htmlspecialchars( ( $row->get( 'title' ) ? $row->get( 'title' ) : $row->name() ) ),
											'video_caption'	=>	htmlspecialchars( $row->get( 'caption' ) ),
											'video_size'	=>	$row->size(),
											'video_type'	=>	$row->mimeType(),
											'video_url'		=>	htmlspecialchars( $row->get( 'url' ) ),
											'video_date'	=>	cbFormatDate( $row->get( 'date' ), true, false, CBTxt::T( 'GROUP_VIDEO_DATE_FORMAT', 'M j, Y' ) ),
											'video'			=>	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ), 'tab' => 'grouptabvideo' ) ) . '">' . htmlspecialchars( ( $row->get( 'title' ) ? $row->get( 'title' ) : $row->name() ) ) . '</a>' );

			if ( $row->get( 'published' ) == 1 ) {
				CBGroupJive::sendNotifications( 'video_new', CBTxt::T( 'New group video' ), CBTxt::T( '[user] has published the video [video] in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 1, $extras );
			} elseif ( ( $row->get( 'published' ) == -1 ) && ( $row->group()->params()->get( 'video', 1 ) == 2 ) ) {
				CBGroupJive::sendNotifications( 'video_approve', CBTxt::T( 'New group video awaiting approval' ), CBTxt::T( '[user] has published the video [video] in the group [group] and is awaiting approval!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 2, $extras );

				cbRedirect( $returnUrl, CBTxt::T( 'Video published successfully and awaiting approval!' ) );
			}

			cbRedirect( $returnUrl, CBTxt::T( 'Video published successfully!' ) );
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Video saved successfully!' ) );
		}
	}

	/**
	 * set video publish state status
	 *
	 * @param int       $state
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function stateVideo( $state, $id, $user )
	{
		global $_CB_framework;

		$row				=	CBGroupJiveVideo::getVideo( (int) $id );
		$returnUrl			=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 2 ) {
					if ( ( $user->get( 'id' ) == $row->get( 'user_id' ) ) && ( $row->get( 'published' ) == -1 ) && ( $row->group()->params()->get( 'video', 1 ) == 2 ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'Your video is awaiting approval.' ), 'error' );
					} elseif ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to publish or unpublish this video.' ), 'error' );
					}
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Video does not exist.' ), 'error' );
		}

		$currentState		=	(int) $row->get( 'published' );

		$row->set( 'published', (int) $state );

		if ( $row->getError() || ( ! $row->store() ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_VIDEO_STATE_FAILED_TO_SAVE', 'Video state failed to saved. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $state && ( $currentState == -1 ) ) {
			$extras			=	array(	'video_id'		=>	(int) $row->get( 'id' ),
										'video_title'	=>	htmlspecialchars( ( $row->get( 'title' ) ? $row->get( 'title' ) : $row->name() ) ),
										'video_caption'	=>	htmlspecialchars( $row->get( 'caption' ) ),
										'video_size'	=>	$row->size(),
										'video_type'	=>	$row->mimeType(),
										'video_url'		=>	htmlspecialchars( $row->get( 'url' ) ),
										'video_date'	=>	cbFormatDate( $row->get( 'date' ), true, false, CBTxt::T( 'GROUP_VIDEO_DATE_FORMAT', 'M j, Y' ) ),
										'video'			=>	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ), 'tab' => 'grouptabvideo' ) ) . '">' . htmlspecialchars( ( $row->get( 'title' ) ? $row->get( 'title' ) : $row->name() ) ) . '</a>' );

			if ( $row->get( 'user_id' ) != $user->get( 'id' ) ) {
				CBGroupJive::sendNotification( 4, $user, (int) $row->get( 'user_id' ), CBTxt::T( 'Video publish request accepted' ), CBTxt::T( 'Your video [video] publish request in the group [group] has been accepted!' ), $row->group(), $extras );
			}

			CBGroupJive::sendNotifications( 'video_new', CBTxt::T( 'New group video' ), CBTxt::T( '[user] has published the video [video] in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 1, $extras );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Video state saved successfully!' ) );
	}

	/**
	 * delete video
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteVideo( $id, $user )
	{
		global $_CB_framework;

		$row			=	CBGroupJiveVideo::getVideo( (int) $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 2 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to delete this video.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Video does not exist.' ), 'error' );
		}

		if ( ! $row->canDelete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_VIDEO_FAILED_TO_DELETE', 'Video failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_VIDEO_FAILED_TO_DELETE', 'Video failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Video deleted successfully!' ) );
	}
}