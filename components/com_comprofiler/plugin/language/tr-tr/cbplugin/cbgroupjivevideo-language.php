<?php
/**
* Community Builder (TM) cbgroupjivevideo Turkish (Turkey) language file Frontend
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
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'Bir kullanıcının bir gruba ekleyebileceği görüntü sayısını yazın. Boş bırakıldığında herhangi bir sınırlama yapılmaz. Sorumlular ve grup sahipleri bu ayardan etkilenmez.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'Bu seçenek etkinleştirildiğinde, grup görüntülerinde güvenlik kodu kullanılır. Bu özelliğin kullanılabilmesi için, son CB AntiSpam uygulama ekinin yüklenmiş ve yayınlanmış olması gereklidir. Sorumlular bu ayardan etkilenmez.',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'Yeni görüntü yayını',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'Onay bekleyen yeni bir görüntü var',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Bu seçenek etkinleştirildiğinde, sayfalandırma kullanılır.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Bir sayfada görüntülenecek satır sayısını belirleyen sayfalandırma sınırını yazın. Sayfalandırma seçeneği devre dışı bırakılsa bile bu değer görüntülenecek satır sayısını belirler.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Bu seçenek etkinleştirildiğinde, satırlarda arama yapılabilir.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Satır sıralamasını seçin.',
'DATE_ASC_a5871f'	=>	'Tarihe Göre Artan',
'DATE_DESC_bcfc6d'	=>	'Tarihe Göre Azalan',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Grup bulunamadı.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'Bu grupta görüntü yayınlama izniniz yok.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'Bu görüntüyü düzenleme izniniz yok.',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'Görüntünün yayınlanma durumunu seçin. Yayınlanmamış görüntüler herkese açık olarak görüntülenmez.',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'İsteğe bağlı, görüntünün başlığı olarak görüntülenecek metin. İnternet adresinin yerine görüntülenir.',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'Yayınlanacak görüntünün İnternet adresini yazın.',
'GROUP_VIDEO_LIMITS_EXT'	=>	'İnternet adresiniz [ext] türünde olmalıdır.',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'İsteğe bağlı bir görüntü başlığı yazın.',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'Görüntü sahibinin kodunu yazın. Görüntünün sahibi görüntüyü oluşturan kullanıcı kodunu belirtir.',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'Hata nedeniyle görüntü kaydedilemedi: [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_VIDEO_28e07a'	=>	'Yeni grup görüntüsü',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'[user] kullanıcısı [group] grubunda [video] görüntüsünü yayınladı!',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'Onay bekleyen yeni bir grup görüntüsü var',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'[user] kullanıcısının [group] grubunda yayınladığı [video] görüntüsü onay bekliyor!',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'Görüntü yayınlandı ve onay bekliyor!',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'Görüntü yayınlandı!',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'Görüntü kaydedildi!',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'Görüntünüz onay bekliyor.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'Bu görüntüyü yayınlama ya da yayından kaldırma izniniz yok.',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'Görüntü bulunamadı.',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'Hata nedeniyle görüntü durumu kaydedilemedi: [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'Görüntü yayınlama isteği onaylandı',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'[group] grubundaki [video] görüntüsünü yayınlama isteğiniz onaylandı!',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'Görüntü durumu kaydedildi!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'Bu görüntüyü silme izniniz yok!',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'Hata nedeniyle görüntü silinemedi: [error]',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'Görüntü silindi!',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Sahip belirtilmemiş!',
'URL_NOT_SPECIFIED_2ccd94'	=>	'İnternet adresi belirtilmemiş!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Grup belirtilmemiş!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Grup bulunamadı!',
'GROUP_VIDEO_INVALID_URL'	=>	'İnternet adresi geçersiz. Lütfen adresin doğruluğundan emin olun!',
'GROUP_VIDEO_INVALID_EXT'	=>	'[ext] İnternet adresi uzantısı geçersiz. Lütfen yalnız [ext] adreslerini yükleyin!',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'Görüntüler',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'Gruba Görüntü Ekleyin',
'CONFIGURATION_254f64'	=>	'Ayarlar',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'Devre Dışı Bırak',
'ENABLE_2faec1'	=>	'Etkinleştir',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Onay ile Etkinleştir',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'İsteğe bağlı, görüntülerin kullanımını sağlar. Grup sahibi ve grup yöneticileri bu ayardan etkilenmez ve her zaman görüntü yükleyebilir. Varolan görüntülerin hala erişilebilir olduğunu unutmayın.',
'DONT_NOTIFY_3ea23f'	=>	'Bildirim Gönderme',
'SEARCH_VIDEOS_e5b832'	=>	'Görüntü Arama...',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'[group] grubunda bir görüntü yayınladı',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'Görüntüyü Düzenle',
'NEW_VIDEO_458670'	=>	'Yeni Görüntü',
'VIDEO_34e2d1'	=>	'Görüntü',
'CAPTION_272ba7'	=>	'Başlık',
'UPDATE_VIDEO_3e00c1'	=>	'Görüntüyü Güncelle',
'PUBLISH_VIDEO_dc049f'	=>	'Görüntüyü Yayınla',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% Görüntü|%%COUNT%% Görüntü',
'AWAITING_APPROVAL_af6558'	=>	'Onay Bekliyor',
'APPROVE_6f7351'	=>	'Onayla',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'Bu görüntüyü yayından kaldırmak istediğinize emin misiniz?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'Bu görüntüyü silmek istediğinize emin misiniz?',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'Aramada herhangi bir grup görüntüsü bulunamadı.',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'Bu grupta henüz bir görüntü yok.',
);
