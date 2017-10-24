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
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerFunction( 'onBeforefieldClass', 'getAjaxResponse', 'CBfield_ajaxfields' );
$_PLUGINS->registerFunction( 'onBeforegetFieldRow', 'getAjaxDisplay', 'CBfield_ajaxfields' );

class CBfield_ajaxfields extends cbFieldHandler
{

	/**
	 * @param FieldTable  $field
	 * @param null|string $file
	 * @param bool|array  $headers
	 * @return null|string
	 */
	private function getTemplate( $field, $file = null, $headers = array( 'template', 'override' ) )
	{
		global $_CB_framework, $_PLUGINS;

		$plugin						=	$_PLUGINS->getLoadedPlugin( 'user', 'cbcorefieldsajax' );

		if ( ! $plugin ) {
			return null;
		}

		$template					=	$field->params->get( 'ajax_template', 'default' );
		$livePath					=	$_PLUGINS->getPluginLivePath( $plugin );
		$absPath					=	$_PLUGINS->getPluginPath( $plugin );

		$file						=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $file );
		$return						=	null;

		if ( $file ) {
			if ( $headers !== false ) {
				$headers[]			=	$file;
			}

			$php					=	$absPath . '/templates/' . $template . '/' . $file . '.php';

			if ( ! file_exists( $php ) ) {
				$php				=	$absPath . '/templates/default/' . $file . '.php';
			}

			if ( file_exists( $php ) ) {
				$return				=	$php;
			}
		}

		if ( $headers !== false ) {
			static $loaded			=	array();

			// Global CSS File:
			if ( in_array( 'template', $headers ) && ( ! in_array( 'template', $loaded ) ) ) {
				$global				=	'/templates/' . $template . '/template.css';

				if ( ! file_exists( $absPath . $global ) ) {
					$global			=	'/templates/default/template.css';
				}

				if ( file_exists( $absPath . $global ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $global );
				}

				$loaded[]			=	'template';
			}

			// File or Custom CSS/JS Headers:
			foreach ( $headers as $header ) {
				if ( in_array( $header, $loaded ) || in_array( $header, array( 'template', 'override' ) ) ) {
					continue;
				}

				$header				=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $header );

				if ( ! $header ) {
					continue;
				}

				$css				=	'/templates/' . $template . '/' . $header . '.css';
				$js					=	'/templates/' . $template . '/' . $header . '.js';

				if ( ! file_exists( $absPath . $css ) ) {
					$css			=	'/templates/default/' . $file . '.css';
				}

				if ( file_exists( $absPath . $css ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $css );
				}

				if ( ! file_exists( $absPath . $js ) ) {
					$js				=	'/templates/default/' . $file . '.js';
				}

				if ( file_exists( $absPath . $js ) ) {
					$_CB_framework->document->addHeadScriptUrl( $livePath . $js );
				}

				$loaded[]			=	$header;
			}

