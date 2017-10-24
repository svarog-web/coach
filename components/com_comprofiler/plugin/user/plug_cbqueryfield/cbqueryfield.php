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
use CBLib\Database\Driver\CmsDatabaseDriver;
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Database\DatabaseDriverInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Input\Get;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerFunction( 'onBeforefieldClass', 'getAjaxResponse', 'CBfield_queryselect' );
$_PLUGINS->registerFunction( 'onBeforefieldClass', 'getValidationResponse', 'CBfield_queryvalidate' );
$_PLUGINS->registerFunction( 'onBeforegetFieldRow', 'getValidationDisplay', 'CBfield_queryvalidate' );
$_PLUGINS->registerFunction( 'onBeforeprepareFieldDataSave', 'checkValidation', 'CBfield_queryvalidate' );
$_PLUGINS->registerUserFieldTypes( array(	'query'					=>	'CBfield_query',
											'querymulticheckbox'	=>	'CBfield_queryselect',
											'querymultiselect'		=>	'CBfield_queryselect',
											'queryselect'			=>	'CBfield_queryselect',
											'queryradio'			=>	'CBfield_queryselect'
										));

class cbQueryField extends cbFieldHandler
{

	/**
	 * Gets the internal or external database
	 *
	 * @param FieldTable $field
	 * @param string     $paramPrefix
	 * @return cbDatabase|DatabaseDriverInterface
	 */
	public function getDatabase( $field, $paramPrefix = 'qry' )
	{
		global $_CB_framework, $_CB_database;

		if ( $field->params->get( $paramPrefix . '_mode', 0 ) ) {
			$driver							=	$_CB_framework->getCfg( 'dbtype' );
			$host							=	$field->params->get( $paramPrefix . '_host', null );
			$username						=	$field->params->get( $paramPrefix . '_username', null );
			$password						=	$field->params->get( $paramPrefix . '_password', null );
			$database						=	$field->params->get( $paramPrefix . '_database', null );
			$charset						=	$field->params->get( $paramPrefix . '_charset', null );
			$prefix							=	$field->params->get( $paramPrefix . '_prefix', null );

			$options						=	array ( 'driver' => $driver, 'host' => $host, 'user' => $username, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

			if ( checkJversion( '3.0+' ) ) {
				try {
					$_J_database			=	JDatabaseDriver::getInstance( $options );
				} catch ( RuntimeException $e ) {
					return null;
				}
			} else {
				$_J_database				=	JDatabase::getInstance( $options );

				if ( JError::isError( $_J_database ) ) {
					return null;
				}
			}

			$_SQL_database					=	new CmsDatabaseDriver( $_J_database, $prefix, checkJversion( 'release' ) );

			if ( $charset ) {
				$_SQL_database->setQuery( 'SET NAMES ' . $_SQL_database->Quote( $charset ) );
				$_SQL_database->query();
			}
		} else {
			$_SQL_database					=	$_CB_database;
		}

		return $_SQL_database;
	}

	public function escapeSQL( $str ) {
		global $_CB_database;

		return $_CB_database->getEscaped( $str );
	}
}

class CBfield_queryvalidate extends cbQueryField
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

