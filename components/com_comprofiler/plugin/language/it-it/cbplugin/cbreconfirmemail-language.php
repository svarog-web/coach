<?php
/**
* Community Builder (TM) cbreconfirmemail Italian (Italy) language file Frontend
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
'CHANGED_820dbd'	=>	'Cambiato',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Sostituzione di ingresso supportato messaggio visualizzato dopo aver cambiato indirizzo e-mail. Lascia in bianco per visualizzare alcun messaggio.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'Il tuo indirizzo e-mail è cambiato e richiede la riconferma. Si prega di verificare il tuo nuovo indirizzo e-mail per il tuo e-mail di conferma.',
'NOTIFICATION_96d008'	=>	'Notifica',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Ingresso una sostituzione supportato dal nome da inviare con tutto riconfermare e-mail (ad esempio My Awesome CB sito!). Se vuoto lasciato imposterà nome utenti. Se viene specificato un nome ReplyTo verrà aggiunto come nome utenti.',
'FROM_NAME_4a4a8f'	=>	'Da nome',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Ingresso una sostituzione supportato da indirizzo a cui inviare tutte le email Riconferma da (ad es general@domain.com). Se lasciato vuoto per impostazione predefinita per gli utenti di posta elettronica. Se viene specificato un indirizzo ReplyTo verrà aggiunto come l\'e-mail degli utenti.',
'FROM_ADDRESS_a5ab7d'	=>	'Da indirizzo',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Sostituzione di ingresso supportato riconferma email argomento.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Il tuo indirizzo e-mail è cambiato',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Html ingresso e la sostituzione supportato corpo-mail riconferma. Fornitura [riconferma] per visualizzare il link di conferma. Inoltre [old_email] può essere utilizzato per visualizzare il vecchio indirizzo e-mail o [new_email] per visualizzare il nuovo indirizzo email.',
'BODY_ac101b'	=>	'Corpo',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'L\'indirizzo email collegato al tuo account [username] ha cambiato [new_email]e richiede la conferma.<br><br>È possibile confermare il tuo indirizzo di posta elettronica cliccando sul seguente link:<br><a href="[reconfirm]">[reconfirm]</a><br><br>Se questo è stato fatto per errore, contatta l\'amministrazione o annullare da<a href="[cancel]">cliccando qui</a>.',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Ingresso una sostituzione supportato indirizzo CC (ad esempio [email]); più indirizzi separati da virgole supportati con lista (ad es email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC Indirizzo',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Ingresso una sostituzione supportato indirizzo BCC (ad esempio [email]); più indirizzi separati da virgole supportati con lista (ad es email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Indirizzo',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Ingresso una sostituzione supportato indirizzo Attachment (ad esempio [cb_myfile]); più indirizzi separati da virgole supportati con lista (ad es /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip).',
'ATTACHMENT_e9cb21'	=>	'Allegato',
'RECONFIRMED_e748a2'	=>	'riconfermato',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Sostituzione di input supportati URL per reindirizzare a riconferma dopo il successo. Se lasciato vuoto verrà eseguito alcun reindirizzamento.',
'REDIRECT_4202ef'	=>	'Redirect',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Sostituzione di ingresso supportato messaggio visualizzato dopo riconferma di successo.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'Nuovo indirizzo email confermato con successo!',
'CANCELLED_a149e8'	=>	'Annullato',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Sostituzione di input supportati URL per reindirizzare a dopo l\'annullamento con successo il cambiamento e-mail. Se lasciato vuoto verrà eseguito alcun reindirizzamento.',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Sostituzione di ingresso supportato messaggio visualizzato dopo aver annullato con successo il cambiamento e-mail.',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'Email cambiamento di indirizzo annullato con successo!',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Confermare il codice mancante.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'Utente non associato con il codice di conferma.',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Confermare il codice non è valido.',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Impossibile annullare la modifica dell\'indirizzo e-mail! errore: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Impossibile confermare il nuovo indirizzo e-mail! errore: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'Indirizzo e-mail è già stato confermato.',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'Email',
);
