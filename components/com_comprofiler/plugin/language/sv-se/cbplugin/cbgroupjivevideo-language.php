<?php
/**
* Community Builder (TM) cbgroupjivevideo Swedish (Sweden) language file Frontend
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
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'Ange det antal videos som varje enskild användare är begränsade till att skapa per grupp. Om detta är tomt så tillåts ett obegränsat antal videos. Moderatorer och gruppägare är undantagna från den här konfiguration.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'Aktivera eller inaktivera användning av captcha för gruppvideos. Detta förutsätter att senaste CB AntiSpam är installerad och publicerad. Moderatorer är undantagna från denna konfiguration.',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'Publicering av ny video',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'Ny video måste godkännas',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Aktivera/Inaktivera användning av sidvisning.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Ange sidbegränsning. Sidbegränsning avgör hur många rader som visas per sida. Om sidbrytning är inaktiverad så kan detta fortfarande användas för att begränsa antalet rader som visas.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Aktivera eller inaktivera användning av sökning i raderna.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Välj radsortering.',
'DATE_ASC_a5871f'	=>	'Datum STIG',
'DATE_DESC_bcfc6d'	=>	'Datum FALL',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Gruppen finns inte.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'Du har inte rättighet att publicera videos i den här gruppen.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'Du har inte rättighet att redigera den här videon.',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'Välj publiceringsstatus för den här videon. Opublicerade videos kommer inte att vara synliga.',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'Valfritt, ange en titel för videon som kommer att visas istället för länken.',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'Ange URL till videon som skall publiceras.',
'GROUP_VIDEO_LIMITS_EXT'	=>	'Din url måste vara av följande typ [ext].',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'Valfritt, lägg till en videobild.',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'Ange ID för videoägaren. Videoägaren avgör vem som är skaparen av videon, detta specificeras av Användar ID.',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'Videon kunde inte sparas! Fel: [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'Y - M - j',
'NEW_GROUP_VIDEO_28e07a'	=>	'Ny gruppvideo',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'[user] har publicerat videon [video] i gruppen [group]!',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'Ny gruppvideo väntar på godkännande',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'[user] har publicerat videon [video] i gruppen [group] och väntar på godkännande!',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'Videon har publicerats och väntar på godkännande!',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'Videon har publicerats!',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'Videon har sparats!',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'Din video väntar på godkännande.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'Du har inte rättigheter att publicera eller avpublicera denna video.',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'Videon finns inte.',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'Videons status kunde inte sparas. Fel: [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'Begäran om videopublicering är godkänd',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'Din begäran om att få din video [video] publicerad i gruppen [group] har godkännts!',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'Video statusen har sparats!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'Du har inte behörighet att radera den här videon.',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'Videon kunde inte raderas. Fel: [error]',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'Videon har raderats!',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Ingen ägare har angetts!',
'URL_NOT_SPECIFIED_2ccd94'	=>	'URL har inte angetts!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Grupp har inte angetts!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Gruppen finns inte!',
'GROUP_VIDEO_INVALID_URL'	=>	'Ogiltig URL. Vänligen kontrollera att URLen verkligen finns!',
'GROUP_VIDEO_INVALID_EXT'	=>	'Ogiltig url länk [ext]. Du kan endast länka [exts]!',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'Videos',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'Lägg till ny Video i gruppen',
'CONFIGURATION_254f64'	=>	'Inställningar',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'Inaktivera',
'ENABLE_2faec1'	=>	'Aktivera',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Aktivera, med godkännande',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'Valfritt, aktivera eller inaktivera användning av video. Gruppägare och gruppadministratörer är undantagna från den här konfiguration och kan alltid dela videoklipp. Obs befintliga videos kommer fortfarande att vara tillgängliga.',
'DONT_NOTIFY_3ea23f'	=>	'Meddela inte',
'SEARCH_VIDEOS_e5b832'	=>	'Sök Videos...',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'publicerade en video i [group]',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'Redigera video',
'NEW_VIDEO_458670'	=>	'Ny video',
'VIDEO_34e2d1'	=>	'Video',
'CAPTION_272ba7'	=>	'Illustration',
'UPDATE_VIDEO_3e00c1'	=>	'Uppdatera video',
'PUBLISH_VIDEO_dc049f'	=>	'Publicera video',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% Video|%%COUNT%% Videos',
'AWAITING_APPROVAL_af6558'	=>	'Väntar på godkännande',
'APPROVE_6f7351'	=>	'Godkänn',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'Är du säker på att du vill avpublicera den här videon?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'Är du säker på att du vill radera den här videon?',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'Inga sökresultat för gruppvideos hittades.',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'Den här gruppen har för tillfället inga videos.',
);
