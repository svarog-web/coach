<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Input\Get;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\Activity\ActivityInterface;
use CB\Plugin\Activity\CommentsInterface;
use CB\Plugin\Activity\TagsInterface;
use CB\Plugin\Activity\StreamInterface;
use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Table\HiddenTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Comments;
use CB\Plugin\Activity\Tags;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

class CBplug_cbactivity extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 * @return string                HTML
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_CB_framework;

		$format						=	$this->input( 'format', null, GetterInterface::STRING );
		$raw						=	( $format == 'raw' );

		if ( ! $raw ) {
			outputCbJs();
			outputCbTemplate();
		}

		$action						=	null;
		$function					=	null;
		$id							=	null;
		$viewer						=	CBuser::getMyUserDataInstance();
		$user						=	$viewer;
		$stream						=	null;
		$inline						=	false;
		$data						=	true;

		if ( isset( $postdata['stream'] ) && ( $postdata['stream'] instanceof StreamInterface ) ) {
			$stream					=	$postdata['stream'];

			if ( $stream instanceof ActivityInterface ) {
				$action				=	'activity';
				$function			=	'show';
			} elseif ( $stream instanceof CommentsInterface ) {
				$action				=	'comments';
				$function			=	'show';
			} elseif ( $stream instanceof TagsInterface ) {
				$action				=	'tags';
				$function			=	'show';
			}

			if ( isset( $postdata['inline'] ) ) {
				$inline				=	$postdata['inline'];
			}

			if ( isset( $postdata['data'] ) ) {
				$data				=	$postdata['data'];
			}

			$user					=	$stream->user();
		} else {
			$action					=	$this->input( 'action', null, GetterInterface::STRING );
			$function				=	$this->input( 'func', null, GetterInterface::STRING );

			if ( $action == 'recentactivity' ) {
				$action				=	'activity';
				$function			=	'recent';
			} elseif ( $action == 'myactivity' ) {
				$action				=	'activity';
				$function			=	'my';
			} elseif ( $action == 'hiddenactivity' ) {
				$action				=	'hidden';
				$function			=	'activity';
			} elseif ( $action == 'hiddencomments' ) {
				$action				=	'hidden';
				$function			=	'comments';
			}

			if ( ( $action == 'activity' ) || ( $function == 'activity' ) ) {
				$stream				=	new Activity();
			} elseif ( ( $action == 'comments' ) || ( $function == 'comments' ) ) {
				$stream				=	new Comments();
			} elseif ( ( $action == 'tags' ) || ( $function == 'tags' ) ) {
				$stream				=	new Tags();
			}

			if ( $stream && $raw ) {
				$token				=	$this->input( 'token', null, GetterInterface::STRING );

				$post				=	new Registry( base64_decode( $this->input( 'stream', null, GetterInterface::BASE64 ) ) );

				$source				=	$post->get( 'source', null, GetterInterface::STRING );
				$userId				=	$post->get( 'user', null, GetterInterface::INT );
				$direction			=	$post->get( 'direction', null, GetterInterface::INT );

				if ( $source !== null ) {
					$stream->source( $source );
				}

				if ( $userId !== null ) {
					$user			=	CBuser::getUserDataInstance( (int) $userId );

					$stream->user( $user );
				}

				if ( ! ( $stream instanceof TagsInterface ) ) {
					if ( $direction !== null ) {
						$stream->direction( $direction );
					}
				}

				$stream->load( $post );

				if ( ( $stream->token() != $token ) || ( ! $token ) ) {
					header( 'HTTP/1.0 401 Unauthorized' );
					exit();
				}

				$id					=	$stream->get( 'id', null, GetterInterface::INT );
			}
		}

		if ( $stream && ( ! ( ( $stream instanceof CommentsInterface ) || ( $stream instanceof TagsInterface ) ) ) ) {
			$hashtag				=	$this->input( 'hashtag', null, GetterInterface::STRING );

			if ( $hashtag !== null ) {
				$stream->set( 'filter', '#' . $hashtag );
			}
		}

		if ( ! $raw ) {
			ob_start();
		}

		switch ( $action ) {
			case 'comments':
				if ( ! $stream ) {
					if ( $raw ) {
						header( 'HTTP/1.0 401 Unauthorized' );
						exit();
					} else {
						cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
					}
				}

				switch ( $function ) {
					case 'new':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->saveComment( null, $stream, $user, $viewer );
						break;
					case 'save':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->saveComment( $id, $stream, $user, $viewer );
						break;
					case 'delete':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->deleteComment( $id, $stream, $user, $viewer );
						break;
					case 'hide':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->hideComment( $id, $stream, $user, $viewer );
						break;
					case 'unhide':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->unhideComment( $id, $stream, $user, $viewer );
						break;
					case 'load':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->showComments( $id, $stream, 3, true, $user, $viewer );
						break;
					case 'show':
					default:
						if ( isset( $postdata['stream'] ) && ( $postdata['stream'] instanceof CommentsInterface ) ) {
							$this->showComments( $id, $stream, ( $inline ? 2 : 0 ), $data, $user, $viewer );
						} else {
							$this->showComments( $id, $stream, ( $inline ? 2 : ( $raw ? 1 : 0 ) ), true, $user, $viewer );
						}
						break;
				}
				break;
			case 'activity':
				if ( ! $stream ) {
					if ( $raw ) {
						header( 'HTTP/1.0 401 Unauthorized' );
						exit();
					} else {
						cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
					}
				}

				switch ( $function ) {
					case 'new':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->saveActivity( null, $stream, $user, $viewer );
						break;
					case 'save':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->saveActivity( $id, $stream, $user, $viewer );
						break;
					case 'delete':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->deleteActivity( $id, $stream, $user, $viewer );
						break;
					case 'hide':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->hideActivity( $id, $stream, $user, $viewer );
						break;
					case 'unhide':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->unhideActivity( $id, $stream, $user, $viewer );
						break;
					case 'load':
						if ( ! $raw ) {
							cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
						}

						$this->showActivity( $id, $stream, 3, true, $user, $viewer );
						break;
					case 'recent':
						$stream->source( 'recent' );

						$menu				=	JFactory::getApplication()->getMenu()->getActive();

						if ( $menu && isset( $menu->id ) ) {
							CBActivity::loadStreamDefaults( $stream, $menu->params, 'activity_' );
						}

						$this->showActivity( $id, $stream, ( $raw ? 1 : 0 ), true, $user, $viewer );

						$_CB_framework->setMenuMeta();
						break;
					case 'my':
						$tab				=	new TabTable();

						$tab->load( array( 'pluginclass' => 'cbactivityTab' ) );

						if ( ! ( $tab->get( 'enabled' ) && CBActivity::canAccess( (int) $tab->get( 'viewaccesslevel' ), (int) $viewer->get( 'id' ) ) ) ) {
							if ( $raw ) {
								header( 'HTTP/1.0 401 Unauthorized' );
								exit();
							} else {
								cbRedirect( $_CB_framework->userProfileUrl( (int) $user->get( 'id' ), false, 'cbactivityTab' ), CBTxt::T( 'Not authorized.' ), 'error' );
							}
						}

						if ( ! ( $tab->params instanceof ParamsInterface ) ) {
							$tab->params	=	new Registry( $tab->params );
						}

						$stream->source( 'profile' );

						CBActivity::loadStreamDefaults( $activity, $tab->params, 'tab_activity_' );

						$this->showActivity( $id, $stream, ( $raw ? 1 : 0 ), true, $user, $viewer );

						$_CB_framework->setMenuMeta();
						break;
					case 'show':
					default:
						if ( isset( $postdata['stream'] ) && ( $postdata['stream'] instanceof ActivityInterface ) ) {
							$this->showActivity( $id, $stream, ( $inline ? 2 : 0 ), $data, $user, $viewer );
						} else {
							$this->showActivity( $id, $stream, ( $inline ? 2 : ( $raw ? 1 : 0 ) ), true, $user, $viewer );
						}
						break;
				}
				break;
			case 'hidden':
				if ( ! $stream ) {
					if ( $raw ) {
						header( 'HTTP/1.0 401 Unauthorized' );
						exit();
					} else {
						cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
					}
				}

				switch ( $function ) {
					case 'users':
						break;
					case 'types':
						break;
					case 'comments':
						$stream->source( 'hidden' );

						$stream->set( 'create_access', -1 );
						$stream->set( 'replies', 0 );

						$this->showComments( $id, $stream, ( $raw ? 1 : 0 ), true, $user, $viewer );
						break;
					case 'activity':
						$stream->source( 'hidden' );

						$stream->set( 'create_access', -1 );
						$stream->set( 'comments', 0 );

						$this->showActivity( $id, $stream, ( $raw ? 1 : 0 ), true, $user, $viewer );
						break;
				}

				$_CB_framework->setMenuMeta();
				break;
			case 'tags':
				if ( ! $stream ) {
					if ( $raw ) {
						header( 'HTTP/1.0 401 Unauthorized' );
						exit();
					} else {
						cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
					}
				}

				switch ( $function ) {
					case 'show':
					default:
						if ( isset( $postdata['stream'] ) && ( $postdata['stream'] instanceof TagsInterface ) ) {
							$this->showTags( $id, $stream, ( $inline ? 2 : 0 ), $data, $user, $viewer );
						} else {
							$this->showTags( $id, $stream, ( $inline ? 2 : ( $raw ? 1 : 0 ) ), true, $user, $viewer );
						}
						break;
				}
				break;
			case 'cleanup':
				if ( ( ! $raw ) || ( $this->input( 'token', null, GetterInterface::STRING ) != md5( $_CB_framework->getCfg( 'secret' ) ) ) ) {
					header( 'HTTP/1.0 401 Unauthorized' );
					exit();
				}

				$this->cleanUp();
				break;
		}

		if ( ! $raw ) {
			$html						=	ob_get_contents();
			ob_end_clean();

			if ( ! $inline ) {
				$class					=	$this->params->get( 'general_class', null );

				$html					=	'<div id="cbActivity" class="cbActivity' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
										.		'<div id="cbActivityInner" class="cbActivityInner">'
										.			$html
										.		'</div>'
										.	'</div>';
			}

			echo $html;
		}
	}

	/**
	 * Prunes old activity entries
	 */
	private function cleanUp()
	{
		global $_CB_database, $_CB_framework;

		$query		=	'SELECT *'
					.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
					.	"\n WHERE " . $_CB_database->NameQuote( 'date' ) . " <= " . $_CB_database->Quote( $_CB_framework->getUTCDate( 'Y-m-d H:i:s', $this->params->get( 'cleanup_duration', '-1 YEAR' ) ) );
		$_CB_database->setQuery( $query );
		$rows		=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\ActivityTable', array( $_CB_database ) );

		/** @var ActivityTable[] $rows */
		foreach ( $rows as $row ) {
			$row->delete();
		}
	}

	/**
	 * Displays comments stream
	 *
	 * @param int       $id
	 * @param Comments  $stream
	 * @param int       $output
	 * @param bool      $data
	 * @param UserTable $user
	 * @param UserTable $viewer
	 */
	private function showComments( $id, $stream, $output, $data, $user, $viewer )
	{
		CBActivity::getTemplate( 'comments', false, false );

		if ( $id ) {
			$stream->set( 'id', $id );
			$stream->set( 'limitstart', 0 );
			$stream->set( 'limit', 0 );
			$stream->set( 'paging', 0 );

			$rows			=	$stream->data();
		} else {
			if ( $data ) {
				$count		=	$stream->data( true );

				if ( ! $count ) {
					$rows	=	array();
				} else {
					$rows	=	$stream->data();

					if ( $count <= ( $stream->get( 'limitstart' ) + $stream->get( 'limit' ) ) ) {
						$stream->set( 'paging', 0 );
					}
				}
			} else {
				$rows		=	array();
			}
		}

		if ( $rows ) {
			if ( $stream->get( 'replies' ) ) {
				CBActivity::preFetchComments( $rows, 'comment' );
			}

			CBActivity::preFetchUsers( $rows );
		}

		echo HTML_cbactivityComments::showComments( $rows, $stream, $output, $user, $viewer, $this );
	}

	/**
	 * Saves comment
	 *
	 * @param int       $id
	 * @param Comments  $stream
	 * @param UserTable $user
	 * @param UserTable $viewer
	 */
	private function saveComment( $id, $stream, $user, $viewer )
	{
		global $_CB_framework, $_PLUGINS;

		$cbModerator		=	CBActivity::isModerator( (int) $viewer->get( 'id' ) );

		CBActivity::getTemplate( 'comments', false, false );

		$row				=	new CommentTable();

		$row->load( (int) $id );

		$canAccess			=	false;

		if ( ! $row->get( 'id' ) ) {
			if ( CBActivity::canCreate( $user, $viewer, $stream ) ) {
				$canAccess	=	true;
			}
		} elseif ( $cbModerator || ( $viewer->get( 'id' ) == $row->get( 'user_id' ) ) ) {
			$canAccess		=	true;
		}

		if ( ! $canAccess ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$messageLimit		=	( $cbModerator ? 0 : (int) $stream->get( 'message_limit', 400 ) );
		$message			=	trim( $this->input( 'message', $row->get( 'message', null, GetterInterface::HTML ), GetterInterface::HTML ) );

		// Remove duplicate spaces:
		$message			=	preg_replace( '/ {2,}/i', ' ', $message );
		// Remove duplicate tabs:
		$message			=	preg_replace( '/\t{2,}/i', "\t", $message );
		// Remove duplicate linebreaks:
		$message			=	preg_replace( '/(\r\n|\r|\n){2,}/i', '$1', $message );

		$row->set( 'user_id', $row->get( 'user_id', $viewer->get( 'id' ) ) );
		$row->set( 'type', $row->get( 'type', $stream->get( 'type' ) ) );
		$row->set( 'subtype', $row->get( 'subtype', $stream->get( 'subtype' ) ) );
		$row->set( 'item', $row->get( 'item', $stream->get( 'item' ) ) );
		$row->set( 'parent', $row->get( 'parent', $stream->get( 'parent' ) ) );

		if ( $messageLimit && ( cbutf8_strlen( $message ) > $messageLimit ) ) {
			$message		=	cbutf8_substr( $message, 0, $messageLimit );
		}

		$row->set( 'message', $message );

		if ( $row->get( 'id' ) ) {
			$row->params()->set( 'modified', $_CB_framework->getUTCDate() );
		}

		$row->set( 'params', $row->params()->asJson() );

		if ( $row->getError() || ( ! $row->check() ) ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		$rows				=	array( &$row );

		if ( $stream->get( 'replies' ) ) {
			CBActivity::preFetchComments( $rows, 'comment' );
		}

		CBActivity::preFetchUsers( $rows );

		$_PLUGINS->trigger( 'activity_onPushComments', array( $stream, $row ) );

		echo HTML_cbactivityComments::showComments( $rows, $stream, 4, $user, $viewer, $this );

		header( 'HTTP/1.0 200 OK' );
		exit();
	}

	/**
	 * Deletes comment
	 *
	 * @param int       $id
	 * @param Activity  $stream
	 * @param UserTable $user
	 * @param UserTable $viewer
	 * @param bool      $silent
	 */
	private function deleteComment( $id, $stream, $user, $viewer, $silent = false )
	{
		global $_PLUGINS;

		$row	=	new CommentTable();

		$row->load( (int) $id );

		if ( ( ! $row->get( 'id' ) ) || ( ( $viewer->get( 'id' ) != $row->get( 'user_id' ) ) && ( ! CBActivity::isModerator( (int) $viewer->get( 'id' ) ) ) ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		if ( ! $row->canDelete() ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			exit();
		}

		if ( ! $row->delete() ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		$_PLUGINS->trigger( 'activity_onRemoveComments', array( $stream, $row ) );

		if ( ! $silent ) {
			if ( $stream->get( 'type' ) == 'comment' ) {
				echo CBTxt::T( 'This reply has been deleted.' );
			} else {
				echo CBTxt::T( 'This comment has been deleted.' );
			}
		}

		header( 'HTTP/1.0 200 OK' );
		exit();
	}

	/**
	 * Hides comment
	 *
	 * @param int       $id
	 * @param Activity  $stream
	 * @param UserTable $user
	 * @param UserTable $viewer
	 * @param bool      $silent
	 */
	private function hideComment( $id, $stream, $user, $viewer, $silent = false )
	{
		$item		=	new CommentTable();

		$item->load( (int) $id );

		if ( ( ! $item->get( 'id' ) ) || ( ! $viewer->get( 'id' ) ) || ( $viewer->get( 'id' ) == $item->get( 'user_id' ) ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$row		=	new HiddenTable();

		$row->load( array( 'user_id' => (int) $viewer->get( 'id' ), 'type' => 'comment', 'item' => (int) $item->get( 'id' ) ) );

		if ( $row->get( 'id' ) ) {
			header( 'HTTP/1.0 200 OK' );
			exit();
		}

		$row->set( 'user_id', (int) $viewer->get( 'id' ) );
		$row->set( 'type', 'comment' );
		$row->set( 'item', (int) $item->get( 'id' ) );

		if ( ! $row->check() ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			exit();
		}

		if ( ! $row->store() ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		if ( ! $silent ) {
			$unhide		=	'<a href="' . $stream->endpoint( 'unhide', array( 'id' => (int) $item->get( 'id' ) ) ) . '" class="commentsContainerUnhide streamItemAction streamItemNoticeRevert">' . CBTxt::T( 'Unhide' ) . '</a>';

			if ( $stream->get( 'type' ) == 'comment' ) {
				echo CBTxt::T( 'COMMENT_REPLY_HIDDEN_UNHIDE', 'This reply has been hidden. [unhide]', array( '[unhide]' => $unhide ) );
			} else {
				echo CBTxt::T( 'COMMENT_HIDDEN_UNHIDE', 'This comment has been hidden. [unhide]', array( '[unhide]' => $unhide ) );
			}
		}

		header( 'HTTP/1.0 200 OK' );
		exit();
	}

	/**
	 * Deletes comment hide
	 *
	 * @param int       $id
	 * @param Activity  $stream
	 * @param UserTable $user
	 * @param UserTable $viewer
	 */
	private function unhideComment( $id, $stream, $user, $viewer )
	{
		$row	=	new HiddenTable();

		$row->load( array( 'user_id' => (int) $viewer->get( 'id' ), 'type' => 'comment', 'item' => (int) $id ) );

		if ( ( ! $row->get( 'id' ) ) || ( ( $viewer->get( 'id' ) != $row->get( 'user_id' ) ) && ( ! CBActivity::isModerator( (int) $viewer->get( 'id' ) ) ) ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		if ( ! $row->canDelete() ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			exit();
		}

		if ( ! $row->delete() ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		header( 'HTTP/1.0 200 OK' );
		exit();
	}

	/**
	 * Displays activity stream
	 *
	 * @param int       $id
	 * @param Activity  $stream
	 * @param int       $output
	 * @param bool      $data
	 * @param UserTable $user
	 * @param UserTable $viewer
	 */
	private function showActivity( $id, $stream, $output, $data, $user, $viewer )
	{
		CBActivity::getTemplate( 'activity', false, false );

		if ( $id ) {
			$stream->set( 'id', $id );
			$stream->set( 'limitstart', 0 );
			$stream->set( 'limit', 0 );
			$stream->set( 'paging', 0 );

			$rows			=	$stream->data();
		} else {
			if ( $data ) {
				$count		=	$stream->data( true );

				if ( ! $count ) {
					$rows	=	array();
				} else {
					$rows	=	$stream->data();

					if ( $count <= ( $stream->get( 'limitstart' ) + $stream->get( 'limit' ) ) ) {
						$stream->set( 'paging', 0 );
					}
				}
			} else {
				$rows		=	array();
			}
		}

		if ( $rows ) {
			if ( $stream->get( 'comments', 1 ) ) {
				CBActivity::preFetchComments( $rows, 'activity' );
			}

			if ( $stream->get( 'tags', 1 ) ) {
				CBActivity::preFetchTags( $rows, 'activity' );
			}

			CBActivity::preFetchUsers( $rows );
		}

		echo HTML_cbactivityActivity::showActivity( $rows, $stream, $output, $user, $viewer, $this );
	}

	/**
	 * Saves activity
	 *
	 * @param int       $id
	 * @param Activity  $stream
	 * @param UserTable $user
	 * @param UserTable $viewer
	 */
	private function saveActivity( $id, $stream, $user, $viewer )
	{
		global $_CB_framework, $_PLUGINS;

		$cbModerator					=	CBActivity::isModerator( (int) $viewer->get( 'id' ) );

		CBActivity::getTemplate( 'activity', false, false );

		$row							=	new ActivityTable();

		$row->load( (int) $id );

		$canAccess						=	false;

		if ( ! $row->get( 'id' ) ) {
			if ( CBActivity::canCreate( $user, $viewer, $stream ) ) {
				$canAccess				=	true;
			}
		} elseif ( ( ( $row->get( 'type' ) == 'status' ) || ( $row->get( 'subtype' ) == 'status' ) ) && ( $cbModerator || ( $viewer->get( 'id' ) == $row->get( 'user_id' ) ) ) ) {
			$canAccess					=	true;
		}

		if ( ! $canAccess ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$messageLimit					=	( $cbModerator ? 0 : (int) $stream->get( 'message_limit', 400 ) );
		$showActions					=	(int) $stream->get( 'actions', 1 );
		$actionLimit					=	( $cbModerator ? 0 : (int) $stream->get( 'actions_message_limit', 100 ) );
		$showLocations					=	(int) $stream->get( 'locations', 1 );
		$locationLimit					=	( $cbModerator ? 0 : (int) $stream->get( 'locations_address_limit', 200 ) );
		$showLinks						=	(int) $stream->get( 'links', 1 );
		$linkLimit						=	( $cbModerator ? 0 : (int) $stream->get( 'links_link_limit', 5 ) );
		$showTags						=	(int) $stream->get( 'tags', 1 );

		$message						=	trim( $this->input( 'message', $row->get( 'message', null, GetterInterface::HTML ), GetterInterface::HTML ) );

		// Remove duplicate spaces:
		$message						=	preg_replace( '/ {2,}/i', ' ', $message );
		// Remove duplicate tabs:
		$message						=	preg_replace( '/\t{2,}/i', "\t", $message );
		// Remove duplicate linebreaks:
		$message						=	preg_replace( '/(\r\n|\r|\n){2,}/i', '$1', $message );

		$row->set( 'user_id', $row->get( 'user_id', $viewer->get( 'id' ) ) );

		if ( $stream->get( 'type' ) && ( $stream->get( 'type' ) != 'status' ) ) {
			$row->set( 'type', $row->get( 'type', $stream->get( 'type' ) ) );
			$row->set( 'subtype', $row->get( 'subtype', 'status' ) );

			$parentDefault				=	null;
		} else {
			$row->set( 'type', $row->get( 'type', 'status' ) );

			$parentDefault				=	( $viewer->get( 'id' ) != $user->get( 'user_id' ) ? $user->get( 'user_id' ) : null );
		}

		$row->set( 'item', $row->get( 'item', $stream->get( 'item' ) ) );
		$row->set( 'parent', $row->get( 'parent', $stream->get( 'parent', $parentDefault ) ) );

		if ( $messageLimit && ( cbutf8_strlen( $message ) > $messageLimit ) ) {
			$message					=	cbutf8_substr( $message, 0, $messageLimit );
		}

		$row->set( 'message', $message );

		if ( $showActions ) {
			$action						=	$this->getInput()->subTree( 'actions' );
			$actionId					=	(int) $action->get( 'id', 0, GetterInterface::INT );
			$actionMessage				=	( $actionId ? trim( $action->get( 'message', '', GetterInterface::STRING ) ) : '' );

			// Remove linebreaks:
			$actionMessage				=	str_replace( array( "\n", "\r\n" ), ' ', $actionMessage );
			// Remove duplicate spaces:
			$actionMessage				=	preg_replace( '/ {2,}/i', ' ', $actionMessage );
			// Remove duplicate tabs:
			$actionMessage				=	preg_replace( '/\t{2,}/i', "\t", $actionMessage );

			if ( $actionLimit && ( cbutf8_strlen( $actionMessage ) > $actionLimit ) ) {
				$actionMessage			=	cbutf8_substr( $actionMessage, 0, $actionLimit );
			}

			$actionId					=	( $actionMessage ? $actionId : 0 );

			$newAction					=	array(	'id'		=>	$actionId,
													'message'	=>	( $actionId ? $actionMessage : '' ),
													'emote'		=>	( $actionId ? (int) $action->get( 'emote', 0, GetterInterface::INT ) : 0 )
												);

			$row->params()->set( 'action', $newAction );
		}

		if ( $showLocations ) {
			$location					=	$this->getInput()->subTree( 'location' );
			$locationId					=	(int) $location->get( 'id', 0, GetterInterface::INT );
			$locationPlace				=	( $locationId ? trim( $location->get( 'place', '', GetterInterface::STRING ) ) : '' );
			$locationAddress			=	( $locationId ? trim( $location->get( 'address', '', GetterInterface::STRING ) ) : '' );

			if ( $locationLimit && ( cbutf8_strlen( $locationPlace ) > $locationLimit ) ) {
				$locationPlace			=	cbutf8_substr( $locationPlace, 0, $locationLimit );
			}

			if ( $locationLimit && ( cbutf8_strlen( $locationAddress ) > $locationLimit ) ) {
				$locationAddress		=	cbutf8_substr( $locationAddress, 0, $locationLimit );
			}

			$locationId					=	( $locationPlace ? $locationId : 0 );

			$newLocation				=	array(	'id'		=>	$locationId,
													'place'		=>	( $locationId ? $locationPlace : '' ),
													'address'	=>	( $locationId ? $locationAddress : '' )
												);

			$row->params()->set( 'location', $newLocation );
		}

		if ( $showLinks ) {
			$links						=	$this->getInput()->subTree( 'links' );
			$newLinks					=	array();

			/** @var ParamsInterface[] $links */
			foreach ( $links as $i => $link ) {
				if ( $linkLimit && ( ( $i + 1 ) > $linkLimit ) ) {
					break;
				}

				$linkUrl				=	trim( $link->get( 'url', '', GetterInterface::STRING ) );

				if ( $linkUrl ) {
					$attachment			=	$stream->parser()->attachment( $linkUrl );

					if ( ! $attachment ) {
						continue;
					}

					$linkType			=	$attachment->get( 'type', '', GetterInterface::STRING );

					switch ( $linkType ) {
						case 'video':
							$linkMedia	=	$attachment->subTree( 'media' )->subTree( 'video' )->subTree( 0 );
							break;
						case 'audio':
							$linkMedia	=	$attachment->subTree( 'media' )->subTree( 'audio' )->subTree( 0 );
							break;
						case 'image':
						case 'url':
						default:
							$linkMedia	=	$attachment->subTree( 'media' )->subTree( 'image' )->subTree( 0 );
							break;
					}

					$newLinks[]			=	array(	'url'			=>	$linkUrl,
													'text'			=>	null,
													'title'			=>	trim( $link->get( 'title', $attachment->subTree( 'title' )->get( 0, '', GetterInterface::STRING ), GetterInterface::STRING ) ),
													'description'	=>	trim( $link->get( 'description', $attachment->subTree( 'description' )->get( 0, '', GetterInterface::STRING ), GetterInterface::STRING ) ),
													'media'			=>	array(	'url' => $linkMedia->get( 'url', '', GetterInterface::STRING ),
																				'mimetype' => $linkMedia->get( 'mimetype', '', GetterInterface::STRING ),
																				'extension' => $linkMedia->get( 'extension', '', GetterInterface::STRING ),
																				'custom' => ''
																			),
													'type'			=>	$linkType,
													'thumbnail'		=>	$link->get( 'thumbnail', 1, GetterInterface::INT ),
													'internal'		=>	0,
												);
				}
			}

			$row->params()->set( 'links', $newLinks );
		}

		if ( $row->get( 'id' ) ) {
			$row->params()->set( 'modified', $_CB_framework->getUTCDate() );
		}

		$row->set( 'params', $row->params()->asJson() );

		if ( $row->getError() || ( ! $row->check() ) ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		if ( $showTags ) {
			$tagsStream					=	$row->tags( $stream->source() );

			if ( $tagsStream ) {
				$tags					=	$this->input( 'tags', array(), GetterInterface::RAW );

				foreach ( $tagsStream->data() as $tag ) {
					/** @var TagTable $tag */
					if ( ! in_array( $tag->get( 'user' ), $tags ) ) {
						$tag->delete();

						$tagsStream->resetData();
					} else {
						$key			=	array_search( $tag->get( 'user' ), $tags );

						if ( $key !== false ) {
							unset( $tags[$key] );
						}
					}
				}

				foreach ( $tags as $tagUser ) {
					if ( is_numeric( $tagUser ) ) {
						$tagUser		=	(int) $tagUser;
					} else {
						$tagUser		=	Get::clean( $tagUser, GetterInterface::STRING );
					}

					$tag				=	new TagTable();

					$tag->set( 'user_id', (int) $tagsStream->user()->get( 'id' ) );
					$tag->set( 'type', $tagsStream->get( 'type', null, GetterInterface::STRING ) );
					$tag->set( 'subtype', $tagsStream->get( 'subtype', null, GetterInterface::STRING ) );
					$tag->set( 'item', $tagsStream->get( 'item', null, GetterInterface::STRING ) );
					$tag->set( 'parent', $tagsStream->get( 'parent', null, GetterInterface::STRING ) );
					$tag->set( 'user', $tagUser );

					$tag->store();

					$tagsStream->resetData();
				}
			}
		}

		$rows							=	array( &$row );

		if ( $stream->get( 'comments', 1 ) ) {
			CBActivity::preFetchComments( $rows, 'activity' );
		}

		if ( $stream->get( 'tags', 1 ) ) {
			CBActivity::preFetchTags( $rows, 'activity' );
		}

		CBActivity::preFetchUsers( $rows );

		$_PLUGINS->trigger( 'activity_onPushActivity', array( $stream, $row ) );

		echo HTML_cbactivityActivity::showActivity( $rows, $stream, 4, $user, $viewer, $this );

		header( 'HTTP/1.0 200 OK' );
		exit();
	}

	/**
	 * Deletes activity
	 *
	 * @param int       $id
	 * @param Activity  $stream
	 * @param UserTable $user
	 * @param UserTable $viewer
	 * @param bool      $silent
	 */
	private function deleteActivity( $id, $stream, $user, $viewer, $silent = false )
	{
		global $_PLUGINS;

		$row	=	new ActivityTable();

		$row->load( (int) $id );

		if ( ( ! $row->get( 'id' ) ) || ( ( $viewer->get( 'id' ) != $row->get( 'user_id' ) ) && ( ! CBActivity::isModerator( (int) $viewer->get( 'id' ) ) ) ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		if ( ! $row->canDelete() ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			exit();
		}

		if ( ! $row->delete() ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		$_PLUGINS->trigger( 'activity_onRemoveActivity', array( $stream, $row ) );

		if ( ! $silent ) {
			echo CBTxt::T( 'This activity has been deleted.' );
		}

		header( 'HTTP/1.0 200 OK' );
		exit();
	}

	/**
	 * Hides activity
	 *
	 * @param int       $id
	 * @param Activity  $stream
	 * @param UserTable $user
	 * @param UserTable $viewer
	 * @param bool      $silent
	 */
	private function hideActivity( $id, $stream, $user, $viewer, $silent = false )
	{
		$item		=	new ActivityTable();

		$item->load( (int) $id );

		if ( ( ! $item->get( 'id' ) ) || ( ! $viewer->get( 'id' ) ) || ( $viewer->get( 'id' ) == $item->get( 'user_id' ) ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		$row		=	new HiddenTable();

		$row->load( array( 'user_id' => (int) $viewer->get( 'id' ), 'type' => 'activity', 'item' => (int) $item->get( 'id' ) ) );

		if ( $row->get( 'id' ) ) {
			header( 'HTTP/1.0 200 OK' );
			exit();
		}

		$row->set( 'user_id', (int) $viewer->get( 'id' ) );
		$row->set( 'type', 'activity' );
		$row->set( 'item', (int) $item->get( 'id' ) );

		if ( ! $row->check() ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			exit();
		}

		if ( ! $row->store() ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		if ( ! $silent ) {
			$unhide		=	'<a href="' . $stream->endpoint( 'unhide', array( 'id' => (int) $item->get( 'id' ) ) ) . '" class="activityContainerUnhide streamItemAction streamItemNoticeRevert">' . CBTxt::T( 'Unhide' ) . '</a>';

			echo CBTxt::T( 'ACTIVITY_HIDDEN_UNHIDE', 'This activity has been hidden. [unhide]', array( '[unhide]' => $unhide ) );
		}

		header( 'HTTP/1.0 200 OK' );
		exit();
	}

	/**
	 * Deletes activity hide
	 *
	 * @param int       $id
	 * @param Activity  $stream
	 * @param UserTable $user
	 * @param UserTable $viewer
	 */
	private function unhideActivity( $id, $stream, $user, $viewer )
	{
		$row	=	new HiddenTable();

		$row->load( array( 'user_id' => (int) $viewer->get( 'id' ), 'type' => 'activity', 'item' => (int) $id ) );

		if ( ( ! $row->get( 'id' ) ) || ( ( $viewer->get( 'id' ) != $row->get( 'user_id' ) ) && ( ! CBActivity::isModerator( (int) $viewer->get( 'id' ) ) ) ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		if ( ! $row->canDelete() ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			exit();
		}

		if ( ! $row->delete() ) {
			header( 'HTTP/1.0 500 Internal Server Error' );
			exit();
		}

		header( 'HTTP/1.0 200 OK' );
		exit();
	}

	/**
	 * Displays tags stream
	 *
	 * @param int       $id
	 * @param Tags      $stream
	 * @param int       $output
	 * @param bool      $data
	 * @param UserTable $user
	 * @param UserTable $viewer
	 */
	private function showTags( $id, $stream, $output, $data, $user, $viewer )
	{
		CBActivity::getTemplate( 'tags', false, false );

		if ( $id ) {
			$stream->set( 'id', $id );

			$rows			=	$stream->data();
		} else {
			if ( $data ) {
				$count		=	$stream->data( true );

				if ( ! $count ) {
					$rows	=	array();
				} else {
					$rows	=	$stream->data();
				}
			} else {
				$rows		=	array();
			}
		}

		if ( $rows ) {
			CBActivity::preFetchUsers( $rows );
		}

		echo HTML_cbactivityTags::showTags( $rows, $stream, $output, $user, $viewer, $this );
	}
}