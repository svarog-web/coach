<?php
/**
* Community Builder (TM) cbreconfirmemail German (Germany) language file Frontend
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
'CHANGED_820dbd'	=>	'Geändert',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Eingabe einer durch Substitution unterstützten Mitteilung, die nach dem Wechsel der E-Mailadresse angezeigt wird. Leerlassen, um keine Mitteilung anzuzeigen.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'Die E-Mailadresse wurde geändert und erfordert eine neue Bestätigung. Bitte die neue E-Mailadresse für die Bestätigungs-Mail überprüfen.',
'NOTIFICATION_96d008'	=>	'Benachrichtigung',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Eingabe eines durch Substitution unterstützten Absenders, der mit allen Bestätigungs-E-Mails gesendet wird (z.B. Meine tolle CB Seite!). Leergelassen ist der Standard der Benutzername. Wenn ein Antworten-an Name bestimmt wird',
'FROM_NAME_4a4a8f'	=>	'Von Name',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Eingabe eines durch Substitution unterstützten Absenders für die Bestätigungs-E-Mails (z.B. general@domain.com). Leergelassen ist die E-Mail des Benutzers Standard. Wenn eine Antworten-An Adresse angegeben wird, wird diese als Benutzer-E-Mail hinzugefügt.',
'FROM_ADDRESS_a5ab7d'	=>	'Von Adresse',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Eingabe eines durch Substitution unterstützten Betreff für die Bestätigungs-E-Mail.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Die E-Mail wurde geändert',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Eingabe eines durch html und Substitution unterstützen Bestätigungs-E-Mail-Textes. [reconfirm] verwenden, um den Bestätigungs-Link anzugeben. Zusätzlich kann [old_email] verwendet werden, um die alte E-Mailadresse anzuzeigen oder
[new_email], um die neue E-Mailadresse anzuzeigen.',
'BODY_ac101b'	=>	'Text',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'Die E-Mailadresse zum angegebenen Konto [username] wurde geändert zu [new_email] und erfordert eine Bestätigung.<br><br>Die E-Mailadresse kann bestätigt werden durch Klicken auf den folgenden Link:<br><a href="[reconfirm]">[reconfirm]</a><br><br>Wurde dies fälschlicherweise getan, bitte die Administration kontaktieren oder Abbrechen durch <a href="[cancel]">Hier klicken</a>.',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Eine durch Substitution unterstützte CC Adresse eingeben (z.B. [email]); mehrfache Adressen durch Komma getrennt werden unterstützt (z.B. email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC Adresse',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Eine durch Substitution unterstützte BCC Adresse eingeben (z.B. [email]); mehrfache Adressen durch Komma getrennt werden unterstützt (z.B. email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Adresse',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Eingabe einer durch Substitution unterstützten Anhang-Adresse (z.B.[ cb_myfile]); Mehrfach-Adressen mit Komma getrennter Liste werden unterstützt. (z.B. /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip).',
'ATTACHMENT_e9cb21'	=>	'Anhang',
'RECONFIRMED_e748a2'	=>	'Bestätigt',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Eingabe einer durch Substitution unterstützten URL zur Umleitung nach einer erfolgreichen Bestätigung. Leergelassen gibt es keine Umleitung',
'REDIRECT_4202ef'	=>	'Umleitung',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Eingabe einer durch Substitution unterstützten Mitteilung, die nach erfolgreicher Bestätigung angezeigt wird.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'Neue E-Mailadresse erfolgreich bestätigt!',
'CANCELLED_a149e8'	=>	'Abgebrochen',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Eingabe einer durch Substitution unterstützten URL zur Umleitung nach einem erfolgreichen Abbruch einer E-Mailänderung. Leergelassen gibt es keine Umleitung',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Eingabe einer durch Substitution unterstützen Mitteilung, die nach einem erfolgreichen Abbruch einer E-Mailänderung angezeigt wird.',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'Änderung der E-Mailadresse erfolgreich abgebrochen!',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Bestätigungscode fehlt.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'Benutzer ist nicht mit dem Bestätigungscode verbunden.',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Bestätigungscode ist ungültig.',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Abbruch der Änderung der E-Mailadressse misslungen! Fehler: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Neue E-Mailadresse konnte nicht bestätigt werden! Fehler: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'Die E-Mail-Adresse ist bereits bestätigt.',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'E-Mails',
);
