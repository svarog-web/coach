<?php
/**
* Community Builder (TM) cbinvites Dutch (Belgium) language file Frontend
* @version $Id:$
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

/**
* WARNING:
* Do not make changes to this file as it will be over-written when you upgrade CB.
* To localize you need to create your own CB language plugin and make changes there.
*/

defined('CBLIB') or die();

return	array(
// 9 language strings from file plug_cbinvites/cbinvites.php
'JOIN_ME_de0c95'	=>	'Join Me!',
'MAILER_FAILED_TO_SEND_46f543'	=>	'Mailer failed to send.',
'TO_ADDRESS_MISSING_225871'	=>	'To address missing.',
'SUBJECT_MISSING_8e0db8'	=>	'Subject missing.',
'BODY_MISSING_b6f835'	=>	'Body missing.',
'SEARCH_INVITES_9e5f33'	=>	'Search Invites...',
'INVITE_CODE_NOT_VALID_7cd6f7'	=>	'Invite code not valid.',
'INVITE_CODE_ALREADY_USED_cd715c'	=>	'Invite code already used.',
'INVITE_CODE_IS_VALID_96aad3'	=>	'Invite code is valid.',
// 44 language strings from file plug_cbinvites/cbinvites.xml
'YOUR_REGISTRATION_INVITE_CODE_324b31'	=>	'Your registration invite code.',
'INVITE_CODE_0a2eb0'	=>	'Invite Code',
'INVITES_213b86'	=>	'Invites',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_ON_TAB_INVITES_b76bc2'	=>	'Enable or disable usage of paging on tab invites.',
'INPUT_PAGE_LIMIT_ON_TAB_INVITES_PAGE_LIMIT_DETERMI_b17959'	=>	'Input page limit on tab invites. Page limit determines how many invites are displayed per page.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_TAB_INVITES_dc5e0c'	=>	'Enable or disable usage of search on tab invites.',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_INVITES_I_e3fe43'	=>	'Select template to be used for all of CB Invites. If template is incomplete then missing files will be used from the default template. Template files can be located at the following location: components/com_comprofiler/plugin/user/plug_cbinvites/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_7cf91e'	=>	'Optionally add a class suffix to surrounding DIV encasing all of CB Invites.',
'SELECT_INVITE_CREATE_ACCESS_ACCESS_DETERMINES_WHO__c4a59a'	=>	'Select invite create access. Access determines who can send invites. The group selected as well as those above it will have access (e.g. Registered will also be accessible to Author). Moderators are exempt from this configuration.',
'INPUT_NUMBER_OF_INVITES_EACH_INDIVIDUAL_USER_IS_LI_0d3553'	=>	'Input number of invites each individual user is limited to have active at any given time (accepted invites do not count towards this limit). If blank allow unlimited invites. Moderators are exempt from this configuration.',
'INPUT_NUMBER_OF_DAYS_AFTER_INVITE_SENT_TO_ALLOW_RE_12681b'	=>	'Input number of days after invite sent to allow resending (accepted invites do not permit resending). If blank disable resending invites. Moderators are exempt from this configuration.',
'RESEND_DELAY_4f8afe'	=>	'Resend Delay',
'SELECT_USAGE_OF_MULTIPLE_EMAILS_IN_A_SINGLE_INVITE_e8af1e'	=>	'Select usage of multiple emails in a single invite using a comma seperated list (e.g. email1@domain.com, email2@domain.com, email3@domain.com). Moderators are exempt from this configuration.',
'MULTIPLE_INVITES_b8f6fd'	=>	'Multiple Invites',
'SELECT_USAGE_OF_DUPLICATE_INVITES_TO_THE_SAME_ADDR_96bfd8'	=>	'Select usage of duplicate invites to the same address. Moderators are exempt from this configuration.',
'DUPLICATE_INVITES_9b3f9e'	=>	'Duplicate Invites',
'SELECT_CONNECTION_METHOD_FROM_INVITER_TO_INVITEE_32692b'	=>	'Select connection method from inviter to invitee.',
'CONNECTION_c2cc70'	=>	'Connection',
'PENDING_CONNECTION_30ec76'	=>	'Pending Connection',
'AUTO_CONNECTION_5f8c15'	=>	'Auto Connection',
'SELECT_INVITE_EMAIL_BODY_EDITOR_d8f5e1'	=>	'Select invite email body editor.',
'PLAIN_TEXT_e44b14'	=>	'Plain Text',
'HTML_TEXT_503c11'	=>	'HTML Text',
'WYSIWYG_fcf0d4'	=>	'WYSIWYG',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_INVITES_REQU_65e72e'	=>	'Enable or disable usage of captcha on invites. Requires latest CB AntiSpam to be installed and published. Moderators are exempt from this configuration.',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_f0ef2c'	=>	'Input a substitution supported from name to be sent with all invites (e.g. My Awesome CB Site!). If left blank will default to users name. If specified a replyto name will be added as the users name.',
'FROM_NAME_4a4a8f'	=>	'Van naam',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_ef3c3b'	=>	'Input a substitution supported from address to send all invites from (e.g. general@domain.com). If left blank will default to users email. If specified a replyto address will be added as the users email.',
'FROM_ADDRESS_a5ab7d'	=>	'Aan adres',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Geef een vervanging in die CC e-mail adressen ondersteund  (BV  [email]); Meerdere e-mail worden ondersteund door ze scheiden met een komma  (BV email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC adres',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Geef een vervanging in die BCC e-mail adressen ondersteund  (BV  [email]); Meerdere e-mail worden ondersteund door ze scheiden met een komma  (BV email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Adres',
'INPUT_SUBSTITUTION_SUPPORTED_PREFIX_OF_INVITE_EMAI_91cd80'	=>	'Input substitution supported prefix of invite email subject. Additional subsitutions supported: [site], [sitename], [path], [itemid], [register], [profile], [code], and [to].',
'SUBJECT_PREFIX_175911'	=>	'Subject Prefix',
'SITENAME_6b68ee'	=>	'[sitename] - ',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_OF_INVITE_EMAI_058012'	=>	'Input substitution supported header of invite email body. Additional subsitutions supported: [site], [sitename], [path], [itemid], [register], [profile], [code], and [to].',
'BODY_HEADER_67622c'	=>	'Body Header',
'YOU_HAVE_BEEN_INVITED_BY_USERNAME_TO_JOIN_SITENAME_e90186'	=>	'<p>You have been invited by [username] to join [sitename]!</p><br>',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_OF_INVITE_EMAI_f5fa57'	=>	'Input substitution supported footer of invite email body. Additional subsitutions supported: [site], [sitename], [path], [itemid], [register], [profile], [code], and [to].',
'BODY_FOOTER_6046e1'	=>	'Body Footer',
'INVITE_CODE_CODESITENAME_SITEREGISTRATION_REGISTER_eec8c6'	=>	'<br><p>Invite Code - [code]<br>[sitename] - [site]<br>Registration - [register]<br>[username] - [profile]</p>',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__523216'	=>	'Input a substitution supported Attachment address (e.g. [cb_myfile]); multiple addresses supported with comma seperated list (e.g. /home/username/public_html/images/file1.zip,[path]/file2.zip, http://www.domain.com/file3.zip). Additional substitutions supported: [site], [sitename], [path], [itemid], [register], [profile], [code], and [to].',
'ATTACHMENT_e9cb21'	=>	'Bijlage',
// 22 language strings from file plug_cbinvites/component.cbinvites.php
'INPUT_INVITE_EMAIL_TO_ADDRESS_SEPARATE_MULTIPLE_EM_2028c1'	=>	'Input invite email to address. Separate multiple email addresses with a comma.',
'INPUT_INVITE_EMAIL_TO_ADDRESS_54551f'	=>	'Input invite email to address.',
'INPUT_INVITE_EMAIL_SUBJECT_IF_LEFT_BLANK_A_SUBJECT_0974fc'	=>	'Input invite email subject; if left blank a subject will be applied.',
'OPTIONALLY_INPUT_PRIVATE_MESSAGE_TO_INCLUDE_WITH_I_e1d750'	=>	'Optionally input private message to include with invite email.',
'INPUT_OWNER_OF_INVITE_AS_SINGLE_INTEGER_USERID_THI_998c63'	=>	'Input owner of invite as single integer user_id. This is the user who sent the invite.',
'OPTIONALLY_INPUT_USER_OF_INVITE_AS_SINGLE_INTEGER__329e71'	=>	'Optionally input user of invite as single integer user_id. This is the user who accepted the invite.',
'COMMA_SEPERATED_LISTS_ARE_NOT_SUPPORTED_PLEASE_USE_7676e6'	=>	'Comma seperated lists are not supported! Please use a single To address.',
'INVITE_LIMIT_REACHED_1e1e31'	=>	'Invite limit reached!',
'TO_ADDRESS_NOT_SPECIFIED_e292c0'	=>	'To address not specified.',
'INVITE_TO_ADDRESS_INVALID'	=>	'To address not valid: [to_address]',
'YOU_CAN_NOT_INVITE_YOUR_SELF_487ade'	=>	'You can not invite your self.',
'TO_ADDRESS_IS_ALREADY_A_USER_4c2f27'	=>	'To address is already a user.',
'TO_ADDRESS_IS_ALREADY_INVITED_f165f0'	=>	'To address is already invited.',
'INVITE_FAILED_SAVE_ERROR'	=>	'Invite failed to save! Error: [error]',
'INVITE_FAILED_SEND_ERROR'	=>	'Invite failed to send! Error: [error]',
'INVITE_SENT_SUCCESSFULLY_380490'	=>	'Invite sent successfully!',
'INVITE_SAVED_SUCCESSFULLY_14a90f'	=>	'Invite saved successfully!',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_RESENT_528e3e'	=>	'Invite already accepted and can not be resent.',
'INVITE_RESEND_NOT_APPLICABLE_AT_THIS_TIME_c65f19'	=>	'Invite resend not applicable at this time.',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_DELETED_cbc934'	=>	'Invite already accepted and can not be deleted.',
'INVITE_FAILED_DELETE_ERROR'	=>	'Invite failed to delete! Error: [error]',
'INVITE_DELETED_SUCCESSFULLY_9ea357'	=>	'Invite deleted successfully!',
// 7 language strings from file plug_cbinvites/templates/default/invite_edit.php
'EDIT_INVITE_1faaed'	=>	'Edit Invite',
'CREATE_INVITE_1e89ce'	=>	'Create Invite',
'TO_e12167'	=>	'Aan',
'BODY_ac101b'	=>	'Body',
'USER_8f9bfe'	=>	'Gebruiker',
'UPDATE_INVITE_7c2f89'	=>	'Update Invite',
'SEND_INVITE_962943'	=>	'Send Invite',
// 9 language strings from file plug_cbinvites/templates/default/tab.php
'NEW_INVITE_4093fa'	=>	'New Invite',
'SENT_7f8c02'	=>	'Sent',
'PLEASE_RESEND_6ba908'	=>	'Please Resend',
'ACCEPTED_382ab5'	=>	'Accepted',
'RESEND_1c0b8f'	=>	'Resend',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_INVITE_405d09'	=>	'Are you sure you want to delete this Invite?',
'NO_INVITE_SEARCH_RESULTS_FOUND_63c4e3'	=>	'No invite search results found.',
'YOU_HAVE_NO_INVITES_2f8b42'	=>	'You have no invites.',
'THIS_USER_HAS_NO_INVITES_f2d878'	=>	'This user has no invites.',
);
