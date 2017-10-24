<?php
/**
* Community Builder (TM) cbreconfirmemail Danish (Denmark) language file Frontend
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
'CHANGED_820dbd'	=>	'Ændret',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Indtast substitutions understøttet besked der vises efter ændring af emailadresse. Efterlad tom for ikke at vise nogen besked.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'Din email adresse er blevet ændret og kræver bekræftelse. Kontroller venligst din nye email adresse til din bekræftelsesemail.',
'NOTIFICATION_96d008'	=>	'Notifikation',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Indtast et substitutionsunderstøttet fra-navn der skal sendes med alle gen-bekræftelsesemails (fx. Mit vilde CB websted!). Hvis det efterlades tomt, så vil standard være brugerens navn. Hvis der er angivet, så vil et svar-til navn blive tilføjet som brugerens navn.',
'FROM_NAME_4a4a8f'	=>	'Fra-navn',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Indtast substitutionsunderstøttet fra-adresse som alle gen-bekræftelsesemails sendes fra (fx. generel@domæne.dk). Hvis efterladt tomt, så vil standard være brugerens email. Hvis der er angivet en svar-til adresse så vil denne blive tilføjet som brugerens email.',
'FROM_ADDRESS_a5ab7d'	=>	'Fra-adresse',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Indtast substitutionsunderstøttet emne for gen-bekræftelsesemail.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Din email adresse er blevet ændret',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Indtast html og substitutionsunderstøttet hovedtekst for gen-bekræftelsesemail. Angiv [reconfirm] for at vises bekræftelseslinket. Eventuelt kan [old_email] blive anvendt til at vises den gamle email adresse eller [new_email] for at vise den nye email adresse.',
'BODY_ac101b'	=>	'Hovedtekst',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'Email adressen der er tilknyttet til din konto [username] er blevet ændret til [new_email] og kræver bekræftelse.<br><br>Du kan bekræfte din email adrsse ved at klikke på det følgende link:<br><a href="[reconfirm]">[reconfirm]</a><br><br>Hvis dette er sket ved en fejl, så kontakt venligst administratoren eller annuller ved at <a href="[cancel]">klikke her</a>.',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Indtast en substitutionsunderstøttet CC adresse (fx. [email]); multiple adresser er understøttet med en komma separeret liste (fx. email1@domæne.dk, email2@domæne.dk, email3@domæne.dk).',
'CC_ADDRESS_b6327b'	=>	'CC adresse',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Indtast en substitutionsunderstøttet BCC adresse (fx. [email]); multiple adresser er understøttet med en komma separeret liste (fx. email1@domæne.dk, email2@domæne.dk, email3@domæne.dk).',
'BCC_ADDRESS_33b728'	=>	'BCC adresse',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Indtast en substitutionsunderstøttet Vedhæftningsadresse (fx. [cb_myfile]); multiple adresser er understøttet med en kommasepareret liste (fx. /home/username/public_html/images/file1.zip, http://www.domæne.dk/fil3.zip).',
'ATTACHMENT_e9cb21'	=>	'Vedhæftning',
'RECONFIRMED_e748a2'	=>	'Genbekræftet',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Indtast substitutionsunderstøttet URL der skal omdirigeres til efter en succesfuld genbekræftelse. Hvis efterladt tom, så vil der ikke omdirigeres.',
'REDIRECT_4202ef'	=>	'Omdiriger',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Indtast substitutionsunderstøttet besked der skal vises en genbekræftelse.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'Ny email adresse bekræftet!',
'CANCELLED_a149e8'	=>	'Annulleret',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Indtast substitutionsunderstøttet URL der skal omdirigeres til efter annullering af email ændring. Hvis efterladt tom, så vil der ikke omdirigeres.',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Indtast substitutionsunderstøttet besked der skal vises en annullering af en email ændring.',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'Email adressen ændring annulleret!',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Bekræftelseskode mangler.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'Bruger ikke associeret med bekræftelseskode',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Bekræftelseskode er ikke gyldig.',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Kunne ikke annullere ændring af email adresse! Fejl: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Kunne ikke bekræfte ny email adresse! Fejl: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'Email adresse er allerede bekræftet.',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'Emails',
);
