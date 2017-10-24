<?php
/**
* Community Builder (TM) cbreconfirmemail Slovak (Slovakia) language file Frontend
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
'CHANGED_820dbd'	=>	'Zmenené',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Zadajte podporovaný zástupný reťazec správy zobrazenej po zmene e-mailovej adresy. Nechajte pole prázdne, ak nemá byť zobrazená žiadna správa.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'Vaša e-mailová adresa bola zmenená a vyžaduje overenie. Skontrolujte vašu schránku s novou e-mailovou adresou, na ktorú bol zaslaný overovací e-mail.',
'NOTIFICATION_96d008'	=>	'Upozornenie',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Zadajte podporovaný zástupný reťazec mena odosielateľa, ktoré bude odoslané s overovacími e-mailami (napr. Môj skvelý CB web!). Ak pole necháte prázdne, použije sa ako predvolené meno používateľov. Ak je definované ako meno pre odpoveď, pridá sa k menám používateľov.',
'FROM_NAME_4a4a8f'	=>	'Meno odosielateľa',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Zadajte podporovaný zástupný reťazec adresy odosielateľa pre posielanie overovacích e-mailov (napr. general@domain.com). Ak pole necháte prázdne, použije sa ako predvolené meno používateľov. Ak je definované ako adresa na odpoveď, pridá s e-mail používateľov.',
'FROM_ADDRESS_a5ab7d'	=>	'E-mail odosielateľa',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Zadajte podporovaný zástupný reťazec predmetu e-mailu opätovného overenia.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Vaša e-mailová adresa bola zmenená',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Zadajte HTML a podporovane zástupné reťazce obsahu e-mailu. Zadaním [reconfirm] bude zobrazený overovací odkaz. Taktiež môžete použiť [old_email] pre zobrazenie starej e-mailovej adresy alebo [new_email] pre zobrazenie novej e-mailovej adresy.',
'BODY_ac101b'	=>	'Obsah',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'E-mailová adresa spojená s vaším používateľským účtom [username] bola zmenená na [new_email] a je potrebné jej overenie.<br><br>E-mailovú adresu môžete potvrdiť kliknutím na nasledovný odkaz:<br><a href="[reconfirm]">[reconfirm]</a><br><br>Ak nastane chyba, spojte sa s administrátorom alebo akciu zrušte <a href="[cancel]">kliknutím sem</a>.',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Voliteľne môžete zadať podporované zástupné reťazce cc adresy (napr. [email]); viac adries oddeľujte čiarkou (napr. email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC adresa',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Voliteľne môžete zadať podporované zástupné reťazce bcc adresy (napr. [email]); viacero adries oddeľujte čiarkou (napr. email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC adresa',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Voliteľne môžete zadať podporované zástupné reťazce adresy prílohy (napr. [cb_myfile]); viac adries oddeľujte čiarkou (napr. /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip).',
'ATTACHMENT_e9cb21'	=>	'Príloha',
'RECONFIRMED_e748a2'	=>	'Znova overené',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Zadajte podporovaný zástupný reťazec URL adresy presmerovania po úspešnom opätovnom overení. Nechajte pole prázdne, ak nemá dochádzať k presmerovaniu.',
'REDIRECT_4202ef'	=>	'Presmerovanie',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Zadajte podporovaný zástupný reťazec správy zobrazenej po úspešnom opätovnom overení.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'Nová e-mailová adresa bola úspešne overená!',
'CANCELLED_a149e8'	=>	'Zrušené',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Zadajte podporovaný zástupný reťazec URL adresy presmerovania po úspešnom zrušení zmeny e-mailu. Nechajte pole prázdne, ak nemá dochádzať k presmerovaniu.',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Zadajte podporovaný zástupný reťazec správy zobrazenej po úspešnom zrušení zmeny e-mailu.',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'Zmena e-mailovej adresy bola úspešne zrušená!',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Chýba overovací kód.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'Používateľ nie je spojený s týmto overovacím kódom.',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Overovací kód nie je platný.',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Zlyhalo zrušenie zmeny e-mailovej adresy! Chyba: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Zlyhalo overenie novej e-mailovej adresy! Chyba: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'E-mailová adresa už bola overená.',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'E-maily',
);
