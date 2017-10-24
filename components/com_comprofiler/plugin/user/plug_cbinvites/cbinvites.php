<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Input\Get;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Database\Table\Table;
use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onAfterUserRegistration', 'acceptInvites', 'cbinvitesPlugin' );
$_PLUGINS->registerFunction( 'onAfterNewUser', 'acceptInvites', 'cbinvitesPlugin' );
$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'deleteInvites', 'cbinvitesPlugin' );

$_PLUGINS->registerUserFieldTypes( array( 'invite_code' => 'cbinvitesField' ) );
$_PLUGINS->registerUserFieldParams();

class cbinvitesClass
{

	/**
	 * @param cbinvitesInviteTable $row
	 * @return bool
	 */
	static public function sendInvite( &$row )
	{
		global $_CB_framework, $_PLUGINS;

		$plugin						=	$_PLUGINS->getLoadedPlugin( 'user', 'cbinvites' );

		if ( ! $plugin ) {
			return false;
		}

		$params						=	$_PLUGINS->getPluginParams( $plugin );

		$cbUser						=	CBuser::getInstance( (int) $row->get( 'user_id' ), false );
		$user						=	$cbUser->getUserData();

		$extra						=	array(	'sitename'	=>	$_CB_framework->getCfg( 'sitename' ),
												'site'		=>	$_CB_framework->getCfg( 'live_site' ),
												'register'	=>	$_CB_framework->viewUrl( 'registers', false, ( $row->get( 'code' ) ? array( 'invite_code' => $row->get( 'code' ) ) : array() ) ),
												'profile'	=>	$_CB_framework->viewUrl( 'userprofile', false, array( 'user' => (int) $row->get( 'user_id' ) ) ),
												'code'		=>	$row->get( 'code' ),
												'to'		=>	$row->get( 'to' )
											);

		$invitePrefix				=	CBTxt::T( $params->get( 'invite_prefix', '[sitename] - ' ) );
		$inviteHeader				=	$cbUser->replaceUserVars( CBTxt::T( $params->get( 'invite_header', '<p>You have been invited by [username] to join [sitename]!</p><br>' ) ), false, false, $extra );
		$inviteFooter				=	$cbUser->replaceUserVars( CBTxt::T( $params->get( 'invite_footer', '<br><p>Invite Code - [code]<br>[sitename] - [site]<br>Registration - [register]<br>[username] - [profile]</p>' ) ), false, false, $extra );
		$inviteHtml					=	( (int) $params->get( 'invite_editor', 2 ) >= 2 ? true : false );

		$mailFromName				=	Get::clean( $cbUser->replaceUserVars( $params->get( 'invite_from_name', null ), true, false, $extra ), GetterInterface::STRING );
		$mailFromAddr				=	Get::clean( $cbUser->replaceUserVars( $params->get( 'invite_from_address', null ), true, false, $extra ), GetterInterface::STRING );
		$mailTo						=	Get::clean( $cbUser->replaceUserVars( $row->get( 'to' ), true, false, $extra ), GetterInterface::STRING );
		$mailCC						=	Get::clean( $cbUser->replaceUserVars( $params->get( 'invite_cc', null ), true, false, $extra ), GetterInterface::STRING );
		$mailBCC					=	Get::clean( $cbUser->replaceUserVars( $params->get( 'invite_bcc', null ), true, false, $extra ), GetterInterface::STRING );
		$mailSubject				=	Get::clean( $cbUser->replaceUserVars( ( $invitePrefix . ( $row->get( 'subject' ) ? $row->get( 'subject' ) : CBTxt::T( 'Join Me!' ) ) ), true, false, $extra ), GetterInterface::STRING );

		if ( $inviteHtml ) {
			$mailBody				=	$inviteHeader . Get::clean( $cbUser->replaceUserVars( $row->get( 'body' ), false, false, $extra ), GetterInterface::HTML ) . $inviteFooter;
		} else {
			$mailBody				=	$inviteHeader . Get::clean( $cbUser->replaceUserVars( $row->get( 'body' ), true, false, $extra ), GetterInterface::STRING ) . $inviteFooter;
		}

		$mailAttachments			=	Get::clean( $cbUser->replaceUserVars( $params->get( 'invite_attachments', null ), true, false, $extra ), GetterInterface::STRING );

		if ( $mailTo ) {
			$mailTo					=	preg_split( '/ *, */', $mailTo );
		}

		if ( $mailCC ) {
			$mailCC					=	preg_split( '/ *, */', $mailCC );
		}

		if ( $mailBCC ) {
			$mailBCC				=	preg_split( '/ *, */', $mailBCC );
		}

		if ( $mailAttachments ) {
			$mailAttachments		=	preg_split( '/ *, */', $mailAttachments );
		}

		if ( $mailTo && $mailSubject && $mailBody ) {
			if ( ! $mailFromName ) {
				$mailFromName		=	$user->name;
				$replyToName		=	null;
			} else {
				$replyToName		=	$user->name;
			}

			if ( ! $mailFromAddr ) {
				$mailFromAddr		=	$user->email;
				$replyToAddr		=	null;
			} else {
				$replyToAddr		=	$user->email;
			}

			$error					=	null;
			$sent					=	comprofilerMail( $mailFromAddr, $mailFromName, $mailTo, $mailSubject, $mailBody, $inviteHtml, $mailCC, $mailBCC, $mailAttachments, $replyToAddr, $replyToName, $error );

			if ( $sent ) {
				return true;
			} else {
				$row->setError( ( $error ? $error : CBTxt::T( 'Mailer failed to send.' ) ) );
			}
		} else {
			if ( ! $mailTo ) {
				$row->setError( CBTxt::T( 'To address missing.' ) );
			} elseif ( ! $mailSubject ) {
				$row->setError( CBTxt::T( 'Subject missing.' ) );
			} elseif ( ! $mailBody ) {
				$row->setError( CBTxt::T( 'Body missing.' ) );
			}
		}

		return false;
	}

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
			$plugin								=	$_PLUGINS->getLoadedPlugin( 'user', 'cbinvites' );

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
}

