<?php
/**
* Community Builder (TM) cbgroupjivevideo Russian (Russia) language file Frontend
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
// 10 language strings from file cbgroupjivevideo/cbgroupjivevideo.xml
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'Input number of videos each individual user is limited to creating per group. If blank allow unlimited videos. Moderators and group owners are exempt from this configuration.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'Enable or disable usage of captcha on group videos. Requires latest CB AntiSpam to be installed and published. Moderators are exempt from this configuration.',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'Publish of new video',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'New video requires approval',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Включить или выключить использование постраничной разбивки.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Введите максимальный размер страницы. Размер страницы обозначает количество строк, отображаемых на странице. Если постраничная разбивка не активирована, этот параметр все равно может использоваться для ограничения количества отображаемых строк.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Включить или выключить использование поиска рядов.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Выберите порядок рядов.',
'DATE_ASC_a5871f'	=>	'Дата ВОСХ',
'DATE_DESC_bcfc6d'	=>	'Дата НИСХ',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Такой группы не существует.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'You do not have sufficient permissions to publish a video in this group.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'You do not have sufficient permissions to edit this video.',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'Select publish state of this video. Unpublished videos will not be visible to the public.',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'Optionally input a video title to display instead of url.',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'Input the URL to the video to publish.',
'GROUP_VIDEO_LIMITS_EXT'	=>	'Your url must be of [ext] type.',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'Optionally input a video caption.',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'Input the video owner id. Video owner determines the creator of the video specified as User ID.',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'Video failed to save! Error: [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_VIDEO_28e07a'	=>	'New group video',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'[user] has published the video [video] in the group [group]!',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'New group video awaiting approval',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'[user] has published the video [video] in the group [group] and is awaiting approval!',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'Video published successfully and awaiting approval!',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'Video published successfully!',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'Video saved successfully!',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'Your video is awaiting approval.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'You do not have sufficient permissions to publish or unpublish this video.',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'Video does not exist.',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'Video state failed to saved. Error: [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'Video publish request accepted',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'Your video [video] publish request in the group [group] has been accepted!',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'Video state saved successfully!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'You do not have sufficient permissions to delete this video.',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'Video failed to delete. Error: [error]',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'Video deleted successfully!',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Owner not specified!',
'URL_NOT_SPECIFIED_2ccd94'	=>	'URL not specified!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Group not specified!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Group does not exist!',
'GROUP_VIDEO_INVALID_URL'	=>	'Invalid URL. Please ensure the URL exists!',
'GROUP_VIDEO_INVALID_EXT'	=>	'Invalid url extension [ext]. Please link only [exts]!',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'Videos',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'Add New Video to Group',
'CONFIGURATION_254f64'	=>	'Конфигурация',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'Выключить',
'ENABLE_2faec1'	=>	'Включить',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Включить, с одобрением',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'Optionally enable or disable usage of videos. Group owner and group administrators are exempt from this configuration and can always share videos. Note existing videos will still be accessible.',
'DONT_NOTIFY_3ea23f'	=>	'Не уведомлять',
'SEARCH_VIDEOS_e5b832'	=>	'Search Videos...',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'published a video in [group]',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'Edit Video',
'NEW_VIDEO_458670'	=>	'New Video',
'VIDEO_34e2d1'	=>	'Video',
'CAPTION_272ba7'	=>	'Caption',
'UPDATE_VIDEO_3e00c1'	=>	'Update Video',
'PUBLISH_VIDEO_dc049f'	=>	'Publish Video',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% Video|%%COUNT%% Videos',
'AWAITING_APPROVAL_af6558'	=>	'Ожидает рассмотрения',
'APPROVE_6f7351'	=>	'Одобрить',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'Are you sure you want to unpublish this Video?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'Are you sure you want to delete this Video?',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'No group video search results found.',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'This group currently has no videos.',
);
