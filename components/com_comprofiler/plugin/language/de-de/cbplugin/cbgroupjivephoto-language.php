<?php
/**
* Community Builder (TM) cbgroupjivephoto German (Germany) language file Frontend
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
'INPUT_NUMBER_OF_PHOTOS_EACH_INDIVIDUAL_USER_IS_LIM_11b810'	=>	'Eingabe der Anzahl Fotos, die jedes Mitglied erstellen darf. Leergelassen gibt es keine Beschränkung. Moderatoren und Gruppenbesitzer sind von dieser Eiinstellung ausgenommen.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_PHOTOS_0ea9f4'	=>	'Aktivieren oder Deaktivieren der Verwendung von Captchas bei den Gruppenfotos. Erfordert die Installation und Freigabe des aktuellsten CB AntiSpam. Moderatoren sind von dieser Einstellung ausgenommen.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'Wählen, ob hochgeladene Bilder immer neu bearbeitet werden sollen. Neubearbeitung fügt zusätzliche Sicherheit hinzu, aber Animationen bleiben nur erhalten, wenn ImageMagick verwendet wird.',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'Eingabe der maximalen Höhe in Pixel, zu der das Bild angepasst wird.',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'Eingabe der maximalen Breite in Pixel, zu der das Bild angepasst wird.',
'INPUT_THE_MAXIMUM_THUMBNAIL_HEIGHT_IN_PIXELS_THAT__9d2b57'	=>	'Eingabe der maximalen Thumbnail-Höhe in Pixel, zu der das Bild angepasst wird.',
'INPUT_THE_MAXIMUM_THUMBNAIL_WIDTH_IN_PIXELS_THAT_T_d159f4'	=>	'Eingabe der maximalen Thumbnail-Breite in Pixel, zu der das Bild angepasst wird.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'Wählen, ob hochgeladene Bilder das Seitenverhältnis aufrechterhalten sollen, wenn das Bild angepasst wird. Bei Nein wird das Bild immer auf das angegebene Maximum von Breite und Höhe angepasst. Bei Ja wird das Seitenverhältnis soweit möglich aufrechterhalten innerhalb der angegebenen Maximalbreite und -Höhe. Bei Ja mit Beschneiden wird das Bild immer auf die Maximalbreite und -höhe angepasst unter Aufrechterhaltung des Seitenverhältnisses, aber mit Abschneiden des Überflüssigen; Dies ist nützlich für quadratische Bilder.',
'INPUT_THE_MINIMUM_IMAGE_FILE_SIZE_IN_KBS_30eae6'	=>	'Eingabe der minimalen Bilddateigrösse in KBs.',
'INPUT_THE_MAXIMUM_IMAGE_FILE_SIZE_IN_KBS_SET_TO_0__f73680'	=>	'Eingabe der maximalen Bildgrösse in KBs. 0 für unbegrenzt.',
'UPLOAD_OF_NEW_PHOTO_1831ae'	=>	'Neues Foto hochladen',
'NEW_PHOTO_REQUIRES_APPROVAL_d212d4'	=>	'Neues Foto braucht Zustimmung',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Seitenumbruch ein- oder ausschalten.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Eingabe der Seitenbegrenzung. Seitenbegrenzung bestimmt, wieviele Zeilen pro Seite angezeigt werden. Wenn Paging deaktiviert ist, kann dies dennoch  verwendet werden, um die Zahl der angezeigten Zeilen zu begrenzen.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Aktivieren oder Deaktivieren der Suche von Zeilen',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Die Art der Zeilensortierung wählen',
'DATE_ASC_a5871f'	=>	'Datum ASC',
'DATE_DESC_bcfc6d'	=>	'Datum DESC',
'FILENAME_ASC_44f721'	=>	'Dateiname ASC',
'FILENAME_DESC_13d728'	=>	'Dateiname DESC',
// 31 language strings from file cbgroupjivephoto/component.cbgroupjivephoto.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Gruppe existiert nicht.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_c06972'	=>	'Nicht genügend Rechte, um ein Gruppenfoto hochzuladen.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_6eccd3'	=>	'Nicht genügend Rechte, um dieses Gruppenfoto zu bearbeiten.',
'SELECT_PUBLISH_STATE_OF_THIS_PHOTO_UNPUBLISHED_PHO_75ff3b'	=>	'Veröffentlichungsstatus dieses Fotos wählen. Nicht freigegebene Fotos sind öffentlich nicht sichtbar.',
'OPTIONALLY_INPUT_A_PHOTO_TITLE_TO_DISPLAY_INSTEAD__323e09'	=>	'Optionale Eingabe eines Fototitels, der anstelle des Dateinamens angezeigt werden soll.',
'SELECT_THE_PHOTO_TO_UPLOAD_8e29df'	=>	'Das hochzuladende Bild wählen.',
'GROUP_PHOTO_LIMITS_EXT'	=>	'Das Foto muss vom Typ [ext] sein.',
'GROUP_PHOTO_LIMITS_MIN'	=>	'Das Foto sollte grösser als [size] sein.',
'GROUP_PHOTO_LIMITS_MAX'	=>	'Das Foto sollte [size] nicht übersteigen.',
'OPTIONALLY_INPUT_A_PHOTO_DESCRIPTION_d4c183'	=>	'Optionale Eingabe einer Beschreibung des Gruppenfotos.',
'INPUT_THE_PHOTO_OWNER_ID_PHOTO_OWNER_DETERMINES_TH_eb7b03'	=>	'Eingabe der ID des Fotosbesitzers. Fotobesitzer bestimmt den Ersteller des Gruppenfotos mittels der Benutzer ID.',
'GROUP_PHOTO_FAILED_TO_SAVE'	=>	'Foto konnte nicht gespeichert werden! Fehler: [error]',
'GROUP_PHOTO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_PHOTO_9ba416'	=>	'Neues Gruppenfoto',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_429420'	=>	'[user] hat das Foto [photo] in der Gruppe [group] hochgeladen!',
'NEW_GROUP_PHOTO_AWAITING_APPROVAL_221466'	=>	'Neues Gruppenfoto wartet auf Zustimmung.',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_e90ac6'	=>	'[user] hat das Foto [photo] in der Gruppe [group] hochgeladen und wartet auf Zustimmung!',
'PHOTO_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_492ec3'	=>	'Photo erfolgreich hochgeladen und wartet auf Zulassung!',
'PHOTO_UPLOADED_SUCCESSFULLY_0b02f3'	=>	'Foto erfolgreich hochgeladen!',
'PHOTO_SAVED_SUCCESSFULLY_726f86'	=>	'Foto erfolgreich gespeichert!',
'YOUR_PHOTO_IS_AWAITING_APPROVAL_d5f199'	=>	'Foto wartet auf Zustimmung',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__dce5c6'	=>	'Nicht genügend Rechte, um dieses Gruppenfoto freizugeben oder zu sperren .',
'PHOTO_DOES_NOT_EXIST_cd6623'	=>	'Ein solches Gruppenfoto gibt es nicht.',
'GROUP_PHOTO_STATE_FAILED_TO_SAVE'	=>	'Fotostatus konnte nicht gespeichert werden. Fehler: [error]',
'PHOTO_UPLOAD_REQUEST_ACCEPTED_bf6572'	=>	'Hochladen des Fotos akzeptiert.',
'YOUR_PHOTO_PHOTO_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_9728ad'	=>	'Anfrage zum Hochladen der Foto [photo] in der Gruppe [group] wurde bewilligt!',
'PHOTO_STATE_SAVED_SUCCESSFULLY_2f8a03'	=>	'Fotostatus erfolgreich gespeichert!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8777b2'	=>	'Nicht genügend Rechte, um dieses Gruppenfoto zu löschen.',
'GROUP_PHOTO_FAILED_TO_DELETE'	=>	'Foto konnte nicht gelöscht werden. Fehler: [error]',
'PHOTO_DELETED_SUCCESSFULLY_a9f27f'	=>	'Foto erfolgreich gelöscht!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_PHOTO_5ca855'	=>	'Kein Zugriff zu dieser Gruppenfoto',
// 7 language strings from file cbgroupjivephoto/library/Table/PhotoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Besitzer fehlt!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Gruppe fehlt!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Eine solche Gruppe gibt es nicht!',
'GROUP_PHOTO_UPLOAD_INVALID_EXT'	=>	'Ungültige Fotoerweiterung [ext]. Bitte nur [exts] hochladen.',
'GROUP_PHOTO_UPLOAD_TOO_SMALL'	=>	'Foto ist zu klein, minimale Grösse ist [size]',
'GROUP_PHOTO_UPLOAD_TOO_LARGE'	=>	'Foto überschreitet die Maximalgrösse von [size]!',
'PHOTO_NOT_SPECIFIED_dd1bfc'	=>	'Foto fehlt!',
// 3 language strings from file cbgroupjivephoto/library/Trigger/AdminTrigger.php
'PHOTOS_5daaf2'	=>	'Fotos',
'ADD_NEW_PHOTO_TO_GROUP_2df00d'	=>	'Neues Gruppenfoto hinzufügen',
'CONFIGURATION_254f64'	=>	'Konfiguration',
// 6 language strings from file cbgroupjivephoto/library/Trigger/PhotoTrigger.php
'DISABLE_bcfacc'	=>	'Deaktivieren',
'ENABLE_2faec1'	=>	'Aktivieren',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Aktivieren mit Zustimmung',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_PHOTOS_GROUP_44b054'	=>	'Optionale Aktivierung oder Deaktivierung der Verwendung von Gruppenfotos. Gruppenbesitzer und Gruppenadministratoren sind von dieser Einstellung ausgenommen und können immer Gruppenbilder hochladen. Zu beachten: Vorhandene Fotos sind immer zugänglich.',
'DONT_NOTIFY_3ea23f'	=>	'Nicht benachrichtigen',
'SEARCH_PHOTOS_e11345'	=>	'Fotos durchsuchen...',
// 2 language strings from file cbgroupjivephoto/templates/default/activity.php
'GROUP_PHOTO_ACTIVITY_TITLE'	=>	'hat ein Foto in [group] hochgeladen',
'ORIGINAL_0a52da'	=>	'Original',
// 6 language strings from file cbgroupjivephoto/templates/default/photo_edit.php
'EDIT_PHOTO_68ffc9'	=>	'Foto bearbeiten',
'NEW_PHOTO_50a153'	=>	'Neues Foto',
'PHOTO_c03d53'	=>	'Foto',
'DESCRIPTION_b5a7ad'	=>	'Beschreibung',
'UPDATE_PHOTO_89bc50'	=>	'Foto aktualisieren',
'UPLOAD_PHOTO_05e477'	=>	'Foto hochladen',
// 7 language strings from file cbgroupjivephoto/templates/default/photos.php
'GROUP_PHOTOS_COUNT'	=>	'%%COUNT%% Foto|%%COUNT%% Fotos',
'AWAITING_APPROVAL_af6558'	=>	'Wartet auf Freigabe',
'APPROVE_6f7351'	=>	'Zustimmen',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_PHOTO_31f072'	=>	'Dieses Foto wirklich sperren?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PHOTO_4d3e2d'	=>	'Dieses Foto wirklich löschen?',
'NO_GROUP_PHOTO_SEARCH_RESULTS_FOUND_64adc0'	=>	'Kein solches Foto gefunden.',
'THIS_GROUP_CURRENTLY_HAS_NO_PHOTOS_8939ef'	=>	'Diese Gruppe hat aktuell keine Fotos.',
);
