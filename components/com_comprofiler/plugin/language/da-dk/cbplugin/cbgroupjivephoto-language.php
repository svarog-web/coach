<?php
/**
* Community Builder (TM) cbgroupjivephoto Danish (Denmark) language file Frontend
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
'INPUT_NUMBER_OF_PHOTOS_EACH_INDIVIDUAL_USER_IS_LIM_11b810'	=>	'Indtast antallet af billeder som hver individuelle bruger er begrænset til at oprette per gruppe. Hvis efterladt tom , så er det ubegrænset. Moderatorer og gruppeejere er undtaget fra denne konfiguration.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_PHOTOS_0ea9f4'	=>	'Aktiver eller deaktiver anvendelsen af captcha på gruppebilleder. Kræver at seneste version af CB AntiSpam er installeret og publiceret. Moderatorer er undtaget fra denne konfiguration.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'Vælg om uploadede billeder altid skal resamples. Resampling tilføjer yderligere sikkerhed, men animationer vil kun blive bibeholdt hvis ImageMagick anvendes.',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'Indtast maksimum højden i pixels som billedet bliver skaleret til.',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'Indtast maksimum bredden i pixels som billedet skaleres til.',
'INPUT_THE_MAXIMUM_THUMBNAIL_HEIGHT_IN_PIXELS_THAT__9d2b57'	=>	'Indtast maksimum miniature højde i pixels, som billedet bliver skaleret til.',
'INPUT_THE_MAXIMUM_THUMBNAIL_WIDTH_IN_PIXELS_THAT_T_d159f4'	=>	'Indtast maksimum miniature bredde i pixels som billedet skaleres til.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'Vælg om billeder der uploades skal bibeholde deres størrelsesforhold når de skaleres. Hvis sat til Nej, så vil billedet altid blive skaleret til den angivne maksimum bredde og højde. Hvis sa til Ja, så vil størrelsesforholdet blive bibeholdt så meget som muligt indenfor maksimum højde og bredde. Hvis sat til Ja, så vil beskæring af billedet altid skalere til den angivne maksimum højde og bredde indenfor størrelsesforholdet og beskære det overskydende; dette er brugbart for at få kvadratiske billeder.',
'INPUT_THE_MINIMUM_IMAGE_FILE_SIZE_IN_KBS_30eae6'	=>	'Indtast minimum billede filstørrelse i KB.',
'INPUT_THE_MAXIMUM_IMAGE_FILE_SIZE_IN_KBS_SET_TO_0__f73680'	=>	'Indtast maksimum billede filstørrelse i KB. Sæt til 0 for ubegrænset.',
'UPLOAD_OF_NEW_PHOTO_1831ae'	=>	'Upload af nyt billede',
'NEW_PHOTO_REQUIRES_APPROVAL_d212d4'	=>	'Nyt billede kræver godkendelse',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Aktiver eller deaktiver anvendelse af sideinddeling.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Indtast side grænse. Sidegrænse bestemmer hvor mange rækker der vises per side. Hvis sideinddeling er deaktiveret, så kan dette stadig anvendes til at begrænse antallet af rækker der vises.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Aktiver eller deaktiver anvendes af søgning på rækker',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Vælg række rækkefølgen',
'DATE_ASC_a5871f'	=>	'Dato stigende',
'DATE_DESC_bcfc6d'	=>	'Dato faldende',
'FILENAME_ASC_44f721'	=>	'Filnavn stigende',
'FILENAME_DESC_13d728'	=>	'Filnavn faldende',
// 31 language strings from file cbgroupjivephoto/component.cbgroupjivephoto.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Gruppe eksisterer ikke.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_c06972'	=>	'Du har ikke tilstrækkelige rettigheder til at uploade billeder i denne gruppe.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_6eccd3'	=>	'Du har ikke tilstrækkelige rettigheder til at redigere dette billede.',
'SELECT_PUBLISH_STATE_OF_THIS_PHOTO_UNPUBLISHED_PHO_75ff3b'	=>	'Vælg publiceringstilstand for dette billede. Afpublicerede billeder vil ikke være synlige for offentligheden.',
'OPTIONALLY_INPUT_A_PHOTO_TITLE_TO_DISPLAY_INSTEAD__323e09'	=>	'Indtast eventuelt en billedtitel der skal vises i stedet for filnavnet.',
'SELECT_THE_PHOTO_TO_UPLOAD_8e29df'	=>	'Vælg billedet der skal uploades.',
'GROUP_PHOTO_LIMITS_EXT'	=>	'Dit billede skal være af typen [ext].',
'GROUP_PHOTO_LIMITS_MIN'	=>	'Dit billede skal være over [size]',
'GROUP_PHOTO_LIMITS_MAX'	=>	'Dit billede må ikke være over [size]',
'OPTIONALLY_INPUT_A_PHOTO_DESCRIPTION_d4c183'	=>	'Indtast eventuelt en billedbeskrivelse.',
'INPUT_THE_PHOTO_OWNER_ID_PHOTO_OWNER_DETERMINES_TH_eb7b03'	=>	'Indtast billede ejer id. Billede ejer bestemmer opretteren af billedet angivet som Bruger ID.',
'GROUP_PHOTO_FAILED_TO_SAVE'	=>	'Billede kunne ikke gemmes!: [error]',
'GROUP_PHOTO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_PHOTO_9ba416'	=>	'Nyt gruppe billede',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_429420'	=>	'[user] har uploadet billedet[photo] i gruppen [group]!',
'NEW_GROUP_PHOTO_AWAITING_APPROVAL_221466'	=>	'Nyt gruppe billede afventer godkendelse',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_e90ac6'	=>	'[user] har uploadet billedet [photo] i gruppen [group] og afventer godkendelse!',
'PHOTO_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_492ec3'	=>	'Billede uploadet og afventer godkendelse!',
'PHOTO_UPLOADED_SUCCESSFULLY_0b02f3'	=>	'Billede uploadet!',
'PHOTO_SAVED_SUCCESSFULLY_726f86'	=>	'Billede gemt!',
'YOUR_PHOTO_IS_AWAITING_APPROVAL_d5f199'	=>	'Dit billede afventer godkendelse.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__dce5c6'	=>	'Du har ikke tilstrækkelige rettigheder til at publicere eller afpublicere dette billede.',
'PHOTO_DOES_NOT_EXIST_cd6623'	=>	'Billede eksisterer ikke.',
'GROUP_PHOTO_STATE_FAILED_TO_SAVE'	=>	'Billedtilstand kunne ikke gemmes. Fejl: [error]',
'PHOTO_UPLOAD_REQUEST_ACCEPTED_bf6572'	=>	'Billedupload forespørgsel accepteret',
'YOUR_PHOTO_PHOTO_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_9728ad'	=>	'Din billede [photo] upload forespørgsel i gruppen [group] er blevet accepteret!',
'PHOTO_STATE_SAVED_SUCCESSFULLY_2f8a03'	=>	'Billedtilstand gemt!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8777b2'	=>	'Du har ikke tilstrækkelige rettigheder til at slette dette billede.',
'GROUP_PHOTO_FAILED_TO_DELETE'	=>	'Billede kunne ikke slettes. Fejl: [error]',
'PHOTO_DELETED_SUCCESSFULLY_a9f27f'	=>	'Billede slettet!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_PHOTO_5ca855'	=>	'Du har ikke adgang til dette billede.',
// 7 language strings from file cbgroupjivephoto/library/Table/PhotoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Ejer ikke angivet!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Gruppe ikke angivet!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Gruppe eksisterer ikke!',
'GROUP_PHOTO_UPLOAD_INVALID_EXT'	=>	'Ugyldig billedendelse [ext]. Upload venligst kun [exts]!',
'GROUP_PHOTO_UPLOAD_TOO_SMALL'	=>	'Billedet er for lille, minimumstørrelsen er [size]!',
'GROUP_PHOTO_UPLOAD_TOO_LARGE'	=>	'Billedet er for stort. Maksimumstørrelsen er [size]!',
'PHOTO_NOT_SPECIFIED_dd1bfc'	=>	'Billede ikke angivet!',
// 3 language strings from file cbgroupjivephoto/library/Trigger/AdminTrigger.php
'PHOTOS_5daaf2'	=>	'Billeder',
'ADD_NEW_PHOTO_TO_GROUP_2df00d'	=>	'Tilføj nyt billede til gruppe',
'CONFIGURATION_254f64'	=>	'Konfiguration',
// 6 language strings from file cbgroupjivephoto/library/Trigger/PhotoTrigger.php
'DISABLE_bcfacc'	=>	'Deaktiver',
'ENABLE_2faec1'	=>	'Aktiver',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Aktiver, med godkendelse',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_PHOTOS_GROUP_44b054'	=>	'Aktiver eller deaktiver eventuelt anvendelse af billeder. Gruppeejere og gruppeadministratorer er undtaget fra denne konfiguration og kan altid uploade billeder. Bemærk at eksisterende billeder vil stadig være tilgængelige.',
'DONT_NOTIFY_3ea23f'	=>	'Underret ikke',
'SEARCH_PHOTOS_e11345'	=>	'Søg billeder...',
// 2 language strings from file cbgroupjivephoto/templates/default/activity.php
'GROUP_PHOTO_ACTIVITY_TITLE'	=>	'uploadede et billede i [group]',
'ORIGINAL_0a52da'	=>	'Original',
// 6 language strings from file cbgroupjivephoto/templates/default/photo_edit.php
'EDIT_PHOTO_68ffc9'	=>	'Rediger billede',
'NEW_PHOTO_50a153'	=>	'Nyt billede',
'PHOTO_c03d53'	=>	'Billede',
'DESCRIPTION_b5a7ad'	=>	'Beskrivelse',
'UPDATE_PHOTO_89bc50'	=>	'Opdater billede',
'UPLOAD_PHOTO_05e477'	=>	'Upload billede',
// 7 language strings from file cbgroupjivephoto/templates/default/photos.php
'GROUP_PHOTOS_COUNT'	=>	'%%COUNT%% Billede|%%COUNT%% Billeder',
'AWAITING_APPROVAL_af6558'	=>	'Afventer godkendelse',
'APPROVE_6f7351'	=>	'Godkend',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_PHOTO_31f072'	=>	'Er du sikker på at du vil afpublicere dette billede?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PHOTO_4d3e2d'	=>	'Er du sikker på at du vil slette dette billede?',
'NO_GROUP_PHOTO_SEARCH_RESULTS_FOUND_64adc0'	=>	'Ingen gruppebillede søgeresultater fundet.',
'THIS_GROUP_CURRENTLY_HAS_NO_PHOTOS_8939ef'	=>	'Denne gruppe har aktuelt ingen billeder.',
);
