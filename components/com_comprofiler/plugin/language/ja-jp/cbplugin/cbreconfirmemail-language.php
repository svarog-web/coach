<?php
/**
* Community Builder (TM) cbreconfirmemail Japanese (Japan) language file Frontend
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
'CHANGED_820dbd'	=>	'変更',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'置換をサポートしたメッセージは、メールアドレスを変更した後に表示されます。何もメッセージを表示しない場合は、空白のままにしてください。',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'メールアドレスが変更され、再確認を必要としています。確認メール用の新しいメールアドレス確認してください。',
'NOTIFICATION_96d008'	=>	'通知',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'入力した差出人名の置換サポートは、すべての再確認メール (例 : 私の素晴らしいCBサイト！) とともに送信されます。左のブランクは、ユーザ名がデフォルト設定された場合になります。指定された場合は返信先名が、ユーザ名として追加されます。',
'FROM_NAME_4a4a8f'	=>	'差出人名',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'入力した差出人アドレスの置換サポートは、すべての再確認メール (例 : general@domain.com) から送信されます。左のブランクは、ユーザのメールがデフォルト設定された場合になります。指定された場合は返信先アドレスが、ユーザのメールとして追加されます。',
'FROM_ADDRESS_a5ab7d'	=>	'差出人アドレス',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'置換をサポートした再確認メールの件名を入力してください。',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'あなたのメールアドレスが変更されました',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'入力した html と置換サポートした再確認メールの本文です。指定した [reconfirm] は確認リンクを表示します。さらに [old_email] は古いメールアドレスを表示するために使用することができ、または [new_email] は新しいメールアドレスを表示します。',
'BODY_ac101b'	=>	'本文',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'アカウント [username] に接続しているメールアドレスは、[new_email] に変更され、確認がひつようになります。<br><br>あなたは、次のリンクをクリックしてメールアドレスを確認することができます : <br><a href="[reconfirm]">[reconfirm]</a><br><br>これを実行しエラーが出た場合は、管理にお問い合わせいただくか、<a href="[cancel]">こちらをクリック</a>してキャンセルしてください。',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'置換をサポートした CC アドレス (例 : [email]) を入力してください。複数のアドレスは、カンマ区切りのリスト (例 : email1@domain.com, email2@domain.com, email3@domain.com) を使用がサポートされています。',
'CC_ADDRESS_b6327b'	=>	'CC アドレス',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'置換をサポートした BCC アドレス (例 : [email]) を入力してください。サポートされた複数のアドレスは、カンマ区切りのリスト (例 : email1@domain.com, email2@domain.com, email3@domain.com) を使用してください。',
'BCC_ADDRESS_33b728'	=>	'BBC アドレス',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'置換をサポートした添付ファイルのアドレス (例 :  [cb_myfile]) を入力してください。サポートされた複数のアドレスは、カンマ区切りのリスト (例 :  /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip) を使用してください。',
'ATTACHMENT_e9cb21'	=>	'添付',
'RECONFIRMED_e748a2'	=>	'再確認',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'置換をサポートした、成功した再確認後にリダイレクトするURLを入力してください。空白のままにするとリダイレクトは全く行われません。',
'REDIRECT_4202ef'	=>	'リダイレクト',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'置換をサポートした、成功した再確認後に表示されるメッセージを入力してください。',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'新しいメールアドレスの確認に成功！',
'CANCELLED_a149e8'	=>	'キャンセル済み',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'置換をサポートした、成功したメールの変更をキャンセルした後にリダイレクトするURLを入力してください。空白のままにするとリダイレクトは全く行われません。',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'置換をサポートした、成功したメールの変更をキャンセルした後に表示されるメッセージを入力してください。',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'メールアドレスの変更を正常にキャンセルしました！',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'不足しているコードを確認してください。',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'ユーザの確認コードに関連付けられていません。',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'確認コードが有効ではありません。',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'メールアドレスの変更を取り消すことができませんでした！エラー : [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'新しいメールアドレスを確認できませんでした！エラー : [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'メールアドレスは既に確認されています。',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'メール',
);
