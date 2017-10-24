<?php
/**
* Community Builder (TM) cbgroupjivephoto Italian (Italy) language file Frontend
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
'INPUT_NUMBER_OF_PHOTOS_EACH_INDIVIDUAL_USER_IS_LIM_11b810'	=>	'Numero di input di foto ogni singolo utente è limitata a creare per gruppo. Se vuoto consentono foto illimitate. Moderatori e proprietari del gruppo sono esenti da questa configurazione.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_PHOTOS_0ea9f4'	=>	'Attivare o disattivare l\'utilizzo di captcha su foto di gruppo. Richiede ultimo CB AntiSpam da installare e pubblicato. I moderatori sono esenti da questa configurazione.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'Scegliere se le immagini caricate devono essere sempre ricampionate. Ricampionamento aggiunge maggiore sicurezza, ma le animazioni saranno conservati solo quando si utilizza ImageMagick.',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'Ingresso l\'altezza massima in pixel che l\'immagine verrà ridimensionata.',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'Immettere la larghezza massima in pixel che l\'immagine verrà ridimensionata.',
'INPUT_THE_MAXIMUM_THUMBNAIL_HEIGHT_IN_PIXELS_THAT__9d2b57'	=>	'Ingresso l\'altezza massima delle miniature in pixel che l\'immagine verrà ridimensionata.',
'INPUT_THE_MAXIMUM_THUMBNAIL_WIDTH_IN_PIXELS_THAT_T_d159f4'	=>	'Immettere la larghezza massima delle miniature in pixel che l\'immagine verrà ridimensionata.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'Scegliere se le immagini caricate dovrebbero mantenere il loro rapporto di aspetto quando si ridimensionano. Se impostato su no l\'immagine verrà sempre ridimensionata alla larghezza massima specificata e altezza. Se impostato su Sì, il rapporto di aspetto sarà mantenuto il più possibile all\'interno della larghezza e altezza massime. Se impostato su Sì con Ritagliare l\'immagine sarà sempre ridimensionare alla larghezza massima specificata e altezza all\'interno le proporzioni e ritagliare qualsiasi troppo pieno; questo è utile per mantenere le immagini squadrate.',
'INPUT_THE_MINIMUM_IMAGE_FILE_SIZE_IN_KBS_30eae6'	=>	'Inserire la dimensione minima del file immagine in KB.',
'INPUT_THE_MAXIMUM_IMAGE_FILE_SIZE_IN_KBS_SET_TO_0__f73680'	=>	'Immettere la dimensione massima del file di immagine in KB. Impostare a 0 per nessun limite.',
'UPLOAD_OF_NEW_PHOTO_1831ae'	=>	'Carica nuova foto',
'NEW_PHOTO_REQUIRES_APPROVAL_d212d4'	=>	'Nuova foto richiede l\'approvazione',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Abilita o disabilita l\'utilizzo della paginazione',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Digita il limite di pagina. Il limite di pagina determina il numero di righe visualizzate per pagina. Se la paginazione è disabilitata questo può ancora essere utilizzato per limitare il numero di righe visualizzate.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Attivare o disattivare l\'utilizzo di ricerca sulle righe.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Selezionare l\'ordinamento fila.',
'DATE_ASC_a5871f'	=>	'Data ASC',
'DATE_DESC_bcfc6d'	=>	'Data DESC',
'FILENAME_ASC_44f721'	=>	'Filename ASC',
'FILENAME_DESC_13d728'	=>	'Filename DESC',
// 31 language strings from file cbgroupjivephoto/component.cbgroupjivephoto.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Gruppo non esiste.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_c06972'	=>	'Non si dispone di autorizzazioni sufficienti per caricare una foto in questo gruppo.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_6eccd3'	=>	'Non si dispone di autorizzazioni sufficienti per modificare questa foto.',
'SELECT_PUBLISH_STATE_OF_THIS_PHOTO_UNPUBLISHED_PHO_75ff3b'	=>	'Selezionare Pubblica stato di questa foto. Foto inedite non saranno visibili al pubblico.',
'OPTIONALLY_INPUT_A_PHOTO_TITLE_TO_DISPLAY_INSTEAD__323e09'	=>	'Ingresso Opzionalmente un titolo foto da visualizzare al posto del nome del file.',
'SELECT_THE_PHOTO_TO_UPLOAD_8e29df'	=>	'Selezionare la foto da caricare.',
'GROUP_PHOTO_LIMITS_EXT'	=>	'La tua foto deve essere di tipo [ext].',
'GROUP_PHOTO_LIMITS_MIN'	=>	'La tua foto deve superare [size].',
'GROUP_PHOTO_LIMITS_MAX'	=>	'La tua foto non deve superare [size].',
'OPTIONALLY_INPUT_A_PHOTO_DESCRIPTION_d4c183'	=>	'Ingresso opzionalmente una descrizione della foto.',
'INPUT_THE_PHOTO_OWNER_ID_PHOTO_OWNER_DETERMINES_TH_eb7b03'	=>	'Ingresso il proprietario id foto. Proprietario Foto determina il creatore della fotografia specificato come ID utente.',
'GROUP_PHOTO_FAILED_TO_SAVE'	=>	'Foto riuscito a salvare! Errore: [error]',
'GROUP_PHOTO_DATE_FORMAT'	=>	'M g, A',
'NEW_GROUP_PHOTO_9ba416'	=>	'Nuova foto di gruppo',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_429420'	=>	'[user] ha pubblicato la foto [photo] nel gruppo [group]!',
'NEW_GROUP_PHOTO_AWAITING_APPROVAL_221466'	=>	'Nuova foto di gruppo in attesa di approvazione',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_e90ac6'	=>	'[user] ha pubblicato la foto [photo] nel gruppo [group] ed è in attesa di approvazione!',
'PHOTO_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_492ec3'	=>	'Foto caricata con successo e in attesa di approvazione!',
'PHOTO_UPLOADED_SUCCESSFULLY_0b02f3'	=>	'Foto caricata con successo!',
'PHOTO_SAVED_SUCCESSFULLY_726f86'	=>	'Foto salvato con successo!',
'YOUR_PHOTO_IS_AWAITING_APPROVAL_d5f199'	=>	'La tua foto è in attesa di approvazione.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__dce5c6'	=>	'Non si dispone di autorizzazioni sufficienti per pubblicare o sospendere questa foto.',
'PHOTO_DOES_NOT_EXIST_cd6623'	=>	'Foto non esiste.',
'GROUP_PHOTO_STATE_FAILED_TO_SAVE'	=>	'Stato Foto non è riuscito a salvare. Errore: [error]',
'PHOTO_UPLOAD_REQUEST_ACCEPTED_bf6572'	=>	'Richiesta di caricamento delle foto accettata',
'YOUR_PHOTO_PHOTO_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_9728ad'	=>	'La tua foto [photo] richiesta di upload nel gruppo [group] è stato accettato!',
'PHOTO_STATE_SAVED_SUCCESSFULLY_2f8a03'	=>	'Foto stato salvato con successo!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8777b2'	=>	'Non si dispone di autorizzazioni sufficienti per eliminare questa foto.',
'GROUP_PHOTO_FAILED_TO_DELETE'	=>	'Foto non è riuscito a cancellare. Errore: [error]',
'PHOTO_DELETED_SUCCESSFULLY_a9f27f'	=>	'Foto cancellato con successo!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_PHOTO_5ca855'	=>	'Non si dispone di accesso a questa foto.',
// 7 language strings from file cbgroupjivephoto/library/Table/PhotoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Proprietario non specificata!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Gruppo non specificato!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Gruppo non esiste!',
'GROUP_PHOTO_UPLOAD_INVALID_EXT'	=>	'Estensione foto non valido [ext]. Carica solamente [exts]!',
'GROUP_PHOTO_UPLOAD_TOO_SMALL'	=>	'La foto è troppo piccolo, il minimo è [size]!',
'GROUP_PHOTO_UPLOAD_TOO_LARGE'	=>	'La dimensione della foto supera il massimo di [size]!',
'PHOTO_NOT_SPECIFIED_dd1bfc'	=>	'Foto non specificata!',
// 3 language strings from file cbgroupjivephoto/library/Trigger/AdminTrigger.php
'PHOTOS_5daaf2'	=>	'Foto',
'ADD_NEW_PHOTO_TO_GROUP_2df00d'	=>	'Aggiungi Nuova Foto al Gruppo',
'CONFIGURATION_254f64'	=>	'Configurazione',
// 6 language strings from file cbgroupjivephoto/library/Trigger/PhotoTrigger.php
'DISABLE_bcfacc'	=>	'Disabilita',
'ENABLE_2faec1'	=>	'Abilita',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Abilita, Con Approvazione',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_PHOTOS_GROUP_44b054'	=>	'Facoltativamente attivare o disattivare l\'utilizzo di foto. Gli amministratori di proprietario del gruppo e di gruppo sono esenti da questa configurazione e può sempre caricare le foto. Nota foto esistenti saranno comunque accessibili.',
'DONT_NOTIFY_3ea23f'	=>	'Non Notificati',
'SEARCH_PHOTOS_e11345'	=>	'Cerca Foto...',
// 2 language strings from file cbgroupjivephoto/templates/default/activity.php
'GROUP_PHOTO_ACTIVITY_TITLE'	=>	'caricata una foto nel [group]',
'ORIGINAL_0a52da'	=>	'Originale',
// 6 language strings from file cbgroupjivephoto/templates/default/photo_edit.php
'EDIT_PHOTO_68ffc9'	=>	'Modifica foto',
'NEW_PHOTO_50a153'	=>	'Nuova foto',
'PHOTO_c03d53'	=>	'Foto',
'DESCRIPTION_b5a7ad'	=>	'Descrizione',
'UPDATE_PHOTO_89bc50'	=>	'Aggiornamento Foto',
'UPLOAD_PHOTO_05e477'	=>	'Carica Foto',
// 7 language strings from file cbgroupjivephoto/templates/default/photos.php
'GROUP_PHOTOS_COUNT'	=>	'%%COUNT%% Foto|%%COUNT%% Foto',
'AWAITING_APPROVAL_af6558'	=>	'In attesa di approvazione',
'APPROVE_6f7351'	=>	'Approva',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_PHOTO_31f072'	=>	'Sei sicuro di voler annullare la pubblicazione di questa foto?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PHOTO_4d3e2d'	=>	'Sei sicuro di voler eliminare questa foto?',
'NO_GROUP_PHOTO_SEARCH_RESULTS_FOUND_64adc0'	=>	'Nessun risultato di ricerca trovato foto di gruppo.',
'THIS_GROUP_CURRENTLY_HAS_NO_PHOTOS_8939ef'	=>	'Questo gruppo non ha attualmente nessuna foto.',
);