class cbinvitesInviteTable extends Table
{
	var $id					=	null;
	var $user_id			=	null;
	var $to					=	null;
	var $subject			=	null;
	var $body				=	null;
	var $code				=	null;
	var $sent				=	null;
	var $accepted			=	null;
	var $user				=	null;

	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_invites';

	/**
	 * Primary key(s) of table
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		$this->set( 'code', $this->get( 'code', md5( uniqid() ) ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'invites_onBeforeUpdateInvite', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'invites_onBeforeCreateInvite', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'invites_onAfterUpdateInvite', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'invites_onAfterCreateInvite', array( $this ) );
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

		$_PLUGINS->trigger( 'invites_onBeforeDeleteInvite', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'invites_onAfterDeleteInvite', array( $this ) );

		return true;
	}

	/**
	 * @return bool
	 */
	public function send()
	{
		global $_PLUGINS;

		$this->set( 'sent', Application::Database()->getUtcDateTime() );

		$_PLUGINS->trigger( 'invites_onBeforeSendInvite', array( &$this ) );

		if ( ! cbinvitesClass::sendInvite( $this ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'invites_onAfterSendInvite', array( $this ) );

		if ( $this->getError() || ( ! $this->store() ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param UserTable $user
	 * @return bool
	 */
	public function accept( $user )
	{
		global $_PLUGINS;

		if ( ! $this->isAccepted() ) {
			$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbinvites' );

			if ( ! $plugin ) {
				return false;
			}

			$params				=	$_PLUGINS->getPluginParams( $plugin );

			$mode				=	$params->get( 'invite_connection', 2 );

			$this->set( 'accepted', Application::Database()->getUtcDateTime() );

			$_PLUGINS->trigger( 'invites_onBeforeAcceptInvite', array( &$this, $user ) );

			if ( $this->store() ) {
				if ( $mode ) {
					$connections	=	new cbConnection( $this->get( 'user_id' ) );

					$connections->addConnection( $user->get( 'id' ), null, false );

					if ( $mode == 2 ) {
						$connections->acceptConnection( $this->get( 'user_id' ), $user->get( 'id' ), false );
					}
				}
			} else {
				return false;
			}

			$_PLUGINS->trigger( 'invites_onAfterAcceptInvite', array( $this, $user ) );
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isSent()
	{
		static $cache		=	array();

		$id					=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			if ( $this->get( 'sent' ) && ( $this->get( 'sent' ) != '0000-00-00 00:00:00' ) && ( $this->get( 'sent' ) != '0000-00-00' ) ) {
				$sent		=	true;
			} else {
				$sent		=	false;
			}

			$cache[$id]		=	$sent;
		}

		return $cache[$id];
	}

	/**
	 * @return bool
	 */
	public function isAccepted()
	{
		static $cache		=	array();

		$id					=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			if ( $this->get( 'accepted' ) && ( $this->get( 'accepted' ) != '0000-00-00 00:00:00' ) && ( $this->get( 'accepted' ) != '0000-00-00' ) ) {
				$accepted	=	true;
			} else {
				$accepted	=	false;
			}

			$cache[$id]		=	$accepted;
		}

		return $cache[$id];
	}

	/**
	 * @return int
	 */
	public function dateDifference()
	{
		global $_CB_framework;

		static $cache		=	array();

		$id					=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	( ( $_CB_framework->getUTCNow() - strtotime( $this->get( 'sent' ) ) ) / 86400 );
		}

		return $cache[$id];
	}

	/**
	 * @return bool
	 */
	public function canResend()
	{
		global $_PLUGINS;

		static $cache		=	array();

		$id					=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbinvites' );

			if ( ! $plugin ) {
				return false;
			}

			$params			=	$_PLUGINS->getPluginParams( $plugin );

			if ( ( ! $this->isAccepted() ) && ( ( ! $this->isSent() ) || ( $this->dateDifference() >= (int) $params->get( 'invite_resend', 7 ) ) ) ) {
				$resend		=	true;
			} else {
				$resend		=	false;
			}

			$cache[$id]		=	$resend;
		}

		return $cache[$id];
	}

	/**
	 * @return bool
	 */
	public function isDuplicate()
	{
		global $_CB_database;

		static $cache			=	array();

		$id						=	$this->get( 'to' );

		if ( ! isset( $cache[$id] ) ) {
			$query				=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' );
			if ( $this->get( 'id' ) ) {
				$query			.=	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " != " . (int) $this->get( 'id' )
								.	"\n AND " . $_CB_database->NameQuote( 'to' ) . " = " . $_CB_database->Quote( $id );
			} else {
				$query			.=	"\n WHERE " . $_CB_database->NameQuote( 'to' ) . " = " . $_CB_database->Quote( $id );
			}
			$_CB_database->setQuery( $query );
			$duplicates			=	(int) $_CB_database->loadResult();

			if ( $duplicates ) {
				$duplicate		=	true;
			} else {
				$duplicate		=	false;
			}

			$cache[$id]			=	$duplicate;
		}

		return $cache[$id];
	}
}

class cbinvitesPlugin extends cbPluginHandler
{

	/**
	 * @param UserTable $user
	 */
	public function acceptInvites( $user )
	{
		global $_CB_database;

		$code							=	$user->get( 'invite_code' );

		$query							=	'SELECT *'
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' );
		if ( $code ) {
			$query						.=	"\n WHERE ( " . $_CB_database->NameQuote( 'to' ) . " = " . $_CB_database->Quote( $user->email )
										.	' OR ' . $_CB_database->NameQuote( 'code' ) . ' = ' . $_CB_database->Quote( $code ) . ' )';
		} else {
			$query						.=	"\n WHERE " . $_CB_database->NameQuote( 'to' ) . " = " . $_CB_database->Quote( $user->email );
		}
		$_CB_database->setQuery( $query );
		$invites						=	$_CB_database->loadObjectList( null, 'cbinvitesInviteTable', array( $_CB_database ) );

		/** @var cbinvitesInviteTable[] $invites */
		if ( $invites ) foreach ( $invites as $invite ) {
			$invite->accept( $user );
		}
	}

	/**
	 * @param UserTable $user
	 */
	public function deleteInvites( $user )
	{
		global $_CB_database;

		$query			=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' )
						.	"\n WHERE ( " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->id
						.	' OR ' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->id . ' )';
		$_CB_database->setQuery( $query );
		$invites		=	$_CB_database->loadObjectList( null, 'cbinvitesInviteTable', array( $_CB_database ) );

		/** @var cbinvitesInviteTable[] $invites */
		if ( $invites ) foreach ( $invites as $invite ) {
			$invite->delete();
		}
	}
}

class cbinvitesTab extends cbTabHandler
{

	/**
	 * @param moscomprofilerTabs $tab
	 * @param UserTable          $user
	 * @param int                $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_framework, $_CB_database;

		$viewer					=	CBuser::getMyUserDataInstance();

		if ( $viewer->get( 'id' ) == $user->get( 'id' ) ) {
			if ( ! ( $tab->params instanceof ParamsInterface ) ) {
				$tab->params	=	new Registry( $tab->params );
			}

			outputCbJs( 1 );
			outputCbTemplate( 1 );
			cbimport( 'cb.pagination' );

			cbinvitesClass::getTemplate( 'tab' );

			$limit				=	(int) $tab->params->get( 'tab_limit', 15 );
			$limitstart			=	$_CB_framework->getUserStateFromRequest( 'tab_invites_limitstart{com_comprofiler}', 'tab_invites_limitstart' );
			$filterSearch		=	$_CB_framework->getUserStateFromRequest( 'tab_invites_search{com_comprofiler}', 'tab_invites_search' );
			$where				=	null;
			$join				=	null;

			if ( isset( $filterSearch ) && ( $filterSearch != '' ) ) {
				$where			.=	"\n AND ( a." . $_CB_database->NameQuote( 'to' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false )
								.	" OR b." . $_CB_database->NameQuote( 'id' ) . " = " . $_CB_database->Quote( $filterSearch )
								.	" OR b." . $_CB_database->NameQuote( 'username' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false )
								.	" OR b." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $filterSearch, true ) . '%', false ) . " )";

				$join			.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS b"
								.	' ON b.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'user' );
			}

			$searching			=	( $where ? true : false );

			$query				=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' ) . " AS a"
								.	$join
								.	"\n WHERE a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->id
								.	$where
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'sent' ) . " DESC";
			$_CB_database->setQuery( $query );
			$total				=	$_CB_database->loadResult();

			if ( $total <= $limitstart ) {
				$limitstart		=	0;
			}

			$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

			$pageNav->setInputNamePrefix( 'tab_invites_' );

			$query				=	'SELECT a.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' ) . " AS a"
								.	$join
								.	"\n WHERE a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->id
								.	$where
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'sent' ) . " DESC";
			if ( $tab->params->get( 'tab_paging', 1 ) ) {
				$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
			} else {
				$_CB_database->setQuery( $query );
			}
			$rows				=	$_CB_database->loadObjectList( null, 'cbinvitesInviteTable', array( $_CB_database ) );

			$input				=	array();
			$input['search']	=	'<input type="text" name="tab_invites_search" value="' . htmlspecialchars( $filterSearch ) . '" onchange="document.inviteForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Invites...' ) ) . '" class="form-control" />';

			$class				=	$this->params->get( 'general_class', null );

			$return				=	'<div id="cbInvites" class="cbInvites' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
								.		'<div id="cbInvitesInner" class="cbInvitesInner">'
								.			HTML_cbinvitesTab::showTab( $rows, $pageNav, $searching, $input, $viewer, $user, $tab, $this )
								.		'</div>'
								.	'</div>';

			return $return;
		}

		return null;
	}
}

class cbinvitesField extends CBfield_text
{

	/**
	 * formats variable array into data attribute string
	 *
	 * @param  FieldTable $field
	 * @param  UserTable  $user
	 * @param  string     $output
	 * @param  string     $reason
	 * @param  array      $attributeArray
	 * @return null|string
	 */
	protected function getDataAttributes( $field, $user, $output, $reason, $attributeArray = array() )
	{
		$attributeArray[]	=	cbValidator::getRuleHtmlAttributes( 'cbfield', array( 'user' => (int) $user->id, 'field' => htmlspecialchars( $field->name ), 'reason' => htmlspecialchars( $reason ) ) );

		return parent::getDataAttributes( $field, $user, $output, $reason, $attributeArray );
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		if ( ( $reason == 'register' ) && ( $output == 'htmledit' ) ) {
			$code	=	cbGetParam( $_GET, 'invite_code' );

			if ( $code ) {
				$user->set( 'invite_code', $code );
			}
		}

		$field->set( 'type', 'text' );

		return parent::getField( $field, $user, $output, $reason, $list_compare_types );
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param array      $postdata
	 * @param string     $reason
	 * @return null|string
	 */
	public function fieldClass( &$field, &$user, &$postdata, $reason )
	{
		parent::fieldClass( $field, $user, $postdata, $reason );

		$function					=	cbGetParam( $_GET, 'function', null );
		$valid						=	true;
		$message					=	null;

		if ( $function == 'checkvalue' ) {
			$value					=	stripslashes( cbGetParam( $postdata, 'value', null ) );

			if ( $value ) {
				$invite				=	new cbinvitesInviteTable();

				$invite->load( array( 'code' => $value ) );

				if ( ! $invite->get( 'id' ) ) {
					$valid			=	false;
					$message		=	CBTxt::T( 'Invite code not valid.' );
				} else {
					if ( $invite->isAccepted() ) {
						$valid		=	false;
						$message	=	CBTxt::T( 'Invite code already used.' );
					} else {
						$message	=	CBTxt::T( 'Invite code is valid.' );
					}
				}
			}
		}

		return json_encode( array( 'valid' => $valid, 'message' => $message ) );
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $columnName
	 * @param string     $value
	 * @param array      $postdata
	 * @param string     $reason
	 * @return bool
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		$validated				=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );

		if ( $validated ) {
			if ( ( $user->get( $columnName ) != $value ) && $value ) {
				$invite			=	new cbinvitesInviteTable();

				$invite->load( array( 'code' => $value ) );

				if ( ! $invite->get( 'id' ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Invite code not valid.' ) );

					$validated	=	false;
				} elseif ( $invite->isAccepted() && ( $user->get( 'id' ) != $invite->get( 'user' ) ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Invite code already used.' ) );

					$validated	=	false;
				}
			}
		}

		return $validated;
	}
}
?>