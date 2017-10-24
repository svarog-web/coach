<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJive\Field;

use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CB\Plugin\GroupJive\CBGroupJive;

defined('CBLIB') or die();

class AutoJoinField extends \cbFieldHandler
{

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
		if ( ! in_array( $reason, array( 'register', 'edit' ) ) ) {
			return null;
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
		if ( ( $output != 'htmledit' ) && ( $reason != 'search' ) ) {
			return null;
		}

		$options				=	$this->getGroups( $field, $user );

		if ( ! $options ) {
			return null;
		}

		switch ( $field->get( 'type' ) ) {
			case 'groupmultiautojoin':
				$fieldType		=	'multiselect';
				break;
			case 'groupautojoin':
			default:
				$fieldType		=	'select';
				break;
		}

		return $this->_fieldEditToHtml( $field, $user, $reason, 'input', $fieldType, null, null, $options );
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
		if ( ! in_array( $reason, array( 'register', 'edit' ) ) ) {
			return;
		}

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		$value		=	$this->getValue( $field, $user, $postdata );

		if ( $this->validate( $field, $user, $field->get( 'name' ), $value, $postdata, $reason ) ) {
			$this->_logFieldUpdate( $field, $user, $reason, null, $value );
		}
	}

	/**
	 * Mutator:
	 * Prepares field data commit
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function commitFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		if ( ! in_array( $reason, array( 'register', 'edit' ) ) ) {
			return;
		}

		$value				=	$this->getValue( $field, $user, $postdata );

		if ( $value ) {
			$groups			=	explode( '|*|', $value );

			cbArrayToInts( $groups );

			foreach ( $groups as $groupId ) {
				$row		=	new \CB\Plugin\GroupJive\Table\UserTable();

				$row->load( array( 'user_id' => (int) $user->get( 'id' ), 'group' => (int) $groupId ) );

				if ( $row->get( 'id' ) ) {
					continue;
				}

				$row->set( 'user_id', (int) $user->get( 'id' ) );
				$row->set( 'group', (int) $groupId );
				$row->set( 'status', 1 );

				if ( $row->getError() || ( ! $row->check() ) ) {
					$this->_setValidationError( $field, $user, $reason, $row->getError() );
					break;
				}

				if ( $row->getError() || ( ! $row->store() ) ) {
					$this->_setValidationError( $field, $user, $reason, $row->getError() );
					break;
				}
			}
		}
	}

	/**
	 * Mutator:
	 * Prepares field data rollback
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function rollbackFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		if ( ! in_array( $reason, array( 'register', 'edit' ) ) ) {
			return;
		}

		$value			=	$this->getValue( $field, $user, $postdata, true );

		if ( $value ) {
			$groups		=	explode( '|*|', $value );

			cbArrayToInts( $groups );

			foreach ( $groups as $groupId ) {
				$row	=	new \CB\Plugin\GroupJive\Table\UserTable();

				$row->load( array( 'user_id' => (int) $user->get( 'id' ), 'group' => (int) $groupId ) );

				if ( ! $row->get( 'id' ) ) {
					continue;
				}

				if ( ! $row->canDelete() ) {
					$this->_setValidationError( $field, $user, $reason, $row->getError() );
					break;
				}

				if ( ! $row->delete() ) {
					$this->_setValidationError( $field, $user, $reason, $row->getError() );
					break;
				}
			}
		}
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param bool       $raw
	 * @param bool       $joined
	 * @return \stdClass[]
	 */
	private function getGroups( $field, $user, $raw = false, $joined = false )
	{
		global $_CB_database;

		$excludeCategories		=	explode( '|*|', $field->params->get( 'autojoin_exclude_categories' ) );
		$excludeGroups			=	explode( '|*|', $field->params->get( 'autojoin_exclude_groups' ) );

		if ( $user->get( 'id' ) && ( ! $joined ) ) {
			static $cache		=	array();

			$userId				=	(int) $user->get( 'id' );

			if ( ! isset( $cache[$userId] ) ) {
				$query			=	'SELECT g.' . $_CB_database->NameQuote( 'id' )
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
								.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $userId
								.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId
								.		' OR u.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL )';

				$_CB_database->setQuery( $query );
				$cache[$userId]	=	$_CB_database->loadResultArray();
			}

			if ( $cache[$userId] ) {
				$excludeGroups	=	array_unique( array_merge( $excludeGroups, $cache[$userId] ) );
			}
		}

		$options				=	CBGroupJive::getGroupOptions( $raw, $excludeCategories, $excludeGroups );

		return $options;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param array      $postdata
	 * @param bool       $joined
	 * @return null|string
	 */
	private function getValue( $field, $user, $postdata, $joined = false )
	{
		$value						=	cbGetParam( $postdata, $field->get( 'name' ), null, _CB_ALLOWRAW );

		if ( ( $value === null ) || ( $value === '' ) || ( is_array( $value ) && ( count( $value ) <= 0 ) ) ) {
			$value					=	'';
		} else {
			$options				=	$this->getGroups( $field, $user, true, $joined );
			$groups					=	array();

			foreach ( $options as $option ) {
				$groups[]			=	$option->value;
			}

			if ( is_array( $value ) ) {
				$values				=	array();

				foreach ( $value as $k => $v ) {
					$v				=	stripslashes( $v );

					if ( in_array( $value, $groups ) ) {
						$values[]	=	$v;
					}
				}

				$value				=	$this->_implodeCBvalues( $values );
			} else {
				$value				=	stripslashes( $value );

				if ( ! in_array( $value, $groups ) ) {
					$value			=	null;
				}
			}
		}

		return $value;
	}
}