			// Override CSS File:
			if ( in_array( 'override', $headers ) && ( ! in_array( 'override', $loaded ) ) ) {
				$override			=	'/templates/' . $template . '/override.css';

				if ( file_exists( $absPath . $override ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $override );
				}

				$loaded[]			=	'override';
			}
		}

		return $return;
	}

	/**
	 * Reloads and outputs the JS headers for ajax output
	 *
	 * @return string
	 */
	private function reloadHeaders()
	{
		global $_CB_framework;

		$_CB_framework->getAllJsPageCodes();

		// Reset meta headers as they can't be used inline anyway:
		$_CB_framework->document->_head['metaTags']		=	array();

		// Remove all non-jQuery scripts as they'll likely just cause errors due to redeclaration:
		foreach( $_CB_framework->document->_head['scriptsUrl'] as $url => $script ) {
			if ( ( strpos( $url, 'jquery.' ) === false ) || ( strpos( $url, 'migrate' ) !== false ) ) {
				unset( $_CB_framework->document->_head['scriptsUrl'][$url] );
			}
		}

		$return											=	'<div class="cbAjaxHeaders" style="display: none;">'
														.		$_CB_framework->document->outputToHead()
														.	'</div>';

		return $return;
	}

	/**
	 * Checks if the user can ajax edit the supplied field
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $reason
	 * @param bool       $ignoreEmpty
	 * @return bool
	 */
	private function canAjax( &$field, &$user, $output, $reason, $ignoreEmpty = false )
	{
		$exclude				=	array( 'points', 'rating' );

		if ( ( ! Application::Cms()->getClientId() ) && ( $output == 'html' ) && ( in_array( $reason, array( 'profile', 'list' ) ) ) && ( $field instanceof FieldTable ) && ( $user instanceof UserTable ) && $field->getTableColumns() && ( ! in_array( $field->get( 'type' ), $exclude ) ) && ( ! $field->get( '_noAjax', false ) ) ) {
			if ( ! ( $field->params instanceof ParamsInterface ) ) {
				$params			=	new Registry( $field->params );
			} else {
				$params			=	$field->params;
			}

			$value				=	$user->get( $field->get( 'name' ) );
			$notEmpty			=	( ( ! ( ( $value === null ) || ( $value === '' ) ) ) || Application::Config()->get( 'showEmptyFields', 1 ) || cbReplaceVars( CBTxt::T( $field->params->get( 'ajax_placeholder' ) ), $user ) || ( $field->get( 'type' ) == 'image' ) );
			$readOnly			=	$field->get( 'readonly' );

			if ( ( $field->get( 'name' ) == 'username' ) && ( ! Application::Config()->get( 'usernameedit', 1 ) ) ) {
				$readOnly		=	true;
			}

			if ( ( ! $readOnly ) && ( $notEmpty || $ignoreEmpty ) && ( ! cbCheckIfUserCanPerformUserTask( $user->get( 'id' ), 'allowModeratorsUserEdit' ) ) ) {
				if ( ( $reason == 'profile' ) && $params->get( 'ajax_profile', 0 ) && Application::MyUser()->canViewAccessLevel( (int) $params->get( 'ajax_profile_access', 2 ) ) ) {
					return true;
				} elseif ( ( $reason == 'list' ) && $params->get( 'ajax_list', 0 ) && Application::MyUser()->canViewAccessLevel( (int) $params->get( 'ajax_list_access', 2 ) ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check well for the $reason ...
	 *
	 * @param  FieldTable $field
	 * @param  UserTable  $user
	 * @param  array      $postdata
	 * @param  string     $reason 'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @return string
	 */
	public function getAjaxResponse( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_database, $_PLUGINS;

		if ( ! $this->canAjax( $field, $user, 'html', $reason, true ) ) {
			return null;
		}

		switch ( $this->input( 'function', null, GetterInterface::STRING ) ) {
			case 'ajax_edit':
				$format									=	( $field->params->get( 'fieldVerifyInput', 0 ) ? 'div' : 'none' );

				if ( $format != 'none' ) {
					$formatted							=	$_PLUGINS->callField( $field->get( 'type' ), 'getFieldRow', array( &$field, &$user, 'htmledit', $format, 'edit', 0 ), $field );
				} else {
					$formatted							=	$_PLUGINS->callField( $field->get( 'type' ), 'getFieldRow', array( &$field, &$user, 'htmledit', 'none', 'edit', 0 ), $field );
				}

				if ( trim( $formatted ) == '' ) {
					return null;
				}

				$headers								=	$this->reloadHeaders();
				$template								=	$this->getTemplate( $field, 'edit' );

				if ( ! $template ) {
					return null;
				}

				return require $template;
				break;
			case 'ajax_save':
				$field->set( '_noAjax', true );

				if ( in_array( $field->get( 'name' ), array ( 'firstname', 'middlename', 'lastname' ) ) ) {
					if ( $field->get( 'name' ) != 'firstname' ) {
						$postdata['firstname']			=	$user->get( 'firstname' );
					}

					if ( $field->get( 'name' ) != 'middlename' ) {
						$postdata['middlename']			=	$user->get( 'middlename' );
					}

					if ( $field->get( 'name' ) != 'lastname' ) {
						$postdata['lastname']			=	$user->get( 'lastname' );
					}
				}

				$_PLUGINS->callField( $field->get( 'type' ), 'fieldClass', array( &$field, &$user, &$postdata, $reason ), $field );

				$oldUserComplete						=	new UserTable( $_CB_database );

				foreach ( array_keys( get_object_vars( $user ) ) as $k ) {
					if ( substr( $k, 0, 1 ) != '_' ) {
						$oldUserComplete->set( $k, $user->get( $k ) );
					}
				}

				$orgValue								=	$user->get( $field->get( 'name' ) );

				$_PLUGINS->callField( $field->get( 'type' ), 'prepareFieldDataSave', array( &$field, &$user, &$postdata, $reason ), $field );

				$store									=	false;

				if ( ! count( $_PLUGINS->getErrorMSG( false ) ) ) {
					$_PLUGINS->callField( $field->get( 'type' ), 'commitFieldDataSave', array( &$field, &$user, &$postdata, $reason ), $field );

					if ( ! count( $_PLUGINS->getErrorMSG( false ) ) ) {
						if ( Application::MyUser()->getUserId() == $user->get( 'id' ) ) {
							$user->set( 'lastupdatedate', Application::Database()->getUtcDateTime() );
						}

						$_PLUGINS->trigger( 'onBeforeUserUpdate', array( &$user, &$user, &$oldUserComplete, &$oldUserComplete ) );

						$clearTextPassword				=	null;

						if ( $field->get( 'name' ) == 'password' ) {
							$clearTextPassword			=	$user->get( 'password' );

							$user->set( 'password', $user->hashAndSaltPassword( $clearTextPassword ) );
						}

						$store							=	$user->store();

						if ( $clearTextPassword ) {
							$user->set( 'password', $clearTextPassword );
						}

						$_PLUGINS->trigger( 'onAfterUserUpdate', array( &$user, &$user, $oldUserComplete ) );
					} else {
						$_PLUGINS->callField( $field->get( 'type' ), 'rollbackFieldDataSave', array( &$field, &$user, &$postdata, $reason ), $field );
						$_PLUGINS->trigger( 'onSaveUserError', array( &$user, $user->getError(), $reason ) );
					}
				}

				if ( ! $store ) {
					if ( $orgValue != $user->get( $field->get( 'name' ) ) ) {
						$user->set( $field->get( 'name' ), $orgValue );
					}
				}

				$cbUser									=	CBuser::getInstance( (int) $user->get( 'id' ), false );
				$placeholder							=	$cbUser->replaceUserVars( CBTxt::T( $field->params->get( 'ajax_placeholder' ) ) );
				$emptyValue								=	$cbUser->replaceUserVars( Application::Config()->get( 'emptyFieldsText', '-' ) );
				$return									=	$_PLUGINS->callField( $field->get( 'type' ), 'getFieldRow', array( &$field, &$user, 'html', 'none', $reason, 0 ), $field );

				if ( ( ( trim( $return ) == '' ) || ( $return == $emptyValue ) ) && $placeholder ) {
					$return								=	$placeholder;
				} elseif ( ( trim( $return ) == '' ) && ( ! Application::Config()->get( 'showEmptyFields', 1 ) ) ) {
					$return								=	$emptyValue;
				}

				$error									=	$this->getFieldAjaxError( $field, $user, $reason );
				$return									=	( $error ? '<div class="alert alert-danger">' . $error . '</div>' : null )
														.	$return
														.	$this->reloadHeaders();

				$field->set( '_noAjax', false );

				return $return;
				break;
		}

		return null;
	}

	/**
	 * Formatter:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $formatting           'tr', 'td', 'div', 'span', 'none',   'table'??
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getAjaxDisplay( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		global $_PLUGINS, $ueConfig;

		if ( ! $this->canAjax( $field, $user, $output, $reason ) ) {
			return null;
		}

		$field->set( '_noAjax', true );

		$hasEdit			=	$_PLUGINS->callField( $field->get( 'type' ), 'getFieldRow', array( &$field, &$user, 'htmledit', 'none', 'edit', $list_compare_types ), $field );

		if ( trim( $hasEdit ) == '' ) {
			$field->set( '_noAjax', false );

			return null;
		}

		$placeholder		=	cbReplaceVars( CBTxt::T( $field->params->get( 'ajax_placeholder' ) ), $user );
		$formatted			=	$_PLUGINS->callField( $field->get( 'type' ), 'getFieldRow', array( &$field, &$user, $output, 'none', $reason, $list_compare_types ), $field );

		if ( ( ( trim( $formatted ) == '' ) || ( $formatted == $ueConfig['emptyFieldsText'] ) ) && $placeholder ) {
			$formatted		=	$placeholder;
		}

		if ( trim( $formatted ) == '' ) {
			$field->set( '_noAjax', false );

			return null;
		}

		$template			=	$this->getTemplate( $field, 'display' );

		if ( ! $template ) {
			return null;
		}

		$return				=	$this->renderFieldHtml( $field, $user, require $template, $output, $formatting, $reason, array() );

		$field->set( '_noAjax', false );

		return $return;
	}

	/**
	 * Parse field validation errors into user readable
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $reason
	 * @return mixed|null
	 */
	private function getFieldAjaxError( &$field, &$user, $reason )
	{
		global $_PLUGINS;

		$errors	=	$_PLUGINS->getErrorMSG( false );
		$title	=	cbFieldHandler::getFieldTitle( $field, $user, 'text', $reason );

		if ( $errors ) foreach ( $errors as $error ) {
			if ( stristr( $error, $title ) ) {
				return str_replace( $title . ' : ', '', $error );
			}
		}

		return null;
	}
}