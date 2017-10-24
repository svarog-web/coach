<?php
/**
* Community Builder (TM) cbgroupjivewall Japanese (Japan) language file Frontend
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
// 13 language strings from file cbgroupjivewall/cbgroupjivewall.xml
'INPUT_NUMBER_OF_CHARACTERS_PER_WALL_POST_IF_BLANK__9b7baf'	=>	'ウォールで、一人あたりの文字制限数を入力してください。空白の場合は、文字数が無制限になります。これは、ウォールの返信分も含まれます。モデレータとグループの所有者は、この設定が免除されています。',
'CHARACTER_LIMIT_52c66f'	=>	'文字数制限',
'CREATE_OF_NEW_POST_740891'	=>	'新しい投稿を作成',
'NEW_POST_REQUIRES_APPROVAL_9310c5'	=>	'新しい投稿には承認が必要',
'USER_REPLY_TO_MY_EXISTING_POSTS_0f7c63'	=>	'私の既存投稿にユーザの返信',
'ENABLE_OR_DISABLE_USAGE_OF_WALL_REPLIES_307e20'	=>	'ウォール返信の使用を有効または無効にします。',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'ページングの使用を有効または無効にします。',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'ページの入力制限です。ページ制限は、ページごとに表示する行数を決定します。ページングが無効になっている場合、表示された行数を制限するために使用することができます。',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'行の順序を選択してください。',
'DATE_ASC_a5871f'	=>	'日付昇順',
'DATE_DESC_bcfc6d'	=>	'日付降順',
'REPLIES_ASC_956301'	=>	'返信昇順',
'REPLIES_DESC_3adf62'	=>	'返信降順',
// 27 language strings from file cbgroupjivewall/component.cbgroupjivewall.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'グループが存在しません。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_POST_IN__66053b'	=>	'このグループに投稿する十分な権限がありません。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_580f05'	=>	'この投稿を編集する権限がありません。',
'SELECT_PUBLISH_STATE_OF_THIS_POST_UNPUBLISHED_POST_25e4c1'	=>	'この投稿の公開ステータスを選択してください。非公開の投稿は、パブリックに表示されません。',
'INPUT_THE_POST_TO_SHARE_f29e91'	=>	'共有の投稿を入力してください',
'INPUT_THE_POST_OWNER_ID_POST_OWNER_DETERMINES_THE__e4515b'	=>	'投稿の所有者ID を入力してください。投稿の所有者は、ユーザID として指定された投稿の作成者を決定します。',
'REPLY_DOES_NOT_EXIST_75f6be'	=>	'返信が存在しません。',
'GROUP_POST_FAILED_TO_SAVE'	=>	'投稿の保存に失敗しました！エラー : [error]',
'NEW_GROUP_POST_REPLY_71f88d'	=>	'新しいグループ投稿の返信',
'USER_HAS_POSTED_A_REPLY_ON_THE_WALL_IN_THE_GROUP_G_dbfbf9'	=>	'[user] はグループ [group] でウォールに返信を投稿しました！',
'NEW_GROUP_POST_098c50'	=>	'新しいグループの投稿',
'USER_HAS_POSTED_ON_THE_WALL_IN_THE_GROUP_GROUP_3b88bf'	=>	'[user] はグループ [group] でウォールに投稿しました！',
'NEW_GROUP_POST_AWAITING_APPROVAL_f17edd'	=>	'新しいグループの投稿が承認中',
'USER_HAS_POSTED_ON_THE_WALL_IN_THE_GROUP_GROUP_AND_634c08'	=>	'[user] はグループ [group] でウォールに投稿し、承認中です！',
'POSTED_SUCCESSFULLY_AND_AWAITING_APPROVAL_b7adff'	=>	'正常に投稿し保留中です！',
'POSTED_SUCCESSFULLY_dc026f'	=>	'正常に投稿しました！',
'POST_SAVED_SUCCESSFULLY_df8d13'	=>	'正常に投稿を保存しました！',
'YOUR_POST_IS_AWAITING_APPROVAL_6636bb'	=>	'あなたの投稿は、承認中です。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__ab5489'	=>	'この投稿を公開または非公開にする権限がありません。',
'POST_DOES_NOT_EXIST_048824'	=>	'投稿が存在しません。',
'GROUP_POST_STATE_FAILED_TO_SAVE'	=>	'投稿のステータス保存に失敗しました！エラー : [error]',
'WALL_POST_REQUEST_ACCEPTED_582538'	=>	'ウォールの投稿リクエストの承認',
'YOUR_WALL_POST_REQUEST_IN_THE_GROUP_GROUP_HAS_BEEN_55d796'	=>	'グループ [group] で、あなたのウォール投稿のリクエストが承認されました！',
'POST_STATE_SAVED_SUCCESSFULLY_81b542'	=>	'正常に投稿のステータスを保存しました！',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_339ee6'	=>	'この投稿を削除する権限がありません。',
'GROUP_POST_FAILED_TO_DELETE'	=>	'投稿の削除に失敗しました！エラー : [error]',
'POST_DELETED_SUCCESSFULLY_d8ccca'	=>	'正常に投稿を削除しました！',
// 5 language strings from file cbgroupjivewall/library/Table/WallTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'所有者が指定されていません！',
'GROUP_NOT_SPECIFIED_70267b'	=>	'グループが指定されていません！',
'POST_NOT_SPECIFIED_d7239e'	=>	'投稿が指定されていません！',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'グループが存在しません！',
'REPLY_DOES_NOT_EXIST_84b465'	=>	'返信が存在しません！',
// 3 language strings from file cbgroupjivewall/library/Trigger/AdminTrigger.php
'WALL_94e8a4'	=>	'ウォール',
'ADD_NEW_POST_TO_GROUP_b327ed'	=>	'グループに新しい投稿を追加',
'CONFIGURATION_254f64'	=>	'設定',
// 5 language strings from file cbgroupjivewall/library/Trigger/WallTrigger.php
'DISABLE_bcfacc'	=>	'無効',
'ENABLE_2faec1'	=>	'有効',
'ENABLE_WITH_APPROVAL_575b45'	=>	'承認を得て有効',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_THE_WALL_GRO_366897'	=>	'必要に応じて、ウォールの使用を有効または無効にします。グループの所有者とグループ管理者は、この設定を免除され、常に投稿することができます。既存の投稿には、まだアクセスできる事に注意してください。',
'DONT_NOTIFY_3ea23f'	=>	'通知なし',
// 1 language strings from file cbgroupjivewall/templates/default/activity.php
'GROUP_WALL_ACTIVITY_TITLE'	=>	'[group] に投稿',
// 6 language strings from file cbgroupjivewall/templates/default/replies.php
'AWAITING_APPROVAL_af6558'	=>	'Awaiting Approval',
'APPROVE_6f7351'	=>	'承認',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_POST_ed089a'	=>	'この投稿を非公開にしてもよろしいですか？',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_POST_200804'	=>	'この投稿を削除してもよろしいですか？',
'WRITE_A_REPLY_a6697c'	=>	'返信する...',
'REPLY_25d8df'	=>	'返信',
// 3 language strings from file cbgroupjivewall/templates/default/wall.php
'HAVE_A_POST_TO_SHARE_0bfd1f'	=>	'共有する投稿はありますか？',
'POST_03d947'	=>	'投稿',
'THIS_GROUP_CURRENTLY_HAS_NO_POSTS_533e74'	=>	'このグループには、現在投稿がありません。',
// 3 language strings from file cbgroupjivewall/templates/default/wall_edit.php
'EDIT_POST_17ebfc'	=>	'投稿を編集',
'NEW_POST_43eabe'	=>	'新しい投稿',
'UPDATE_POST_b9935d'	=>	'投稿の更新',
);
