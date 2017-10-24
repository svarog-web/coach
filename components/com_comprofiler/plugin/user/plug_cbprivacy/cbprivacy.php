<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Database\Table\Table;
use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CB\Database\Table\ListTable;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

static $CB_loaded	=	0;

if ( ! $CB_loaded++ ) {
	$_PLUGINS->loadPluginGroup( 'user' );
}

$_PLUGINS->registerFunction( 'onBeforeUserProfileAccess', 'getProfile', 'cbprivacyPlugin' );
$_PLUGINS->registerFunction( 'onBeforeDisplayUsersList', 'getList', 'cbprivacyPlugin' );
$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'deletePrivacy', 'cbprivacyPlugin' );
$_PLUGINS->registerFunction( 'onAfterEditATab', 'tabEdit', 'cbprivacyPlugin' );
$_PLUGINS->registerFunction( 'onAfterTabsFetch', 'tabsFetch', 'cbprivacyPlugin' );
$_PLUGINS->registerFunction( 'onAfterFieldsFetch', 'fieldsFetch', 'cbprivacyPlugin' );
$_PLUGINS->registerFunction( 'onBeforeprepareFieldDataSave', 'fieldPrepareSave', 'cbprivacyPlugin' );
$_PLUGINS->registerFunction( 'onAfterprepareFieldDataNotSaved', 'fieldCommitSave', 'cbprivacyPlugin' );
$_PLUGINS->registerFunction( 'onFieldIcons', 'fieldIcons', 'cbprivacyPlugin' );
$_PLUGINS->registerFunction( 'onBeforegetFieldRow', 'fieldDisplay', 'cbprivacyPlugin' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array(	'privacy_profile' => 'cbprivacyFieldProfile',
											'privacy_disable_me' => 'cbprivacyFieldDisable',
											'privacy_delete_me' => 'cbprivacyFieldDelete'
										));

class cbprivacyClass {

