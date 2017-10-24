<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Comments;
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Table\TagTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'cleanUp', 'cbactivityPlugin' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array(	'activity'	=>	'cbactivityField',
											'comments'	=>	'cbactivityField',
										));

class cbactivityPlugin extends cbPluginHandler
{

	/**
	 * Deletes data when a user is deleted
	 *
	 * @param  UserTable $user
	 * @param  int       $status
	 */
	public function cleanUp( $user, $status )
	{
		global $_CB_database, $_PLUGINS;

		$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );
		$params				=	$_PLUGINS->getPluginParams( $plugin );

		if ( $params->get( 'general_delete', 1 ) ) {
			$query			=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );
			$_CB_database->setQuery( $query );
			$activities		=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\ActivityTable', array( $_CB_database ) );

			/** @var ActivityTable[] $activities */
			foreach ( $activities as $activity ) {
				$activity->delete();
			}

			$query			=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_comments' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );
			$_CB_database->setQuery( $query );
			$comments		=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\CommentTable', array( $_CB_database ) );

			/** @var CommentTable[] $comments */
			foreach ( $comments as $comment ) {
				$comment->delete();
			}

			$query			=	'SELECT *'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_tags' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' );
			$_CB_database->setQuery( $query );
			$tags			=	$_CB_database->loadObjectList( null, '\CB\Plugin\Activity\Table\TagTable', array( $_CB_database ) );

			/** @var TagTable[] $tags */
			foreach ( $tags as $tag ) {
				$tag->delete();
			}
		}
	}
}

class cbactivityTab extends cbTabHandler
{

	/**
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		$activity	=	new Activity( 'profile', $user );

		CBActivity::loadStreamDefaults( $activity, $this->params, 'tab_activity_' );

		return $activity->stream();
	}
}

class cbactivityField extends cbFieldHandler
{

	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		$return			=	null;

		if ( $field->get( 'type' ) == 'comments' ) {
			$comments	=	new Comments( 'field', $user, (int) $field->params->get( 'field_comments_direction', 0 ) );

			$comments->set( 'type', 'field' );
			$comments->set( 'item', (int) $field->get( 'fieldid' ) );
			$comments->set( 'parent', (int) $user->get( 'id' ) );

			CBActivity::loadStreamDefaults( $comments, $field->params, 'field_comments_' );

			$return		=	$comments->stream( false );
		} else {
			$activity	=	new Activity( 'field', $user, (int) $field->params->get( 'field_activity_direction', 0 ) );

			$activity->set( 'type', 'field' );
			$activity->set( 'subtype', 'status' );
			$activity->set( 'item', (int) $field->get( 'fieldid' ) );
			$activity->set( 'parent', (int) $user->get( 'id' ) );

			CBActivity::loadStreamDefaults( $activity, $field->params, 'field_activity_' );

			$return		=	$activity->stream( false );
		}

		if ( ! ( ( $output == 'html' ) && ( $reason == 'profile' ) ) ) {
			return null;
		}

		return $this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name' ), $return, $output, false ), $reason, $field, $user );
	}
}