<?php
/**
* Community Builder (TM) cbinvites Danish (Denmark) language file Frontend
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
'JOIN_ME_de0c95'	=>	'Følg mig!',
'MAILER_FAILED_TO_SEND_46f543'	=>	'Mailer kunne ikke sende.',
'TO_ADDRESS_MISSING_225871'	=>	'Til-adresse mangler',
'SUBJECT_MISSING_8e0db8'	=>	'Emne mangler.',
'BODY_MISSING_b6f835'	=>	'Hovedtekst mangler.',
'SEARCH_INVITES_9e5f33'	=>	'Søg inviterede...',
'INVITE_CODE_NOT_VALID_7cd6f7'	=>	'Invitationskode ikke gyldig',
'INVITE_CODE_ALREADY_USED_cd715c'	=>	'Invitationskode er allerede brugt',
'INVITE_CODE_IS_VALID_96aad3'	=>	'Invitationskode er gyldig.',
// 44 language strings from file plug_cbinvites/cbinvites.xml
'YOUR_REGISTRATION_INVITE_CODE_324b31'	=>	'Din registrerings invitationskode.',
'INVITE_CODE_0a2eb0'	=>	'Invitationskode',
'INVITES_213b86'	=>	'Inviterede',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_ON_TAB_INVITES_b76bc2'	=>	'Aktiver eller deaktiver anvendelsen af sideinddeling på fanen invitationer.',
'INPUT_PAGE_LIMIT_ON_TAB_INVITES_PAGE_LIMIT_DETERMI_b17959'	=>	'Indtast sidegrænse på fane invitationer. Sidegrænse bestemmer hvor mange invitationer der vises per side.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_TAB_INVITES_dc5e0c'	=>	'Aktiver eller deaktiver anvendelse af søgning på fane invitationer.',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_INVITES_I_e3fe43'	=>	'Vælg skabelon der skal anvendes for alt i CB Invites. Hvis skabelonen er ufuldstændig, så vil de manglende filer blive anvendt fra standard skabelonen. Skabelonfiler kan findes på placeringen: components/com_comprofiler/plugin/user/plug_cbinvites/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_7cf91e'	=>	'Tilføj eventuelt et klasse suffiks til omkransende DIV der indeholder hele CB Invites',
'SELECT_INVITE_CREATE_ACCESS_ACCESS_DETERMINES_WHO__c4a59a'	=>	'Vælg invitations oprettelses adgang. Adgangen bestemmer hvem der kan sende invitationer. Gruppen der er valgt såvel som dem over vil have adgang (fx Registered vil også være tilgængelige for Author). Moderatorer er undtaget fra denne konfiguration.',
'INPUT_NUMBER_OF_INVITES_EACH_INDIVIDUAL_USER_IS_LI_0d3553'	=>	'Indtast antallet af invitationer som hver individuel bruger er begrænset til at have aktive på ethvert givent tidspunkt (accepterede invitationer tæller ikke med her). Hvis efterladt tom, så er det ubegrænset. Moderatorer er undtaget fra denne konfiguration.',
'INPUT_NUMBER_OF_DAYS_AFTER_INVITE_SENT_TO_ALLOW_RE_12681b'	=>	'Indtast antallet af dage efter at invitationen er afsendt hvor det er tilladt at genudsende (accepterede invitationer tillader ikke genudsendelse). Hvis efterladt tom, så deaktiveres genudsendelse af invitationer. Moderatorer er undtaget fra denne konfiguration.',
'RESEND_DELAY_4f8afe'	=>	'Gensend pause',
'SELECT_USAGE_OF_MULTIPLE_EMAILS_IN_A_SINGLE_INVITE_e8af1e'	=>	'Vælg anvendelse af multiple e-mails i en enkelt invitation ved at anvende kommasepareret liste (fx email1@domain.com, email2@domain.com, email3@domain.com). Moderatorer er undtaget fra denne konfiguration.',
'MULTIPLE_INVITES_b8f6fd'	=>	'Multiple invitationer',
'SELECT_USAGE_OF_DUPLICATE_INVITES_TO_THE_SAME_ADDR_96bfd8'	=>	'Vælg anvendelsen af dobbeltinvitationer til den samme adresse. Moderatorer er undtaget fra denne konfiguration.',
'DUPLICATE_INVITES_9b3f9e'	=>	'Dobbelte invitationer',
'SELECT_CONNECTION_METHOD_FROM_INVITER_TO_INVITEE_32692b'	=>	'Vælg forbindelsesmetode fra den inviterende til den inviterede.',
'CONNECTION_c2cc70'	=>	'Forbindelse',
'PENDING_CONNECTION_30ec76'	=>	'Afventende forbindelse',
'AUTO_CONNECTION_5f8c15'	=>	'Auto forbindelse',
'SELECT_INVITE_EMAIL_BODY_EDITOR_d8f5e1'	=>	'Vælg invitations e-mail hovedtekst editor.',
'PLAIN_TEXT_e44b14'	=>	'Ren tekst',
'HTML_TEXT_503c11'	=>	'HTML tekst',
'WYSIWYG_fcf0d4'	=>	'WYSIWYG',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_INVITES_REQU_65e72e'	=>	'Aktiver eller deaktiver anvendelsen af captcha på invitationer. Kræver at seneste CB AntiSpam plugin er installeret og publiceret. Moderatorer er undtaget fra denne konfiguration.',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_f0ef2c'	=>	'Indtast et substitutionsunderstøttet fra-navn der skal sendes med alle invitationer (fx Mit fantastiske CB websted!). Hvis efterladt tom, så vil standard blive brugerens navn. Hvis angivet, så vil et svar-til-navn blive tilføjet som den samme brugers navn.',
'FROM_NAME_4a4a8f'	=>	'Fra navn',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_ef3c3b'	=>	'Indtast en substitutionsunderstøttet fra-adresse som alle invitationer skal sendes fra (fx general@domain.com). Hvis efterladt tom, så vælges som standard brugerens e-mail adresse. Hvis angivet, så vil en svar-til-adresse blive tilføjet som brugerens e-mail.',
'FROM_ADDRESS_a5ab7d'	=>	'Fra adresse',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Indtast en substitutionsunderstøttet CC adresse (fx. [email]); multiple adresser er understøttet med en komma separeret liste (fx. email1@domæne.dk, email2@domæne.dk, email3@domæne.dk).',
'CC_ADDRESS_b6327b'	=>	'CC adresse',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Indtast en substitutionsunderstøttet BCC adresse (fx. [email]); multiple adresser er understøttet med en komma separeret liste (fx. email1@domæne.dk, email2@domæne.dk, email3@domæne.dk).',
'BCC_ADDRESS_33b728'	=>	'BCC adresse',
'INPUT_SUBSTITUTION_SUPPORTED_PREFIX_OF_INVITE_EMAI_91cd80'	=>	'Indtast substitutionsunderstøttet præfiks for emne på invitations e-mail. Yderligere substitutioner der er understøttede: [site], [sitename], [path], [itemid], [register], [profile], [code], og [to].',
'SUBJECT_PREFIX_175911'	=>	'Emne præfiks',
'SITENAME_6b68ee'	=>	'[sitename] - ',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_OF_INVITE_EMAI_058012'	=>	'Indtast substitutionsunderstøttet header til invitations e-mail hovedtekst. Yderligere understøttede substitutioner: [site], [sitename], [path], [itemid], [register], [profile], [code], og [to].',
'BODY_HEADER_67622c'	=>	'Hovedtekst header',
'YOU_HAVE_BEEN_INVITED_BY_USERNAME_TO_JOIN_SITENAME_e90186'	=>	'<p>Du er blevet inviteret af [username] til at deltage på [sitename]!</p><br>',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_OF_INVITE_EMAI_f5fa57'	=>	'Indtast substitutionsunderstøttet sidefod på invitations e-mail hovedtekst. Yderligere understøttede substitutioner: [site], [sitename], [path], [itemid], [register], [profile], [code], og [to].',
'BODY_FOOTER_6046e1'	=>	'Hovedtekst sidefod',
'INVITE_CODE_CODESITENAME_SITEREGISTRATION_REGISTER_eec8c6'	=>	'<br><p>Invitationskode - [code]<br>[sitename] - [site]<br>Registrering - [register]<br>[username] - [profile]</p>',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__523216'	=>	'Indtast en substitutionsunderstøttet vedhæftningsadresse (fx [cb_myfile]); multiple adresser er understøttet med separeret liste (fx /home/username/public_html/images/file1.zip,[path]/file2.zip, http://www.domain.com/file3.zip). Yderligere understøttede substitutioner: [site], [sitename], [path], [itemid], [register], [profile], [code], og [to].',
'ATTACHMENT_e9cb21'	=>	'Vedhæftning',
// 22 language strings from file plug_cbinvites/component.cbinvites.php
'INPUT_INVITE_EMAIL_TO_ADDRESS_SEPARATE_MULTIPLE_EM_2028c1'	=>	'Indtast invitations e-mail til-adresse. Separer multiple adresser med et komma.',
'INPUT_INVITE_EMAIL_TO_ADDRESS_54551f'	=>	'Indtast invitations e-mail til-adresse',
'INPUT_INVITE_EMAIL_SUBJECT_IF_LEFT_BLANK_A_SUBJECT_0974fc'	=>	'Indtast invitations e-mail emne; hvis efterladt tom, så vil et emne tilføjes.',
'OPTIONALLY_INPUT_PRIVATE_MESSAGE_TO_INCLUDE_WITH_I_e1d750'	=>	'Indtast eventuelt privat besked der skal inkluderes med invitations e-mailen.',
'INPUT_OWNER_OF_INVITE_AS_SINGLE_INTEGER_USERID_THI_998c63'	=>	'Indtast ejer af invitation som enkelt heltal user_id. Dette er brugeren som sendte invitationen.',
'OPTIONALLY_INPUT_USER_OF_INVITE_AS_SINGLE_INTEGER__329e71'	=>	'Indtast eventuelt brugeren der inviteres som enkelt heltal user_id. Dette er brugeren der accepterede invitationen.',
'COMMA_SEPERATED_LISTS_ARE_NOT_SUPPORTED_PLEASE_USE_7676e6'	=>	'Kommaseparerede lister er ikke understøttede! Anvend venligst en enkelt til-adresse',
'INVITE_LIMIT_REACHED_1e1e31'	=>	'Invitationsgrænse nået!',
'TO_ADDRESS_NOT_SPECIFIED_e292c0'	=>	'Til-adressen er ikke angivet',
'INVITE_TO_ADDRESS_INVALID'	=>	'Til-adressen er ikke gyldig: [to_address]',
'YOU_CAN_NOT_INVITE_YOUR_SELF_487ade'	=>	'Du kan ikke invitere dig selv.',
'TO_ADDRESS_IS_ALREADY_A_USER_4c2f27'	=>	'Til-adressen er allerede en bruger.',
'TO_ADDRESS_IS_ALREADY_INVITED_f165f0'	=>	'Til-adressen er allerede inviteret.',
'INVITE_FAILED_SAVE_ERROR'	=>	'Invitation kunne ikke gemmes. Fejl: [error]',
'INVITE_FAILED_SEND_ERROR'	=>	'Invitationen kunne ikke sendes! Fejl: [error]',
'INVITE_SENT_SUCCESSFULLY_380490'	=>	'Invitation sendt!',
'INVITE_SAVED_SUCCESSFULLY_14a90f'	=>	'Invitation gemt!',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_RESENT_528e3e'	=>	'Invitation er allerede accepteret og kan ikke genudsendes.',
'INVITE_RESEND_NOT_APPLICABLE_AT_THIS_TIME_c65f19'	=>	'Invitation genudsendelse er ikke muligt lige nu.',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_DELETED_cbc934'	=>	'Invitation er allerede accepteret og kan ikke slettes.',
'INVITE_FAILED_DELETE_ERROR'	=>	'Invitation kunne ikke slettes! Fejl: [error]',
'INVITE_DELETED_SUCCESSFULLY_9ea357'	=>	'Invitation slettet!',
// 7 language strings from file plug_cbinvites/templates/default/invite_edit.php
'EDIT_INVITE_1faaed'	=>	'Rediger invitation',
'CREATE_INVITE_1e89ce'	=>	'Opret invitation',
'TO_e12167'	=>	'Til',
'BODY_ac101b'	=>	'Brødtekst',
'USER_8f9bfe'	=>	'Bruger',
'UPDATE_INVITE_7c2f89'	=>	'Opdater invitation',
'SEND_INVITE_962943'	=>	'Send invitation',
// 9 language strings from file plug_cbinvites/templates/default/tab.php
'NEW_INVITE_4093fa'	=>	'Ny invitation',
'SENT_7f8c02'	=>	'Sendt',
'PLEASE_RESEND_6ba908'	=>	'Genudsend venligst',
'ACCEPTED_382ab5'	=>	'Accepteret',
'RESEND_1c0b8f'	=>	'Gensend',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_INVITE_405d09'	=>	'Er du sikker på at du ønsker at slette denne invitation?',
'NO_INVITE_SEARCH_RESULTS_FOUND_63c4e3'	=>	'Der er intet resultat af søgning i invitationer. ',
'YOU_HAVE_NO_INVITES_2f8b42'	=>	'Du har ingen invitationer',
'THIS_USER_HAS_NO_INVITES_f2d878'	=>	'Denne bruger har ingen invitationer.',
);