	/**
	 * @param null|array $files
	 * @param bool       $loadGlobal
	 * @param bool       $loadHeader
	 */
	static public function getTemplate( $files = null, $loadGlobal = true, $loadHeader = true )
	{
		global $_CB_framework, $_PLUGINS;

		static $tmpl							=	array();

		if ( ! $files ) {
			$files								=	array();
		} elseif ( ! is_array( $files ) ) {
			$files								=	array( $files );
		}

		$id										=	md5( serialize( array( $files, $loadGlobal, $loadHeader ) ) );

		if ( ! isset( $tmpl[$id] ) ) {
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbprivacy' );

			if ( ! $plugin ) {
				return;
			}

			$livePath							=	$_PLUGINS->getPluginLivePath( $plugin );
			$absPath							=	$_PLUGINS->getPluginPath( $plugin );
			$params								=	$_PLUGINS->getPluginParams( $plugin );

			$template							=	$params->get( 'general_template', 'default' );
			$paths								=	array( 'global_css' => null, 'php' => null, 'css' => null, 'js' => null, 'override_css' => null );

			foreach ( $files as $file ) {
				$file							=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $file );
				$globalCss						=	'/templates/' . $template . '/template.css';
				$overrideCss					=	'/templates/' . $template . '/override.css';

				if ( $file ) {
					$php						=	$absPath . '/templates/' . $template . '/' . $file . '.php';
					$css						=	'/templates/' . $template . '/' . $file . '.css';
					$js							=	'/templates/' . $template . '/' . $file . '.js';
				} else {
					$php						=	null;
					$css						=	null;
					$js							=	null;
				}

				if ( $loadGlobal && $loadHeader ) {
					if ( ! file_exists( $absPath . $globalCss ) ) {
						$globalCss				=	'/templates/default/template.css';
					}

					if ( file_exists( $absPath . $globalCss ) ) {
						$_CB_framework->document->addHeadStyleSheet( $livePath . $globalCss );

						$paths['global_css']	=	$livePath . $globalCss;
					}
				}

				if ( $file ) {
					if ( ! file_exists( $php ) ) {
						$php					=	$absPath . '/templates/default/' . $file . '.php';
					}

					if ( file_exists( $php ) ) {
						require_once( $php );

						$paths['php']			=	$php;
					}

					if ( $loadHeader ) {
						if ( ! file_exists( $absPath . $css ) ) {
							$css				=	'/templates/default/' . $file . '.css';
						}

						if ( file_exists( $absPath . $css ) ) {
							$_CB_framework->document->addHeadStyleSheet( $livePath . $css );

							$paths['css']		=	$livePath . $css;
						}

						if ( ! file_exists( $absPath . $js ) ) {
							$js					=	'/templates/default/' . $file . '.js';
						}

						if ( file_exists( $absPath . $js ) ) {
							$_CB_framework->document->addHeadScriptUrl( $livePath . $js );

							$paths['js']		=	$livePath . $js;
						}
					}
				}

				if ( $loadGlobal && $loadHeader ) {
					if ( file_exists( $absPath . $overrideCss ) ) {
						$_CB_framework->document->addHeadStyleSheet( $livePath . $overrideCss );

						$paths['override_css']	=	$livePath . $overrideCss;
					}
				}
			}

			$tmpl[$id]							=	$paths;
		}
	}

	/**
	 * @param null|int $userId
	 * @return bool
	 */
	static public function checkUserModerator( $userId = null )
	{
		static $cache			=	array();

		if ( $userId === null ) {
			$userId				=	Application::MyUser()->getUserId();
		}

		if ( ! isset( $cache[$userId] ) ) {
			$cache[$userId]		=	Application::User( (int) $userId )->isGlobalModerator();
		}

		return $cache[$userId];
	}

	/**
	 * @param UserTable $user
	 * @param null|int  $fieldId
	 * @return bool
	 */
	static public function checkProfileDisplayAccess( $user, $fieldId = null )
	{
		if ( self::checkUserModerator() ) {
			return true;
		}

		static $field							=	null;
		static $rows							=	array();
		static $cache							=	array();

		$myId									=	Application::MyUser()->getUserId();
		$userId									=	(int) $user->get( 'id' );
		$fieldId								=	(int) $fieldId;

		if ( ! isset( $cache[$userId][$myId][$fieldId] ) ) {
			$authorized							=	true;

			if ( ! $field ) {
				$field							=	new FieldTable();

				$field->load( array( 'name' => 'privacy_profile', 'published' => 1 ) );
			}

			if ( ! ( $field->params instanceof ParamsInterface ) ) {
				$field->params					=	new Registry( $field->params );
			}

			$hideFields							=	cbToArrayOfInt( explode( '|*|', $field->params->get( 'cbprivacy_profile_fields', null ) ) );

			if ( ( $fieldId && in_array( $fieldId, $hideFields ) ) || ( ! $fieldId ) ) {
				if ( ! isset( $rows[$userId] ) ) {
					$row						=	new cbprivacyPrivacyTable();

					$query						=	'SELECT *'
												.	"\n FROM " . $row->getDbo()->NameQuote( $row->getTableName() )
												.	"\n WHERE " . $row->getDbo()->NameQuote( 'user_id' ) . " = " . (int) $userId
												.	"\n AND " . $row->getDbo()->NameQuote( 'type' ) . " = " . $row->getDbo()->Quote( 'profile' )
												.	"\n AND ( " . $row->getDbo()->NameQuote( 'subtype' ) . " IS NULL OR " . $row->getDbo()->NameQuote( 'subtype' ) . " = " . $row->getDbo()->Quote( '' ) . " )";
					$row->getDbo()->setQuery( $query, 0, 1 );
					$row->getDbo()->loadObject( $row );

					$rows[$userId]				=	$row;
				}

				/** @var cbprivacyPrivacyTable $privacy */
				$privacy						=	$rows[$userId];
				$rule							=	$privacy->get( 'rule', $field->params->get( 'cbprivacy_profile_default', '0' ) );

				if ( $rule != '0' ) {
					if ( ! $privacy->get( 'id' ) ) {
						$privacy->set( 'user_id', (int) $userId );
						$privacy->set( 'type', 'profile' );
						$privacy->set( 'rule', $rule );
					}

					if ( ! $privacy->isAuthorized( $myId ) ) {
						$authorized				=	false;
					}
				}
			}

			$cache[$userId][$myId][$fieldId]	=	$authorized;
		}

		return $cache[$userId][$myId][$fieldId];
	}

	/**
	 * @param TabTable|int $tab
	 * @param UserTable    $user
	 * @return bool
	 */
	static public function checkTabDisplayAccess( $tab, $user )
	{
		if ( self::checkUserModerator() ) {
			return true;
		}

		static $rows								=	array();
		static $tabs								=	array();
		static $cache								=	array();

		$return										=	true;

		if ( is_integer( $tab ) ) {
			if ( ! isset( $tabs[$tab] ) ) {
				$loadedTab							=	new TabTable();

				$loadedTab->load( $tab );

				$tabs[$tab]							=	$loadedTab;
			}

			$tab									=	$tabs[$tab];
		}

		if ( $tab instanceof TabTable ) {
			$myId									=	Application::MyUser()->getUserId();
			$userId									=	(int) $user->get( 'id' );
			$tabId									=	(int) $tab->get( 'tabid' );

			if ( ! isset( $cache[$tabId][$userId][$myId] ) ) {
				$authorized							=	true;

				if ( ! ( $tab->params instanceof ParamsInterface ) ) {
					$tab->params					=	new Registry( $tab->params );
				}

				$display							=	$tab->params->get( 'cbprivacy_display', '0' );

				if ( $display > 0 ) {
					if ( $display == 3 ) {
						$privacy					=	new cbprivacyPrivacyTable();
						$rule						=	$tab->params->get( 'cbprivacy_default', '0' );
					} else {
						if ( ! isset( $rows[$userId][$tabId] ) ) {
							$row					=	new cbprivacyPrivacyTable();

							$row->load( array( 'user_id' => (int) $userId, 'type' => 'profile', 'subtype' => 'tab', 'item' => (int) $tabId ) );

							$rows[$userId][$tabId]	=	$row;
						}

						/** @var cbprivacyPrivacyTable $privacy */
						$privacy					=	$rows[$userId][$tabId];
						$rule						=	$privacy->get( 'rule', $tab->params->get( 'cbprivacy_default', '0' ) );
					}

					if ( $rule != '0' ) {
						if ( ! $privacy->get( 'id' ) ) {
							$privacy->set( 'user_id', (int) $userId );
							$privacy->set( 'type', 'profile' );
							$privacy->set( 'subtype', 'tab' );
							$privacy->set( 'item', (int) $tabId );
							$privacy->set( 'rule', $rule );
						}

						if ( ! $privacy->isAuthorized( $myId ) ) {
							$authorized				=	false;
						}
					}
				}

				$cache[$tabId][$userId][$myId]		=	$authorized;
			}

			$return									=	$cache[$tabId][$userId][$myId];
		}

		return $return;
	}

	/**
	 * @param TabTable|int $tab
	 * @return bool
	 */
	static public function checkTabEditAccess( $tab )
	{
		static $tabs					=	array();
		static $cache					=	array();

		$return							=	true;

		if ( is_integer( $tab ) ) {
			if ( ! isset( $tabs[$tab] ) ) {
				$loadedTab				=	new TabTable();

				$loadedTab->load( $tab );

				$tabs[$tab]				=	$loadedTab;
			}

			$tab						=	$tabs[$tab];
		}

		if ( $tab instanceof TabTable ) {
			$myId						=	Application::MyUser()->getUserId();
			$tabId						=	(int) $tab->get( 'tabid' );

			if ( ! isset( $cache[$tabId][$myId] ) ) {
				$authorized				=	true;

				if ( ! ( $tab->params instanceof ParamsInterface ) ) {
					$tab->params		=	new Registry( $tab->params );
				}

				$display				=	$tab->params->get( 'cbprivacy_edit', '0' );

				if ( ( $display == 1 ) || ( ( $display == 2 ) && ( ! self::checkUserModerator() ) ) || ( ( $display == 3 ) && ( ! Application::MyUser()->canViewAccessLevel( $tab->params->get( 'cbprivacy_edit_access', '1' ) ) ) ) ) {
					$authorized			=	false;
				}

				$cache[$tabId][$myId]	=	$authorized;
			}

			$return						=	$cache[$tabId][$myId];
		}

		return $return;
	}

	/**
	 * @param FieldTable|int $field
	 * @param UserTable      $user
	 * @return bool
	 */
	static public function checkFieldDisplayAccess( $field, $user )
	{
		if ( self::checkUserModerator() ) {
			return true;
		}

		static $rows									=	array();
		static $fields									=	array();
		static $cache									=	array();

		if ( is_integer( $field ) ) {
			if ( ! isset( $fields[$field] ) ) {
				$loadedField							=	new FieldTable();

				$loadedField->load( $field );

				$fields[$field]							=	$loadedField;
			}

			$field										=	$fields[$field];
		}

		$return											=	true;

		if ( $field instanceof FieldTable ) {
			$myId										=	Application::MyUser()->getUserId();
			$userId										=	(int) $user->get( 'id' );
			$fieldId									=	(int) $field->get( 'fieldid' );

			if ( ! isset( $cache[$fieldId][$userId][$myId] ) ) {
				$authorized								=	true;
				$tabId									=	(int) $field->get( 'tabid' );

				if ( ! ( $field->params instanceof ParamsInterface ) ) {
					$field->params						=	new Registry( $field->params );
				}

				$display								=	$field->params->get( 'cbprivacy_display', '0' );

				if ( $display > 0 ) {
					if ( $display == 3 ) {
						$privacy						=	new cbprivacyPrivacyTable();
						$rule							=	$field->params->get( 'cbprivacy_default', '0' );
					} else {
						if ( ! isset( $rows[$userId][$fieldId] ) ) {
							$row						=	new cbprivacyPrivacyTable();

							$row->load( array( 'user_id' => (int) $userId, 'type' => 'profile', 'subtype' => 'field', 'item' => (int) $fieldId ) );

							$rows[$userId][$fieldId]	=	$row;
						}

						/** @var cbprivacyPrivacyTable $privacy */
						$privacy						=	$rows[$userId][$fieldId];
						$rule							=	$privacy->get( 'rule', $field->params->get( 'cbprivacy_default', '0' ) );
					}

					if ( $rule != '0' ) {
						if ( ! $privacy->get( 'id' ) ) {
							$privacy->set( 'user_id', (int) $userId );
							$privacy->set( 'type', 'profile' );
							$privacy->set( 'subtype', 'field' );
							$privacy->set( 'item', (int) $fieldId );
							$privacy->set( 'rule', $rule );
						}

						if ( ! $privacy->isAuthorized( $myId ) ) {
							$authorized					=	false;
						}
					}
				}

				if ( $authorized && ( ! cbprivacyClass::checkTabDisplayAccess( $tabId, $user ) ) ) {
					$authorized							=	false;
				}

				if ( $authorized && ( ! cbprivacyClass::checkProfileDisplayAccess( $user, $fieldId ) ) ) {
					$authorized							=	false;
				}

				$cache[$fieldId][$userId][$myId]		=	$authorized;
			}

			$return										=	$cache[$fieldId][$userId][$myId];
		}

		return $return;
	}

	/**
	 * @param FieldTable|int $field
	 * @return bool
	 */
	static public function checkFieldEditAccess( $field )
	{
		static $fields						=	array();
		static $cache						=	array();

		if ( is_integer( $field ) ) {
			if ( ! isset( $fields[$field] ) ) {
				$loadedField				=	new FieldTable();

				$loadedField->load( $field );

				$fields[$field]				=	$loadedField;
			}

			$field							=	$fields[$field];
		}

		$return								=	true;

		if ( $field instanceof FieldTable ) {
			$myId							=	Application::MyUser()->getUserId();
			$fieldId						=	(int) $field->get( 'fieldid' );

			if ( ! isset( $cache[$fieldId][$myId] ) ) {
				$authorized					=	true;
				$tabId						=	(int) $field->get( 'tabid' );

				if ( ! ( $field->params instanceof ParamsInterface ) ) {
					$field->params			=	new Registry( $field->params );
				}

				$display					=	$field->params->get( 'cbprivacy_edit', '0' );

				if ( ( $display == 1 ) || ( ( $display == 2 ) && ( ! self::checkUserModerator() ) ) || ( ( $display == 3 ) && ( ! Application::MyUser()->canViewAccessLevel( $field->params->get( 'cbprivacy_edit_access', '1' ) ) ) ) ) {
					$authorized				=	false;
				}

				if ( $authorized && ( ! cbprivacyClass::checkTabEditAccess( $tabId ) ) ) {
					$authorized				=	false;
				}

				$cache[$fieldId][$myId]		=	$authorized;
			}

			$return							=	$cache[$fieldId][$myId];
		}

		return $return;
	}

	/**
	 * Returns the privacy selector
	 *
	 * @param string       $name
	 * @param string|array $value
	 * @param null|string  $attributes
	 * @return string
	 */
	static public function getPrivacyInput( $name, $value = '0', $attributes = null )
	{
		global $_CB_framework;

		$options		=	self::getPrivacyOptions();
		$value			=	self::validatePrivacy( $value );

		static $loaded	=	0;

		if ( ! $loaded++ ) {
			$js			=	"$( '.cbPrivacyInput' ).cbselect({"
						.		"width: 'auto'"
						.	"}).on( 'cbselect.selecting', function( e, cbselect, value ) {"
						.		"var selected = $( this ).cbselect( 'get' );"
						.		"if ( value == 0 ) {"
						.			"$( this ).cbselect( 'set', '0' );"
						.		"} else if ( value == 1 ) {"
						.			"$( this ).cbselect( 'set', '1' );"
						.		"} else if ( value == 99 ) {"
						.			"$( this ).cbselect( 'set', '99' );"
						.		"} else if ( value == 2 ) {"
						.			"var unset = ['0', '1', '99'];"
						.			"$.each( selected, function( i, v ) {"
						.				"if ( v.indexOf( 'CONN-' ) > -1 ) {"
						.					"unset.push( v );"
						.				"}"
						.			"});"
						.			"$( this ).cbselect( 'unset', unset );"
						.		"} else {"
						.			"if ( selected.indexOf( '0' ) > -1 ) {"
						.				"$( this ).cbselect( 'unset', '0' );"
						.			"} else if ( selected.indexOf( '1' ) > -1 ) {"
						.				"$( this ).cbselect( 'unset', '1' );"
						.			"} else if ( selected.indexOf( '99' ) > -1 ) {"
						.				"$( this ).cbselect( 'unset', '99' );"
						.			"} else if ( ( selected.indexOf( '2' ) > -1 ) && ( value.indexOf( 'CONN-' ) > -1 ) ) {"
						.				"$( this ).cbselect( 'unset', '2' );"
						.			"}"
						.		"}"
						.	"}).on( 'change', function() {"
						.		"var value = $( this ).cbselect( 'get' );"
						.		"if ( ( value == null ) || ( value.length == 0 ) ) {"
						.			"value = $( this ).cbselect( 'set', '" . addslashes( (string) $options[0]->value ) . "' );"
						.		"}"
						.	"}).change();";

			$_CB_framework->outputCbJQuery( $js, 'cbselect' );
		}

		self::getTemplate( 'selector' );

		return moscomprofilerHTML::selectList( $options, $name . '[]', 'class="form-control cbPrivacyInput" multiple="multiple"' . ( $attributes ? ' ' . $attributes : null ), 'value', 'text', $value, 0, false, false, false );
	}

	/**
	 * Validates selected privacy value as some values can't be selected with others
	 *
	 * @param array $value
	 * @return array
	 */
	static public function validatePrivacy( $value )
	{
		global $_PLUGINS;

		if ( $value == '' ) {
			return array();
		}

		$options			=	self::getPrivacyOptions();
		$values				=	array();

		foreach( $options as $option ) {
			$values[]		=	$option->value;
		}

		if ( ! is_array( $value ) ) {
			$value			=	explode( '|*|', $value );
		}

		$_PLUGINS->trigger( 'privacy_onBeforeValidatePrivacy', array( &$value, &$values, $options ) );

		foreach ( $value as $k => $v ) {
			if ( ! in_array( $v, $values ) ) {
				unset( $value[$k] );
			}
		}

		if ( in_array( '0', $value ) ) {
			$value			=	array( '0' );
		} elseif ( in_array( '1', $value ) ) {
			$value			=	array( '1' );
		} elseif ( in_array( '2', $value ) ) {
			if ( in_array( '3', $value ) ) {
				$value		=	array( '2', '3' );
			} else {
				$value		=	array( '2' );
			}
		} elseif ( in_array( '99', $value ) ) {
			$value			=	array( '99' );
		}

		$_PLUGINS->trigger( 'privacy_onAfterValidatePrivacy', array( &$value, $values, $options ) );

		return $value;
	}

	/**
	 * Returns an options array of available privacy values
	 *
	 * @return array
	 */
	static public function getPrivacyOptions()
	{
		global $_PLUGINS, $ueConfig;

		static $cache						=	null;

		$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbprivacy' );

		if ( ! $plugin ) {
			return array();
		}

		$params								=	$_PLUGINS->getPluginParams( $plugin );

		if ( $cache === null ) {
			$cache							=	array();

			$_PLUGINS->trigger( 'privacy_onBeforePrivacyOptions', array( &$cache ) );

			if ( $params->get( 'privacy_options_visible', 1 ) ) {
				$cache[]					=	moscomprofilerHTML::makeOption( '0', CBTxt::T( 'Public' ) );
			}

			if ( ( ( $ueConfig['profile_viewaccesslevel'] == 1 ) && $params->get( 'privacy_options_users', 1 ) ) ) {
				$cache[]					=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Users' ) );
			}

			if ( $params->get( 'privacy_options_invisible', 1 ) ) {
				$cache[]					=	moscomprofilerHTML::makeOption( '99', CBTxt::T( 'Private' ) );
			}

			if ( $ueConfig['allowConnections'] ) {
				if ( $params->get( 'privacy_options_conn', 1 ) ) {
					$cache[]				=	moscomprofilerHTML::makeOption( '2', CBTxt::T( 'Connections' ) );
				}

				if ( $params->get( 'privacy_options_connofconn', 1 ) ) {
					$cache[]				=	moscomprofilerHTML::makeOption( '3', CBTxt::T( 'Connections of Connections' ) );
				}

				if ( $ueConfig['connection_categories'] && ( $params->get( 'privacy_options_conntypes', '0' ) != '' ) ) {
					$connTypes				=	explode( '|*|', $params->get( 'privacy_options_conntypes', '0' ) );
					$types					=	self::getConnectionTypes();

					if ( $types ) {
						$cache[]			=	moscomprofilerHTML::makeOptGroup( CBTxt::T( 'Connection Types' ) );

						foreach ( $types as $type ) {
							if ( in_array( '0', $connTypes ) || in_array( $type->value, $connTypes ) ) {
								$cache[]	=	moscomprofilerHTML::makeOption( 'CONN-' . (string) $type->value, $type->text );
							}
						}

						$cache[]			=	moscomprofilerHTML::makeOptGroup( null );
					}
				}
			}

			if ( $params->get( 'privacy_options_viewaccesslevels', '' ) != '' ) {
				$viewAccessLevels			=	explode( '|*|', $params->get( 'privacy_options_viewaccesslevels', '' ) );
				$accessLevels				=	Application::CmsPermissions()->getAllViewAccessLevels( true, Application::MyUser() );

				if ( $accessLevels ) {
					$cache[]				=	moscomprofilerHTML::makeOptGroup( CBTxt::T( 'View Access Levels' ) );

					foreach ( $accessLevels as $accessLevel ) {
						if ( in_array( '0', $viewAccessLevels ) || in_array( $accessLevel->value, $viewAccessLevels ) ) {
							$cache[]		=	moscomprofilerHTML::makeOption( 'ACCESS-' . (string) $accessLevel->value, CBTxt::T( $accessLevel->text ) );
						}
					}

					$cache[]				=	moscomprofilerHTML::makeOptGroup( null );
				}
			}

			if ( $params->get( 'privacy_options_usergroups', '' ) != '' ) {
				$userGroups					=	explode( '|*|', $params->get( 'privacy_options_usergroups', '' ) );
				$groups						=	Application::CmsPermissions()->getAllGroups( true, '' );

				if ( $groups ) {
					$cache[]				=	moscomprofilerHTML::makeOptGroup( CBTxt::T( 'Usergroups' ) );

					foreach ( $groups as $group ) {
						if ( in_array( '0', $userGroups ) || in_array( $group->value, $userGroups ) ) {
							$cache[]		=	moscomprofilerHTML::makeOption( 'GROUP-' . (string) $group->value, CBTxt::T( $group->text ) );
						}
					}

					$cache[]				=	moscomprofilerHTML::makeOptGroup( null );
				}
			}

			$_PLUGINS->trigger( 'privacy_onAfterPrivacyOptions', array( &$cache ) );
		}

		return $cache;
	}

	/**
	 * Returns an options array of connection types
	 *
	 * @return array
	 */
	static public function getConnectionTypes()
	{
		global $ueConfig;

		$options			=	array();

		if ( $ueConfig['connection_categories'] ) {
			$types			=	explode( "\n", $ueConfig['connection_categories'] );

			foreach ( $types as $type ) {
				$options[]	=	moscomprofilerHTML::makeOption( trim( htmlspecialchars( $type ) ), CBTxt::T( trim( $type ) ) );
			}
		}

		return $options;
	}
}

