<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'progress' => 'cbprogressfieldField' ) );

class cbprogressfieldField extends cbFieldHandler {

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
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( $output == 'html' ) ) {
			if ( $field->params->get( 'prg_hide', 0 ) ) {
				if ( $this->getComplete( $user, $field, true ) == 100 ) {
					return null;
				}
			}

			if ( $field->params->get( 'prg_private', 1 ) && ( $user->get( 'id' ) != Application::MyUser()->getUserId() ) && ( ! Application::MyUser()->isGlobalModerator() ) ) {
				return null;
			}
		}

		return parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
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
		if ( ! $user->get( 'id' ) ) {
			return null;
		}

		switch( $output ) {
			case 'html':
				return $this->_formatFieldOutput( $field->get( 'name' ), $this->getComplete( $user, $field ), $output, false );
				break;
			case 'htmledit':
				return null;
				break;
			default:
				return $this->_formatFieldOutput( $field->get( 'name' ), $this->getComplete( $user, $field, true ), $output, false );
				break;
		}
	}

	/**
	 * @param  UserTable   $user
	 * @param  FieldTable  $field
	 * @param  bool        $raw
	 * @return mixed
	 */
	private function getComplete( $user, $field, $raw = false )
	{
		$bar								=	$field->params->get( 'prg_bar', 1 );
		$completeness						=	$field->params->get( 'prg_completeness', 1 );
		$checklist							=	$field->params->get( 'prg_checklist', 3 );
		$cbFields							=	$field->params->get( 'prg_fields', null );

		if ( ! $cbFields ) {
			return null;
		}

		$cbFields							=	explode( '|*|', $cbFields );

		cbArrayToInts( $cbFields );

		$userFields							=	$this->getUserFields( $user, $cbFields, $field );

		if ( ! $userFields ) {
			return null;
		}

		$complete							=	0;
		$worth								=	( 100 / count( $userFields ) );

		foreach ( $userFields as $userField ) {
			if ( $userField->complete ) {
				$complete					+=	$worth;
			}
		}

		if ( $complete ) {
			if ( $complete > 100 ) {
				$complete					=	100;
			} else {
				$complete					=	round( $complete, 0 );
			}
		}

		if ( $raw ) {
			return $complete;
		}

		if ( $bar || $checklist ) {
			$content						=	'<div class="cbProgress">';

			if ( $bar ) {
				$barColor					=	null;

				switch ( $bar ) {
					case 'blue':
						$barColor			=	' progress-bar-info';
						break;
					case 'red':
						$barColor			=	' progress-bar-danger';
						break;
					case 'green':
						$barColor			=	' progress-bar-success';
						break;
					case 'orange':
						$barColor			=	' progress-bar-warning';
						break;
				}

				$content					.=		'<div class="cbProgressBar progress">'
											.			'<div class="progress-bar' . $barColor . '" role="progressbar" aria-valuenow="' . $complete . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $complete . '%;">'
											.				( $completeness ? CBTxt::T( 'PROFILE_COMPLETE_PERCENT', '[complete]%', array( '[complete]' => $complete ) ) : null )
											.			'</div>'
											.		'</div>';
			}

			if ( $checklist ) {
				$content					.=		'<div class="cbProgressChecklist well well-sm">';

				foreach( $userFields as $userField ) {
					if ( $checklist == 1 ) {
						if ( $userField->complete ) {
							$content		.=			'<div class="cbProgressChecklistComplete"><span class="fa fa-check text-success"></span> ' . $userField->title . '</div>';
						}
					} if ( $checklist == 2 ) {
						if ( ! $userField->complete ) {
							$content		.=			'<div class="cbProgressChecklistInComplete"><span class="fa fa-times text-danger"></span> ' . $userField->title . '</div>';
						}
					} if ( $checklist == 3 ) {
						$content			.=			'<div class="' . ( $userField->complete ? 'cbProgressChecklistComplete' : 'cbProgressChecklistInComplete' ) . '">' . ( $userField->complete ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-times text-danger"></span>' ) . ' ' . $userField->title . '</div>';
					}
				}

				$content					.=		'</div>';
			}

			$content						.=	'</div>';
		} else {
			$content						=		CBTxt::T( 'PROFILE_COMPLETE_PERCENT', '[complete]%', array( '[complete]' => $complete ) );
		}

		return $content;
	}

	/**
	 * @param  UserTable  $user
	 * @param  int[]      $cbFields
	 * @param  FieldTable $cbField
	 * @return array
	 */
	private function getUserFields( $user, $cbFields, $cbField )
	{
		global $_PLUGINS;

		static $cache								=	array();
		/** @var FieldTable[] $fields */
		static $fields								=	array();

		$userId										=	(int) $user->get( 'id' );
		$progress									=	array();

		foreach ( $cbFields as $cbFieldId ) {
			if ( $cbFieldId == $cbField->get( 'fieldid' ) ) {
				continue;
			}

			if ( ! isset( $cache[$cbFieldId][$userId] ) ) {
				if ( ! isset( $fields[$cbFieldId] ) ) {
					$loadField						=	new FieldTable();

					$loadField->load( (int) $cbFieldId );

					if ( ! ( $loadField->params instanceof ParamsInterface ) ) {
						$loadField->params			=	new Registry( $loadField->params );
					}

					$fields[$cbFieldId]				=	$loadField;
				}

				$field								=	$fields[$cbFieldId];

				if ( ( $field->tablecolumns != '' ) && ( ! trim( $_PLUGINS->callField( $field->get( 'type' ), 'getFieldRow', array( &$field, &$user, 'htmledit', 'none', 'edit', 0 ), $field ) ) ) ) {
					continue;
				}

				$fieldValue							=	$user->get( $field->get( 'name' ) );

				if ( is_array( $fieldValue ) ) {
					$fieldValue						=	implode( '|*|', $fieldValue );
				}

				if ( ( $fieldValue === null ) && ( ! $field->get( 'tablecolumns' ) ) ) {
					$fieldValue						=	$_PLUGINS->callField( $field->get( 'type' ), 'getFieldRow', array( &$field, &$user, 'php', 'none', 'profile', 0 ), $field );

					if ( is_array( $fieldValue ) ) {
						$fieldValue					=	array_shift( $fieldValue );

						if ( is_array( $fieldValue ) ) {
							$fieldValue				=	implode( '|*|', $fieldValue );
						}
					}
				}

				if ( ( $fieldValue == '0000-00-00 00:00:00' ) || ( $fieldValue == '0000-00-00' ) ) {
					$fieldValue						=	null;
				}

				$progressField						=	new stdClass();
				$progressField->id					=	(int) $field->get( 'fieldid' );
				$progressField->title				=	$_PLUGINS->callField( $field->get( 'type' ), 'getFieldTitle', array( &$field, &$user, 'html', 'profile' ), $field );

				if ( ! $progressField->title ) {
					$progressField->title			=	$field->get( 'name' );
				}

				$progressField->value				=	$fieldValue;

				switch ( $field->get( 'type' ) ) {
					case 'checkbox':
						$progressField->complete	=	( (int) $fieldValue === 1 ? true : false );
						break;
					case 'progress':
						$progressField->complete	=	( (int) $fieldValue === 100 ? true : false );
						break;
					default:
						$progressField->complete	=	( $fieldValue != '' ? true : false );
						break;
				}

				$cache[$cbFieldId][$userId]			=	$progressField;
			}

			$progress[]								=	$cache[$cbFieldId][$userId];
		}

		return $progress;
	}
}