			if ( ( ( ! $readOnly ) || ( $reason == 'register' ) || Application::Cms()->getClientId() ) && $field->params->get( 'qry_validate', 0 ) && ( ( $checkAjax && $field->params->get( 'qry_validate_ajax', 0 ) ) || ( ! $checkAjax ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Sends the field value through the query and tests for validity
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param mixed      $value
	 * @return bool
	 */
	private function queryValidate( $field, $user, $value )
	{
		$query					=	CBuser::getInstance( (int) $user->id, false )->replaceUserVars( $field->params->get( 'qry_validate_query', null ), array( $this, 'escapeSQL' ), false, array( 'value' => $value ), false );

		if ( $query ) {
			$_SQL_database		=	$this->getDatabase( $field, 'qry_validate' );

			$_SQL_database->setQuery( $query );

			$results			=	$_SQL_database->loadResultArray();

			if ( count( $results ) > 0 ) {
				if ( count( $results ) == 1 ) {
					$results	=	array_shift( $results );
				} else {
					$results	=	true;
				}
			} else {
				$results		=	false;
			}

			$validateOn			=	(int) $field->params->get( 'qry_validate_on', 0 );

			if ( $results ) {
				if ( $validateOn ) {
					return true;
				} else {
					return false;
				}
			} else {
				if ( $validateOn ) {
					return false;
				} else {
					return true;
				}
			}
		} else {
			return true;
		}
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
	public function getValidationResponse( &$field, &$user, &$postdata, $reason )
	{
		if ( ( cbGetParam( $_GET, 'function', null ) == 'queryvalidate' ) && $this->canValidate( $field, $user, $reason ) ) {
			$previousValue		=	$user->get( $field->get( 'name' ) );
			$value				=	cbGetParam( $postdata, 'value' );

			if ( is_array( $value ) ) {
				$value			=	$this->_implodeCBvalues( $value );
			}

			$value				=	stripslashes( $value );

			if ( $value && ( $value != $previousValue ) ) {
				$valid			=	$this->queryValidate( $field, $user, $value );
			} else {
				$valid			=	true;
			}

			if ( $valid ) {
				$message		=	CBTxt::T( 'QUERY_VALIDATION_SUCCESS', $field->params->get( 'qry_validate_success', '' ), array( '[title]' => $field->get( 'title' ), '[value]' => $value ) );
			} else {
				$message		=	CBTxt::T( 'QUERY_VALIDATION_ERROR', $field->params->get( 'qry_validate_error', 'Not a valid input.' ), array( '[title]' => $field->get( 'title' ), '[value]' => $value ) );
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
				$js				=	"params.method = 'cbqueryvalidate';"
								.	"return $.validator.methods.cbfield.call( this, value, element, params );";

				cbValidator::addRule( 'cbqueryvalidate', $js );
			}

			$js					=	"$( '#" . addslashes( $field->get( 'name' ) ) . "' ).attr({"
								.		"'data-rule-cbqueryvalidate': '" . json_encode( array( 'user' => (int) $user->get( 'id' ), 'field' => htmlspecialchars( $field->get( 'name' ) ), 'reason' => htmlspecialchars( $reason ), 'function' => 'queryvalidate' ) ) . "'"
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
				if ( ! $this->queryValidate( $field, $user, $value ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'QUERY_VALIDATION_ERROR', $field->params->get( 'qry_validate_error', 'Not a valid input.' ), array( '[fieldname]' => $field->get( 'title' ), '[value]' => $value ) ) );
				}
			}
		}
	}
}

class CBfield_query extends cbQueryField
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
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbqueryfield' );

			if ( ! $plugin ) {
				return;
			}

			$livePath							=	$_PLUGINS->getPluginLivePath( $plugin );
			$absPath							=	$_PLUGINS->getPluginPath( $plugin );

			$template							=	$field->params->get( 'qry_template', 'default' );
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
		$cbUser											=	CBuser::getInstance( (int) $user->id, false );
		$query											=	$cbUser->replaceUserVars( $field->params->get( 'qry_query', null ), array( $this, 'escapeSQL' ), false, null, false );
		$return											=	null;

		if ( $query ) {
			$_SQL_database								=	$this->getDatabase( $field );

			$_SQL_database->setQuery( $query );

			switch ( (int) $field->params->get( 'qry_output', 0 ) ) {
				case 2:
					$this->getTemplate( $field, 'display' );

					$return								=	HTML_queryfieldDisplay::showField( $_SQL_database, $field, $user, $output, $reason );
					break;
				case 1:
					$rows								=	$_SQL_database->loadAssocList();

					if ( $rows ) {
						$return							=	$cbUser->replaceUserVars( CBTxt::T( $field->params->get( 'qry_header', null ) ), false, false, null, false );

						foreach ( $rows as $row ) {
							$extra						=	array();

							if ( $row ) foreach ( $row as $k => $v ) {
								$extra["column_$k"]		=	$v;
							}

							$return						.=	$cbUser->replaceUserVars( CBTxt::T( $field->params->get( 'qry_row', null ) ), false, false, $extra, false );
						}

						$return							.=	$cbUser->replaceUserVars( CBTxt::T( $field->params->get( 'qry_footer', null ) ), false, false, null, false );
					}
					break;
				case 0:
				default:
					if ( $field->params->get( 'qry_columns', 0 ) ) {
						$row							=	$_SQL_database->loadAssoc();

						if ( $row ) {
							if ( $field->params->get( 'qry_display', 0 ) ) {
								$extra					=	array();

								foreach ( $row as $k => $v ) {
									$extra["column_$k"]	=	$v;
								}

								$return					=	$cbUser->replaceUserVars( CBTxt::T( $field->params->get( 'qry_custom', null ) ), false, false, $extra, false );
							} else {
								$start					=	null;
								$end					=	null;

								switch( $field->params->get( 'qry_delimiter', 0 ) ) {
									case 8:
										$start			=	'<p>';
										$delimiter		=	'</p><p>';
										$end			=	'</p>';
										break;
									case 7:
										$start			=	'<span>';
										$delimiter		=	'</span><span>';
										$end			=	'</span>';
										break;
									case 6:
										$start			=	'<div>';
										$delimiter		=	'</div><div>';
										$end			=	'</div>';
										break;
									case 5:
										$start			=	'<ol><li>';
										$delimiter		=	'</li><li>';
										$end			=	'</li></ol>';
										break;
									case 4:
										$start			=	'<ul><li>';
										$delimiter		=	'</li><li>';
										$end			=	'</li></ul>';
										break;
									case 3:
										$delimiter		=	'<br />';
										break;
									case 2:
										$delimiter		=	' ';
										break;
									case 1:
										$delimiter		=	' - ';
										break;
									case 0:
									default:
										$delimiter		=	', ';
										break;
								}

								$return					=	$start . implode( $delimiter, $row ) . $end;
							}
						}
					} else {
						$return							=	$_SQL_database->loadResult();

						if ( is_string( $return ) ) {
							$return						=	$cbUser->replaceUserVars( CBTxt::T( $return ), false, false, null, false );
						}
					}
					break;
			}
		}

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

class CBfield_queryselect extends cbQueryField
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

		if ( ( $this->input( 'function', null, GetterInterface::STRING ) != 'query_options' ) || ( ! in_array( $field->get( 'type' ), array( 'queryselect', 'querymultiselect' ) ) ) ) {
			return null;
		}

		$updateFields				=	$field->params->get( 'qry_update' );

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
				$updateOn						=	$field->params->get( 'qry_update' );

				if ( $updateOn && in_array( $field->get( 'type' ), array( 'queryselect', 'querymultiselect' ) ) ) {
					$updateOn					=	cbToArrayOfInt( explode( '|*|', $updateOn ) );
					$selectors					=	array();

					foreach ( $updateOn as $updateField ) {
						if ( ! $updateField ) {
							continue;
						}

						$selectors[]			=	'#cbfr_' . (int) $updateField . ',#cbfrd_' . (int) $updateField;
					}

					if ( $selectors ) {
						$_CB_framework->addJQueryPlugin( 'cbqueryfield', '/components/com_comprofiler/plugin/user/plug_cbqueryfield/js/cbqueryfield.js' );

						if ( Application::Cms()->getClientId() ) {
							$updateUrl			=	$_CB_framework->backendViewUrl( 'fieldclass', false, array( 'field' => $field->get( 'name' ), 'function' => 'query_options', 'user' => (int) $user->get( 'id' ), 'reason' => $reason ), 'raw' );
						} else {
							$updateUrl			=	$_CB_framework->viewUrl( 'fieldclass', false, array( 'field' => $field->get( 'name' ), 'function' => 'query_options', 'user' => (int) $user->get( 'id' ), 'reason' => $reason ), 'raw' );
						}

						$js						=	"$( '#cbfr_" . (int) $field->get( 'fieldid' ) . ",#cbfrd_" . (int) $field->get( 'fieldid' ) . "' ).cbqueryfield({"
												.		"selectors: '" . addslashes( implode( ',', $selectors ) ) . "',"
												.		"url: '" . addslashes( $updateUrl ) . "'"
												.	"});";

						$_CB_framework->outputCbJQuery( $js, 'cbqueryfield' );
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
			case 'querymulticheckbox':
				$fieldType				=	'multicheckbox';
				break;
			case 'queryradio':
				$fieldType				=	'radio';
				break;
			case 'querymultiselect':
				$fieldType				=	'multiselect';
				break;
			case 'queryselect':
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
			case 'querymulticheckbox':
				$fieldType				=	'multicheckbox';
				break;
			case 'queryradio':
				$fieldType				=	'radio';
				break;
			case 'querymultiselect':
				$fieldType				=	'multiselect';
				break;
			case 'queryselect':
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

		static $cache						=	array();

		$options							=	array();
		$cacheId							=	(int) $field->get( 'fieldid' );

		if ( ! isset( $cache[$cacheId] ) ) {
			$sql							=	"SELECT " . $_CB_database->NameQuote( 'fieldtitle' ) . " AS " . $_CB_database->NameQuote( 'value' )
											.	", if ( " . $_CB_database->NameQuote( 'fieldlabel' ) . " != '', " . $_CB_database->NameQuote( 'fieldlabel' ) . ", " . $_CB_database->NameQuote( 'fieldtitle' ) . " ) AS " . $_CB_database->NameQuote( 'text' )
											.	", CONCAT( " . $_CB_database->Quote( 'cbf' ) . ", " . $_CB_database->NameQuote( 'fieldvalueid' ) . " ) AS " . $_CB_database->NameQuote( 'id' )
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_field_values' )
											.	"\n WHERE " . $_CB_database->NameQuote( 'fieldid' ) . " = " . (int) $cacheId
											.	"\n ORDER BY" . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $sql );
			$cache[$cacheId]				=	$_CB_database->loadObjectList();
		}

		$rows								=	$cache[$cacheId];

		if ( $rows ) foreach ( $rows as $row ) {
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

		// Force the user object to CBuser instance to allow substitutions for non-existant users to work:
		$cbUser								=	new CBuser();
		$cbUser->_cbuser					=	$user;

		$query								=	$cbUser->replaceUserVars( $field->params->get( 'qry_query', null ), array( $this, 'escapeSQL' ), false, null, false );

		if ( ! $query ) {
			return $options;
		}

		$valueColumn						=	$field->params->get( 'qry_col_value', null );
		$labelColumn						=	$field->params->get( 'qry_col_label', null );
		$groupColumn						=	$field->params->get( 'qry_col_optgrp', null );
		$optgroups							=	array();

		$cacheId							=	md5( $query );

		if ( ! isset( $cache[$cacheId] ) ) {
			$_SQL_database					=	$this->getDatabase( $field );

			$_SQL_database->setQuery( $query );

			$cache[$cacheId]				=	$_SQL_database->loadAssocList();
		}

		$rows								=	$cache[$cacheId];

		if ( $rows ) foreach ( $rows as $row ) {
			if ( is_array( $row ) ) foreach ( $row as $k => $v ) {
				if ( is_string( $v ) ) {
					$row[$k]				=	trim( $v );
				}
			} elseif ( is_string( $row ) ) {
				$row						=	trim( $row );
			}

			$value							=	null;

			if ( $valueColumn && isset( $row[$valueColumn] ) ) {
				$value						=	$row[$valueColumn];
			}

			$label							=	null;

			if ( $labelColumn && isset( $row[$labelColumn] ) ) {
				$label						=	$row[$labelColumn];

				if ( ! $value ) {
					$value					=	$label;
				}
			} elseif ( $value ) {
				$label						=	$value;
			}

			if ( ( ! $value ) && ( ! $label ) ) {
				if ( is_array( $row ) ) {
					$value					=	array_shift( $row );
				} else {
					$value					=	$row;
				}

				$label						=	$value;
			}

			if ( $value && $label ) {
				if ( $raw ) {
					if ( $raw === 'value' ) {
						$options[]			=	$value;
					} elseif ( $raw === 'text' ) {
						$options[]			=	CBTxt::T( $label );
					} else {
						$options[]			=	array( 'value' => $value, 'text' => CBTxt::T( $label ) );
					}
				} else {
					if ( in_array( $field->get( 'type' ), array( 'queryselect', 'querymultiselect' ) ) ) {
						if ( $groupColumn && isset( $row[$groupColumn] ) && ( $row[$groupColumn] != '' ) && ( ! in_array( $row[$groupColumn], $optgroups ) ) ) {
							$options[]		=	moscomprofilerHTML::makeOptGroup( CBTxt::T( $row[$groupColumn] ) );
							$optgroups[]	=	$row[$groupColumn];
						}
					}

					$options[]				=	moscomprofilerHTML::makeOption( $value, CBTxt::T( $label ) );
				}
			}
		}

		return $options;
	}
}