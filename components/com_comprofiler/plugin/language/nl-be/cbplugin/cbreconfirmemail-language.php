<?php
/**
* Community Builder (TM) cbreconfirmemail Dutch (Belgium) language file Frontend
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
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Invoeren vervanging ondersteund bericht getoond na veranderen van e-mail adres. Laat leeg om geen bericht te laten zien',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'U e-mail adres is aangepast en moet opnieuw bevestigd worden. Controleer u e-mail om het e-mail adres te bevestigen.',
'NOTIFICATION_96d008'	=>	'Meldingen',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Invoeren vervanging ondersteunde Afzendernaam die wordt verstuurd bij alle herbevestiging e-mails ( vb. My Awesome CB site!). Indien leeg gelaten wordt standaard de gebruikers\' naam gebruikt. Indien gespecificeerd zal een Antwoord aan naam worden toegevoegd als de gebruiker\'s naam.',
'FROM_NAME_4a4a8f'	=>	'Van naam',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Invoeren vervanging ondersteunde afzender waarvan alle herevestigings e-mails worden verstuurd (BV general@domain.com). Indien leeg gelaten wordt het e-mail adres gebruikt van de gebruiker . Indien gespecificeerd zal een antwoord aan e-mail adres worden toegevoegd als de gebruiker\' e-mail.',
'FROM_ADDRESS_a5ab7d'	=>	'Aan adres',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Invoeren vervanging ondersteund herbevestig e-mail onderwerp.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Uw e-mail adres werd veranderd',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Invoeren vervang html voor ondersteund bevestig opnieuw e-mail bericht, geef [reconfirm] in om de bevestigings-link weer te geven. Optioneel [old_email] worden gebruikt om het oude e-mail adres weer te geven of [new_email] om het nieuwe e-mail adres weer te geven.',
'BODY_ac101b'	=>	'Bericht',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'Het e-mail adres gekoppeld aan jouw account [username] is veranderd naar [new_email] en vereist bevestiging. <br><br>U kan uw e-mail adres bevestigen door op de volgende link te klikken:<br><a href="[reconfirm]">[reconfirm]</a><br><br>Als dit per ongeluk was gedaan neem dan contact op met beheer of annuleer de wijziging door hier <a href="[cancel]">te klikken</a>.',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Geef een vervanging in voor ondersteund CC e-mail adres (BV  [email]); Meerdere e-mail adressen worden ondersteund door komma gescheiden lijst (BV email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC Adres',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Geef een vervanging in voor ondersteund BCC e-mail adres (BV  [email]); Meerdere e-mail adressen worden ondersteund door komma gescheiden lijst (BV email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Adres',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Voer een vervanging in voor ondersteund Bijlages adres (bv [cb_myfile] ); meerdere adressen worden ondersteund door komma gescheiden lijst (bv /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip).',
'ATTACHMENT_e9cb21'	=>	'Bijlage',
'RECONFIRMED_e748a2'	=>	'Herbevestigd',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Invoeren vervanging ondersteunde URL om te doorverwijzen na succesvolle herbevestiging. indien leeg wordt er geen doorverwijzing uitgevoerd.',
'REDIRECT_4202ef'	=>	'Doorverwijzing',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Invoer vervanging ondersteund bericht getoond na succesvolle herbevestiging.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'Nieuw e-mail adres succesvol bevestigd!',
'CANCELLED_a149e8'	=>	'Geannuleerd',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Invoeren vervanging ondersteunde URL om doorverwijzen na succesvolle annulering van e-mail aanpassing. Indien leeg wordt er geen doorverwijzing uitgevoerd.',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Invoeren vervanging ondersteunde bericht weergave na succesvolle annulering van e-mail aanpassing.',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'E-mail adres aanpassing succesvol geannuleerd!',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Bevestigings code ontbreekt.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'Gebruiker is niet gekoppeld aan bevestigings-code',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Bevestigings-code is niet geldig',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Het annuleren van e-mail adres aanpassing mislukt. Fout: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Bevestiging van het nieuwe e-mail adres is mislukt! Fout: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'E-mail adres werd reeds bevestigd',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'E-mails',
);
