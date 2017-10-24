<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CBLib\Language\CBTxt;
use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );
$_PLUGINS->registerUserFieldTypes( array( 'gravatar' => 'CBfield_gravatar' ) );
$_PLUGINS->registerUserFieldParams();

class CBfield_gravatar extends CBfield_email
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
		$return						=	null;

		switch ( $output ) {
			case 'html':
			case 'rss':
				$return				=	$this->formatFieldValueLayout( $this->getGravatarHtml( $field, $user, $reason, ( $reason != 'profile' ) ), $reason, $field, $user );
				break;
			case 'htmledit':
				if ( ! $field->params->get( 'gravatar_primary_email', 0, GetterInterface::INT ) ) {
					if ( $reason == 'search' ) {
						$choices	=	array();
						$choices[]	=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'UE_NO_PREFERENCE', 'No preference' ) );
						$choices[]	=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'UE_HAS_PROFILE_IMAGE', 'Has a profile image' ) );
						$choices[]	=	moscomprofilerHTML::makeOption( '0', CBTxt::T( 'UE_HAS_NO_PROFILE_IMAGE', 'Has no profile image' ) );

						$return		=	$this->_fieldSearchModeHtml( $field, $user, $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $user->get( $field->get( 'name', null, GetterInterface::STRING ) ), '', $choices ), 'singlechoice', $list_compare_types );
					} else {
						$return		=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
					}
				}
				break;
			default:
				$return				=	$this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $this->getGravatarUrl( $field, $user, ( $reason != 'profile' ) ), $output );
				break;
		}

		return $return;
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
		$searchMode				=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'isisnot', $list_compare_types );
		$col					=	$field->get( 'name', null, GetterInterface::STRING );
		$value					=	cbGetParam( $postdata, $col );

		if ( $value === '0' ) {
			$value				=	0;
		} elseif ( $value == '1' ) {
			$value				=	1;
		} else {
			$value				=	null;
		}

		$query					=	array();

		if ( $value !== null ) {
			$sql				=	new cbSqlQueryPart();
			$sql->tag			=	'column';
			$sql->name			=	$col;
			$sql->table			=	$field->get( 'table', null, GetterInterface::STRING );
			$sql->type			=	'sql:field';
			$sql->operator		=	$value ? 'IS NOT' : 'IS';
			$sql->value			=	'NULL';
			$sql->valuetype		=	'const:null';
			$sql->searchmode	=	$searchMode;

			$query[]			=	$sql;

			$searchVals->$col	=	$value;
		}

		return $query;
	}

	/**
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  bool        $thumbnail
	 * @return string
	 */
	private function getGravatarUrl( $field, $user, $thumbnail = true )
	{
		if ( $field->params->get( 'gravatar_primary_email', 0, GetterInterface::INT ) ) {
			$email				=	$user->get( 'email', null, GetterInterface::STRING );
		} else {
			$email				=	$user->get( $field->get( 'name', null, GetterInterface::STRING ), null, GetterInterface::STRING );
		}

		$emailValid				=	( $email && cbIsValidEmail( $email ) );
		$tn						=	( $thumbnail ? 'tn' : null );
		$default				=	$field->params->get( 'gravatar_default', null, GetterInterface::STRING );

		if ( $default == 1 ) {
			$default			=	'gravatar_logo';
		} elseif ( $default == 2 ) {
			$default			=	$field->params->get( 'gravatar_default_custom', null, GetterInterface::STRING );
		}

		$defaultGravatar		=	( $default && ( strpos( $default, 'gravatar_' ) === 0 ) );

		if ( ! $default ) {
			$default			=	selectTemplate() . 'images/avatar/' . $tn . 'nophoto_n.png';
		} elseif ( ( $default == 'none' ) || ( $default == 3 ) ) {
			$default			=	null;
		} elseif ( ! $defaultGravatar ) {
			if ( ! file_exists( selectTemplate( 'absolute_path' ) . '/images/avatar/' . $tn . $default ) ) {
				$default		=	'nophoto_n.png';
			}

			$default			=	selectTemplate() . 'images/avatar/' . $tn . $default;
		}

		if ( $emailValid || $defaultGravatar ) {
			$scheme				=	( ( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) ) ? 'https' : 'http' );

			if ( ! $emailValid ) {
				$email			=	null;
			}

			$url				=	$scheme . '://www.gravatar.com/avatar/' . md5( strtolower( trim( $email ) ) );
			$parts				=	array();

			if ( $thumbnail ) {
				$size			=	$field->params->get( 'gravatar_thumb', 60, GetterInterface::INT );
			} else {
				$size			=	$field->params->get( 'gravatar_full', 200, GetterInterface::INT );
			}

			if ( $size ) {
				$parts['s']		=	$size;
			}

			if ( $default && ( $defaultGravatar && ( $default != 'gravatar_logo' ) || ( ( ! $defaultGravatar ) && $field->params->get( 'gravatar_default_external', 1, GetterInterface::INT ) ) ) ) {
				$parts['d']		=	( $defaultGravatar ? str_replace( 'gravatar_', '', $default ) : ( $default == 'none' ? 'blank' : urlencode( $default ) ) );
			}

			$rating				=	$field->params->get( 'gravatar_rating', 'g', GetterInterface::STRING );

			if ( $rating != 'g' ) {
				$parts['r']		=	$rating;
			}

			if ( $parts ) {
				$url			=	$url . '?' . http_build_query( $parts );
			}
		} else {
			$url				=	( $default == 'none' ? null : $default );
		}

		return $url;
	}

	/**
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $reason     'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  bool        $thumbnail
	 * @return string
	 */
	private function getGravatarHtml( &$field, &$user, $reason, $thumbnail = true )
	{
		global $_CB_framework;

		switch ( $field->params->get( 'altText', 0, GetterInterface::INT ) ) {
			case 2:
				$alt		=	cbReplaceVars( $field->params->get( 'altTextCustom', null, GetterInterface::STRING ), $user );
				break;
			case 1:
				$alt		=	null;
				break;
			default:
				$alt		=	cbReplaceVars( $field->title, $user );
				break;
		}

		switch ( $field->params->get( 'titleText', 0, GetterInterface::INT ) ) {
			case 2:
				$title		=	cbReplaceVars( $field->params->get( 'titleTextCustom', null, GetterInterface::STRING ), $user );
				break;
			case 1:
				$title		=	null;
				break;
			default:
				$title		=	cbReplaceVars( $field->title, $user );
				break;
		}

		$imgUrl				=	$this->getGravatarUrl( $field, $user, $thumbnail );

		if ( ! $imgUrl ) {
			return null;
		}

		switch ( $field->params->get( 'imageStyle', 'roundedbordered', GetterInterface::STRING ) ) {
			case 'rounded':
				$style		=	' img-rounded';
				break;
			case 'roundedbordered':
				$style		=	' img-thumbnail';
				break;
			case 'circle':
				$style		=	' img-circle';
				break;
			case 'circlebordered':
				$style		=	' img-thumbnail img-circle';
				break;
			default:
				$style		=	null;
				break;
		}

		$return				=	'<img src="' . $imgUrl . '"' . ( $alt ? ' alt="' . htmlspecialchars( $alt ) . '"' : null ) . ( $title ? ' title="' . htmlspecialchars( $title ) . '"' : null ) . ' class="cbImgPict ' . ( $thumbnail ? 'cbThumbPict' : 'cbFullPict' ) . $style . '" />';

		if ( $user->get( '_allowProfileLink', $field->get( '_allowProfileLink', 1, GetterInterface::INT ), GetterInterface::INT ) && ( ! in_array( $reason, array( 'profile', 'edit' ) ) ) ) {
			$return			=	'<a href="' . $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), true, $field->get( 'tabid', 0, GetterInterface::INT ) ) . '">'
							.		$return
							.	'</a>';
		}

		return $return;
	}
}
