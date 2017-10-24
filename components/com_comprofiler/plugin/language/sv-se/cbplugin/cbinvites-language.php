<?php
/**
* Community Builder (TM) cbinvites Swedish (Sweden) language file Frontend
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
// 9 language strings from file plug_cbinvites/cbinvites.php
'JOIN_ME_de0c95'	=>	'!Jag vill bli medlem!',
'MAILER_FAILED_TO_SEND_46f543'	=>	'Kunde inte skicka mail.',
'TO_ADDRESS_MISSING_225871'	=>	'!Till adress saknas.',
'SUBJECT_MISSING_8e0db8'	=>	'Ämne saknas',
'BODY_MISSING_b6f835'	=>	'Innehåll saknas.',
'SEARCH_INVITES_9e5f33'	=>	'!Sök inbjudningar...',
'INVITE_CODE_NOT_VALID_7cd6f7'	=>	'Inbjudningskod ogiltig',
'INVITE_CODE_ALREADY_USED_cd715c'	=>	'Inbjudningskoden har redan använts.',
'INVITE_CODE_IS_VALID_96aad3'	=>	'!Inbjudningskoden är giltig.',
// 44 language strings from file plug_cbinvites/cbinvites.xml
'YOUR_REGISTRATION_INVITE_CODE_324b31'	=>	'Din inbjudningskod för registrering.',
'INVITE_CODE_0a2eb0'	=>	'Inbjudningskod',
'INVITES_213b86'	=>	'Inbjudningar',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_ON_TAB_INVITES_b76bc2'	=>	'Aktivera/Inaktivera paginering i inbjudningsfliken.',
'INPUT_PAGE_LIMIT_ON_TAB_INVITES_PAGE_LIMIT_DETERMI_b17959'	=>	'!Ange sidbegränsning på inbjudningsfliken. Sidbegränsningen avgör hur många inbjudningar som visas per sida.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_TAB_INVITES_dc5e0c'	=>	'!Aktivera eller inaktivera användning av sökfunktionen på inbjudningsfliken.',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_INVITES_I_e3fe43'	=>	'!Välj den mall som skall användas för alla inbjudningar. Om mallen inte är helt färdig så kommer saknade filer att användas från den förvalda mallen. Mallfiler hittas på följande plats: components/com_comprofiler/plugin/user/plug_cbinvites/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_7cf91e'	=>	'!Valfritt lägg till ett klassuffix till omgivande DIV som innesluter alla CB Inbjudningar.',
'SELECT_INVITE_CREATE_ACCESS_ACCESS_DETERMINES_WHO__c4a59a'	=>	'!Välj åtkomstnivå för att skapa inbjudningar. Åtkomstnivån avgör vem som kan skicka en inbjudning. Gruppen som valts liksom de ovan kommer att ha tillgång till detta (t.ex. Registrerad kommer också att vara tillgänglig för författare). Moderators are exempt from this configuration.',
'INPUT_NUMBER_OF_INVITES_EACH_INDIVIDUAL_USER_IS_LI_0d3553'	=>	'!Ange antalet inbjudningar varje enskild användare är begränsad till att ha aktiva vid en viss tidpunkt (ej accepterad inbjudan räknas inte med i denna gräns). Om tom tillåts obegränsat antal inbjudningar. Moderatorer är undantagna från denna konfiguration.',
'INPUT_NUMBER_OF_DAYS_AFTER_INVITE_SENT_TO_ALLOW_RE_12681b'	=>	'!Ange antal dagar efter att inbjudan har skickats för att tillåta omsändning (accepterad inbjudan tillåter inte omsändning). Om blank så är omsändning inaktiverad. Moderatorer är undantagna från denna konfiguration.',
'RESEND_DELAY_4f8afe'	=>	'Fördröjning innan omsändning',
'SELECT_USAGE_OF_MULTIPLE_EMAILS_IN_A_SINGLE_INVITE_e8af1e'	=>	'!Välj om användning av flera e-postmeddelanden i en enda inbjudning skall kunna användas genom en kommaseparerad lista (t.ex. email1@domain.com, email2@domain.com, email3@domain.com). Moderatorer är undantagna från denna konfiguration.',
'MULTIPLE_INVITES_b8f6fd'	=>	'!Flera inbjudningar',
'SELECT_USAGE_OF_DUPLICATE_INVITES_TO_THE_SAME_ADDR_96bfd8'	=>	'!Välj användning av dubbletta inbjudningar till samma adress. Moderatorer är undantagna från denna konfiguration.',
'DUPLICATE_INVITES_9b3f9e'	=>	'!Dubletta inbjudningar',
'SELECT_CONNECTION_METHOD_FROM_INVITER_TO_INVITEE_32692b'	=>	'!Välj kontaktmetod från inbjudaren till inbjudna.',
'CONNECTION_c2cc70'	=>	'!Kontakt',
'PENDING_CONNECTION_30ec76'	=>	'!Väntande kontakt',
'AUTO_CONNECTION_5f8c15'	=>	'!Auto kontakt',
'SELECT_INVITE_EMAIL_BODY_EDITOR_d8f5e1'	=>	'!Välj editortyp för inbjudningsmejlets meddelandetext.',
'PLAIN_TEXT_e44b14'	=>	'!Endast Text',
'HTML_TEXT_503c11'	=>	'!HTML Text',
'WYSIWYG_fcf0d4'	=>	'WYSIWYG',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_INVITES_REQU_65e72e'	=>	'Aktivera/Inaktivera användning av captcha i inbjudningar. Detta kräver att senaste CB AntiSpam är installerad och aktiverad. Moderatorer är undantagna från denna inställning.',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_f0ef2c'	=>	'!Ange ett annat från namn som ska skickas med alla inbjudningar (t.ex. Min Grymma CB webbplats!). Om den lämnas tom kommer som standard användarens namn att användas. Om det är specificerat kommer ett svara till namn att läggas till som användarnamn.',
'FROM_NAME_4a4a8f'	=>	'!Från Namn',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_ef3c3b'	=>	'!Ange ett annat från adress för att skicka alla inbjudningar från (t.ex. general@domain.com). Om detta lämnas tomt kommer som standard användarens E-postadress att användas. Om specificerat så kommer en svara till adress att läggas till som användarens E-postadress.',
'FROM_ADDRESS_a5ab7d'	=>	'Från adress',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'!Ange ett annat CC-adress (t.ex. [email]); flera adresser stöds med kommaseparerad lista (t.ex. email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'CC Adress',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'!Ange ett annat BCC-adress (t.ex. [email]); flera adresser stöds med kommaseparerad lista (t.ex. email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'BCC Adress',
'INPUT_SUBSTITUTION_SUPPORTED_PREFIX_OF_INVITE_EMAI_91cd80'	=>	'!Ange ett annat prefix för inbjudningsmejls ämne. Andra ersättningar som stöds är: [site], [sitename], [path], [itemId], [registrera], [profil], [code] och [to].',
'SUBJECT_PREFIX_175911'	=>	'Ämne prefix',
'SITENAME_6b68ee'	=>	'[sitename] - ',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_OF_INVITE_EMAI_058012'	=>	'!Ange ett annat sidhuvud för inbjudningsmejlets meddelandetext. Andra ersättningar som stöds är: [site], [sitename], [path], [itemid], [register], [profile], [code], och [to].',
'BODY_HEADER_67622c'	=>	'!Sidhuvud för text',
'YOU_HAVE_BEEN_INVITED_BY_USERNAME_TO_JOIN_SITENAME_e90186'	=>	'<p>[username] har bjudit in dig till [sitename]!</p><br>',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_OF_INVITE_EMAI_f5fa57'	=>	'!Ange en annan sidfot för inbjudningsmejlets meddelandetext. Andra ersättningar som stöds är: [site], [sitename], [path], [itemid], [register], [profile], [code], och [to].',
'BODY_FOOTER_6046e1'	=>	'!Sidfot för text',
'INVITE_CODE_CODESITENAME_SITEREGISTRATION_REGISTER_eec8c6'	=>	'<br><p>Inbjudningskod - [code]<br>[sitename] - [site]<br>Registrering - [register]<br>[username] - [profile]</p>',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__523216'	=>	'!Ange ett annat namn för bilagan (e.g. [cb_myfile]); flera namn kan användas med en kommaseparerad lista (e.g. /home/username/public_html/images/file1.zip,[path]/file2.zip, http://www.domain.com/file3.zip). Additional substitutions supported: [site], [sitename], [path], [itemid], [register], [profile], [code], and [to].',
'ATTACHMENT_e9cb21'	=>	'Bilaga',
// 22 language strings from file plug_cbinvites/component.cbinvites.php
'INPUT_INVITE_EMAIL_TO_ADDRESS_SEPARATE_MULTIPLE_EM_2028c1'	=>	'!Lägg till inbjudningsmejlet till adressen. Separera flera e-postadresser med kommatecken.',
'INPUT_INVITE_EMAIL_TO_ADDRESS_54551f'	=>	'!Lägg till inbjudningsmejlet till adressen.',
'INPUT_INVITE_EMAIL_SUBJECT_IF_LEFT_BLANK_A_SUBJECT_0974fc'	=>	'!Ange ett ämne för inbjudningsmejlet; om detta lämnas tomt kommer ett ämne att läggas till.',
'OPTIONALLY_INPUT_PRIVATE_MESSAGE_TO_INCLUDE_WITH_I_e1d750'	=>	'!Valfritt ange ett privat meddelande att inkludera med inbjudningsmejlet.',
'INPUT_OWNER_OF_INVITE_AS_SINGLE_INTEGER_USERID_THI_998c63'	=>	'!Ange ägaren av inbjudan som ett heltal för user_id. Detta är den användare som skickade inbjudan.',
'OPTIONALLY_INPUT_USER_OF_INVITE_AS_SINGLE_INTEGER__329e71'	=>	'!Valfritt ange användare av inbjudan som ett heltal för user_id. Detta är den användare som accepterade inbjudan.',
'COMMA_SEPERATED_LISTS_ARE_NOT_SUPPORTED_PLEASE_USE_7676e6'	=>	'!Kommaseparerad lista stöds inte! Använd en ensam Till adress.',
'INVITE_LIMIT_REACHED_1e1e31'	=>	'Inbjudningsbegränsning nådd!',
'TO_ADDRESS_NOT_SPECIFIED_e292c0'	=>	'Mottagaradress ej angiven.',
'INVITE_TO_ADDRESS_INVALID'	=>	'Mottagaradressen är inte giltig: [to_address]',
'YOU_CAN_NOT_INVITE_YOUR_SELF_487ade'	=>	'Du kan inte bjuda in dig själv.',
'TO_ADDRESS_IS_ALREADY_A_USER_4c2f27'	=>	'Mottagaren är redan användare.',
'TO_ADDRESS_IS_ALREADY_INVITED_f165f0'	=>	'Mottagaren har redan fått en inbjudan.',
'INVITE_FAILED_SAVE_ERROR'	=>	'Kunde inte spara inbjudan. Fel: [error]',
'INVITE_FAILED_SEND_ERROR'	=>	'!Inbjudan kunde inte skickas! Fel: [error]',
'INVITE_SENT_SUCCESSFULLY_380490'	=>	'!Inbjudan har skickats!',
'INVITE_SAVED_SUCCESSFULLY_14a90f'	=>	'Inbjudan har sparatas!',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_RESENT_528e3e'	=>	'Inbjudan har redan accepterats och kan inte skickas om.',
'INVITE_RESEND_NOT_APPLICABLE_AT_THIS_TIME_c65f19'	=>	'Omsändning av inbjudan är inte relevant just nu.',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_DELETED_cbc934'	=>	'Inbjudan har redan accepterats och kan inte raderas.',
'INVITE_FAILED_DELETE_ERROR'	=>	'!Inbjudningen kunde inte raderas! Fel: [error]',
'INVITE_DELETED_SUCCESSFULLY_9ea357'	=>	'!Inbjudningen har raderats!',
// 7 language strings from file plug_cbinvites/templates/default/invite_edit.php
'EDIT_INVITE_1faaed'	=>	'Ändra inbjudan',
'CREATE_INVITE_1e89ce'	=>	'Skriv inbjudan',
'TO_e12167'	=>	'!Till',
'BODY_ac101b'	=>	'!Meddelandetext',
'USER_8f9bfe'	=>	'!Medlem',
'UPDATE_INVITE_7c2f89'	=>	'Uppdatera inbjudan',
'SEND_INVITE_962943'	=>	'!Skicka inbjudan',
// 9 language strings from file plug_cbinvites/templates/default/tab.php
'NEW_INVITE_4093fa'	=>	'!Ny inbjudan',
'SENT_7f8c02'	=>	'´Skickat',
'PLEASE_RESEND_6ba908'	=>	'Skicka igen',
'ACCEPTED_382ab5'	=>	'!Accepterad',
'RESEND_1c0b8f'	=>	'!Skicka igen',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_INVITE_405d09'	=>	'Vill du verkligen radera denna inbjudan?',
'NO_INVITE_SEARCH_RESULTS_FOUND_63c4e3'	=>	'!Inga sökresultat för inbjudningar hittades.',
'YOU_HAVE_NO_INVITES_2f8b42'	=>	'!Du har inga inbjudningar.',
'THIS_USER_HAS_NO_INVITES_f2d878'	=>	'!Den här medlemmen har inga inbjudningar.',
);
