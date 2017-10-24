<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Application\Application;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->registerFunction( 'onBeforeUserProfileEditDisplay', 'setTemplate', 'CBfield_template' );
$_PLUGINS->registerFunction( 'onBeforeUserProfileRequest', 'setTemplate', 'CBfield_template' );
$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'templatechanger_cb' => 'CBfield_template' ) );

class CBfield_template extends cbFieldHandler
{

	/**
	 * @param  UserTable   $user
	 */
	public function setTemplate( $user )
	{
		global $_CB_framework, $ueConfig;

		if ( $this->params->get( 'templatechanger_user', 0 ) ) {
			$templateUser				=	CBuser::getUserDataInstance( $_CB_framework->myId() );
		} else {
			$templateUser				=	$user;
		}

		if ( isset( $templateUser->template_profile ) && ( $templateUser->template_profile != '' ) ) {
			$ueConfig['templatedir']	=	$templateUser->template_profile;
		}
	}

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
		global $_CB_database;

		switch( $output ) {
			case 'htmledit':
				$exclude						=	$field->params->get( 'templatechanger_cb_exclude', null );
				$cacheId						=	(int) $user->get( 'id' );

				static $cache					=	array();

				if ( ! isset( $cache[$exclude][$cacheId] ) ) {
					$query						=	'SELECT ' . $_CB_database->NameQuote( 'name' ) . ' AS text'
												.	', ' . $_CB_database->NameQuote( 'folder' ) . ' AS value'
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'templates' )
												.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
												.	"\n AND " . $_CB_database->NameQuote( 'viewaccesslevel' ) . " IN " . $_CB_database->safeArrayOfIntegers( Application::MyUser()->getAuthorisedViewLevels() )
												.	( $exclude ? "\n AND " . $_CB_database->NameQuote( 'folder' ) . " NOT IN " . $_CB_database->safeArrayOfStrings( explode( '|*|', $exclude ) ) : null )
												.	"\n ORDER BY  " . $_CB_database->Quote( 'ordering' );
					$_CB_database->setQuery( $query );
					$cache[$exclude][$cacheId]	=	$_CB_database->loadObjectList();
				}

				$return							=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $user->get( $field->get( 'name' ) ), null, $cache[$exclude][$cacheId] );

				if ( $reason == 'search' ) {
					$return						=	$this->_fieldSearchModeHtml( $field, $user, $return, 'select', $list_compare_types );
				}
				break;
			default:
				$return							=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}

		return $return;
	}
}
?>