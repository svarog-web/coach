<?php
/**
* Community Builder (TM) cbgroupjivefile Polish (Poland) language file Frontend
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
// 16 language strings from file cbgroupjivefile/cbgroupjivefile.xml
'INPUT_NUMBER_OF_FILES_EACH_INDIVIDUAL_USER_IS_LIMI_db8dbc'	=>	'Wprowadź maksymalną liczbę plików, którą pojedynczy użytkownik może zapisać w ramach jednej grupy. Pusta wartość oznacza brak ustalonego limitu. Ustawienie nie dotyczy moderatorów i właścicieli grup.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_FILES__cf15ea'	=>	'Włącz lub wyłącz użycie captcha przy obsłudze plików grupy. Opcja wymaga zainstalowanej i opublikowanej wtyczki CB AnitSpam. Ustawienie nie dotyczy moderatorów.',
'INPUT_THE_MINIMUM_FILE_SIZE_IN_KBS_f6c682'	=>	'Podaj minimalny rozmiar pliku w KB.',
'INPUT_THE_MAXIMUM_FILE_SIZE_IN_KBS_SET_TO_0_FOR_NO_58cb50'	=>	'Podaj maksymalny rozmiar pliku w KB. Ustawienie 0 oznacza brak limitu.',
'INPUT_THE_ALLOWED_FILE_EXTENSIONS_AS_A_COMMA_SEPAR_75447c'	=>	'Podaj listę dozwolonych rozszerzeń plików, odzieloną przecinkami.',
'FILE_TYPES_f12b42'	=>	'Typy Plików',
'UPLOAD_OF_NEW_FILE_6e6e69'	=>	'Prześlij nowy plik',
'NEW_FILE_REQUIRES_APPROVAL_cef783'	=>	'Nowy plik wymaga zatwierdzenia',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Włącz lub wyłącz podział na strony.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Podaj długość strony. Długość strony określa ilość wierszy wyświetlanych na jednej stronie. Jeśli podział na strony jest wyłączony, to ustawienie to może być użyte do ograniczenia ilości wyświetlanych wyników.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Włącz lub wyłącz przeszukiwanie wierszy.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Wybierz sortowanie wierszy.',
'DATE_ASC_a5871f'	=>	'Data Rosn',
'DATE_DESC_bcfc6d'	=>	'Data Malej',
'FILENAME_ASC_44f721'	=>	'Nazwa Pliku Rosn',
'FILENAME_DESC_13d728'	=>	'Nazwa Pliku Malej',
// 31 language strings from file cbgroupjivefile/component.cbgroupjivefile.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Grupa nie istnieje.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_050e48'	=>	'Nie posiadasz wystarczających uprawnień do dodania pliku do tej grupy.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_338361'	=>	'Nie posiadasz wystarczających uprawnień do edycji tego pliku.',
'SELECT_PUBLISH_STATE_OF_THIS_FILE_UNPUBLISHED_FILE_4be1f3'	=>	'Wybierz status publikacji pliku. Nieopublikowane pliki nie będą widoczne dla odwiedzających.',
'OPTIONALLY_INPUT_A_FILE_TITLE_TO_DISPLAY_INSTEAD_O_b6523c'	=>	'Opcjonalnie można podać tytuł pliku, który będzie wyświetlany zamiast nazwy pliku.',
'SELECT_THE_FILE_TO_UPLOAD_739b2a'	=>	'Wybierz plik do przesłania.',
'GROUP_FILE_LIMITS_EXT'	=>	'Plik musi być typu [ext].',
'GROUP_FILE_LIMITS_MIN'	=>	'Plik powinien być większy od [size].',
'GROUP_FILE_LIMITS_MAX'	=>	'Plik powinien być mniejszy od [size].',
'OPTIONALLY_INPUT_A_FILE_DESCRIPTION_b5bf92'	=>	'Opcjonalnie podaj opis pliku.',
'INPUT_THE_FILE_OWNER_ID_FILE_OWNER_DETERMINES_THE__8773c0'	=>	'Podaj ID właściciela pliku. Właściciel pliku oznacza jego twórcę, określonego przez ID Użytkownika.',
'GROUP_FILE_FAILED_TO_SAVE'	=>	'Nie udało się zapisać pliku! Błąd: [error]',
'GROUP_FILE_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_FILE_35b542'	=>	'Nowy plik grupy',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_db49b7'	=>	'[user] dodał(a) nowy plik [file] do grupy [group]!',
'NEW_GROUP_FILE_AWAITING_APPROVAL_498d56'	=>	'Nowy plik grupy oczekuje na zatwierdzenie',
'USER_HAS_UPLOADED_THE_FILE_FILE_IN_THE_GROUP_GROUP_37493d'	=>	'[user] dodał/a nowy plik [file] do grupy [group], który oczekuje na zatwierdzenie!',
'FILE_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_1e465f'	=>	'Plik został pomyślnie dodany i obecnie oczekuje na zatwierdzenie!',
'FILE_UPLOADED_SUCCESSFULLY_2169cb'	=>	'Plik został dodany pomyślnie!',
'FILE_SAVED_SUCCESSFULLY_c80b21'	=>	'Plik został zapisany z powodzeniem!',
'YOUR_FILE_IS_AWAITING_APPROVAL_dabe41'	=>	'Twój plik oczekuje na zatwierdzenie.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__fbef44'	=>	'Nie posiadasz wystarczających uprawnień zmienić publikację tego pliku.',
'FILE_DOES_NOT_EXIST_50db35'	=>	'Plik nie istnieje.',
'GROUP_FILE_STATE_FAILED_TO_SAVE'	=>	'Nie udało się zapisać statusu publikacji pliku. Błąd: [error]',
'FILE_UPLOAD_REQUEST_ACCEPTED_623624'	=>	'Prośba o dodanie pliku została zaakceptowana',
'YOUR_FILE_FILE_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_H_80bbf8'	=>	'Twoja prośba o dodanie pliku [file] do grupy [group] została zaakceptowana!',
'FILE_STATE_SAVED_SUCCESSFULLY_cc3caf'	=>	'Status publikacji pliku został zapisany z powodzeniem!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_35a6cc'	=>	'Nie posiadasz wystarczających uprawnień by usunąć ten plik.',
'GROUP_FILE_FAILED_TO_DELETE'	=>	'Nie udało się usunąć pliku! Błąd: [error]',
'FILE_DELETED_SUCCESSFULLY_5ea0ed'	=>	'Plik został usunięty!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_FILE_d7c056'	=>	'Nie posiadasz dostępu tego pliku.',
// 8 language strings from file cbgroupjivefile/library/Table/FileTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Nie określono Właściciela!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Nie wskazano Grupy!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Grupa nie istnieje!',
'GROUP_FILE_UPLOAD_INVALID_EXT'	=>	'Nieprawidłowe rozszerzenie pliku [ext]. Można przesyłać tylko [exts]!',
'GROUP_FILE_UPLOAD_TOO_SMALL'	=>	'Plik jest zbyt mały, minimalny rozmiar to [size]!',
'GROUP_FILE_UPLOAD_TOO_LARGE'	=>	'Plik przekracza maksymalny rozmiar [size]!',
'FILE_NOT_SPECIFIED_93ec32'	=>	'Nie wskazano pliku!',
'GROUP_FILE_UPLOAD_FAILED'	=>	'Nie udało się przesłać pliku [file]!',
// 3 language strings from file cbgroupjivefile/library/Trigger/AdminTrigger.php
'FILES_91f3a2'	=>	'Pliki',
'ADD_NEW_FILE_TO_GROUP_518801'	=>	'Dodaj Nowy Plik do Grupy',
'CONFIGURATION_254f64'	=>	'Konfiguracja',
// 6 language strings from file cbgroupjivefile/library/Trigger/FileTrigger.php
'DISABLE_bcfacc'	=>	'Wyłącz',
'ENABLE_2faec1'	=>	'Włącz',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Włącz, z wymogiem Zatwierdzenia',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_FILES_GROUP__3216b3'	=>	'Opcjonalnie włącz lub wyłącz dodawanie plików. Ustawienie nie dotyczy właścicieli i administratorów grupy, którzy zawsze mogą dodawać pliki. Uwaga: Pliki dodane przed zmianą ustawienia będą nadal widoczne.',
'DONT_NOTIFY_3ea23f'	=>	'Nie Powiadamiaj',
'SEARCH_FILES_ec9e5b'	=>	'Szukaj w Plikach...',
// 5 language strings from file cbgroupjivefile/templates/default/activity.php
'GROUP_FILE_ACTIVITY_TITLE'	=>	'dodał/a plik do [group]',
'TYPE_a1fa27'	=>	'Typ',
'SIZE_6f6cb7'	=>	'Rozmiar',
'CLICK_TO_DOWNLOAD_26f519'	=>	'Pobierz',
'UNKNOWN_88183b'	=>	'Nieznany',
// 6 language strings from file cbgroupjivefile/templates/default/file_edit.php
'EDIT_FILE_29e095'	=>	'Edytuj Plik',
'NEW_FILE_10716b'	=>	'Nowy Plik',
'FILE_0b2791'	=>	'Plik',
'DESCRIPTION_b5a7ad'	=>	'Opis',
'UPDATE_FILE_e9812b'	=>	'Aktualizuj Plik',
'UPLOAD_FILE_fbb7d7'	=>	'Prześlij Plik',
// 7 language strings from file cbgroupjivefile/templates/default/files.php
'GROUP_FILES_COUNT'	=>	'%%COUNT%% Plik|%%COUNT%% Pliki',
'AWAITING_APPROVAL_af6558'	=>	'Oczekuje na Zatwierdzenie',
'APPROVE_6f7351'	=>	'Zatwierdź',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_FILE_babc72'	=>	'Czy na pewno chcesz wyłączyć publikację tego Pliku?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_FILE_494de2'	=>	'Czy na pewno chcesz usunąć ten Plik?',
'NO_GROUP_FILE_SEARCH_RESULTS_FOUND_6609b5'	=>	'Brak wyników wyszukiwania plików w grupie.',
'THIS_GROUP_CURRENTLY_HAS_NO_FILES_f0b8c6'	=>	'Grupa nie posiada plików.',
);
