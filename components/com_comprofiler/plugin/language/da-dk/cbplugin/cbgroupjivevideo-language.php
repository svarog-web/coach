<?php
/**
* Community Builder (TM) cbgroupjivevideo Danish (Denmark) language file Frontend
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
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'Indtast antallet af videoer som hver individuelle bruger er begrænset til at oprettet per gruppe. Hvis efterladt tom, så er det ubegrænset. Moderatorer og gruppeejere er undtaget fra denne konfiguration.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'Aktiver eller deaktiver anvendelse af captcha på gruppevideoer. Kræver at seneste CB AntiSpam plugin er installeret og publiceret. Moderatorer er undtaget fra denne konfiguration.',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'Publicering af ny video',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'Nye video kræver godkendelse',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Aktiver eller deaktiver anvendelse af sideinddeling.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Indtast side grænse. Sidegrænse bestemmer hvor mange rækker der vises per side. Hvis sideinddeling er deaktiveret, så kan dette stadig anvendes til at begrænse antallet af rækker der vises.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Aktiver eller deaktiver anvendes af søgning på rækker',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Vælg række rækkefølgen',
'DATE_ASC_a5871f'	=>	'Dato stigende',
'DATE_DESC_bcfc6d'	=>	'Dato faldende',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Gruppe eksisterer ikke.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'Du har ikke tilstrækkelige rettigheder til at publicere en video i denne gruppe.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'Du har ikke tilstrækkelige rettigheder til at redigere denne video.',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'Vælg publiceringstilstand for denne video. Afpublicerede videoer vil ikke være synlige for offenligheden',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'Indtast eventuelt en videotitel der skal vises i stedet for url.',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'Indtast URLen til videoen der skal publiceres.',
'GROUP_VIDEO_LIMITS_EXT'	=>	'Din URL skal være af typen [ext].',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'Indtast eventuelt en video titel.',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'Indtast video ejer id. Video ejer bestemmer opretteren af videoen angivet som Bruger ID.',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'Video kunne ikke gemmes! Fejl: [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_VIDEO_28e07a'	=>	'Ny gruppevideo',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'[user] har publiceret videoen [video] i gruppen [group]!',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'Ny gruppevideo afventer godkendelse',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'[user] har publiceret videoen [video] i gruppen [group] og afventer godkendelse!',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'Video publiceret og afventer godkendelse!',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'Video publiceret!',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'Video gemt!',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'Din video afventer godkendelse.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'Du har ikke tilstrækkelige rettigheder til at publicere eller afpublicere denne video.',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'Videoen eksisterer ikke.',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'Videotilstand kunne ikke gemmes. Fejl: [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'Video publiceringsforespørgsel accepteret',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'Din video [video] publiceringsforespørgseli gruppen [group] er accepteret!',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'Videotilstand gemt!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'Du har ikke tilstrækkelige rettigheder til at slette denne video!',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'Video kunne ikke slettes. Fejl: [error]',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'Video slettet!',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Ejer ikke angivet!',
'URL_NOT_SPECIFIED_2ccd94'	=>	'URL ikke angivet!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Gruppe ikke angivet!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Gruppe eksisterer ikke!',
'GROUP_VIDEO_INVALID_URL'	=>	'Ugyldig URL. Vær venligst sikker på at URLen eksisterer!',
'GROUP_VIDEO_INVALID_EXT'	=>	'Ugyldig URL endelse [ext]. Link venligst kun til [exts]!',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'Videoer',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'Tilføj ny video til gruppe',
'CONFIGURATION_254f64'	=>	'Konfiguration',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'Deaktiver',
'ENABLE_2faec1'	=>	'Aktiver',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Aktiver, med godkendelse',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'Aktiver eller deaktiver eventuelt anvendelse af videoer. Gruppeejer og gruppeadministratorer er undtaget fra denne konfiguration og kan altid dele videoer. Bemærk at eksisterende videoer stadig vil være tilgængelige.',
'DONT_NOTIFY_3ea23f'	=>	'Underret ikke',
'SEARCH_VIDEOS_e5b832'	=>	'Søg videoer...',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'publicerede en video i [group]',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'Rediger video',
'NEW_VIDEO_458670'	=>	'Ny video',
'VIDEO_34e2d1'	=>	'Video',
'CAPTION_272ba7'	=>	'Titel',
'UPDATE_VIDEO_3e00c1'	=>	'Opdater video',
'PUBLISH_VIDEO_dc049f'	=>	'Publicer video',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% Video|%%COUNT%% Videoer',
'AWAITING_APPROVAL_af6558'	=>	'Afventer godkendelse',
'APPROVE_6f7351'	=>	'Godkend',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'Er du sikker på at du ønsker at afpublicere denne video?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'Er du sikker på at du ønsker at slette denne video?',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'Ingen gruppevideo søgeresultater fundet.',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'Denne gruppe har lige nu ingen videoer.',
);
