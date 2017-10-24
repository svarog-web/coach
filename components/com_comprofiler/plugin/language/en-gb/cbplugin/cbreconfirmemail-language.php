<?php
/**
* Community Builder (TM) cbreconfirmemail English (United Kingdom) language file Frontend
* @version $Id:$
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

/**
* WARNING:
* Do not make changes to this file as it will be over-written when you upgrade CB.
* To localize you need to create your own CB language plugin and make changes there.
*/

defined('CBLIB') or die();

return	array(
// 28 language strings from file plug_cbreconfirmemail/cbreconfirmemail.xml
'CHANGED_820dbd'	=>	'Changed',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Input substitution supported message displayed after changing email address. Leave blank to display no message.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'Your email address has changed and requires reconfirmation. Please check your new email address for your confirmation email.',
'NOTIFICATION_96d008'	=>	'Notification',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Input a substitution supported from name to be sent with all reconfirm emails (e.g. My Awesome CB Site!). If left blank will default to users name. If specified a replyto name will be added as the users name.',
'FROM_NAME_4a4a8f'	=>	'From Name',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Input a substitution supported from address to send all reconfirm emails from (e.g. general@domain.com). If left blank will default to users email. If specified a replyto address will be added as the users email.',
'FROM_ADDRESS_a5ab7d'	=>	'From Address',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Input substitution supported reconfirm email subject.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Your email address has changed',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Input html and substitution supported reconfirm email body. Supply [reconfirm] to display the confirmation link. Additionally [old_email] can be used to display the old email address or [new_email] to display the new email address.',
'BODY_ac101b'	=>	'Body',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'The email address attached to your account [username] has changed to [new_email] and requires confirmation.<br><br>You can confirm your email address by clicking on the following link:<br><a href="[reconfirm]">[reconfirm]</a><br><br>If this was done in error please contact administration or cancel by <a href="[cancel]">clicking here</a>.',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Input a substitution supported CC address (e.g. [email]); multiple addresses supported with comma seperated list (e.g. email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC Address',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Input a substitution supported BCC address (e.g. [email]); multiple addresses supported with comma seperated list (e.g. email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Address',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Input a substitution supported Attachment address (e.g. [cb_myfile]); multiple addresses supported with comma seperated list (e.g. /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip).',
'ATTACHMENT_e9cb21'	=>	'Attachment',
'RECONFIRMED_e748a2'	=>	'Reconfirmed',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Input substitution supported URL to redirect to after successful reconfirm. If left blank no redirect will be performed.',
'REDIRECT_4202ef'	=>	'Redirect',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Input substitution supported message displayed after successful reconfirm.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'New email address confirmed successfully!',
'CANCELLED_a149e8'	=>	'Cancelled',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Input substitution supported URL to redirect to after successfully cancelling email change. If left blank no redirect will be performed.',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Input substitution supported message displayed after successfully cancelling email change.',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'Email address change cancelled successfully!',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Confirm code missing.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'User not associated with confirm code.',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Confirm code is not valid.',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Failed to cancel email address change! Error: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Failed to confirm new email address! Error: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'Email address has already been confirmed.',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'Emails',
);
