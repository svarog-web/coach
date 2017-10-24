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
use CB\Plugin\GroupJiveWall\CBGroupJiveWall;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

class CBplug_cbgroupjivewall extends cbPluginHandler
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
			case 'wall':
				switch ( $function ) {
					case 'publish':
						$this->stateWall( 1, $id, $user );
						break;
					case 'unpublish':
						$this->stateWall( 0, $id, $user );
						break;
					case 'delete':
						$this->deleteWall( $id, $user );
						break;
					case 'new':
						$this->showWallEdit( null, $user );
						break;
					case 'edit':
						$this->showWallEdit( $id, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveWallEdit( $id, $user );
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
	 * prepare frontend wall edit render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showWallEdit( $id, $user )
	{
		global $_CB_framework;

		$row					=	CBGroupJiveWall::getPost( (int) $id );
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
			if ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'wall' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to post in this group.' ), 'error' );
			} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $group ) < 2 ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this post.' ), 'error' );
			}
		}

		CBGroupJive::getTemplate( 'wall_edit', true, true, $this->element );

		$input					=	array();

		$publishedTooltip		=	cbTooltip( null, CBTxt::T( 'Select publish state of this post. Unpublished posts will not be visible to the public.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['published']		=	moscomprofilerHTML::yesnoSelectList( 'published', 'class="form-control"' . $publishedTooltip, (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) );

		$postTooltip			=	cbTooltip( null, CBTxt::T( 'Input the post to share.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['post']			=	'<textarea id="post" name="post" class="form-control required" cols="55" rows="8"' . $postTooltip . '>' . htmlspecialchars( $this->input( 'post/post', $row->get( 'post' ), GetterInterface::HTML ) ) . '</textarea>';

		$ownerTooltip			=	cbTooltip( null, CBTxt::T( 'Input the post owner id. Post owner determines the creator of the post specified as User ID.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['user_id']		=	'<input type="text" id="user_id" name="user_id" value="' . (int) $this->input( 'post/user_id', $this->input( 'user', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ), GetterInterface::INT ) . '" class="digits required form-control" size="6"' . $ownerTooltip . ' />';

		HTML_groupjiveWallEdit::showWallEdit( $row, $input, $group, $user, $this );
	}

	/**
	 * save wall
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveWallEdit( $id, $user )
	{
		global $_CB_framework;

		$row					=	CBGroupJiveWall::getPost( (int) $id );
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
			if ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'wall' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to post in this group.' ), 'error' );
			} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $group ) < 2 ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this post.' ), 'error' );
			}
		}

		$replyId				=	(int) $this->input( 'reply', null, GetterInterface::INT );

		if ( $replyId ) {
			$reply				=	CBGroupJiveWall::getPost( (int) $replyId );

			if ( ! $reply->get( 'id' ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Reply does not exist.' ), 'error' );
			}

			$row->set( 'reply', (int) $reply->get( 'id' ) );
		}

		$canModerate			=	( CBGroupJive::getGroupStatus( $user, $group ) >= 2 );

		if ( $isModerator ) {
			$row->set( 'user_id', (int) $this->input( 'post/user_id', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ) );
		} else {
			$row->set( 'user_id', (int) $row->get( 'user_id', $user->get( 'id' ) ) );
		}

		$row->set( 'published', ( $isModerator || $canModerate || ( $row->get( 'id' ) && ( $row->get( 'published' ) != -1 ) ) || ( $group->params()->get( 'wall', 1 ) != 2 ) ? (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) : -1 ) );
		$row->set( 'group', (int) $group->get( 'id' ) );

		$postLimit				=	( $isModerator ? 0 : (int) $this->params->get( 'groups_wall_character_limit', 400 ) );
		$post					=	trim( $this->input( 'post', $row->get( 'post', null, GetterInterface::HTML ), GetterInterface::HTML ) );

		// Remove duplicate spaces:
		$post					=	preg_replace( '/ {2,}/i', ' ', $post );
		// Remove duplicate tabs:
		$post					=	preg_replace( '/\t{2,}/i', "\t", $post );
		// Remove duplicate linebreaks:
		$post					=	preg_replace( '/(\r\n|\r|\n){2,}/i', '$1', $post );

		if ( $postLimit && ( cbutf8_strlen( $post ) > $postLimit ) ) {
			$post				=	cbutf8_substr( $post, 0, $postLimit );
		}

		$row->set( 'post', $post );

		$new					=	( $row->get( 'id' ) ? false : true );

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_POST_FAILED_TO_SAVE', 'Post failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showWallEdit( $id, $user );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_POST_FAILED_TO_SAVE', 'Post failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showWallEdit( $id, $user );
			return;
		}

		if ( $new ) {
			$extra				=	array(	'wall_id'	=>	(int) $row->get( 'id' ),
											'wall_post'	=>	$row->post()
										);

			if ( $row->get( 'published' ) == 1 ) {
				if ( $row->reply()->get( 'id' ) ) {
					CBGroupJive::sendNotifications( 'wall_reply', CBTxt::T( 'New group post reply' ), CBTxt::T( '[user] has posted a reply on the wall in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), (int) $row->reply()->get( 'user_id' ), array( $user->get( 'id' ) ), 1, $extra );
				} else {
					CBGroupJive::sendNotifications( 'wall_new', CBTxt::T( 'New group post' ), CBTxt::T( '[user] has posted on the wall in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 1, $extra );
				}
			} elseif ( ( $row->get( 'published' ) == -1 ) && ( $row->group()->params()->get( 'wall', 1 ) == 2 ) ) {
				CBGroupJive::sendNotifications( 'wall_approve', CBTxt::T( 'New group post awaiting approval' ), CBTxt::T( '[user] has posted on the wall in the group [group] and is awaiting approval!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 2, $extra );

				cbRedirect( $returnUrl, CBTxt::T( 'Posted successfully and awaiting approval!' ) );
			}

			cbRedirect( $returnUrl, CBTxt::T( 'Posted successfully!' ) );
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Post saved successfully!' ) );
		}
	}

	/**
	 * set wall publish state status
	 *
	 * @param int       $state
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function stateWall( $state, $id, $user )
	{
		global $_CB_framework;

		$row				=	CBGroupJiveWall::getPost( (int) $id );
		$returnUrl			=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 2 ) {
					if ( ( $user->get( 'id' ) == $row->get( 'user_id' ) ) && ( $row->get( 'published' ) == -1 ) && ( $row->group()->params()->get( 'wall', 1 ) == 2 ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'Your post is awaiting approval.' ), 'error' );
					} elseif ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to publish or unpublish this post.' ), 'error' );
					}
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Post does not exist.' ), 'error' );
		}

		$currentState		=	(int) $row->get( 'published' );

		$row->set( 'published', (int) $state );

		if ( $row->getError() || ( ! $row->store() ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_POST_STATE_FAILED_TO_SAVE', 'Post state failed to saved. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $state && ( $currentState == -1 ) ) {
			$extra			=	array(	'wall_id'	=>	(int) $row->get( 'id' ),
										'wall_post'	=>	$row->post()
									);

			if ( $row->get( 'user_id' ) != $user->get( 'id' ) ) {
				CBGroupJive::sendNotification( 4, $user, (int) $row->get( 'user_id' ), CBTxt::T( 'Wall post request accepted' ), CBTxt::T( 'Your wall post request in the group [group] has been accepted!' ), $row->group() );
			}

			if ( $row->reply()->get( 'id' ) ) {
				CBGroupJive::sendNotifications( 'wall_reply', CBTxt::T( 'New group post reply' ), CBTxt::T( '[user] has posted a reply on the wall in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), (int) $row->reply()->get( 'user_id' ), array( $user->get( 'id' ) ), 1, $extra );
			} else {
				CBGroupJive::sendNotifications( 'wall_new', CBTxt::T( 'New group post' ), CBTxt::T( '[user] has posted on the wall in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 1, $extra );
			}
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Post state saved successfully!' ) );
	}

	/**
	 * delete wall
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteWall( $id, $user )
	{
		global $_CB_framework;

		$row			=	CBGroupJiveWall::getPost( (int) $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 2 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to delete this post.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Post does not exist.' ), 'error' );
		}

		if ( ! $row->canDelete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_POST_FAILED_TO_DELETE', 'Post failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_POST_FAILED_TO_DELETE', 'Post failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Post deleted successfully!' ) );
	}

}