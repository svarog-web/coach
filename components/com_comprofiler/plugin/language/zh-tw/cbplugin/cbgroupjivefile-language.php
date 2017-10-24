<?php
/**
* Community Builder (TM) cbgroupjivefile Chinese (Taiwan) language file Frontend
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
// 16 language strings from file cbgroupjivefile/cbgroupjivefile.xml
'INPUT_NUMBER_OF_FILES_EACH_INDIVIDUAL_USER_IS_LIMI_db8dbc'	=>	'Input number of files each individual user is limited to creating per group. If blank allow unlimited files. Moderators and group owners are exempt from this configuration.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_FILES__cf15ea'	=>	'Enable or disable usage of captcha on group files. Requires latest CB AntiSpam to be installed and published. Moderators are exempt from this configuration.',
'INPUT_THE_MINIMUM_FILE_SIZE_IN_KBS_f6c682'	=>	'Input the minimum file size in KBs.',
'INPUT_THE_MAXIMUM_FILE_SIZE_IN_KBS_SET_TO_0_FOR_NO_58cb50'	=>	'Input the maximum file size in KBs. Set to 0 for no limit.',
'INPUT_THE_ALLOWED_FILE_EXTENSIONS_AS_A_COMMA_SEPAR_75447c'	=>	'Input the allowed file extensions as a comma separated list.',
'FILE_TYPES_f12b42'	=>	'File Types',
'UPLOAD_OF_NEW_FILE_6e6e69'	=>	'Upload of new file',
'NEW_FILE_REQUIRES_APPROVAL_cef783'	=>	'New file requires approval',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'啓用或關閉頁碼',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Input page limit. Page limit determines how many rows are displayed per page. If paging is disabled this can still be used to limit the number of rows displayed.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Enable or disable usage of search on rows.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Select the row ordering.',
'DATE_ASC_a5871f'	=>	'Date ASC',
'DATE_DESC_bcfc6d'	=>	'Date DESC',
'FILENAME_ASC_44f721'	=>	'Filename ASC',
'FILENAME_DESC_13d728'	=>	'Filename DESC',
// 31 language strings from file cbgroupjivefile/component.cbgroupjivefile.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Group does not exist.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_050e48'	=>	'You do not have sufficient permissions to upload a file in this group.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_338361'	=>	'You do not have sufficient permissions to edit this file.',
'SELECT_PUBLISH_STATE_OF_THIS_FILE_UNPUBLISHED_FILE_4be1f3'	=>	'Select publish state of this file. Unpublished files will not be visible to the public.',
'OPTIONALLY_INPUT_A_FILE_TITLE_TO_DISPLAY_INSTEAD_O_b6523c'	=>	'Optionally input a file title to display instead of filename.',
'SELECT_THE_FILE_TO_UPLOAD_739b2a'	=>	'Select the file to upload.',
'GROUP_FILE_LIMITS_EXT'	=>	'Your file must be of [ext] type.',
'GROUP_FILE_LIMITS_MIN'	=>	'Your file should exceed [size].',
'GROUP_FILE_LIMITS_MAX'	=>	'Your file should not exceed [size].',
'OPTIONALLY_INPUT_A_FILE_DESCRIPTION_b5bf92'	=>	'Optionally input a file description.',
'INPUT_THE_FILE_OWNER_ID_FILE_OWNER_DETERMINES_THE__8773c0'	=>	'Input the file owner id. File owner determines the creator of the file specified as User ID.',
'GROUP_FILE_FAILED_TO_SAVE'	=>	'File failed to save! Error: [error]',
'GROUP_FILE_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_FILE_35b542'	=>	'New group file',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_db49b7'	=>	'[user] has uploaded the file [file] in the group [group]!',
'NEW_GROUP_FILE_AWAITING_APPROVAL_498d56'	=>	'New group file awaiting approval',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_37493d'	=>	'[user] has uploaded the file [file] in the group [group] and is awaiting approval!',
'FILE_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_1e465f'	=>	'File uploaded successfully and awaiting approval!',
'FILE_UPLOADED_SUCCESSFULLY_2169cb'	=>	'File uploaded successfully!',
'FILE_SAVED_SUCCESSFULLY_c80b21'	=>	'File saved successfully!',
'YOUR_FILE_IS_AWAITING_APPROVAL_dabe41'	=>	'Your file is awaiting approval.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__fbef44'	=>	'You do not have sufficient permissions to publish or unpublish this file.',
'FILE_DOES_NOT_EXIST_50db35'	=>	'File does not exist.',
'GROUP_FILE_STATE_FAILED_TO_SAVE'	=>	'File state failed to saved. Error: [error]',
'FILE_UPLOAD_REQUEST_ACCEPTED_623624'	=>	'File upload request accepted',
'YOUR_FILE_FILE_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_H_80bbf8'	=>	'Your file [file] upload request in the group [group] has been accepted!',
'FILE_STATE_SAVED_SUCCESSFULLY_cc3caf'	=>	'File state saved successfully!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_35a6cc'	=>	'You do not have sufficient permissions to delete this file.',
'GROUP_FILE_FAILED_TO_DELETE'	=>	'File failed to delete. Error: [error]',
'FILE_DELETED_SUCCESSFULLY_5ea0ed'	=>	'File deleted successfully!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_FILE_d7c056'	=>	'You do not have access to this file.',
// 8 language strings from file cbgroupjivefile/library/Table/FileTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Owner not specified!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Group not specified!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Group does not exist!',
'GROUP_FILE_UPLOAD_INVALID_EXT'	=>	'Invalid file extension [ext]. Please upload only [exts]!',
'GROUP_FILE_UPLOAD_TOO_SMALL'	=>	'The file is too small, the minimum is [size]!',
'GROUP_FILE_UPLOAD_TOO_LARGE'	=>	'The file size exceeds the maximum of [size]!',
'FILE_NOT_SPECIFIED_93ec32'	=>	'File not specified!',
'GROUP_FILE_UPLOAD_FAILED'	=>	'The file [file] failed to upload!',
// 3 language strings from file cbgroupjivefile/library/Trigger/AdminTrigger.php
'FILES_91f3a2'	=>	'Files',
'ADD_NEW_FILE_TO_GROUP_518801'	=>	'Add New File to Group',
'CONFIGURATION_254f64'	=>	'設置',
// 6 language strings from file cbgroupjivefile/library/Trigger/FileTrigger.php
'DISABLE_bcfacc'	=>	'Disable',
'ENABLE_2faec1'	=>	'Enable',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Enable, with Approval',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_FILES_GROUP__3216b3'	=>	'Optionally enable or disable usage of files. Group owner and group administrators are exempt from this configuration and can always upload files. Note existing files will still be accessible.',
'DONT_NOTIFY_3ea23f'	=>	'Don\'t Notify',
'SEARCH_FILES_ec9e5b'	=>	'Search Files...',
// 5 language strings from file cbgroupjivefile/templates/default/activity.php
'GROUP_FILE_ACTIVITY_TITLE'	=>	'uploaded a file in [group]',
'TYPE_a1fa27'	=>	'類型',
'SIZE_6f6cb7'	=>	'Size',
'CLICK_TO_DOWNLOAD_26f519'	=>	'Click to Download',
'UNKNOWN_88183b'	=>	'Unknown',
// 6 language strings from file cbgroupjivefile/templates/default/file_edit.php
'EDIT_FILE_29e095'	=>	'Edit File',
'NEW_FILE_10716b'	=>	'New File',
'FILE_0b2791'	=>	'File',
'DESCRIPTION_b5a7ad'	=>	'描述',
'UPDATE_FILE_e9812b'	=>	'Update File',
'UPLOAD_FILE_fbb7d7'	=>	'Upload File',
// 7 language strings from file cbgroupjivefile/templates/default/files.php
'GROUP_FILES_COUNT'	=>	'%%COUNT%% File|%%COUNT%% Files',
'AWAITING_APPROVAL_af6558'	=>	'Awaiting Approval',
'APPROVE_6f7351'	=>	'批准',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_FILE_babc72'	=>	'Are you sure you want to unpublish this File?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_FILE_494de2'	=>	'Are you sure you want to delete this File?',
'NO_GROUP_FILE_SEARCH_RESULTS_FOUND_6609b5'	=>	'No group file search results found.',
'THIS_GROUP_CURRENTLY_HAS_NO_FILES_f0b8c6'	=>	'This group currently has no files.',
);
