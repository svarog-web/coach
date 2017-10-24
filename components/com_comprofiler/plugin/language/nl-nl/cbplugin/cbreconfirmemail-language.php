<?php
/**
* Community Builder (TM) cbreconfirmemail Dutch (Netherlands) language file Frontend
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
'CHANGED_820dbd'	=>	'Veranderd',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Invoer vervanging ondersteund bericht weergaven na veranderen van e-mail adres. Laat leeg om geen bericht te laten zien',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'U e-mail adres is aangepast en moet opnieuw bevestigd worden. Controleer u e-mail om het e-mail adres te bevestigen.',
'NOTIFICATION_96d008'	=>	'Notificaties',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Voer in een vervanging voor ondersteunde Afzendernaam van alle e-mails die worden verstuurd bij herbevestigingen (bijvoorbeeld My Awesome CB site!). Indien leeg gelaten wordt standaard gebruikers naam. Indien gespecificeerd een Antwoord aan  naam zal deze worden toegevoegd als de afzender.',
'FROM_NAME_4a4a8f'	=>	'Van naam',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Voer een vervanging in voor de afzender voor het versturen van alle Bevestigings-e-mails (BV general@domain.com). Indien leeg gelaten wordt het e-mail adres gebruikt van de gebruiker . Indien gespecificeerd het e-mail adres van de gebruiker gebruikt als Antwoord aan e-mail adres',
'FROM_ADDRESS_a5ab7d'	=>	'Aan adres',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Invoer vervanging ondersteund, bevestig opnieuw e-mail onderwerp.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Je e-mail is aangepast.',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Invoer  html en vervanging ondersteund bevestig opnieuw e-mail body, geef [reconfirm] om de bevestigings-link weer te geven . Optioneel [old_email] worden gebruikt om het oude e-mail adres weer te geven of [new_email] om het nieuwe e-mail adres weer te geven.',
'BODY_ac101b'	=>	'Body',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'Het e-mail adres gekoppeld aan jouw account [username] is gewijzigd naar [new_email] en vereist bevestiging. <br><br> Je kan je e-mail adres bevestigen doot op de volgende link te klikken <br><a href="[reconfirm]">[reconfirm]</a><br><br> Als dit niet corect is neem dan contact op met beheer of anuleer de wijziging door hier <a href="[cancel]">te klikken</a>',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Geef een vervanging in die CC e-mail adressen ondersteund  (BV  [email]); Meerdere e-mail worden ondersteund door ze scheiden met een komma  (BV email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC adres',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Geef een vervanging in die BCC e-mail adressen ondersteund  (BV  [email]); Meerdere e-mail worden ondersteund door ze scheiden met een komma  (BV email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Adres',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Voer een vervanging in voor het vangen van het bijlage adres (bv [cb_myfile] ); meerdere adressen worden ondersteund door ze te scheiden met een komma (bv /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip).',
'ATTACHMENT_e9cb21'	=>	'Bijlage',
'RECONFIRMED_e748a2'	=>	'Opnieuw bevestigd',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Invoer vervanging ondersteund URL om te leiden na succesvolle bevestiging in dien leeg wordt er geen omleiding uitgevoerd.',
'REDIRECT_4202ef'	=>	'Omleiding',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Invoer vervanging ondersteund bericht weergaven na succesvolle bevestiging.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'Nieuw e-mail adres is met succes bevestigd ',
'CANCELLED_a149e8'	=>	'Geannuleerd',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Invoer vervanging ondersteund URL om te leiden na succesvolle annulering van e-mail aanpassing.',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Invoer vervanging ondersteund bericht weergaven na succesvolle annulering van e-mail aanpassing.',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'E-mail adres aanpassing is met succes geannuleerd ',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Bevestigings-code ontbreekt.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'Gebruiker is niet gekoppeld aan bevestigings-code',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Bevestigings-code is niet geldig',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Het annuleren van e-mail adres aanpassing mislukt. Fout: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Mislukt om het e-mail adres te bevestigen! Fout: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'E-mail adres is reeds bevestigd',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'E-mails',
);
