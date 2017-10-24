<?php
/**
* Community Builder (TM) cbgroupjivephoto Swedish (Sweden) language file Frontend
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
'INPUT_NUMBER_OF_PHOTOS_EACH_INDIVIDUAL_USER_IS_LIM_11b810'	=>	'Ange det antal bilder som varje enskild användare är begränsad till att skapa per grupp. Om detta lämnas tomt så tillåts ett obegränsat antal bilder. Moderatorer och gruppägare är undantagna från denna konfiguration.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_PHOTOS_0ea9f4'	=>	'Aktivera eller inaktivera användning av captcha för gruppfoton. Detta kräver att senaste CB AntiSpam måste vara installerat och publicerat. Moderatorer är undantagna från denna konfiguration.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'Välj om bilder som laddas upp alltid bör omsamplas. Omsampling lägger till ytterligare säkerhet, men animationer kommer endast hållas när ImageMagick används.',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'Ange den maximala höjden i antal pixlar som bilden kommer att ändras till.',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'Ange den maximala bredden i antal pixlar som bilden kommer att ändras till.',
'INPUT_THE_MAXIMUM_THUMBNAIL_HEIGHT_IN_PIXELS_THAT__9d2b57'	=>	'Ange miniatyrbildens maximala höjd i pixlar som bilden kommer att ändras till.',
'INPUT_THE_MAXIMUM_THUMBNAIL_WIDTH_IN_PIXELS_THAT_T_d159f4'	=>	'Ange miniatyrbildens maximal bredd i pixlar som bilden kommer att ändras till.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'Välj om uppladdade bilder skall behålla sitt bildformatet när du ändrar storlek. Om Nej så kommer bilden alltid att skalas till den angivna maximala bredden och höjden. Om satt till Ja så kommer bildförhållandet att bibehållas så mycket som möjligt inom den maximala bredden och höjden. Om satt till Ja med Beskär så kommer bilden alltid att ändras till den angivna maximala bredden och höjden i bildförhållande och allt överflödigt kommer att beskäras; Detta är användbart för att upprätthålla fyrkantiga bilder.',
'INPUT_THE_MINIMUM_IMAGE_FILE_SIZE_IN_KBS_30eae6'	=>	'Ange den minsta storleken i KB för bildfilen.',
'INPUT_THE_MAXIMUM_IMAGE_FILE_SIZE_IN_KBS_SET_TO_0__f73680'	=>	'Ange den största storleken i KB för bildfilen. Ange detta till 0 för ingen begränsning.',
'UPLOAD_OF_NEW_PHOTO_1831ae'	=>	'Ladda upp ett nytt foto',
'NEW_PHOTO_REQUIRES_APPROVAL_d212d4'	=>	'Nytt foto måste godkännas',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Aktivera/Inaktivera användning av sidvisning.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Ange sidbegränsning. Sidbegränsning avgör hur många rader som visas per sida. Om sidbrytning är inaktiverad så kan detta fortfarande användas för att begränsa antalet rader som visas.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Aktivera eller inaktivera användning av sökning i raderna.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Välj radsortering.',
'DATE_ASC_a5871f'	=>	'Datum STIG',
'DATE_DESC_bcfc6d'	=>	'Datum FALL',
'FILENAME_ASC_44f721'	=>	'Filnamn STIG',
'FILENAME_DESC_13d728'	=>	'Filnamn FALL',
// 31 language strings from file cbgroupjivephoto/component.cbgroupjivephoto.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Gruppen finns inte.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_c06972'	=>	'Du har inte tillräcklig behörighet för att ladda upp ett foto i denna grupp.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_6eccd3'	=>	'Du har inte tillräcklig behörighet för att redigera detta foto.',
'SELECT_PUBLISH_STATE_OF_THIS_PHOTO_UNPUBLISHED_PHO_75ff3b'	=>	'Välj publiceringsstatus för detta foto. Opublicerade foton kommer inte att vara synliga.',
'OPTIONALLY_INPUT_A_PHOTO_TITLE_TO_DISPLAY_INSTEAD__323e09'	=>	'Valfritt ange en rubrik för detta foto som visas i stället för filnamnet.',
'SELECT_THE_PHOTO_TO_UPLOAD_8e29df'	=>	'Välj foto som skall laddas upp.',
'GROUP_PHOTO_LIMITS_EXT'	=>	'Ditt foto måste vara av följande typ [ext].',
'GROUP_PHOTO_LIMITS_MIN'	=>	'Ditt foto måste överstiga [size].',
'GROUP_PHOTO_LIMITS_MAX'	=>	'Ditt foto får inte överstiga [size].',
'OPTIONALLY_INPUT_A_PHOTO_DESCRIPTION_d4c183'	=>	'Valfritt, ange en beskrivning för ditt foto.',
'INPUT_THE_PHOTO_OWNER_ID_PHOTO_OWNER_DETERMINES_TH_eb7b03'	=>	'Ange fotoägarens id. Fotoägare bestämmer vem som skapat fotot anges som användar ID.',
'GROUP_PHOTO_FAILED_TO_SAVE'	=>	'Fotot kunde inte sparas! Fel: [error]',
'GROUP_PHOTO_DATE_FORMAT'	=>	'Y - M - j',
'NEW_GROUP_PHOTO_9ba416'	=>	'Nytt grupp foto',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_429420'	=>	'[user] har laddat upp fotot [photo] i gruppen [group]!',
'NEW_GROUP_PHOTO_AWAITING_APPROVAL_221466'	=>	'Nytt grupp foto väntar på godkännande',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_e90ac6'	=>	'[user] har laddat upp fotot [photo] i gruppen [group] och väntar på godkännande!',
'PHOTO_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_492ec3'	=>	'Fotot har laddats upp och väntar nu på godkännande!',
'PHOTO_UPLOADED_SUCCESSFULLY_0b02f3'	=>	'Fotot har laddats upp!',
'PHOTO_SAVED_SUCCESSFULLY_726f86'	=>	'Fotot har sparats!',
'YOUR_PHOTO_IS_AWAITING_APPROVAL_d5f199'	=>	'Ditt foto väntar på godkännande.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__dce5c6'	=>	'Du har inte tillräcklig behörighet att publicera eller avpublicera detta foto.',
'PHOTO_DOES_NOT_EXIST_cd6623'	=>	'Fotot finns inte.',
'GROUP_PHOTO_STATE_FAILED_TO_SAVE'	=>	'Statusen på fotot kunde inte sparas. Fel: [error]',
'PHOTO_UPLOAD_REQUEST_ACCEPTED_bf6572'	=>	'Begäran om uppladdning av foton har accepterats',
'YOUR_PHOTO_PHOTO_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_9728ad'	=>	'Din begäran om att få ladda upp ditt foto [photo] i gruppen [group] har accepterats!',
'PHOTO_STATE_SAVED_SUCCESSFULLY_2f8a03'	=>	'Status på fotot har sparats!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8777b2'	=>	'Du har inte tillräcklig behörighet för att radera detta foto.',
'GROUP_PHOTO_FAILED_TO_DELETE'	=>	'Fotot kunde inte raderas. Fel: [error]',
'PHOTO_DELETED_SUCCESSFULLY_a9f27f'	=>	'Fotot har raderats!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_PHOTO_5ca855'	=>	'Du har inte åtkomst till detta foto.',
// 7 language strings from file cbgroupjivephoto/library/Table/PhotoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Ägare har inte angetts!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Grupp har inte angetts!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Gruppen finns inte!',
'GROUP_PHOTO_UPLOAD_INVALID_EXT'	=>	'Ogiltig filändelse för fotot [ext]. Du kan bara ladda upp följande filändelser [exts]!',
'GROUP_PHOTO_UPLOAD_TOO_SMALL'	=>	'Fotot är för litet, minsta storlek är [size]!',
'GROUP_PHOTO_UPLOAD_TOO_LARGE'	=>	'Fotot överskrider den högsta tillåtna storleken på [size]!',
'PHOTO_NOT_SPECIFIED_dd1bfc'	=>	'Foto har inte angetts!',
// 3 language strings from file cbgroupjivephoto/library/Trigger/AdminTrigger.php
'PHOTOS_5daaf2'	=>	'Foton',
'ADD_NEW_PHOTO_TO_GROUP_2df00d'	=>	'Lägg till ett nytt foto i gruppen',
'CONFIGURATION_254f64'	=>	'Inställningar',
// 6 language strings from file cbgroupjivephoto/library/Trigger/PhotoTrigger.php
'DISABLE_bcfacc'	=>	'Inaktivera',
'ENABLE_2faec1'	=>	'Aktivera',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Aktivera, med godkännande',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_PHOTOS_GROUP_44b054'	=>	'Valfritt aktivera eller inaktivera användning av bilder. Gruppägare och gruppadministratörer är undantagna från denna konfiguration och kan alltid ladda upp bilder. Observera att befintliga bilder kommer fortfarande att vara tillgängliga.',
'DONT_NOTIFY_3ea23f'	=>	'Meddela inte',
'SEARCH_PHOTOS_e11345'	=>	'Sök foton...',
// 2 language strings from file cbgroupjivephoto/templates/default/activity.php
'GROUP_PHOTO_ACTIVITY_TITLE'	=>	'laddade upp ett foto i gruppen [group]',
'ORIGINAL_0a52da'	=>	'Original',
// 6 language strings from file cbgroupjivephoto/templates/default/photo_edit.php
'EDIT_PHOTO_68ffc9'	=>	'Redigera foto',
'NEW_PHOTO_50a153'	=>	'Nytt foto',
'PHOTO_c03d53'	=>	'Foto',
'DESCRIPTION_b5a7ad'	=>	'Beskrivning',
'UPDATE_PHOTO_89bc50'	=>	'Uppdatera foto',
'UPLOAD_PHOTO_05e477'	=>	'Ladda upp foto',
// 7 language strings from file cbgroupjivephoto/templates/default/photos.php
'GROUP_PHOTOS_COUNT'	=>	'%%COUNT%% Foto|%%COUNT%% Foton',
'AWAITING_APPROVAL_af6558'	=>	'Väntar på godkännande',
'APPROVE_6f7351'	=>	'Godkänn',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_PHOTO_31f072'	=>	'Är du säker på att du vill avpublicera detta foto?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PHOTO_4d3e2d'	=>	'Är du säker på att du vill radera detta foto?',
'NO_GROUP_PHOTO_SEARCH_RESULTS_FOUND_64adc0'	=>	'Inga sökresultat hittades för grupp foto.',
'THIS_GROUP_CURRENTLY_HAS_NO_PHOTOS_8939ef'	=>	'Den här gruppen har inga fotografier.',
);
