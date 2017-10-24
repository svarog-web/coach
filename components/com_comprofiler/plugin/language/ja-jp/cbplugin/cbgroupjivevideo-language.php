<?php
/**
* Community Builder (TM) cbgroupjivevideo Japanese (Japan) language file Frontend
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
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'個々のユーザグループごとに作成することに限定された、動画数を入力してください。空白の場合は、動画数が無制限になります。モデレータとグループの所有者は、この設定が免除されます。',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'グループの動画で、キャプチャの使用を有効または無効にします。最新の CB AntiSpam をインストールして公開する必要があります。モデレータは、この設定が免除されています。',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'新しい動画の公開',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'新しい動画は承認が必要です',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'ページングの使用を有効または無効にします。',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'ページの入力制限です。ページ制限は、ページごとに表示する行数を決定します。ページングが無効になっている場合、表示された行数を制限するために使用することができます。',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'列の検索の使用を有効または無効にします。',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'行の順序を選択してください。',
'DATE_ASC_a5871f'	=>	'日付昇順',
'DATE_DESC_bcfc6d'	=>	'日付降順',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'グループが存在しません。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'あなたは、このグループで動画を公開するための十分な権限がありません。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'あなたは、この動画を編集するための十分な権限がありません。',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'この動画の公開ステータスを選択してください。未公開の動画は、パブリックで表示されません。',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'必要に応じて、URLの代わりに表示する動画タイトルを入力してください。',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'公開する動画のURLを入力してください。',
'GROUP_VIDEO_LIMITS_EXT'	=>	'URL は [ext] である必要があります。',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'必要に応じて、動画のキャプションを入力してください。',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'動画の所有者IDを入力してください。動画の所有者は、ユーザIDとして指定された動画の作成者を決定します。',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'動画の保存に失敗しました！エラー : [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'Y ,M j',
'NEW_GROUP_VIDEO_28e07a'	=>	'新しいグループの動画',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'[user] はグループ [group] で動画 [video] を公開しました！',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'承認中の新しい動画',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'[user] はグループ [group] で動画 [video] を公開し、承認待ちになっています！',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'動画を正常に公開し保留中です！',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'動画を正常に公開しました！',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'動画を正常に保存しました！',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'あなたの動画は承認中です。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'あなたは、このグループで動画を非公開するための十分な権限がありません。',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'動画が存在しません。',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'動画のステータス保存に失敗しました！エラー : [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'動画公開の承認リクエスト',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'あなたの動画 [video] は、グループ [group] でリクエストをし承認されました！',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'動画のステータスを正常に保存しました！',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'あなたは、この動画を削除するための十分な権限がありません。',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'動画の削除に失敗しました！エラー : [error]',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'動画を正常に削除しました！',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'所有者が指定されていません！',
'URL_NOT_SPECIFIED_2ccd94'	=>	'URL が指定されていません！',
'GROUP_NOT_SPECIFIED_70267b'	=>	'グループが指定されていません！',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'グループが存在しません！',
'GROUP_VIDEO_INVALID_URL'	=>	'無効な URL です。URL が存在しているか確認してください！',
'GROUP_VIDEO_INVALID_EXT'	=>	'無効な URL の拡張子 [ext] です。[exts] のみリンクしてください！',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'動画',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'グループの新しい動画を追加',
'CONFIGURATION_254f64'	=>	'設定',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'無効',
'ENABLE_2faec1'	=>	'有効',
'ENABLE_WITH_APPROVAL_575b45'	=>	'承認を得て有効',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'必要に応じて、動画の使用を有効または無効にします。グループの所有者とグループ管理者は、この構成を免除され、常に動画を共有することができます。既存の動画はまだアクセスできることに注意してください。',
'DONT_NOTIFY_3ea23f'	=>	'通知なし',
'SEARCH_VIDEOS_e5b832'	=>	'動画検索...',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'[group] で動画を公開',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'動画を編集',
'NEW_VIDEO_458670'	=>	'新しい動画',
'VIDEO_34e2d1'	=>	'動画',
'CAPTION_272ba7'	=>	'キャプション',
'UPDATE_VIDEO_3e00c1'	=>	'動画を更新',
'PUBLISH_VIDEO_dc049f'	=>	'動画を公開',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% 動画|%%COUNT%% 動画',
'AWAITING_APPROVAL_af6558'	=>	'Awaiting Approval',
'APPROVE_6f7351'	=>	'承認',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'この動画を非公開にしてもよろしいですか？',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'この動画を削除してもよろしいですか？',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'グループの動画の検索結果は見つかりませんでした。',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'このグループには現在動画がありません。',
);
