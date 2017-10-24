<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C)2005-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

/** ensure this file is being included by a parent file **/
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Database\Table\OrderedTable;

global $_PLUGINS;
if ( isset( $_PLUGINS ) ) {
    // onAfterUserUpdate triggered function is used to log frontend profile updating actions
    $_PLUGINS->registerFunction( 'onAfterUserUpdate','pul_getChanges','getcbpuloggerTab' );
    // onAfterUpdateUser triggered function is used to log backend profile updating actions
    $_PLUGINS->registerFunction( 'onAfterUpdateUser','pul_getChanges','getcbpuloggerTab' );
    // onAfterUserAvatarUpdate triggered function below is used to log avatar image changes via image uploading
    // Please note that avatar updates that involve switching to avatar gallery item is not logged (via Update image menu)
    $_PLUGINS->registerFunction( 'onAfterUserAvatarUpdate','pul_onAfterUserAvatarUpdate','getcbpuloggerTab' );
    // onLogChange triggered function used to find any single field changes (experimentally placed here - not used) 
    // $_PLUGINS->registerFunction( 'onLogChange','pul_onLogChange','getcbpuloggerTab' );
    $_PLUGINS->registerFunction( 'onAfterDeleteUser', 'userDeleted','getcbpuloggerTab' );
}

/**
 * Class pulEntry
 * Database Table class for internal logging
 */
class pulEntry extends OrderedTable {
//class pulEntry extends comprofilerDBTable {
    public $id;            // sql:int(10)
    public $changedate;    // sql:datetime
    public $profileid;     // sql:int(11)
    public $editedbyid;    // sql:int(10)
    public $editedbyip;    // sql:varchar(255)
    public $mode;          // sql:char(1)
    public $fieldname;     // sql:varchar(50)
    public $oldvalue;      // sql:text
    public $newvalue;      // sql:text

    /**
     * Table Constructor
     *
     * @param  DatabaseDriverInterface|null  $db
     */
    public function __construct( $db = null )
    {
        parent::__construct( $db, '#__comprofiler_plug_pulogger', 'id' );
    }
} // end of pulEntry class

/**
 * Basic tab extender. Any plugin that needs to display a tab in the user profile
 * needs to have such a class. Also, currently, even plugins that do not display tabs (e.g., auto-welcome plugin)
 * need to have such a class if they are to access plugin parameters (see $this->params statement).
 */
class getcbpuloggerTab extends cbTabHandler {
    /**
     * Construnctor
     */
    //function getcbpuloggerTab() {
    //    $this->cbTabHandler();
    //}

