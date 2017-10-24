<?php
/**
 * Joomla Community Builder User Plugin: plug_cbprofilebook
 * @version $Id: $
 * @package CommunityBuilder ProfileBook
 * @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CB\Database\Table\FieldTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Database\Table\Table;
use CBLib\Registry\Registry;

/** ensure this file is being included by a parent file */
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->registerFunction( 'onAfterLogoutForm', 'onAfterLogoutForm', 'getProfileBookTab' );
$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'userDeleted','getProfileBookTab' );
$_PLUGINS->registerUserFieldTypes( array( 	'pb_profile_rating'		=> 'CBfield_pb_profile_rating' ) );	// reserved, used now: 'other_types', future reserved: 'all_types'

class CBfield_pb_profile_rating extends cbFieldHandler
{
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		global $_CB_database;

		$oReturn						=	'';

		$fpbStatDisplay					=	$this->params->get( 'pbStatRating', '1' );

		if ( ( $fpbStatDisplay == 1 ) && isset( $user ) && $user->id ) {

			pbcbRatings::_getLanguageFile();

			$sql						=	'SELECT AVG(postervote) FROM #__comprofiler_plug_profilebook WHERE userid = ' . (int) $user->id . ' AND postervote > 0';
			$_CB_database->setQuery( $sql );
			$value						=	$_CB_database->loadResult();
	
			switch ( $output ) {
				case 'html':
				case 'rss':
					if ( $value !== null ) {
						$value			=	(int) round( $value, 0 );
					}
					$oReturn			=	pbcbRatings::_getRatingImage( $value, true );
					break;

				case 'htmledit':
					$oReturn			=	null;
					/*
					if ( $reason == 'search' ) {
						$oReturn		=	$this->_fieldSearchModeHtml( $field, $user, $oReturn, 'none', $list_compare_types );		//TBD: search by rating
					}
					*/
					break;

				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					if ( $value !== null ) {
						$value			=	(float) $value;
					}
					$oReturn			=	$this->_formatFieldOutputIntBoolFloat( $field->name, $value, $output );;
					break;
			}
		}
		return $oReturn;
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save user edit, 'register' for save registration
	 * @return boolean                          True: All fields have validated, False: Some fields didn't validate
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		return true;				// nothing to do, Status fields don't save :-)
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason    'edit' for save user edit, 'register' for save registration
	 * @return boolean                          True: All fields have validated, False: Some fields didn't validate
	 */
	function bindSearchCriteria( &$field, &$user, &$postdata, $list_compare_types, $reason )
	{
		return array();
	}
}

class pbProfileBookEntry extends Table
{
	public $id;				// sql:int(11)
	public $mode;				// sql:char(1)
	public $posterid;			// sql:int(11)
	public $posterip;			// sql:varchar(255)
	public $postername;		// sql:varchar(255)
	public $posteremail;		// sql:varchar(255)
	public $posterlocation;	// sql:varchar(255)
	public $posterurl;			// sql:varchar(255)
	public $postervote;		// sql:int(11)
	public $postertitle;		// sql:varchar(255)
	public $postercomment;		// sql:text
	public $date;				// sql:datetime
	public $userid;			// sql:int(11)
	public $feedback;			// sql:text
	public $editdate;			// sql:datetime
	public $editedbyid;		// sql:int(11)
	public $editedbyname;		// sql:varchar(255)
	public $published;			// sql:tinyint(3)
	public $status;			// sql:tinyint(3)

	public $_pbid;
	/**
	 * Table name in database
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plug_profilebook';

	/**
	 * Primary key(s) of table
	 * @var array
	 */
	protected $_tbl_key		=	array( 'id' => 'int' );
}

class getprofilebookTab extends cbTabHandler
{
	protected $pbconfig;

	/**
	 * Called after logout form in the CB login module (if CB plugins integration activated in CB login module)
	 *
	 * @param  int       $name_length
	 * @param  int       $pass_length
	 * @param  int       $horizontal
	 * @param  string    $class_sfx
	 * @param  Registry  $params
	 * @return string|null
	 */
	function onAfterLogoutForm( /** @noinspection PhpUnusedParameterInspection */ $name_length, $pass_length, $horizontal, $class_sfx, &$params )
	{
		global $_CB_database;

		$_CB_database->setQuery( 'SELECT COUNT(*) FROM #__comprofiler_plug_profilebook WHERE status = 0 AND userid = ' . (int) Application::MyUser()->get( 'id' ) );
		$unreadPB	=	$_CB_database->loadResult();
		if ( $unreadPB > 0 ) {
			// $this->pbconfig->MainMode		=	$this->params->get( 'pbMainMode', 'guestbook' );
			// $modeName		=	( $this->pbconfig->MainMode == 'guestbook' ? CBTxt::T( "guestbook" ) : ( $this->pbconfig->MainMode == 'wall' ? CBTxt::T( "wall" ) : '' ) );
			$modeName			=	'';
			return '<a href="' . $this->_getAbsURLwithParam( array() ) . '" class="mod_login' . $class_sfx . '">' . sprintf( CBTxt::Th( $unreadPB == 1 ? "You have %s new %s post" : "You have %s new %s posts" ), $unreadPB, $modeName ) . '</a>';
		} else {
			return null;
		}
	}
	/**
	 * UserBot Called when a user is deleted from backend (prepare future unregistration)
	 * @param  UserTable  $user  Reflecting the user being deleted
	 * @returns true if all is ok, or false if ErrorMSG generated
	 */
	function userDeleted( $user )
	{
		global $_CB_database;
		
		$sql		=	'DELETE FROM #__comprofiler_plug_profilebook'
					.	' WHERE userid = ' . (int) $user->id
					;
		$_CB_database->SetQuery( $sql );
		$_CB_database->query();

		return true;
	}

	/**
	 * Saves a new entry
	 *
	 * Uses: $this->pbconfig->MainMode, $this->pbconfig->AllowAnony, $this->pbconfig->UseLocation, $this->pbconfig->LocationField, $this->pbconfig->UseWebAddress, $this->pbconfig->WebField, $this->pbconfig->EnableRating
	 *
	 * @param  int       $userId
	 * @param  TabTable  $tab
	 * @return boolean
	 */
	function pbSave( $userId, &$tab )
	{
		global $_CB_framework, $_CB_database, $ueConfig;

		$posterId						=	Application::MyUser()->getUserId();

		if ( $userId == 0 ) {
			return false;
		}
		if ( ! ( ( $this->pbconfig->AllowAnony == '1' ) || ( $this->pbconfig->AllowAnony == '0' && $posterId > 0 ) ) ) {
			return false;
		}
		if ( ( $this->pbconfig->MainMode == 'blog' ) && ( $userId != $posterId ) ) {
			return false;
		}
		if ( ( $this->pbconfig->MainMode == 'guestbook' ) && ( $userId == $posterId ) ) {
			return false;
		}
		
		$cbUser							=	CBuser::getInstance( $posterId );
		$posterUser						=	$cbUser ? $cbUser->getUserData() : null;

		$entry							=	new pbProfileBookEntry( $_CB_database );
		$entry->mode					=	$this->pbconfig->MainMode[0];
		$entry->posterid				=	(int) $posterId;
		$entry->posterip				=	cbGetIPlist();
		if ( ( $posterId == 0 ) || Application::User( $posterId )->isGlobalModerator() ) {
			$entry->postername			=	$this->_myUnescapedReqParam( 'postername', null );
			$entry->posteremail			=	$this->_myUnescapedReqParam( 'posteremail', null );
		} else {
			$entry->postername			=	cbUnHtmlspecialchars( getNameFormat( $posterUser->name, $posterUser->username, $ueConfig['name_format'] ) );
			$entry->posteremail			=	$posterUser->email;
		}
		if ( $this->pbconfig->UseLocation ) {
			if ( $posterId && $this->pbconfig->LocationField != 0 ) {
				$locationField			=	new FieldTable( $_CB_database );
				$locationField->load( $this->pbconfig->LocationField );
				$naLocationField		=	$locationField->name;
				$entry->posterlocation	=	$posterUser->$naLocationField;
			} else {
				$entry->posterlocation	=	$this->_myUnescapedReqParam( 'posterlocation', null );
			}
		}
		if ( $this->pbconfig->UseWebAddress ) {
			if ( $posterId && $this->pbconfig->WebField != 0 ) {
				$webfield				=	new FieldTable();
				$webfield->load( $this->pbconfig->WebField );
				$naWebField				=	$webfield->name;
				$entry->posterurl		=	$posterUser->$naWebField;
			} else {
				$entry->posterurl		=	$this->_myUnescapedReqParam( 'posterurl', null );
			}
		}
		if ( $this->pbconfig->EnableRating && ( $posterId != $userId ) ) {
			$entry->postervote			=	$this->_myUnescapedReqParam( 'postervote', null );
			if ( $entry->postervote !== null ) {
				if ( pbcbRatings::validateRating( $entry->postervote ) ) {
					$entry->postervote	=	(int) $entry->postervote;
				} elseif ( $this->pbconfig->EnableRating == 3 ) {
					return false;
				}
			}
		}
		$entry->postertitle				=	$this->_myUnescapedReqParam( 'postertitle', null );
		$entry->postercomment			=	$this->_myUnescapedReqParam( 'postercomments', null );
		$entry->date					=	date( 'Y-m-d H:i:s', $_CB_framework->now() );
		$entry->userid					=	(int) $userId;
		$entry->published				=	( $this->getUserParam( $userId, 'autopublish') ? 1 : 0 );
		$entry->status					=	( $entry->userid == $entry->posterid ? 1 : 0 );

		$query			=	'SELECT COUNT(*) FROM #__comprofiler_plug_profilebook'
						.	' WHERE postername = '	. $_CB_database->Quote( $entry->postername )
						.	' AND posteremail = '	. $_CB_database->Quote( $entry->posteremail )
						.	' AND postertitle = '	. $_CB_database->Quote( $entry->postertitle )
						.	' AND postercomment = '. $_CB_database->Quote( $entry->postercomment )
						.	' AND posterid = '		. (int) $entry->posterid
						.	' AND userid = '		. (int) $userId
						;
		$_CB_database->setQuery( $query );
		$count							=	$_CB_database->loadResult();

		$res							=	true;
		// avoid double-posts on clicking reload:
		if ( $count == 0 ) {
			if ( ! $entry->store() ) {
				trigger_error( 'cbProfileBook Save SQL error: ' . $entry->getError(), E_USER_WARNING );
				return false;
			}
			$notify				=	$this->getUserParam( $userId, 'notifyme' );

			if ( $notify && ( $userId != $posterId ) ) {
				$autoPublish		=	$this->getUserParam( $userId, 'autopublish' );
				$cbNotification		=	new cbNotification( );
				$res	=	$cbNotification->sendFromSystem( (int) $userId, sprintf( CBTxt::T( "You have received a new entry in your %s" ), CBTxt::T( $tab->title ) ),
															  sprintf( CBTxt::T( "%s has just submitted a new entry in your %s." ), $entry->postername, CBTxt::T( $tab->title ) )
															  . sprintf( $autoPublish ? CBTxt::T( "\n\nYour current setting is that new entries in your %1\$s are automatically published. To see the new entry, please login. You can then see the new entry and take appropriate action if needed. Direct access to your %1\$s:\n%2\$s\n" )
															  						   : CBTxt::T( "\n\nYour current setting is that you need to review entries in your %1\$s. Please login, review the new entry and publish if you agree. Direct access to your %1\$s:\n%2\$s\n" ),
															  			CBTxt::T( $tab->title ), cbUnHtmlspecialchars( $this->_getAbsURLwithParam( array() ) ) ) );
			}
		}
		return $res;
	}

