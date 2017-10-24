<?php
/**
* Community Builder (TM) cbgroupjivevideo Polish (Poland) language file Frontend
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
// 10 language strings from file cbgroupjivevideo/cbgroupjivevideo.xml
'INPUT_NUMBER_OF_VIDEOS_EACH_INDIVIDUAL_USER_IS_LIM_c86242'	=>	'Wprowadź maksymalną liczbę filmów, którą pojedynczy użytkownik może zapisać w ramach jednej grupy. Pusta wartość oznacza brak ustalonego limitu. Ustawienie nie dotyczy moderatorów i właścicieli grup.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_VIDEOS_6066cb'	=>	'Włącz lub wyłącz użycie captcha przy obsłudze filmów grupy. Opcja wymaga zainstalowanej i opublikowanej wtyczki CB AnitSpam. Ustawienie nie dotyczy moderatorów.',
'PUBLISH_OF_NEW_VIDEO_026206'	=>	'Opublikuj nowy film',
'NEW_VIDEO_REQUIRES_APPROVAL_a484cb'	=>	'Nowy Film wymaga zatwierdzenia',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Włącz lub wyłącz podział na strony.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Podaj długość strony. Długość strony określa ilość wierszy wyświetlanych na jednej stronie. Jeśli podział na strony jest wyłączony, to ustawienie to może być użyte do ograniczenia ilości wyświetlanych wyników.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Włącz lub wyłącz przeszukiwanie wierszy.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Wybierz sortowanie wierszy.',
'DATE_ASC_a5871f'	=>	'Data Rosnąco',
'DATE_DESC_bcfc6d'	=>	'Data Malejąco',
// 28 language strings from file cbgroupjivevideo/component.cbgroupjivevideo.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Grupa nie istnieje.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__0b0480'	=>	'Nie posiadasz wystarczających uprawnień do dodania filmu do tej grupy.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_08c995'	=>	'Nie posiadasz wystarczających uprawnień do edycji tego filmu.',
'SELECT_PUBLISH_STATE_OF_THIS_VIDEO_UNPUBLISHED_VID_3eabcd'	=>	'Wybierz status publikacji filmu. Nieopublikowany film nie będzie widoczny dla odwiedzających.',
'OPTIONALLY_INPUT_A_VIDEO_TITLE_TO_DISPLAY_INSTEAD__f25147'	=>	'Opcjonalnie można podać tytuł filmu, który będzie wyświetlany zamiast jego adresu url.',
'INPUT_THE_URL_TO_THE_VIDEO_TO_PUBLISH_4a8a28'	=>	'Podaj adres URL filmu do zamieszczenia.',
'GROUP_VIDEO_LIMITS_EXT'	=>	'Twój adres URL musi być typu [ext].',
'OPTIONALLY_INPUT_A_VIDEO_CAPTION_be178a'	=>	'Opcjonalnie podaj podpis pod filmem.',
'INPUT_THE_VIDEO_OWNER_ID_VIDEO_OWNER_DETERMINES_TH_008f4c'	=>	'Podaj ID właściciela filmu. Właściciel filmu oznacza jego autora, określonego przez ID Użytkownika.',
'GROUP_VIDEO_FAILED_TO_SAVE'	=>	'Nie udało się zapisać Filmu! Błąd: [error]',
'GROUP_VIDEO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_VIDEO_28e07a'	=>	'Nowy film grupy',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_bf41d0'	=>	'[user] dodał nowy film [video] do grupy [group]!',
'NEW_GROUP_VIDEO_AWAITING_APPROVAL_9740f3'	=>	'Nowy film grupy oczekuje na zatwierdzenie',
'USER_HAS_PUBLISHED_THE_VIDEO_VIDEO_IN_THE_GROUP_GR_a94089'	=>	'[user] dodał(a) nowy film [video] do grupy [group], który oczekuje na zatwierdzenie!',
'VIDEO_PUBLISHED_SUCCESSFULLY_AND_AWAITING_APPROVAL_d7c1b5'	=>	'Film został opublikowany i czeka na zatwierdzenie!',
'VIDEO_PUBLISHED_SUCCESSFULLY_9c46a0'	=>	'Film został opublikowany z powodzeniem!',
'VIDEO_SAVED_SUCCESSFULLY_d725ea'	=>	'Film został zapisany z powodzeniem!',
'YOUR_VIDEO_IS_AWAITING_APPROVAL_3c3526'	=>	'Twój film oczekuje na zatwierdzenie.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__08c91a'	=>	'Nie posiadasz wystarczających uprawnień zmienić publikację tego filmu.',
'VIDEO_DOES_NOT_EXIST_695b98'	=>	'Film nie istnieje.',
'GROUP_VIDEO_STATE_FAILED_TO_SAVE'	=>	'Nie udało się zapisać statusu publikacji filmu. Błąd: [error]',
'VIDEO_PUBLISH_REQUEST_ACCEPTED_f4e6ff'	=>	'Prośba o dodanie filmu została zaakceptowana',
'YOUR_VIDEO_VIDEO_PUBLISH_REQUEST_IN_THE_GROUP_GROU_c3891f'	=>	'Twoja prośba o dodanie filmu [video] do grupy [group] została zaakceptowana!',
'VIDEO_STATE_SAVED_SUCCESSFULLY_df7038'	=>	'Status publikacji filmu został zapisany z powodzeniem!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8e03ba'	=>	'Nie posiadasz wystarczających uprawnień by usunąć ten film.',
'GROUP_VIDEO_FAILED_TO_DELETE'	=>	'Nie udało się usunąć filmu. Błąd: [error]',
'VIDEO_DELETED_SUCCESSFULLY_08a3fa'	=>	'Film został usunięty z powodzeniem!',
// 6 language strings from file cbgroupjivevideo/library/Table/VideoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Nie określono Właściciela!',
'URL_NOT_SPECIFIED_2ccd94'	=>	'Nie podano adresu URL!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Nie wskazano Grupy!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Grupa nie istnieje!',
'GROUP_VIDEO_INVALID_URL'	=>	'Nieprawidłowy adres URL. Upewnij się, czy adres istnieje!',
'GROUP_VIDEO_INVALID_EXT'	=>	'Nieprawidłowe rozszerzenie [ext] adresu URL. Można podłączać wyłącznie [exts]!',
// 3 language strings from file cbgroupjivevideo/library/Trigger/AdminTrigger.php
'VIDEOS_554cfa'	=>	'Filmy',
'ADD_NEW_VIDEO_TO_GROUP_4d5188'	=>	'Dodaj Nowy Film do Grupy',
'CONFIGURATION_254f64'	=>	'Konfiguracja',
// 6 language strings from file cbgroupjivevideo/library/Trigger/VideoTrigger.php
'DISABLE_bcfacc'	=>	'Wyłącz',
'ENABLE_2faec1'	=>	'Włącz',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Włącz, z wymogiem Zatwierdzenia',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_VIDEOS_GROUP_0ca36a'	=>	'Opcjonalnie włącz lub wyłącz dodawanie filmów. Ustawienie nie dotyczy właściciela grupy i administratorów grup, którzy zawsze mogą dodawać filmy. Uwaga: Filmy dodane przed zmianą ustawienia będą nadal widoczne.',
'DONT_NOTIFY_3ea23f'	=>	'Nie Powiadamiaj',
'SEARCH_VIDEOS_e5b832'	=>	'Szukaj Filmów...',
// 1 language strings from file cbgroupjivevideo/templates/default/activity.php
'GROUP_VIDEO_ACTIVITY_TITLE'	=>	'opublikował film w grupie [group]',
// 6 language strings from file cbgroupjivevideo/templates/default/video_edit.php
'EDIT_VIDEO_5b2cbf'	=>	'Edytuj Film',
'NEW_VIDEO_458670'	=>	'Nowy Film',
'VIDEO_34e2d1'	=>	'Film',
'CAPTION_272ba7'	=>	'Podpis',
'UPDATE_VIDEO_3e00c1'	=>	'Uaktualnij Film',
'PUBLISH_VIDEO_dc049f'	=>	'Opublikuj Film',
// 7 language strings from file cbgroupjivevideo/templates/default/videos.php
'GROUP_VIDEOS_COUNT'	=>	'%%COUNT%% Film|%%COUNT%% Filmy',
'AWAITING_APPROVAL_af6558'	=>	'Oczekuje na Zatwierdzenie',
'APPROVE_6f7351'	=>	'Zatwierdź',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_VIDEO_b49259'	=>	'Czy na pewno chcesz wyłączyć publikację tego Filmu?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_VIDEO_406194'	=>	'Czy na pewno chcesz usunąć ten Film?',
'NO_GROUP_VIDEO_SEARCH_RESULTS_FOUND_53386f'	=>	'Nie znaleziono filmów grupy.',
'THIS_GROUP_CURRENTLY_HAS_NO_VIDEOS_8547fe'	=>	'Grupa nie posiada obecnie filmów.',
);
