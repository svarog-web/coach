<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Input\Get;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerFunction( 'onBeforefieldClass', 'getAjaxResponse', 'CBfield_codeselect' );
$_PLUGINS->registerFunction( 'onBeforefieldClass', 'getValidationResponse', 'CBfield_codevalidate' );
$_PLUGINS->registerFunction( 'onBeforegetFieldRow', 'getValidationDisplay', 'CBfield_codevalidate' );
$_PLUGINS->registerFunction( 'onBeforeprepareFieldDataSave', 'checkValidation', 'CBfield_codevalidate' );
$_PLUGINS->registerUserFieldTypes( array(	'code'					=>	'CBfield_code',
											'codemulticheckbox'		=>	'CBfield_codeselect',
											'codemultiselect'		=>	'CBfield_codeselect',
											'codeselect'			=>	'CBfield_codeselect',
											'coderadio'				=>	'CBfield_codeselect'
										));

class CBfield_codevalidate extends cbFieldHandler
{

	/**
	 * Checks if the field can be query validated
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $reason
	 * @param bool       $checkAjax
	 * @return bool
	 */
	private function canValidate( &$field, &$user, $reason, $checkAjax = true )
	{
		global $ueConfig;

		if ( ! ( $user instanceof UserTable ) ) {
			$user				=	new UserTable();
		}

		if ( ( $field instanceof FieldTable ) && ( ( ! Application::Cms()->getClientId() ) || ( Application::Cms()->getClientId() && ( $ueConfig['adminrequiredfields'] == 1 ) ) ) && in_array( $reason, array( 'edit', 'register' ) ) ) {
			if ( ! ( $field->params instanceof ParamsInterface ) ) {
				$field->params	=	new Registry( $field->params );
			}

			$readOnly			=	$field->get( 'readonly' );

			if ( $field->get( 'name' ) == 'username' ) {
				if ( ! $ueConfig['usernameedit'] ) {
					$readOnly	=	true;
				}
			}

			if ( ( ( ! $readOnly ) || ( $reason == 'register' ) || Application::Cms()->getClientId() ) && $field->params->get( 'code_validate', 0 ) && ( ( $checkAjax && $field->params->get( 'code_validate_ajax', 0 ) ) || ( ! $checkAjax ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Sends the field value through the code and tests for validity
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param mixed      $value
	 * @return bool
	 */
	private function codeValidate( $field, $user, $value )
	{
		$code			=	CBuser::getInstance( (int) $user->get( 'id' ), false )->replaceUserVars( $field->params->get( 'code_validate_code', '', GetterInterface::RAW ), false, false, array( 'value' => $value ), false );

		if ( ! $code ) {
			return false;
		}

		ob_start();
		$function		=	create_function( '$field,$user,$value', $code );
		$return			=	$function( $field, $user, $value );
		ob_end_clean();

		return $return;
	}

	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check well for the $reason ...
	 *
	 * @param FieldTable     $field
	 * @param null|UserTable $user
	 * @param array          $postdata
	 * @param string         $reason 'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @return string
	 */
	public function getValidationResponse( &$field, &$user, &$postdata, $reason )
	{
		if ( ( cbGetParam( $_GET, 'function', null ) == 'codevalidate' ) && $this->canValidate( $field, $user, $reason ) ) {
			$previousValue		=	$user->get( $field->get( 'name' ) );
			$value				=	cbGetParam( $postdata, 'value' );

			if ( is_array( $value ) ) {
				$value			=	$this->_implodeCBvalues( $value );
			}

			$value				=	stripslashes( $value );

			if ( $value && ( $value != $previousValue ) ) {
				$valid			=	$this->codeValidate( $field, $user, $value );
			} else {
				$valid			=	true;
			}

			if ( $valid ) {
				$message		=	CBTxt::T( 'CODE_VALIDATION_SUCCESS', $field->params->get( 'code_validate_success', '' ), array( '[title]' => $field->get( 'title' ), '[value]' => $value ) );
			} else {
				$message		=	CBTxt::T( 'CODE_VALIDATION_ERROR', $field->params->get( 'code_validate_error', 'Not a valid input.' ), array( '[title]' => $field->get( 'title' ), '[value]' => $value ) );
			}

			return json_encode( array( 'valid' => $valid, 'message' => $message ) );
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
	public function getValidationDisplay( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		global $_CB_framework;

		if ( $this->canValidate( $field, $user, $reason ) ) {
			static $JS_LOADED	=	0;

			if ( ! $JS_LOADED++ ) {
				$js				=	"params.method = 'cbcodevalidate';"
								.	"return $.validator.methods.cbfield.call( this, value, element, params );";

				cbValidator::addRule( 'cbcodevalidate', $js );
			}

			$js					=	"$( '#" . addslashes( $field->get( 'name' ) ) . "' ).attr({"
								.		"'data-rule-cbcodevalidate': '" . json_encode( array( 'user' => (int) $user->get( 'id' ), 'field' => htmlspecialchars( $field->get( 'name' ) ), 'reason' => htmlspecialchars( $reason ), 'function' => 'codevalidate' ) ) . "'"
								.	"});";

			$_CB_framework->outputCbJQuery( $js );
		}
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 * @return bool
	 */
	public function checkValidation( &$field, &$user, &$postdata, $reason )
	{
		if ( $this->canValidate( $field, $user, $reason, false ) ) {
			$fieldName			=	$field->get( 'name' );
			$previousValue		=	$user->get( $fieldName );
			$value				=	cbGetParam( $postdata, $fieldName );

			if ( is_array( $value ) ) {
				$value			=	$this->_implodeCBvalues( $value );
			}

			$value				=	stripslashes( $value );

			if ( $value && ( $value != $previousValue ) ) {
				if ( ! $this->codeValidate( $field, $user, $value ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'CODE_VALIDATION_ERROR', $field->params->get( 'code_validate_error', 'Not a valid input.' ), array( '[fieldname]' => $field->get( 'title' ), '[value]' => $value ) ) );
				}
			}
		}
	}
}

class CBfield_code extends cbFieldHandler
{

	/**
	 * @param FieldTable $field
	 * @param null|array $files
	 * @param bool       $loadGlobal
	 * @param bool       $loadHeader
	 */
	private function getTemplate( $field, $files = null, $loadGlobal = true, $loadHeader = true )
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
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbcodefield' );

			if ( ! $plugin ) {
				return;
			}

			$livePath							=	$_PLUGINS->getPluginLivePath( $plugin );
			$absPath							=	$_PLUGINS->getPluginPath( $plugin );

			$template							=	$field->params->get( 'code_template', 'default' );
			$globalCss							=	'/templates/' . $template . '/template.css';
			$overrideCss						=	'/templates/' . $template . '/override.css';

			if ( $loadGlobal && $loadHeader ) {
				if ( ! file_exists( $absPath . $globalCss ) ) {
					$globalCss					=	'/templates/default/template.css';
				}

				if ( file_exists( $absPath . $globalCss ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $globalCss );

					$paths['global_css']		=	$livePath . $globalCss;
				}
			}

			$paths								=	array( 'global_css' => null, 'php' => null, 'css' => null, 'js' => null, 'override_css' => null );

			foreach ( $files as $file ) {
				$file							=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $file );

				if ( $file ) {
					$php						=	$absPath . '/templates/' . $template . '/' . $file . '.php';
					$css						=	'/templates/' . $template . '/' . $file . '.css';
					$js							=	'/templates/' . $template . '/' . $file . '.js';
				} else {
					$php						=	null;
					$css						=	null;
					$js							=	null;
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
			}

			if ( $loadGlobal && $loadHeader ) {
				if ( file_exists( $absPath . $overrideCss ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $overrideCss );

					$paths['override_css']		=	$livePath . $overrideCss;
				}
			}

			$tmpl[$id]							=	$paths;
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
		$this->getTemplate( $field, 'display' );

		$code			=	CBuser::getInstance( (int) $user->get( 'id' ), false )->replaceUserVars( $field->params->get( 'code', '', GetterInterface::RAW ), false, false, null, false );
		$return			=	HTML_codefieldDisplay::showField( $code, $field, $user, $output, $reason );

		switch ( $output ) {
			case 'html':
			case 'rss':
			case 'htmledit':
				if ( $reason == 'search' ) {
					return	null;
				} else {
					return $this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name' ), $return, $output, false ), $reason, $field, $user );
				}
				break;
			default:
				return $this->_formatFieldOutput( $field->get( 'name' ), $return, $output, false );
				break;
		}
	}
}

class CBfield_codeselect extends cbFieldHandler
{

	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check well for the $reason ...
	 *
	 * @param  FieldTable     $field
	 * @param  null|UserTable $user
	 * @param  array          $postdata
	 * @param  string         $reason 'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @return string
	 */
	public function getAjaxResponse( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_database;

		if ( ( $this->input( 'function', null, GetterInterface::STRING ) != 'code_options' ) || ( ! in_array( $field->get( 'type' ), array( 'codeselect', 'codemultiselect' ) ) ) ) {
			return null;
		}

		$updateFields				=	$field->params->get( 'code_update' );

		if ( ! $updateFields ) {
			return null;
		}

		if ( ! ( $user instanceof UserTable ) ) {
			$user					=	new UserTable();
		}

		static $cache				=	array();

		if ( ! isset( $cache[$updateFields] ) ) {
			$query					=	"SELECT " . $_CB_database->NameQuote( 'name' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'fieldid' ) . " IN " . $_CB_database->safeArrayOfIntegers( explode( '|*|', $updateFields ) );
			$_CB_database->setQuery( $query );
			$cache[$updateFields]	=	$_CB_database->loadResultArray();
		}

		$allowedFields				=	$cache[$updateFields];

		if ( ! in_array( $field->get( 'name' ), $allowedFields ) ) {
			$allowedFields[]		=	$field->get( 'name' );
		}

		$valueCache					=	array();

		foreach ( $postdata as $k => $v ) {
			if ( ( ! $k ) || ( ! in_array( $k, $allowedFields ) ) ) {
				continue;
			}

			if ( is_array( $v ) ) {
				$v					=	$this->_implodeCBvalues( $v );
			}

			$valueCache[$k]			=	$user->get( $k );

			$user->set( $k, Get::clean( $v, GetterInterface::STRING ) );
		}

		$value						=	$user->get( $field->get( 'name' ) );
		$return						=	$this->getEdit( $value, $field, $user, $reason, 0 );

		foreach ( $valueCache as $k => $v ) {
			$user->set( $k, $v );
		}

		return trim( $return );
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
		global $_CB_framework;

		$value									=	$user->get( $field->get( 'name' ) );

		switch ( $output ) {
			case 'html':
			case 'rss':
				$values							=	$this->_explodeCBvalues( $value );
				$options						=	$this->getOptions( $field, $user, true );
				$labels							=	array();

				if ( $options ) foreach ( $options as $option ) {
					if ( in_array( $option['value'], $values ) && ( ! in_array( $option['text'], $labels ) ) ) {
						$labels[]				=	$option['text'];
					}
				}

				$displayStyle					=	$field->params->get( 'field_display_style' );

				return $this->formatFieldValueLayout( $this->_arrayToFormat( $field, $labels, $output, ( $displayStyle == 1 ? 'ul' : ( $displayStyle == 2 ? 'ol' : ', ' ) ), trim( $field->params->get( 'field_display_class' ) ) ), $reason, $field, $user );
				break;
			case 'htmledit':
				$updateOn						=	$field->params->get( 'code_update' );

				if ( $updateOn && in_array( $field->get( 'type' ), array( 'codeselect', 'codemultiselect' ) ) ) {
					$updateOn					=	cbToArrayOfInt( explode( '|*|', $updateOn ) );
					$selectors					=	array();

					foreach ( $updateOn as $updateField ) {
						if ( ! $updateField ) {
							continue;
						}

						$selectors[]			=	'#cbfr_' . (int) $updateField . ',#cbfrd_' . (int) $updateField;
					}

					if ( $selectors ) {
						$_CB_framework->addJQueryPlugin( 'cbcodefield', '/components/com_comprofiler/plugin/user/plug_cbcodefield/js/cbcodefield.js' );

						if ( Application::Cms()->getClientId() ) {
							$updateUrl			=	$_CB_framework->backendViewUrl( 'fieldclass', false, array( 'field' => $field->get( 'name' ), 'function' => 'code_options', 'user' => (int) $user->get( 'id' ), 'reason' => $reason ), 'raw' );
						} else {
							$updateUrl			=	$_CB_framework->viewUrl( 'fieldclass', false, array( 'field' => $field->get( 'name' ), 'function' => 'code_options', 'user' => (int) $user->get( 'id' ), 'reason' => $reason ), 'raw' );
						}

						$js						=	"$( '#cbfr_" . (int) $field->get( 'fieldid' ) . ",#cbfrd_" . (int) $field->get( 'fieldid' ) . "' ).cbcodefield({"
												.		"selectors: '" . addslashes( implode( ',', $selectors ) ) . "',"
												.		"url: '" . addslashes( $updateUrl ) . "'"
												.	"});";

						$_CB_framework->outputCbJQuery( $js, 'cbcodefield' );
					}
				}

				return $this->getEdit( $value, $field, $user, $reason, $list_compare_types );
				break;
			case 'xml':
			case 'json':
			case 'php':
			case 'csv':
				return $this->_arrayToFormat( $field, $this->_explodeCBvalues( $value ), $output );
				break;
			case 'csvheader':
			case 'fieldslist':
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value							=	cbGetParam( $postdata, $col, null, _CB_ALLOWRAW );
			$options						=	$this->getOptions( $field, $user, 'value' );

			if ( is_array( $value ) ) {
				if ( count( $value ) > 0 ) {
					$okVals					=	array();

					foreach ( $value as $k => $v ) {
						$v					=	stripslashes( $v );

						if ( in_array( $v, $options, true ) && ( ! in_array( $v, $okVals, true ) ) ) {
							$okVals[$k]		=	$v;
						}
					}

					$value					=	$this->_implodeCBvalues( $okVals );
				} else {
					$value					=	'';
				}
			} elseif ( ( $value === null ) || ( $value === '' ) ) {
				$value						=	'';
			} else {
				$value						=	stripslashes( $value );

				if ( ! in_array( $value, $options, true ) ) {
					$value					=	null;
				}
			}

			if ( $this->validate( $field, $user, $col, $value, $postdata, $reason ) ) {
				if ( isset( $user->$col ) && ( (string) $user->$col ) !== (string) $value ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
			}

			$user->$col						=	$value;
		}
	}

	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $searchVals          RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata            Typically $_POST (but not necessarily), filtering required.
	 * @param  int         $list_compare_types  IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string      $reason              'edit' for save user edit, 'register' for save registration
	 * @return cbSqlQueryPart[]
	 */
	public function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason )
	{
		switch ( $field->get( 'type' ) ) {
			case 'codemulticheckbox':
				$fieldType				=	'multicheckbox';
				break;
			case 'coderadio':
				$fieldType				=	'radio';
				break;
			case 'codemultiselect':
				$fieldType				=	'multiselect';
				break;
			case 'codeselect':
			default:
				$fieldType				=	'select';
				break;
		}

		if ( ( $fieldType == 'radio' ) && in_array( $list_compare_types, array( 0, 2 ) ) ) {
			$fieldType					=	'multicheckbox';
		}

		$query							=	array();
		$searchMode						=	$this->_bindSearchMode( $field, $searchVals, $postdata, ( strpos( $fieldType, 'multi' ) === 0 ? 'multiplechoice' : 'singlechoice' ), $list_compare_types );

		if ( $searchMode ) foreach ( $field->getTableColumns() as $col ) {
			$value						=	cbGetParam( $postdata, $col );

			if ( is_array( $value ) ) {
				if ( count( $value ) <= 0 ) {
					$value				=	null;
				}

				if ( ( $value !== null ) && ( $value !== '' ) && in_array( $searchMode, array( 'is', 'isnot' ) ) ) {
					$value				=	stripslashes( $this->_implodeCBvalues( $value ) );
				}
			} else {
				if ( ( $value === null ) || ( $value === '' ) ) {
					if ( ( $list_compare_types == 1 ) && in_array( $searchMode, array( 'is', 'isnot' ) ) ) {
						$value			=	'';
					} else {
						$value			=	null;
					}
				}
			}

			if ( $value !== null ) {
				$searchVals->$col		=	$value;

				$sql					=	new cbSqlQueryPart();
				$sql->tag				=	'column';
				$sql->name				=	$col;
				$sql->table				=	$field->get( 'table' );
				$sql->type				=	'sql:field';
				$sql->operator			=	'=';
				$sql->value				=	$value;
				$sql->valuetype			=	'const:string';
				$sql->searchmode		=	$searchMode;

				$query[]				=	$sql;
			}
		}

		return $query;
	}

	/**
	 * @param mixed      $value
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $reason             'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param int        $list_compare_types IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	private function getEdit( $value, $field, $user, $reason, $list_compare_types )
	{
		$options						=	$this->getOptions( $field, $user );

		switch ( $field->get( 'type' ) ) {
			case 'codemulticheckbox':
				$fieldType				=	'multicheckbox';
				break;
			case 'coderadio':
				$fieldType				=	'radio';
				break;
			case 'codemultiselect':
				$fieldType				=	'multiselect';
				break;
			case 'codeselect':
			default:
				$fieldType				=	'select';
				break;
		}

		if ( $reason == 'search' ) {
			switch ( $fieldType ) {
				case 'radio':
					if ( in_array( $list_compare_types, array( 0, 2 ) ) || ( is_array( $value ) && ( count( $value ) > 1 ) ) ) {
						$fieldType		=	'multicheckbox';
					}

					$class				=	'cb__js_radio';
					break;
				case 'select':
					$class				=	'cb__js_select';
					break;
				default:
					$class				=	null;
					break;
			}

			if ( in_array( $list_compare_types, array( 0, 2 ) ) && ( $fieldType != 'multicheckbox' ) ) {
				array_unshift( $options, moscomprofilerHTML::makeOption( '', CBTxt::T( 'UE_NO_PREFERENCE', 'No preference' ) ) );
			}

			$html						=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', $fieldType, $value, null, $options );
			$return						=	$this->_fieldSearchModeHtml( $field, $user, $html, ( strpos( $fieldType, 'multi' ) === 0 ? 'multiplechoice' : 'singlechoice' ), $list_compare_types, $class );
		} else {
			$return						=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', $fieldType, $value, null, $options );
		}

		return $return;
	}

	/**
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  bool        $raw
	 * @return array
	 */
	private function getOptions( $field, $user, $raw = false )
	{
		global $_CB_database;

		static $cache							=	array();

		$options								=	array();
		$cacheId								=	(int) $field->get( 'fieldid' );

		if ( ! isset( $cache[$cacheId] ) ) {
			$query								=	"SELECT " . $_CB_database->NameQuote( 'fieldtitle' ) . " AS " . $_CB_database->NameQuote( 'value' )
												.	", if ( " . $_CB_database->NameQuote( 'fieldlabel' ) . " != '', " . $_CB_database->NameQuote( 'fieldlabel' ) . ", " . $_CB_database->NameQuote( 'fieldtitle' ) . " ) AS " . $_CB_database->NameQuote( 'text' )
												.	", CONCAT( " . $_CB_database->Quote( 'cbf' ) . ", " . $_CB_database->NameQuote( 'fieldvalueid' ) . " ) AS " . $_CB_database->NameQuote( 'id' )
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_field_values' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'fieldid' ) . " = " . (int) $cacheId
												.	"\n ORDER BY" . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$cache[$cacheId]					=	$_CB_database->loadObjectList();
		}

		$rows									=	$cache[$cacheId];

		if ( $rows ) {
			foreach ( $rows as $row ) {
				if ( $raw ) {
					if ( $raw === 'value' ) {
						$options[]				=	$row->value;
					} elseif ( $raw === 'text' ) {
						$options[]				=	CBTxt::T( $row->text );
					} else {
						$options[]				=	array( 'value' => $row->value, 'text' => CBTxt::T( $row->text ), 'id' => $row->id );
					}
				} else {
					$options[]					=	moscomprofilerHTML::makeOption( $row->value, CBTxt::T( $row->text ), 'value', 'text', $row->id );
				}
			}
		}

		// Force the user object to CBuser instance to allow substitutions for non-existant users to work:
		$cbUser									=	new CBuser();
		$cbUser->_cbuser						=	$user;

		$code									=	$cbUser->replaceUserVars( $field->params->get( 'code', '', GetterInterface::RAW ), false, false, null, false );

		if ( ! $code ) {
			return $options;
		}

		ob_start();
		$function								=	create_function( '$field,$user', $code );
		$rows									=	$function( $field, $user );
		ob_end_clean();

		if ( $rows ) {
			foreach ( $rows as $k => $v ) {
				if ( $raw ) {
					if ( is_array( $v ) ) {
						foreach ( $v as $value => $label ) {
							if ( $raw === 'value' ) {
								$options[]		=	$value;
							} elseif ( $raw === 'text' ) {
								$options[]		=	CBTxt::T( $label );
							} else {
								$options[]		=	array( 'value' => $value, 'text' => CBTxt::T( $label ) );
							}
						}
					} else {
						if ( $raw === 'value' ) {
							$options[]			=	$k;
						} elseif ( $raw === 'text' ) {
							$options[]			=	CBTxt::T( $v );
						} else {
							$options[]			=	array( 'value' => $k, 'text' => CBTxt::T( $v ) );
						}
					}
				} else {
					if ( is_array( $v ) ) {
						if ( in_array( $field->get( 'type' ), array( 'codeselect', 'codemultiselect' ) ) ) {
							$options[]			=	moscomprofilerHTML::makeOptGroup( CBTxt::T( $k ) );
						}

						foreach ( $v as $value => $label ) {
							$options[]			=	moscomprofilerHTML::makeOption( $value, CBTxt::T( $label ) );
						}

						if ( in_array( $field->get( 'type' ), array( 'codeselect', 'codemultiselect' ) ) ) {
							$options[]			=	moscomprofilerHTML::makeOptGroup( null );
						}
					} else {
						$options[]				=	moscomprofilerHTML::makeOption( $k, CBTxt::T( $v ) );
					}
				}
			}
		}

		return $options;
	}
}