	/**
	* gets an * UNESCAPED * and urldecoded request parameter for the plugin
	* you need to call stripslashes to remove escapes, and htmlspecialchars before displaying.
	*
	* @param  string  $name     name of parameter in REQUEST URL
	* @param  string  $def      default value of parameter in REQUEST URL if none found
	* @param  string  $postfix  postfix for identifying multiple pagings/search/sorts (optional)
	* @return string            value of the parameter (urldecode processed for international and special chars) and ESCAPED! and ALLOW HTML!
	*/
	function _myUnescapedReqParam( $name, $def = null, $postfix = '' )
	{
		$string				=	$this->_getReqParam( $name, $def, $postfix );
		if ( $string !== null ) {
			$string			=	stripslashes( $string );
		}
		return $string;
	}

	function pbEdit( $id, $userId, $curruser, $iAmModerator, $tab )
	{
		global $_CB_database, $ueConfig;

		$postertitle		=	$this->_getReqParam( 'postertitle', null );
		$postercomment		=	$this->_getReqParam( 'postercomments', null );
		$postername			=	$this->_getReqParam( 'postername', null );
		$posterlocation		=	$this->_getReqParam( 'posterlocation', null );
		$posteremail		=	$this->_getReqParam( 'posteremail', null );
		$posterurl			=	$this->_getReqParam( 'posterurl', null );
		$posterid			=	Application::MyUser()->getUserId();
		$posterip			=	cbGetIPlist();
		$postervote			=	$this->_getReqParam( 'postervote', 'NULL' );
		
		$editedbyname		=	( $posterid ? getNameFormat( $curruser->name, $curruser->username, $ueConfig['name_format'] ) : stripslashes( $postername ) );

		$query				=	'UPDATE #__comprofiler_plug_profilebook'
							.	' SET editdate = NOW()'
							.	( $posterid ? ', editedbyid = ' . $posterid : '' )
							.	', editedbyname = '		. $_CB_database->Quote( $editedbyname )
							.	', postername = '		. $_CB_database->Quote( stripslashes( $postername ) )
							.	( $posteremail ? ', posteremail = ' . $_CB_database->Quote( stripslashes( $posteremail ) ) : '' )
							.	', posterlocation = '	. $_CB_database->Quote( stripslashes( $posterlocation ) )
							.	', postertitle = '		. $_CB_database->Quote( stripslashes( $postertitle ) )
							.	', postercomment = '	. $_CB_database->Quote( stripslashes( $postercomment ) )
							.	', postervote = '		. (int) $postervote
							.	', posterip = '			. $_CB_database->Quote( $posterip )
						//	.	( $published !== null ? ', published = ' . (int) $published : '' )
							.	', posterurl = '		. $_CB_database->Quote( stripslashes( $posterurl ) )
							.	' WHERE id = '			. (int) $id
							.	' AND userid = '		. (int) $userId
							.	( $iAmModerator ? '' : ' AND posterid = ' . (int) $posterid )
							;
		$_CB_database->setQuery( $query );
		$_CB_database->query();
		$notify				=	$this->getUserParam( $userId, 'notifyme' );
		
		$res				=	true;
		if ( $notify && ( $userId != $posterid ) ) {
			$autoPublish		=	$this->getUserParam( $userId, 'autopublish' );
			$cbNotification		=	new cbNotification( );
			$res			=	$cbNotification->sendFromSystem( (int) $userId, sprintf( CBTxt::T( "An entry in your %s has just been updated" ), CBTxt::T( $tab->title ) ),
																   sprintf( CBTxt::T( "%s has just submitted an edited entry for %s in your %s." ), $editedbyname, $postername, CBTxt::T( $tab->title ) )
																   . ( $iAmModerator ? '' : sprintf( $autoPublish ? CBTxt::T( "\n\nYour current setting is that new entries in your %1\$s are automatically published. To see the new entry, please login. You can then see the new entry and take appropriate action if needed. Direct access to your %1\$s:\n%2\$s\n" )
																   													: CBTxt::T( "\n\nYour current setting is that you need to review entries in your %1\$s. Please login, review the new entry and publish if you agree. Direct access to your %1\$s:\n%2\$s\n" ),
																   									 CBTxt::T( $tab->title ), cbUnHtmlspecialchars( $this->_getAbsURLwithParam( array() ) ) ) ) );
		}
		return $res;
	}

	function pbDelete( $id )
	{
		global $_CB_database;

		$entry				=	new pbProfileBookEntry( $_CB_database );
		if ( ! $entry->delete( (int) $id ) ) {
			trigger_error( 'cbProfileBook Delete SQL error: ' . $entry->getError(), E_USER_WARNING );
			return;
		}
	}

	function pbUpdate( $id, $isMe )
	{
		global $_CB_database;

		$feedback			=	stripslashes( $this->_getReqParam( 'feedback', null ) );
		if ( ! $isMe && $feedback )
			$feedback		=	'[' . CBTxt::T( "Edited by Site Moderator" ) . ']: ' . $feedback;
		$published			=	$this->_getReqParam( 'published', 0 );
		$query				=	'UPDATE #__comprofiler_plug_profilebook '
							.	' SET feedback = ' . $_CB_database->Quote( $feedback )
							.	', published = '	. (int) $published
							.	' WHERE id = '		. (int) $id;
		$_CB_database->setQuery( $query );
		$_CB_database->query();
	}

	function pbPublish( $id )
	{
		global $_CB_database;
		
		$published			=	$this->_getReqParam( 'published', 0 );
		$query				=	'UPDATE #__comprofiler_plug_profilebook'
							.	' SET published = '	. (int) $published
							.	' WHERE id = '		. (int) $id
							;
		$_CB_database->setQuery( $query );
		$_CB_database->query();
	}
	
	function pbStatus( $id, $status = null )
	{
		global $_CB_database;

		if ( $status === null ) {
			$status		=	( $this->_getReqParam( 'status', 0 ) ? 1 : 0 );
		}
		$query		=	'UPDATE #__comprofiler_plug_profilebook'
					.	' SET status = ' . (int) $status
					.	' WHERE id = ' . (int) $id;
		$_CB_database->setQuery( $query );
		$_CB_database->query();
	}

	function getEditTab( $tab, $user, $ui )
	{
		pbcbRatings::_getLanguageFile();
	}
	
	/**
	 * Generates the HTML to display the user profile tab
	 * @param  TabTable   $tab   tab reflecting the tab database entry
	 * @param  UserTable  $user  reflecting the user being displayed
	 * @param  int        $ui    1 for front-end, 2 for back-end
	 * @returns mixed            : either string HTML for tab content, or false if ErrorMSG generated
	 */
	function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_database, $ueConfig;
		
		pbcbRatings::_getLanguageFile();

		$myId				=	Application::MyUser()->getUserId();
		$iAmModerator		=	Application::MyUser()->isGlobalModerator();
		
		//Get the tab related parameters, these settings are global and set by administrator
		$this->getPbConfig();
		
		//Return if the user doesn't have the ProfileBook enabled no need to go any further
		if ( ! $this->getUserParam( $user->id, 'enable') ) {
			return null;
		}
		
		//If there is a tab description display it
		$return				=	$this->_writeTabDescription( $tab, $user, 'cbpbDescr_' . $this->pbconfig->MainMode );

		//Check to see if there are actions that need to be executed
		$action				=	$this->_getReqParam( 'formaction' .  $this->pbconfig->MainMode[0], null );
		$id					=	$this->_getReqParam( 'id', 0 );
		$showform			=	( $this->pbconfig->ShowEditor == '1' ) || $this->_getReqParam( 'showform' .  $this->pbconfig->MainMode[0], 0 );
		
		//Section for posting a new entry
		$item				=	new pbProfileBookEntry( $_CB_database );
		$item->posterid		=	- 1;
		$item->_pbid		=	null;
		$item->userid		=	$user->id;

