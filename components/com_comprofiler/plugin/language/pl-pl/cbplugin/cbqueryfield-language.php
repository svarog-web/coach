<?php
/**
* Community Builder (TM) cbqueryfield Polish (Poland) language file Frontend
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
'ENABLE_OR_DISABLE_QUERY_VALIDATION_adcc72'	=>	'Włącz lub wyłącz sprawdzanie poprawności zapytania.',
'QUERY_VALIDATION_7c2420'	=>	'Sprawdzanie Poprawności Zapytania',
'INPUT_SUBSTITUTION_SUPPORTED_QUERY_SUPPLY_VALUE_SU_80e274'	=>	'Wprowadź Zapytanie - zmienne i ciągi języka są wpsierane. Użyj zmiennej [value] jako wartości pola podanej przez użytkownika. ',
'QUERY_66c1b4'	=>	'Zapytanie',
'SELECT_MODE_OF_QUERY_THE_MODE_WILL_DETERMINE_WHAT__bfedc3'	=>	'Wybierz tryb zapytania. Tryb określa, na jakiej bazie zostanie wykonane zapytanie.',
'EXTERNAL_b206a1'	=>	'Zewnętrzna',
'HOST_c2ca16'	=>	'Host',
'DATABASE_e307db'	=>	'Baza Danych',
'CHARSET_f594c0'	=>	'Zestaw Znaków',
'TABLE_PREFIX_3b39f2'	=>	'Prefix Tabel',
'SELECT_IF_FIELD_SHOULD_VALIDATE_ON_EMPTY_QUERY_RES_01474a'	=>	'Wybierz, czy poprawność pola ma być sprawdzana dla pustego wyniku zapytania (wynik z 0 wierszy) lub niepustego wyniku zapytania (wynik z co najmniej 1 wierszem). W przypadku, gdy zapytanie zwróci tylko 1 wynik, zostanie użyta uzyskana wartość.',
'VALIDATE_ON_281360'	=>	'Sprawdzanie Poprawności Włączone',
'EMPTY_RESULTS_4d107e'	=>	'Pusty Wynik',
'SUCCESSFUL_RESULTS_85cea2'	=>	'Niepusty Wynik',
'INPUT_MESSAGE_TO_DISPLAY_ON_SUCCESSFUL_VALIDATION__ef7f20'	=>	'Podaj komunikat wyświetlany przy pomyślnym sprawdzeniu poprawności zapytania. Wspierane są wyłacznie zmienne [value] i [title].',
'SUCCESS_MESSAGE_4e9d73'	=>	'Komunikat Poprawnego Sprawdzenia',
'INPUT_MESSAGE_TO_DISPLAY_ON_FAILED_VALIDATION_SUPP_2fa2ea'	=>	'Podaj komunikat błedu przy nieudanym sprawdzeniu poprawności zapytania. Wspierane są wyłacznie zmienne [value] i [title].',
'ERROR_MESSAGE_c02034'	=>	'Komunikat Błędu',
'NOT_A_VALID_INPUT_191edb'	=>	'Nieprawidłowa wartość.',
'ENABLE_OR_DISABLE_AJAX_QUERY_VALIDATION_6b4061'	=>	'Włącz lub wyłącz sprawdzanie poprawności zapytania Ajax.',
'AJAX_VALIDATION_0388d2'	=>	'Sprawdzanie Poprawności Ajax',
'SELECT_HOW_THE_QUERY_RESULTS_ARE_OUTPUT_THIS_IS_US_4ef407'	=>	'Wybierz sposób wyświetlania rezultatów zapytania. Ta opcja jest przydatna, jeśli wyświetla się kilka wyników. Szablon przekazuje surowe wyniki zapytania do szablonu PHP, gdzie może być ustawiony wybrany sposób prezentacji wyników.',
'OUTPUT_29c2c0'	=>	'Widok',
'SINGLE_ROW_b306d9'	=>	'Pojedyńczy Wiersz',
'MULTIPLE_ROWS_c5550e'	=>	'Wiele Wierszy',
'SELECT_IF_ONLY_A_SINGLE_SELECT_COLUMN_SHOULD_BE_DI_bffb09'	=>	'Wybierz, czy ma być wyświetlana tylko pojedyńcza kolumna SELECT (zwykle pierwsza) lub wiele kolumn.',
'COLUMNS_168b82'	=>	'Kolumny',
'SINGLE_COLUMN_5d53c1'	=>	'Pjedyńcza Kolumna',
'MULTIPLE_COLUMNS_90e95e'	=>	'Wiele Kolumn',
'SELECT_HOW_COLUMN_VALUES_SHOULD_BE_DISPLAYED_357b62'	=>	'Wybierz jak mają być wyświeltane wartości w kolumnach.',
'DELIMITER_abb968'	=>	'Separator',
'SELECT_DELIMITER_TO_SEPERATE_COLUMN_VALUES_4f406a'	=>	'Wybierz separator rozdzielający kolumny',
'COMMA_58be47'	=>	'Przecinek',
'DASH_366359'	=>	'Myślnik',
'SPACE_d511f8'	=>	'Spacja',
'LINEBREAK_a6e832'	=>	'Enter',
'BULLETIN_LIST_d04278'	=>	'Lista Nienumerowana',
'NUMBERED_LIST_bf087c'	=>	'Lista Numerowana',
'DIV_43d118'	=>	'Div',
'SPAN_9ce621'	=>	'Span',
'PARAGRAPH_feaf0a'	=>	'Paragraf',
'INPUT_SUBSTITUTION_SUPPORTED_ROW_DISPLAY_QUERY_SEL_a64d2c'	=>	'Podaj treść wyświetlania wiersza - wspierane są zmienne i ciągi pliku języka. Kolumny SELECT zapytania mogą być używane jako zmienne (np. [column_username]).',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_FOR_RESULTS_DI_fa0f1f'	=>	'Podaj nagłówek wyświetlany powyżej wyników - wspierane są zmienne i ciągi pliku języka. To uzawienie może być używane do tworzenia znaczników zawierających listę wierszy np. tabeli lub div.',
'HEADER_bf50d5'	=>	'Nagłówek',
'ROW_a70367'	=>	'Wiersz',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_FOR_RESULTS_DI_30d424'	=>	'Podaj stopkę wyświetlaną poniżej wyników - wspierane są zmienne i ciągi pliku języka. To uzawienie może być używane do domknięcia znaczników zawierających listę wierszy np. tabeli lub div.',
'FOOTER_ded40f'	=>	'Stopka',
'SELECT_TEMPLATE_TO_BE_USED_FOR_THIS_QUERY_DISPLAY__46bba0'	=>	'Wybierz szablon, który będzie używany dla wyświetlania wyników zapytania. Jeżeli szablon nie jest kompletny, wtedy brakujące pliki zostaną zastąpione plikami z domyślnego szablonu. Pliki szablonu można znaleźć w: components/com_comprofiler/plugin/user/plug_cbqueryfield/templates/.',
'OPTIONALLY_SELECT_FIELDS_TO_TRIGGER_THE_OPTIONS_FO_c2d27f'	=>	'Opcjonalnie wybierz pola, które wymuszą aktualizację ustawień tego pola.',
'UPDATE_ON_c98238'	=>	'Aktualizacja Włączona',
'SELECT_FIELDS_b7951c'	=>	'- Wybierz Pola -',
);
