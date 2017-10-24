<?php
/**
* Community Builder (TM) cbgroupjivephoto Polish (Poland) language file Frontend
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
// 20 language strings from file cbgroupjivephoto/cbgroupjivephoto.xml
'INPUT_NUMBER_OF_PHOTOS_EACH_INDIVIDUAL_USER_IS_LIM_11b810'	=>	'Wprowadź maksymalną liczbę zdjęć, którą pojedynczy użytkownik może zapisać w ramach jednej grupy. Pusta wartość oznacza brak ustalonego limitu. Ustawienie nie dotyczy moderatorów i właścicieli grup.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_PHOTOS_0ea9f4'	=>	'Włącz lub wyłącz użycie captcha przy obsłudze zdjęć grupy. Opcja wymaga zainstalowanej i opublikowanej wtyczki CB AnitSpam. Ustawienie nie dotyczy moderatorów.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'Wybierz, czy przesyłane zdjęcia mają być zawsze zmniejszane. Zmniejszanie zdjęć podnosi poziom bezpieczeństwa, ale zmniejszanie animacji jest możliwe tylko przy użyciu ImageMagic.',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'Podaj maksymalną wysokość w pikselach, do której będą zmniejszane zdjęcia.',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'Podaj maksymalną szerokość w pikselach, do której będą zmniejszane zdjęcia.',
'INPUT_THE_MAXIMUM_THUMBNAIL_HEIGHT_IN_PIXELS_THAT__9d2b57'	=>	'Podaj maksymalną wysokość miniaturki w pikselach, do której będą zminiejszane zdjęcia.',
'INPUT_THE_MAXIMUM_THUMBNAIL_WIDTH_IN_PIXELS_THAT_T_d159f4'	=>	'Podaj maksymalną szerokość miniaturki w pikselach, do której będą zmniejszane zdjęcia.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'Wybierz, czy przesyłane zdjęcia powinny zachowywać proporcje boków przy zmniejszeniu. Przy ustawieniu Nie, zdjęcia będą skalowane do maksymalnej wysokości i szerokości. Przy ustawieniu Tak, podczas zmniejszania będą zachowywane proporcje boków. Przy ustawieniu Tak z Przycinaniem zdjęcia będą zmniejszane do maksymalnej wysokości i szerokości i następnie docinane; to ustawienie jest przydatne przy kwadratowych zdjęciach.',
'INPUT_THE_MINIMUM_IMAGE_FILE_SIZE_IN_KBS_30eae6'	=>	'Podaj minimalny rozmiar pliku zdjęcia w KB.',
'INPUT_THE_MAXIMUM_IMAGE_FILE_SIZE_IN_KBS_SET_TO_0__f73680'	=>	'Podaj maksymalny rozmiar pliku zdjęcia w KB. Ustawienie 0 oznacza brak limitu.',
'UPLOAD_OF_NEW_PHOTO_1831ae'	=>	'Załaduj nowe zdjęcie',
'NEW_PHOTO_REQUIRES_APPROVAL_d212d4'	=>	'Nowe Zdjęcie wymaga zatwierdzenia',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Włącz lub wyłącz podział na strony.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Podaj długość strony. Długość strony określa ilość wierszy wyświetlanych na jednej stronie. Jeśli podział na strony jest wyłączony, to ustawienie to może być użyte do ograniczenia ilości wyświetlanych wyników.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Włącz lub wyłącz przeszukiwanie wierszy.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Wybierz sortowanie wierszy.',
'DATE_ASC_a5871f'	=>	'Data Rosn',
'DATE_DESC_bcfc6d'	=>	'Data Malej',
'FILENAME_ASC_44f721'	=>	'Nazwa Pliku Rosn',
'FILENAME_DESC_13d728'	=>	'Nazwa Pliku Malej',
// 31 language strings from file cbgroupjivephoto/component.cbgroupjivephoto.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Grupa nie istnieje.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_c06972'	=>	'Nie posiadasz wystarczających uprawnień do dodania zdjęcia do tej grupy.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_6eccd3'	=>	'Nie posiadasz wystarczających uprawnień do edycji tego zdjęcia.',
'SELECT_PUBLISH_STATE_OF_THIS_PHOTO_UNPUBLISHED_PHO_75ff3b'	=>	'Wybierz status publikacji zdjęcia. Zdjęcie nieopublikowane nie będzie widoczne dla odwiedzających.',
'OPTIONALLY_INPUT_A_PHOTO_TITLE_TO_DISPLAY_INSTEAD__323e09'	=>	'Opcjonalnie można podać tytuł zdjęcia, który będzie wyświetlany zamiast nazwy pliku.',
'SELECT_THE_PHOTO_TO_UPLOAD_8e29df'	=>	'Wybierz zdjęcie do dodania.',
'GROUP_PHOTO_LIMITS_EXT'	=>	'Plik zdjęcia musi być typu [ext].',
'GROUP_PHOTO_LIMITS_MIN'	=>	'Zdjęcie powinno być większe niż [size].',
'GROUP_PHOTO_LIMITS_MAX'	=>	'Zdjęcie nie powinno być większe niż [size].',
'OPTIONALLY_INPUT_A_PHOTO_DESCRIPTION_d4c183'	=>	'Opcjonalnie można podać opis zdjęcia.',
'INPUT_THE_PHOTO_OWNER_ID_PHOTO_OWNER_DETERMINES_TH_eb7b03'	=>	'Podaj ID właściciela zdjęcia. Właściciel zdjęcia oznacza jego autora, określonego przez ID Użytkownika.',
'GROUP_PHOTO_FAILED_TO_SAVE'	=>	'Nie udało się zapisać Zdjęcia! Błąd: [error]',
'GROUP_PHOTO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_PHOTO_9ba416'	=>	'Nowe zdjęcie grupy',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_429420'	=>	'[user] dodał(a) nowe zdjęcie [photo] do grupy [group]!',
'NEW_GROUP_PHOTO_AWAITING_APPROVAL_221466'	=>	'Nowe zdjęcie grupy oczekuje na zatwierdzenie',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_e90ac6'	=>	'[user] dodał nowe zdjęcie [photo] do grupy [group], które oczekuje na zatwierdzenie!',
'PHOTO_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_492ec3'	=>	'Zdjęcie zostało dodane pomyślnie i oczekuje na zatwierdzenie!',
'PHOTO_UPLOADED_SUCCESSFULLY_0b02f3'	=>	'Zdjęcie zostało dodane pomyślnie!',
'PHOTO_SAVED_SUCCESSFULLY_726f86'	=>	'Zdjęcie zostało zapisane pomyślnie!',
'YOUR_PHOTO_IS_AWAITING_APPROVAL_d5f199'	=>	'Twoje zdjęcie oczekuje na zatwierdzenie.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__dce5c6'	=>	'Nie posiadasz wystarczających uprawnień zmienić publikację tego zdjęcia.',
'PHOTO_DOES_NOT_EXIST_cd6623'	=>	'Zdjęcie nie istnieje.',
'GROUP_PHOTO_STATE_FAILED_TO_SAVE'	=>	'Nie udało się zapisać statusu publikacji zdjęcia. Błąd: [error]',
'PHOTO_UPLOAD_REQUEST_ACCEPTED_bf6572'	=>	'Prośba o dodanie zdjęcia została zaakceptowana',
'YOUR_PHOTO_PHOTO_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_9728ad'	=>	'Twoja prośba o dodanie zdjęcia [photo] do grupy [group] została zaakceptowana!',
'PHOTO_STATE_SAVED_SUCCESSFULLY_2f8a03'	=>	'Status publikacji zdjęcia został zapisany z powodzeniem!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8777b2'	=>	'Nie posiadasz wystarczających uprawnień by usunąć to zdjęcie.',
'GROUP_PHOTO_FAILED_TO_DELETE'	=>	'Nie udało się usunąć zdjęcia. Błąd: [error]',
'PHOTO_DELETED_SUCCESSFULLY_a9f27f'	=>	'Zdjęcie zostało usunięte z powodzeniem!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_PHOTO_5ca855'	=>	'Nie posiadasz dostępu tego zdjęcia.',
// 7 language strings from file cbgroupjivephoto/library/Table/PhotoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Nie wskazano Właściciela!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Nie wskazano Grupy!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Grupa nie istnieje!',
'GROUP_PHOTO_UPLOAD_INVALID_EXT'	=>	'Nieprawidłowe rozszerzenie zdjęcia [ext]. Dodawaj wyłącznie zdjęcia o rozszerzeniach [ests]!',
'GROUP_PHOTO_UPLOAD_TOO_SMALL'	=>	'Zdjęcie jest zbyt małe. Minimalny rozmiar to [size]!',
'GROUP_PHOTO_UPLOAD_TOO_LARGE'	=>	'Zdjęcie przekracza maksymalny rozmiar [size]!',
'PHOTO_NOT_SPECIFIED_dd1bfc'	=>	'Nie wskazano zdjęcia!',
// 3 language strings from file cbgroupjivephoto/library/Trigger/AdminTrigger.php
'PHOTOS_5daaf2'	=>	'Zdjęcia',
'ADD_NEW_PHOTO_TO_GROUP_2df00d'	=>	'Dodaj Nowe Zdjęcie do Grupy',
'CONFIGURATION_254f64'	=>	'Konfiguracja',
// 6 language strings from file cbgroupjivephoto/library/Trigger/PhotoTrigger.php
'DISABLE_bcfacc'	=>	'Wyłącz',
'ENABLE_2faec1'	=>	'Włącz',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Włącz, z wymogiem Zatwierdzenia',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_PHOTOS_GROUP_44b054'	=>	'Opcjonalnie włącz lub wyłącz dodawanie zdjęć. Ustawienie nie dotyczy właściciela grupy i administratorów grup, którzy zawsze mogą dodawać zdjęcia. Uwaga: Zdjęcia dodane przed zmianą ustawienia będą nadal widoczne.',
'DONT_NOTIFY_3ea23f'	=>	'Nie Powiadamiaj',
'SEARCH_PHOTOS_e11345'	=>	'Szukaj w Zdjęciach...',
// 2 language strings from file cbgroupjivephoto/templates/default/activity.php
'GROUP_PHOTO_ACTIVITY_TITLE'	=>	'dodał zdjęcie do [group]',
'ORIGINAL_0a52da'	=>	'Oryginał',
// 6 language strings from file cbgroupjivephoto/templates/default/photo_edit.php
'EDIT_PHOTO_68ffc9'	=>	'Edytuj Zdjęcie',
'NEW_PHOTO_50a153'	=>	'Nowe Zdjęcie',
'PHOTO_c03d53'	=>	'Zdjęcie',
'DESCRIPTION_b5a7ad'	=>	'Opis',
'UPDATE_PHOTO_89bc50'	=>	'Aktualizuj Zdjęcie',
'UPLOAD_PHOTO_05e477'	=>	'Prześlij Zdjęcie',
// 7 language strings from file cbgroupjivephoto/templates/default/photos.php
'GROUP_PHOTOS_COUNT'	=>	'%%COUNT%% Zdjęcie|%%COUNT%% Zdjęcia',
'AWAITING_APPROVAL_af6558'	=>	'Oczekuje na Zatwierdzenie',
'APPROVE_6f7351'	=>	'Zatwierdź',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_PHOTO_31f072'	=>	'Czy na pewno chcesz wyłączyć publikację tego Zdjęcia?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PHOTO_4d3e2d'	=>	'Czy na pewno chcesz usunąć to Zdjęcie?',
'NO_GROUP_PHOTO_SEARCH_RESULTS_FOUND_64adc0'	=>	'Bark wyników wyszukiwania zdjęć w grupie.',
'THIS_GROUP_CURRENTLY_HAS_NO_PHOTOS_8939ef'	=>	'Grupa nie zawiera zdjęć.',
);