class cbprivacyPrivacyTable extends Table
{
	var $id				=	null;
	var $user_id		=	null;
	var $type			=	null;
	var $subtype		=	null;
	var $item			=	null;
	var $rule			=	null;
	var $params			=	null;

	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl		=	'#__comprofiler_plugin_privacy';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key	=	'id';

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'privacy_onBeforeUpdatePrivacy', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'privacy_onBeforeCreatePrivacy', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'privacy_onAfterUpdatePrivacy', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'privacy_onAfterCreatePrivacy', array( $this ) );
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

		$_PLUGINS->trigger( 'privacy_onBeforeDeletePrivacy', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'privacy_onAfterDeletePrivacy', array( $this ) );

		return true;
	}

	/**
	 * @param int $userId
	 * @return mixed
	 */
	public function isAuthorized( $userId )
	{
		global $_PLUGINS;

		static $cache											=	array();

		$id														=	$this->get( 'id' );
		$owner													=	(int) $this->get( 'user_id' );
		$userId													=	(int) $userId;

		if ( ! $id ) {
			// Generate an ID regarding a privacy rows data since it hasn't been stored yet (default):
			$id													=	$owner;

			if ( $this->get( 'type' ) ) {
				$id												.=	'.' . $this->get( 'type' );
			}

			if ( $this->get( 'subtype' ) ) {
				$id												.=	'.' . $this->get( 'subtype' );
			}

			if ( $this->get( 'item' ) ) {
				$id												.=	'.' . $this->get( 'item' );
			}

			if ( $this->get( 'rule' ) ) {
				$id												.=	'.' . $this->get( 'rule' );
			}
		}

		if ( ! isset( $cache[$userId][$id] ) ) {
			$rules												=	explode( '|*|', $this->get( 'rule' ) );
			$cache[$userId][$id]								=	false;

			$_PLUGINS->trigger( 'privacy_onBeforeIsAuthorized', array( &$cache[$userId][$id], $rules, $userId, $this ) );

			if ( empty( $rules ) || in_array( '0', $rules ) || ( $userId == $owner ) ) {
				$cache[$userId][$id]							=	true;
			} elseif ( in_array( '1', $rules ) ) {
				if ( $userId > 0 ) {
					$cache[$userId][$id]						=	true;
				}
			} elseif ( in_array( '99', $rules ) ) {
				$cache[$userId][$id]							=	false;
			} else {
				$types											=	array();

				foreach ( $rules as $rule ) {
					if ( substr( $rule, 0, 5 ) == 'CONN-' ) {
						$types[]								=	str_replace( 'CONN-', '', $rule );
					}
				}

				$access											=	array();

				foreach ( $rules as $rule ) {
					if ( substr( $rule, 0, 7 ) == 'ACCESS-' ) {
						$access[]								=	str_replace( 'ACCESS-', '', $rule );
					}
				}

				$groups											=	array();

				foreach ( $rules as $rule ) {
					if ( substr( $rule, 0, 6 ) == 'GROUP-' ) {
						$groups[]								=	str_replace( 'GROUP-', '', $rule );
					}
				}

				if ( ( $cache[$userId][$id] == false ) && ( in_array( '2', $rules ) || $types ) ) {
					static $connections							=	array();

					if ( ! isset( $connections[$userId][$owner] ) ) {
						$cbConnection							=	new cbConnection( $userId );

						$connections[$userId][$owner]			=	$cbConnection->getConnectionDetails( $owner, $userId );
					}

					$connection									=	$connections[$userId][$owner];

					if ( $connection && ( $connection->accepted == 1 ) && ( $connection->pending == 0 ) ) {
						if ( in_array( '2', $rules ) ) {
							$cache[$userId][$id]				=	true;
						} else {
							if ( $connection->type ) {
								$connTypes						=	explode( '|*|', $connection->type );

								foreach ( $connTypes as $connType ) {
									if ( in_array( trim( htmlspecialchars( $connType ) ), $types ) ) {
										$cache[$userId][$id]	=	true;
									}
								}
							}
						}
					}
				}

				if ( ( $cache[$userId][$id] == false ) && in_array( '3', $rules ) ) {
					static $subConnections						=	array();

					if ( ! isset( $subConnections[$userId][$owner] ) ) {
						$cbConnection							=	new cbConnection( $userId );

						$subConnections[$userId][$owner]		=	$cbConnection->getDegreeOfSepPathArray( $owner, $userId, 1, 2 );
					}

					if ( ! empty( $subConnections[$userId][$owner] ) ) {
						$cache[$userId][$id]					=	true;
					}
				}

				if ( ( $cache[$userId][$id] == false ) && $access ) {
					static $accessLevels						=	array();

					if ( ! isset( $accessLevels[$userId] ) ) {
						$accessLevels[$userId]					=	Application::User( $userId )->getAuthorisedViewLevels();
					}

					$usersAccess								=	$accessLevels[$userId];

					foreach ( $access as $accessLevel ) {
						if ( ( $cache[$userId][$id] == false ) && in_array( $accessLevel, $usersAccess ) ) {
							$cache[$userId][$id]				=	true;
						}
					}
				}

				if ( ( $cache[$userId][$id] == false ) && $groups ) {
					static $userGroups							=	array();

					if ( ! isset( $userGroups[$userId] ) ) {
						$userGroups[$userId]					=	Application::User( $userId )->getAuthorisedGroups();
					}

					$usersGroups								=	$userGroups[$userId];

					foreach ( $groups as $group ) {
						if ( ( $cache[$userId][$id] == false ) && in_array( $group, $usersGroups ) ) {
							$cache[$userId][$id]				=	true;
						}
					}
				}
			}

			$_PLUGINS->trigger( 'privacy_onAfterIsAuthorized', array( &$cache[$userId][$id], $rules, $userId, $this ) );
		}

		return $cache[$userId][$id];
	}
}

