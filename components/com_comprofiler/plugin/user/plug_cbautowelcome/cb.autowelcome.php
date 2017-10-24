<?php
/**
 * Joomla Community Builder User Plugin: plug_cbautowelcome
 * @version $Id: $
 * @package CommunityBuilder CB Autowelcome
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

/** ensure this file is being included by a parent file **/
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->registerFunction( 'onUserActive', 'awUserActivated', 'plug_cbautowelcomeclass' );

/**
 * Need to generate tab object to grab plugin parameters.
 */
class plug_cbautowelcomeclass extends cbTabHandler {

	/**
	 * Get all plugin, tab, and CB fields related with this application
	 * @access private
	 * @param object mosUser reflecting the user being displayed
	 */
	function _awGetPlugParameters(){

		$params = $this->params;
		
		// Plugin Parameters
		$PlugParams["awautomessageenable"] = intval($params->get('awAutoMessageEnable', 0));
		$PlugParams["awmessagemethod"] = intval($params->get('awMessageMethod', 0));
		
		$PlugParams["awpmsmessagesubject"] = $params->get('awPMSMessageSubject', "Welcome Aboard [Name]!");
		$PlugParams["awpmsmessagebody"] = $params->get('awPMSMessageBody', "Hello [NAME], Welcome to our site!");
		$PlugParams["awpmsfromuserid"] = intval($params->get('awPMSFromUserId', 62));

		$PlugParams["awemailmessagesubject"] = $params->get('awEMAILMessageSubject', "Welcome Aboard [NAME]!");
		$PlugParams["awemailmessagebody"] = $params->get('awEMAILMessageBody', "Hello [NAME], Welcome to our site!");
		$PlugParams["awemailfromuserid"] = intval($params->get('awEMAILFromUserId', 62));

		
		$PlugParams["awautoconnectenable"] = intval($params->get('awAutoConnectEnable', 0));
		$PlugParams["awautoconnectmessage"] = $params->get('awConnectMessage', 'Auto Connection Request');
		$PlugParams["awkeyuserid"] = $params->get('awKeyUserId', 62);
		$PlugParams["awautoconnectdirection"] = intval($params->get('awAutoConnectDirection', 0));
			
		return $PlugParams;
	}
	
	function awUserActivated($user, $success) {
		global $_CB_framework, $ueConfig;
		
		if (!$success) return false;
		
        // get CBUser so we can use replaceUserVars() from CBAPI
        $awCBuser =&  CBUser::getInstance((int) $user->id);
        
		$res_wpms = true;
		$res_wemail = true;		
		$res_wconnect = true;
		
		$plugparams=$this->_awGetPlugParameters();
		
		$testNotifications = new cbNotification();
	
		if ($plugparams["awautomessageenable"]) {
			switch ($plugparams["awmessagemethod"]) {
				case 0: // PMS
					$cbawNotification = new cbNotification();
					$res_wpms = $cbawNotification->sendUserPMSmsg((int) $user->id,
						$plugparams["awpmsfromuserid"],
                        $awCBuser->replaceUserVars( $plugparams["awpmsmessagesubject"] ),
						$awCBuser->replaceUserVars( $plugparams["awpmsmessagebody"] ), 
						true);
					if (!$res_wpms) {
						$this->_setErrorMSG("Auto-Welcome plugin failed to send PMS welcome message");
					}
					break;
				case 1: // Email
					$cbawNotification = new cbNotification();
					$res_wemail=$cbawNotification->sendUserEmail((int) $user->id,
						$plugparams["awemailfromuserid"],
						$awCBuser->replaceUserVars( $plugparams["awemailmessagesubject"] ),
						$awCBuser->replaceUserVars( $plugparams["awemailmessagebody"] ),
						$plugparams["awemailfromuserid"]);	//reveal email
					if (!$res_wemail) {
						$this->_setErrorMSG("Auto-Welcome plugin failed to send Email welcome message");
					}			
					break;
				case 2: // Email and PMS
					$cbawNotification = new cbNotification();
					$res_wpms = $cbawNotification->sendUserPMSmsg((int) $user->id,
						$plugparams["awpmsfromuserid"],
						$awCBuser->replaceUserVars( $plugparams["awpmsmessagesubject"] ),
						$awCBuser->replaceUserVars( $plugparams["awpmsmessagebody"] ), 
						true);
					if (!$res_wpms) {
						$this->_setErrorMSG("Auto-Welcome plugin failed to send PMS welcome message");
					}
					$res_wemail=$cbawNotification->sendUserEmail((int) $user->id,
						$plugparams["awemailfromuserid"],
						$awCBuser->replaceUserVars( $plugparams["awemailmessagesubject"] ),
						$awCBuser->replaceUserVars( $plugparams["awemailmessagebody"] ),
						$plugparams["awpmsfromuserid"]);	//reveal email				
					if (!$res_wemail) {
						$this->_setErrorMSG("Auto-Welcome plugin failed to send Email welcome message");
					}			
					break;
				default:
					break;
			}		
		}
	
		if ($plugparams["awautoconnectenable"] && $ueConfig['allowConnections']) {
		
			$awkeyuserid_count = substr_count($plugparams["awkeyuserid"],',');
			$res_wconnect = true;
			$awkeyuserid_item = explode(",",$plugparams["awkeyuserid"]);
			
			if ($plugparams["awautoconnectdirection"]==0) { // connect new user to key users
				$cbawCon=new cbConnection( (int) $user->id);
				for ($aw_i=0;$aw_i<=$awkeyuserid_count;$aw_i++) {
					$res_wconnect = $res_wconnect && $cbawCon->addConnection((int) $awkeyuserid_item[$aw_i],
						$awCBuser->replaceUserVars( $plugparams["awautoconnectmessage"] ));
				}
				if (!$res_wconnect) {
					$this->_setErrorMSG("Auto-Welcome plugin failed to initiate auto-connection");
				}
				unset($cbawCon); // cleanup			
			} else { // connect key users to new user
				for ($aw_i=0;$aw_i<=$awkeyuserid_count;$aw_i++) {
					$cbawCon=new cbConnection((int) $awkeyuserid_item[$aw_i]);
					$res_wconnect = $res_wconnect && $cbawCon->addConnection((int) $user->id,
						$awCBuser->replaceUserVars( $plugparams["awautoconnectmessage"] ));
					unset($cbawCon); // cleanup
				}
				if (!$res_wconnect) {
					$this->_setErrorMSG("Auto-Welcome plugin failed to initiate auto-connection");
				}
			}
		}
		
		if (!($res_wemail && $res_wpms && $res_wconnect)) {
			$this->raiseError(0);
		}
		
		return $res_wemail && $res_wpms && $res_wconnect;
	}
	
	
} // end of class plug_cbautowelcomeclass
?>