		//Check to see if the visting user is the profile owner
		if ( $myId != $user->id ) {
			//Not the owner
			$isME			=	false;
			
			//Check to see if the user is logged in
			if ( $myId == 0 ) {
				//Not logged in so assumed to be annonymous
				$isAnony	=	true;
				$required	=	true;
				$curruser	=	null;
			} else {
				//Yes logged in so not annonymous
				$isAnony	=	false;
				$required	=	false;
				
				//get the attributes of the user visiting the profile
				$currCBuser		=&	CBuser::getInstance( $myId );
				if ( ! $currCBuser ) {
					return null;
				}
				$curruser		=	$currCBuser->getUserData();
			}
			
			//Allow Posting based on AllowAnony config setting
			if ( ( $this->pbconfig->MainMode != 'blog' ) && ( ( $this->pbconfig->AllowAnony == '1' ) || ( $this->pbconfig->AllowAnony == '0' && $myId > 0 ) ) ) {
				
				//Check to see if a user has submitted a profile entry to be saved
				if ( $action == 'new' ) {
					
					$resultError				=	null;
					if ( $this->_getReqParam( 'postercomments' ) == '' ) {
						$resultError			=	htmlspecialchars( CBTxt::T( "Comment is Required!" ) );
					}
					if ( $this->pbconfig->ShowTitle && ( $this->_getReqParam( 'postertitle' ) == '' ) ) {
						$resultError			=	htmlspecialchars( CBTxt::T( "Title is Required!" ) );
					}
					// Captcha integration:
					if ( ( $this->pbconfig->Captcha == 2 ) || ( ( $this->pbconfig->Captcha == 1 ) && ( $curruser === null ) ) ) {
						global $_PLUGINS;
						
						$_PLUGINS->loadPluginGroup( 'user' );
						$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );
						if ( $_PLUGINS->is_errors() ) {
							$resultError		=	$_PLUGINS->getErrorMSG();
						}
					}
					
					if ( $resultError === null ) {
						$this->pbSave( $user->id, $tab );
					} else {
						$item					=	new pbProfileBookEntry( $_CB_database );
						$item->postertitle		=	$this->_getReqParam( 'postertitle' );
						$item->postercomment	=	$this->_getReqParam( 'postercomments' );
						$item->postername		=	$this->_getReqParam( 'postername' );
						$item->posterlocation	=	$this->_getReqParam( 'posterlocation' );
						$item->posteremail		=	$this->_getReqParam( 'posteremail' );
						$item->posterurl		=	$this->_getReqParam( 'posterurl' );
						$item->postervote		=	$this->_getReqParam( 'postervote' );
						$item->posterid			=	- 1;
						$item->_pbid			=	null;
					}
					if ( ( $resultError !== null ) || ( $this->pbconfig->MainMode == 'blog' ) ) {
						// in case of error, or in case of a successfully posted blog, can post again:
						$formName				=	'pbnewcomment' . $this->pbconfig->MainMode;
						$return					.=	'<div class="error">' . $resultError . '</div>';
						$editorHtml				=	null;
						$return					.=	$this->_hiddenBBeditor( $editorHtml, $item, $formName, true, true, $curruser, $required, null );
						$return					.=	$editorHtml;
					}
				} else {
					$formName					=	'pbnewcomment' . $this->pbconfig->MainMode;
					$editorHtml					=	null;
					$return			.=	$this->_hiddenBBeditor( $editorHtml, $item, $formName, true, $showform, $curruser, $required, null );
					$return			.=	$editorHtml;
				}
			
			}
		} else {
			//The visiting user is the profile owner
			$isME				=	true;
			$isAnony			=	false;
			$required			=	false;
			$curruser			=	& $user;
			if ( in_array( $this->pbconfig->MainMode, array( 'blog', 'wall' ) ) ) {
				$formName		=	'pbnewcomment' . $this->pbconfig->MainMode;
				$editorHtml		=	null;
				$return			.=	$this->_hiddenBBeditor( $editorHtml, $item, $formName, true, $showform, $curruser, $required, null );
				$return			.=	$editorHtml;
			}
		}
		if ( $iAmModerator || ! $isAnony ) {
			if ( $action == 'edit' ) {
				$this->pbEdit( $id, $user->id, $curruser, $iAmModerator, $tab );
			}
		}
		
		if ( $isME || $iAmModerator ) {
			//Take necessary profile owner action if there is
			switch ( $action ) {
				CASE 'new' :
					if ( in_array( $this->pbconfig->MainMode, array( 'blog', 'wall' ) ) ) {
						$this->pbSave( $user->id, $tab );
						//print "I'm publishing post id:".$id;
					}
				break;
				CASE 'delete' :
					$this->pbDelete( $id );
				//print "I'm deleting post id:".$id;
				break;
				CASE 'update' :
					$this->pbUpdate( $id, $isME );
				//print "I'm updating feedback for post id:".$id;
				break;
				CASE 'publish' :
					$this->pbPublish( $id );
				//print "I'm publishing post id:".$id;
				break;
				CASE 'status' :
					$this->pbStatus( $id );
				//print "I'm reading post id:".$id;
				break;
				DEFAULT :
				//print "I'm doing nothing:".$id." action:".$action;
				break;
			}
		}
		
		//Find and Show Postings
		$pagingParams		=	$this->_getPaging( array(), array( 'pbposts_' ) );
		$pWHERE				=	null;
		
		//if the user viewing the profile is not the owner then only show published entries
		if ( ! $isME && ! $iAmModerator ) {
			$pWHERE 		=	"\n AND published = 1";
		}

		$total				=	null;

		//check to see if the Admin enabled pagination
		if ( $this->pbconfig->PagingEnabled ) {
			//select a count of all applicable entries for pagination
			$query			=	'SELECT COUNT(*)'
							.	"\n FROM #__comprofiler_plug_profilebook"
							.	"\n WHERE userid = " . (int) $user->id
							.	"\n AND mode = " . $_CB_database->Quote( $this->pbconfig->MainMode[0] )
							.	"\n " . $pWHERE
							;
			$_CB_database->setQuery( $query );
			$total			=	$_CB_database->loadResult();
			
			if ( ! is_numeric( $total ) )
				$total		=	0;
			
			if ( $pagingParams['pbposts_limitstart'] === null )
				$pagingParams['pbposts_limitstart']	=	'0';
			if ( $this->pbconfig->EntriesPerPage > $total )
				$pagingParams['pbposts_limitstart']	=	'0';
		
		} else {
			$pagingParams['pbposts_limitstart']		=	'0';
		}

		//select all entries and related details
		$query			=	'SELECT *, pb.id AS _pbid '
						.	"\n FROM #__comprofiler_plug_profilebook pb"
						.	"\n LEFT JOIN #__users u ON pb.posterid=u.id"
						.	"\n LEFT JOIN #__comprofiler c ON pb.posterid=c.id"
						.	"\n WHERE pb.userid=" . (int) $user->id
						.	"\n AND mode = " . $_CB_database->Quote( $this->pbconfig->MainMode[0] )
						.	$pWHERE
						.	"\n ORDER BY date "
						.	$this->pbconfig->SortDirection
						;
		$_CB_database->setQuery( $query, (int) ( $pagingParams["pbposts_limitstart"] ? $pagingParams["pbposts_limitstart"] : 0 ), (int) $this->pbconfig->EntriesPerPage );
		// print $_CB_database->getQuery();
		$items			=	$_CB_database->loadObjectList();
		
		//check to make sure we got at least 1 record
		if ( count( $items ) > 0 ) {
			$this->_getpbCSS();

			//build header information for display table
			$return		.=	"<div class=\"cbpbMainArea\" style=\"text-align:left;width:100%;\">";
			if ( $this->pbconfig->MainMode == 'blog' ) {
				$return			.=	"\n" . '<div class="cbpbEntries cbpbBlog">';
			} else {
				$return			.=	"\n<table cellpadding=\"3\" cellspacing=\"0\" style=\"border:0; width:100%; table-layout:fixed; word-wrap:break-word;\">";
				$return			.=	"\n\t<tr class=\"sectiontableheader\">";
				$return			.=	"\n<th style=\"width:30%;\">" . CBTxt::Th( "Name" ) . "</th>";
				$return			.=	"\n<th style=\"width:70%;\">" . CBTxt::Th( "Entry" ) . "</th>";
				$return			.=	"\n</tr>";
			}
			$i					=	2;
			$kk					=	0;
			//iterate through each item and display it accordingly
			foreach ( $items as $item ) {
				$kk++;
				$k				=	$this->pbconfig->MainMode[0] . $kk;
				$i				=	( $i == 1 ) ? 2 : 1;
				
				//get the date that the entry was submitted on a format it according to CB config
				$signtime		=	cbFormatDate( $item->date );
				$edittime		=	cbFormatDate( $item->editdate );
				
				$img			=	null;
				//check to see if Ratings are enabled
				if ( $this->pbconfig->EnableRating ) {
					$img		=	pbcbRatings::_getRatingImage( $item->postervote, false, true );
				}

				//if the profile visitor is a moderator and the ip address of the poster is not null then show the ip address with link to lookup site
				$ip				=	'';
				if ( $iAmModerator && $item->posterip != '' ) {
					$ips		=	explode( ',', $item->posterip );
					foreach ( $ips as $poster_proxy_ip ) {
						// $ip		.=	"<br /><a href=\"http://openrbl.org/dnsbl?i=" . $poster_proxy_ip . "&amp;f=2\" target=\"_blank\">"
						$ip		.=	"<br /><a href=\"http://whatismyipaddress.com/ip/" . htmlspecialchars( $poster_proxy_ip ) . "&amp;f=2\" target=\"_blank\">"
								.	htmlspecialchars( $poster_proxy_ip ) . '</a>';
					}
				}

				//start a new row for the record
				if ( $this->pbconfig->MainMode == 'blog' ) {
					$return		.=	"\n\t" . '<div class="cbpbEntry cbpboe' . $i . '">';
					$returnFavor =	null;
				} else {
					$return		.=	"\n\t<tr class=\"sectiontableentry$i\" style=\"margin-bottom:5px;\" >";
					$pimg		=	'';
					$returnFavor =	null;
					//check to see if the entry was submitted by a member
					if ( $item->posterid != 0 && $item->posterid != '' && $item->posterid != null && isset( $item->username ) ) {
						
						//make link to profile and format name according to cb config
						$pname		=	"<a href=\"" . cbSef( 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user=' . $item->posterid ) . '">' . htmlspecialchars( getNameFormat( $item->name, $item->username, $ueConfig['name_format'] ) ) . '</a>';
						
						//get users avatar if they have one
						// if (isset($item->avatar)) {
						$pimg		=	'<br />' . CBuser::getInstance( $item->posterid )->getField( 'avatar', null, 'html', 'none', 'list' );
						// }
						//get users primary email address and display it according to CB config
						$pEmail		=	'<br />' . CBuser::getInstance( $item->posterid )->getField( 'primaryemailaddress' );
						
						if ( $this->pbconfig->EnableGesture && $isME ) {
							$returnFavor	=	'| <a href="' . $this->_getAbsURLwithParam( array( 'user' => $item->posterid, ( 'showform' .  $this->pbconfig->MainMode[0] ) => 1 ) ) . '">'
											.	CBTxt::Th( "Return Gesture" )
											.	'</a>';
						}
					
					} else {
						//entry was submitted by anonymous user just diplay entered data
						$pname		=	htmlspecialchars( $item->postername );
						$pEmail		=	'<br />' . '<a href="mailto:' . htmlspecialchars( $item->posteremail ) . '">' .  htmlspecialchars( $item->posteremail ) . '</a>';
					}
					//check to see if the location was entered to determine how to display it
					if ( $this->pbconfig->UseLocation && $item->posterlocation != null && $item->posterlocation != '' ) {
						$pLocation	=	'<br />' . htmlspecialchars( $item->posterlocation );
					} else {
						$pLocation	=	null;
					}
						//check to see if the web address was entered to determine how to display it
					if ( $this->pbconfig->UseWebAddress && $item->posterurl != null && $item->posterurl != '' ) {
						$URL		=	'<br />' . $this->_displayWebAddress( $item->posterurl );
					} else {
						$URL		=	null;
					}

				    if ( ! $this->pbconfig->EntryShowName ) {
						$pname		=	null;
					}

				    if ( ! $this->pbconfig->EntryShowEmail ) {
						$pEmail		=	null;
					}

				    if ( ! $this->pbconfig->EntryShowIP ) {
						$ip			=	null;
					}

				    if ( ! $this->pbconfig->EntryShowAvatar ) {
						$pimg		=	null;
					}

					//display information about the poster
					$return			.=	"\n\t\t<td style=\"overflow:hidden;\" valign=\"top\"><b>" . $pname . '</b>' . $pEmail . $pLocation . $URL . $ip . $pimg . '</td>';
					$return			.=	"\n\t\t<td style=\"overflow:hidden;\" valign=\"top\">";
				}

				$return			.=	'<div class="cbpbSnglMsgAll">';

				// display title:
				if ( $item->postertitle ) {
					$return		.=	'<h3 class="pbTitle">' . htmlspecialchars( $item->postertitle ) . '</h3>';
				}

				//display unpublished, signed on date, edited by on date, and rating
				$return			.=	'<div class="cbpbHead">' . '<div class="small cbpbDateInfo">';
				if ( ! $item->published ) {
					$return		.=	'<strong>' . CBTxt::Th( "Not Published" ) . '</strong> | ';
				}
				$return			.=	sprintf( CBTxt::Th( "Created On: %s" ), $signtime );
				if ( $isME && ( $item->status == 0 ) ) {
					$return		.=	' <span class="cbpbNew">' . CBTxt::Th("NEW") . '</span>';
				}
				if ( $item->editdate ) {
					$return		.=	'<br />' . sprintf( CBTxt::Th( "Edited By %s On: %s" ), htmlspecialchars( $item->editedbyname ), htmlspecialchars( $edittime ) );
				}
				$return			.=	'</div>';
				if ( $img ) {
					$return		.=	'<div class="cbpbRateInfo"> ' . $img . '</div>';
				}
				
				$return			.=	'</div>'
								.	'<div class="cbClr"> </div>';
				if ( $this->pbconfig->MainMode != 'blog' ) {
					$return		.=	'<div class="cbpbTopSep" style="width:100%">'
								.	'<hr class="cbpbSepMsg" />'
								.	'</div>';
				}


				//parse bbcode and display			
				$return			.=	'<div class="cbpbSnglMsg">'
								.	$this->parseBBCode( nl2br( htmlspecialchars( $item->postercomment ) ), $this->pbconfig->AllowBBCode, $this->pbconfig->AllowSmiles )
								.	'</div>'
								;

				//add warning if it's not the author who edited
				if ( $item->editdate && ( $item->posterid != $item->editedbyid || $item->postername != $item->editedbyname ) ) {
					$return		.=	CBTxt::T( '<br /><strong>[Notice: </strong><em>Last Edit by Site Moderator</em><strong>]</strong>' );
				}
				
				//check to see if the profile owner has left feedback and determine how to display
				if ( $item->feedback != '' ) {
					$return		.=	'<div class="small cbpbFeedback">'
								.		'<span class="titleCell">'
								.			sprintf( CBTxt::Th( "Feedback from %s: " ), getNameFormat( $user->name, $user->username, $ueConfig['name_format'] ) )
								.		'</span>'
								.		'<span class="fieldCell">'
								.			$this->parseBBCode( nl2br( htmlspecialchars( $item->feedback ) ), $this->pbconfig->AllowBBCode, $this->pbconfig->AllowSmiles )
								.		'</span>'
								.	'</div>';
				}
				
				$editorHtml		=	null;
				if ( $isME || $iAmModerator || ( $myId && $myId == $item->posterid ) ) {
					$return		.=	'<div class="cbpbControlArea">';

					if ( $iAmModerator || ( $myId && $myId == $item->posterid ) ) {
						$formName	=	'pbeditcomment' . $k;
						$return		.=	$this->_hiddenBBeditor( $editorHtml, $item, $formName, false, 0, $curruser, $required, ( $iAmModerator && ( $myId != $item->posterid ) ) ? CBTxt::T( "You are about to edit somebody else's text as a site Moderator. This will be clearly noted. Proceed ?" ) : null );
						if ( $iAmModerator ) {
							$return	.=	' | ';
						}
					}
					if ( $isME || $iAmModerator ) {
						
						//yes it is so display action links
						$base_url	=	$this->_getAbsURLwithParam( $pagingParams );
						
						$return		.=	'<form name="actionForm' . $k . '" id="actionForm' . $k . '" method="post" action="' . $base_url . '" style="display:none;">'
									.	'<input type="submit" name="submitform" value="submit" style="display:none;" />'
									.	'<input type="hidden" name="' . $this->_getPagingParamName( 'id' ) . '" value="' . $item->_pbid . '" />'
									.	'<input type="hidden" id="published' . $k . '" name="' . $this->_getPagingParamName( 'published' ) . '" value="1" />'
									.	'<input type="hidden" name="' . $this->_getPagingParamName( 'status' ) . '" id="status' . $k . '" value="1" />'
									.	'<input type="hidden" id="formaction' . $k . '" name="' . $this->_getPagingParamName( 'formaction' .  $this->pbconfig->MainMode[0] ) . '" value="update" />'
									.	'</form>'
									;
						if ( $item->published == 0 ) {
							$published		=	null;
							$publishLink	=	'<a href="javascript:document.actionForm' . $k . '.formaction' . $k . ".value='publish';document.actionForm" . $k . '.published' . $k . '.value=1;document.actionForm' . $k . '.submit();">' . CBTxt::Th( 'Publish' ) . '</a>';
						} else {
							$published		=	'checked="checked"';
							$publishLink	=	'<a href="javascript:document.actionForm' . $k . '.formaction' . $k . ".value='publish';document.actionForm" . $k . '.published' . $k . '.value=0;document.actionForm' . $k . '.submit();">' . CBTxt::Th( 'Un-Publish' ) . '</a>';
						}
	/*
						if ( $item->status == 0 ) {
							$statusLink		=	'<a href="javascript:document.actionForm' . $k . '.formaction' . $k . ".value='status';document.actionForm" . $k . '.status' . $k . '.value=1;document.actionForm' . $k . '.submit();">' . CBtxt::Th( 'Mark Read' ) . '</a>';
						} elseif ( $isME && ( $item->posterid != $myId ) ) {
							$statusLink		=	'<a href="javascript:document.actionForm' . $k . '.formaction' . $k . ".value='status';document.actionForm" . $k . '.status' . $k . '.value=0;document.actionForm' . $k . '.submit();">' . CBtxt::Th( 'Mark Unread' ) . '</a>';
						} else {
							$statusLink		=	null;
						}
	*/
						
						$return		.=	"<a href=\"javascript:if (confirm('" . addslashes( CBTxt::T( "Do you really want to delete permanently this Comment and associated User Rating ?" ) ) . "')) { document.actionForm" . $k . '.formaction' . $k . ".value='delete';document.actionForm" . $k . '.submit(); }">'
									.	CBTxt::Th( "Delete" ) . '</a> | '
									.	$publishLink
									//	.	( $statusLink ? ' | ' . $statusLink : '' )
									;
						if ( ( $isME && ( $item->posterid != $myId ) ) || ( $iAmModerator && $item->feedback ) ) {
							$popform	=	'<form name="adminForm' . $k . '" id="adminForm' . $k . '" method="post" action="' . $base_url . '">'
										.	'<b>' . CBTxt::Th( 'Publish' ) . ':</b><input type="checkbox" name="' . $this->_getPagingParamName( 'published' ) . '" value="1" ' . $published . ' />'
										.	'<input type="hidden" name="' . $this->_getPagingParamName( 'id' ) . '" value="' . $item->_pbid . '" /><input type="hidden" name="' . $this->_getPagingParamName( 'formaction' .  $this->pbconfig->MainMode[0] ) . '" value="update" />'
										.	'<br /><b>' . CBTxt::Th( 'Your Feedback' ) . ':</b><br /><textarea rows="5" cols ="40" class="inputbox" name="' . $this->_getPagingParamName( 'feedback' ) . '" style="height:75px;width:400px;overflow:auto;" >' . htmlspecialchars( $item->feedback ) . '</textarea>'
										.	'<br /><input class="button" type="submit" value="' . htmlspecialchars( CBTxt::T( 'Update' ) ) . '" /></form>'
										;
							$linkTitle	=	CBTxt::Th( $item->feedback ? "Edit Feedback" : "Give Feedback" );
							$showform	=	false;
							$warnText	=	( ( ! $isME ) ? CBTxt::T( "You are about to edit somebody else's text as a site Moderator. This will be clearly noted. Proceed ?" ) : '' );
							$return		.=	' | '
										.	'<a href="javascript:void(0);" class="cbpbToggleEditor' . ( $showform ? ' cbpbEditorShow' : '' ) . '" rel="cbpbdivEditFeedback' . $k . '" title="' . htmlspecialchars( $warnText ) . '">'  . $linkTitle . ' </a>';
							$editorHtml	.=	'<div class="cbpbEditorContainer" id="cbpbdivEditFeedback' . $k . '">' . $popform . '</div>';
						}
						$return			.=	$returnFavor;
					}
					$return				.=	'</div>';
					// hidden editors:
				}
				$return					.=	'</div>';	// class="cbpbSnglMsgAll"
				if ( $editorHtml ) {
					$return				.=	'<div class="cbpbEditorsArea">' . $editorHtml . '</div>';
				}
				if ( $this->pbconfig->MainMode == 'blog' ) {
					$return			.=	'<div class="cbpbTopSep">'
									.	'<hr class="cbpbSepMsg" />'
									.	'</div>';
					$return			.=	"\n\t" . '</div>';
				} else {
					$return			.=	'<br /><br /></td>';
					$return			.=	"\n\t</tr>";
				}
			}
			if ( $this->pbconfig->MainMode == 'blog' ) {
				$return				.=	"\n\t" . '</div>';
			} else {
				$return				.=	"\n</table>";
			}
			//display pagination
			if ( $this->pbconfig->PagingEnabled && ( $this->pbconfig->EntriesPerPage < $total ) ) {
				$return			.=	'<div style="width:95%;text-align:center;">'
								.	$this->_writePaging( $pagingParams, 'pbposts_', $this->pbconfig->EntriesPerPage, $total )
								.	'</div>';
			}
			$return				.=	'';
			$return				.=	"\n</div>";

			foreach ( $items as $item ) {
				if ( $isME && ( $item->status == 0 ) ) {
					$this->pbStatus( $item->_pbid, 1 );
				}
			}
		} else {
			//no posts so determine what to display
			$return				.=	'<br /><br /><div class="sectiontableheader" style="text-align:left;width:95%;">';
			$return				.=	CBTxt::Th( "This user currently doesn't have any posts." );
			$return				.=	'</div>';
		}
		
		return $return;
	}

	/**
	 * Hidden BBcode editor
	 *
	 * @uses $this->pbconfig->MainMode, $this->pbconfig->EnableRating, $this->pbconfig->ShowTitle, $this->pbconfig->ShowName, $this->pbconfig->ShowEmail, $this->pbconfig->UseLocation, $this->pbconfig->LocationField, $this->pbconfig->UseWebAddress, $this->pbconfig->WebField, $this->pbconfig->AllowBBCode, $this->pbconfig->AllowSmiles, $this->pbconfig->Captcha
	 *
	 * @param  string              $editorHtml
	 * @param  pbProfileBookEntry  $item
	 * @param  string              $formName
	 * @param  boolean             $isNew
	 * @param  boolean             $showform
	 * @param  UserTable           $curruser
	 * @param  boolean             $required
	 * @param  string              $warnText
	 * @return string
	 * 	 */
	function _hiddenBBeditor( &$editorHtml, $item, $formName, $isNew, $showform, $curruser, $required, $warnText )
	{
		static $jsSent	=	0;
		if ( ! $jsSent++ ) {
			$this->_getpbJS();
		}

		if ( $isNew ) {
			switch ( $this->pbconfig->MainMode ) {
				case 'blog':
					$linkTitle		=	CBTxt::Th( "Add new blog entry" );
					$txtSubmit		=	CBTxt::T( "Save Blog Entry" );
					$htmlAreaLabel	=	CBTxt::Th( "Blog text" );
					break;
	
				case 'wall':
					$linkTitle		=	CBTxt::Th( "Write on the wall" );
					$txtSubmit		=	CBTxt::T( "Submit Entry" );
					$htmlAreaLabel	=	CBTxt::Th( "Wall entry" );
					break;
	
				case 'guestbook':
				default:
					$linkTitle		=	CBTxt::Th( "Sign Profile Book" );
					$txtSubmit		=	CBTxt::T( "Submit Entry" );
					$htmlAreaLabel	=	CBTxt::Th( "Comments" );
					break;
			}
		} else {
			$linkTitle				=	CBTxt::Th( "Edit" );
			$txtSubmit				=	CBTxt::T( "Update Entry" );
			$htmlAreaLabel			=	CBTxt::Th( "Entry" );
		}

		$return						=	'<a href="javascript:void(0);" class="cbpbToggleEditor' . ( $showform ? ' cbpbEditorShow' : '' ) . '" rel="div' . $formName . '" title="' . htmlspecialchars( $warnText ) . '">'  . $linkTitle . ' </a>';
		$editorHtml					=	$this->_bbeditor( $item, $formName, $htmlAreaLabel, $txtSubmit, $curruser, $required );
		return $return;
	}

	function _getpbJS( )
	{
		global $_CB_framework;

		$this->_getpbCSS();
		$_CB_framework->addJQueryPlugin( 'cbprofilebook', '/components/com_comprofiler/plugin/user/plug_cbprofilebook/bb_adm.js', array( -1 => array( 'autosize' ) ) );
		$_CB_framework->outputCbJQuery( '', 'cbprofilebook' );
	}

	function _getpbCSS( )
	{
		cbpbCssOutput();
	}

	private function _displayWebAddress( $oValue )
	{
		if ( Application::Config()->get( 'allow_website' ) != 1 ) {
			return '';
		}

		$oReturn			=	explode( '|*|', $oValue );
		if ( count( $oReturn ) < 2 ) {
			$oReturn[1]		=	$oReturn[0];
		}

		return '<a href="http://' . htmlspecialchars( $oReturn[0] ) . '" target="_blank" rel="nofollow">' . htmlspecialchars( $oReturn[1] ) . '</a>';
	}

	/**
	 * BBcode editor
	 *
	 * @uses $this->pbconfig->EnableRating, $this->pbconfig->ShowTitle, $this->pbconfig->ShowName, $this->pbconfig->ShowEmail, $this->pbconfig->UseLocation, $this->pbconfig->LocationField, $this->pbconfig->UseWebAddress, $this->pbconfig->WebField, $this->pbconfig->AllowBBCode, $this->pbconfig->AllowSmiles, $this->pbconfig->Captcha
	 *
	 * @param  pbProfileBookEntry  $item
	 * @param  string              $idTag
	 * @param  string              $htmlAreaLabel
	 * @param  string              $txtSubmit
	 * @param  UserTable           $curruser
	 * @param  boolean             $required
	 * @return string
	 */
	function _bbeditor( $item, $idTag, $htmlAreaLabel, $txtSubmit, $curruser, $required )
	{
		global $_CB_framework, $ueConfig;

		$myId			=	Application::MyUser()->getUserId();

		$newOrMe		=	( ( $item->posterid == -1 ) || ( $item->posterid == $myId ) );

		$htmltext		=	'<div class="cbpbEditorContainer" id="div' . $idTag . '">';
		
		//get the CB initiatied form action path this is used for all forms
		$base_url		=	$this->_getAbsURLwithParam( array() );
		$htmltext		.=	'<form name="admin' . $idTag . '" id="admin' . $idTag . '" method="post" onsubmit="javascript: return pb_submitForm(this);" action="' . $base_url . "\">\n";
		$htmltext		.=	'<input type="hidden" name="' . $this->_getPagingParamName( 'formaction' .  $this->pbconfig->MainMode[0] ) . '" value="' . ( $item->_pbid ? 'edit' : 'new' ) . "\" />\n";
		if ( $item->_pbid ) {
			$htmltext	.=	'<input type="hidden" name="' . $this->_getPagingParamName( 'id' ) . '" value="' . $item->_pbid . "\" />\n";
		}
		if ( $this->pbconfig->AllowBBCode ) {
			$editor		=	$this->getEditor( $idTag );
		} else {
			$editor		=	null;
		}
		$htmltext		.=	"<table width=\"100%\">\n";
		$locationField	=	null;
		//Check to see if the Location field should be used
		if ( $this->pbconfig->UseLocation ) {
			//Check to see if a registered user is logged in and if the admin has defined a a value for the location field
			if ( $myId && ( $this->pbconfig->LocationField != 0 ) && $newOrMe ) {
				$locationField		=	new FieldTable();
				$locationField->load( $this->pbconfig->LocationField );
				$naLocationField	=	$locationField->name;
				//if they true then display the location value from the users cb profile in read only
				$locationField		=	'<td class="titleCell">' . CBTxt::th( "Location" ) . ':<br /><input type="hidden" name="' . $this->_getPagingParamName( 'posterlocation' ) . '" value="' . htmlspecialchars( $curruser->$naLocationField ) . '" />' . htmlspecialchars( $curruser->$naLocationField ) . '</td>';
			} else {
				//else display an entry field to capture the location
				$locationField		=	'<td class="titleCell">' . CBTxt::th( "Location" ) . ':<br /><input class="inputbox" type="text" name="' . $this->_getPagingParamName( 'posterlocation' ) . '" value="' . htmlspecialchars( $item->posterlocation ) . '" /></td>';
			}
		}
		
		$webField					=	null;
		if ( $this->pbconfig->UseWebAddress ) {
			if ( $myId && ( $this->pbconfig->WebField != 0 ) && $newOrMe  ) {
				$webfield			=	new FieldTable();
				$webfield->load( $this->pbconfig->WebField );
				$naWebField			=	$webfield->name;
				$webField			=	'<td class="titleCell">' . CBTxt::th( "Web Address" ) . ':<br /><input type="hidden" name="' . $this->_getPagingParamName( 'posterurl' ) . '" value="' . $curruser->$naWebField . '" />' . $this->_displayWebAddress( $curruser->$naWebField ) . '</td>';
			} else {
				$webField			=	'<td class="titleCell">' . CBTxt::th( "Web Address" ) . ':<br /><input class="inputbox" type="text" name="' . $this->_getPagingParamName( 'posterurl' ) . '" value="' . htmlspecialchars( $item->posterurl ) . '" /></td>';
			}
		}
		
		$htmltext				.=	"\n<tr>";
		if ( ! $myId ) {
			$htmltext			.=	'<td class="titleCell">' . CBTxt::th( "Name" )  . ':<br /><input class="inputbox" type="text" name="' . $this->_getPagingParamName( 'postername' ) . '" value="' . htmlspecialchars( $item->postername ) . '" /></td>';
			$htmltext			.=	'<td class="titleCell">' . CBTxt::th( "Email" ) . ':<br /><input class="inputbox" type="text" name="' . $this->_getPagingParamName( 'posteremail' ) . '" value="' . htmlspecialchars( $item->posteremail ) . '" /></td>';
		} else {
			$htmlName	=	( $item->postername ? htmlspecialchars( $item->postername ) : getNameFormat( $curruser->name, $curruser->username, $ueConfig['name_format'] ) );
			if ( $this->pbconfig->ShowName ) {
				$htmltext		.=	'<td class="titleCell">' . CBTxt::th( "Name" ) . ':<br /><input type="hidden" name="' . $this->_getPagingParamName( 'postername' ) . '" value="' . $htmlName . '" />' . $htmlName . '</td>';
			} else {
				$htmltext		.=	'<td><input type="hidden" name="' . $this->_getPagingParamName( 'postername' ) . '" value="' . $htmlName . '" /></td>';
			}
			if ( $this->pbconfig->ShowEmail ) {
				$htmltext		.=	'<td class="titleCell">' . CBTxt::th( "Email" ) . ':<br />';
				if ( ! $item->posteremail || $myId == $item->posterid || Application::MyUser()->isAuthorizedToPerformActionOnAsset( 'core.manage', 'com_users' ) ) {
					$htmltext	.=	'<input type="hidden" name="' . $this->_getPagingParamName( 'posteremail' ) . '" value="' . ( $item->posteremail ? htmlspecialchars( $item->posteremail ) : htmlspecialchars( $curruser->email ) ) . '" />' . ( $item->posteremail ? htmlspecialchars( $item->posteremail ) : htmlspecialchars( $curruser->email ) );
				} else {
					$htmltext	.=	CBTxt::th( "Hidden" );
				}
			} else {
				if ( ! $item->posteremail || $myId == $item->posterid || Application::MyUser()->isAuthorizedToPerformActionOnAsset( 'core.manage', 'com_users' ) ) {
					$htmltext	.=	'<td><input type="hidden" name="' . $this->_getPagingParamName( 'posteremail' ) . '" value="' . ( $item->posteremail ? htmlspecialchars( $item->posteremail ) : htmlspecialchars( $curruser->email ) ) . '" /></td>';
				}
			}
		}
		$htmltext				.=	'</tr>';
		
		//Check to see if we are displaying the web address or location field. If we are then add a row for them
		if ( $webField != null || $locationField != null ) {
			$htmltext		.=	"\n<tr>" . $locationField . $webField . '</tr>';
		}
		$htmltext			.=	'<tr><td colspan="2">';
		
		//Check to see if the admin has enabled rating for profile entries
		if ( $this->pbconfig->EnableRating && ( $myId != $item->userid ) ) {
			//Yep its enabled so get the ratings HTML/Code
			$htmltext		.=	'<div class="titleCell">' . CBTxt::Th( "User Rating" ) . ':</div>'
							.	'<div class="fieldCell">' . pbcbRatings::getRatingForm( $item->postervote, 'admin' . $idTag, $this->_getPagingParamName( 'postervote' ), ( $this->pbconfig->EnableRating == 3 ) ) . '</div>'
							;
		}

		// Title line:
		if ( $this->pbconfig->ShowTitle ) {
			$htmltext		.=	'<div class="pbTitleInput">'
							.	'<span class="titleCell">' . CBTxt::Th( "Title" ) . ':</span> '
							.	'<span class="fieldCell">'
							.	'<input class="form-control pbTitleBox" type="text" name="' . $this->_getPagingParamName( 'postertitle' ) . '" value="' . htmlspecialchars( $item->postertitle ) . '" maxlength="128" />'
							.	'</span>'
							.	'</div>'
							;
		}
		// Comment editor:
		$htmltext			.=	'<div class="pbCommentInput">'
							.	'<span class="titleCell">' . $htmlAreaLabel . ':</span>'
							.	'<span class="fieldCell">'
							.	$editor
							.	'<table class="cbpbEditorTexts"><tr>';
		if ( $this->pbconfig->AllowSmiles ) {
			$htmltext		.=	"<td width=\"73%\">\n";
		} else {
			$htmltext		.=	"<td width=\"100%\">\n";
		}
		$htmltext			.=	'<textarea class="inputbox cbpbEditor" name="' . $this->_getPagingParamName( 'postercomments' )
							.	'" rows="7" cols ="40" style="width: 95%; overflow:auto;" >'
							.	htmlspecialchars( $item->postercomment ) . "</textarea>\n</td>\n";
		if ( $this->pbconfig->AllowSmiles ) {
			$htmltext		.=	"<td>\n" . $this->getSmilies( $idTag ) . "</td>\n";
		}
		$htmltext			.=	"</tr>\n</table>\n"
							.	'</span>'
							.	'</div>'
							.	'</td></tr>';
		
		// Captcha integration:
		if ( ( $this->pbconfig->Captcha == 2 ) || ( ( $this->pbconfig->Captcha == 1 ) && ( $curruser === null ) ) ) {
			global $_PLUGINS;
			
			$_PLUGINS->loadPluginGroup( 'user' );
			$pluginsResults	=	$_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( true ) ); // onCheckCaptchaHtmlElements
			if ( implode( $pluginsResults ) != '' ) {
				$htmltext	.=	'<tr><td colspan="2">' . implode( '</td></tr><tr><td colspan="2">', $pluginsResults ) . '</td></tr>';
			}
		}
		
		$htmltext			.=	'<tr><td colspan="2"><span class="fieldCell"><input class="button" name="submitentry" type="submit" value="' . $txtSubmit . "\" /></span></td></tr>\n";
		$htmltext			.=	"</table>\n";
		$htmltext			.=	"</form>\n";
		$htmltext			.=	"</div>\n";
		
		//Add the localized Javascript parameters so that error messages are properly translated
		$validateArray		=	array();
		if ( $required ) {
			$validateArray[]	=	array( 'field' => 'postername', 'confirm' => null, 'error' => CBTxt::T( "Name is Required!" ) );
			$validateArray[]	=	array( 'field' => 'posteremail', 'confirm' => null, 'error' => CBTxt::T( "Email Address is Required!" ) );
		}
		if ( $myId != $item->userid ) {
			if ( $this->pbconfig->EnableRating == 3 ) {
				$validateArray[]	=	array( 'field' => 'postervote', 'confirm' => null, 'error' => CBTxt::T( "User Rating is Required!" ) );
			} elseif ( $this->pbconfig->EnableRating == 2 ) {
				$validateArray[]	=	array( 'field' => 'postervote', 'confirm' => CBTxt::T( "You have not selected a User Rating. Do you really want to provide an Entry without User Rating ?" ), 'error' => null );
			}
		}
		if ( $this->pbconfig->ShowTitle ) {
			$validateArray[]	=	array( 'field' => 'postertitle', 'confirm' => null, 'error' => CBTxt::T( "Title is Required!" ) );
		}
		$validateArray[]		=	array( 'field' => 'postercomments', 'confirm' => null, 'error' => CBTxt::T( "Comment is Required!" ) );
		
		$res				=	array();
		foreach ( $validateArray as $validateField ) {
			$res[]			=	"Array('" . addslashes( $this->_getPagingParamName( $validateField['field'] ) ) . "',"
							.	"'" . addslashes( $validateField['confirm'] ) . "',"
							.	"'" . addslashes( $validateField['error'] ) . "')";
		}
		$_CB_framework->document->addHeadScriptDeclaration(
			  'var _admin' . $idTag . '_validations = Array( ' . implode( ',', $res ) . ");\n"
			. 'var _admin' . $idTag . "_bbcodestack = Array();\n"
		);
		return $htmltext;
	}

	function getEditor( $idTag )
	{
		$editor =  '<table class="cbpbeditorButtons" border="0" cellspacing="0" cellpadding="0">';
		$editor .= '    <tr>';
		$editor .= '       <td style="padding: 2px 1px 2px 0;">';
		$editor .= '		<input type="button" class="button" accesskey="b" name="addbbcode0" value="B" style="font-weight:bold; width: 30px" onclick="bbstyle(this.form,0)" title="' . htmlspecialchars( CBTxt::T( "Bold text: [b]text[/b]" ) ) . '" />';
		$editor .= '       <input type="button" class="button" accesskey="i" name="addbbcode2" value="i" style="font-style:italic; width: 30px" onclick="bbstyle(this.form,2)" title="' . htmlspecialchars( CBTxt::T( "Italic text: [i]text[/i]" ) ) . '" />';
		$editor .= '       <input type="button" class="button" accesskey="u" name="addbbcode4" value="u" style="text-decoration: underline; width: 30px" onclick="bbstyle(this.form,4)" title="' . htmlspecialchars( CBTxt::T( "Underline text: [u]text[/u]" ) ) . '" />';
		$editor .= '       <input type="button" class="button" accesskey="q" name="addbbcode6" value="Quote" style="width: 55px" onclick="bbstyle(this.form,6)" title="' . htmlspecialchars( CBTxt::T( "Quoted text: [quote]text[/quote]" ) ) . '" />';
		$editor .= '       <input type="button" class="button" accesskey="k" name="addbbcode10" value="ul" style="width: 40px" onclick="bbstyle(this.form,10)" title="' . htmlspecialchars( CBTxt::T( "Unordered List: [ul] [li]text[/li] [/ul] - Hint: a list must contain List Items" ) ) . '" />';
		$editor .= '       <input type="button" class="button" accesskey="o" name="addbbcode12" value="ol" style="width: 40px" onclick="bbstyle(this.form,12)" title="' . htmlspecialchars( CBTxt::T( "Ordered List: [ol] [li]text[/li] [/ol] - Hint: a list must contain List Items" ) ) . '" />';
		
		$editor .= '       <input type="button" class="button" accesskey="l" name="addbbcode18" value="li" style="width: 40px" onclick="bbstyle(this.form,18)" title="' . htmlspecialchars( CBTxt::T( "List Item: [li] list item [/li] - Hint: a list item must be within a [ol] or [ul] List" ) ) . '" />';
		if ( $this->params->get( 'pbAllowImgBBCode', 0 ) == 1 ) {
			$editor .= '       <input type="button" class="button" accesskey="p" name="addbbcode14" value="Img" style="width: 40px"  onclick="bbstyle(this.form,14)" title="' . htmlspecialchars( CBTxt::T( "Image: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]" ) ) . '" />';
		}
		$editor .= '       <input type="button" class="button" accesskey="w" name="addbbcode16" value="URL" style="text-decoration: underline; width: 40px" onclick="bbstyle(this.form,16)" title="' . htmlspecialchars( CBTxt::T( "Link: [url=http://www.zzz.com/]This is a link[/url]" ) ) . '" />';
		$editor .= '      </td>';
		$editor .= '    </tr><tr>';
		$editor .= '      <td style="padding: 2px 1px 2px 0;">&nbsp;<span title="' . htmlspecialchars( CBTxt::T( "Color: [color=#FF6600]text[/color]" ) ) . '">' . CBTxt::Th( "Color" ) . ':</span>';
		$editor .= '         <select name="addbbcode240" onchange="bbfontstyle(document.admin' . $idTag . '.profilebookpostercomments,\'[color=\' + this.form.addbbcode240.options[this.form.addbbcode240.selectedIndex].value + \']\', \'[/color]\');this.selectedIndex=0;" title="' . htmlspecialchars( CBTxt::T( "Color: [color=#FF6600]text[/color]" ) ) . '" class="inputbox">';
		$editor .= '                     <option style="color:black;   background-color: #FAFAFA" value="">' . htmlspecialchars( CBTxt::T( "Standard" ) ) . '</option>';
		$editor .= '                     <option style="color:#FF0000; background-color: #FAFAFA" value="#FF0000">' . htmlspecialchars( CBTxt::T( "Red" ) ) . '</option>';
		
		$editor .= '                     <option style="color:#800080; background-color: #FAFAFA" value="#800080">' . htmlspecialchars( CBTxt::T( "Purple" ) ) . '</option>';
		$editor .= '                     <option style="color:#0000FF; background-color: #FAFAFA" value="#0000FF">' . htmlspecialchars( CBTxt::T( "Blue" ) ) . '</option>';
		$editor .= '                     <option style="color:#008000; background-color: #FAFAFA" value="#008000">' . htmlspecialchars( CBTxt::T( "Green" ) ) . '</option>';
		$editor .= '                    <option style="color:#FFFF00; background-color: #FAFAFA" value="#FFFF00">' . htmlspecialchars( CBTxt::T( "Yellow" ) ) . '</option>';
		$editor .= '                     <option style="color:#FF6600; background-color: #FAFAFA" value="#FF6600">' . htmlspecialchars( CBTxt::T( "Orange" ) ) . '</option>';
		$editor .= '                     <option style="color:#000080; background-color: #FAFAFA" value="#000080">' . htmlspecialchars( CBTxt::T( "Darkblue" ) ) . '</option>';
		
		$editor .= '                     <option style="color:#825900; background-color: #FAFAFA" value="#825900">' . htmlspecialchars( CBTxt::T( "Brown" ) ) . '</option>';
		$editor .= '                     <option style="color:#9A9C02; background-color: #FAFAFA" value="#9A9C02">' . htmlspecialchars( CBTxt::T( "Gold" ) ) . '</option>';
		$editor .= '                     <option style="color:#A7A7A7; background-color: #FAFAFA" value="#A7A7A7">' . htmlspecialchars( CBTxt::T( "Silver" ) ) . '</option>';
		$editor .= '         </select> &nbsp;<span title="' . htmlspecialchars( CBTxt::T( "Size: [size=1]text size[/size] - Hint: sizes range from 1 to 5" ) ) . '">' . CBTxt::Th( "Size" ) . ':</span><select name="addbbcode242" onchange="bbfontstyle(document.admin' . $idTag . '.profilebookpostercomments,\'[size=\' + this.form.addbbcode242.options[this.form.addbbcode242.selectedIndex].value + \']\', \'[/size]\');this.selectedIndex=2;" title="' . htmlspecialchars( CBTxt::T( "Size: [size=1]text size[/size] - Hint: sizes range from 1 to 5" ) ) . '" class="inputbox">';
		$editor .= '                     <option value="1">' . htmlspecialchars( CBTxt::T( "Very Small" ) ) . '</option>';
		$editor .= '                     <option value="2">' . htmlspecialchars( CBTxt::T( "Small" ) ) . '</option>';
		$editor .= '                     <option value="3" selected="selected">' . htmlspecialchars( CBTxt::T( "Normal" ) ) . '</option>';
		$editor .= '                     <option value="4">' . htmlspecialchars( CBTxt::T( "Big" ) ) . '</option>';
		$editor .= '                     <option value="5">' . htmlspecialchars( CBTxt::T( "Very Big" ) ) . '</option>';
		if ( $this->params->get( 'pbAllowVideoBBCode', 0 ) == 1 ) {
			$editor .= '         </select> &nbsp;<span title="' . htmlspecialchars( CBTxt::T( "Video: [video type=youtube]id[/video] - Hint: id is only the embedding id of the video" ) ) . '">' . CBTxt::Th( "Video" ) . ':</span><select name="addbbcode244" onchange="bbfontstyle(document.admin' . $idTag . '.profilebookpostercomments,\'[video type=\' + this.form.addbbcode244.options[this.form.addbbcode244.selectedIndex].value + \']\', \'[/video]\');this.selectedIndex=0;" title="' . htmlspecialchars( CBTxt::T( "Video: [video type=youtube]id[/video] - Hint: id is only the embedding id of the video" ) ) . '" class="inputbox">';
			$editor .= '                     <option value="" selected="selected">- ' . htmlspecialchars( CBTxt::T( "Video" ) ) . ' -</option>';
			$editor .= '                     <option value="youtube">' . htmlspecialchars( CBTxt::T( "Youtube" ) ) . '</option>';
			$editor .= '                     <option value="gvideo">' . htmlspecialchars( CBTxt::T( "GoogleVideo" ) ) . '</option>';
			$editor .= '                     <option value="veoh">' . htmlspecialchars( CBTxt::T( "Veoh" ) ) . '</option>';
			$editor .= '                     <option value="vimeo">' . htmlspecialchars( CBTxt::T( "Vimeo" ) ) . '</option>';
//ko		$editor .= '                     <option value="myspace">' . htmlspecialchars( CBTxt::T( "MySpace" ) ) . '</option>';
		}
		$editor .= '         </select>';
		$editor .= '                  &nbsp;&nbsp;<a href="javascript:bbstyle(document.admin' . $idTag . ',-1)" title="' . htmlspecialchars( CBTxt::T( "Close all open bbCode tags" ) ) . '"><small>' . CBTxt::Th( "Close All Tags" ) . '</small></a>';
		$editor .= '		</td>';
		$editor .= '    </tr>';
		
		//$editor .='    <tr>';
		//$editor .='       <td style="padding: 2px 1px 2px 0px;"><input type="text" name="helpbox"id="helpbox" size="45" class="inputbox" maxlength="100" style="width: 450px; font-size:9px; border: 0px" value="bbCode Help - Hint: bbCode can be used on selected text!" /></td>';
		//$editor .='    </tr>';
		$editor .= ' </table>';

		cbpbJqEditorOutput();
		return $editor;
	}
	
	function smiliesArray( )
	{
				$smilies	=	array(
					 ':)'=>'smile.png'
					,';)'=>'wink.png'
					,'B)'=>'cool.png'
					,'8)'=>'cool.png'
					,':lol:'=>'grin.png'
					,':laugh:'=>'laughing.png'
					,':cheer:'=>'cheerful.png'
					,':kiss:'=>'kissing.png'
					,':silly:'=>'silly.png'
					,':ohmy:'=>'shocked.png'
					,':woohoo:'=>'w00t.png'
					,':whistle:'=>'whistling.png'
					,':('=>'sad.png'
					,':angry:'=>'angry.png'
					,':blink:'=>'blink.png'
					,':sick:'=>'sick.png'
					,':unsure:'=>'unsure.png'
					,':dry:'=>'ermm.png'
					,':huh:'=>'wassat.png'
					,':pinch:'=>'pinch.png'
					,':side:'=>'sideways.png'
					,':evil:'=>'devil.png'
					,':blush:'=>'blush.png'
					,':-)'=>'smile.png'
					,':-('=>'sad.png'
					,';-)'=>'wink.png'
					,':S'=>'dizzy.png'
					,':s'=>'dizzy.png'
					,':P'=>'tongue.png'
					,':p'=>'tongue.png'
					,':D'=>'laughing.png'
					,':X'=>'sick.png'
					,':x'=>'sick.png');
					return $smilies;
	}

	function getSmilies( $idTag )
	{
		global $_CB_framework;
		
		$params					=	$this->params;
		$this->pbconfig->AllowSmiles			=	$params->get( 'pbAllowSmiles', '1' ); //Determine if Smilies are allowed
		if ( ! $this->pbconfig->AllowSmiles ) {
			return null;
		}
		$smilies				=	$this->smiliesArray();
		$return					=	null;
		$outputed				=	array();
		foreach ( $smilies as $code => $location ) {
			if ( ! in_array( $location, $outputed ) ) {
				$return			.=	'<img onclick="javascript:pb_emo(document.admin' . $idTag . '.profilebookpostercomments,\'' . $code . '\');" style="cursor:pointer" class="btnImage" src="' . $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/plugin/user/plug_cbprofilebook/smilies/' . $location . '" alt="' . $code . '" title="' . $code . '" /> ';
				$outputed[]		=	$location;
			}
		}
		return $return;
	}
	
	function parseSmilies( $text )
	{
		global $_CB_framework;
		
		$smilies				=	$this->smiliesArray();
		foreach ( $smilies as $code => $location ) {
			$text				=	str_replace( $code, '<img src="' . $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/plugin/user/plug_cbprofilebook/smilies/' . $location . '" alt="" style="vertical-align: middle;border:0px;" />', $text );
		}
		return $text;
	}

	/**
	 * Parses BBcode
	 *
	 * @uses $this->pbconfig->AllowBBCode, $this->pbconfig->AllowSmiles
	 *
	 * @param  string  $text
	 * @return string
	 */
	function parseBBCode( $text )
	{
		global $_CB_framework;
		
		if ( $this->pbconfig->AllowSmiles ) {
			$text				=	$this->parseSmilies( $text );
		}

		if ( $this->pbconfig->AllowBBCode ) {
			/** @noinspection PhpIncludeInspection */
			require_once ( $_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbprofilebook/classes/bbcode.inc.php' );
			$bbcode				=	new profilebook_bbcode( );
			$bbcode->add_tag( array( 'Name' => 'quote', 'HtmlBegin' => '<div style="padding:5px;border:solid 1px #000000;background-color:#e6e6e6;color:#000000;font-family: Arial, Verdana, sans-serif;font-size: 9px;display: block;">', 'HtmlEnd' => '</div>' ) );
			$bbcode->add_tag( array( 'Name' => 'b', 'HtmlBegin' => '<span style="font-weight: bold;">', 'HtmlEnd' => '</span>' ) );
			$bbcode->add_tag( array( 'Name' => 'ul', 'HtmlBegin' => '<ul>', 'HtmlEnd' => '</ul>' ) );
			$bbcode->add_tag( array( 'Name' => 'ol', 'HtmlBegin' => '<ol type="1">', 'HtmlEnd' => '</ol>' ) );
			$bbcode->add_tag( array( 'Name' => 'li', 'HtmlBegin' => '<li>', 'HtmlEnd' => '</li>' ) );
			$bbcode->add_tag( array( 'Name' => 'i', 'HtmlBegin' => '<span style="font-style: italic;">', 'HtmlEnd' => '</span>' ) );
			$bbcode->add_tag( array( 'Name' => 'u', 'HtmlBegin' => '<span style="text-decoration: underline;">', 'HtmlEnd' => '</span>' ) );
			$bbcode->add_tag( array( 'Name' => 'link',
									 'HasParam' => true,
									 'HtmlBegin' => '<a href="%%P%%" target="_blank" rel="nofollow">',
									 'HtmlEnd' => '</a>' ) );

			if ( $this->params->get( 'pbAllowImgBBCode', 0 ) == 1 ) {
				// $bbcode->add_tag(array('Name'=>'img','HasParam'=>true,'HtmlBegin'=>'<img src="%%P%%" size="%%P%%" alt="" />','HasEnd'=>false));

				$bbcode->add_tag( array( 'Name' => 'img',
										 'HtmlBegin' => '<img src="%%P%%" alt="" />',
										 'HtmlEnd' => '',
										 'ReplaceContent' => true ) );
			}

			if ( $this->params->get( 'pbAllowVideoBBCode', 0 ) == 1 ) {
				$bbcode->add_tag( array( 'Name' => 'video type=youtube',
										 'HtmlBegin' => '<span class="cbpbYoutubePlayer"><object type="application/x-shockwave-flash" style="width:400px;height:325px;" data="http://www.youtube.com/v/%%P%%"><param name="movie" value="http://www.youtube.com/v/%%P%%" /></object></span>',
										 'HtmlEnd' => '',
										 'ReplaceContent' => true ) );

				$bbcode->add_tag( array( 'Name' => 'video type=gvideo',
										 'HtmlBegin' => '<span class="cbpbGvideoPlayer"><object type="application/x-shockwave-flash" style="width:400px;height:325px;" data="http://video.google.com/googleplayer.swf?docId=%%P%%&amp;hl=en"><param name="movie" value="http://video.google.com/googleplayer.swf?docId=%%P%%&amp;hl=en"/></object></span>', 'HtmlEnd' => '', 'ReplaceContent' => true ) );

				$bbcode->add_tag( array( 'Name' => 'video type=veoh', 'HtmlBegin' => '<span class="cbpbVeohPlayer"><object type="application/x-shockwave-flash" style="width:400px;height:325px;" data="http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&type=v&permalinkId=%%P%%"><param name="movie" value="http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&type=v&permalinkId=%%P%%" /></object></span>',
										 'HtmlEnd' => '',
										 'ReplaceContent' => true ) );

				$bbcode->add_tag( array( 'Name' => 'video type=vimeo',
										 'HtmlBegin' => '<span class="cbpbViemoPlayer"><object type="application/x-shockwave-flash" style="width:400px;height:325px;" data="http://www.vimeo.com/moogaloop.swf?clip_id=%%P%%&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1"><param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=%%P%%&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" /><param name="allowfullscreen" value="true" /><param name="scale" value="showAll" /></object></span>',
										 'HtmlEnd' => '',
										 'ReplaceContent' => true ) );
//ko			$bbcode->add_tag( array( 'Name' => 'video type=myspace', 'HtmlBegin' => '<span class="cbpbMyspacePlayer"><object type="application/x-shockwave-flash" style="width:400px;height:325px;" data="http://mediaservices.myspace.com/services/media/embed.aspx/m=%%P%%,t=1,mt=video"><param name="movie" value="http://mediaservices.myspace.com/services/media/embed.aspx/m=%%P%%,t=1,mt=video" /><param name="wmode" value="transparent" /><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /></object></span>', 'HtmlEnd' => '', 'ReplaceContent' => true ) );
			}

			$bbcode->add_tag( array( 'Name' => 'color', 'HasParam' => true, 'ParamRegex' => '[A-Za-z0-9#]+', 'HtmlBegin' => '<span style="color: %%P%%;">', 'HtmlEnd' => '</span>', 'ParamRegexReplace' => array( '/^[A-Fa-f0-9]{6}$/' => '#$0' ) ) );

			$bbcode->add_tag( array( 'Name' => 'email',
									 'HtmlBegin' => '<a href="mailto:%%P%%">%%P%%',
									 'HtmlEnd' => '</a>',
									 'ReplaceContent' => true,
									 'ParamRegexReplace' => array( _UE_PREGMATCH_VALID_EMAIL => '$0' ) ) );

			//		$bbcode->add_tag(array('Name'=>'size','HasParam'=>true,'HtmlBegin'=>'<span style="font-size:%%P%%pt;">','HtmlEnd'=>'</span>','ParamRegex'=>'[0-9]+'));

			$bbcode->add_tag( array( 'Name' => 'size',
									 'HasParam' => true,
									 'HtmlBegin' => '<span style="font-size:%%P%%%;">',
									 'HtmlEnd' => '</span>',
									 'ParamRegexReplace' => array( '/^1$/' => '80', '/^2$/' => '90', '/^3$/' => '100', '/^4$/' => '125', '/^5$/' => '200' ) ) );

			$bbcode->add_tag( array( 'Name' => 'align', 'HtmlBegin' => '<div style="text-align: %%P%%">', 'HtmlEnd' => '</div>', 'HasParam' => true, 'ParamRegex' => '(center|right|left)' ) );
			$bbcode->add_alias( 'url', 'link' );
			$text				=	$bbcode->parse_bbcode( $text );
		}

		return $text;
	}

	/**
	 * Get the user's config for that tab
	 *
	 * @param  int $id
	 * @return stdClass
	 */
	function pbGetUser( $id )
	{
		$cbUser			=	CBuser::getInstance( (int) $id );
		$user			=	$cbUser->getUserData();
		return $user;
	}

	/**
	 * Get the tab related parameters, these settings are global and set by administrator
	 *
	 */
	function getPbConfig( )
	{
		$pb						=	new stdClass();
		$params					=&	$this->params;
		$pb->MainMode			=	'guestbook';	//$params->get( 'pbMainMode', 'guestbook' ); //Determine whether it's a guestbook, blog or wall
		$pb->AllowAnony			=	$params->get( 'pbAllowAnony', '0' ); //Determine whether Anonymous Users can post
		$pb->EnableRating		=	$params->get( 'pbEnableRating', '1' ); //Determine if Profile Ratings should be used
		$pb->EntriesPerPage		=	$params->get( 'pbEntriesPerPage', '10' ); //Determine number of posts to show per page
		$pb->PagingEnabled		=	$params->get( 'pbPagingEnabled', 1 ); //Determine if Pagination is enabled
		$pb->SortDirection		=	$params->get( 'pbSortDirection', 'DESC' ); //Determine sort order of posting date
		$pb->ShowTitle			=	$params->get( 'pbShowTitle', '0' ); //Determine whether to show titles in editor
		$pb->ShowName			=	$params->get( 'pbShowName', '1' ); //Determine whether to show name of logged-in users
		$pb->ShowEmail			=	$params->get( 'pbShowEmail', '0' ); //Determine whether to show name of logged-in users
		$pb->UseLocation		=	$params->get( 'pbUseLocation', '1' ); //Determine whether to use Location Field
		$pb->LocationField		=	$params->get( 'pbLocationField', '0' ); //Determine whether what field is the location field
		$pb->UseWebAddress		=	$params->get( 'pbUseWebAddress', '1' ); //Determine whether to use Web Address Field
		$pb->WebField			=	$params->get( 'pbWebField', '0' ); //Determine whether what field is the web address field
		$pb->EnableGesture		=	$params->get( 'pbEnableGesture', '0' ); //Determine whether return gestures are enabled
		$pb->AllowBBCode		=	$params->get( 'pbAllowBBCode', '1' ); //Determine if BBCode is allowed
		$pb->AllowSmiles		=	$params->get( 'pbAllowSmiles', '1' ); //Determine if Smiles are allowed
		$pb->Captcha			=	$params->get( 'pbCaptcha', '1' ); //Determine if Captcha is enabled
		$pb->ShowEditor			=	$params->get( 'pbShowEditor', '0' ); //Determine if editor should be shown by default
		$pb->EntryShowName		=	$params->get( 'pbEntryShowName', '1' ); //Determine whether to show entry name of poster
		$pb->EntryShowEmail		=	$params->get( 'pbEntryShowEmail', '1' ); //Determine whether to show entry email of poster
		$pb->EntryShowIP		=	$params->get( 'pbEntryShowIP', '1' ); //Determine whether to show entry ip address of poster
		$pb->EntryShowAvatar	=	$params->get( 'pbEntryShowAvatar', '1' ); //Determine whether to show entry avatar of poster

		$this->pbconfig			=	$pb;
	}

	/**
	 * Gets user param
	 *
	 * @param  int     $userId
	 * @param  string  $paramName  ( 'enable', 'autopublish', 'notifyme')
	 * @return boolean 
	 */
	function getUserParam( $userId, $paramName )
	{
		$user					=	$this->pbGetUser( $userId );
		switch ($paramName) {
			case 'enable':
				return ( strtolower( $user->cb_pb_enable ) != '_ue_no' );
				break;
			case 'autopublish':
				return ( strtolower( $user->cb_pb_autopublish ) != '_ue_no' );
				break;
			case 'notifyme':
				return ( strtolower( $user->cb_pb_notifyme ) != '_ue_no' );
				break;
			
			default:
				trigger_error('getUserParam unknown', E_USER_WARNING );
				return false;
				break;
		}
	}
} // end class getprofilebookTab

class getprofilebookblogTab extends getprofilebookTab
{
	/**
	 * Get the tab related parameters, these settings are global and set by administrator
	 *
	 */
	function getPbConfig( )
	{
		$pb						=	new stdClass();
		$params					=&	$this->params;
		$pb->MainMode			=	'blog';		//$params->get( 'pbMainMode', 'blog' ); //Determine whether it's a guestbook, blog or wall
		$pb->AllowAnony			=	'0';		//$params->get( 'pbAllowAnony', '0' ); //Determine whether Anonymous Users can post
		$pb->EnableRating		=	'0';		//$params->get( 'pbEnableRating', '0' ); //Determine if Profile Ratings should be used
		$pb->EntriesPerPage		=	$params->get( 'pbEntriesPerPage', '5' ); //Determine number of posts to show per page
		$pb->PagingEnabled		=	$params->get( 'pbPagingEnabled', 1 ); //Determine if Pagination is enabled
		$pb->SortDirection		=	$params->get( 'pbSortDirection', 'DESC' ); //Determine sort order of posting date
		$pb->ShowTitle			=	$params->get( 'pbShowTitle', '1' ); //Determine whether to show titles in editor
		$pb->ShowName			=	'0';		//$params->get( 'pbShowName', '0' ); //Determine whether to show name of logged-in users
		$pb->ShowEmail			=	'0';		//$params->get( 'pbShowEmail', '0' ); //Determine whether to show name of logged-in users
		$pb->UseLocation		=	'0';		//$params->get( 'pbUseLocation', '0' ); //Determine whether to use Location Field
		$pb->LocationField		=	'0';		//$params->get( 'pbLocationField', '0' ); //Determine whether what field is the location field
		$pb->UseWebAddress		=	'0';		//$params->get( 'pbUseWebAddress', '0' ); //Determine whether to use Web Address Field
		$pb->WebField			=	'0';		//$params->get( 'pbWebField', '0' ); //Determine whether what field is the web address field
		$pb->EnableGesture		=	'0';		//$params->get( 'pbEnableGesture', '0' ); //Determine whether return gestures are enabled
		$pb->AllowBBCode		=	$params->get( 'pbAllowBBCode', '1' ); //Determine if BBCode is allowed
		$pb->AllowSmiles		=	$params->get( 'pbAllowSmiles', '1' ); //Determine if Smiles are allowed
		$pb->Captcha			=	'0';		//$params->get( 'pbCaptcha', '1' ); //Determine if Captcha is enabled
		$pb->ShowEditor			=	$params->get( 'pbShowEditor', '0' ); //Determine if editor should be shown by default
		$pb->EntryShowName		=	$params->get( 'pbEntryShowName', '1' ); //Determine whether to show entry name of poster
		$pb->EntryShowEmail		=	$params->get( 'pbEntryShowEmail', '1' ); //Determine whether to show entry email of poster
		$pb->EntryShowIP		=	$params->get( 'pbEntryShowIP', '1' ); //Determine whether to show entry ip address of poster
		$pb->EntryShowAvatar	=	$params->get( 'pbEntryShowAvatar', '1' ); //Determine whether to show entry avatar of poster

		$this->pbconfig			=	$pb;
	}

	/**
	 * Gets user param
	 *
	 * @param  int     $userId
	 * @param  string  $paramName  ( 'enable', 'autopublish', 'notifyme')
	 * @return boolean 
	 */
	function getUserParam( $userId, $paramName )
	{
		switch ($paramName) {
			case 'enable':
				$user			=	$this->pbGetUser( $userId );
				return ( strtolower( $user->cb_pb_enable_blog ) != '_ue_no' );
				break;
			case 'autopublish':
				return true;
				break;
			case 'notifyme':
				return false;
				break;
			
			default:
				trigger_error('getUserParam unknown', E_USER_WARNING );
				return false;
				break;
		}
	}
}

class getprofilebookwallTab extends getprofilebookTab
{
	/**
	 * Get the tab related parameters, these settings are global and set by administrator
	 *
	 */
	function getPbConfig( )
	{
		$pb						=	new stdClass();
		$params					=&	$this->params;
		$pb->MainMode			=	'wall';		//$params->get( 'pbMainMode', 'wall' ); //Determine whether it's a guestbook, blog or wall
		$pb->AllowAnony			=	$params->get( 'pbAllowAnony', '0' ); //Determine whether Anonymous Users can post
		$pb->EnableRating		=	$params->get( 'pbEnableRating', '1' ); //Determine if Profile Ratings should be used
		$pb->EntriesPerPage		=	$params->get( 'pbEntriesPerPage', '10' ); //Determine number of posts to show per page
		$pb->PagingEnabled		=	$params->get( 'pbPagingEnabled', 1 ); //Determine if Pagination is enabled
		$pb->SortDirection		=	$params->get( 'pbSortDirection', 'DESC' ); //Determine sort order of posting date
		$pb->ShowTitle			=	$params->get( 'pbShowTitle', '0' ); //Determine whether to show titles in editor
		$pb->ShowName			=	$params->get( 'pbShowName', '0' ); //Determine whether to show name of logged-in users
		$pb->ShowEmail			=	$params->get( 'pbShowEmail', '0' ); //Determine whether to show name of logged-in users
		$pb->UseLocation		=	$params->get( 'pbUseLocation', '0' ); //Determine whether to use Location Field
		$pb->LocationField		=	$params->get( 'pbLocationField', '0' ); //Determine whether what field is the location field
		$pb->UseWebAddress		=	$params->get( 'pbUseWebAddress', '0' ); //Determine whether to use Web Address Field
		$pb->WebField			=	$params->get( 'pbWebField', '0' ); //Determine whether what field is the web address field
		$pb->EnableGesture		=	$params->get( 'pbEnableGesture', '0' ); //Determine whether return gestures are enabled
		$pb->AllowBBCode		=	$params->get( 'pbAllowBBCode', '1' ); //Determine if BBCode is allowed
		$pb->AllowSmiles		=	$params->get( 'pbAllowSmiles', '1' ); //Determine if Smiles are allowed
		$pb->Captcha			=	$params->get( 'pbCaptcha', '1' ); //Determine if Captcha is enabled
		$pb->ShowEditor			=	$params->get( 'pbShowEditor', '0' ); //Determine if editor should be shown by default
		$pb->EntryShowName		=	$params->get( 'pbEntryShowName', '1' ); //Determine whether to show entry name of poster
		$pb->EntryShowEmail		=	$params->get( 'pbEntryShowEmail', '1' ); //Determine whether to show entry email of poster
		$pb->EntryShowIP		=	$params->get( 'pbEntryShowIP', '1' ); //Determine whether to show entry ip address of poster
		$pb->EntryShowAvatar	=	$params->get( 'pbEntryShowAvatar', '1' ); //Determine whether to show entry avatar of poster

		$this->pbconfig			=	$pb;
	}

	/**
	 * Gets user param
	 *
	 * @param  int     $userId
	 * @param  string  $paramName  ( 'enable', 'autopublish', 'notifyme')
	 * @return boolean 
	 */
	function getUserParam( $userId, $paramName )
	{
		$user					=	$this->pbGetUser( $userId );
		switch ($paramName) {
			case 'enable':
				return ( strtolower( $user->cb_pb_enable_wall ) != '_ue_no' );
				break;
			case 'autopublish':
				return ( strtolower( $user->cb_pb_autopublish_wall ) != '_ue_no' );
				break;
			case 'notifyme':
				return ( strtolower( $user->cb_pb_notifyme_wall ) != '_ue_no' );
				break;
			
			default:
				trigger_error('getUserParam unknown', E_USER_WARNING );
				return false;
				break;
		}
	}
}

class pbcbRatings
{
	/**
	 * Returns HTML form for rating with javascript-assisted radio-buttons
	 *
	 * @param  int      $vote         1 .. $starsNumber
	 * @param  string   $formId       HTML id of form
	 * @param  string   $inputName    Name of the <input type="radio" /> tags (value is 1 .. $starsNumber)
	 * @param  boolean  $mandatory    If form is mandatory, a star will be displayed
	 * @param  int      $starsNumber  Total umber of stars (default: 5)
	 * @return string
	 */
	static function getRatingForm( $vote, $formId, $inputName, $mandatory = false, $starsNumber = 5 )
	{
		global $_CB_framework;
		
		$chk		=	' checked="checked"';

		$html		=	'<div class="fieldCell content_vote">'
					.	'<div style="float:left;">'
					.	CBTxt::Th("Poor")
					.	' </div>'
					.	'<div class="stars" style="float:left;">'
					;
		for ( $i = 1 ; $i <= $starsNumber ; $i++ ) {
			$html	.=	'<input type="radio" class="star" alt="' . htmlspecialchars( sprintf( ( $i == 1 ? CBTxt::T("Vote %s star") : CBTxt::T("Vote %s stars") ), $i ) ) . '" name="' . $inputName . '"' . ( $vote == $i ? $chk : '' ) . ' value="' . $i . '" />';
		}
		$html		.=	'</div>'
					.	'<div style="float:left;"> '
					.	CBTxt::Th("Best")
					.	'</div>'
					.	'<div style="clear:both;"></div>'
					.	'</div>'
					;
		$_CB_framework->outputCbJQuery( "$('form#" . $formId . " input[type=radio].star').rating({" . ( $mandatory ? "required:true, " : '' ) . "cancel: '" . addslashes( CBTxt::T("Cancel Rating") ) . "'});", 'rating' );
		return $html;
	}

	/**
	 * Returns HTML to represent the rating
	 *
	 * @param  int|null  $rating           1 ... $starsNumber OR null
	 * @param  boolean   $alwaysShowStars  Show always stars, also if no rating has been made ($rating === null)
	 * @param  boolean   $alignRight       TRUE: Align right, FALSE: left
	 * @param  int       $starsNumber      Total umber of stars (default: 5)
	 * @return string
	 */
	static function _getRatingImage( $rating, $alwaysShowStars = false, $alignRight = false, $starsNumber = 5 )
	{
		if ( ! $alwaysShowStars && ( $rating === null ) ) {
			return "";
		}

		$starImageOn	=	'<span class="cbStarRatingOn">&nbsp;</span>';
		$starImageOff	=	'<span class="cbStarRatingOff">&nbsp;</span>';

		$img			=	'<div class="cbStarRatingBlock' . ( $alignRight ? 'R' : '' ) . '">'
						.	str_repeat( $starImageOn,  $rating )
						.	str_repeat( $starImageOff, $starsNumber - $rating )
						.	'<div class="cbClr"> </div></div>'
						;
		return $img;
	}

	/**
	 * Validates entry.
	 *
	 * @param  int      $input
	 * @param  int      $starsNumber
	 * @return boolean
	 */
	static function validateRating( $input, $starsNumber = 5 )
	{
		return ( ( $input >= 1 ) && ( $input <= $starsNumber ) && ( $input == (int) $input ) );
	}

	/**
	 * Utility function to include the language file
	 *
	 */
	static function _getLanguageFile( )
	{
		static $notincluded		=	true;

		if ( $notincluded ) {
			cbimport( 'language.cbteamplugins' );
			$notincluded		=	false;
		}
	}
}	//end class pbcbRatings

function cbpbJqEditorOutput( )
{
	global $_CB_framework;

	static $outputed = 0;
	if ( ! $outputed++ ) {
		$_CB_framework->outputCbJQuery( "$('table.cbpbeditorButtons input[title],table.cbpbeditorButtons select[title]').each( function() { $(this).attr('title', $(this).attr('title').replace(/^([^:]*):(.*)$/, '\$1|\$2') ); } );" );
		$_CB_framework->outputCbJQuery( "$('table.cbpbeditorButtons input[title]').cluetip( { splitTitle: '|', arrows: true, cursor: '', width: 400, dropShadow: false, cluetipClass: 'jtip', fx: { open: 'fadeIn', openSpeed: 'fast' } /*, positionBy: 'bottomTop' */ });", 'cluetip' );
		$_CB_framework->outputCbJQuery( "$('table.cbpbeditorButtons select[title]').cluetip({ activation: 'focus', splitTitle: '|', arrows: true, cursor: '', width: 400, dropShadow: false, cluetipClass: 'jtip', fx: { open: 'fadeIn', openSpeed: 'fast' } });", 'cluetip' );
	}
}

function cbpbCssOutput( )
{
	global $_CB_framework;

	static $outputed = 0;
	if ( ! $outputed++ ) {

		//TBD: add those to CB templates: this is not in a separate file, because we want to move them to CB template:
		$css	=	'.cbpbToggleEditor { padding-right: 14px; margin-bottom: 10px; }
.cbpbEditorHidden { background: url(' . $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/plugin/user/plug_cbprofilebook/smilies/none-arrow.gif' . ') no-repeat right; }
.cbpbEditorVisible { background: url(' . $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/plugin/user/plug_cbprofilebook/smilies/block-arrow.gif' . ') no-repeat right; }
.cbpbMainArea { margin-top: 16px; }
.cbpbBlog .cbpbSnglMsgAll, .cbpbBlog .cbpbTopSep { width:65%; margin: auto; }
.cbpbBlog .cbpbEditorsArea { width:auto; margin: 0px 0px 0px 17.5%; }
.pbTitle { font-size: 140%; }
.cbpbNew { background-color: #ffcc66; color: #332200; padding: 3px 3px 1px; margin-left: 5px; }
hr.cbpbSepMsg { border: 0px; width:100%; height: 2px; background-color: #ddd; }
.cbpbDateInfo { float:left; width:70%; }
.cbpbBlog .cbpbDateInfo { margin-bottom: 10px; }
.cbpbRateInfo { float:right; width:25%;text-align:right; }'
				// Editing:
. '
.cbpbEditorContainer { border: 2px solid #ccc; background-color:#f4f4f4; margin: 0px 0px 20px; padding: 10px; display: block; width: auto; }
div.pbTitleInput { margin: 12px 0px; }
input.pbTitleBox { font-size:130%; font-weight:bold; }
.pbTitleInput .fieldCell, .pbCommentInput .fieldCell { display: block; }
.pbCommentInput .fieldCell td { padding: 0px; vertical-align: middle; }
.cbpbeditorButtons select.inputbox { vertical-align: baseline; }
.cbpbEditorTexts { padding: 0px; border: 0px; margin: 0px; border-collapse: collapse; }'
				;
		$_CB_framework->document->addHeadStyleInline( $css );
	}
}
