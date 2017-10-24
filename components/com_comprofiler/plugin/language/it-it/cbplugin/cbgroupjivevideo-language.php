<?php
/**
* Community Builder (TM) cbgroupjivevideo Italian (Italy) language file Frontend
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
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'Numero di input di video per ogni singolo utente è limitata a creare per gruppo. Se vuoto consentono video illimitate. Moderatori e proprietari del gruppo sono esenti da questa configurazione.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'Attivare o disattivare l\'utilizzo di captcha sui video del gruppo. Richiede ultimo CB AntiSpam da installare e pubblicato. I moderatori sono esenti da questa configurazione.',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'Pubblicare nuovo video',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'Nuovo video richiede l\'approvazione',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Abilita o disabilita l\'utilizzo della paginazione',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Digita il limite di pagina. Il limite di pagina determina il numero di righe visualizzate per pagina. Se la paginazione è disabilitata questo può ancora essere utilizzato per limitare il numero di righe visualizzate.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Attivare o disattivare l\'utilizzo di ricerca sulle righe.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Selezionare l\'ordinamento fila.',
'DATE_ASC_a5871f'	=>	'Data ASC',
'DATE_DESC_bcfc6d'	=>	'Data DESC',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Gruppo non esiste.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'Non si dispone di autorizzazioni sufficienti per pubblicare un video in questo gruppo.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'Non si dispone di autorizzazioni sufficienti per modificare il video.',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'Selezionare Pubblica stato di questo video. Video inediti non saranno visibili al pubblico.',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'Ingresso Opzionalmente un titolo video per visualizzare invece di URL.',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'Inserire l\'URL al video da pubblicare.',
'GROUP_VIDEO_LIMITS_EXT'	=>	'Il tuo url deve essere di tipo [ext].',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'Ingresso Opzionalmente un video didascalia.',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'Ingresso il proprietario del video id. Proprietario del video determina il creatore del video specificato come ID utente.',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'Video non è riuscito a salvare! Errore: [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'M g, A',
'NEW_GROUP_VIDEO_28e07a'	=>	'Video Nuovo nel gruppo',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'[user]  ha pubblicato il video [video] nel gruppo [group]!',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'Video Nuovo gruppo in attesa di approvazione',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'[user] ha pubblicato il video [video] nel gruppo [group] in attesa di approvazione!',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'Video pubblicato con successo e in attesa di approvazione!',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'Il video pubblicato con successo!',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'Il video salvato con successo!',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'Il tuo video è in attesa di approvazione.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'Non si dispone di autorizzazioni sufficienti per pubblicare o sospendere il video.',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'Il video non esiste.',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'Stato video non è riuscito a salvare. Errore: [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'Video pubblicare richiesta accettata',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'Il tuo video [video] pubblicare richiesta nel gruppo [group] è stato accettato!',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'Video stato salvato con successo!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'Non si dispone di autorizzazioni sufficienti per eliminare il video.',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'Video non è riuscito a cancellare. Errore: [error]',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'Video cancellato con successo!',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Proprietario non specificato!',
'URL_NOT_SPECIFIED_2ccd94'	=>	'URL non specificato!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Gruppo non specificato!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Gruppo non esiste!',
'GROUP_VIDEO_INVALID_URL'	=>	'URL non valido. Assicurati esiste l\'URL!',
'GROUP_VIDEO_INVALID_EXT'	=>	'Estensione URL non valido [ext]. Si prega di collegare solo [exts]!',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'Video',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'Aggiungi nuovo video di gruppo',
'CONFIGURATION_254f64'	=>	'Configurazione',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'Disabilita',
'ENABLE_2faec1'	=>	'Abilita',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Attiva, con l\'approvazione',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'Facoltativamente abilitare o disabilitare l\'uso di video. Gli amministratori di proprietario del gruppo e di gruppo sono esenti da questa configurazione e possono sempre condividere video. Nota video esistenti saranno comunque accessibili.',
'DONT_NOTIFY_3ea23f'	=>	'Non Notificati',
'SEARCH_VIDEOS_e5b832'	=>	'Cerca Video...',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'Ha Pubblicato un Video Nel [group]',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'Modifica video',
'NEW_VIDEO_458670'	=>	'nuovo Video',
'VIDEO_34e2d1'	=>	'Video',
'CAPTION_272ba7'	=>	'Titolo',
'UPDATE_VIDEO_3e00c1'	=>	'Aggiornamento Video',
'PUBLISH_VIDEO_dc049f'	=>	'Pubblicare video',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% Video|%%COUNT%% Video',
'AWAITING_APPROVAL_af6558'	=>	'In attesa di approvazione',
'APPROVE_6f7351'	=>	'Approva',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'Sei sicuro di voler annullare la pubblicazione di questo video?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'Sei sicuro di voler eliminare questo video?',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'Nessun risultato di ricerca video del gruppo trovato.',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'Questo gruppo non ha attualmente nessun video.',
);
