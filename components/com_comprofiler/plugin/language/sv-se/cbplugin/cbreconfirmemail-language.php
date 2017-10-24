<?php
/**
* Community Builder (TM) cbreconfirmemail Swedish (Sweden) language file Frontend
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
'CHANGED_820dbd'	=>	'Ändrad',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Ange ett meddelande som ska visas efter att mailadressen har ändrats, meddelandet stöder ersättningssträngar. Lämna detta tomt för att inte visa ett meddelande.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'Din E-postadress har ändrats och måste bekräftas igen. Kontrollera din nya mejladress för att se om du har fått ett bekräftelsemejl. ',
'NOTIFICATION_96d008'	=>	'Meddelande',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Ange ett namn som ska skickas med alla bekräftelsemejl, (t.ex. Min Grymma CB webbplats!). Om detta lämnas tomt så kommer användarens namn att användas. Om ett svara till namn har angetts så kommer detta att läggas till som användarens namn.',
'FROM_NAME_4a4a8f'	=>	'Från namn',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Ange ett annat från adress för att skicka alla bekräftelsemejlen från (t.ex. general@domain.com). Om detta lämnas tomt kommer som standard användarens E-postadress att användas. Om specificerat så kommer en svara till adress att läggas till som användarens E-postadress.',
'FROM_ADDRESS_a5ab7d'	=>	'Från adress',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Ange ett ämne för bekräftelsemejlen.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Din E-postadress har ändrats',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Ange ett meddelande i bekräftelse mejlet. html och substitutioner kan användas. Infoga [reconfirm] för att visa bekräftelselänken. [old_email] kan användas för visa den gamla mejladressen eller [new_email] för at visa den nya mejladressen.',
'BODY_ac101b'	=>	'Meddelande',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'Den e-postadress som är kopplad till ditt konto [username] har ändrats till [new_email] och behöver bekräftas.<br><br>Du kan bekräfta din nya e-postadress genom att klicka på länken:<br><a href="[reconfirm]">[reconfirm]</a><br><br>Om det inte är du som har gjort denna begäran så kontakta administratören eller avbryt genom att <a href="[cancel]">klicka här</a>.',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'!Ange en CC-adress som stöder variabeldata (t.ex. [email]); flera adresser stöds med kommaseparerad lista (t.ex. email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC Adress',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'!Ange en BCC-adress som stöder variabeldata (t.ex. [email]); flera adresser stöds med kommaseparerad lista (t.ex. email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Adress',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Ange en adress för bilagan (t.ex. [cb_myfile]); flera adresser kan skrivas in om dom separeras med kommatecken. Komma separerad lista (t ex /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip).',
'ATTACHMENT_e9cb21'	=>	'Bilaga',
'RECONFIRMED_e748a2'	=>	'Bekräftad',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Ange en URL som ska användas för omdirigering efter att bekräftelse har gjorts. Substitutioner kan användas. Om detta lämnas tomt så kommer ingen omdirigering att göras.',
'REDIRECT_4202ef'	=>	'Omdirigera',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Ange ett meddelande som ska visas efter gjord bekräftelse. Substitutioner kan användas.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'Nya e-postadressen är nu bekräftad.',
'CANCELLED_a149e8'	=>	'Avbrutet',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Ange en URL som man blir omdirigerad till efter att man har avbrutit ändring av e-postadressen. Substitutioner kan användas. Om detta lämnas tomt så kommer in ingen omdirigering att göras.',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Ange ett meddelande som ska visas efter att ändring av e-post adressen har avbrutits. Substitutioner kan användas.',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'Ändring av E-postadressen har avbrutits.',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Bekräftelsekoden saknas.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'Användaren är inte associerad med bekräftelsekoden.',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Bekräftelsekoden är inte giltig.',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Kunde inte avbryta ändring av e-postadressen. Fel: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Kunde inte bekräfta den nya e-postadressen! Fel: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'E-postadressen har redan bekräftats.',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'E-post',
);
