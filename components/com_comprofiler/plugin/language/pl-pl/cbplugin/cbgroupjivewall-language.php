<?php
/**
* Community Builder (TM) cbgroupjivewall Polish (Poland) language file Frontend
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
// 13 language strings from file cbgroupjivewall/cbgroupjivewall.xml
'INPUT_NUMBER_OF_CHARACTERS_PER_WALL_POST_IF_BLANK__9b7baf'	=>	'Podaj maksymalną długość wpisu w ilości znaków. Pusta wartość oznacza brak ograniczenia. Ustalony limit dotyczy również odpowiedzi na wpis na ścianie. Ustawienie nie dotyczy moderatorów i właścicieli grup. ',
'CHARACTER_LIMIT_52c66f'	=>	'Limit Znaków',
'CREATE_OF_NEW_POST_740891'	=>	'Utwórz nowy wpis',
'NEW_POST_REQUIRES_APPROVAL_9310c5'	=>	'Nowy wpis wymaga zatwierdzenia',
'USER_REPLY_TO_MY_EXISTING_POSTS_0f7c63'	=>	'Odpowiedź Użytkownika na moje wpisy',
'ENABLE_OR_DISABLE_USAGE_OF_WALL_REPLIES_307e20'	=>	'Włącz lub wyłącz możliwość odpowiedzi na ścianie.',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Włącz lub wyłącz podział na strony.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Podaj długość strony. Długość strony określa ilość wierszy wyświetlanych na jednej stronie. Jeśli podział na strony jest wyłączony, to ustawienie to może być użyte do ograniczenia ilości wyświetlanych wyników.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Wybierz sortowanie wierszy.',
'DATE_ASC_a5871f'	=>	'Data Rosnąco',
'DATE_DESC_bcfc6d'	=>	'Data Malejąco',
'REPLIES_ASC_956301'	=>	'Odpowiedzi Rosn',
'REPLIES_DESC_3adf62'	=>	'Odpowiedzi Malej',
// 27 language strings from file cbgroupjivewall/component.cbgroupjivewall.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Grupa nie istnieje.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_POST_IN__66053b'	=>	'Nie posiadasz wystarczających uprawnień, by zamieścić wpis na tej grupie.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_580f05'	=>	'Nie posiadasz wystarczających uprawnień, by edytować ten wpis.',
'SELECT_PUBLISH_STATE_OF_THIS_POST_UNPUBLISHED_POST_25e4c1'	=>	'Wybierz status publikacji wpisu. Nieopublikowany wpis nie będzie widoczne dla odwiedzających.',
'INPUT_THE_POST_TO_SHARE_f29e91'	=>	'Wprowadź treść wpisu na ścianę.',
'INPUT_THE_POST_OWNER_ID_POST_OWNER_DETERMINES_THE__e4515b'	=>	'Podaj ID właściciela wpisu. Właściciel wpisu oznacza jego autora, określonego przez ID Użytkownika.',
'REPLY_DOES_NOT_EXIST_75f6be'	=>	'Odpowiedź nie istnieje.',
'GROUP_POST_FAILED_TO_SAVE'	=>	'Nie udało się zapisać wpisu! Błąd: [error]',
'NEW_GROUP_POST_REPLY_71f88d'	=>	'Nowa odpowiedź na wpis na ścianie grupy',
'USER_HAS_POSTED_A_REPLY_ON_THE_WALL_IN_THE_GROUP_G_dbfbf9'	=>	'[user] przesłał odpowiedź na wpis na ścianie grupy [group]!',
'NEW_GROUP_POST_098c50'	=>	'Nowy wpis na ścianie grupy',
'USER_HAS_POSTED_ON_THE_WALL_IN_THE_GROUP_GROUP_3b88bf'	=>	'[user] dodał wpis na ścianie grupy [group]!',
'NEW_GROUP_POST_AWAITING_APPROVAL_f17edd'	=>	'Nowy wpis na ścianie grupy oczekuje na zatwierdzenie',
'USER_HAS_POSTED_ON_THE_WALL_IN_THE_GROUP_GROUP_AND_634c08'	=>	'[user] dodał wpis na ścianie grupy [group], który oczekuje na zatwierdzenie!',
'POSTED_SUCCESSFULLY_AND_AWAITING_APPROVAL_b7adff'	=>	'Wpis przesłany poprawnie, obecnie oczekuje na zatwierdzenie!',
'POSTED_SUCCESSFULLY_dc026f'	=>	'Wpis został zamieszczony!',
'POST_SAVED_SUCCESSFULLY_df8d13'	=>	'Wpis zapisany poprawnie!',
'YOUR_POST_IS_AWAITING_APPROVAL_6636bb'	=>	'Twój wpis oczekuje na zatwierdzenie.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__ab5489'	=>	'Nie posiadasz wystarczających uprawnień, by opublikować lub zakończyć publikację tego wpisu.',
'POST_DOES_NOT_EXIST_048824'	=>	'Wpis nie istnieje.',
'GROUP_POST_STATE_FAILED_TO_SAVE'	=>	'Nie udało się zapisać stanu publikacji wpisu. Błąd: [error]',
'WALL_POST_REQUEST_ACCEPTED_582538'	=>	'Wpis na ścianie wymaga akceptacji',
'YOUR_WALL_POST_REQUEST_IN_THE_GROUP_GROUP_HAS_BEEN_55d796'	=>	'Twoja prośba o dodanie wpisu na ścianie grupy [group] została zaakceptowana!',
'POST_STATE_SAVED_SUCCESSFULLY_81b542'	=>	'Zapisano stan publikacji wpisu!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_339ee6'	=>	'Nie posiadasz wystarczających uprawnień by usunąć ten wpis.',
'GROUP_POST_FAILED_TO_DELETE'	=>	'Nie udało się usunąć Wpisu! Błąd: [error]',
'POST_DELETED_SUCCESSFULLY_d8ccca'	=>	'Wpis został usunięty!',
// 5 language strings from file cbgroupjivewall/library/Table/WallTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Nie określono Właściciela!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Nie wskazano Grupy!',
'POST_NOT_SPECIFIED_d7239e'	=>	'Nie wskazano Wpisu!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Grupa nie istnieje!',
'REPLY_DOES_NOT_EXIST_84b465'	=>	'Odpowiedź nie istnieje!',
// 3 language strings from file cbgroupjivewall/library/Trigger/AdminTrigger.php
'WALL_94e8a4'	=>	'Ściana',
'ADD_NEW_POST_TO_GROUP_b327ed'	=>	'Dodaj Nowy Wpis do Grupy',
'CONFIGURATION_254f64'	=>	'Konfiguracja',
// 5 language strings from file cbgroupjivewall/library/Trigger/WallTrigger.php
'DISABLE_bcfacc'	=>	'Wyłącz',
'ENABLE_2faec1'	=>	'Włącz',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Włącz, z wymogiem Zatwierdzenia',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_THE_WALL_GRO_366897'	=>	'Opcjonalnie włącz lub wyłącz użycie ściany wpisów. Ustawienie nie dotyczy właścicieli i administratorów grup, którzy zawsze mogą publikować na ścianie. Uwaga: wpisy zamieszone przed zmianą ustawienia nadal będą widoczne.',
'DONT_NOTIFY_3ea23f'	=>	'Nie Powiadamiaj',
// 1 language strings from file cbgroupjivewall/templates/default/activity.php
'GROUP_WALL_ACTIVITY_TITLE'	=>	'opublikowany na [group]',
// 6 language strings from file cbgroupjivewall/templates/default/replies.php
'AWAITING_APPROVAL_af6558'	=>	'Oczekuje na Zatwierdzenie',
'APPROVE_6f7351'	=>	'Zatwierdź',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_POST_ed089a'	=>	'Czy na pewno chcesz wyłączyć publikację tego Wpisu?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_POST_200804'	=>	'Czy na pewno chcesz usunąć ten Wpis?',
'WRITE_A_REPLY_a6697c'	=>	'Napisz odpowiedź...',
'REPLY_25d8df'	=>	'Odpowiedź',
// 3 language strings from file cbgroupjivewall/templates/default/wall.php
'HAVE_A_POST_TO_SHARE_0bfd1f'	=>	'Masz wpis do udostępnienia na ścianie?',
'POST_03d947'	=>	'Wpis',
'THIS_GROUP_CURRENTLY_HAS_NO_POSTS_533e74'	=>	'Grupa nie zawiera wpisów na ścianie.',
// 3 language strings from file cbgroupjivewall/templates/default/wall_edit.php
'EDIT_POST_17ebfc'	=>	'Edytuj Wpis',
'NEW_POST_43eabe'	=>	'Nowy Wpis',
'UPDATE_POST_b9935d'	=>	'Uaktualnij Wpis',
);
