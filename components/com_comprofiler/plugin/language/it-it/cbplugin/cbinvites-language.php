<?php
/**
* Community Builder (TM) cbinvites Italian (Italy) language file Frontend
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
'JOIN_ME_de0c95'	=>	'Unisciti anche Tu!',
'MAILER_FAILED_TO_SEND_46f543'	=>	'Il sistema di invio email non è riuscito a trasmettere.',
'TO_ADDRESS_MISSING_225871'	=>	'Indirizzo mancante.',
'SUBJECT_MISSING_8e0db8'	=>	'Oggetto mancante',
'BODY_MISSING_b6f835'	=>	'Corpo mancante',
'SEARCH_INVITES_9e5f33'	=>	'Cerca Inviti...',
'INVITE_CODE_NOT_VALID_7cd6f7'	=>	'Codice invito non valido',
'INVITE_CODE_ALREADY_USED_cd715c'	=>	'Codice Invito già usato',
'INVITE_CODE_IS_VALID_96aad3'	=>	'Codice invito valido',
// 44 language strings from file plug_cbinvites/cbinvites.xml
'YOUR_REGISTRATION_INVITE_CODE_324b31'	=>	'Il tuo codice di invito per la registrazione.',
'INVITE_CODE_0a2eb0'	=>	'Codice Invito',
'INVITES_213b86'	=>	'Inviti',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_ON_TAB_INVITES_b76bc2'	=>	'Attivare o disattivare la paginazione su scheda inviti.',
'INPUT_PAGE_LIMIT_ON_TAB_INVITES_PAGE_LIMIT_DETERMI_b17959'	=>	'Digita il limite di pagine nella scheda inviti. Il limite di pagina determina il numero di inviti visualizzati per pagina.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_TAB_INVITES_dc5e0c'	=>	'Abilitare o disabilitare la ricerca su scheda inviti.',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_INVITES_I_e3fe43'	=>	'Seleziona modello da utilizzare per tutti inviti CB. Se il modello è incompleto file allora mancanti verranno utilizzati dal modello predefinito. I file modello possono essere posizionati nella seguente posizione: componenti / com_comprofiler / plugin / user / plug_cbinvites / templates /.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_7cf91e'	=>	'Facoltativamente aggiungere un suffisso classe DIV che stringe tutti CB invita circostante.',
'SELECT_INVITE_CREATE_ACCESS_ACCESS_DETERMINES_WHO__c4a59a'	=>	'Selezionare invitano creare l\'accesso. L\'accesso determina chi può inviare inviti. Il gruppo selezionato così come quelli sopra avrà accesso (es registrato sarà accessibile anche ai Autore). I moderatori sono esenti da questa configurazione.',
'INPUT_NUMBER_OF_INVITES_EACH_INDIVIDUAL_USER_IS_LI_0d3553'	=>	'Numero di ingresso di inviti ogni singolo utente è limitato ad avere attiva in un dato momento (inviti accettati non contano per questo limite). Se vuoto permettono invita illimitate. I moderatori sono esenti da questa configurazione.',
'INPUT_NUMBER_OF_DAYS_AFTER_INVITE_SENT_TO_ALLOW_RE_12681b'	=>	'Numero di ingresso di giorni dopo invite inviato a consentire di rispedizione (inviti accettati non consentono rispedizione). Se vuote invita il nuovo invio disabili. I moderatori sono esenti da questa configurazione.',
'RESEND_DELAY_4f8afe'	=>	'ritardo rinvio',
'SELECT_USAGE_OF_MULTIPLE_EMAILS_IN_A_SINGLE_INVITE_e8af1e'	=>	'Selezionare l\'utilizzo di più messaggi di posta elettronica in un unico invito con una virgola elenco separato (ad es email1@domain.com, email2@domain.com, email3@domain.com). I moderatori sono esenti da questa configurazione.',
'MULTIPLE_INVITES_b8f6fd'	=>	'Invii Multipli',
'SELECT_USAGE_OF_DUPLICATE_INVITES_TO_THE_SAME_ADDR_96bfd8'	=>	'Selezionare l\'uso del duplicato invita allo stesso indirizzo. I moderatori sono esenti da questa configurazione.',
'DUPLICATE_INVITES_9b3f9e'	=>	'Duplica Inviti',
'SELECT_CONNECTION_METHOD_FROM_INVITER_TO_INVITEE_32692b'	=>	'Selezionare il metodo di connessione tra chi invita e l\'invitato.',
'CONNECTION_c2cc70'	=>	'Connessione',
'PENDING_CONNECTION_30ec76'	=>	'Amicizie in attesa',
'AUTO_CONNECTION_5f8c15'	=>	'Auto Amicizia',
'SELECT_INVITE_EMAIL_BODY_EDITOR_d8f5e1'	=>	'Selezionare il tipo di editor per il corpo dell\'email di invito.',
'PLAIN_TEXT_e44b14'	=>	'Testo normale',
'HTML_TEXT_503c11'	=>	'Testo HTML',
'WYSIWYG_fcf0d4'	=>	'WYSIWYG',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_INVITES_REQU_65e72e'	=>	'Attivare o disattivare l\'utilizzo di captcha su invito. Richiede ultima CB Anti Spam da installato e pubblicato. I moderatori sono esenti da questa configurazione.',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_f0ef2c'	=>	'Ingresso una sostituzione supportato dal nome da inviare a tutte invita (ad esempio My Awesome CB sito!). Se vuoto lasciato imposterà nome utenti. Se viene specificato un nome ReplyTo verrà aggiunto come nome utenti.',
'FROM_NAME_4a4a8f'	=>	'Da nome',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_ef3c3b'	=>	'Ingresso una sostituzione supportato da indirizzo per inviare tutti gli inviti da (ad es general@domain.com). Se lasciato vuoto per impostazione predefinita per gli utenti di posta elettronica. Se viene specificato un indirizzo ReplyTo verrà aggiunto come l\'e-mail degli utenti.',
'FROM_ADDRESS_a5ab7d'	=>	'Da indirizzo',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Ingresso una sostituzione supportato indirizzo CC (ad esempio [email]); più indirizzi separati da virgole supportati con lista (ad es email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC Indirizzo',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Ingresso una sostituzione supportato indirizzo BCC (ad esempio [email]); più indirizzi separati da virgole supportati con lista (ad es email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Indirizzo',
'INPUT_SUBSTITUTION_SUPPORTED_PREFIX_OF_INVITE_EMAI_91cd80'	=>	'Sostituzione di ingresso supportato prefisso di invitare email soggetto. Subsitutions supplementari supportati: [sito], [PhicaBook], [percorso], [itemid], [Registra], [profile], [codice], e [a].',
'SUBJECT_PREFIX_175911'	=>	'Oggetto Prefisso',
'SITENAME_6b68ee'	=>	'[sitename] -',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_OF_INVITE_EMAI_058012'	=>	'Sostituzione di ingresso supportato intestazione di invitare corpo-mail. Subsitutions addizionali supportati: [site], [sitename], [path], [itemid], [register], [profile], [code], e [to].',
'BODY_HEADER_67622c'	=>	'Intestazione Corpo',
'YOU_HAVE_BEEN_INVITED_BY_USERNAME_TO_JOIN_SITENAME_e90186'	=>	'<p>Sei stato invitato da [username] ad unirti [sitename]!</p><br>',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_OF_INVITE_EMAI_f5fa57'	=>	'Sostituzione di ingresso supportato piè di invitare corpo-mail. Subsitutions addizionali supportati: [site], [sitename], [path], [itemid], [register], [profile], [code], e [to].',
'BODY_FOOTER_6046e1'	=>	'Corpo Footer',
'INVITE_CODE_CODESITENAME_SITEREGISTRATION_REGISTER_eec8c6'	=>	'<br><p>Codice Invito - [code]<br>[sitename] - [site]<br>Registrati - [register]<br>[username] - [profile]</p>',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__523216'	=>	'Ingresso una sostituzione supportato indirizzo Attachment (ad esempio [cb_myfile]); più indirizzi separati da virgole supportati con lista (ad es /home/username/public_html/images/file1.zip,[path]/file2.zip, http://www.domain.com/file3.zip). Sostituzioni supplementari supportati: [site], [sitename], [path], [itemid], [register], [profile], [code], and [to].',
'ATTACHMENT_e9cb21'	=>	'Allegato',
// 22 language strings from file plug_cbinvites/component.cbinvites.php
'INPUT_INVITE_EMAIL_TO_ADDRESS_SEPARATE_MULTIPLE_EM_2028c1'	=>	'Digita indirizzi email da invitare. Separa più indirizzi email con una virgola.',
'INPUT_INVITE_EMAIL_TO_ADDRESS_54551f'	=>	'Digita un indirizzo email da invitare.',
'INPUT_INVITE_EMAIL_SUBJECT_IF_LEFT_BLANK_A_SUBJECT_0974fc'	=>	'Digita il soggetto della email di invito; se lasci vuoto verrà inserito un testo precompilato.',
'OPTIONALLY_INPUT_PRIVATE_MESSAGE_TO_INCLUDE_WITH_I_e1d750'	=>	'Opzionalmente digita un messaggio privato da includere con l\'email di invito.',
'INPUT_OWNER_OF_INVITE_AS_SINGLE_INTEGER_USERID_THI_998c63'	=>	'Digita il proprietario dell\'invito come singolo numero intero corrispondente alla user_id. Questo è l\'utente che spedisce l\'invito.',
'OPTIONALLY_INPUT_USER_OF_INVITE_AS_SINGLE_INTEGER__329e71'	=>	'Opzionalmente digita l\'utente dell\'invito come singolo numero intero corrispondente alla user_id. Questo è l\'utente che ha accettato l\'invito.',
'COMMA_SEPERATED_LISTS_ARE_NOT_SUPPORTED_PLEASE_USE_7676e6'	=>	'Liste separate da virgola non supportate! Per favore usa un solo indirizzo di spedizione.',
'INVITE_LIMIT_REACHED_1e1e31'	=>	'Limite di invio raggiunto!',
'TO_ADDRESS_NOT_SPECIFIED_e292c0'	=>	'Indirizzo non specificato.',
'INVITE_TO_ADDRESS_INVALID'	=>	'Indirizzo non valido: [to_address]',
'YOU_CAN_NOT_INVITE_YOUR_SELF_487ade'	=>	'Non puoi invitare te stesso.',
'TO_ADDRESS_IS_ALREADY_A_USER_4c2f27'	=>	'Questo indirizzo email è già un utente.',
'TO_ADDRESS_IS_ALREADY_INVITED_f165f0'	=>	'Questo indirizzo email è già stato invitato.',
'INVITE_FAILED_SAVE_ERROR'	=>	'Invito non salvato! Errore: [error]',
'INVITE_FAILED_SEND_ERROR'	=>	'Invito non spedito! Errore: [error]',
'INVITE_SENT_SUCCESSFULLY_380490'	=>	'Invito spedito!',
'INVITE_SAVED_SUCCESSFULLY_14a90f'	=>	'Invita salvato con successo!',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_RESENT_528e3e'	=>	'Invito già accolto e non può essere inviato nuovamente.',
'INVITE_RESEND_NOT_APPLICABLE_AT_THIS_TIME_c65f19'	=>	'Invito inviare nuovamente non applicabile in questo momento.',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_DELETED_cbc934'	=>	'Invito già accolto e non può essere eliminato.',
'INVITE_FAILED_DELETE_ERROR'	=>	'Invito non eliminato! Errore: [error]',
'INVITE_DELETED_SUCCESSFULLY_9ea357'	=>	'Invito eliminato correttamente!',
// 7 language strings from file plug_cbinvites/templates/default/invite_edit.php
'EDIT_INVITE_1faaed'	=>	'Modifica Invito',
'CREATE_INVITE_1e89ce'	=>	'Crea Invito',
'TO_e12167'	=>	'A',
'BODY_ac101b'	=>	'Testo',
'USER_8f9bfe'	=>	'Utente',
'UPDATE_INVITE_7c2f89'	=>	'Aggiorna Invito',
'SEND_INVITE_962943'	=>	'Invia invito',
// 9 language strings from file plug_cbinvites/templates/default/tab.php
'NEW_INVITE_4093fa'	=>	'Nuovo invito',
'SENT_7f8c02'	=>	'Inviato',
'PLEASE_RESEND_6ba908'	=>	'Si prega di inviare di nuovo',
'ACCEPTED_382ab5'	=>	'Accettato',
'RESEND_1c0b8f'	=>	'Invia di nuovo',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_INVITE_405d09'	=>	'Sei sicuro di voler eliminare questo Invito?',
'NO_INVITE_SEARCH_RESULTS_FOUND_63c4e3'	=>	'Nessun Invito Trovato.',
'YOU_HAVE_NO_INVITES_2f8b42'	=>	'You have no invites.',
'THIS_USER_HAS_NO_INVITES_f2d878'	=>	'Questo utente non ha inviti.',
);
