<?php
/**
* Community Builder (TM) cbgroupjivephoto Slovak (Slovakia) language file Frontend
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
'INPUT_NUMBER_OF_PHOTOS_EACH_INDIVIDUAL_USER_IS_LIM_11b810'	=>	'Zadajte počet fotografií, ktoré môžu jednotliví používatelia vytvoriť v každej skupine. Ak pole necháte prázdne, bude počet fotografií neobmedzený. Moderátorov a vlastníkov skupín sa toto nastavenie netýka.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_PHOTOS_0ea9f4'	=>	'Zapnúť alebo vypnúť použitie captcha vo fotografiách skupín. Vyžaduje sa, aby bol nainštalovaný a uverejnený najnovší CB AntiSpam modul. Moderátorov sa toto nastavenie netýka.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'Zvoľte, či nahrávané obrázky budú vždy prepočítavané. Prepočítavanie zvyšuje bezpečnosť, avšak animácie zachováva len ImageMagick.',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'Zadajte maximálnu výšku v pixeloch, na ktorú budú obrázky upravené.',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'Zadajte maximálnu šírku v pixeloch, na ktorú budú obrázky upravené.',
'INPUT_THE_MAXIMUM_THUMBNAIL_HEIGHT_IN_PIXELS_THAT__9d2b57'	=>	'Zadajte maximálnu výšku náhľadu v pixeloch, na ktorú budú obrázky upravené.',
'INPUT_THE_MAXIMUM_THUMBNAIL_WIDTH_IN_PIXELS_THAT_T_d159f4'	=>	'Zadajte maximálnu šírku náhľadu v pixeloch, na ktorú budú obrázky upravené.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'Nastavte, či nahrávané obrázky majú počas zmeny veľkosti zachovávať svoj pomer strán. Pri nastavení na Nie bude obrázok vždy zmenený na zadanú výšku a šírku. Pri nastavení na Áno bude dodržaný pomer strán tak, aby bola čo najviac dodržaná zadaná výška a šírka náhľadu. Pri nastavení na Áno s orezaním bude obrázok vždy upravený na zadanú maximálnu šírku a výšku so zachovaním pomeru strán a zvyšok sa oreže; je to užitočné najmä pri štvorcových obrázkoch.',
'INPUT_THE_MINIMUM_IMAGE_FILE_SIZE_IN_KBS_30eae6'	=>	'Zadajte minimálnu veľkosť obrázku v KB.',
'INPUT_THE_MAXIMUM_IMAGE_FILE_SIZE_IN_KBS_SET_TO_0__f73680'	=>	'Zadajte maximálnu veľkosť obrázku v KB. Nastavte na 0 pre neobmedzenú veľkosť.',
'UPLOAD_OF_NEW_PHOTO_1831ae'	=>	'Nahrať novú fotografiu',
'NEW_PHOTO_REQUIRES_APPROVAL_d212d4'	=>	'Nová fotografia vyžaduje schválenie',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Zapnúť alebo vypnúť použitie stránkovania.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Zadajte stránkový limit. Stránkový limit určuje počet riadkov, ktoré budú zobrazené na každej stránke. Ak je stránkovanie vypnuté, môžete to využiť na obmedzenie počtu zobrazených riadkov.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Zapnúť alebo vypnúť vyhľadávanie v riadkoch.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Vyberte triedenie riadkov.',
'DATE_ASC_a5871f'	=>	'Dátum VZOST.',
'DATE_DESC_bcfc6d'	=>	'Dátum ZOST.',
'FILENAME_ASC_44f721'	=>	'Názov VZOST.',
'FILENAME_DESC_13d728'	=>	'Názov ZOST.',
// 31 language strings from file cbgroupjivephoto/component.cbgroupjivephoto.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Skupina neexistuje!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_c06972'	=>	'Nemáte dostatočné oprávnenia na nahrávanie fotografií v tejto skupine.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_6eccd3'	=>	'Nemáte dostatočné oprávnenia na úpravu tejto fotografie.',
'SELECT_PUBLISH_STATE_OF_THIS_PHOTO_UNPUBLISHED_PHO_75ff3b'	=>	'Vyberte stav uverejnenia fotografie. Nepublikované príspevky nebudú viditeľné pre ostatných.',
'OPTIONALLY_INPUT_A_PHOTO_TITLE_TO_DISPLAY_INSTEAD__323e09'	=>	'Voliteľne môže zadať názov fotografie, ktorý bude zobrazený namiesto pôvodného názvu.',
'SELECT_THE_PHOTO_TO_UPLOAD_8e29df'	=>	'Vyberte nahrávanú fotografiu.',
'GROUP_PHOTO_LIMITS_EXT'	=>	'Vaša fotografia musí mať príponu [ext].',
'GROUP_PHOTO_LIMITS_MIN'	=>	'Vaša fotografia nesmie presiahnuť [size].',
'GROUP_PHOTO_LIMITS_MAX'	=>	'Vaša fotografia nesmie presiahnuť [size].',
'OPTIONALLY_INPUT_A_PHOTO_DESCRIPTION_d4c183'	=>	'Voliteľne môžete zadať popis fotografie.',
'INPUT_THE_PHOTO_OWNER_ID_PHOTO_OWNER_DETERMINES_TH_eb7b03'	=>	'Zadajte id vlastníka fotografie. Vlastníka fotografie určuje autorstvo fotografie ako ID používateľa.',
'GROUP_PHOTO_FAILED_TO_SAVE'	=>	'Zlyhalo ukladanie fotografie! Chyba: [error]',
'GROUP_PHOTO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_PHOTO_9ba416'	=>	'Nová fotografia v skupine',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_429420'	=>	'Používateľ [user] nahral do skupiny [group] fotografiu [photo]!',
'NEW_GROUP_PHOTO_AWAITING_APPROVAL_221466'	=>	'Nová fotografia v skupine čaká na schválenie',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_e90ac6'	=>	'Používateľ [user] uverejnil v skupine [group] fotografiu [photo], ktorá čaká na schválenie!',
'PHOTO_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_492ec3'	=>	'Fotografia bola úspešne nahratá a čaká na schválenie!',
'PHOTO_UPLOADED_SUCCESSFULLY_0b02f3'	=>	'Fotografia bola úspešne nahratá!',
'PHOTO_SAVED_SUCCESSFULLY_726f86'	=>	'Fotografia bola úspešne uložená!',
'YOUR_PHOTO_IS_AWAITING_APPROVAL_d5f199'	=>	'Vaša fotografia čaká na schválenie.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__dce5c6'	=>	'Nemáte dostatok oprávnení na publikovanie alebo odpublikovanie tejto fotografie.',
'PHOTO_DOES_NOT_EXIST_cd6623'	=>	'Fotografia neexistuje.',
'GROUP_PHOTO_STATE_FAILED_TO_SAVE'	=>	'Zlyhalo uloženie stavu fotografie. Chyba: [error]',
'PHOTO_UPLOAD_REQUEST_ACCEPTED_bf6572'	=>	'Žiadosť o nahratie fotografie bola schválená',
'YOUR_PHOTO_PHOTO_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_9728ad'	=>	'Vaša žiadosť o nahratie fotografie [photo] v skupine [group] bola schválená!',
'PHOTO_STATE_SAVED_SUCCESSFULLY_2f8a03'	=>	'Stav fotografie bol úspešne uložený!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8777b2'	=>	'Nemáte dostatok oprávnení na vymazanie tejto fotograffie.',
'GROUP_PHOTO_FAILED_TO_DELETE'	=>	'Zlyhalo vymazanie fotografie. Chyba: [error]',
'PHOTO_DELETED_SUCCESSFULLY_a9f27f'	=>	'Fotografia bola úspešne vymazaná!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_PHOTO_5ca855'	=>	'Nemáte dostatočné oprávnenia na prístup k tejto fotografii.',
// 7 language strings from file cbgroupjivephoto/library/Table/PhotoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Vlastník nie je definovaný!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Skupina nie je definovaná!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Skupina neexistuje!',
'GROUP_PHOTO_UPLOAD_INVALID_EXT'	=>	'Nesprávna prípona fotografie [ext]. Nahrávajte len súbory s príponou [exts]!',
'GROUP_PHOTO_UPLOAD_TOO_SMALL'	=>	'Fotografia je príliš malá, minimum je [size]!',
'GROUP_PHOTO_UPLOAD_TOO_LARGE'	=>	'Veľkosť fotografie prekračuje povolené maximum [size]!',
'PHOTO_NOT_SPECIFIED_dd1bfc'	=>	'Fotografia nebola definovaná!',
// 3 language strings from file cbgroupjivephoto/library/Trigger/AdminTrigger.php
'PHOTOS_5daaf2'	=>	'Fotografie',
'ADD_NEW_PHOTO_TO_GROUP_2df00d'	=>	'Pridať novú fotografiu do skupiny',
'CONFIGURATION_254f64'	=>	'Nastavenie',
// 6 language strings from file cbgroupjivephoto/library/Trigger/PhotoTrigger.php
'DISABLE_bcfacc'	=>	'Vypnúť',
'ENABLE_2faec1'	=>	'Zapnúť',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Zapnúť, so schvaľovaním',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_PHOTOS_GROUP_44b054'	=>	'Voliteľne môžete zapnúť alebo vypnúť používanie fotografií. Na vlastníkov a administrátorov skupín sa toto nastavenie nevzťahuje a môžu fotografie pridávať vždy. Pamätajte na to, že existujúce fotografie budú i tak prístupné.',
'DONT_NOTIFY_3ea23f'	=>	'Neupozorňovať',
'SEARCH_PHOTOS_e11345'	=>	'Prehľadávať fotografie...',
// 2 language strings from file cbgroupjivephoto/templates/default/activity.php
'GROUP_PHOTO_ACTIVITY_TITLE'	=>	'nahral fotografiu do skupiny [group]',
'ORIGINAL_0a52da'	=>	'Originál',
// 6 language strings from file cbgroupjivephoto/templates/default/photo_edit.php
'EDIT_PHOTO_68ffc9'	=>	'Upraviť fotografiu',
'NEW_PHOTO_50a153'	=>	'Nová fotografia',
'PHOTO_c03d53'	=>	'Fotografia',
'DESCRIPTION_b5a7ad'	=>	'Popis',
'UPDATE_PHOTO_89bc50'	=>	'Aktualizovať fotografiu',
'UPLOAD_PHOTO_05e477'	=>	'Nahrať fotografiu',
// 7 language strings from file cbgroupjivephoto/templates/default/photos.php
'GROUP_PHOTOS_COUNT'	=>	'%%COUNT%% fotografia|Fotografie: %%COUNT%%',
'AWAITING_APPROVAL_af6558'	=>	'Čaká na schválenie',
'APPROVE_6f7351'	=>	'Schváliť',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_PHOTO_31f072'	=>	'Skutočne chcete zrušiť uverejnenie tejto fotografie?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PHOTO_4d3e2d'	=>	'Skutočne chcete vymazať túto fotografiu?',
'NO_GROUP_PHOTO_SEARCH_RESULTS_FOUND_64adc0'	=>	'Neboli nájdené žiadne fotografie skupiny.',
'THIS_GROUP_CURRENTLY_HAS_NO_PHOTOS_8939ef'	=>	'V tejto skupine teraz nie sú žiadne fotografie.',
);
