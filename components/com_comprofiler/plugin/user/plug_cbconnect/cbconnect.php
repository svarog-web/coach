<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Registry\Registry;
use CB\Plugin\Connect\CBConnect;
use CB\Plugin\Connect\Connect;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->registerFunction( 'onAfterLoginForm', 'getLoginButtons', 'cbconnectPlugin' );
$_PLUGINS->registerFunction( 'onAfterLogoutForm', 'getLoginButtons', 'cbconnectPlugin' );
$_PLUGINS->registerFunction( 'onBeforeRegisterFormDisplay', 'getRegistrationButtons', 'cbconnectPlugin' );
$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'socialid' => 'cbconnectField' ) );

class cbconnectPlugin extends cbPluginHandler
{

	/**
	 * Outputs the provider buttons to the login/logout form
	 *
	 * @param int      $nameLenght
	 * @param int      $passLenght
	 * @param int      $horizontal
	 * @param string   $classSfx
	 * @param Registry $params
	 * @return array|null|string
	 */
	public function getLoginButtons( $nameLenght, $passLenght, $horizontal, $classSfx, $params )
	{
		global $_CB_framework;

		$return				=	null;

		foreach ( CBConnect::getProviders() as $id => $provider ) {
			$connect		=	new Connect( $id );

			$return			.=	$connect->button( $horizontal );
		}

		if ( $return ) {
			static $CSS		=	0;

			if ( ! $CSS++ ) {
				$_CB_framework->document->addHeadStyleSheet( $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/plugin/user/plug_cbconnect/css/cbconnect.css' );
			}

			$return			=	'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . ' cbConnectButtons">'
							.		$return
							.	'</div>';

			return array( 'afterButton' => $return );
		}

		return null;
	}

	/**
	 * Outputs the provider buttons to the registration form
	 *
	 * @param UserTable $user
	 * @param string    $regErrorMSG
	 * @return null|string
	 */
	public function getRegistrationButtons( $user, $regErrorMSG )
	{
		global $_CB_framework;

		if ( $user->get( 'id' ) ) {
			return null;
		}

		$return				=	null;

		foreach ( CBConnect::getProviders() as $id => $provider ) {
			$connect		=	new Connect( $id );

			$return			.=	$connect->button( 1, true );
		}

		if ( $return ) {
			static $CSS		=	0;

			if ( ! $CSS++ ) {
				$_CB_framework->document->addHeadStyleSheet( $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/plugin/user/plug_cbconnect/css/cbconnect.css' );
			}

			$return			=	'<div class="content-spacer text-center cb_template cb_template_' . selectTemplate( 'dir' ) . ' cbConnectButtons">'
							.		$return
							.	'</div>';

			return $return;
		}

		return null;
	}
}

class cbconnectField extends cbFieldHandler
{

	/**
	 * Translates a fields name to its provider id
	 *
	 * @param FieldTable $field
	 * @return null|string
	 */
	private function fieldToProviderId( $field )
	{
		$return				=	null;

		foreach ( CBConnect::getProviders() as $id => $provider ) {
			if ( $provider['field'] == $field->get( 'name' ) ) {
				$return		=	$id;
				break;
			}
		}

		return $return;
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
		$providerId					=	$this->fieldToProviderId( $field );

		if ( ! $providerId ) {
			return null;
		}

		$connect					=	new Connect( $providerId );
		$value						=	$user->get( $field->get( 'name' ) );
		$return						=	null;

		switch( $output ) {
			case 'htmledit':
				if ( $reason == 'search' ) {
					$return			=	$this->_fieldSearchModeHtml( $field, $user, $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, null ), 'text', $list_compare_types );
				} else {
					if ( Application::Cms()->getClientId() ) {
						$return		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, null );
					} elseif ( $value && ( $user->get( 'id' ) == Application::MyUser()->get( 'id' ) ) ) {
						$values		=	array();
						// CBTxt::T( 'UNLINK_PROVIDER_ACCOUNT', 'Unlink your [provider] account', array( '[provider]' => $connect->name() ) )
						$values[]	=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'UNLINK_PROVIDER_ACCOUNT UNLINK_' . strtoupper( $connect->id ) . '_ACCOUNT', 'Unlink your [provider] account', array( '[provider]' => $connect->name() ) ) );

						$return		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'multicheckbox', null, null, $values );
					} elseif ( $value && ( ! Application::MyUser()->get( 'id' ) ) ) {
						$url		=	$connect->profileUrl( $value );

						if ( $url ) {
							$url	=	'<a href="' . $url . '" target="_blank" rel="nofollow">'
											// CBTxt::T( 'PROVIDER_PROFILE', '[provider] profile', array( '[provider]' => $connect->name() ) )
									.		CBTxt::T( 'PROVIDER_PROFILE ' . strtoupper( $connect->id ) . '_PROFILE', '[provider] profile', array( '[provider]' => $connect->name() ) )
									.	'</a>';
						}

						if ( ! $url ) {
							// CBTxt::T( 'PROVIDER_PROFILE_ID', '[provider] profile id [provider_id]', array( '[provider]' => $connect->name(), '[provider_id]' => $value ) )
							$url	=	CBTxt::T( 'PROVIDER_PROFILE_ID ' . strtoupper( $connect->id ) . '_PROFILE_ID', '[provider] profile id [provider_id]', array( '[provider]' => $connect->name(), '[provider_id]' => $value ) );
						}

						// CBTxt::T( 'PROVIDER_PROFILE_LINKED_TO_ACCOUNT', 'Your [provider_profile] will be linked to this account.', array( '[provider]' => $connect->name(), '[provider_profile]' => $url, '[provider_id]' => $value ) )
						$return		=	CBTxt::T( 'PROVIDER_PROFILE_LINKED_TO_ACCOUNT ' . strtoupper( $connect->id ) . '_PROFILE_LINKED_TO_ACCOUNT', 'Your [provider_profile] will be linked to this account.', array( '[provider]' => $connect->name(), '[provider_profile]' => $url, '[provider_id]' => $value ) )
									.	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'hidden', $value, null );
					}
				}
				break;
			case 'html':
			case 'rss':
				if ( $value ) {
					$url			=	$connect->profileUrl( $value );

					if ( $url ) {
						$value		=	'<a href="' . $url . '" target="_blank" rel="nofollow">'
											// CBTxt::T( 'VIEW_PROVIDER_PROFILE', 'View [provider] Profile', array( '[provider]' => $connect->name() ) )
									.		CBTxt::T( 'VIEW_PROVIDER_PROFILE VIEW_' . strtoupper( $connect->id ) . '_PROFILE', 'View [provider] Profile', array( '[provider]' => $connect->name() ) )
									.	'</a>';
					}
				}

				$return				=	$this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name' ), $value, $output, false ), $reason, $field, $user, false );
				break;
			default:
				$return				=	$this->_formatFieldOutput( $field->get( 'name' ), $value, $output );
				break;
		}

		return $return;
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
		if ( ! $this->fieldToProviderId( $field ) ) {
			return;
		}

		$fieldName							=	$field->get( 'name' );
		$currentValue						=	$user->get( $fieldName );
		$value								=	cbGetParam( $postdata, $fieldName );

		if ( $currentValue && ( $user->get( 'id' ) == Application::MyUser()->get( 'id' ) ) ) {
			if ( is_array( $value ) ) {
				if ( isset( $value[0] ) && ( $value[0] == 1 ) ) {
					$postdata[$fieldName]	=	'';
				}
			}

			$value							=	cbGetParam( $postdata, $fieldName );
		}

		if ( ( ! Application::Cms()->getClientId() ) && $user->get( 'id' ) && $currentValue && ( $value !== '' ) ) {
			$postdata[$fieldName]			=	$currentValue;
		}

		parent::prepareFieldDataSave( $field, $user, $postdata, $reason );
	}
}