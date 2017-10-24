<?php
/**
* Community Builder (TM) cbqueryfield Slovak (Slovakia) language file Frontend
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
// 51 language strings from file plug_cbqueryfield/cbqueryfield.xml
'ENABLE_OR_DISABLE_QUERY_VALIDATION_adcc72'	=>	'Zapnúť alebo vypnúť overovanie dotazov.',
'QUERY_VALIDATION_7c2420'	=>	'Overovanie dotazov',
'INPUT_SUBSTITUTION_SUPPORTED_QUERY_SUPPLY_VALUE_SU_80e274'	=>	'Zadajte podporovaný zástupný reťazec dotazu. Zadajte [value] pre vstupné pole vyplnené používateľom.',
'QUERY_66c1b4'	=>	'Dotaz',
'SELECT_MODE_OF_QUERY_THE_MODE_WILL_DETERMINE_WHAT__bfedc3'	=>	'Zvoľte režim dotazu. Režim určuje, aký dotaz je v databáze vykonávaný.',
'EXTERNAL_b206a1'	=>	'Externý',
'HOST_c2ca16'	=>	'Hosťovaný',
'DATABASE_e307db'	=>	'Databáza',
'CHARSET_f594c0'	=>	'Znaková sada',
'TABLE_PREFIX_3b39f2'	=>	'Predpona tabuľky',
'SELECT_IF_FIELD_SHOULD_VALIDATE_ON_EMPTY_QUERY_RES_01474a'	=>	'Zvoľte, či pole bude overované pri prázdnych výsledkoch dotazov (výsledkom je 0 riadkov) alebo pri úspešných dotazoch (výsledkom je aspoň 1 riadok). Ak dotaz vráti len 1 výsledok, potom bude použitá hodnota výsledku.',
'VALIDATE_ON_281360'	=>	'Overovať',
'EMPTY_RESULTS_4d107e'	=>	'Prázdne výsledky',
'SUCCESSFUL_RESULTS_85cea2'	=>	'Úspešné výsledky',
'INPUT_MESSAGE_TO_DISPLAY_ON_SUCCESSFUL_VALIDATION__ef7f20'	=>	'Zadajte správu zobrazovanú pri úspešnom overení. Podporované sú len odosielané reťazce [value] a [title].',
'SUCCESS_MESSAGE_4e9d73'	=>	'Správa o úspešnom procese',
'INPUT_MESSAGE_TO_DISPLAY_ON_FAILED_VALIDATION_SUPP_2fa2ea'	=>	'Zadajte správu zobrazovanú pri zlyhaní overenia. Podporované sú len odosielané reťazce [value] a [title].',
'ERROR_MESSAGE_c02034'	=>	'Chybová správa',
'NOT_A_VALID_INPUT_191edb'	=>	'Neplatný vstup.',
'ENABLE_OR_DISABLE_AJAX_QUERY_VALIDATION_6b4061'	=>	'Zapnúť alebo vypnúť ajaxové overovanie dotazov.',
'AJAX_VALIDATION_0388d2'	=>	'Ajaxové overovanie',
'SELECT_HOW_THE_QUERY_RESULTS_ARE_OUTPUT_THIS_IS_US_4ef407'	=>	'Zvoľte spôsob výstupu výsledkov dotazu. Je to užitočné najmä ak chcete zobraziť viacero výsledkov. Šablóna výstupu odošle surové výsledky dotazu do PHP šablóny, ktorá môže byť použitá na vlastné parsovanie a výstup výsledkov dotazu.',
'OUTPUT_29c2c0'	=>	'Výstup',
'SINGLE_ROW_b306d9'	=>	'Jeden riadok',
'MULTIPLE_ROWS_c5550e'	=>	'Viac riadkov',
'SELECT_IF_ONLY_A_SINGLE_SELECT_COLUMN_SHOULD_BE_DI_bffb09'	=>	'Vyberte len ak má byť zobrazený jeden stĺpec VÝBERU (často ako prvý) alebo ich má byť zobrazených viacero.',
'COLUMNS_168b82'	=>	'Stĺpce',
'SINGLE_COLUMN_5d53c1'	=>	'Jeden stĺpec',
'MULTIPLE_COLUMNS_90e95e'	=>	'Viac stĺpcov',
'SELECT_HOW_COLUMN_VALUES_SHOULD_BE_DISPLAYED_357b62'	=>	'Vyberte spôsob zobrazenia hodnôt stĺplca.',
'DELIMITER_abb968'	=>	'Oddeľovač',
'SELECT_DELIMITER_TO_SEPERATE_COLUMN_VALUES_4f406a'	=>	'Vyberte oddeľovač na rozdelenie hodnôt do stĺpcov.',
'COMMA_58be47'	=>	'Čiarka',
'DASH_366359'	=>	'Pomlčka',
'SPACE_d511f8'	=>	'Medzera',
'LINEBREAK_a6e832'	=>	'Nový riadok',
'BULLETIN_LIST_d04278'	=>	'Odrážkový zoznam',
'NUMBERED_LIST_bf087c'	=>	'Číslovaný zoznam',
'DIV_43d118'	=>	'Div',
'SPAN_9ce621'	=>	'Span',
'PARAGRAPH_feaf0a'	=>	'Odsek',
'INPUT_SUBSTITUTION_SUPPORTED_ROW_DISPLAY_QUERY_SEL_a64d2c'	=>	'Zadajte podporovaný zástupný reťazec zobrazenia riadka. Dotaz SELECT na stĺpce môže tiež byť použitý ako reťazec (napr. [column_username]).',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_FOR_RESULTS_DI_fa0f1f'	=>	'Zadajte podporovaný zástupný reťazec použitý v záhlaví na zobrazenie výsledkov. Môžete to využiť na pridanie doplnkových tagov, ako table alebo div do riadkov.',
'HEADER_bf50d5'	=>	'Záhlavie',
'ROW_a70367'	=>	'Riadok',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_FOR_RESULTS_DI_30d424'	=>	'Zadajte podporovaný zástupný reťazec použitý v pätičke na zobrazenie výsledkov. Môžete to využiť na uzavretie pridaných doplnkových tagov, ako table alebo div do riadkov.',
'FOOTER_ded40f'	=>	'Pätička',
'SELECT_TEMPLATE_TO_BE_USED_FOR_THIS_QUERY_DISPLAY__46bba0'	=>	'Zvoľte šablónu, ktorá bude použitá pre zobrazenie tohto dotazu. Ak je šablóna neúplná, tak chýbajúce polia budú použité z predvolenej šablóny. Súbory šablón môžu byť umiestnené na tejto adrese: components/com_comprofiler/plugin/user/plug_cbqueryfield/templates/.',
'OPTIONALLY_SELECT_FIELDS_TO_TRIGGER_THE_OPTIONS_FO_c2d27f'	=>	'Voliteľne môže vybrať polia, ktoré budú prepínať nastavenia poľa.',
'UPDATE_ON_c98238'	=>	'Aktualizácia',
'SELECT_FIELDS_b7951c'	=>	'- Vyberte polia -',
);