class cbprivacyClosedTable extends Table
{
	var $id				=	null;
	var $user_id		=	null;
	var $username		=	null;
	var $name			=	null;
	var $email			=	null;
	var $type			=	null;
	var $date			=	null;
	var $reason			=	null;
	var $params			=	null;

	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl		=	'#__comprofiler_plugin_privacy_closed';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key	=	'id';

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'privacy_onBeforeUpdateClosed', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'privacy_onBeforeCreateClosed', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'privacy_onAfterUpdateClosed', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'privacy_onAfterCreateClosed', array( $this ) );
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

		$_PLUGINS->trigger( 'privacy_onBeforeDeleteClosed', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'privacy_onAfterDeleteClosed', array( $this ) );

		return true;
	}
}

class cbprivacyPlugin extends cbPluginHandler
{

	/**
	 * @param int    $uid
	 * @param string $msg
	 */
	public function getProfile( $uid, &$msg )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( ! cbprivacyClass::checkUserModerator() ) ) {
			$user		=	loadComprofilerUser( $uid );

			if ( $user && ( Application::MyUser()->getUserId() != $user->get( 'id' ) ) && ( ! cbprivacyClass::checkProfileDisplayAccess( $user ) ) ) {
				$msg	=	CBTxt::Th( 'UE_NOT_AUTHORIZED', 'You are not authorized to view this page!' );
			}
		}
	}

	/**
	 * @param ListTable    $row
	 * @param UserTable[]  $users
	 * @param array        $columns
	 * @param FieldTable[] $fields
	 * @param array        $input
	 * @param int          $listid
	 * @param string|null  $search
	 * @param int          $Itemid
	 * @param int          $ui
	 */
	public function getList( &$row, &$users, &$columns, &$fields, &$input, $listid, &$search, &$Itemid, $ui )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( ! cbprivacyClass::checkUserModerator() ) ) {
			if ( $users ) foreach( $users as $k => $user ) {
				if ( isset( $users[$k] ) && ( Application::MyUser()->getUserId() != $user->get( 'id' ) ) ) {
					if ( ! cbprivacyClass::checkProfileDisplayAccess( $user ) ) {
						unset( $users[$k] );
					} else {
						if ( $fields ) foreach ( $fields as $field ) {
							if ( ( $search !== null ) && cbGetParam( $_REQUEST, $field->get( 'name' ), null ) && ( ! cbprivacyClass::checkFieldDisplayAccess( $field, $user ) ) ) {
								unset( $users[$k] );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * @param UserTable $user
	 */
	public function deletePrivacy( $user )
	{
		global $_CB_database;

		$query		=	'SELECT *'
					.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_privacy' )
					.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->id;
		$_CB_database->setQuery( $query );
		$rows		=	$_CB_database->loadObjectList( null, 'cbprivacyPrivacyTable', array( $_CB_database ) );

		/** @var cbprivacyPrivacyTable[] $rows */
		foreach ( $rows as $row ) {
			$row->delete();
		}
	}

	/**
	 * @param string    $content
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param array     $postdata
	 * @param string    $output
	 * @param string    $formatting
	 * @param string    $reason
	 * @param bool      $tabbed
	 */
	public function tabEdit( &$content, &$tab, &$user, &$postdata, $output, $formatting, $reason, $tabbed )
	{
		if ( in_array( $reason, array( 'edit', 'register' ) ) ) {
			if ( $tab instanceof TabTable ) {
				$userId						=	(int) $user->get( 'id' );
				$tabId						=	(int) $tab->get( 'tabid' );

				if ( ( $reason != 'register' ) && ( ! Application::Cms()->getClientId() ) && ( ! cbprivacyClass::checkUserModerator() ) && ( ! cbprivacyClass::checkTabEditAccess( $tab ) ) ) {
					$content				=	' ';
				} else {
					if ( ! ( $tab->params instanceof ParamsInterface ) ) {
						$tab->params		=	new Registry( $tab->params );
					}

					$display				=	$tab->params->get( 'cbprivacy_display', '0' );

					if ( ( $reason == 'register' ) && ( ! $tab->params->get( 'cbprivacy_display_reg', '0' ) ) ) {
						$display			=	'0';
					}

					if ( ( $display == 1 ) || ( ( $display == 2 ) && cbprivacyClass::checkUserModerator() ) ) {
						$privacy			=	new cbprivacyPrivacyTable();

						$privacy->load( array( 'user_id' => (int) $userId, 'type' => 'profile', 'subtype' => 'tab', 'item' => (int) $tabId ) );

						$input				=	cbprivacyClass::getPrivacyInput( 'privacy_tab_' . $tabId, $privacy->get( 'rule', $tab->params->get( 'cbprivacy_default', '0' ) ) );
						$return				=	null;

						switch ( $formatting ) {
							case 'tabletrs':
								$return		.=	'<tr id="cbtp_' . (int) $tabId . '" class="cb_table_line cbft_privacy cbtt_select cb_table_line_field">'
											.		'<td class="fieldCell text-right" colspan="2" style="width: 100%;">'
											.			$input
											.		'</td>'
											.	'</tr>';
								break;
							default:
								$return		.=	'<div class="cbft_privacy cbtt_select form-group cb_form_line clearfix cbtwolinesfield" id="cbtp_' . (int) $tabId . '">'
											.		'<div class="cb_field col-sm-12">'
											.			'<div class="text-right">'
											.				$input
											.			'</div>'
											.		'</div>'
											.	'</div>';
								break;
						}

						$content			=	$return
											.	$content;
					}
				}
			}
		}
	}

	/**
	 * @param TabTable[] $tabs
	 * @param UserTable  $user
	 * @param string     $reason
	 */
	public function tabsFetch( &$tabs, &$user, $reason )
	{
		static $rows								=	array();

		$canCheck									=	( ( ! Application::Cms()->getClientId() ) && ( ! cbprivacyClass::checkUserModerator() ) );
		$checkUser									=	( $user && ( $user instanceof UserTable ) && ( ! $user->getError() ) );

		if ( $canCheck && ( $reason == 'profile' ) && $checkUser && ( Application::MyUser()->getUserId() != $user->get( 'id' ) ) ) {
			if ( $tabs ) foreach ( $tabs as $tabId => $tab ) {
				if ( isset( $tabs[$tabId] ) && ( ! cbprivacyClass::checkTabDisplayAccess( $tab, $user ) ) ) {
					unset( $tabs[$tabId] );
				}
			}
		} elseif ( ( $reason == 'editsave' ) && $checkUser && $user->get( 'id' ) ) {
			if ( $tabs ) foreach ( $tabs as $tabId => $tab ) {
				if ( isset( $tabs[$tabId] ) && $canCheck && ( ! cbprivacyClass::checkTabEditAccess( $tab ) ) ) {
					unset( $tabs[$tabId] );

					continue;
				}

				if ( $tab instanceof TabTable ) {
					$userId							=	(int) $user->get( 'id' );
					$tabId							=	(int) $tab->get( 'tabid' );
					$value							=	implode( '|*|', cbprivacyClass::validatePrivacy( $this->input( 'privacy_tab_' . $tabId, '0', GetterInterface::RAW ) ) );

					if ( $value != '' ) {
						if ( ! ( $tab->params instanceof ParamsInterface ) ) {
							$tab->params			=	new Registry( $tab->params );
						}

						if ( ! isset( $rows[$userId][$tabId] ) ) {
							$row					=	new cbprivacyPrivacyTable();

							$row->load( array( 'user_id' => (int) $userId, 'type' => 'profile', 'subtype' => 'tab', 'item' => (int) $tabId ) );

							$rows[$userId][$tabId]	=	$row;
						}

						/** @var cbprivacyPrivacyTable $privacy */
						$privacy					=	$rows[$userId][$tabId];
						$rule						=	$privacy->get( 'rule', $tab->params->get( 'cbprivacy_display', '0' ) );

						if ( ( ! $privacy->get( 'id' ) ) || ( $rule != $value ) ) {
							if ( ! $privacy->get( 'id' ) ) {
								$privacy->set( 'user_id', (int) $user->get( 'id' ) );
								$privacy->set( 'type', 'profile' );
								$privacy->set( 'subtype', 'tab' );
								$privacy->set( 'item', (int) $tabId );
							}

							$privacy->set( 'rule', $value );

							$privacy->store();
						}
					}
				}
			}
		} elseif ( $canCheck && ( $reason == 'edit' ) && $checkUser && $user->get( 'id' ) ) {
			if ( $tabs ) foreach ( $tabs as $tabId => $tab ) {
				if ( isset( $tabs[$tabId] ) && ( ! cbprivacyClass::checkTabEditAccess( $tab ) ) ) {
					unset( $tabs[$tabId] );
				}
			}
		}
	}

	/**
	 * @param FieldTable[] $fields
	 * @param UserTable    $user
	 * @param string       $reason
	 * @param int          $tabid
	 * @param int|string   $fieldIdOrName
	 * @param bool         $fullAccess
	 */
	public function fieldsFetch( &$fields, &$user, $reason, $tabid, $fieldIdOrName, $fullAccess )
	{
		if ( $fieldIdOrName ) {
			// getFields usage provides this and in this case $user is the viewing user and not the profile owner so skip this check:
			return;
		}

		$checkUser		=	( $user && ( $user instanceof UserTable ) && ( ! $user->getError() ) );

		if ( ( ! Application::Cms()->getClientId() ) && ( ! cbprivacyClass::checkUserModerator() ) && ( ! $fullAccess ) && $checkUser ) {
			if ( ( $reason == 'profile' ) && ( Application::MyUser()->getUserId() != $user->get( 'id' ) ) ) {
				if ( $fields ) foreach ( $fields as $fieldId => $field ) {
					if ( isset( $fields[$fieldId] ) && $field->get( 'profile' ) && ( ! cbprivacyClass::checkFieldDisplayAccess( $field, $user ) )  ) {
						unset( $fields[$fieldId] );
					}
				}
			} elseif ( ( $reason == 'edit' ) && $user->get( 'id' ) ) {
				if ( $fields ) foreach ( $fields as $fieldId => $field ) {
					if ( isset( $fields[$fieldId] ) && ( ! cbprivacyClass::checkFieldEditAccess( $field ) ) ) {
						unset( $fields[$fieldId] );
					}
				}
			}
		}
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $formatting
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null|string
	 */
	public function fieldDisplay( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$return					=	null;

		if ( ( ! $field->get( '_noPrivacy', false ) ) && ( ! Application::Cms()->getClientId() ) && ( ! cbprivacyClass::checkUserModerator() ) ) {
			$field->set( '_noPrivacy', true );

			if ( ( $output == 'html' ) && ( $reason != 'search' ) && $field->get( 'profile' ) && ( Application::MyUser()->getUserId() != $user->get( 'id' ) ) ) {
				if ( ! cbprivacyClass::checkFieldDisplayAccess( $field, $user ) ) {
					$return		=	' ';
				}
			} elseif ( ( $output == 'htmledit' ) && ( $reason != 'search' ) && $user->get( 'id' ) ) {
				if ( ! cbprivacyClass::checkFieldEditAccess( $field ) ) {
					$return		=	' ';
				}
			}

			$field->set( '_noPrivacy', false );
		}

		return $return;
	}

	/**
	 * @param cbFieldHandler $fieldHandler
	 * @param FieldTable     $field
	 * @param UserTable      $user
	 * @param string         $output
	 * @param string         $reason
	 * @param string         $tag
	 * @param string         $type
	 * @param string         $value
	 * @param string         $additional
	 * @param string         $allValues
	 * @param bool           $displayFieldIcons
	 * @param bool           $required
	 * @return null|string
	 */
	public function fieldIcons( &$fieldHandler, &$field, &$user, $output, $reason, $tag, $type, $value, $additional, $allValues, $displayFieldIcons, $required )
	{
		global $_CB_fieldIconDisplayed;

		static $fieldsPrivacyDisplayed							=	array();

		$return													=	null;

		if ( in_array( $reason, array( 'edit', 'register' ) ) ) {
			if ( $field instanceof FieldTable && $field->get( 'profile' ) ) {
				$userId											=	(int) $user->get( 'id' );
				$fieldId										=	(int) $field->get( 'fieldid' );

				if ( ! isset( $fieldsPrivacyDisplayed[$fieldId] ) ) {
					if ( ! ( $field->params instanceof ParamsInterface ) ) {
						$field->params							=	new Registry( $field->params );
					}

					$display									=	$field->params->get( 'cbprivacy_display', '0' );

					if ( ( $reason == 'register' ) && ( ! $field->params->get( 'cbprivacy_display_reg', '1' ) ) ) {
						$display								=	'0';
					}

					if ( ( $display == 1 ) || ( ( $display == 2 ) && cbprivacyClass::checkUserModerator() ) ) {
						$privacy								=	new cbprivacyPrivacyTable();

						$privacy->load( array( 'user_id' => (int) $userId, 'type' => 'profile', 'subtype' => 'field', 'item' => (int) $fieldId ) );

						$fieldsPrivacyDisplayed[$fieldId]		=	true;

						$return									=	cbprivacyClass::getPrivacyInput( 'privacy_field_' . $fieldId, $privacy->get( 'rule', $field->params->get( 'cbprivacy_default', '0' ) ) );

						if ( ! isset( $_CB_fieldIconDisplayed[$fieldId] ) ) {
							$_CB_fieldIconDisplayed[$fieldId]	=	true;

							if ( $displayFieldIcons ) {
								$return							.=	' ' . getFieldIcons( null, $required, null, $fieldHandler->getFieldDescription( $field, $user, $output, $reason ), $fieldHandler->getFieldTitle( $field, $user, $output, $reason ), false, $field->params->get( 'fieldLayoutIcons', null ) );
							}
						}
					}
				}
			}
		}

		return $return;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param array      $postdata
	 * @param string     $reason
	 * @return null|string
	 */
	public function fieldPrepareSave( &$field, &$user, &$postdata, $reason )
	{
		$return				=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( ! cbprivacyClass::checkUserModerator() ) && ( $reason != 'search' ) && $user->get( 'id' ) ) {
			if ( ! cbprivacyClass::checkFieldEditAccess( $field ) ) {
				$return		=	' ';
			}
		}

		$this->fieldCommitSave( $field, $user, $postdata, $reason );

		return $return;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param array      $postdata
	 * @param string     $reason
	 */
	public function fieldCommitSave( &$field, &$user, &$postdata, $reason )
	{
		static $rows								=	array();

		if ( ( $reason != 'search' ) && $field->get( 'profile' ) && $user->get( 'id' ) ) {
			if ( $field instanceof FieldTable ) {
				$userId								=	(int) $user->get( 'id' );
				$fieldId							=	(int) $field->get( 'fieldid' );
				$value								=	implode( '|*|', cbprivacyClass::validatePrivacy( $this->input( 'privacy_field_' . $fieldId, '0', GetterInterface::RAW ) ) );

				if ( $value != '' ) {
					if ( ! ( $field->params instanceof ParamsInterface ) ) {
						$field->params				=	new Registry( $field->params );
					}

					if ( ! isset( $rows[$userId][$fieldId] ) ) {
						$row						=	new cbprivacyPrivacyTable();

						$row->load( array( 'user_id' => (int) $userId, 'type' => 'profile', 'subtype' => 'field', 'item' => (int) $fieldId ) );

						$rows[$userId][$fieldId]	=	$row;
					}

					/** @var cbprivacyPrivacyTable $privacy */
					$privacy						=	$rows[$userId][$fieldId];
					$rule							=	$privacy->get( 'rule', $field->params->get( 'cbprivacy_display', '0' ) );

					if ( ( ! $privacy->get( 'id' ) ) || ( $rule != $value ) ) {
						if ( ! $privacy->get( 'id' ) ) {
							$privacy->set( 'user_id', (int) $user->get( 'id' ) );
							$privacy->set( 'type', 'profile' );
							$privacy->set( 'subtype', 'field' );
							$privacy->set( 'item', (int) $fieldId );
						}

						$privacy->set( 'rule', $value );

						$privacy->store();
					}
				}
			}
		}
	}
}

class cbprivacyFieldProfile extends cbFieldHandler
{

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $formatting
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$return			=	null;

		if ( ( $output == 'htmledit' ) && in_array( $reason, array( 'edit', 'register' ) ) ) {
			$field->set( 'profile', 0 );
			$field->set( 'readonly', 0 );

			$return		=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}

		return $return;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null|string
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		$return			=	null;

		if ( ( $output == 'htmledit' ) && in_array( $reason, array( 'edit', 'register' ) ) ) {
			$privacy	=	new cbprivacyPrivacyTable();

			$query		=	'SELECT *'
						.	"\n FROM " . $privacy->getDbo()->NameQuote( $privacy->getTableName() )
						.	"\n WHERE " . $privacy->getDbo()->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
						.	"\n AND " . $privacy->getDbo()->NameQuote( 'type' ) . " = " . $privacy->getDbo()->Quote( 'profile' )
						.	"\n AND ( " . $privacy->getDbo()->NameQuote( 'subtype' ) . " IS NULL OR " . $privacy->getDbo()->NameQuote( 'subtype' ) . " = " . $privacy->getDbo()->Quote( '' ) . " )";
			$privacy->getDbo()->setQuery( $query, 0, 1 );
			$privacy->getDbo()->loadObject( $privacy );

			$value		=	cbprivacyClass::getPrivacyInput( $field->get( 'name' ), $privacy->get( 'rule', $field->params->get( 'cbprivacy_profile_default', '0' ) ) );

			$return		=	$this->formatFieldValueLayout( $value, $reason, $field, $user, false )
						.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, 'html', $value, null, null, true, 0 );
		}

		return $return;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param array      $postdata
	 * @param string     $reason
	 */
	public function commitFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		if ( in_array( $reason, array( 'edit', 'register' ) ) ) {
			$value			=	implode( '|*|', cbprivacyClass::validatePrivacy( $this->input( $field->get( 'name' ), '0', GetterInterface::RAW ) ) );

			if ( $value != '' ) {
				$privacy	=	new cbprivacyPrivacyTable();

				$query		=	'SELECT *'
							.	"\n FROM " . $privacy->getDbo()->NameQuote( $privacy->getTableName() )
							.	"\n WHERE " . $privacy->getDbo()->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
							.	"\n AND " . $privacy->getDbo()->NameQuote( 'type' ) . " = " . $privacy->getDbo()->Quote( 'profile' )
							.	"\n AND ( " . $privacy->getDbo()->NameQuote( 'subtype' ) . " IS NULL OR " . $privacy->getDbo()->NameQuote( 'subtype' ) . " = " . $privacy->getDbo()->Quote( '' ) . " )";
				$privacy->getDbo()->setQuery( $query, 0, 1 );
				$privacy->getDbo()->loadObject( $privacy );

				if ( ( ! $privacy->get( 'id' ) ) || ( $privacy->get( 'rule' ) != $value ) ) {
					if ( ! $privacy->get( 'id' ) ) {
						$privacy->set( 'user_id', (int) $user->get( 'id' ) );
						$privacy->set( 'type', 'profile' );
					}

					$privacy->set( 'rule', $value );

					$privacy->store();
				}
			}
		}
	}
}

class cbprivacyFieldDisable extends cbFieldHandler
{

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $formatting
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$return			=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( $output == 'htmledit' ) && ( $reason == 'edit' ) && $user->get( 'id' ) && ( ! cbprivacyClass::checkUserModerator( $user->get( 'id' ) ) ) ) {
			$field->set( 'registration', 0 );
			$field->set( 'profile', 0 );
			$field->set( 'required', 0 );
			$field->set( 'readonly', 0 );

			$return		=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}

		return $return;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null|string
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		global $_CB_framework;

		$return			=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( $output == 'htmledit' ) && ( $reason == 'edit' ) && $user->get( 'id' ) && ( ! cbprivacyClass::checkUserModerator( $user->get( 'id' ) ) ) ) {
			$url		=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'privacy', 'func' => 'disable' ) );

			$value		=	'<a href="javascript: void(0);" onclick="if ( confirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to disable your account?' ) ) . '\' ) ) { location.href = \'' . $url . '\'; }">'
						.		CBTxt::T( 'Disable account and user profile and related features.' )
						.	'</a>';

			$return		=	$this->formatFieldValueLayout( $value, $reason, $field, $user, false )
						.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, 'html', $value, null, null, true, 0 );
		}

		return $return;
	}
}

