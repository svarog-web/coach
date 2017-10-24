<?php
/**
* Community Builder (TM) cbinvites Japanese (Japan) language file Frontend
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
'JOIN_ME_de0c95'	=>	'仲間になりませんか！',
'MAILER_FAILED_TO_SEND_46f543'	=>	'メーラーは送信できませんでした。',
'TO_ADDRESS_MISSING_225871'	=>	'宛先のアドレスが不足しています。',
'SUBJECT_MISSING_8e0db8'	=>	'件名が不足しています。',
'BODY_MISSING_b6f835'	=>	'本文が不足しています。',
'SEARCH_INVITES_9e5f33'	=>	'招待検索...',
'INVITE_CODE_NOT_VALID_7cd6f7'	=>	'招待コードが有効ではありません。',
'INVITE_CODE_ALREADY_USED_cd715c'	=>	'招待コードは既に使用されています。',
'INVITE_CODE_IS_VALID_96aad3'	=>	'招待コードは有効です。',
// 44 language strings from file plug_cbinvites/cbinvites.xml
'YOUR_REGISTRATION_INVITE_CODE_324b31'	=>	'あなたの登録招待コードです。',
'INVITE_CODE_0a2eb0'	=>	'招待コード',
'INVITES_213b86'	=>	'招待',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_ON_TAB_INVITES_b76bc2'	=>	'招待タブのページングの使用を有効または無効にします。',
'INPUT_PAGE_LIMIT_ON_TAB_INVITES_PAGE_LIMIT_DETERMI_b17959'	=>	'招待タブのページ制限を入力してください。ページ制限は、ページごとに、どのくらい招待を表示するか決定します。',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_TAB_INVITES_dc5e0c'	=>	'招待タブの検索の使用を有効または無効にします。',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_INVITES_I_e3fe43'	=>	'選択したテンプレートは CB 招待すべてで使用されます。テンプレートが不完全な場合は、不足しているファイルを、デフォルトのテンプレートから使用します。テンプレートファイルは次の場所に配置することができます : components/com_comprofiler/plugin/user/plug_cbinvites/templates/',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_7cf91e'	=>	'必要に応じて、CB 招待のすべてを包む DIV 周囲のクラス接尾辞を追加します。',
'SELECT_INVITE_CREATE_ACCESS_ACCESS_DETERMINES_WHO__c4a59a'	=>	'アクセスの招待作成を選択してください。アクセスは、招待を送信できるユーザを決定します。その上で、それらと同様に選択したグループがアクセス (例 : 登録はまた、作者にアクセス可能になります) できる必要があります。モデレータは、この設定を免除されています。',
'INPUT_NUMBER_OF_INVITES_EACH_INDIVIDUAL_USER_IS_LI_0d3553'	=>	'招待の入力数は、個々のユーザが任意の時点 (承諾された招待は、この制限にカウントされません) でアクティブを有するように制限されます。空白の場合は、招待の制限を無制限にします。モデレータは、この設定が免除されています。',
'INPUT_NUMBER_OF_DAYS_AFTER_INVITE_SENT_TO_ALLOW_RE_12681b'	=>	'日の入力数は、送信された招待の後で再送信 (承諾された招待は再送信は許可されません) を可能にします。空白の場合は、再送信を無効にします。モデレータは、この設定が免除されています。',
'RESEND_DELAY_4f8afe'	=>	'再送信の遅延',
'SELECT_USAGE_OF_MULTIPLE_EMAILS_IN_A_SINGLE_INVITE_e8af1e'	=>	'単一の招待で複数のメールを選択し使用する場合は、カンマ区切りのリスト (例 : email1@domain.com, email2@domain.com, email3@domain.com) を使用してください。モデレータは、この設定が免除されています。',
'MULTIPLE_INVITES_b8f6fd'	=>	'複数の招待',
'SELECT_USAGE_OF_DUPLICATE_INVITES_TO_THE_SAME_ADDR_96bfd8'	=>	'重複の用法を選択し、同じアドレスに招待します。モデレータは、この設定が免除されています。',
'DUPLICATE_INVITES_9b3f9e'	=>	'重複の招待',
'SELECT_CONNECTION_METHOD_FROM_INVITER_TO_INVITEE_32692b'	=>	'招待から選択した接続方法で招待します。',
'CONNECTION_c2cc70'	=>	'コネクション',
'PENDING_CONNECTION_30ec76'	=>	'保留中のコネクション',
'AUTO_CONNECTION_5f8c15'	=>	'自動コネクション',
'SELECT_INVITE_EMAIL_BODY_EDITOR_d8f5e1'	=>	'選択した招待のメール本文の編集者',
'PLAIN_TEXT_e44b14'	=>	'プレーンテキスト',
'HTML_TEXT_503c11'	=>	'HTML テキスト',
'WYSIWYG_fcf0d4'	=>	'WYSIWYG',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_INVITES_REQU_65e72e'	=>	'Enable or disable usage of captcha on invites. Requires latest CB AntiSpam to be installed and published. Moderators are exempt from this configuration.',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_f0ef2c'	=>	'名前からサポートされている入力の置換は、すべての招待状 (例 : 私の素晴らしい CB サイト！) を送信します。左が空白の場合は、デフォルトでユーザ名にります。指定された場合は、返信先の名前にユーザ名として追加されます。',
'FROM_NAME_4a4a8f'	=>	'差出人名',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_ef3c3b'	=>	'入力したアドレスの置換サポートは、すべての招待 (例 : general@domain.com) を送信します。左が空白の場合は、デフォルトでユーザのメールにります。指定された場合は返信先アドレスが、ユーザのメールとして追加されます。',
'FROM_ADDRESS_a5ab7d'	=>	'差出人アドレス',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'置換をサポートした CC アドレス (例 : [email]) を入力してください。複数のアドレスは、カンマ区切りのリスト (例 : email1@domain.com, email2@domain.com, email3@domain.com) を使用がサポートされています。',
'CC_ADDRESS_b6327b'	=>	'CC アドレス',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'置換をサポートした BCC アドレス (例 : [email]) を入力してください。複数のアドレスは、カンマ区切りのリスト (例 : email1@domain.com, email2@domain.com, email3@domain.com) を使用がサポートされています。',
'BCC_ADDRESS_33b728'	=>	'BBC アドレス',
'INPUT_SUBSTITUTION_SUPPORTED_PREFIX_OF_INVITE_EMAI_91cd80'	=>	'置換をサポートした、メールの件名で招待の接頭辞を入力してください。追加の置換サポート : [site], [sitename], [path], [itemid], [register], [profile], [code], および [to] になります。',
'SUBJECT_PREFIX_175911'	=>	'件名の接頭辞',
'SITENAME_6b68ee'	=>	'[sitename] - ',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_OF_INVITE_EMAI_058012'	=>	'置換をサポートした、メールの本文で招待のヘッダを入力してください。追加の置換サポート : [site], [sitename], [path], [itemid], [register], [profile], [code], および [to] になります。',
'BODY_HEADER_67622c'	=>	'本文のヘッダ',
'YOU_HAVE_BEEN_INVITED_BY_USERNAME_TO_JOIN_SITENAME_e90186'	=>	'<p>You have been invited by [username] to join [sitename]!</p><br>',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_OF_INVITE_EMAI_f5fa57'	=>	'置換をサポートした、メールの本文で招待のフッタを入力してください。追加の置換サポート : [site], [sitename], [path], [itemid], [register], [profile], [code], および [to] になります。',
'BODY_FOOTER_6046e1'	=>	'本文のフッタ',
'INVITE_CODE_CODESITENAME_SITEREGISTRATION_REGISTER_eec8c6'	=>	'<br><p>Invite Code - [code]<br>[sitename] - [site]<br>Registration - [register]<br>[username] - [profile]</p>',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__523216'	=>	'置換をサポートした、添付ファイルのアドレス (例 : [cb_myfile]) を入力してください。サポートされた複数のアドレスは、カンマ区切りのリスト (例 : /home/username/public_html/images/file1.zip,[path]/file2.zip, http://www.domain.com/file3.zip) を使用してください。追加の置換サポート : [site], [sitename], [path], [itemid], [register], [profile], [code], および [to] になります。',
'ATTACHMENT_e9cb21'	=>	'添付',
// 22 language strings from file plug_cbinvites/component.cbinvites.php
'INPUT_INVITE_EMAIL_TO_ADDRESS_SEPARATE_MULTIPLE_EM_2028c1'	=>	'アドレスに招待メールを入力してください。メールアドレスが複数の場合は、カンマ区切りで指定してください。',
'INPUT_INVITE_EMAIL_TO_ADDRESS_54551f'	=>	'アドレスに招待メールを入力してください。',
'INPUT_INVITE_EMAIL_SUBJECT_IF_LEFT_BLANK_A_SUBJECT_0974fc'	=>	'招待メールの件名を入力してください。空白のままの場合は、件名が適用されます。',
'OPTIONALLY_INPUT_PRIVATE_MESSAGE_TO_INCLUDE_WITH_I_e1d750'	=>	'必要に応じて、招待メールに含めるプライベートメッセージを入力してください。',
'INPUT_OWNER_OF_INVITE_AS_SINGLE_INTEGER_USERID_THI_998c63'	=>	'単一の整数 user_id として招待の所有者を入力してください。これは、招待を送信したユーザになります。',
'OPTIONALLY_INPUT_USER_OF_INVITE_AS_SINGLE_INTEGER__329e71'	=>	'必要に応じて、単一の整数 user_id として招待のユーザを入力してください。これは、招待を承諾したユーザになります。',
'COMMA_SEPERATED_LISTS_ARE_NOT_SUPPORTED_PLEASE_USE_7676e6'	=>	'カンマ区切りのリストはサポートされていません！1 つの宛先アドレスを使用してください。',
'INVITE_LIMIT_REACHED_1e1e31'	=>	'招待は制限に達しました！',
'TO_ADDRESS_NOT_SPECIFIED_e292c0'	=>	'宛先アドレスが指定されていません！',
'INVITE_TO_ADDRESS_INVALID'	=>	'宛先アドレスが有効ではありません : [to_address]',
'YOU_CAN_NOT_INVITE_YOUR_SELF_487ade'	=>	'自分自身を招待することはできません。',
'TO_ADDRESS_IS_ALREADY_A_USER_4c2f27'	=>	'宛先アドレスは既にユーザです。',
'TO_ADDRESS_IS_ALREADY_INVITED_f165f0'	=>	'宛先アドレスは既に招待されています。',
'INVITE_FAILED_SAVE_ERROR'	=>	'招待の保存に失敗しました！エラー: [error]',
'INVITE_FAILED_SEND_ERROR'	=>	'招待の送信に失敗しました！エラー: [error]',
'INVITE_SENT_SUCCESSFULLY_380490'	=>	'招待を正常に送信しました！',
'INVITE_SAVED_SUCCESSFULLY_14a90f'	=>	'招待を正常に保存しました！',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_RESENT_528e3e'	=>	'既に招待は承諾され再送信することはできません。',
'INVITE_RESEND_NOT_APPLICABLE_AT_THIS_TIME_c65f19'	=>	'招待の再送信は現時点では適用されません。',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_DELETED_cbc934'	=>	'既に招待は承諾され削除することはできません。',
'INVITE_FAILED_DELETE_ERROR'	=>	'招待の削除に失敗しました！エラー: [error]',
'INVITE_DELETED_SUCCESSFULLY_9ea357'	=>	'招待を正常に削除しました！',
// 7 language strings from file plug_cbinvites/templates/default/invite_edit.php
'EDIT_INVITE_1faaed'	=>	'招待を編集',
'CREATE_INVITE_1e89ce'	=>	'招待を作成',
'TO_e12167'	=>	'宛先',
'BODY_ac101b'	=>	'本文',
'USER_8f9bfe'	=>	'ユーザ',
'UPDATE_INVITE_7c2f89'	=>	'招待を更新',
'SEND_INVITE_962943'	=>	'招待を保存',
// 9 language strings from file plug_cbinvites/templates/default/tab.php
'NEW_INVITE_4093fa'	=>	'新しい招待',
'SENT_7f8c02'	=>	'送信',
'PLEASE_RESEND_6ba908'	=>	'再送信してください。',
'ACCEPTED_382ab5'	=>	'承認',
'RESEND_1c0b8f'	=>	'再送信',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_INVITE_405d09'	=>	'この招待を削除してもよろしいですか？',
'NO_INVITE_SEARCH_RESULTS_FOUND_63c4e3'	=>	'招待の検索結果は見つかりませんでした。',
'YOU_HAVE_NO_INVITES_2f8b42'	=>	'あなたは、いずれの招待も持っていません。',
'THIS_USER_HAS_NO_INVITES_f2d878'	=>	'このユーザには招待されていません。',
);
