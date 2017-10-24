<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Table;

use CB\Plugin\Activity\CBActivity;
use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Comments;
use CB\Plugin\Activity\Tags;

defined('CBLIB') or die();

class ActivityTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var string  */
	public $type			=	null;
	/** @var string  */
	public $subtype			=	null;
	/** @var string  */
	public $item			=	null;
	/** @var string  */
	public $parent			=	null;
	/** @var string  */
	public $title			=	null;
	/** @var string  */
	public $message			=	null;
	/** @var string  */
	public $date			=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_activity';

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
		$type		=	preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'type' ) );
		$subType	=	preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'subtype' ) );

		if ( $this->get( 'user_id' ) == '' ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		} elseif ( $type == '' ) {
			$this->setError( CBTxt::T( 'Type not specified!' ) );

			return false;
		} elseif ( ( ( $type == 'status' ) || ( $subType == 'status' ) ) && ( trim( $this->get( 'message' ) ) == '' ) ) {
			$this->setError( CBTxt::T( 'Message not specified!' ) );

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

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		$this->set( 'type', preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'type' ) ) );
		$this->set( 'subtype', preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'subtype' ) ) );
		$this->set( 'date', $this->get( 'date', $_CB_framework->getUTCDate() ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'activity_onBeforeUpdateActivity', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateActivity', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateActivity', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateActivity', array( $this ) );
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

		$_PLUGINS->trigger( 'activity_onBeforeDeleteActivity', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Deletes activity about this activity:
		$query				=	'SELECT *'
							.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity' )
							.	"\n WHERE " . $this->getDbo()->NameQuote( 'type' ) . " = " . $this->getDbo()->Quote( 'activity' )
							.	"\n AND " . $this->getDbo()->NameQuote( 'item' ) . " = " . (int) $this->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$activities			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\ActivityTable', array( $this->getDbo() ) );

		/** @var ActivityTable[] $activities */
		foreach ( $activities as $activity ) {
			$activity->delete();
		}

		if ( ! $this->get( 'item' ) ) {
			// Deletes activity specific comments:
			$query			=	'SELECT *'
							.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_comments' )
							.	"\n WHERE " . $this->getDbo()->NameQuote( 'type' ) . " = " . $this->getDbo()->Quote( 'activity' )
							.	"\n AND " . $this->getDbo()->NameQuote( 'item' ) . " = " . (int) $this->get( 'id' );
			$this->getDbo()->setQuery( $query );
			$comments		=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\CommentTable', array( $this->getDbo() ) );

			/** @var CommentTable[] $comments */
			foreach ( $comments as $comment ) {
				$comment->delete();
			}

			// Deletes activity specific tags:
			$query			=	'SELECT *'
							.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_tags' )
							.	"\n WHERE " . $this->getDbo()->NameQuote( 'type' ) . " = " . $this->getDbo()->Quote( 'activity' )
							.	"\n AND " . $this->getDbo()->NameQuote( 'item' ) . " = " . (int) $this->get( 'id' );
			$this->getDbo()->setQuery( $query );
			$tags			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\TagTable', array( $this->getDbo() ) );

			/** @var TagTable[] $tags */
			foreach ( $tags as $tag ) {
				$tag->delete();
			}
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteActivity', array( $this ) );

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
	 * @return null|string
	 */
	public function action()
	{
		static $cache						=	array();

		$actions							=	CBActivity::loadActionOptions( true );
		$emotes								=	CBActivity::loadEmoteOptions( false, true );

		$id									=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$action							=	$this->params()->subTree( 'action' );
			$actionId						=	(int) $action->get( 'id', 0, GetterInterface::INT );
			$actionTitle					=	null;
			$actionMessage					=	null;
			$actionEmote					=	null;
			$return							=	null;

			if ( $actionId && isset( $actions[$actionId] ) ) {
				if ( $actions[$actionId]->get( 'published' ) ) {
					$actionTitle			=	( $actions[$actionId]->get( 'title' ) != '' ? trim( CBTxt::T( $actions[$actionId]->get( 'title' ) ) ) : null );

					$message				=	$action->get( 'message', null, GetterInterface::STRING );

					if ( $message != '' ) {
						$actionMessage		=	'<span class="activityActionMessage">' . trim( htmlspecialchars( $message ) ) . '</span>';
					}

					$emoteId				=	(int) $action->get( 'emote', 0, GetterInterface::INT );

					if ( $emoteId && isset( $emotes[$emoteId] ) && $emotes[$emoteId]->get( 'published' ) ) {
						$actionEmote		=	( $emotes[$emoteId]->icon() ? $emotes[$emoteId]->icon() : null );
					} else {
						$actionEmote		=	( $actions[$actionId]->icon() ? $actions[$actionId]->icon() : null );
					}
				}

				if ( $actionMessage ) {
					$return					=	trim( CBTxt::T( 'ACTIVITY_STATUS_ACTION', '[title] [message] [emote]', array( '[title]' => $actionTitle, '[message]' => $actionMessage, '[emote]' => $actionEmote ) ) );
				}
			}

			$cache[$id]						=	$return;
		}

		return $cache[$id];
	}

	/**
	 * @return null|string
	 */
	public function location()
	{
		static $cache							=	array();

		$locations								=	CBActivity::loadLocationOptions( true );

		$id										=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$location							=	$this->params()->subTree( 'location' );
			$locationId							=	(int) $location->get( 'id', 0, GetterInterface::INT );
			$locationTitle						=	null;
			$locationAddress					=	null;
			$return								=	null;

			if ( $locationId && isset( $locations[$locationId] ) ) {
				if ( $locations[$locationId]->get( 'published' ) ) {
					$locationTitle				=	( $locations[$locationId]->get( 'title' ) != '' ? trim( CBTxt::T( $locations[$locationId]->get( 'title' ) ) ) : null );

					$place						=	$location->get( 'place', null, GetterInterface::STRING );

					if ( $place != '' ) {
						$address				=	$location->get( 'address', null, GetterInterface::STRING );

						if ( $address != '' ) {
							$addressUrl			=	'https://www.google.com/maps/place/' . urlencode( $address );
						} else {
							$addressUrl			=	'https://www.google.com/maps/search/' . urlencode( $place );
						}

						$locationAddress		=	'<span class="activityLocation">'
												.		'<a href="' . htmlspecialchars( $addressUrl ) . '" target="_blank" rel="nofollow">' . trim( htmlspecialchars( $place ) ) . '</a>'
												.	'</span>';
					}
				}

				if ( $locationAddress ) {
					$return						=	trim( CBTxt::T( 'ACTIVITY_STATUS_LOCATION', '[title] [location]', array( '[title]' => $locationTitle, '[location]' => $locationAddress ) ) );
				}
			}

			$cache[$id]							=	$return;
		}

		return $cache[$id];
	}

	/**
	 * @return array
	 */
	public function attachments()
	{
		static $cache								=	array();

		$id											=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$links									=	$this->params()->subTree( 'links' )->asArray();

			foreach ( $links as $i => &$link ) {
				if ( ( ! isset( $link['url'] ) ) || ( ! $link['url'] ) ) {
					unset( $links[$i] );
				} elseif ( substr( $link['url'], 0, 3 ) == 'www' ) {
					$link['url']					=	'http://' . $link['url'];
				}

				if ( ! isset( $link['text'] ) ) {
					$link['text']					=	null;
				}

				if ( ! isset( $link['title'] ) ) {
					$link['title']					=	null;
				}

				if ( ! isset( $link['description'] ) ) {
					$link['description']			=	null;
				}

				if ( ! isset( $link['media']['url'] ) ) {
					$link['media']['url']			=	null;
				}

				if ( ! isset( $link['media']['mimetype'] ) ) {
					$link['media']['mimetype']		=	null;
				}

				if ( ! isset( $link['media']['extension'] ) ) {
					$link['media']['extension']		=	null;
				}

				if ( ! isset( $link['media']['custom'] ) ) {
					$link['media']['custom']		=	null;
				}

				if ( ! isset( $link['type'] ) ) {
					$link['type']					=	'url';
				}

				if ( ! isset( $link['tumbnail'] ) ) {
					$link['tumbnail']				=	1;
				}

				if ( ! isset( $link['internal'] ) ) {
					$link['internal']				=	0;
				}
			}

			array_values( $links );

			$cache[$id]								=	$links;
		}

		return $cache[$id];
	}

	/**
	 * @param string         $source
	 * @param null|UserTable $user
	 * @param int            $direction
	 * @return Comments|null
	 */
	public function comments( $source = 'stream', $user = null, $direction = 1 )
	{
		$params				=	$this->params()->subTree( 'comments' );

		if ( ! $params->get( 'display', 1 ) ) {
			return null;
		}

		/** @var Comments[] $cache */
		static $cache		=	array();

		if ( $this->get( 'item' ) && $params->get( 'source', 1 ) ) {
			$id				=	$this->get( 'type' ) . $this->get( 'subtype' ) . $this->get( 'item' ) . $this->get( 'parent' );
		} else {
			$id				=	$this->get( 'id' );
		}

		$id					=	md5( $id . $source . ( $user ? $user->get( 'id' ) : null ) . $direction . Application::MyUser()->getUserId() );

		if ( ! isset( $cache[$id] ) ) {
			$stream			=	new Comments( $source, $user, $direction );

			if ( $this->get( 'item' ) && $params->get( 'source', 1 ) ) {
				$stream->set( 'type', $this->get( 'type' ) );
				$stream->set( 'subtype', $this->get( 'subtype' ) );
				$stream->set( 'item', $this->get( 'item' ) );
				$stream->set( 'parent', $this->get( 'parent' ) );
			} else {
				$stream->set( 'type', 'activity' );
				$stream->set( 'item', (int) $this->get( 'id' ) );
			}

			$object			=	array(	'source'	=>	'activity',
										'id'		=>	(int) $this->get( 'id' ),
										'user_id'	=>	(int) $this->get( 'user_id' ),
										'type'		=>	$this->get( 'type' ),
										'subtype'	=>	$this->get( 'subtype' ),
										'item'		=>	$this->get( 'item' ),
										'parent'	=>	$this->get( 'parent' )
									);

			$stream->set( 'object', $object );

			$cache[$id]		=	$stream;
		}

		return $cache[$id];
	}

	/**
	 * @param string         $source
	 * @param null|UserTable $user
	 * @return Tags|null
	 */
	public function tags( $source = 'stream', $user = null )
	{
		$params				=	$this->params()->subTree( 'tags' );

		if ( ! $params->get( 'display', 1 ) ) {
			return null;
		}

		/** @var Tags[] $cache */
		static $cache		=	array();

		if ( $this->get( 'item' ) && $params->get( 'source', 1 ) ) {
			$id				=	$this->get( 'type' ) . $this->get( 'subtype' ) . $this->get( 'item' ) . $this->get( 'parent' );
		} else {
			$id				=	$this->get( 'id' );
		}

		$id					=	md5( $id . $source . ( $user ? $user->get( 'id' ) : null ) . Application::MyUser()->getUserId() );

		if ( ! isset( $cache[$id] ) ) {
			$stream			=	new Tags( $source, $user );

			if ( $this->get( 'item' ) && $params->get( 'source', 1 ) ) {
				$stream->set( 'type', $this->get( 'type' ) );
				$stream->set( 'subtype', $this->get( 'subtype' ) );
				$stream->set( 'item', $this->get( 'item' ) );
				$stream->set( 'parent', $this->get( 'parent' ) );
			} else {
				$stream->set( 'type', 'activity' );
				$stream->set( 'item', (int) $this->get( 'id' ) );
			}

			$object			=	array(	'source'	=>	'activity',
										'id'		=>	(int) $this->get( 'id' ),
										'user_id'	=>	(int) $this->get( 'user_id' ),
										'type'		=>	$this->get( 'type' ),
										'subtype'	=>	$this->get( 'subtype' ),
										'item'		=>	$this->get( 'item' ),
										'parent'	=>	$this->get( 'parent' )
									);

			$stream->set( 'object', $object );

			$cache[$id]		=	$stream;
		}

		return $cache[$id];
	}
}