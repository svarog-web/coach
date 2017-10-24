<?php
/**
* Community Builder (TM) cbgroupjivephoto Japanese (Japan) language file Frontend
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
// 20 language strings from file cbgroupjivephoto/cbgroupjivephoto.xml
'INPUT_NUMBER_OF_PHOTOS_EACH_INDIVIDUAL_USER_IS_LIM_11b810'	=>	'個々のユーザグループごとに作成を限定された写真数を入力してください。空白の場合は、写真数は無制限になります。モデレータとグループの所有者は、この設定が免除されています。',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_PHOTOS_0ea9f4'	=>	'グループの写真で、キャプチャの使用を有効または無効にします。最新の CB AntiSpam をインストールして公開する必要があります。モデレータは、この設定が免除されています。',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'アップロードしれた画像を常に再サンプリングする必要がある場合は選択してください。再サンプリングは、追加のセキュリティが追加されますが、アニメーションは ImageMagick を使用する際にのみ保持されます。',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'リサイズされる画像の最大の高さをピクセル単位で入力してください。',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'リサイズされる画像の最大の幅をピクセル単位で入力してください。',
'INPUT_THE_MAXIMUM_THUMBNAIL_HEIGHT_IN_PIXELS_THAT__9d2b57'	=>	'リサイズされる画像サムネイルの最大の高さをピクセル単位で入力してください。',
'INPUT_THE_MAXIMUM_THUMBNAIL_WIDTH_IN_PIXELS_THAT_T_d159f4'	=>	'リサイズされる画像サムネイルの最大の幅をピクセル単位で入力してください。',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'アップロードした画像はサイズ変更時、その縦横比を維持する必要がある場合に選択してください。画像を<strong>いいえ</strong>に設定した場合は、必ず指定された最大の幅と高さにリサイズされます。縦横比を<strong>はい</strong>に設定すると最大の幅と高さの内で可能な限り維持されます。常にアスペクト比内で指定した最大幅と高さにサイズを変更し、任意のオーバーフローをトリミングします。画像をトリミングで <strong>はい</strong> に設定した場合、二乗イメージを維持するために便利です。',
'INPUT_THE_MINIMUM_IMAGE_FILE_SIZE_IN_KBS_30eae6'	=>	'KB単位で画像ファイルの最小サイズを入力してください。',
'INPUT_THE_MAXIMUM_IMAGE_FILE_SIZE_IN_KBS_SET_TO_0__f73680'	=>	'KB単位で画像ファイルの最大サイズを入力してください。制限なしの場合は 0 に設定してください。',
'UPLOAD_OF_NEW_PHOTO_1831ae'	=>	'新しい写真のアップロード',
'NEW_PHOTO_REQUIRES_APPROVAL_d212d4'	=>	'新しい写真は承認が必要',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'ページングの使用を有効または無効にします。',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'ページの入力制限です。ページ制限は、ページごとに表示する行数を決定します。ページングが無効になっている場合、表示された行数を制限するために使用することができます。',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'列の検索の使用を有効または無効にします。',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'行の順序を選択してください。',
'DATE_ASC_a5871f'	=>	'日付昇順',
'DATE_DESC_bcfc6d'	=>	'日付降順',
'FILENAME_ASC_44f721'	=>	'ファイル名昇順',
'FILENAME_DESC_13d728'	=>	'ファイル名降順',
// 31 language strings from file cbgroupjivephoto/component.cbgroupjivephoto.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'グループが存在しません。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_c06972'	=>	'このグループの写真をアップロードする十分な権限がありません。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_6eccd3'	=>	'この写真を編集する十分な権限がありません。',
'SELECT_PUBLISH_STATE_OF_THIS_PHOTO_UNPUBLISHED_PHO_75ff3b'	=>	'この写真の公開ステータスを選択してください。未公開の写真は、パブリックに表示されません。',
'OPTIONALLY_INPUT_A_PHOTO_TITLE_TO_DISPLAY_INSTEAD__323e09'	=>	'必要に応じて、ファイル名の代わりに表示される写真のタイトルを入力してください。',
'SELECT_THE_PHOTO_TO_UPLOAD_8e29df'	=>	'アップロードする写真を選択してください。',
'GROUP_PHOTO_LIMITS_EXT'	=>	'写真は [ext] タイプである必要があります。',
'GROUP_PHOTO_LIMITS_MIN'	=>	'写真は [size] を超えている必要があります。',
'GROUP_PHOTO_LIMITS_MAX'	=>	'写真は [size] を超えてはなりません。',
'OPTIONALLY_INPUT_A_PHOTO_DESCRIPTION_d4c183'	=>	'必要に応じて、写真の説明を入力してください。',
'INPUT_THE_PHOTO_OWNER_ID_PHOTO_OWNER_DETERMINES_TH_eb7b03'	=>	'写真の所有者ID を入力してください。写真の所有者は、ユーザID として指定した写真の作成者を決定します。',
'GROUP_PHOTO_FAILED_TO_SAVE'	=>	'写真の保存に失敗しました！エラー : [error]',
'GROUP_PHOTO_DATE_FORMAT'	=>	'Y, M j',
'NEW_GROUP_PHOTO_9ba416'	=>	'新しいグループの写真',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_429420'	=>	'[user] はグループ [group] 内で写真 [photo] をアップロードしました！',
'NEW_GROUP_PHOTO_AWAITING_APPROVAL_221466'	=>	'新しいグループ写真の承認中',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_e90ac6'	=>	'[user] はグループ [group] 内で写真 [photo] をアップロードし、承認中です！',
'PHOTO_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_492ec3'	=>	'写真を正常にアップロードし保留中です！',
'PHOTO_UPLOADED_SUCCESSFULLY_0b02f3'	=>	'写真を正常にアップロードしました！',
'PHOTO_SAVED_SUCCESSFULLY_726f86'	=>	'写真を正常に保存しました！',
'YOUR_PHOTO_IS_AWAITING_APPROVAL_d5f199'	=>	'あなたの写真は、承認中です。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__dce5c6'	=>	'この写真を公開、または非公開にする十分な権限がありません。',
'PHOTO_DOES_NOT_EXIST_cd6623'	=>	'写真が存在しません。',
'GROUP_PHOTO_STATE_FAILED_TO_SAVE'	=>	'写真のステータス保存に失敗しました！エラー : [error]',
'PHOTO_UPLOAD_REQUEST_ACCEPTED_bf6572'	=>	'写真のアップロードリクエストの承認',
'YOUR_PHOTO_PHOTO_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_9728ad'	=>	'あなたのアップロードした写真 [photo] のリクエストがグループ [group] で承認されました！',
'PHOTO_STATE_SAVED_SUCCESSFULLY_2f8a03'	=>	'写真のステータスを正常に保存しました！',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8777b2'	=>	'この写真を削除する十分な権限がありません。',
'GROUP_PHOTO_FAILED_TO_DELETE'	=>	'写真の削除に失敗しました！エラー : [error]',
'PHOTO_DELETED_SUCCESSFULLY_a9f27f'	=>	'写真を正常に削除しました！',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_PHOTO_5ca855'	=>	'この写真にアクセスすることはできません。',
// 7 language strings from file cbgroupjivephoto/library/Table/PhotoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'所有者が指定されていません！',
'GROUP_NOT_SPECIFIED_70267b'	=>	'グループが指定されていません！',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'グループが存在しません。',
'GROUP_PHOTO_UPLOAD_INVALID_EXT'	=>	'無効な写真の拡張子 [ext] です。[exts] のみアップロードしてください！',
'GROUP_PHOTO_UPLOAD_TOO_SMALL'	=>	'写真が小さすぎます。最小値は [size] です！',
'GROUP_PHOTO_UPLOAD_TOO_LARGE'	=>	'写真サイズの最大値 [size] を超えました！',
'PHOTO_NOT_SPECIFIED_dd1bfc'	=>	'写真が指定されていません！',
// 3 language strings from file cbgroupjivephoto/library/Trigger/AdminTrigger.php
'PHOTOS_5daaf2'	=>	'写真',
'ADD_NEW_PHOTO_TO_GROUP_2df00d'	=>	'グループに新しい写真を追加',
'CONFIGURATION_254f64'	=>	'設定',
// 6 language strings from file cbgroupjivephoto/library/Trigger/PhotoTrigger.php
'DISABLE_bcfacc'	=>	'無効',
'ENABLE_2faec1'	=>	'有効',
'ENABLE_WITH_APPROVAL_575b45'	=>	'承認を得て有効',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_PHOTOS_GROUP_44b054'	=>	'必要に応じて、写真の使用を有効または無効にします。グループの所有者とグループ管理者は、この設定を免除され、常に写真をアップロードすることができます。既存の写真には、まだアクセスできることに注意してください。',
'DONT_NOTIFY_3ea23f'	=>	'通知なし',
'SEARCH_PHOTOS_e11345'	=>	'写真検索...',
// 2 language strings from file cbgroupjivephoto/templates/default/activity.php
'GROUP_PHOTO_ACTIVITY_TITLE'	=>	'[group] で写真をアップロード',
'ORIGINAL_0a52da'	=>	'オリジナル',
// 6 language strings from file cbgroupjivephoto/templates/default/photo_edit.php
'EDIT_PHOTO_68ffc9'	=>	'写真を編集',
'NEW_PHOTO_50a153'	=>	'新しい写真',
'PHOTO_c03d53'	=>	'写真',
'DESCRIPTION_b5a7ad'	=>	'説明',
'UPDATE_PHOTO_89bc50'	=>	'写真の更新',
'UPLOAD_PHOTO_05e477'	=>	'写真のアップロード',
// 7 language strings from file cbgroupjivephoto/templates/default/photos.php
'GROUP_PHOTOS_COUNT'	=>	'%%COUNT%% 写真|%%COUNT%% 写真',
'AWAITING_APPROVAL_af6558'	=>	'Awaiting Approval',
'APPROVE_6f7351'	=>	'承認',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_PHOTO_31f072'	=>	'この写真を非公開にしてもよろしいですか？',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PHOTO_4d3e2d'	=>	'この写真を削除してもよろしいですか？',
'NO_GROUP_PHOTO_SEARCH_RESULTS_FOUND_64adc0'	=>	'グループの写真の検索結果が見つかりません。',
'THIS_GROUP_CURRENTLY_HAS_NO_PHOTOS_8939ef'	=>	'このグループには、現在写真がありません。',
);
