<?php
/**
* Community Builder (TM) cbgroupjivephoto Dutch (Netherlands) language file Frontend
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
'INPUT_NUMBER_OF_PHOTOS_EACH_INDIVIDUAL_USER_IS_LIM_11b810'	=>	'Vul het aantal foto\'s in dat elke deelnemer mag uploaden. Indien dit veld leeg blijft, is het aantal onbeperkt. Moderators en eigenaren van een groep zijn uitgesloten van deze configuratie.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_PHOTOS_0ea9f4'	=>	'in- of uitschakelen van de beveiligingscode voor foto\'s voor een groep. Dit vereist dat CB AntiSpam is geïnstalleerd en gepubliceerd. Moderators zijn uitgesloten van deze configuratie.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'Selecteer of geuploade afbeeldingen altijd moeten worden geresampled. Het resamplen van afbeeldingen zorgt voor aanvullende veiligheid. Animaties worden alleen bewaard wanneer er gebruik wordt gemaakt van ImageMagick',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'Vul de maximale hoogte in pixels in voor aanpassing van de grootte van de afbeelding.',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'Vul de maximale breedte in pixels in voor aanpassing van de grootte van de afbeelding.',
'INPUT_THE_MAXIMUM_THUMBNAIL_HEIGHT_IN_PIXELS_THAT__9d2b57'	=>	'Vul de maximale hoogte in pixels in voor aanpassing van de grootte van de miniatuurafbeelding.',
'INPUT_THE_MAXIMUM_THUMBNAIL_WIDTH_IN_PIXELS_THAT_T_d159f4'	=>	'Vul de maximale breedte in pixels in voor aanpassing van de grootte van de miniatuurafbeelding.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'Kies of de geüploade afbeeldingen hun originele beeldverhouding moeten behouden bij het vergroten of verkleinen. Indien "Nee" zal de afbeelding altijd worden vergroot of verkleind naar de maximum hoogte en breedte.  Indien "Ja"  zal de beeldverhouding zoveel mogelijk worden behouden binnen de grenzen van de maximum hoogte en breedte. Indien "Ja" bij het bijsnijden van een afbeelding zal de beeldhouding daarvan vergroten of verkleinen naar de gespecificeerde maximum hoogte en breedte en alle overloop wegsnijden. Dit is gemakkelijk voor het behoud van vierkante afbeeldingen.',
'INPUT_THE_MINIMUM_IMAGE_FILE_SIZE_IN_KBS_30eae6'	=>	'Vul, in KB, de minimale grootte van de afbeelding in.',
'INPUT_THE_MAXIMUM_IMAGE_FILE_SIZE_IN_KBS_SET_TO_0__f73680'	=>	'Vul, in KB, de maximale grootte van de afbeelding in. Stel in op 0 indien geen limiet.',
'UPLOAD_OF_NEW_PHOTO_1831ae'	=>	'Upload van nieuw foto',
'NEW_PHOTO_REQUIRES_APPROVAL_d212d4'	=>	'Nieuw foto vereist goedkeuring.',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'In- en uitschakelen gebruik van bladerfunctie.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Invullen bladlimiet. De bladlimiet bepaalt hoeveel rijen er per pagina worden getoond. Indien de bladlimiet is uitgeschakeld, kan dit nog steeds gebruikt worden om het aantal getoonde rijen te limiteren.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'In- of uitschakelen van zoeken in rijen.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Selecteeer de volgorde van de rij',
'DATE_ASC_a5871f'	=>	'Datum oplopend',
'DATE_DESC_bcfc6d'	=>	'Datum aflopend',
'FILENAME_ASC_44f721'	=>	'Bestandsnaam oplopend',
'FILENAME_DESC_13d728'	=>	'Bestandsnaam aflopend',
// 31 language strings from file cbgroupjivephoto/component.cbgroupjivephoto.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Groep bestaat niet.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_c06972'	=>	'Je hebt onvoldoende rechten om foto\'s up te loaden in deze groep.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_6eccd3'	=>	'Je hebt onvoldoende rechten om deze foto te wijzigen.',
'SELECT_PUBLISH_STATE_OF_THIS_PHOTO_UNPUBLISHED_PHO_75ff3b'	=>	'Selecteer de status van deze foto. Niet gepubliceerde foto\'s zijn niet zichtbaar voor het publiek.',
'OPTIONALLY_INPUT_A_PHOTO_TITLE_TO_DISPLAY_INSTEAD__323e09'	=>	'Vul in plaats van de bestandsnaam de titel voor een foto in.',
'SELECT_THE_PHOTO_TO_UPLOAD_8e29df'	=>	'Selecteer de foto die je wilt uploaden.',
'GROUP_PHOTO_LIMITS_EXT'	=>	'Jouw foto moet van het volgende bestandstype zijn: [ext]',
'GROUP_PHOTO_LIMITS_MIN'	=>	'Jouw foto moet groter zijn dan [size].',
'GROUP_PHOTO_LIMITS_MAX'	=>	'Jouw foto mag niet groter zijn dan [size]',
'OPTIONALLY_INPUT_A_PHOTO_DESCRIPTION_d4c183'	=>	'Vul optioneel een omschrijving van de foto in.',
'INPUT_THE_PHOTO_OWNER_ID_PHOTO_OWNER_DETERMINES_TH_eb7b03'	=>	'Vul de ID van de eigenaar in. Deze ID bepaalt de maker van de foto gespecificeerd als de ID van de deelnemer.',
'GROUP_PHOTO_FAILED_TO_SAVE'	=>	'Opslaan foto mislukt. Fout: [error]',
'GROUP_PHOTO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_PHOTO_9ba416'	=>	'Nieuwe foto',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_429420'	=>	'[user] heeft de foto [photo] geupload in de groep [group].',
'NEW_GROUP_PHOTO_AWAITING_APPROVAL_221466'	=>	'Nieuwe foto wacht op goedkeuring',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_e90ac6'	=>	'[user] heeft de foto [photo] geupload in de groep en wacht op goedkeuring.',
'PHOTO_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_492ec3'	=>	'Uploaden foto gelukt en wacht nu op goedkeuring.',
'PHOTO_UPLOADED_SUCCESSFULLY_0b02f3'	=>	'Uploaden foto gelukt.',
'PHOTO_SAVED_SUCCESSFULLY_726f86'	=>	'Opslaan foto gelukt.',
'YOUR_PHOTO_IS_AWAITING_APPROVAL_d5f199'	=>	'Jouw foto wacht op goedkeuring.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__dce5c6'	=>	'Je hebt onvoldoende rechten om deze foto te publiceren of te depubliceren.',
'PHOTO_DOES_NOT_EXIST_cd6623'	=>	'Foto bestaat niet.',
'GROUP_PHOTO_STATE_FAILED_TO_SAVE'	=>	'Opslaan status foto mislukt. Fout: [error]',
'PHOTO_UPLOAD_REQUEST_ACCEPTED_bf6572'	=>	'Upload foto goedgekeurd.',
'YOUR_PHOTO_PHOTO_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_9728ad'	=>	'Jouw upload van de foto [photo} in de groep [group] is goedgekeurd.',
'PHOTO_STATE_SAVED_SUCCESSFULLY_2f8a03'	=>	'Opslaan status foto gelukt.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8777b2'	=>	'Je hebt onvoldoende rechten om deze foto te verwijderen.',
'GROUP_PHOTO_FAILED_TO_DELETE'	=>	'Verwijderen foto mislukt. Fout: [error]',
'PHOTO_DELETED_SUCCESSFULLY_a9f27f'	=>	'Verwijderen foto gelukt.',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_PHOTO_5ca855'	=>	'Je hebt geen toegang tot deze foto.',
// 7 language strings from file cbgroupjivephoto/library/Table/PhotoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Eigenaar niet gespecificeerd.',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Groep niet bekend.',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Groep bestaat niet.',
'GROUP_PHOTO_UPLOAD_INVALID_EXT'	=>	'Ongeldige extensie foto. [ext]. Alleen [exts] is toegestaan.',
'GROUP_PHOTO_UPLOAD_TOO_SMALL'	=>	'De foto is te klein. MInimale grootte is [size].',
'GROUP_PHOTO_UPLOAD_TOO_LARGE'	=>	'De foto overschrijdt de maximale grootte van [size].',
'PHOTO_NOT_SPECIFIED_dd1bfc'	=>	'Foto niet gespecificeerd.',
// 3 language strings from file cbgroupjivephoto/library/Trigger/AdminTrigger.php
'PHOTOS_5daaf2'	=>	'Foto\'s',
'ADD_NEW_PHOTO_TO_GROUP_2df00d'	=>	'Voeg nieuw foto toe aan groep',
'CONFIGURATION_254f64'	=>	'Configuratie',
// 6 language strings from file cbgroupjivephoto/library/Trigger/PhotoTrigger.php
'DISABLE_bcfacc'	=>	'Uitschakelen',
'ENABLE_2faec1'	=>	'Inschakelen',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Inschakelen, met toestemming',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_PHOTOS_GROUP_44b054'	=>	'in- of uitschakelen van foto\'s. De eigenaar van de groep en administrators van de groep zijn uitgesloten van deze configuratie en kunnen te allen tijde foto\'s uploaden. NB. bestaande foto\'s blijven toegankelijk.',
'DONT_NOTIFY_3ea23f'	=>	'Niet informeren',
'SEARCH_PHOTOS_e11345'	=>	'Zoek foto\'s...',
// 2 language strings from file cbgroupjivephoto/templates/default/activity.php
'GROUP_PHOTO_ACTIVITY_TITLE'	=>	'Foto geupload in groep.',
'ORIGINAL_0a52da'	=>	'Origineel',
// 6 language strings from file cbgroupjivephoto/templates/default/photo_edit.php
'EDIT_PHOTO_68ffc9'	=>	'Foto aanpassen.',
'NEW_PHOTO_50a153'	=>	'NIeuwe foto',
'PHOTO_c03d53'	=>	'Foto',
'DESCRIPTION_b5a7ad'	=>	'Omschrijving',
'UPDATE_PHOTO_89bc50'	=>	'Foto aanpassen',
'UPLOAD_PHOTO_05e477'	=>	'Foto uploaden',
// 7 language strings from file cbgroupjivephoto/templates/default/photos.php
'GROUP_PHOTOS_COUNT'	=>	'%%COUNT%% Foto|%%COUNT%% Foto\'s',
'AWAITING_APPROVAL_af6558'	=>	'In afwachting van goedkeuring.',
'APPROVE_6f7351'	=>	'Goedkeuren',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_PHOTO_31f072'	=>	'Weet je zeker dat je deze foto wilt depubliceren?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PHOTO_4d3e2d'	=>	'Weet je zeker dat je deze foto wilt verwijderen?',
'NO_GROUP_PHOTO_SEARCH_RESULTS_FOUND_64adc0'	=>	'Geen zoekresultaten voor foto\'s gevonden.',
'THIS_GROUP_CURRENTLY_HAS_NO_PHOTOS_8939ef'	=>	'Deze groep heeft op dit moment geen foto\'s.',
);
