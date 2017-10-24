<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Database\Table\TableInterface;
use CB\Database\Table\UserTable;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Comments;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Table\HiddenTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionActivity extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 * @return null|string
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_framework, $_CB_database;

		if ( ! $this->installed() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NOT_INSTALLED', ':: Action [action] :: CB Activity is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return null;
		}

		$return									=	null;

		foreach ( $trigger->getParams()->subTree( 'activity' ) as $row ) {
			/** @var ParamsInterface $row */
			$mode								=	$row->get( 'mode', 'activity', GetterInterface::STRING );
			$method								=	$row->get( 'method', 'create', GetterInterface::STRING );
			$owner								=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner							=	(int) $user->get( 'id' );
			} else {
				$owner							=	(int) $trigger->getSubstituteString( $owner );
			}

			$type								=	$trigger->getSubstituteString( $row->get( 'type', null, GetterInterface::STRING ) );
			$subtype							=	$trigger->getSubstituteString( $row->get( 'subtype', null, GetterInterface::STRING ) );
			$item								=	$trigger->getSubstituteString( $row->get( 'item', null, GetterInterface::STRING ) );
			$parent								=	$trigger->getSubstituteString( $row->get( 'parent', null, GetterInterface::STRING ) );

			if ( $mode == 'stream' ) {
				if ( $owner ) {
					$streamUser					=	CBuser::getUserDataInstance( (int) $owner );
				} else {
					$streamUser					=	CBuser::getMyUserDataInstance();
				}

				$source							=	$trigger->getSubstituteString( $row->get( 'source', null, GetterInterface::STRING ) );
				$direction						=	(int) $row->get( 'direction', 0, GetterInterface::INT );

				if ( $row->get( 'stream', 'activity', GetterInterface::STRING ) == 'comments' ) {
					$object						=	new Comments( $source, $streamUser, $direction );

					CBActivity::loadStreamDefaults( $object, $row->subTree( 'comments_stream' ), 'comments_' );
				} else {
					$object						=	new Activity( $source, $streamUser, $direction );

					CBActivity::loadStreamDefaults( $object, $row->subTree( 'activity_stream' ), 'activity_' );
				}

				if ( $type ) {
					$object->set( 'type', $type );
				}

				if ( $subtype ) {
					$object->set( 'subtype', $subtype );
				}

				if ( $item ) {
					$object->set( 'item', $item );
				}

				if ( $parent ) {
					$object->set( 'parent', $parent );
				}

				if ( $row->get( 'output', 'echo', GetterInterface::STRING ) == 'echo' ) {
					echo $object->stream( false );
				} else {
					$return						.=	$object->stream( false );
				}
			} elseif ( $method == 'delete' ) {
				$where							=	array();

				if ( $owner ) {
					$where[]					=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $owner;
				}

				if ( $type ) {
					$where[]					=	$_CB_database->NameQuote( 'type' ) . ( strpos( $type, '%' ) !== false ? ' LIKE ' : ' = ' ) . $_CB_database->Quote( $type );
				}

				if ( $item ) {
					$where[]					=	$_CB_database->NameQuote( 'item' ) . ' = ' . $_CB_database->Quote( $item );
				}

				if ( $mode != 'hidden' ) {
					if ( $subtype ) {
						$where[]				=	$_CB_database->NameQuote( 'subtype' ) . ( strpos( $type, '%' ) !== false ? ' LIKE ' : ' = ' ) . $_CB_database->Quote( $subtype );
					}

					if ( $parent ) {
						$where[]				=	$_CB_database->NameQuote( 'parent' ) . ' = ' . $_CB_database->Quote( $parent );
					}
				}

				switch ( $mode ) {
					case 'hidden':
						$table					=	'#__comprofiler_plugin_activity_hidden';
						$class					=	'\CB\Plugin\Activity\Table\HiddenTable';
						break;
					case 'tag':
						$table					=	'#__comprofiler_plugin_activity_tags';
						$class					=	'\CB\Plugin\Activity\Table\TagTable';
						break;
					case 'comment':
						$table					=	'#__comprofiler_plugin_activity_comments';
						$class					=	'\CB\Plugin\Activity\Table\CommentTable';
						break;
					case 'activity':
					default:
						$table					=	'#__comprofiler_plugin_activity';
						$class					=	'\CB\Plugin\Activity\Table\ActivityTable';
						break;
				}

				$query							=	'SELECT *'
												.	"\n FROM " . $_CB_database->NameQuote( $table )
												.	( $where ? "\n WHERE " . implode( "\n AND ", $where ) : null );
				$_CB_database->setQuery( $query );
				$objects						=	$_CB_database->loadObjectList( null, $class, array( $_CB_database ) );

				/** @var TableInterface[] $objects */
				foreach ( $objects as $object ) {
					$object->delete();
				}
			} else {
				if ( ! $owner ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_OWNER', ':: Action [action] :: CB Activity skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
					}

					continue;
				}

				if ( ! $type ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_ACTIVITY_NO_TYPE', ':: Action [action] :: CB Activity skipped due to missing type', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
					}

					continue;
				}

				switch ( $mode ) {
					case 'hidden':
						$object					=	new HiddenTable( $_CB_database );
						break;
					case 'tag':
						$object					=	new TagTable( $_CB_database );
						break;
					case 'comment':
						$object					=	new CommentTable( $_CB_database );
						break;
					case 'activity':
					default:
						$object					=	new ActivityTable( $_CB_database );
						break;
				}

				if ( $item ) {
					$load						=	array( 'user_id' => $owner, 'type' => $type, 'item' => $item );

					if ( $mode != 'hidden' ) {
						if ( $subtype ) {
							$load['subtype']	=	$subtype;
						}

						if ( $parent ) {
							$load['parent']		=	$parent;
						}
					}

					$object->load( $load );
				}

				$object->set( 'user_id', $owner );

				if ( $type ) {
					$object->set( 'type', $type );
				}

				if ( $mode != 'hidden' ) {
					if ( $subtype ) {
						$object->set( 'subtype', $subtype );
					}

					if ( $parent ) {
						$object->set( 'parent', $parent );
					}
				}

				if ( $item ) {
					$object->set( 'item', $item );
				}

				if ( $mode == 'activity' ) {
					$title						=	$trigger->getSubstituteString( $row->get( 'title', null, GetterInterface::RAW ), false );

					if ( $title ) {
						$object->set( 'title', $title );
					}

					$date						=	$trigger->getSubstituteString( $row->get( 'date', null, GetterInterface::STRING ) );

					if ( $date ) {
						$object->set( 'date', $_CB_framework->getUTCDate( 'Y-m-d H:i:s', $date ) );
					}

					$action						=	$row->subTree( 'action' );
					$actionId					=	$action->get( 'id', null, GetterInterface::INT );

					if ( $actionId ) {
						$actionMessage			=	$trigger->getSubstituteString( $action->get( 'message', null, GetterInterface::STRING ), false );

						if ( $actionMessage ) {
							$newAction			=	array(	'id'		=>	$actionId,
															'message'	=>	$actionMessage,
															'emote'		=>	$action->get( 'emote', '', GetterInterface::STRING )
														);

							$object->params()->set( 'action', $newAction );
						}
					}

					$location					=	$row->subTree( 'location' );
					$locationId					=	$location->get( 'id', null, GetterInterface::INT );

					if ( $locationId ) {
						$locationPlace			=	$trigger->getSubstituteString( $location->get( 'place', null, GetterInterface::STRING ) );

						if ( $locationPlace ) {
							$newLocation		=	array(	'id'		=>	$locationId,
															'place'		=>	$locationPlace,
															'address'	=>	$trigger->getSubstituteString( $location->get( 'address', null, GetterInterface::STRING ) )
														);

							$object->params()->set( 'location', $newLocation );
						}
					}

					$newLinks					=	array();

					foreach ( $row->subTree( 'links' ) as $link ) {
						/** @var ParamsInterface $link */
						$linkType				=	$trigger->getSubstituteString( $link->get( 'type', null, GetterInterface::STRING ) );
						$linkUrl				=	$trigger->getSubstituteString( $link->get( 'url', null, GetterInterface::STRING ), false );

						if ( ( ! $linkType ) || ( ! $linkUrl ) ) {
							continue;
						}

						$linkMedia				=	$link->subTree( 'media' );

						$newLinks[]				=	array(	'url'			=>	$linkUrl,
															'text'			=>	$trigger->getSubstituteString( $link->get( 'text', null, GetterInterface::STRING ), false ),
															'title'			=>	$trigger->getSubstituteString( $link->get( 'title', null, GetterInterface::STRING ), false ),
															'description'	=>	$trigger->getSubstituteString( $link->get( 'description', null, GetterInterface::RAW ), false ),
															'media'			=>	array(	'url' => $trigger->getSubstituteString( $linkMedia->get( 'url', null, GetterInterface::STRING ), false ),
																						'mimetype' => $trigger->getSubstituteString( $linkMedia->get( 'mimetype', null, GetterInterface::STRING ) ),
																						'extension' => $trigger->getSubstituteString( $linkMedia->get( 'extension', null, GetterInterface::STRING ) ),
																						'custom' => $trigger->getSubstituteString( $linkMedia->get( 'custom', null, GetterInterface::RAW ), false )
																					),
															'type'			=>	$linkType,
															'thumbnail'		=>	$link->get( 'thumbnail', 1, GetterInterface::INT ),
															'internal'		=>	$link->get( 'internal', 0, GetterInterface::INT )
														);
					}

					if ( $newLinks ) {
						$object->params()->set( 'links', $newLinks );
					}

					$comments					=	$row->subTree( 'comments' );

					$object->params()->set( 'comments', array(	'display'	=>	(int) $comments->get( 'display', 1, GetterInterface::INT ),
																'source'	=>	(int) $comments->get( 'source', 1, GetterInterface::INT )
															));

					$tags						=	$row->subTree( 'tags' );

					$object->params()->set( 'tags', array(	'display'	=>	(int) $tags->get( 'display', 1, GetterInterface::INT ),
															'source'	=>	(int) $tags->get( 'source', 1, GetterInterface::INT )
														));

					$object->set( 'params', $object->params()->asJson() );
				} elseif ( $mode == 'comment' ) {
					$tags						=	$row->subTree( 'replies' );

					$object->params()->set( 'replies', array( 'display' => (int) $tags->get( 'display', 1, GetterInterface::INT ) ));

					$object->set( 'params', $object->params()->asJson() );
				}

				if ( in_array( $mode, array( 'activity', 'comment' ) ) ) {
					$message					=	$trigger->getSubstituteString( $row->get( 'message', null, GetterInterface::RAW ), false );

					if ( $message ) {
						$object->set( 'message', $message );
					}
				}

				if ( ! $object->store() ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_ACTIVITY_CREATE_FAILED', ':: Action [action] :: CB Activity failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $object->getError() ) ) );
					}

					continue;
				}
			}
		}

		return $return;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' ) ) {
			return true;
		}

		return false;
	}
}