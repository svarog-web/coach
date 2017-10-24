<?php
/**
* Community Builder (TM) cbgroupjivefile Danish (Denmark) language file Frontend
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
// 16 language strings from file cbgroupjivefile/cbgroupjivefile.xml
'INPUT_NUMBER_OF_FILES_EACH_INDIVIDUAL_USER_IS_LIMI_db8dbc'	=>	'Indtast antallet af filer som hver individuelle bruger er begrænset til at oprette per gruppe. Hvis efterladt tom, så er det ubegrænset. Moderatorer og gruppeejere er undtaget fra denne konfiguration.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_FILES__cf15ea'	=>	'Aktiver eller deaktiver anvendelsen af captcha på gruppefiler. Kræver at den seneste version af CB AntiSpam er installeret og publiceret. Moderatorer er undtaget fra denne konfiguration.',
'INPUT_THE_MINIMUM_FILE_SIZE_IN_KBS_f6c682'	=>	'Indtast minimums filstørrelsen i KB.',
'INPUT_THE_MAXIMUM_FILE_SIZE_IN_KBS_SET_TO_0_FOR_NO_58cb50'	=>	'Indtast maksimum filstørrelsen i KB. Sæt til 0 for ubegrænset.',
'INPUT_THE_ALLOWED_FILE_EXTENSIONS_AS_A_COMMA_SEPAR_75447c'	=>	'Indtast de tilladte fil-endelser som en kommasepareret liste.',
'FILE_TYPES_f12b42'	=>	'Filtyper',
'UPLOAD_OF_NEW_FILE_6e6e69'	=>	'Upload af ny fil',
'NEW_FILE_REQUIRES_APPROVAL_cef783'	=>	'Ny fil kræver godkendelse',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Aktiver eller deaktiver anvendelse af sideinddeling.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Indtast side grænse. Sidegrænse bestemmer hvor mange rækker der vises per side. Hvis sideinddeling er deaktiveret, så kan dette stadig anvendes til at begrænse antallet af rækker der vises.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Aktiver eller deaktiver anvendes af søgning på rækker',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Vælg række rækkefølgen',
'DATE_ASC_a5871f'	=>	'Dato stigende',
'DATE_DESC_bcfc6d'	=>	'Dato faldende',
'FILENAME_ASC_44f721'	=>	'Filnavn stigende',
'FILENAME_DESC_13d728'	=>	'Filnavn faldende',
// 31 language strings from file cbgroupjivefile/component.cbgroupjivefile.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Gruppe eksisterer ikke.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_050e48'	=>	'Du har ikke tilstrækkelige rettigheder til at uploade en fil i denne gruppe.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_338361'	=>	'Du har ikke tilstrækkelige rettigheder til at redigere denne fil.',
'SELECT_PUBLISH_STATE_OF_THIS_FILE_UNPUBLISHED_FILE_4be1f3'	=>	'Vælg publiceringstilstand for denne fil. Afpublicerede filer vil ikke være synlig for offentligheden.',
'OPTIONALLY_INPUT_A_FILE_TITLE_TO_DISPLAY_INSTEAD_O_b6523c'	=>	'Indtast eventuelt en filtitel der skal vises i stedet for filnavnet.',
'SELECT_THE_FILE_TO_UPLOAD_739b2a'	=>	'Vælg filen der skal uploades',
'GROUP_FILE_LIMITS_EXT'	=>	'Din fil skal være af typen [ext].',
'GROUP_FILE_LIMITS_MIN'	=>	'Din skal skal være større end [size].',
'GROUP_FILE_LIMITS_MAX'	=>	'Din fil må ikke være større end [size].',
'OPTIONALLY_INPUT_A_FILE_DESCRIPTION_b5bf92'	=>	'Indtast eventuelt en filbeskrivelse.',
'INPUT_THE_FILE_OWNER_ID_FILE_OWNER_DETERMINES_THE__8773c0'	=>	'Indtast fil ejer id. Fil ejeren bestemmer opretteren for filen angivet som Bruger ID.',
'GROUP_FILE_FAILED_TO_SAVE'	=>	'Filen kunne ikke gemmes! Fejl: [error]',
'GROUP_FILE_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_FILE_35b542'	=>	'Ny gruppe fil',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_db49b7'	=>	'[user] har uploadet filen [file] i gruppen [group]!',
'NEW_GROUP_FILE_AWAITING_APPROVAL_498d56'	=>	'Ny gruppefil afventer godkendelse!',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_37493d'	=>	'[user] har uploadet filen [file] i gruppen [group] og afventer godkendelse!and is awaiting approval!',
'FILE_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_1e465f'	=>	'Fil uploadet og afventer godkendelse!',
'FILE_UPLOADED_SUCCESSFULLY_2169cb'	=>	'Fil uploadet!',
'FILE_SAVED_SUCCESSFULLY_c80b21'	=>	'Fil gemt!',
'YOUR_FILE_IS_AWAITING_APPROVAL_dabe41'	=>	'Din fil afventer godkendelse.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__fbef44'	=>	'Du har ikke tilstrækkelige rettigheder til at publicere eller afpublicere denne fil.',
'FILE_DOES_NOT_EXIST_50db35'	=>	'Filen eksisterer ikke.',
'GROUP_FILE_STATE_FAILED_TO_SAVE'	=>	'Filtilstand kunne ikke gemmes. Fejl: [error]',
'FILE_UPLOAD_REQUEST_ACCEPTED_623624'	=>	'Filupload forepørgsel accepteret',
'YOUR_FILE_FILE_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_H_80bbf8'	=>	'Din fil [file] uploadforespørgsel i gruppen [group] er blevet accepteret!',
'FILE_STATE_SAVED_SUCCESSFULLY_cc3caf'	=>	'Filtilstand gemt!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_35a6cc'	=>	'Du har ikke tilstrækkelige rettigheder til at slette denne fil.',
'GROUP_FILE_FAILED_TO_DELETE'	=>	'Fil kunne ikke slettes. Fejl: [error]',
'FILE_DELETED_SUCCESSFULLY_5ea0ed'	=>	'Fil slettet',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_FILE_d7c056'	=>	'Du har ikke adgang til denne fil.',
// 8 language strings from file cbgroupjivefile/library/Table/FileTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Ejer ikke angivet!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Gruppe ikke angivet!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Gruppe eksisterer ikke!',
'GROUP_FILE_UPLOAD_INVALID_EXT'	=>	'Ugyldig filendelse [ext]. Upload venligst kun [exts]!',
'GROUP_FILE_UPLOAD_TOO_SMALL'	=>	'Filen er for lille. Minimumsstørrelsen er [size]!',
'GROUP_FILE_UPLOAD_TOO_LARGE'	=>	'Filen er for stor. Maksimumsstørrelsen er [size]!',
'FILE_NOT_SPECIFIED_93ec32'	=>	'Fil ikke angivet!',
'GROUP_FILE_UPLOAD_FAILED'	=>	'Filen [file] kunne ikke uploades!',
// 3 language strings from file cbgroupjivefile/library/Trigger/AdminTrigger.php
'FILES_91f3a2'	=>	'Filer',
'ADD_NEW_FILE_TO_GROUP_518801'	=>	'Tilføj ny fil til gruppe',
'CONFIGURATION_254f64'	=>	'Konfiguration',
// 6 language strings from file cbgroupjivefile/library/Trigger/FileTrigger.php
'DISABLE_bcfacc'	=>	'Deaktiver',
'ENABLE_2faec1'	=>	'Aktiver',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Aktiver, med godkendelse',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_FILES_GROUP__3216b3'	=>	'Aktiver eller deaktiver eventuelt anvendelsen af filer. Gruppeejere og gruppeadministratorer er undtaget fra denne konfiguration og kan altid uploade filer. Bemærk at eksisterende filer stadig vil være tilgængelige.',
'DONT_NOTIFY_3ea23f'	=>	'Underret ikke',
'SEARCH_FILES_ec9e5b'	=>	'Søg filer...',
// 5 language strings from file cbgroupjivefile/templates/default/activity.php
'GROUP_FILE_ACTIVITY_TITLE'	=>	'uploadede en fil i [group]',
'TYPE_a1fa27'	=>	'Type',
'SIZE_6f6cb7'	=>	'Størrelse',
'CLICK_TO_DOWNLOAD_26f519'	=>	'Klik for at downloade',
'UNKNOWN_88183b'	=>	'Ukendt',
// 6 language strings from file cbgroupjivefile/templates/default/file_edit.php
'EDIT_FILE_29e095'	=>	'Rediger fil',
'NEW_FILE_10716b'	=>	'Ny fil',
'FILE_0b2791'	=>	'Fil',
'DESCRIPTION_b5a7ad'	=>	'Beskrivelse',
'UPDATE_FILE_e9812b'	=>	'Opdater fil',
'UPLOAD_FILE_fbb7d7'	=>	'Upload fil',
// 7 language strings from file cbgroupjivefile/templates/default/files.php
'GROUP_FILES_COUNT'	=>	'%%COUNT%% Fil|%%COUNT%% Filer',
'AWAITING_APPROVAL_af6558'	=>	'Afventer godkendelse',
'APPROVE_6f7351'	=>	'Godkend',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_FILE_babc72'	=>	'Er du sikker på at du vil afpublicere denne fil?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_FILE_494de2'	=>	'Er du sikker på at du vil slette denne fil?',
'NO_GROUP_FILE_SEARCH_RESULTS_FOUND_6609b5'	=>	'Ingen gruppefil søgeresultater fundet.',
'THIS_GROUP_CURRENTLY_HAS_NO_FILES_f0b8c6'	=>	'Denne gruppe har aktuelt ingen filer.',
);
