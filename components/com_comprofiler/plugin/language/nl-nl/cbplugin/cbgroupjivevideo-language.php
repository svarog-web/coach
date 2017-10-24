<?php
/**
* Community Builder (TM) cbgroupjivevideo Dutch (Netherlands) language file Frontend
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
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'Vul het aantal video\'s in dat elke individuele deelnemer mag plaatsen per groep. Indien dit veld leeg blijft\', is het aantal onbeperkt. Moderators en de eigenaars van groepen zijn uitgesloten van deze configuratie.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'In- of uitschakelen van de beveiligingscode voor video\'s van de groep. Dit vereist dat CB AntiSpam is geïnstalleerd en ingeschakeld. Moderators zijn uitgesloten van deze configuratie.',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'Publicatie van nieuwe video',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'Nieuwe video vereist goedkeuring',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'In- en uitschakelen gebruik van bladerfunctie.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Invullen bladlimiet. De bladlimiet bepaalt hoeveel rijen er per pagina worden getoond. Indien de bladlimiet is uitgeschakeld, kan dit nog steeds gebruikt worden om het aantal getoonde rijen te limiteren.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'In- of uitschakelen van zoeken in rijen.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Selecteeer de volgorde van de rij',
'DATE_ASC_a5871f'	=>	'Datum oplopend',
'DATE_DESC_bcfc6d'	=>	'Datum aflopend',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Groep bestaat niet.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'Je hebt onvoldoende rechten om een video in deze groep te publiceren.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'Je hebt onvoldoende rechten om deze video aan te passen.',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'Selecteer de status van deze video. Niet gepubliceerde video\'s zijn niet zichtbaar voor het publiek.',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'Vul optioneel een titel voor de video in, in plaats van de url.',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'Vul de url van de video in om deze te publiceren.',
'GROUP_VIDEO_LIMITS_EXT'	=>	'Jouw url moet van het type [ext] zijn.',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'Vul optioneel een bijschrift bij de video in.',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'Vul het ID van de eigenaar van de video in. De eigenaar van de video bepaalt de maker van de video gespecificeerd als ID van de deelnemer.',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'Opslaan video mislukt. Fout: [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_VIDEO_28e07a'	=>	'Nieuwe video',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'[user] heeft de video [video] gepubliceerd in de groep [group].',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'Nieuwe video wacht op goedkeuring',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'[user] heeft de video [video] in de groep [group] gepubliceerd en wacht op goedkeuring.',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'Publiceren video gelukt en wacht nu op goedkeuring.',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'Publiceren video gelukt.',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'Opslaan video gelukt.',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'Jouw video wacht op goedkeuring.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'Je hebt onvoldoende rechten om deze video te publiceren of te depubliceren.',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'Video bestaat niet.',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'Status video opslaan mislukt. Fout: [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'Verzoek publicatie video goedgekeurd.',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'Jouw verzoek om publicatie van de video [video] in de groep [group] is goedgekeurd.',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'Opslaan status video gelukt.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'Je hebt onvoldoende rechten om deze video te verwijderen.',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'Verwijderen video mislukt.',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'Verwijderen video gelukt.',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Eigenaar niet gespecificeerd.',
'URL_NOT_SPECIFIED_2ccd94'	=>	'URL niet gespecificeerd.',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Groep niet bekend.',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Groep bestaat niet.',
'GROUP_VIDEO_INVALID_URL'	=>	'Ongeldige URL. Wees er zeker van de URL bestaat.',
'GROUP_VIDEO_INVALID_EXT'	=>	'Ongeldige URL [ext]. Plaats svp alleen [exts]',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'Videos',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'Nieuwe video',
'CONFIGURATION_254f64'	=>	'Configuratie',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'Uitschakelen',
'ENABLE_2faec1'	=>	'Inschakelen',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Inschakelen, met toestemming',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'in- of uitschakelen van het gebruik van video\'s. Eigenaars en administrators van de groep zijn uitgesloten van deze configuratie en kunnen altijd video\'s delen. NB. bestaande video\'s blijven toegankelijk.',
'DONT_NOTIFY_3ea23f'	=>	'Niet informeren',
'SEARCH_VIDEOS_e5b832'	=>	'Video\'s zpeken...',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'publiceerde een video in [group]',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'Video aanpassen',
'NEW_VIDEO_458670'	=>	'Nieuwe video',
'VIDEO_34e2d1'	=>	'Video',
'CAPTION_272ba7'	=>	'Bijschrift',
'UPDATE_VIDEO_3e00c1'	=>	'Video updaten',
'PUBLISH_VIDEO_dc049f'	=>	'Video publiceren.',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% Video|%%COUNT%% Videos',
'AWAITING_APPROVAL_af6558'	=>	'In afwachting van goedkeuring.',
'APPROVE_6f7351'	=>	'Goedkeuren',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'Weet je zeker dat je deze video wilt depubliceren?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'Weet je zeker dat je deze video wilt verwijderen?',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'Geen zoekresultaten gevonden bij het zoeken naar video\'s in deze groep.',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'Deze groep heeft geen video\'s',
);
