<?php
/**
* Community Builder (TM) cbgroupjivevideo Slovak (Slovakia) language file Frontend
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
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'Zadajte počet videí, ktoré môžu jednotliví používatelia vytvoriť v každej skupine. Ak pole necháte prázdne, bude počet videí neobmedzený. Moderátorov a vlastníkov skupín sa toto nastavenie netýka.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'Zapnúť alebo vypnúť použitie captcha vo videách skupín. Vyžaduje sa, aby bol nainštalovaný a uverejnený najnovší CB AntiSpam modul. Moderátorov sa toto nastavenie netýka.',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'Uverejniť nové video',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'Nové video vyžaduje schválenie',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Zapnúť alebo vypnúť použitie stránkovania.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Zadajte stránkový limit. Stránkový limit určuje počet riadkov, ktoré budú zobrazené na každej stránke. Ak je stránkovanie vypnuté, môžete to využiť na obmedzenie počtu zobrazených riadkov.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Zapnúť alebo vypnúť vyhľadávanie v riadkoch.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Vyberte triedenie riadkov.',
'DATE_ASC_a5871f'	=>	'Dátum VZOST.',
'DATE_DESC_bcfc6d'	=>	'Dátum ZOST.',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Skupina neexistuje!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'Nemáte dostatočné oprávnenia na uverejnenie videa v tejto skupine.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'Nemáte dostatočné oprávnenia na úpravu tohto videa.',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'Vyberte stav uverejnenia videa. Nepublikované videá nebudú viditeľné pre ostatných.',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'Voliteľne môže zadať názov videa, ktorý bude zobrazený namiesto URL adresy.',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'Pre uverejnenie zadajte URL adresu videa.',
'GROUP_VIDEO_LIMITS_EXT'	=>	'Vaša url musí mať príponu [ext].',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'Voliteľne môžete zadať popis videa.',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'Zadajte id vlastníka videa. Vlastník videa určuje autorstvo videa definovaného ako ID používateľa.',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'Zlyhalo ukladanie videa! Chyba: [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_VIDEO_28e07a'	=>	'Nové video v skupine',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'Používateľ [user] uverejnil video [video] v skupine [group]!',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'Nové video v skupine čaká na schválenie',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'Používateľ [user] uverejnil v skupine [group] video [video], ktoré čaká na schválenie!',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'Video bolo úspešne uverejnené a čaká na schválenie!',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'Video bolo úspešne uverejnené!',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'Video bolo úspešne uložené!',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'Vaše video čaká na schválenie.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'Nemáte dostatok oprávnení na publikovanie alebo odpublikovanie tohto videa.',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'Video neexistuje',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'Zlyhalo uloženie stavu videa. Chyba: [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'Žiadosť o zverejnenie videa bola schválená',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'Uverejnenie vášho videa [video] v skupine [group] bolo schválené!',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'Stav videa bol úspešne uložený!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'Nemáte dostatočné oprávnenia na vymazanie tohto videa.',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'Zlyhalo vymazanie videa. Chyba: [error]',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'Video bolo úspešne vymazané!',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Vlastník nie je definovaný!',
'URL_NOT_SPECIFIED_2ccd94'	=>	'URL adresa nebola definovaná!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Skupina nie je definovaná!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Skupina neexistuje!',
'GROUP_VIDEO_INVALID_URL'	=>	'Nesprávna URL. Uistite sa, že URL adresa existuje!',
'GROUP_VIDEO_INVALID_EXT'	=>	'Nesprávna prípona URL [ext]. Odkazujte len na [exts]!',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'Videá',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'Pridať nové video pre skupinu',
'CONFIGURATION_254f64'	=>	'Nastavenie',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'Vypnúť',
'ENABLE_2faec1'	=>	'Zapnúť',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Zapnúť, so schvaľovaním',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'Voliteľne môžete zapnúť alebo vypnúť používanie videí. Na vlastníkov a administrátorov skupín sa toto nastavenie nevzťahuje a môžu videá pridávať vždy. Pamätajte na to, že existujúce videá budú i tak prístupné.',
'DONT_NOTIFY_3ea23f'	=>	'Neupozorňovať',
'SEARCH_VIDEOS_e5b832'	=>	'Prehľadávať videá...',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'uverejnil video v skupine [group]',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'Upraviť video',
'NEW_VIDEO_458670'	=>	'Nové video',
'VIDEO_34e2d1'	=>	'Video',
'CAPTION_272ba7'	=>	'Popis',
'UPDATE_VIDEO_3e00c1'	=>	'Aktualizovať video',
'PUBLISH_VIDEO_dc049f'	=>	'Uverejniť video',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% video|Videá: %%COUNT%%',
'AWAITING_APPROVAL_af6558'	=>	'Čaká na schválenie',
'APPROVE_6f7351'	=>	'Schváliť',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'Skutočne chcete zrušiť uverejnenie tohto videa?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'Skutočne chcete vymazať toto video?',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'Neboli nájdené žiadne videá skupiny.',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'Táto skupina momentálne nemá žiadne videá.',
);