    /**
     * @return mixed
     */
    function getcbpulparams(){

	    $params = $this->params;
	    // Plugin and Tab Parameters
		$pulParams["pulbackendlogging"] = (int) $params->get('pulBackEndLogging', 1); // get backend logging switch
	    $pulParams["pulenablenotifications"] = (int) $params->get('pulEnableNotifications', 1); // get notifications switch
        $pulParams["pulenablebabuserview"] = (int) $params->get('pulEnableTabUserView', 0); // get user tab view switch
        $pulParams["pulenablepagingfe"] = (int) $params->get('pulEnablePagingFE', 1); // get fe paging switch
        $pulParams["pulentriesperpagefe"] = (int) $params->get('pulEntriesPerPageFE', 20); // get fe paging size
        $pulParams["pulentriesperpagebe"] = (int) $params->get('pulEntriesPerPageBE', 50); // get be paging size
        $pulParams["pulnotificationlist"] = $params->get('pulNotificationList', '0');
        if (!in_array($pulParams["pulnotificationlist"], array("0","1","2"))) {
            $pulParams["pulnotificationlist"] = "0";
        }
        $pulParams["pulnotificationrecipientlist"] = $params->get('pulNotificationRecipientList',null);
		$pulParams["pulNotificationAclList"] = $params->get('pulNotificationAclList', 6);

        //$num_userids = explode(",", $pulParams["pulnotificationrecipientlist"]);
        
        
        $pulParams["pultablecssmode"] = $params->get('pulTableCSSMode', 'cssintpl');
        if (!in_array($pulParams["pultablecssmode"], array("cssintpl","csspush"))) {
            $pulParams["pultablecssmode"] = "cssintpl";
        }

        
        $pulParams["pultablecss"] = $params->get('pulTableCSS',
"#pul-tbl
{
	font-family: &quot;Lucida Sans Unicode&quot;, &quot;Lucida Grande&quot;, Sans-Serif;
	font-size: 10px;
	background: #fff;
	margin: 5px;
	width: 99%;
	border-collapse: collapse;
	text-align: left;
}
#pul-tbl th
{
	font-size: 10px;
	font-weight: normal;
	color: #039;
	padding: 10px 8px;
	border-bottom: 2px solid #6678b1;
}
#pul-tbl td
{
	border-bottom: 1px solid #ccc;
	color: #669;
	padding: 6px 8px;
}
#pul-tbl tbody tr:hover td
{
	color: #009;
}
");
		
	    return $pulParams;
    }


    /**
    * This is the function that is called to edit the tab in profile update frontend mode
    * or backend CB User Manager viewing (also considered as edit mode)
    * 
    * @param mixed $tab
    * @param mixed $user
    * @param mixed $ui
    * @return mixed
    */
    function getEditTab( $tab, $user, $ui ) {
        global $_CB_database, $_CB_framework;
        
        $return = null;
        if ($ui!=2) { // do not show if not in backend
            return( $return);
        }

        // presentation logic starts here 
        $pulParams = $this->getcbpulparams();

        $pul_total = 0; // initialize
        $pul_enablepaging = 0; // No paging supported in backend (planned for CB 2.0)
        $pul_itemsperpage = $pulParams["pulentriesperpagebe"]; // Get number of last entries to show in backend
        
        // get existing log entry count for this profileid
        if ( $pul_enablepaging ) { // if paging enabled then get limits

            $pul_query = 'SELECT COUNT(*)'
                . "\n FROM #__comprofiler_plug_pulogger"
                . "\n WHERE profileid = " . (int) $user->id;
            $_CB_database->setQuery( $pul_query );
            $pul_total = $_CB_database->loadResult();
            
            if ( ! is_numeric( $pul_total ) ) {
                $pul_total = 0;    
            }

            $pul_pagingParams = array();
            if ( $pul_pagingParams['pulposts_limitstart'] === null )
                $pul_pagingParams['pulposts_limitstart'] = '0';
            if ( $pul_itemsperpage > $pul_total )
                $pul_pagingParams['pulposts_limitstart'] = '0';
        
        } else {
            $pul_pagingParams['pulposts_limitstart'] = '0';
        }
        
        // Select all entries to be displayed
        $pul_query = "SELECT *"
            . "\n FROM #__comprofiler_plug_pulogger"
            . "\n WHERE profileid=" . (int) $user->id
            . "\n ORDER BY changedate desc";
            
        $_CB_database->setQuery($pul_query, (int) ( $pul_pagingParams["pulposts_limitstart"] ? $pul_pagingParams["pulposts_limitstart"] : 0 ), (int) $pul_itemsperpage );
        
        $pulitems = $_CB_database->loadObjectList();
        // $puldisplaycount = count($pulitems);


        $pul_htmlout  = '<a href="'. $_CB_framework->userProfileUrl( (int) $user->id ) . ' target="_blank">' . CBTxt::T( 'URL to frontend profile' ) . '</a>';
        $pul_htmlout .= $this->pul_gettblheader();
 
        // Get username of profile and initiate lookup table to avoid second query multiple times
        
        $pul_username_lookup[$user->id] = $user->username;
        
        $k=0;
        $i=0;
        foreach ($pulitems as $pulitem) {
            $k++;
            $i= ($i==1) ? 2 : 1;

            if ($i == 1) { // odd row
                $pul_row_class = "odd";
            } else { // even row
                $pul_row_class = "even";
            }

            if (!array_key_exists($pulitem->editedbyid, $pul_username_lookup)) {
                $pul_editedbyuser    =&    CBuser::getUserDataInstance( $pulitem->editedbyid );
                $pul_username_lookup[$pulitem->editedbyid] = $pul_editedbyusername = $pul_editedbyuser->username;
            } else {
                $pul_editedbyusername = $pul_username_lookup[$pulitem->editedbyid];
            }
            
            $pul_htmlout .=  "\t<tr class=\"" . $pul_row_class . "\">\n"
                . "\t\t<td>" . $pulitem->changedate . "</td>\n"
                . "\t\t<td>" . $pulitem->fieldname . "</td>\n"
                . "\t\t<td>" . $pulitem->oldvalue . "</td>\n"
                . "\t\t<td>" . $pulitem->newvalue . "</td>\n"
                . "\t\t<td>" . $pulitem->mode . ',' . $pulitem->editedbyid . '(' . $pul_editedbyusername . ')'. "</td>\n"
                . "\t</tr>\n";                 
        }
        
        $pul_htmlout .= "</table>\n";
        // Add paging control at end of list if paging enabled and needed
        if ( $pul_enablepaging && ($pul_itemsperpage < $pul_total) ) {
            $pul_htmlout .= '<div style="clear:both;">&nbsp;</div>';
            $pul_htmlout .= '<div style="width:95%;text-align:center;">'
                . $this->_writePaging($pul_pagingParams,"pulposts_",$pul_itemsperpage,$pul_total)
                . "\n<div>\n";
        }
        return( $pul_htmlout );
    } // end of getEditTab function
    
    /**
    * Generates the HTML to display the user profile tab
    * @param object tab reflecting the tab database entry
    * @param object mosUser reflecting the user being displayed
    * @param int 1 for front-end, 2 for back-end
    * @returns mixed : either string HTML for tab content, or false if ErrorMSG generated
    */
    function getDisplayTab($tab,$user,$ui) {
        global $_CB_database, $_CB_framework;
        
        $return = null;
        $isModerator=Application::MyUser()->isGlobalModerator();
        //$isModerator=isModerator($_CB_framework->myId());

        //if ($_CB_framework->myId() == $user->id) {
        if ( Application::MyUser()->getUserId() == $user->id)  {
            $isME=true;
        } else {
            $isME=false;
        }

        // presentation logic starts here
        $pulParams = $this->getcbpulparams();
        $pul_enableuserview = $pulParams["pulenablebabuserview"]; // get number of entries per page setting
        $pul_enablepaging = $pulParams["pulenablepagingfe"]; // get paging switch setting
        $pul_itemsperpage = $pulParams["pulentriesperpagefe"]; // get number of entries per page setting


        if ( !$isModerator && ( !$pul_enableuserview || !$isME ) ) { // if not moderator viewing and user viewing off then don't show tab at all
            return $return;
        }

        // Output needed CSS
        if ($pulParams["pultablecssmode"] == "csspush") {
            $_CB_framework->document->addHeadStyleInline( $pulParams["pultablecss"] );
        }
        //Find and Show Postings
        $pul_pagingParams = $this->_getPaging( array(), array( 'pulposts_' ) );
        $pul_WHERE = null;
        
        // get existing log entry count for this profileid
        if ( $pul_enablepaging ) { // if paging enabled prepare limits

            $pul_query = 'SELECT COUNT(*)'
                . "\n FROM #__comprofiler_plug_pulogger"
                . "\n WHERE profileid = " . (int) $user->id;
            $_CB_database->setQuery( $pul_query );
            $pul_total = $_CB_database->loadResult();
            
            if ( ! is_numeric( $pul_total ) ) {
                $pul_total = 0;    
            }

            if ( $pul_pagingParams['pulposts_limitstart'] === null )
                $pul_pagingParams['pulposts_limitstart'] = '0';
            if ( $pul_itemsperpage > $pul_total )
                $pul_pagingParams['pulposts_limitstart'] = '0';
        
        } else {
            $pul_pagingParams['pulposts_limitstart'] = '0';
        }
        
        // Select all entries to be displayed
        $pul_query = "SELECT *"
            . "\n FROM #__comprofiler_plug_pulogger"
            . "\n WHERE profileid=" . (int) $user->id
            . "\n ORDER BY changedate desc";
            
        $_CB_database->setQuery($pul_query, (int) ( $pul_pagingParams["pulposts_limitstart"] ? $pul_pagingParams["pulposts_limitstart"] : 0 ), (int) $pul_itemsperpage );
        
        $pulitems = $_CB_database->loadObjectList();
        // $puldisplaycount = count($pulitems);

        $pul_htmlout = $this->pul_gettblheader();
 
        // Get username of profile and initiate lookup table to avoid second query multiple times        
        $pul_username_lookup[$user->id] = $user->username;

        $k=0;
        $i=0;
        foreach ($pulitems as $pulitem) {
            $k++;
            $i= ($i==1) ? 2 : 1;

            if ($i == 1) { // odd row
                $pul_row_class = "odd";
            } else { // even row
                $pul_row_class = "even";
            }
            
            if (!array_key_exists($pulitem->editedbyid, $pul_username_lookup)) {
                $pul_editedbyuser    =&    CBuser::getUserDataInstance( $pulitem->editedbyid );
                $pul_username_lookup[$pulitem->editedbyid] = $pul_editedbyusername = $pul_editedbyuser->username;
            } else {
                $pul_editedbyusername = $pul_username_lookup[$pulitem->editedbyid];
            }
            

            $pul_htmlout .=  "\t\t<tr class=\"" . $pul_row_class . "\">\n"
                . "\t\t\t<td>" . $pulitem->changedate . "</td>\n"
                . "\t\t\t<td>" . $pulitem->fieldname . "</td>\n"
                . "\t\t\t<td>" . $pulitem->oldvalue . "</td>\n"
                . "\t\t\t<td>" . $pulitem->newvalue . "</td>\n"
                . "\t\t\t<td>" . $pulitem->mode . ',' . $pulitem->editedbyid . '(' . $pul_editedbyusername . ')'. "</td>\n"
                . "\t\t</tr>\n";                 
        }
        
        $pul_htmlout .= "\t</tbody>\n";
        $pul_htmlout .= "</table>\n";
        // Add paging control at end of list if paging enabled
        if ( $pul_enablepaging && ($pul_itemsperpage < $pul_total) ) {
            $pul_htmlout .= '<div style="clear:both;">&nbsp;</div>';
            $pul_htmlout .= '<div style="width:95%;text-align:center;">'
                .$this->_writePaging($pul_pagingParams,"pulposts_",$pul_itemsperpage,$pul_total)
                . "\n</div>\n";
        }
        return( $pul_htmlout);
        
    } // end of getDisplayTab function
 
     /**
    * This function is called after frontend user clicks the update button during profile
    * editing. 
    * 
    * @param mixed $user1
    * @param mixed $user2
    * @param mixed $previoususer
    */
    function pul_getChanges($user1,$user2,$previoususer) {
        global $_CB_framework, $_CB_database;

        $pulDiffs=$this->objCompare($previoususer, $user1);  // get profile update differences
        if ($pulDiffs==null) return; // if no differences then return

        $pul_changedate = Application::Database()->getUtcDateTime();
        $pul_profileid = $user1->user_id;
        // $pul_editedbyid = $_CB_framework->myId(); // get id of updater (could be moderator)
		$pul_editedbyid = Application::MyUser()->getUserId(); // get id of updater (could be moderator)

        $pul_editedbyip = cbGetIPlist(); // get IP of updater
        // $pul_mode = $_CB_framework->getUi(); // update mode 1 for f 2 for b
		$pul_mode = Application::Cms()->getClientId(); // update mode 0 for f 1 for b

		// TODO - add new feature to not log backend changes if parameter is set
		$pulParams = $this->getcbpulparams();
		if (!$pulParams["pulbackendlogging"] && $pul_mode) { // if backend logging disabled return (nothing left to do)
			return;
		}


		foreach ($pulDiffs as $pulDiff) { // decompose changes to save individually as separate rows in db table
            $pul_entry = new pulEntry( $_CB_database ); // create new event log object for stroage
            $pul_entry->changedate = $pul_changedate;
            $pul_entry->profileid = $pul_profileid;
            $pul_entry->editedbyid = $pul_editedbyid; // get id of updater (could be moderator)
            $pul_entry->editedbyip = $pul_editedbyip; // get IP of updater
            $pul_entry->mode = $pul_mode; // frontend-backend mode
            $pul_entry->fieldname = $pulDiff[0];
            $pul_entry->oldvalue = $pulDiff[1];
            $pul_entry->newvalue = $pulDiff[2];
            if ( ! $pul_entry->store() ) {
                trigger_error( 'cbProfileUpdateLogger Save SQL error: ' . $pul_entry->getError(), E_USER_WARNING );
                return false;
            }
            unset($pul_entry); // free object and memory
        }
        
        if (!$pulParams["pulenablenotifications"]) { // if notifications disabled return (nothing left to do)
            return;
        }
            
        // moderator notification logic follows
        $pul_profileusername = $user1->username;
        $pul_editedbyuser    =&    CBuser::getUserDataInstance( $pul_editedbyid );
        $pul_editedbyusername = $pul_editedbyuser->username;

        $pul_notificationaray = $this->pul_getnotificationstrings();

        $pul_notificationSubject = sprintf( $pul_notificationaray[0],
                    $pul_profileusername, $pul_editedbyusername);
        
        $pul_notificationBody  = sprintf( $pul_notificationaray[1] );
        $pul_notificationBody .= sprintf( $pul_notificationaray[2], $pul_changedate);
        $pul_notificationBody .= sprintf( $pul_notificationaray[3], $pul_profileid);
        $pul_notificationBody .= sprintf( $pul_notificationaray[4], $pul_profileusername);
        $pul_notificationBody .= sprintf( $pul_notificationaray[5], cbSef( 'index.php?option=com_comprofiler&task=userprofile&user=' . (int) $pul_profileid . getCBprofileItemid( false ) ));
        $pul_notificationBody .= sprintf( $pul_notificationaray[6], $pul_editedbyid);
        $pul_notificationBody .= sprintf( $pul_notificationaray[7], $pul_editedbyusername);
        $pul_notificationBody .= sprintf( $pul_notificationaray[8], $pul_editedbyip);
        $pul_notificationBody .= sprintf( $pul_notificationaray[9], ($pul_mode==0) ? $pul_notificationaray[10] : $pul_notificationaray[11]);
        $pul_notificationBody .= sprintf( $pul_notificationaray[12]);
        
        foreach ($pulDiffs as $pulDiff) { // decompose changes to add separately to notification body
            $pul_notificationBody .= sprintf( $pul_notificationaray[13], $pulDiff[0] );
            $pul_notificationBody .= sprintf( $pul_notificationaray[14], $pulDiff[1] );
            $pul_notificationBody .= sprintf( $pul_notificationaray[15], $pulDiff[2] );
            $pul_notificationBody .= sprintf("-\n");                
        }
        
        $pulNotification = new cbNotification();

        
        switch ( $pulParams["pulnotificationlist"] ) {
            
            case "0": // CB Moderators to receive notifications
                $pul_res = $pulNotification->sendToModerators( $pul_notificationSubject, $pul_notificationBody );
                break;
            
            case "1": // ACL View to receive notifications

				$pul_gid_mods =	Application::CmsPermissions()->getGroupsOfViewAccessLevel( $pulParams["pulNotificationAclList"], true );

				//$pul_gid_mods = $_CB_framework->acl->mapGroupNamesToValues( 'Superadministrator' );
				if ( $pul_gid_mods ) {
					$query = 'SELECT DISTINCT u.id'
						. "\n FROM #__users u"
						. "\n INNER JOIN #__comprofiler c"
						. ' ON u.id = c.id';

					$query .= "\n INNER JOIN #__user_usergroup_map g"
						. ' ON c.id = g.user_id'
						. "\n WHERE g.group_id IN " . $_CB_database->safeArrayOfIntegers( $pul_gid_mods );

					$query .= "\n AND u.block = 0"
						. "\n AND c.confirmed = 1"
						. "\n AND c.approved = 1"
						. "\n AND u.sendEmail = 1";

					$_CB_database->setQuery( $query );
					$mods = $_CB_database->loadObjectList();
					$pul_res = TRUE;
					if ( $mods ) foreach ( $mods AS $mod ) {
						$pul_res = $pul_res && $pulNotification->sendFromSystem( $mod->id, $pul_notificationSubject, $pul_notificationBody );
					}
				}

                break;
            
            case "2": // List of userids to receive notifications
                $list_userids = explode(",", $pulParams["pulnotificationrecipientlist"]);
                $count_userids = count($list_userids);
                if ($count_userids > 0) {
                    $pul_res = TRUE;
                    foreach ( $list_userids AS $one_userid) {
                        $pul_res = $pul_res && $pulNotification->sendFromSystem( $one_userid, $pul_notificationSubject, $pul_notificationBody );
                    }
                }
                break;
            
            default:
                break;
        }
        
        if (!$pul_res) {
            $this->_setErrorMSG(CBTxt::T( 'CB Profile Logger failed to send moderation email' ) );
        }
        unset($pulNotification);        
        return ($pul_res);        
    } // end of pul_getChanges function
    
    /**
    * put your comment there...
    * 
    * @param mixed $user
    * @param mixed $isModerator
    * @param mixed $newFileName
    */
    function pul_onAfterUserAvatarUpdate($user1, $user2, $isModerator, $newFileName){
        global $_CB_framework, $_CB_database, $ueConfig;
        
        if ($isModerator) {
            $pul_event = "avatar";
        } else {
            if ($ueConfig['avatarUploadApproval']) {
                $pul_event = "avatar_pending";    
            }
        }

        $pul_changedate = Application::Database()->getUtcDateTime();
        $pul_profileid = $user1->user_id;
        $pul_editedbyid = Application::MyUser()->getUserId(); // get id of updater (could be moderator)
        $pul_editedbyip = cbGetIPlist(); // get IP of updater
        // $pul_mode = $_CB_framework->getUi(); // update mode 0 for f 1 for b
		$pul_mode = Application::Cms()->getClientId(); // update mode 0 for f 1 for b

		$pul_entry = new pulEntry( $_CB_database ); // create new event log object for stroage
        $pul_entry->changedate = $pul_changedate;
        $pul_entry->profileid = $pul_profileid; // get userid or profile being updated
        $pul_entry->editedbyid = $pul_editedbyid; // get id of updater (could be moderator)
        $pul_entry->editedbyip = $pul_editedbyip; // get IP of updater
        $pul_entry->mode = $pul_mode; // update mode 0 for f 1 for b

        $pul_entry->fieldname = $pul_event;
        $pul_entry->oldvalue = $user1->avatar;
        $pul_entry->newvalue = $newFileName;
        if ( ! $pul_entry->store() ) {
            trigger_error( CBTxt::T( 'cbProfileUpdateLogger Save SQL error: ' ) . $pul_entry->getError(), E_USER_WARNING );
            return false;
        }
        unset($pul_entry); // free object and memory

                
        $pulParams = $this->getcbpulparams();
        
        if (!$pulParams["pulenablenotifications"]) { // if notifications disabled return (nothing left to do)
            return;
        }

        // moderator notification logic follows
        $pul_profileusername = $user1->username;
        $pul_editedbyuser    =&    CBuser::getUserDataInstance( $pul_editedbyid );
        $pul_editedbyusername = $pul_editedbyuser->username;

        $pul_notificationaray = $this->pul_getnotificationstrings();

        $pul_notificationSubject = sprintf( $pul_notificationaray[0],
            $pul_profileusername, $pul_editedbyusername);

        $pul_notificationBody  = sprintf( $pul_notificationaray[1] );
        $pul_notificationBody .= sprintf( $pul_notificationaray[2], $pul_changedate);
        $pul_notificationBody .= sprintf( $pul_notificationaray[3], $pul_profileid);
        $pul_notificationBody .= sprintf( $pul_notificationaray[4], $pul_profileusername);
        $pul_notificationBody .= sprintf( $pul_notificationaray[5], cbSef( 'index.php?option=com_comprofiler&task=userprofile&user=' . (int) $pul_profileid . getCBprofileItemid( false ) ));
        $pul_notificationBody .= sprintf( $pul_notificationaray[6], $pul_editedbyid);
        $pul_notificationBody .= sprintf( $pul_notificationaray[7], $pul_editedbyusername);
        $pul_notificationBody .= sprintf( $pul_notificationaray[8], $pul_editedbyip);
        $pul_notificationBody .= sprintf( $pul_notificationaray[9], ($pul_mode==0) ? $pul_notificationaray[10] : $pul_notificationaray[11]);
        $pul_notificationBody .= sprintf( $pul_notificationaray[12]);

        $pul_notificationBody .= sprintf( $pul_notificationaray[13], $pul_event );
        $pul_notificationBody .= sprintf( $pul_notificationaray[14], $user1->avatar );
        $pul_notificationBody .= sprintf( $pul_notificationaray[15], $newFileName );
        $pul_notificationBody .= sprintf("-\n");

        $pulNotification = new cbNotification();
        $pul_res = $pulNotification->sendToModerators( $pul_notificationSubject, $pul_notificationBody );
 
        if (!$pul_res) {
                $this->_setErrorMSG( CBTxt::T( 'CB Profile Logger failed to send moderation email' ) );
        }
        unset($pulNotification); // free object and memory        
        return ($pul_res);        

    } // end of pul_onAfterUserAvatarUpdate function

    /**
    * This function finds all updated fields in user profile and returns them
    * 
    * @param mixed $oB this is the user object before the update
    * @param mixed $oA this is the user object after the update
    * 
    * returns an array of arrays containing fieldname, beforevalue, aftervalue
    */
    function objCompare($oB, $oA) {
        $retArr = null;
        $oVarsA=get_object_vars($oA);
        $oVarsB=get_object_vars($oB);
        $aKeys=array_keys($oVarsA);
        $bKeys=array_keys($oVarsB);
        if($aKeys !== $bKeys) {
            // $GLOBALS[err]=ERR(__CLASS__,__FUNCTION__,__FILE__,'',"Supplied objects are not of same class.");
            return false;
        } else {
            foreach($aKeys as $sKey) {
                $sKey_fc = substr($sKey,0,1);
                if ($sKey_fc != "_" && $sKey_fc != "#") {
                    if ( $oA->$sKey !=  $oB->$sKey) {
                       switch ($sKey) {
                           case "password":
                                if ($oA->$sKey!=null ) { // Password being updated
                                    $retArr[]=array($sKey,"xxxxxxxx","xxxxxxxx");    
                                }
                                break;
                           case "params": // ignore for now as objects seem to differ
                                if (is_array($oA->$sKey)) { // make sure after update user parameters are stored as array
                                    $pul_aparams = $oA->$sKey;    
                                } else { // otherwise if string use CB function to convert to array
                                     if (is_string($oA->$sKey)) { // if string convert to array
                                        $pa = new cbParamsBase($oA->$sKey);
                                        $pul_aparams = get_object_vars( $pa->_params ); // convert to array                                        
                                    }
                                }
                                if (is_array($oB->$sKey)) { // make sure before update user parameters are stored as array
                                    $pul_bparams = $oB->$sKey;
                                } else { // otherwise if string use CB function to convert to array
                                    if (is_string($oB->$sKey)) {
                                        $pb = new cbParamsBase($oB->$sKey);
                                        $pul_bparams = get_object_vars( $pb->_params ); // convert to array                                        
                                    }
                                }
                                
                                $pul_pKeys=array_keys($pul_aparams);
                                $pul_bKeys=array_keys($pul_bparams);
                                foreach($pul_pKeys as $pul_pKey) {
                                    $pul_pKeyfc = substr($pul_pKey,0,1);
                                    if ($pul_pKeyfc != "_" && $pul_pKeyfc != "#") {
                                        if (array_key_exists($pul_pKey,$pul_bparams)) {
                                            if ( trim($pul_aparams[$pul_pKey]) != trim($pul_bparams[$pul_pKey]) ) {

                                                $retArr[]=array($pul_pKey,$pul_bparams[$pul_pKey],$pul_aparams[$pul_pKey]);    
                                            }
                                        }
                                    }
                                }
                                break; 
                           default:
                               if ( ! ( ($oB->$sKey == "0000-00-00") AND ( $oA->$sKey == null ) ) ) { // empty date exception
                                    $retArr[] = array($sKey, $oB->$sKey, $oA->$sKey);
                                }
                       }  
                    }
                }
            }
        }
        return $retArr;
    } // end of objCompare function

    /**
     * @param $field_descr
     * @param $field_data
     * @param $field_type
     * @param int $field_required
     * @param string $min_length
     * @param string $max_length
     * @return bool
     */
    function pul_field_validator($field_descr, $field_data, $field_type, $field_required=1, $min_length="", $max_length="") {
        # array for storing error messages
        $messages[] = "";
    
        # first, if no data and field is not required, just return now:
        if ( !$field_data && !$field_required ) { 
            return true; 
        }
        
        # initialize a flag variable - used to flag whether data is valid or not
        $field_ok = false;
        
        # this is the regexp for email validation:
        $email_regexp  = "^([a-zA-Z0-9_-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|";
        $email_regexp .= "(([a-zA-Z0-9-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$";

        # a hash array of "types of data" pointing to "regexps" used to validate the data:
        $data_types=array(
            "email" => $email_regexp,
            "digit" => "^[0-9]$",
            "number" => "^[0-9]+$",
            "alpha" => "^[a-zA-Z]+$",
            "alpha_space" => "^[a-zA-Z ]+$",
            "alphanumeric" => "^[a-zA-Z0-9]+$",
            "alphanumeric_space" => "^[a-zA-Z0-9 ]+$",
            "string" => "",
            "comma_integer_list" => "^(\d+(,\d+)*)?$"
        );
    
        # check for required fields
        if ( $field_required && empty($field_data) ) {
            $messages[] = "$field_descr is a required field.";
            return false;
        }
    
        # if field type is a string, no need to check regexp:
        if ($field_type == "string") {
            $field_ok = true;
        } else {
            # Check the field data against the regexp pattern:
            $field_ok = ereg($data_types[$field_type], $field_data);
        }
    
        # if field data is bad, add message:
        if (!$field_ok) {
            $messages[] = "Please enter a valid $field_descr.";
            return false;
        }
    
        # field data max length checking:
        if ( $field_ok && $max_length ) {
            if ( strlen($field_data) > $max_length ) {
                $messages[] = "$field_descr is invalid, it should not be more than $max_length characters.";
                return false;
            }
        }
        
        # field data min length checking:
        if ( $field_ok && $min_length ) {
            if ( strlen($field_data) > $min_length ) {
                $messages[] = "$field_descr is invalid, it should be at least $min_length characters.";
                return false;
            }
        }
   }


    /**
     * This is the function that is called to return html code for table header
     * layout that is used in frontenc and backend tab display
     *
     * @return mixed
     */
    function pul_gettblheader() {

        $pul_htmltblheader = "\n<table id=\"pul-tbl\">\n"
            . "\t<thead>\n"
            . "\t\t<tr>\n"
            . "\t\t\t<th scope=\"col\">" . CBTxt::T( 'Date Time' ) . "</th>\n"
            . "\t\t\t<th scope=\"col\">" . CBTxt::T( 'Field Name' ) . "</th>\n"
            . "\t\t\t<th scope=\"col\">" . CBTxt::T( 'Old Value' ) . "</th>\n"
            . "\t\t\t<th scope=\"col\">" . CBTxt::T( 'New Value' ) . "</th>\n"
            . "\t\t\t<th scope=\"col\">" . CBTxt::T( 'Mode,ID(username)' ) . "</th>\n"
            . "\t\t</tr>\n"
            . "\t</thead>\n"
            . "\t<tbody>\n";

        return ( $pul_htmltblheader );
    }


    /**
     * This is the function that is called to return language ready notification strings in an array
     * ready to be used to formulate notification messages
     */
    function pul_getnotificationstrings() {

        $pul_notarray[0] = CBTxt::T( 'Profile Logger notification: %s profile updated by %s' );
        $pul_notarray[1] = CBTxt::T( 'Profile update event\n--------------------\n' );
        $pul_notarray[2] = CBTxt::T( 'Date/Time stamp: %s\n' );
        $pul_notarray[3] = CBTxt::T( 'Id of profile being changed: %d\n' );
        $pul_notarray[4] = CBTxt::T( 'Username of profile being changed: %s\n' );
        $pul_notarray[5] = CBTxt::T( 'URL of profile being changed: %s\n' );
        $pul_notarray[6] = CBTxt::T( 'Id of user making change: %d\n' );
        $pul_notarray[7] = CBTxt::T( 'Username of user making change: %s\n' );
        $pul_notarray[8] = CBTxt::T( 'IP of user making change: %s\n' );
        $pul_notarray[9] = CBTxt::T( 'Changes made from: %s\n' );
        $pul_notarray[10] = CBTxt::T( 'frontend' );
        $pul_notarray[11] = CBTxt::T( 'backend' );
        $pul_notarray[12] = CBTxt::T( '\n\nModification logged\n-------------------\n' );
        $pul_notarray[13] = CBTxt::T( 'Fieldname: %s\n' );
        $pul_notarray[14] = CBTxt::T( 'Old value: %s\n' );
        $pul_notarray[15] = CBTxt::T( 'New value: %s\n' );


        return ( $pul_notarray );
    }

    /**
    * UserBot Called when a user is deleted from backend (prepare future unregistration)
    * @param object mosUser reflecting the user being deleted
    * @param int 1 for successful deleting
    * @returns true if all is ok, or false if ErrorMSG generated
    * 
    */
    function userDeleted($user, $success) {
	global $_CB_database,$_CB_framework;
		
	$sql="DELETE FROM #__comprofiler_plug_pulogger WHERE profileid=" . (int) $user->id;
	$_CB_database->SetQuery($sql);
	if (!$_CB_database->query()) {
		$this->_setErrorMSG("SQL error cb.profilegallery:userDeleted-1" . $_CB_database->stderr(true));
		return false;
	}
				
	return true;
    }
} // end of getcbpuloggerTab class
?>
