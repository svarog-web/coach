<?php
/**
* Community Builder (TM) cbgroupjivefile Japanese (Japan) language file Frontend
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
'INPUT_NUMBER_OF_FILES_EACH_INDIVIDUAL_USER_IS_LIMI_db8dbc'	=>	'個々のユーザグループごとに作成を限定されたファイル数を入力してください。空白の場合は、ファイル数が無制限になります。モデレータは、この設定が免除されています。',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_FILES__cf15ea'	=>	'グループのファイルでキャプチャの使用を有効または無効にします。最新の CB AntiSpam をインストールして公開する必要があります。モデレータは、この設定が免除されています。',
'INPUT_THE_MINIMUM_FILE_SIZE_IN_KBS_f6c682'	=>	'KB単位で最小ファイルサイズを入力してください。',
'INPUT_THE_MAXIMUM_FILE_SIZE_IN_KBS_SET_TO_0_FOR_NO_58cb50'	=>	'KB単位でファイルの最大サイズを入力してください。無制限にする場合は 0 にセットしてください。',
'INPUT_THE_ALLOWED_FILE_EXTENSIONS_AS_A_COMMA_SEPAR_75447c'	=>	'許可したいファイルの拡張子を、カンマ区切りで入力してくだい。',
'FILE_TYPES_f12b42'	=>	'ファイルタイプ',
'UPLOAD_OF_NEW_FILE_6e6e69'	=>	'新しいファイルのアップロード',
'NEW_FILE_REQUIRES_APPROVAL_cef783'	=>	'新しいファイルは承認が必要',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'ページングの使用を有効または無効にします。',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'ページの入力制限です。ページ制限は、ページごとに表示する行数を決定します。ページングが無効になっている場合、表示された行数を制限するために使用することができます。',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'列の検索の使用を有効または無効にします。',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'行の順序を選択してください。',
'DATE_ASC_a5871f'	=>	'日付昇順',
'DATE_DESC_bcfc6d'	=>	'日付降順',
'FILENAME_ASC_44f721'	=>	'ファイル名昇順',
'FILENAME_DESC_13d728'	=>	'ファイル名で降順',
// 31 language strings from file cbgroupjivefile/component.cbgroupjivefile.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'グループが存在しません。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_050e48'	=>	'あなたは、このグループのファイルをアップロードできる十分な権限がありません。',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_338361'	=>	'あなたは、このファイルを編集する十分な権限がありません。',
'SELECT_PUBLISH_STATE_OF_THIS_FILE_UNPUBLISHED_FILE_4be1f3'	=>	'このファイルの公開ステータスを選択してください。未公開のファイルは、パブリックに表示されません。',
'OPTIONALLY_INPUT_A_FILE_TITLE_TO_DISPLAY_INSTEAD_O_b6523c'	=>	'必要に応じて、ファイル名の代わりに表示するファイルのタイトルを入力してください。',
'SELECT_THE_FILE_TO_UPLOAD_739b2a'	=>	'アップロードするファイルを選択してください。',
'GROUP_FILE_LIMITS_EXT'	=>	'ファイルは、[ext] タイプである必要があります。',
'GROUP_FILE_LIMITS_MIN'	=>	'ファイルは、[size] を超えている必要があります。',
'GROUP_FILE_LIMITS_MAX'	=>	'ファイルは、[size] を超えてはなりません。',
'OPTIONALLY_INPUT_A_FILE_DESCRIPTION_b5bf92'	=>	'必要に応じて、ファイルの説明を入力してください。',
'INPUT_THE_FILE_OWNER_ID_FILE_OWNER_DETERMINES_THE__8773c0'	=>	'ファイルの所有者ID を入力してください。ファイルの所有者は、ユーザID として指定されたファイルの作成者を決定します。',
'GROUP_FILE_FAILED_TO_SAVE'	=>	'ファイルの保存に失敗しました！エラー : [error]',
'GROUP_FILE_DATE_FORMAT'	=>	'Y, M j',
'NEW_GROUP_FILE_35b542'	=>	'新しいグループのファイル',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_db49b7'	=>	'[user] はグループ [group] 内にファイル [file] をアップロードしました！',
'NEW_GROUP_FILE_AWAITING_APPROVAL_498d56'	=>	'新しいグループファイルは承認中',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_37493d'	=>	'[user] はグループ [group] 内にファイル [file] をアップロードし、承認中です！',
'FILE_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_1e465f'	=>	'ファイルを正常にアップロードし保留中です！',
'FILE_UPLOADED_SUCCESSFULLY_2169cb'	=>	'ファイルを正常にアップロードしました！',
'FILE_SAVED_SUCCESSFULLY_c80b21'	=>	'ファイルを正常に保存しました！',
'YOUR_FILE_IS_AWAITING_APPROVAL_dabe41'	=>	'ファイルは承認中です',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__fbef44'	=>	'あなたは、このファイルを公開、または非公開する十分な権限がありません。',
'FILE_DOES_NOT_EXIST_50db35'	=>	'ファイルが存在しません。',
'GROUP_FILE_STATE_FAILED_TO_SAVE'	=>	'ファイルのステータス保存に失敗しました！エラー : [error]',
'FILE_UPLOAD_REQUEST_ACCEPTED_623624'	=>	'ファイルアップロードの承認リクエスト',
'YOUR_FILE_FILE_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_H_80bbf8'	=>	'グループ [group] でファイル [file] のアップロードリクエストが承認されました！',
'FILE_STATE_SAVED_SUCCESSFULLY_cc3caf'	=>	'ファイルのステータスを正常に保存しました！',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_35a6cc'	=>	'あなたは、このファイルを削除する十分な権限がありません。',
'GROUP_FILE_FAILED_TO_DELETE'	=>	'ファイルの削除に失敗しました！エラー : [error]',
'FILE_DELETED_SUCCESSFULLY_5ea0ed'	=>	'ファイルを正常に削除しました！',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_FILE_d7c056'	=>	'あなたは、このファイルにアクセスする権限がありません。',
// 8 language strings from file cbgroupjivefile/library/Table/FileTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'所有者が指定されていません！',
'GROUP_NOT_SPECIFIED_70267b'	=>	'グループが指定されていません！',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'グループが存在しません！',
'GROUP_FILE_UPLOAD_INVALID_EXT'	=>	'無効なファイルの拡張子 [ext] です。[exts] のみアップロードしてください！',
'GROUP_FILE_UPLOAD_TOO_SMALL'	=>	'ファイルが小さすぎます。最小値は [size] です！',
'GROUP_FILE_UPLOAD_TOO_LARGE'	=>	'ファイルサイズが最大値の [size] を超えました！',
'FILE_NOT_SPECIFIED_93ec32'	=>	'ファイルが指定されていません！',
'GROUP_FILE_UPLOAD_FAILED'	=>	'ファイル [file] のアップロードに失敗しました！',
// 3 language strings from file cbgroupjivefile/library/Trigger/AdminTrigger.php
'FILES_91f3a2'	=>	'ファイル',
'ADD_NEW_FILE_TO_GROUP_518801'	=>	'グループに新しいファイルを追加',
'CONFIGURATION_254f64'	=>	'設定',
// 6 language strings from file cbgroupjivefile/library/Trigger/FileTrigger.php
'DISABLE_bcfacc'	=>	'無効',
'ENABLE_2faec1'	=>	'有効',
'ENABLE_WITH_APPROVAL_575b45'	=>	'承認を得て有効',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_FILES_GROUP__3216b3'	=>	'必要に応じて、ファイルの使用を有効または無効にします。グループの所有者とグループ管理者は、この設定を免除され、常にファイルをアップロードすることができます。既存のファイルは、引き続きアクセスできることに注意してください。',
'DONT_NOTIFY_3ea23f'	=>	'通知なし',
'SEARCH_FILES_ec9e5b'	=>	'ファイル検索...',
// 5 language strings from file cbgroupjivefile/templates/default/activity.php
'GROUP_FILE_ACTIVITY_TITLE'	=>	'[group] でファイルをアップロード',
'TYPE_a1fa27'	=>	'タイプ',
'SIZE_6f6cb7'	=>	'サイズ',
'CLICK_TO_DOWNLOAD_26f519'	=>	'ダウンロードするにはクリック',
'UNKNOWN_88183b'	=>	'不明',
// 6 language strings from file cbgroupjivefile/templates/default/file_edit.php
'EDIT_FILE_29e095'	=>	'ファイルを編集',
'NEW_FILE_10716b'	=>	'新しいファイル',
'FILE_0b2791'	=>	'ファイル',
'DESCRIPTION_b5a7ad'	=>	'説明',
'UPDATE_FILE_e9812b'	=>	'ファイルの更新',
'UPLOAD_FILE_fbb7d7'	=>	'ファイルのアップロード',
// 7 language strings from file cbgroupjivefile/templates/default/files.php
'GROUP_FILES_COUNT'	=>	'%%COUNT%% ファイル|%%COUNT%% ファイル',
'AWAITING_APPROVAL_af6558'	=>	'Awaiting Approval',
'APPROVE_6f7351'	=>	'承認',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_FILE_babc72'	=>	'ファイルを非公開にしてもよろしいですか？',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_FILE_494de2'	=>	'ファイルを削除してもよろしいですか？',
'NO_GROUP_FILE_SEARCH_RESULTS_FOUND_6609b5'	=>	'グループのファイルの検索結果が見つかりませんでした。',
'THIS_GROUP_CURRENTLY_HAS_NO_FILES_f0b8c6'	=>	'このグループには、現在ファイルがありません。',
);
