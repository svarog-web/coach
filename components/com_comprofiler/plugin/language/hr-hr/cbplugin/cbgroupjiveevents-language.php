<?php
/**
* Community Builder (TM) cbgroupjiveevents Croatian (Croatia) language file Frontend
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
// 24 language strings from file cbgroupjiveevents/cbgroupjiveevents.xml
'ENABLE_OR_DISABLE_USAGE_OF_CONTENT_PLUGINS_CONTENT_9c9e72'	=>	'Enable or disable usage of content plugins content.prepare on group events.',
'CONTENT_PLUGINS_897da7'	=>	'Content Plugins',
'INPUT_NUMBER_OF_EVENTS_EACH_INDIVIDUAL_USER_IS_LIM_dde6e0'	=>	'Input number of events each individual user is limited to creating per group. If blank allow unlimited events. Moderators and group owners are exempt from this configuration.',
'ENABLE_OR_DISABLE_USAGE_OF_LOCATION_ADDRESS_THE_AD_3170ba'	=>	'Enable or disable usage of location address. The address will automatically map to Google Maps.',
'ADDRESS_dd7bf2'	=>	'Address',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_EVENTS_4ca8d1'	=>	'Enable or disable usage of captcha on group events. Requires latest CB AntiSpam to be installed and published. Moderators are exempt from this configuration.',
'SCHEDULE_OF_NEW_EVENT_6729bc'	=>	'Schedule of new event',
'EDIT_OF_EXISTING_EVENT_6e5d82'	=>	'Edit of existing event',
'NEW_EVENT_REQUIRES_APPROVAL_bed887'	=>	'New event requires approval',
'USER_ATTENDS_MY_EXISTING_EVENTS_6325f2'	=>	'User attends my existing events',
'USER_UNATTENDS_MY_EXISTING_EVENTS_c91a5c'	=>	'User unattends my existing events',
'ENABLE_OR_DISABLE_USAGE_OF_EVENT_MESSAGES_EVENT_ME_d677bd'	=>	'Enable or disable usage of event messages. Event message allows the event owner, group owner, group administrators to send a notification to all attending event guests. Moderators are exempt from this configuration.',
'SELECT_HOW_EVENT_MESSAGES_SHOULD_BE_SENT_39c8d8'	=>	'Select how event messages should be sent.',
'TYPE_a1fa27'	=>	'Type',
'PRIVATE_MESSAGE_066ad4'	=>	'Private Message',
'ENABLE_OR_DISABLE_USAGE_OF_EVENT_MESSAGE_SUBJECT_T_b0170a'	=>	'Enable or disable usage of event message subject. This allows customizing the message subject. Note this only works with email message type.',
'ENABLE_OR_DISABLE_SUPPORT_FOR_HTML_IN_EVENT_MESSAG_e00f52'	=>	'Enable or disable support for HTML in event messages.',
'HTML_4c4ad5'	=>	'HTML',
'INPUT_THE_NUMBER_OF_SECONDS_BETWEEN_EVENT_MESSAGES_8b0b94'	=>	'Input the number of seconds between event messages. Leave empty for no delay. Moderators are exempt from this configuration.',
'DELAY_8f497c'	=>	'Delay',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_EVENT_MESSAG_22d506'	=>	'Enable or disable usage of captcha on event message. Requires latest CB AntiSpam to be installed and published. Moderators are exempt from this configuration.',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Enable or disable usage of paging.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Input page limit. Page limit determines how many rows are displayed per page. If paging is disabled this can still be used to limit the number of rows displayed.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Enable or disable usage of search on rows.',
// 61 language strings from file cbgroupjiveevents/component.cbgroupjiveevents.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Group does not exist.',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_EVENT_b450eb'	=>	'You do not have access to this event.',
'EVENT_DOES_NOT_EXIST_2499af'	=>	'Event does not exist.',
'SEARCH_ATTENDING_a45168'	=>	'Search Attending...',
'YOU_DO_NOT_HAVE_ACCESS_TO_MESSAGING_FOR_THIS_EVENT_0cf8c4'	=>	'You do not have access to messaging for this event.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_MESSAGIN_9dc489'	=>	'You do not have sufficient permissions to messaging for this event.',
'YOU_CAN_NOT_SEND_A_MESSAGE_TO_THIS_EVENT_AT_THIS_T_82f604'	=>	'You can not send a message to this event at this time. Please wait awhile and try again.',
'OPTIONALLY_INPUT_A_MESSAGE_SUBJECT_5d4232'	=>	'Optionally input a message subject.',
'INPUT_A_MESSAGE_TO_SEND_TO_THIS_EVENTS_GUESTS_d9f757'	=>	'Input a message to send to this events guests.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_SCHEDULE_377dda'	=>	'You do not have sufficient permissions to schedule an event in this group.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_7a77ac'	=>	'You do not have sufficient permissions to edit this event.',
'SELECT_PUBLISH_STATE_OF_THIS_EVENT_UNPUBLISHED_EVE_eee522'	=>	'Select publish state of this event. Unpublished events will not be visible to the public.',
'INPUT_THE_EVENT_TITLE_THIS_IS_THE_TITLE_THAT_WILL__58d733'	=>	'Input the event title. This is the title that will distinguish this event from others. Suggested to input something to uniquely identify your event.',
'INPUT_A_DETAILED_DESCRIPTION_ABOUT_THIS_EVENT_352b85'	=>	'Input a detailed description about this event.',
'INPUT_THE_LOCATION_FOR_THIS_EVENT_EG_MY_HOUSE_THE__dd5da8'	=>	'Input the location for this event (e.g. My House, The Park, Restaurant Name, etc..).',
'OPTIONALLY_INPUT_THE_ADDRESS_FOR_THIS_EVENT_OR_CLI_753f71'	=>	'Optionally input the address for this event or click the map button to attempt to find your current location.',
'SELECT_THE_DATE_AND_TIME_THIS_EVENT_STARTS_ea1ef7'	=>	'Select the date and time this event starts.',
'OPTIONALLY_SELECT_THE_END_DATE_AND_TIME_FOR_THIS_E_b327a7'	=>	'Optionally select the end date and time for this event.',
'OPTIONALLY_INPUT_A_GUEST_LIMIT_FOR_THIS_EVENT_cd7cde'	=>	'Optionally input a guest limit for this event.',
'INPUT_THE_EVENT_OWNER_ID_EVENT_OWNER_DETERMINES_TH_094492'	=>	'Input the event owner id. Event owner determines the creator of the event specified as User ID.',
'GROUP_EVENT_FAILED_TO_SAVE'	=>	'Event failed to save! Error: [error]',
'NEW_GROUP_EVENT_7a6b0d'	=>	'New group event',
'USER_HAS_SCHEDULED_THE_EVENT_EVENT_IN_THE_GROUP_GR_517c40'	=>	'[user] has scheduled the event [event] in the group [group]!',
'NEW_GROUP_EVENT_AWAITING_APPROVAL_39e90d'	=>	'New group event awaiting approval',
'USER_HAS_SCHEDULED_THE_EVENT_EVENT_IN_THE_GROUP_GR_217963'	=>	'[user] has scheduled the event [event] in the group [group] and is awaiting approval!',
'EVENT_SCHEDULED_SUCCESSFULLY_AND_AWAITING_APPROVAL_70038a'	=>	'Event scheduled successfully and awaiting approval!',
'EVENT_SCHEDULED_SUCCESSFULLY_0f6b9c'	=>	'Event scheduled successfully!',
'GROUP_EVENT_CHANGED_7cd889'	=>	'Group event changed',
'USER_HAS_CHANGED_THE_SCHEDULED_EVENT_EVENT_IN_THE__82e41c'	=>	'[user] has changed the scheduled event [event] in the group [group]!',
'EVENT_SAVED_SUCCESSFULLY_e074e6'	=>	'Event saved successfully!',
'YOUR_EVENT_IS_AWAITING_APPROVAL_0bbb1c'	=>	'Your event is awaiting approval.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__d26385'	=>	'You do not have sufficient permissions to publish or unpublish this event.',
'GROUP_EVENT_STATE_FAILED_TO_SAVE'	=>	'Event state failed to saved. Error: [error]',
'EVENT_SCHEDULE_REQUEST_ACCEPTED_ac27df'	=>	'Event schedule request accepted',
'YOUR_EVENT_EVENT_SCHEDULE_REQUEST_IN_THE_GROUP_GRO_fc4c05'	=>	'Your event [event] schedule request in the group [group] has been accepted!',
'EVENT_STATE_SAVED_SUCCESSFULLY_123c23'	=>	'Event state saved successfully!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_4b06a1'	=>	'You do not have sufficient permissions to delete this event.',
'GROUP_EVENT_FAILED_TO_DELETE'	=>	'Event failed to delete. Error: [error]',
'EVENT_DELETED_SUCCESSFULLY_be1e9a'	=>	'Event deleted successfully!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_ATTEND_T_dc27d7'	=>	'You do not have sufficient permissions to attend this event.',
'YOU_CAN_NOT_ATTEND_AN_EXPIRED_EVENT_d41f63'	=>	'You can not attend an expired event.',
'THIS_EVENT_IS_FULL_c0d66e'	=>	'This event is full.',
'YOU_ARE_ALREADY_ATTENDING_THIS_EVENT_1bdf2f'	=>	'You are already attending this event.',
'GROUP_EVENT_ATTEND_FAILED'	=>	'Event attend failed. Error: [error]',
'USER_ATTENDING_YOUR_GROUP_EVENT_76f83f'	=>	'User attending your group event',
'USER_WILL_BE_ATTENDING_YOUR_EVENT_EVENT_IN_THE_GRO_f05a66'	=>	'[user] will be attending your event [event] in the group [group]!',
'EVENT_ATTENDED_SUCCESSFULLY_86dd6d'	=>	'Event attended successfully!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UNATTEND_653cb2'	=>	'You do not have sufficient permissions to unattend this event.',
'YOU_CAN_NOT_UNATTEND_AN_EXPIRED_EVENT_0020a4'	=>	'You can not unattend an expired event.',
'YOU_CAN_NOT_UNATTEND_AN_EVENT_YOU_ARE_NOT_ATTENDIN_0d0bd9'	=>	'You can not unattend an event you are not attending.',
'GROUP_EVENT_FAILED_TO_UNATTEND'	=>	'Event failed to unattend. Error: [error]',
'USER_UNATTENDED_YOUR_GROUP_EVENT_6aa20f'	=>	'User unattended your group event',
'USER_WILL_NO_LONGER_BE_ATTENDING_YOUR_EVENT_EVENT__818798'	=>	'[user] will no longer be attending your event [event] in the group [group]!',
'EVENT_UNATTENDED_SUCCESSFULLY_d29b36'	=>	'Event unattended successfully!',
'GROUP_EVENT_MESSAGE_FAILED_TO_SEND'	=>	'Event message failed to send! Error: [error]',
'MESSAGE_NOT_SPECIFIED_d1e791'	=>	'Message not specified!',
'THIS_EVENT_HAS_NO_GUESTS_TO_MESSAGE_22a114'	=>	'This event has no guests to message.',
'GROUP_EVENT_MESSAGE_SUBJECT'	=>	'Event message - [subject]',
'EVENT_MESSAGE_77e9b0'	=>	'Event message',
'GROUP_EVENT_MESSAGE'	=>	'Event [event] has sent the following message.<p>[message]</p>',
'EVENT_MESSAGED_SUCCESSFULLY_6da671'	=>	'Event messaged successfully!',
// 3 language strings from file cbgroupjiveevents/library/Table/AttendanceTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Owner not specified!',
'EVENT_NOT_SPECIFIED_3f2fec'	=>	'Event not specified!',
'EVENT_DOES_NOT_EXIST_bb3b8e'	=>	'Event does not exist!',
// 6 language strings from file cbgroupjiveevents/library/Table/EventTable.php
'GROUP_NOT_SPECIFIED_70267b'	=>	'Group not specified!',
'START_DATE_NOT_SPECIFIED_1127f6'	=>	'Start date not specified!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Group does not exist!',
'END_DATE_CAN_NOT_BE_BEFORE_THE_START_DATE_fab0bf'	=>	'End date can not be before the start date!',
'GROUP_EVENT_DATE_FORMAT'	=>	'l, F j Y',
'GROUP_EVENT_TIME_FORMAT'	=>	' g:i A',
// 4 language strings from file cbgroupjiveevents/library/Trigger/AdminTrigger.php
'EVENTS_87f9f7'	=>	'Events',
'ADD_NEW_EVENT_TO_GROUP_16a334'	=>	'Add New Event to Group',
'EVENT_ATTENDANCE_5c872e'	=>	'Event Attendance',
'CONFIGURATION_254f64'	=>	'Configuration',
// 6 language strings from file cbgroupjiveevents/library/Trigger/EventsTrigger.php
'DISABLE_bcfacc'	=>	'Disable',
'ENABLE_2faec1'	=>	'Enable',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Enable, with Approval',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_EVENTS_GROUP_538b5f'	=>	'Optionally enable or disable usage of events. Group owner and group administrators are exempt from this configuration and can always schedule events. Note existing events will still be accessible.',
'DONT_NOTIFY_3ea23f'	=>	'Don\'t Notify',
'SEARCH_EVENTS_16de57'	=>	'Search Events...',
// 10 language strings from file cbgroupjiveevents/templates/default/activity.php
'GROUP_EVENT_ACTIVITY_TITLE'	=>	'scheduled an event in [group]',
'GROUP_EVENT_ADDRESS_MAP_URL'	=>	'https://www.google.com/maps/place/[address]',
'GROUP_EVENT_LOCATION_MAP_URL'	=>	'https://www.google.com/maps/search/[location]',
'ATTEND_9961c9'	=>	'Attend',
'THIS_EVENT_HAS_ENDED_48f27e'	=>	'This event has ended.',
'GROUP_EVENT_ENDS_IN'	=>	'This event is currently in progress and ends in [timeago].',
'THIS_EVENT_IS_CURRENTLY_IN_PROGRESS_558969'	=>	'This event is currently in progress.',
'GROUP_EVENT_STARTS_IN'	=>	'This event starts in [timeago].',
'GROUP_GUESTS_COUNT_LIMITED'	=>	'%%COUNT%% of [limit] Guest|%%COUNT%% of [limit] Guests',
'GROUP_GUESTS_COUNT'	=>	'%%COUNT%% Guest|%%COUNT%% Guests',
// 3 language strings from file cbgroupjiveevents/templates/default/attending.php
'NO_EVENT_GUEST_SEARCH_RESULTS_FOUND_ca6af0'	=>	'No event guest search results found.',
'THIS_EVENT_CURRENTLY_HAS_NO_GUESTS_207e61'	=>	'This event currently has no guests.',
'BACK_0557fa'	=>	'Back',
// 8 language strings from file cbgroupjiveevents/templates/default/event_edit.php
'EDIT_EVENT_6a11c1'	=>	'Edit Event',
'NEW_EVENT_842b2b'	=>	'New Event',
'EVENT_a4ecfc'	=>	'Event',
'START_DATE_db3794'	=>	'Start Date',
'END_DATE_3c1429'	=>	'End Date',
'GUEST_LIMIT_b0a150'	=>	'Guest Limit',
'UPDATE_EVENT_f126f4'	=>	'Update Event',
'SCHEDULE_EVENT_98fbce'	=>	'Schedule Event',
// 9 language strings from file cbgroupjiveevents/templates/default/events.php
'GROUP_EVENTS_COUNT'	=>	'%%COUNT%% Event|%%COUNT%% Events',
'AWAITING_APPROVAL_af6558'	=>	'Awaiting Approval',
'ARE_YOU_SURE_YOU_DO_NOT_WANT_TO_ATTEND_THIS_EVENT_c67ed0'	=>	'Are you sure you do not want to attend this Event?',
'UNATTEND_3534eb'	=>	'Unattend',
'APPROVE_6f7351'	=>	'Approve',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_EVENT_77b8b6'	=>	'Are you sure you want to unpublish this Event?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_EVENT_86f16e'	=>	'Are you sure you want to delete this Event?',
'NO_GROUP_EVENT_SEARCH_RESULTS_FOUND_aa3f23'	=>	'No group event search results found.',
'THIS_GROUP_CURRENTLY_HAS_NO_EVENTS_4bf235'	=>	'This group currently has no events.',
// 2 language strings from file cbgroupjiveevents/templates/default/message.php
'MESSAGE_GUESTS_a2a831'	=>	'Message Guests',
'SEND_MESSAGE_1432f3'	=>	'Send Message',
// 1 language strings from file cbgroupjiveevents/xml/controllers/frontcontroller.xml
'ATTENDANCE_6d1460'	=>	'Attendance',
);