class cbprivacyFieldDelete extends cbFieldHandler
{

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $formatting
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$return			=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( $output == 'htmledit' ) && ( $reason == 'edit' ) && $user->get( 'id' ) && ( ! cbprivacyClass::checkUserModerator( $user->get( 'id' ) ) ) ) {
			$field->set( 'registration', 0 );
			$field->set( 'profile', 0 );
			$field->set( 'required', 0 );
			$field->set( 'readonly', 0 );

			$return		=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}

		return $return;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null|string
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		global $_CB_framework;

		$return			=	null;

		if ( ( ! Application::Cms()->getClientId() ) && ( $output == 'htmledit' ) && ( $reason == 'edit' ) && $user->get( 'id' ) && ( ! cbprivacyClass::checkUserModerator( $user->get( 'id' ) ) ) ) {
			$url		=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'privacy', 'func' => 'delete' ) );

			$value		=	'<a href="javascript: void(0);" onclick="if ( confirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete your account?' ) ) . '\' ) ) { location.href = \'' . $url . '\'; }">'
						.		CBTxt::T( 'Delete account and user profile.' )
						.	'</a>';

			$return		=	$this->formatFieldValueLayout( $value, $reason, $field, $user, false )
						.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, 'html', $value, null, null, true, 0 );
		}

		return $return;
	}
}