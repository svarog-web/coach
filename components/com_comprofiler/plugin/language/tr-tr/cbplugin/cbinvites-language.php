<?php
/**
* Community Builder (TM) cbinvites Turkish (Turkey) language file Frontend
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
'JOIN_ME_de0c95'	=>	'Bana Katıl!',
'MAILER_FAILED_TO_SEND_46f543'	=>	'E-posta gönderilemedi.',
'TO_ADDRESS_MISSING_225871'	=>	'Kime adresi eksik.',
'SUBJECT_MISSING_8e0db8'	=>	'Konu eksik.',
'BODY_MISSING_b6f835'	=>	'İleti metni eksik.',
'SEARCH_INVITES_9e5f33'	=>	'Çağrı Arama...',
'INVITE_CODE_NOT_VALID_7cd6f7'	=>	'Çağrı kodu geçersiz.',
'INVITE_CODE_ALREADY_USED_cd715c'	=>	'Çağrı kodu zaten kullanılmış.',
'INVITE_CODE_IS_VALID_96aad3'	=>	'Çağrı kodu geçerli.',
// 44 language strings from file plug_cbinvites/cbinvites.xml
'YOUR_REGISTRATION_INVITE_CODE_324b31'	=>	'Kayıt çağrı kodunuz.',
'INVITE_CODE_0a2eb0'	=>	'Çağrı Kodu',
'INVITES_213b86'	=>	'Çağrılar',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_ON_TAB_INVITES_b76bc2'	=>	'Bu seçenek etkinleştirildiğinde, çağrılar sekmesinde sayfalandırma kullanılır.',
'INPUT_PAGE_LIMIT_ON_TAB_INVITES_PAGE_LIMIT_DETERMI_b17959'	=>	'Bir sayfada görüntülenecek sekme çağrılarının sayısını yazın.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_TAB_INVITES_dc5e0c'	=>	'Bu seçenek etkinleştirildiğinde, sekme çağrılarında arama yapılabilir.',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_INVITES_I_e3fe43'	=>	'Tüm CB çağrılarında kullanılacak temayı seçin. Temada eksik dosyalar varsa, onların yerine varsayılan temanın dosyaları kullanılır. Tema dosyaları şu konumda bulunur: components/com_comprofiler/plugin/user/plug_cbinvites/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_7cf91e'	=>	'İsteğe bağlı, tüm CB çağrıları için DIV bölümünde kullanılacak sınıf soneki.',
'SELECT_INVITE_CREATE_ACCESS_ACCESS_DETERMINES_WHO__c4a59a'	=>	'Çağrı ekleyebilecek kişileri seçin. Seçilen grubun üstündeki gruplara da çağrı ekleme izni verilir (örneğin kayıtlı kullanıcılar grubuna izin verildiğinde, Yazarlar grubuna da verilmiş olur). Sorumlular bu ayardan etkilenmez.',
'INPUT_NUMBER_OF_INVITES_EACH_INDIVIDUAL_USER_IS_LI_0d3553'	=>	'Beliril bir süre içinde kullanıcının alabileceği çağrı sayısını yazın. Boş bırakıldığında herhangi bir sınırlama yapılmaz. Sorumlular bu ayardan etkilenmez.',
'INPUT_NUMBER_OF_DAYS_AFTER_INVITE_SENT_TO_ALLOW_RE_12681b'	=>	'Çağrının yeniden gönderilmesi için beklenecek gün sayısını yazın (onaylanan çağrılar için yeniden gönderim yapılmaz). Boş bırakıldığından çağrılar yeniden gönderilmez. Sorumlular bu ayardan etkilenmez.',
'RESEND_DELAY_4f8afe'	=>	'Yeniden Gönderim Gecikmesi',
'SELECT_USAGE_OF_MULTIPLE_EMAILS_IN_A_SINGLE_INVITE_e8af1e'	=>	'Tek bir çağrı için kullanılacak eposta adreslerini virgül ile ayırarak yazın (email1@domain.com, email2@domain.com, email3@domain.com gibi). Sorumlular bu ayardan etkilenmez.',
'MULTIPLE_INVITES_b8f6fd'	=>	'Çoklu Çağrılar',
'SELECT_USAGE_OF_DUPLICATE_INVITES_TO_THE_SAME_ADDR_96bfd8'	=>	'Bu seçenek etkinleştirildiğinde, aynı adrese birden fazla çağrı yapılabilir. Sorumlular bu ayardan etkilenmez.',
'DUPLICATE_INVITES_9b3f9e'	=>	'Çift Çağrılar',
'SELECT_CONNECTION_METHOD_FROM_INVITER_TO_INVITEE_32692b'	=>	'Çağıran ya da çağrılan olarak arkadaşlık yöntemini seçin.',
'CONNECTION_c2cc70'	=>	'Arkadaşlık',
'PENDING_CONNECTION_30ec76'	=>	'Bekleyen Arkadaşlık',
'AUTO_CONNECTION_5f8c15'	=>	'Otomatik Arkadaşlık',
'SELECT_INVITE_EMAIL_BODY_EDITOR_d8f5e1'	=>	'Çağrı e-postası metin düzenleyicisini seçin.',
'PLAIN_TEXT_e44b14'	=>	'Düz Metin',
'HTML_TEXT_503c11'	=>	'HTML Metin',
'WYSIWYG_fcf0d4'	=>	'WYSIWYG',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_INVITES_REQU_65e72e'	=>	'Bu seçenek etkinleştirildiğinde, çağrılarda güvenlik kodu kullanılır. Bu özelliğin kullanılabilmesi için, son CB AntiSpam uygulama ekinin yüklenmiş ve yayınlanmış olması gereklidir. Sorumlular bu ayardan etkilenmez.',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_f0ef2c'	=>	'Çağrı e-postalarında kod ile görüntülenecek kimden adını yazın (örnek Harika CB Sitesi!). Boş bırakılırsa varsayılan olarak kullanıcı adı kullanılır. Bir yanıtlama adı belirtilirse kullanıcının adı olarak eklenir.',
'FROM_NAME_4a4a8f'	=>	'Kimden Adı',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_ef3c3b'	=>	'Çağrı e-postalarında kod ile görüntülenecek e-posta adresini yazın (örnek genel@sitem.com). Boş bırakılırsa varsayılan olarak kullanıcının e-posta adresi kullanılır. Bir yanıtlama adresi belirtilirse kullanıcının e-posta adresi olarak eklenir.',
'FROM_ADDRESS_a5ab7d'	=>	'Kimden E-posta Adresi',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Kod ile görüntülenecek kopya adreslerini yazın (örnek: [email]); virgül ile ayırarak birden çok adres yazılabilir. (örnek eposta1@websitem.com, eposta2@websitem.com, eposta3@websitem.com).',
'CC_ADDRESS_b6327b'	=>	'Kopya Adresleri',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Kod ile görüntülenecek gizli kopya adreslerini yazın (örnek: [email]); virgül ile ayırarak birden çok adres yazılabilir. (örnek eposta1@websitem.com, eposta2@websitem.com, eposta3@websitem.com).',
'BCC_ADDRESS_33b728'	=>	'Gizli Kopya Adresleri',
'INPUT_SUBSTITUTION_SUPPORTED_PREFIX_OF_INVITE_EMAI_91cd80'	=>	'Çağrı e-postasının konusunda kod ile görüntülenecek öneki yazın. [site], [sitename], [path], [itemid], [register], [profile], [code], and [to] ek abonelikleri desteklenir.',
'SUBJECT_PREFIX_175911'	=>	'Konu Ön Eki',
'SITENAME_6b68ee'	=>	'[sitename] - ',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_OF_INVITE_EMAI_058012'	=>	'Çağrı e-postasının metninde kod ile görüntülenecek üstbilgiyi yazın. [site], [sitename], [path], [itemid], [register], [profile], [code] ve [to] ek abonelikleri desteklenir.',
'BODY_HEADER_67622c'	=>	'Metin Üstbilgisi',
'YOU_HAVE_BEEN_INVITED_BY_USERNAME_TO_JOIN_SITENAME_e90186'	=>	'<p>[username] kullanıcısı tarafından [sitename] sitesine katılmaya çağrıldınız!</p><br>',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_OF_INVITE_EMAI_f5fa57'	=>	'Çağrı e-postasının metninde kod ile görüntülenecek altbilgiyi yazın. [site], [sitename], [path], [itemid], [register], [profile], [code] ve [to] ek abonelikleri desteklenir.',
'BODY_FOOTER_6046e1'	=>	'Metin Altbilgisi',
'INVITE_CODE_CODESITENAME_SITEREGISTRATION_REGISTER_eec8c6'	=>	'<br><p>Çağrı Kodu - [code]<br>[sitename] - [site]<br>Kayıt - [register]<br>[username] - [profile]</p>',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__523216'	=>	'Kod ile görüntülenecek ek adreslerini yazın (örnek: [cb_myfile]); virgül ile ayırarak birden çok adres yazılabilir. (örnek: /home/username/public_html/images/dosya1.zip, http://www.websitem.com/dosya3.zip). [site], [sitename], [path], [itemid], [register], [profile], [code] ve [to] ek abonelikleri desteklenir.',
'ATTACHMENT_e9cb21'	=>	'Ek',
// 22 language strings from file plug_cbinvites/component.cbinvites.php
'INPUT_INVITE_EMAIL_TO_ADDRESS_SEPARATE_MULTIPLE_EM_2028c1'	=>	'Çağrı e-posta adresini yazın. Virgül ile ayrılarak birden çok adres yazılabilir.',
'INPUT_INVITE_EMAIL_TO_ADDRESS_54551f'	=>	'Çağrının e-postasının kime adresini yazın.',
'INPUT_INVITE_EMAIL_SUBJECT_IF_LEFT_BLANK_A_SUBJECT_0974fc'	=>	'Çağrı e-postasının konusunu yazın. Boş bırakılırsa varsayılan konu kullanılır.',
'OPTIONALLY_INPUT_PRIVATE_MESSAGE_TO_INCLUDE_WITH_I_e1d750'	=>	'İsteğe bağlı, çağrı e-postasına eklenecek özel ileti.',
'INPUT_OWNER_OF_INVITE_AS_SINGLE_INTEGER_USERID_THI_998c63'	=>	'Çağrı sahibinin tamsayı kullanıcı kodunu (user_id) yazın. Bu çağrıyı gönderen kullanıcıdır.',
'OPTIONALLY_INPUT_USER_OF_INVITE_AS_SINGLE_INTEGER__329e71'	=>	'İsteğe bağlı, çağrı sahibinin tamsayı kullanıcı kodu (user_id). Bu kullanıcı çağrıyı onaylayan kullanıcıdır.',
'COMMA_SEPERATED_LISTS_ARE_NOT_SUPPORTED_PLEASE_USE_7676e6'	=>	'Virgül ile ayrılmış listeler desteklenmiyor!. Lütfen tek bir Kime adresi kullanın.',
'INVITE_LIMIT_REACHED_1e1e31'	=>	'Çağrı sayısı sınırına ulaşıldı!',
'TO_ADDRESS_NOT_SPECIFIED_e292c0'	=>	'Kime adresi belirtilmemiş.',
'INVITE_TO_ADDRESS_INVALID'	=>	'Kime adresi geçersiz: [to_address]',
'YOU_CAN_NOT_INVITE_YOUR_SELF_487ade'	=>	'Kendinizi çağıramazsınız.',
'TO_ADDRESS_IS_ALREADY_A_USER_4c2f27'	=>	'Kime adresi zaten bir kullanıcı.',
'TO_ADDRESS_IS_ALREADY_INVITED_f165f0'	=>	'Kime adresi zaten çağrıldı.',
'INVITE_FAILED_SAVE_ERROR'	=>	'Çağrı kaydedilemedi! Hata: [error]',
'INVITE_FAILED_SEND_ERROR'	=>	'Çağrı gönderilemedi! Hata: [error]',
'INVITE_SENT_SUCCESSFULLY_380490'	=>	'Çağrı gönderildi!',
'INVITE_SAVED_SUCCESSFULLY_14a90f'	=>	'Çağrı kaydedildi!',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_RESENT_528e3e'	=>	'Çağrı zaten onaylanmış olduğundan yeniden gönderilemez.',
'INVITE_RESEND_NOT_APPLICABLE_AT_THIS_TIME_c65f19'	=>	'Çağrı şu anda yeniden gönderilemez.',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_DELETED_cbc934'	=>	'Çağrı zaten onaylanmış olduğundan silinemez..',
'INVITE_FAILED_DELETE_ERROR'	=>	'Çağrı silinemedi! Hata: [error]',
'INVITE_DELETED_SUCCESSFULLY_9ea357'	=>	'Çağrı silindi!',
// 7 language strings from file plug_cbinvites/templates/default/invite_edit.php
'EDIT_INVITE_1faaed'	=>	'Çağrıyı Düzenleyin',
'CREATE_INVITE_1e89ce'	=>	'Çağrı Ekleyin',
'TO_e12167'	=>	'Kime',
'BODY_ac101b'	=>	'Metin',
'USER_8f9bfe'	=>	'Kullanıcı',
'UPDATE_INVITE_7c2f89'	=>	'Çağrıyı Güncelleyin',
'SEND_INVITE_962943'	=>	'Çağrı Gönderin',
// 9 language strings from file plug_cbinvites/templates/default/tab.php
'NEW_INVITE_4093fa'	=>	'Çağrı Ekleyin',
'SENT_7f8c02'	=>	'Gönderin',
'PLEASE_RESEND_6ba908'	=>	'Lütfen Yeniden Gönderin',
'ACCEPTED_382ab5'	=>	'Onaylandı',
'RESEND_1c0b8f'	=>	'Yeniden Gönderin',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_INVITE_405d09'	=>	'Bu çağrıyı silmek istediğinize emin misiniz?',
'NO_INVITE_SEARCH_RESULTS_FOUND_63c4e3'	=>	'Aramada herhangi bir çağrı bulunamadı.',
'YOU_HAVE_NO_INVITES_2f8b42'	=>	'Henüz bir çağrınız yok.',
'THIS_USER_HAS_NO_INVITES_f2d878'	=>	'Bu kullanıcının henüz bir çağrısı yok.',
);
