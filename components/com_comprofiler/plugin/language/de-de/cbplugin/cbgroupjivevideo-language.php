<?php
/**
* Community Builder (TM) cbgroupjivevideo German (Germany) language file Frontend
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
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'Eingabe der Anzahl Videos, die jedes Mitglied pro Gruppe erstellen kann. Leergelassen ohne Beschränkung. Moderatoren und Gruppenbesitzer sind von dieser Einstellung ausgenommen.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'Aktivieren oder Deaktivieren der Verwendung von Captcha bei Gruppenvideos. Erfordert die Installation und Freigabe des aktuellsten CB AntiSpam. Moderatoren sind von dieser Einstellung ausgenommen.',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'Freigabe eines neuen Videos',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'Neues Gruppenvideo erfordert Zustimmung',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Seitenumbruch ein- oder ausschalten.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Eingabe der Seitenbegrenzung. Seitenbegrenzung bestimmt, wieviele Zeilen pro Seite angezeigt werden. Wenn Paging deaktiviert ist, kann dies dennoch  verwendet werden, um die Zahl der angezeigten Zeilen zu begrenzen.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Aktivieren oder Deaktivieren der Suche von Zeilen',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Die Art der Zeilensortierung wählen',
'DATE_ASC_a5871f'	=>	'Datum ASC',
'DATE_DESC_bcfc6d'	=>	'Datum DESC',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Gruppe existiert nicht.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'Nicht genügend Rechte, um ein Video in dieser Gruppe zu veröffentlichen.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'Nicht genügend Rechte, um dieses Video zu bearbeiten.',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'Veröffentlichungsstatus dieses Videos wählen. Unveröffentlichte Videos sind öffentlich nicht sichtbar.',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'Optionale Eingabe eines Videotitels anstelle der Anzeige der URL.',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'Eingabe der URL des zu veröffentlichenden Videos.',
'GROUP_VIDEO_LIMITS_EXT'	=>	'Die URéL muss vom Typ [ext] sein.',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'Optionale Eingabe eines Untertitels',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'Eingabe der ID des Videobesitzers. Videobesitzer bestimmt den Ersteller des Gruppenvideos mittels der Benutzer ID.',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'Video konnte nicht gespeichert werden! Fehler: [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_VIDEO_28e07a'	=>	'Neues Gruppenvideo',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'[user] hat das Video [video] in der Gruppe [group] veröffentlicht!',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'Neues Gruppenvideo wartet auf Zustimmung',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'[user] hat das Video [video] in der Gruppe [group] veröffentlicht und wartet auf Zulassung!',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'Video erfolgreich hochgeladen und wartet auf Zulassung',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'Video erfolgreich veröffentlicht!',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'Video  erfolgreich gespeichert!',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'Video wartet auf Zulassung.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'Nicht genügend Rechte, um ein Video  zu veröffentlichen oder zu sperren.',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'Ein solches Video gibt es nicht.',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'Videostatus konnte nicht gespeichert werden. Fehler: [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'Freigabe des Videos bewilligt',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'Die Freigabe des Videos [video] in der Gruppe [group] wurde bewilligt!',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'Videostatus erfolgreich gespeichert!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'Nicht genügend Rechte, um dieses Video zu löschen.',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'Video konnte nicht gelöscht werden. Fehler: [error]',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'Video erfolgreich gelöscht!',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Besitzer fehlt!',
'URL_NOT_SPECIFIED_2ccd94'	=>	'URL fehlt!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Gruppe fehlt!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Eine solche Gruppe gibt es nicht!',
'GROUP_VIDEO_INVALID_URL'	=>	'Ungültige URL. Bitte prüfen, ob die URL existiert!',
'GROUP_VIDEO_INVALID_EXT'	=>	'Ungültige URL-Erweiterung [ext]. Bitte nur [exts] verlinken!',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'Videos',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'Neues Gruppenvideo',
'CONFIGURATION_254f64'	=>	'Konfiguration',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'Deaktivieren',
'ENABLE_2faec1'	=>	'Aktivieren',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Aktivieren, mit Zustimmung',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'Optionale Aktivierung oder Deaktivierung der Verwendung von Videos. Gruppenbesitzer und Gruppenadministratoren sind von dieser Einstellung ausgenommen und können immer Videos teilen.  Zu beachten: Existierende Videos sind immer zugänglich.',
'DONT_NOTIFY_3ea23f'	=>	'Nicht benachrichtigen',
'SEARCH_VIDEOS_e5b832'	=>	'Videos durchsuchen....',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'hat ein Video in [group] veröffentlicht',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'Video bearbeiten',
'NEW_VIDEO_458670'	=>	'Neues Video',
'VIDEO_34e2d1'	=>	'Video',
'CAPTION_272ba7'	=>	'Untertitel',
'UPDATE_VIDEO_3e00c1'	=>	'Video aktualisieren',
'PUBLISH_VIDEO_dc049f'	=>	'Video veröffentlichen',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% Video|%%COUNT%% Videos',
'AWAITING_APPROVAL_af6558'	=>	'Wartet auf Freigabe',
'APPROVE_6f7351'	=>	'Zustimmen',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'Dieses Video wirklich sperren?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'Dieses Video wirklich löschen?',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'Kein solches Gruppenvideo gefunden.',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'Diese Gruppe hat aktuell keine Videos.',
);
