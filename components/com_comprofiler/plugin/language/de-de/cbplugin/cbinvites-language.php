<?php
/**
* Community Builder (TM) cbinvites German (Germany) language file Frontend
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
'JOIN_ME_de0c95'	=>	'Tritt bei!',
'MAILER_FAILED_TO_SEND_46f543'	=>	'E-Mail konnte nicht gesendet werden.',
'TO_ADDRESS_MISSING_225871'	=>	'Adressat fehlt.',
'SUBJECT_MISSING_8e0db8'	=>	'Betreff fehlt.',
'BODY_MISSING_b6f835'	=>	'Text fehlt.',
'SEARCH_INVITES_9e5f33'	=>	'Durchsuche Einladungen...',
'INVITE_CODE_NOT_VALID_7cd6f7'	=>	'Einladungcode ist ungültig.',
'INVITE_CODE_ALREADY_USED_cd715c'	=>	'Einladungscode wurde schon verwendet.',
'INVITE_CODE_IS_VALID_96aad3'	=>	'Einladungscode ist gültig.',
// 44 language strings from file plug_cbinvites/cbinvites.xml
'YOUR_REGISTRATION_INVITE_CODE_324b31'	=>	'Individueller Einladungscode',
'INVITE_CODE_0a2eb0'	=>	'Einladungscode',
'INVITES_213b86'	=>	'Einladungen',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_ON_TAB_INVITES_b76bc2'	=>	'Aktiviert oder Deaktiviert den Seitenumbruch bei Einladungs-Tab.',
'INPUT_PAGE_LIMIT_ON_TAB_INVITES_PAGE_LIMIT_DETERMI_b17959'	=>	'Seitenbegrenzung bei Einladungs-Tab eingeben. Seitenbegrenzung legt fest, wieviele Einladungen pro Seite angezeigt werden.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_TAB_INVITES_dc5e0c'	=>	'Aktiviert oder Deaktiviert den Gebrauch der Suchfunktion bei Einladungs-Tab.',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_INVITES_I_e3fe43'	=>	'Template für alle CB Einladungen wählen. Wenn Template nicht komplett ist, dann werden die fehlenden Dateien vom Standardtemplate benutzt. Template-Dateien können hier gefunden werden: components/com_comprofiler/plugin/user/plug_cbinvites/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_7cf91e'	=>	'Optional kann ein Klassen-Suffix für das alle CB Einladungen umgebende DIV vergeben werden.',
'SELECT_INVITE_CREATE_ACCESS_ACCESS_DETERMINES_WHO__c4a59a'	=>	'Zugang zur Einladungs-Erstellung wählne. Der Zugriff bestimmt, wer Einladung versenden darf. Mitglieder einer Gruppe oder höher, erhalten Zugangsrechte (z.B. falls "registrierte", dann auch "Autoren"). Moderatoren sind von der Festlegung ausgenommen.',
'INPUT_NUMBER_OF_INVITES_EACH_INDIVIDUAL_USER_IS_LI_0d3553'	=>	'Anzahl der Einladungen, die ein einzelner Nutzer zu einer bestimmten Zeit aktiv halten  kann. (Angenommene Einladungen werden nicht zu dieser Begrenzung gezählt). Falls leer gelassen, uneingeschränkte Anzahl Einladungen. Moderatoren sind von dieser Festlegung nicht betroffen.',
'INPUT_NUMBER_OF_DAYS_AFTER_INVITE_SENT_TO_ALLOW_RE_12681b'	=>	'Eingabe der Anzahl Tage,  nach der ein Wiederversenden der Einladung erlaubt wird (akzeptierte Einladungen erlauben kein Wiederversenden). Leeergelassen wird Wiederversenden deaktiviert. Moderatoren sind von dieser Einstellung ausgenommen.',
'RESEND_DELAY_4f8afe'	=>	'Verzögerung für erneutes Senden',
'SELECT_USAGE_OF_MULTIPLE_EMAILS_IN_A_SINGLE_INVITE_e8af1e'	=>	'Verwendung von mehrfachen E-Mails in einer einzelnen Einladung wählen, E-Mails mit Komma getrennt (z.B. email1@domain.com, email2@domain.com, email3@domain.com). Moderatoren sind von dieser Einstellung ausgenommen.',
'MULTIPLE_INVITES_b8f6fd'	=>	'Mehrfach-Einladungen',
'SELECT_USAGE_OF_DUPLICATE_INVITES_TO_THE_SAME_ADDR_96bfd8'	=>	'Verwendung von doppelten Einladungen an dieselbe Adresse wählen. Moderatoren sind von dieser Einstellung ausgenommen.',
'DUPLICATE_INVITES_9b3f9e'	=>	'Einladungen kopieren',
'SELECT_CONNECTION_METHOD_FROM_INVITER_TO_INVITEE_32692b'	=>	'Verbindungsmethode wählen zwischen einladender und eingeladener Person',
'CONNECTION_c2cc70'	=>	'Verbindung',
'PENDING_CONNECTION_30ec76'	=>	'Wartende Verbindung',
'AUTO_CONNECTION_5f8c15'	=>	'Automatische Verbindung',
'SELECT_INVITE_EMAIL_BODY_EDITOR_d8f5e1'	=>	'Den Editor für den Einladungs-E-Mail-Text wählen',
'PLAIN_TEXT_e44b14'	=>	'Nur Text',
'HTML_TEXT_503c11'	=>	'HTML Text',
'WYSIWYG_fcf0d4'	=>	'WYSIWYG',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_INVITES_REQU_65e72e'	=>	'Ein- oder Ausschalten des Captcha für Einladungen. Die letzte Version des CB AntiSpam muss installiert und aktiviert sein. Moderatoren sind davon ausgenommen.',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_f0ef2c'	=>	'Einen mit Substitutionen unterstützten Absender, der mit allen Einladungen gesendet werden soll (z.B. Meine tolle CB Seite!) Leergelassen ist der Standard der Benutzername. Wenn ein Antworten-An Name angegeben ist, wird dieser als Benutzername verwendet.',
'FROM_NAME_4a4a8f'	=>	'Von Name',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_ef3c3b'	=>	'Einen durch Substitutionen unterstützten Absender, der mit allen Einladungen gesendet werden soll, eingeben (z.B. Meine tolle CB standard@domain.com).
Leergelassen ist der Standard die Benutzer-E-Mail. Wenn ein Antworten-An Name angegeben ist, wird dieser als Benutzername verwendet.',
'FROM_ADDRESS_a5ab7d'	=>	'Von Adresse',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Eine durch Substitution unterstützte CC Adresse eingeben (z.B. [email]); mehrfache Adressen durch Komma getrennt werden unterstützt (z.B. email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC Adresse',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Eine durch Substitution unterstützte BCC Adresse eingeben (z.B. [email]); mehrfache Adressen durch Komma getrennt werden unterstützt (z.B. email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Adresse',
'INPUT_SUBSTITUTION_SUPPORTED_PREFIX_OF_INVITE_EMAI_91cd80'	=>	'Eingabe eines durch Substitutionen unterstützten Präfix des E-Mail-Betreffs. Zusätzlich werden folgende Substitutionen unterstützt: [site], [sitename], [path], [itemid], [register], [profile], [code], und [to].',
'SUBJECT_PREFIX_175911'	=>	'Präfix für Betreff',
'SITENAME_6b68ee'	=>	'[sitename] - ',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_OF_INVITE_EMAI_058012'	=>	'Eingabe der durch Substitutions unterstützten Kopfzeile des E-Mail-Textes. Zusätzlich werden folgende Substitutionen unterstützt: [site], [sitename], [path], [itemid], [register], [profile], [code], und [to].',
'BODY_HEADER_67622c'	=>	'Kopfzeile',
'YOU_HAVE_BEEN_INVITED_BY_USERNAME_TO_JOIN_SITENAME_e90186'	=>	'<p>Einladung von [username] für [sitename]!</p><br>',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_OF_INVITE_EMAI_f5fa57'	=>	'Eingabe der durch Substitutions unterstützten Fusszeile des E-Mail-Textes. Zusätzlich werden folgende Substitutionen unterstützt: [site], [sitename], [path], [itemid], [register], [profile], [code], und [to].',
'BODY_FOOTER_6046e1'	=>	'Fusszeile',
'INVITE_CODE_CODESITENAME_SITEREGISTRATION_REGISTER_eec8c6'	=>	'<br><p>Einladungscode - [code]<br>[sitename] - [site]<br>Registrierung - [register]<br>[username] - [profile]</p>',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__523216'	=>	'Eingabe einer durch Substitutionen unterstützten Anhang-Adresse (z.B. [cb_myfile]); mehrfache Adressen unterstützt mit Komma getrennter Liste (z.B. /home/username/public_html/images/file1.zip, [path]/file2.zip, http://www.domain.com/file3.zip). Zusätzlich unterstützte Substitutionen: [site], [sitename], [path], [itemid], [register], [profile], [code], und [to].',
'ATTACHMENT_e9cb21'	=>	'Anhang',
// 22 language strings from file plug_cbinvites/component.cbinvites.php
'INPUT_INVITE_EMAIL_TO_ADDRESS_SEPARATE_MULTIPLE_EM_2028c1'	=>	'E-Mailadresse für Adressat der Einladungs-E-Mail eingeben. Mehrere E-Mailadressen mit Komma trennen.',
'INPUT_INVITE_EMAIL_TO_ADDRESS_54551f'	=>	'E-Mailadresse für Adressat der Einladungs-E-Mail eingeben',
'INPUT_INVITE_EMAIL_SUBJECT_IF_LEFT_BLANK_A_SUBJECT_0974fc'	=>	'Betreff für Einladungs-E-Mail eingeben; leergelassen wird ein Betreff angewendet.',
'OPTIONALLY_INPUT_PRIVATE_MESSAGE_TO_INCLUDE_WITH_I_e1d750'	=>	'Optionale Eingabe einer persönlichen Mitteilung im Rahmen der Einladungs-E-Mail.',
'INPUT_OWNER_OF_INVITE_AS_SINGLE_INTEGER_USERID_THI_998c63'	=>	'Eingabe des Einladungs-Autors als Ganzzahl user_id. Dies ist der Benutzer, der die Einladung versendete.',
'OPTIONALLY_INPUT_USER_OF_INVITE_AS_SINGLE_INTEGER__329e71'	=>	'Optionale Eingabe des Einladungs-Benutzers als Ganzzahl user_id. Dies ist der Benutzer, der die Einladung angenommen hat.',
'COMMA_SEPERATED_LISTS_ARE_NOT_SUPPORTED_PLEASE_USE_7676e6'	=>	'Mit Kommas getrennte Listen werden nicht unterstützt! Bitte einen einzelnen Adressaten wählen',
'INVITE_LIMIT_REACHED_1e1e31'	=>	'Maximalzahl von Einladungen errreicht!',
'TO_ADDRESS_NOT_SPECIFIED_e292c0'	=>	'Kein Adressat bestimmt.',
'INVITE_TO_ADDRESS_INVALID'	=>	'Adressat ist nicht gültig: [to_address]',
'YOU_CAN_NOT_INVITE_YOUR_SELF_487ade'	=>	'Selbst-Einladungen sind nicht möglich!',
'TO_ADDRESS_IS_ALREADY_A_USER_4c2f27'	=>	'Adressat ist schon Benutzer.',
'TO_ADDRESS_IS_ALREADY_INVITED_f165f0'	=>	'Adressat ist schon eingeladen.',
'INVITE_FAILED_SAVE_ERROR'	=>	'Einladung konnte nicht gespeichert werden! Fehler: [error]',
'INVITE_FAILED_SEND_ERROR'	=>	'Einladung konnte nicht versendet werden! Fehler: [error]',
'INVITE_SENT_SUCCESSFULLY_380490'	=>	'Einladung wurde erfolgreich versendet!',
'INVITE_SAVED_SUCCESSFULLY_14a90f'	=>	'Einladung wurde erfolgreich gespeichert!',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_RESENT_528e3e'	=>	'Einladung wurde schon angenommen und kann nicht nochmals gesendet werden.',
'INVITE_RESEND_NOT_APPLICABLE_AT_THIS_TIME_c65f19'	=>	'Erneute Sendung der Einladung geht zur Zeit nicht.',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_DELETED_cbc934'	=>	'Einladung wurde schon angenommen und kann nicht gelöscht werden.',
'INVITE_FAILED_DELETE_ERROR'	=>	'Einladung konnte nicht gelöscht werden! Fehler: [error]',
'INVITE_DELETED_SUCCESSFULLY_9ea357'	=>	'Einladung erfolgreich gelöscht!',
// 7 language strings from file plug_cbinvites/templates/default/invite_edit.php
'EDIT_INVITE_1faaed'	=>	'Einladung bearbeiten',
'CREATE_INVITE_1e89ce'	=>	'Einladung erstellen',
'TO_e12167'	=>	'An',
'BODY_ac101b'	=>	'Text',
'USER_8f9bfe'	=>	'Benutzer',
'UPDATE_INVITE_7c2f89'	=>	'Einladung aktualisieren',
'SEND_INVITE_962943'	=>	'Einladung senden',
// 9 language strings from file plug_cbinvites/templates/default/tab.php
'NEW_INVITE_4093fa'	=>	'Neue Einladung',
'SENT_7f8c02'	=>	'Gesendet',
'PLEASE_RESEND_6ba908'	=>	'Bitte erneut senden',
'ACCEPTED_382ab5'	=>	'Akzeptiert',
'RESEND_1c0b8f'	=>	'Erneut senden',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_INVITE_405d09'	=>	'Soll diese Einladung wirklich gelöscht werden?',
'NO_INVITE_SEARCH_RESULTS_FOUND_63c4e3'	=>	'Suche nach Einladungen erfolglos.',
'YOU_HAVE_NO_INVITES_2f8b42'	=>	'Keine Einladungen vorhanden',
'THIS_USER_HAS_NO_INVITES_f2d878'	=>	'Derzeit keine offenen Einladungen.',
);
