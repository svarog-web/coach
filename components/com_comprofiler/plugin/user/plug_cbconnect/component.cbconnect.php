<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\Connect\CBConnect;
use CB\Plugin\Connect\Connect;
use CB\Plugin\Connect\Profile;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBplug_cbconnect extends cbPluginHandler
{
	/** @var Connect  */
	private $connect	=	null;

	/**
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @param array     $postdata
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_PLUGINS, $_CB_database;

		$_PLUGINS->loadPluginGroup( 'user' );

		$providerId					=	$this->input( 'provider', null, GetterInterface::STRING );

		if ( ! $providerId ) {
			// HybridAuth B/C:
			$providerId				=	$this->input( 'hauth_start', null, GetterInterface::STRING );

			if ( ! $providerId ) {
				$providerId			=	$this->input( 'hauth_done', null, GetterInterface::STRING );
			}
		}

		$providerId					=	trim( strtolower( $providerId ) );

		if ( ! $providerId ) {
			CBConnect::returnRedirect( null, 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$this->connect				=	new Connect( $providerId );

		if ( ! $this->connect->provider() ) {
			if ( $this->connect->id ) {
				// CBTxt::T( 'PROVIDER_NOT_AVAILABLE', '[provider] is not available.', array( '[provider]' => $providerId ) )
				CBConnect::returnRedirect( null, 'index.php', CBTxt::T( 'PROVIDER_NOT_AVAILABLE ' . strtoupper( $this->connect->id ) . '_NOT_AVAILABLE', '[provider] is not available.', array( '[provider]' => $providerId ) ), 'error' );
			} else {
				CBConnect::returnRedirect( null, 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
			}
		}

		if ( CBConnect::getReturn( true ) ) {
			$this->connect->provider()->session()->set( $this->connect->id . '.return', CBConnect::getReturn( true ) );
		}

		$error						=	$this->input( 'error_description', null, GetterInterface::STRING );

		if ( ! $error ) {
			$error					=	$this->input( 'error', null, GetterInterface::STRING );
		}

		if ( $error ) {
			// CBTxt::T( 'PROVIDER_FAILED_TO_AUTHENTICATE', '[provider] failed to authenticate. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $error ) )
			CBConnect::returnRedirect( $this->connect->id, 'index.php', CBTxt::T( 'PROVIDER_FAILED_TO_AUTHENTICATE ' . strtoupper( $this->connect->id ) . '_FAILED_TO_AUTHENTICATE', '[provider] failed to authenticate. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $error ) ), 'error' );
		}

		try {
			$this->connect->provider()->authenticate();

			if ( $this->connect->provider()->authorized() ) {
				$profile			=	$this->connect->provider()->profile();

				if ( ! $profile->get( 'id' ) ) {
					// CBTxt::T( 'PROVIDER_PROFILE_MISSING', '[provider] profile could not be found.', array( '[provider]' => $this->connect->name() ) )
					throw new Exception( CBTxt::T( 'PROVIDER_PROFILE_MISSING ' . strtoupper( $this->connect->id ) . '_PROFILE_MISSING', '[provider] profile could not be found.', array( '[provider]' => $this->connect->name() ) ) );
				}

				$query				=	'SELECT ' . $_CB_database->NameQuote( 'id' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' )
									.	"\n WHERE " . $_CB_database->NameQuote( $this->connect->field() ) . " = " . $_CB_database->Quote( $profile->get( 'id' ) );
				$_CB_database->setQuery( $query );
				$userId				=	(int) $_CB_database->loadResult();

				$myUser				=	CBuser::getMyUserDataInstance();
				$user				=	CBuser::getUserDataInstance( $userId );

				if ( $myUser->get( 'id' ) ) {
					if ( ( ! $this->connect->params()->get( 'link', true, GetterInterface::BOOLEAN ) ) && ( ! $myUser->get( $this->connect->field() ) ) ) {
						// CBTxt::T( 'LINKING_FOR_PROVIDER_NOT_PERMITTED', 'Linking for [provider] is not permitted.', array( '[provider]' => $this->connect->name() ) )
						throw new Exception( CBTxt::T( 'LINKING_FOR_PROVIDER_NOT_PERMITTED LINKING_FOR_' . strtoupper( $this->connect->id ) . '_NOT_PERMITTED', 'Linking for [provider] is not permitted.', array( '[provider]' => $this->connect->name() ) ) );
					}

					if ( ! $myUser->get( $this->connect->field() ) ) {
						if ( $user->get( 'id' ) && ( $myUser->get( 'id' ) != $user->get( 'id' ) ) ) {
							// CBTxt::T( 'PROVIDER_ALREADY_LINKED', '[provider] account already linked to another user.', array( '[provider]' => $this->connect->name() ) )
							throw new Exception( CBTxt::T( 'PROVIDER_ALREADY_LINKED ' . strtoupper( $this->connect->id ) . '_ALREADY_LINKED', '[provider] account already linked to another user.', array( '[provider]' => $this->connect->name() ) ) );
						}

						if ( ! $myUser->storeDatabaseValue( $this->connect->field(), $profile->get( 'id' ) ) ) {
							// CBTxt::T( 'PROVIDER_FAILED_TO_LINK', '[provider] account failed to link. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $myUser->getError() ) )
							throw new Exception( CBTxt::T( 'PROVIDER_FAILED_TO_LINK ' . strtoupper( $this->connect->id ) . '_FAILED_TO_LINK', '[provider] account failed to link. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $myUser->getError() ) ) );
						}

						if ( $this->connect->params()->get( 'link_resynchronize', 0 ) ) {
							$this->update( $user, $profile );
						}

						// CBTxt::T( 'PROVIDER_LINKED_SUCCESSFULLY', '[provider] account linked successfully!', array( '[provider]' => $this->connect->name() ) )
						CBConnect::returnRedirect( $this->connect->id, 'index.php', CBTxt::T( 'PROVIDER_LINKED_SUCCESSFULLY ' . strtoupper( $this->connect->id ) . '_LINKED_SUCCESSFULLY', '[provider] account linked successfully!', array( '[provider]' => $this->connect->name() ) ) );
					}

					// CBTxt::T( 'ALREADY_LINKED_TO_PROVIDER', 'You are already linked to a [provider] account.', array( '[provider]' => $this->connect->name() ) )
					throw new Exception( CBTxt::T( 'ALREADY_LINKED_TO_PROVIDER ALREADY_LINKED_TO_' . strtoupper( $this->connect->id ), 'You are already linked to a [provider] account.', array( '[provider]' => $this->connect->name() ) ) );
				} else {
					if ( ( ! $this->connect->params()->get( 'register', true, GetterInterface::BOOLEAN ) ) && ( ! $user->get( 'id' ) ) ) {
						// CBTxt::T( 'SIGN_UP_WITH_PROVIDER_NOT_PERMITTED', 'Sign up with [provider] is not permitted.', array( '[provider]' => $this->connect->name() ) )
						throw new Exception( CBTxt::T( 'SIGN_UP_WITH_PROVIDER_NOT_PERMITTED SIGN_UP_WITH_' . strtoupper( $this->connect->id ) . '_NOT_PERMITTED', 'Sign up with [provider] is not permitted.', array( '[provider]' => $this->connect->name() ) ) );
					}

					$login			=	true;

					if ( ! $user->get( 'id' ) ) {
						$login		=	$this->register( $user, $profile );
					} elseif ( $this->connect->params()->get( 'login_resynchronize', 0 ) ) {
						$this->update( $user, $profile );
					}

					if ( $login ) {
						$this->login( $user );
					}
				}
			}
		} catch ( Exception $e ) {
			CBConnect::returnRedirect( $this->connect->id, 'index.php', $e->getMessage(), 'error' );
		}
	}

	/**
	 * Registers a new user
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @return bool
	 * @throws Exception
	 */
	private function register( &$user, $profile )
	{
		global $_CB_framework, $_PLUGINS, $ueConfig;

		$mode						=	$this->connect->params()->get( 'mode', 1, GetterInterface::INT );
		$approve					=	$this->connect->params()->get( 'approve', 0, GetterInterface::INT );
		$confirm					=	$this->connect->params()->get( 'confirm', 0, GetterInterface::INT );
		$usergroup					=	$this->connect->params()->get( 'usergroup', null, GetterInterface::STRING );
		$approval					=	( $approve == 2 ? $ueConfig['reg_admin_approval'] : $approve );
		$confirmation				=	( $confirm == 2 ? $ueConfig['reg_confirmation'] : $confirm );
		$username					=	$this->username( $user, $profile );
		$dummyUser					=	new UserTable();

		// Username fallback to Username:
		if ( $profile->get( 'username' ) && ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) ) ) ) {
			$username				=	preg_replace( '/[<>\\\\"%();&\']+/', '', trim( $profile->get( 'username' ) ) );
		}

		// Username fallback to Name:
		$name						=	$this->name( $user, $profile );

		if ( $name && ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) ) ) ) {
			$username				=	preg_replace( '/[<>\\\\"%();&\']+/', '', $name );
		}

		// Username fallback to ID:
		if ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) ) ) {
			$username				=	(string) $profile->get( 'id' );
		}

		if ( $mode == 2 ) {
			$user->set( 'email', $profile->get( 'email' ) );
		} else {
			if ( $dummyUser->loadByUsername( $username ) ) {
				throw new Exception( CBTxt::T( 'UE_USERNAME_NOT_AVAILABLE', "The username '[username]' is already in use.", array( '[username]' =>  htmlspecialchars( $username ) ) ) );
			}

			if ( ! $this->email( $user, $profile ) ) {
				return false;
			}

			if ( $dummyUser->loadByEmail( $user->get( 'email' ) ) ) {
				throw new Exception( CBTxt::T( 'UE_EMAIL_NOT_AVAILABLE', "The email '[email]' is already in use.", array( '[email]' =>  htmlspecialchars( $user->get( 'email' ) ) ) ) );
			}

			$this->image( 'avatar', $user, $profile );
			$this->image( 'canvas', $user, $profile );

			if ( ! $usergroup ) {
				$gids				=	array( (int) $_CB_framework->getCfg( 'new_usertype' ) );
			} else {
				$gids				=	cbToArrayOfInt( explode( '|*|', $usergroup ) );
			}

			$user->set( 'gids', $gids );
			$user->set( 'sendEmail', 0 );
			$user->set( 'registerDate', Application::Database()->getUtcDateTime() );
			$user->set( 'password', $user->hashAndSaltPassword( $user->getRandomPassword() ) );
			$user->set( 'registeripaddr', cbGetIPlist() );

			if ( $approval == 0 ) {
				$user->set( 'approved', 1 );
			} else {
				$user->set( 'approved', 0 );
			}

			if ( $confirmation == 0 ) {
				$user->set( 'confirmed', 1 );
			} else {
				$user->set( 'confirmed', 0 );
			}

			if ( ( $user->get( 'confirmed' ) == 1 ) && ( $user->get( 'approved' ) == 1 ) ) {
				$user->set( 'block', 0 );
			} else {
				$user->set( 'block', 1 );
			}
		}

		if ( $name ) {
			$user->set( 'name', $name );
		} else {
			$user->set( 'name', $username );
		}

		switch ( $ueConfig['name_style'] ) {
			case 2:
				$lastName			=	strrpos( $user->get( 'name' ), ' ' );

				if ( $lastName !== false ) {
					$user->set( 'firstname', substr( $user->get( 'name' ), 0, $lastName ) );
					$user->set( 'lastname', substr( $user->get( 'name' ), ( $lastName + 1 ) ) );
				} else {
					$user->set( 'firstname', '' );
					$user->set( 'lastname', $user->get( 'name' ) );
				}
				break;
			case 3:
				$middleName			=	strpos( $user->get( 'name' ), ' ' );
				$lastName			=	strrpos( $user->get( 'name' ), ' ' );

				if ( $lastName !== false ) {
					$user->set( 'firstname', substr( $user->get( 'name' ), 0, $middleName ) );
					$user->set( 'lastname', substr( $user->get( 'name' ), ( $lastName + 1 ) ) );

					if ( $middleName !== $lastName ) {
						$user->set( 'middlename', substr( $user->get( 'name' ), ( $middleName + 1 ), ( $lastName - $middleName - 1 ) ) );
					} else {
						$user->set( 'middlename', '' );
					}
				} else {
					$user->set( 'firstname', '' );
					$user->set( 'lastname', $user->get( 'name' ) );
				}
				break;
		}

		$user->set( 'username', $username );
		$user->set( $this->connect->field(), $profile->get( 'id' ) );

		$this->fields( $user, $profile );

		if ( $mode == 2 ) {
			foreach ( $user as $k => $v ) {
				$_POST[$k]			=	$v;
			}

			$emailPass				=	( isset( $ueConfig['emailpass'] ) ? $ueConfig['emailpass'] : '0' );
			$regErrorMSG			=	null;

			if ( ( ( $_CB_framework->getCfg( 'allowUserRegistration' ) == '0' ) && ( ( ! isset( $ueConfig['reg_admin_allowcbregistration'] ) ) || $ueConfig['reg_admin_allowcbregistration'] != '1' ) ) ) {
				$msg				=	CBTxt::T( 'UE_NOT_AUTHORIZED', 'You are not authorized to view this page!' );
			} else {
				$msg				=	null;
			}

			$_PLUGINS->trigger( 'onBeforeRegisterFormRequest', array( &$msg, $emailPass, &$regErrorMSG ) );

			if ( $msg ) {
				$_CB_framework->enqueueMessage( $msg, 'error' );
				return false;
			}

			$fieldsQuery			=	null;
			$results				=	$_PLUGINS->trigger( 'onBeforeRegisterForm', array( 'com_comprofiler', $emailPass, &$regErrorMSG, $fieldsQuery ) );

			if ( $_PLUGINS->is_errors() ) {
				$_CB_framework->enqueueMessage( $_PLUGINS->getErrorMSG( '<br />' ), 'error' );
				return false;
			}

			if ( implode( '', $results ) != '' ) {
				$return				=		'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
									.			'<div>' . implode( '</div><div>', $results ) . '</div>'
									.		'</div>';

				echo $return;
				return false;
			}

			// CBTxt::T( 'PROVIDER_SIGN_UP_INCOMPLETE', 'Your [provider] sign up is incomplete. Please complete the following.', array( '[provider]' => $this->connect->name() ) )
			$_CB_framework->enqueueMessage( CBTxt::T( 'PROVIDER_SIGN_UP_INCOMPLETE ' . strtoupper( $this->connect->id ) . '_SIGN_UP_INCOMPLETE', 'Your [provider] sign up is incomplete. Please complete the following.', array( '[provider]' => $this->connect->name() ) ) );

			HTML_comprofiler::registerForm( 'com_comprofiler', $emailPass, $user, $_POST, $regErrorMSG );
			return false;
		} else {
			$_PLUGINS->trigger( 'onBeforeUserRegistration', array( &$user, &$user ) );

			if ( $user->store() ) {
				if ( $user->get( 'confirmed' ) == 0 ) {
					$user->store();
				}

				$messagesToUser		=	activateUser( $user, 1, 'UserRegistration' );

				$_PLUGINS->trigger( 'onAfterUserRegistration', array( &$user, &$user, true ) );

				if ( $user->get( 'block' ) == 1 ) {
					$return			=		'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
									.			'<div>' . implode( '</div><div>', $messagesToUser ) . '</div>'
									.		'</div>';

					echo $return;

					return false;
				} else {
					return true;
				}
			}

			// CBTxt::T( 'SIGN_UP_WITH_PROVIDER_FAILED', 'Sign up with [provider] failed. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $user->getError() ) )
			throw new Exception( CBTxt::T( 'SIGN_UP_WITH_PROVIDER_FAILED SIGN_UP_WITH_' . strtoupper( $this->connect->id ) . '_FAILED', 'Sign up with [provider] failed. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $user->getError() ) ) );
		}
	}

	/**
	 * Updates a user
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @return bool
	 */
	private function update( &$user, $profile )
	{
		global $ueConfig;

		$username						=	$this->username( $user, $profile );
		$dummyUser						=	new UserTable();

		// Username fallback to Username:
		if ( $profile->get( 'username' ) && ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) && ( $dummyUser->get( 'id' ) != $user->get( 'id' ) ) ) ) ) {
			$username					=	preg_replace( '/[<>\\\\"%();&\']+/', '', trim( $profile->get( 'username' ) ) );
		}

		// Username fallback to Name:
		$name							=	$this->name( $user, $profile );

		if ( $name && ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) && ( $dummyUser->get( 'id' ) != $user->get( 'id' ) ) ) ) ) {
			$username					=	preg_replace( '/[<>\\\\"%();&\']+/', '', $name );
		}

		// Username fallback to ID:
		if ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) && ( $dummyUser->get( 'id' ) != $user->get( 'id' ) ) ) ) {
			$username					=	(string) $profile->get( 'id' );
		}

		// If username exists, doesn't match, and doesn't belong to another user then remap it:
		if ( $username && ( $username != $user->get( 'username' ) ) && ( ( ! $dummyUser->loadByUsername( $username ) ) || ( $dummyUser->get( 'id' ) == $user->get( 'id' ) ) ) ) {
			$user->set( 'username', $username );
		}

		// If email exists, doesn't match, and doesn't belong to another user then remap it:
		if ( $profile->get( 'email' ) && ( $profile->get( 'email' ) != $user->get( 'email' ) ) && ( ( ! $dummyUser->loadByEmail( $profile->get( 'email' ) ) ) || ( $dummyUser->get( 'id' ) == $user->get( 'id' ) ) ) ) {
			$user->set( 'email', $profile->get( 'email' ) );
		}

		if ( $name ) {
			if ( $name != $user->get( 'name' ) ) {
				$user->set( 'name', $name );
			}
		} elseif ( $username ) {
			if ( $username != $user->get( 'name' ) ) {
				$user->set( 'name', $username );
			}
		}

		switch ( $ueConfig['name_style'] ) {
			case 2:
				$lastNamePos			=	strrpos( $user->get( 'name' ), ' ' );
				$middleName				=	'';

				if ( $lastNamePos !== false ) {
					$firstName			=	substr( $user->get( 'name' ), 0, $lastNamePos );
					$lastName			=	substr( $user->get( 'name' ), ( $lastNamePos + 1 ) );
				} else {
					$firstName			=	'';
					$lastName			=	$user->get( 'name' );
				}
				break;
			case 3:
				$middleNamePos			=	strpos( $user->get( 'name' ), ' ' );
				$lastNamePos			=	strrpos( $user->get( 'name' ), ' ' );

				if ( $lastNamePos !== false ) {
					$firstName			=	substr( $user->get( 'name' ), 0, $middleNamePos );
					$lastName			=	substr( $user->get( 'name' ), ( $lastNamePos + 1 ) );

					if ( $middleNamePos !== $lastNamePos ) {
						$middleName		=	substr( $user->get( 'name' ), ( $middleNamePos + 1 ), ( $lastNamePos - $middleNamePos - 1 ) );
					} else {
						$middleName		=	'';
					}
				} else {
					$firstName			=	'';
					$middleName			=	'';
					$lastName			=	$user->get( 'name' );
				}
				break;
			default:
				$firstName				=	'';
				$middleName				=	'';
				$lastName				=	'';
				break;
		}

		if ( $firstName != $user->get( 'firstname' ) ) {
			$user->set( 'firstname', $firstName );
		}

		if ( $middleName != $user->get( 'middlename' ) ) {
			$user->set( 'middlename', $middleName );
		}

		if ( $lastName != $user->get( 'lastname' ) ) {
			$user->set( 'lastname', $lastName );
		}

		$this->image( 'avatar', $user, $profile );
		$this->image( 'canvas', $user, $profile );

		$this->fields( $user, $profile );

		$user->store();
	}

	/**
	 * Returns formatted name
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @return string
	 */
	private function name( &$user, $profile )
	{
		if ( $profile->get( 'name' ) ) {
			$name		=	trim( $profile->get( 'name' ) );
		} elseif ( $profile->get( 'firstname' ) || $profile->get( 'middlename' ) || $profile->get( 'lastname' ) ) {
			$name		=	$profile->get( 'firstname' );

			if ( $profile->get( 'middlename' ) )  {
				$name	.=	' ' . $profile->get( 'middlename' );
			}

			if ( $profile->get( 'lastname' ) )  {
				$name	.=	' ' . $profile->get( 'lastname' );
			}

			$name		=	trim( $name );
		} else {
			$name		=	null;
		}

		return $name;
	}

	/**
	 * Returns formatted username
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @return string
	 */
	private function username( &$user, $profile )
	{
		$providers				=	CBConnect::getProviders();
		$usernameFormat			=	$this->connect->params()->get( 'username', null, GetterInterface::STRING );
		$username				=	null;

		if ( $usernameFormat ) {
			$extras				=	array(	'provider' => $this->connect->id, 'profile_id' => $profile->get( 'id' ), 'profile_username' => $profile->get( 'username' ),
											'profile_name' => $profile->get( 'name' ), 'profile_firstname' => $profile->get( 'firstname' ), 'profile_middlename' => $profile->get( 'middlename' ),
											'profile_lastname' => $profile->get( 'lastname' ), 'profile_email' => $profile->get( 'email' ) );

			foreach ( $providers[$this->connect->id]['fields'] as $field ) {
				$k				=	$this->connect->id . '_' . trim( strtolower( str_replace( array( '.', '-' ), '_', $field ) ) );

				$extras[$k]		=	$profile->profile()->get( $field, null, GetterInterface::STRING );
			}

			$username			=	preg_replace( '/[<>\\\\"%();&\']+/', '', trim( cbReplaceVars( $usernameFormat, $user, true, false, $extras, false ) ) );
		}

		return $username;
	}

	/**
	 * Checks if an email address has been supplied by the provider or if email form needs to render
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @return bool
	 */
	private function email( &$user, $profile )
	{
		global $_CB_framework;

		$email						=	$this->input( 'email', null, GetterInterface::STRING );
		$emailVerify				=	$this->input( 'email__verify', null, GetterInterface::STRING );

		if ( $email ) {
			if ( ! cbIsValidEmail( $email ) ) {
				$_CB_framework->enqueueMessage( sprintf( CBTxt::T( 'UE_EMAIL_NOVALID', 'This is not a valid email address.' ), htmlspecialchars( $email ) ), 'error' );

				$email				=	null;
			} else {
				$field				=	new FieldTable();

				$field->load( array( 'name' => 'email' ) );

				$field->set( 'params', new Registry( $field->get( 'params' ) ) );

				if ( $field->params->get( 'fieldVerifyInput', 0 ) && ( $email != $emailVerify ) ) {
					$_CB_framework->enqueueMessage( CBTxt::T( 'Email and verification do not match, please try again.' ), 'error' );

					$email			=	null;
				}
			}
		}

		if ( ! $email ) {
			$email					=	$profile->get( 'email' );
		}

		if ( ! $email ) {
			$regAntiSpamValues		=	cbGetRegAntiSpams();

			outputCbTemplate();
			outputCbJs();
			cbValidator::loadValidation();

			$cbUser					=	CBuser::getInstance( null );

			// CBTxt::T( 'PROVIDER_SIGN_UP_INCOMPLETE', 'Your [provider] sign up is incomplete. Please complete the following.', array( '[provider]' => $this->connect->name() ) )
			$_CB_framework->enqueueMessage( CBTxt::T( 'PROVIDER_SIGN_UP_INCOMPLETE ' . strtoupper( $this->connect->id ) . '_SIGN_UP_INCOMPLETE', 'Your [provider] sign up is incomplete. Please complete the following.', array( '[provider]' => $this->connect->name() ) ) );

			$return					=	'<form action="' . $_CB_framework->pluginClassUrl( $this->element, false, array( 'provider' => $this->connect->id ) ) . '" method="post" enctype="multipart/form-data" name="adminForm" id="cbcheckedadminForm" class="cb_form form-auto cbValidation">'
									.		'<div class="cbRegistrationTitle page-header">'
									.			'<h3>' . CBTxt::T( 'Sign up incomplete' ) . '</h3>'
									.		'</div>'
									.		$cbUser->getField( 'email', null, 'htmledit', 'div', 'register', 0, true, array( 'required' => 1, 'edit' => 1, 'registration' => 1 ) )
									.		'<div class="form-group cb_form_line clearfix">'
									.			'<div class="col-sm-offset-3 col-sm-9">'
									.				'<input type="submit" value="Sign up" class="btn btn-primary cbRegistrationSubmit" data-submit-text="Loading...">'
									.			'</div>'
									.		'</div>'
									.		cbGetSpoofInputTag( 'plugin' )
									.		cbGetRegAntiSpamInputTag( $regAntiSpamValues )
									.	'</form>';

			echo $return;

			return false;
		}

		$user->set( 'email', $email );

		return true;
	}

	/**
	 * Parses profile data for an image and uploads it
	 *
	 * @param string    $type
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @throws Exception
	 */
	private function image( $type = 'avatar', &$user, $profile )
	{
		global $_CB_framework, $ueConfig;

		$tmpPath							=	$_CB_framework->getCfg( 'absolute_path' ) . '/tmp/';
		$imagePath							=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/';

		if ( ( ! $type ) || ( ! in_array( $type, array( 'avatar', 'canvas' ) ) ) ) {
			return;
		}

		if ( $profile->get( $type ) ) {
			$hash							=	substr( md5( $profile->get( $type ) ), 0, 6 );

			if ( $user->get( $type ) && ( $hash == substr( $user->get( $type ), 0, 6 ) ) ) {
				// The hashes are the same. Now check if the file exists. If it does then skip image processing:
				if ( file_exists( $imagePath . $user->get( $type ) ) ) {
					return;
				}
			}

			try {
				$field						=	new FieldTable();

				$field->load( array( 'name' => $type ) );

				$field->set( 'params', new Registry( $field->get( 'params' ) ) );

				$conversionType				=	(int) ( isset( $ueConfig['conversiontype'] ) ? $ueConfig['conversiontype'] : 0 );
				$imageSoftware				=	( $conversionType == 5 ? 'gmagick' : ( $conversionType == 1 ? 'imagick' : ( $conversionType == 4 ? 'gd' : 'auto' ) ) );
				$fileName					=	uniqid( $hash . '_' );
				$resize						=	$field->params->get( 'avatarResizeAlways', '' );

				if ( $resize == '' ) {
					if ( isset( $ueConfig['avatarResizeAlways'] ) ) {
						$resize				=	$ueConfig['avatarResizeAlways'];
					} else {
						$resize				=	1;
					}
				}

				$aspectRatio				=	$field->params->get( 'avatarMaintainRatio', '' );

				if ( $aspectRatio == '' ) {
					if ( isset( $ueConfig['avatarMaintainRatio'] ) ) {
						$aspectRatio		=	$ueConfig['avatarMaintainRatio'];
					} else {
						$aspectRatio		=	1;
					}
				}

				$image						=	new \CBLib\Image\Image( $imageSoftware, $resize, $aspectRatio );
				/** @var GuzzleHttp\ClientInterface $client */
				$client						=	new GuzzleHttp\Client();
				/** @var GuzzleHttp\Message\Response $result */
				$result						=	$client->get( $profile->get( $type ) );

				if ( $result->getStatusCode() != 200 ) {
					return;
				}

				$photo						=	$image->getImagine()->load( $result->getBody() );

				if ( ! $photo ) {
					return;
				}

				$ext						=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $profile->get( $type ), PATHINFO_EXTENSION ) ) );

				if ( ( ! $ext ) || ( ! in_array( $ext, array( 'jpg', 'jpeg', 'gif', 'png' ) ) ) ) {
					$mime					=	$result->getHeader( 'Content-Type' );

					switch ( $mime ) {
						case 'image/jpeg':
							$ext			=	'jpg';
							break;
						case 'image/png':
							$ext			=	'png';
							break;
						case 'image/gif':
							$ext			=	'gif';
							break;
					}
				}

				if ( ! in_array( $ext, array( 'jpg', 'jpeg', 'gif', 'png' ) ) ) {
					return;
				}

				$tmpImage					=	$tmpPath . $fileName . '.' . $ext;

				$photo->save( $tmpImage );

				$image->setImage( $photo );
				$image->setName( $fileName );
				$image->setSource( $tmpImage );
				$image->setDestination( $imagePath );

				$width						=	$field->params->get( 'avatarWidth', ( $type == 'canvas' ? 1280 : '' ) );

				if ( $width == '' ) {
					if ( isset( $ueConfig['avatarWidth'] ) ) {
						$width				=	$ueConfig['avatarWidth'];
					} else {
						$width				=	200;
					}
				}

				$height						=	$field->params->get( 'avatarHeight', ( $type == 'canvas' ? 640 : '' ) );

				if ( $height == '' ) {
					if ( isset( $ueConfig['avatarHeight'] ) ) {
						$height				=	$ueConfig['avatarHeight'];
					} else {
						$height				=	500;
					}
				}

				$image->processImage( $width, $height );

				if ( $user->get( $type ) ) {
					if ( file_exists( $imagePath . $user->get( $type ) ) ) {
						@unlink( $imagePath . $user->get( $type ) );
					}

					if ( file_exists( $imagePath . 'tn' . $user->get( $type ) ) ) {
						@unlink( $imagePath . 'tn' . $user->get( $type ) );
					}
				}

				$user->set( $type, $image->getCleanFilename() );

				$image->setName( 'tn' . $fileName );

				$thumbWidth					=	$field->params->get( 'thumbWidth', ( $type == 'canvas' ? 640 : '' ) );

				if ( $thumbWidth == '' ) {
					if ( isset( $ueConfig['thumbWidth'] ) ) {
						$thumbWidth			=	$ueConfig['thumbWidth'];
					} else {
						$thumbWidth			=	60;
					}
				}

				$thumbHeight				=	$field->params->get( 'thumbHeight', ( $type == 'canvas' ? 320 : '' ) );

				if ( $thumbHeight == '' ) {
					if ( isset( $ueConfig['thumbHeight'] ) ) {
						$thumbHeight		=	$ueConfig['thumbHeight'];
					} else {
						$thumbHeight		=	86;
					}
				}

				$image->processImage( $thumbWidth, $thumbHeight );

				unlink( $tmpImage );

				$approval					=	$this->connect->params()->get( $type . '_approve', 2, GetterInterface::INT );

				if ( $approval == 2 ) {
					$approval				=	$field->params->get( 'avatarUploadApproval', '' );

					if ( $approval == '' ) {
						if ( isset( $ueConfig['avatarUploadApproval'] ) ) {
							$approval		=	$ueConfig['avatarUploadApproval'];
						} else {
							$approval		=	1;
						}
					}
				}

				$user->set( $type . 'approved', ( $approval ? 0 : 1 ) );
			} catch ( Exception $e ) {
				if ( $_CB_framework->getCfg( 'debug' ) ) {
					throw new Exception( $e->getMessage() );
				}
			}
		}
	}

	/**
	 * Maps profile fields to the user
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @return bool
	 */
	private function fields( &$user, $profile )
	{
		$providers			=	CBConnect::getProviders();
		$allowed			=	$providers[$this->connect->id]['fields'];
		$exclude			=	array( 'id', 'username', 'name', 'firstname', 'middlename', 'lastname', 'email', 'avatar' );

		foreach ( $this->connect->params()->subTree( 'fields' ) as $field ) {
			/** @var ParamsInterface $field */
			$fromField		=	$field->get( 'from', null, GetterInterface::STRING );
			$toField		=	$field->get( 'to', null, GetterInterface::STRING );

			if ( ( ! $fromField ) || ( ! $toField ) || in_array( $toField, $exclude ) || ( ! in_array( $fromField, $allowed ) ) ) {
				continue;
			}

			$value			=	$profile->profile()->get( $fromField, null, GetterInterface::STRING );

			if ( is_array( $value ) || is_object( $value ) ) {
				continue;
			}

			$user->set( $toField, $value );
		}
	}

	/**
	 * Logs in a user
	 *
	 * @param UserTable $user
	 * @return bool
	 * @throws Exception
	 */
	private function login( $user )
	{
		global $_CB_framework;

		$cbAuthenticate			=	new CBAuthentication();
		$messagesToUser			=	array();
		$alertMessages			=	array();
		$redirectUrl			=	null;
		$resultError			=	$cbAuthenticate->login( $user->get( 'username' ), false, true, true, $redirectUrl, $messagesToUser, $alertMessages, 1 );

		if ( count( $messagesToUser ) > 0 ) {
			if ( $resultError ) {
				$_CB_framework->enqueueMessage( $resultError, 'error' );
			}

			$return				=		'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
								.			'<div>' . implode( '</div><div>', $messagesToUser ) . '</div>'
								.		'</div>';

			echo $return;

			return false;
		} elseif ( $resultError ) {
			throw new Exception( $resultError );
		} else {
			$redirect			=	null;

			if ( ( ! $user->get( 'lastvisitDate' ) ) || ( $user->get( 'lastvisitDate' ) == '0000-00-00 00:00:00' ) ) {
				$redirect		=	$this->connect->params()->get( 'firstlogin', true, GetterInterface::STRING );
			}

			if ( ! $redirect ) {
				$redirect		=	$this->connect->params()->get( 'login', true, GetterInterface::STRING );
			}

			if ( ! $redirect ) {
				$redirect		=	base64_decode( $this->connect->provider()->session()->get( $this->connect->id . '.return' ) );
			}

			if ( ! $redirect ) {
				$redirect		=	CBConnect::getReturn( true, true );
			}

			if ( ! $redirect ) {
				$redirect		=	'index.php';
			}

			$message			=	( count( $alertMessages ) > 0 ? stripslashes( implode( '<br />', $alertMessages ) ) : null );

			cbRedirect( $redirect, $message, 'message' );
		}

		return true;
	}
}