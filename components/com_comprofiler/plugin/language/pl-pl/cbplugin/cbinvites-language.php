<?php
/**
* Community Builder (TM) cbinvites Polish (Poland) language file Frontend
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
'JOIN_ME_de0c95'	=>	'Dołącz do Mnie!',
'MAILER_FAILED_TO_SEND_46f543'	=>	'Automat nie zdołał wysłać wiadomości.',
'TO_ADDRESS_MISSING_225871'	=>	'Brak Adresata.',
'SUBJECT_MISSING_8e0db8'	=>	'Brak Tematu.',
'BODY_MISSING_b6f835'	=>	'Brak Treści.',
'SEARCH_INVITES_9e5f33'	=>	'Szukaj Zaproszeń...',
'INVITE_CODE_NOT_VALID_7cd6f7'	=>	'Kod zaproszenia jest nieprawidłowy.',
'INVITE_CODE_ALREADY_USED_cd715c'	=>	'Kod zaproszenia został już wykorzystany',
'INVITE_CODE_IS_VALID_96aad3'	=>	'Kod Zaproszenia jest prawidłowy.',
// 44 language strings from file plug_cbinvites/cbinvites.xml
'YOUR_REGISTRATION_INVITE_CODE_324b31'	=>	'Twój kod zaproszenia do rejestracji.',
'INVITE_CODE_0a2eb0'	=>	'Kod Zaproszenia',
'INVITES_213b86'	=>	'Zaproszenia',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_ON_TAB_INVITES_b76bc2'	=>	'Włącz lub wyłącz podział na strony w zakładce zaproszeń.',
'INPUT_PAGE_LIMIT_ON_TAB_INVITES_PAGE_LIMIT_DETERMI_b17959'	=>	'Podaj limit strony w zakładce zaproszeń. Limit długości strony określa ile pozycji zaproszeń jest wyświetlane na jednej stronie.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_TAB_INVITES_dc5e0c'	=>	'Włącz lub wyłącz możliwość wyszukiwania w zakładce zaproszeń.',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_INVITES_I_e3fe43'	=>	'Wybierz szablon, który będzie używany dla wszystkich zakłądek CB Zaproszenia. W przypadku, gdy szablon nie jest kompletny, brakujące pliki zostaną uzupełnione z domyślnego szablonu. Pliki szablonu można znaleźć w następującym miejscu: components/com_comprofiler/plugin/user/plug_cbinvites/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_7cf91e'	=>	'Opcjonalnie dodaj suffix klasy elementu DIV obejmującego zakładkę CB Zaproszenia.',
'SELECT_INVITE_CREATE_ACCESS_ACCESS_DETERMINES_WHO__c4a59a'	=>	'Wybierz poziom dostępu tworzenia zaproszeń. Poziom dostępu określa, kto będzie miał możliwość wysyłania zaproszeń. Dostęp jest przyznanawany wybranej grupie oraz wszystkim grupom powyżej (np. Registered obejmują również Author). Ustawienie nie dotyczy moderatorów.',
'INPUT_NUMBER_OF_INVITES_EACH_INDIVIDUAL_USER_IS_LI_0d3553'	=>	'Podaj limit liczby zaproszeń pojedyńczego użytkownika, które pozostają aktywne w dowolnym przedziale czasu (zaakceptowane zaproszenia nie są wliczane do limitu). Pusta wartość oznacza brak ograniczeń. Ustawienie nie obejmuje Moderatorów.',
'INPUT_NUMBER_OF_DAYS_AFTER_INVITE_SENT_TO_ALLOW_RE_12681b'	=>	'Podaj ilość dni, po których będzie możliwość wysłania zaproszenia ponownie (zaakceptowane zaproszenia nie są objęte tym limitem). Pozostawienie pustej wartości wyłacza możliwość ponownego wysyłania zaproszeń. Ustawienie nie obejmuje Moderatorów.',
'RESEND_DELAY_4f8afe'	=>	'Opóźnienie Ponownego Wysłania',
'SELECT_USAGE_OF_MULTIPLE_EMAILS_IN_A_SINGLE_INVITE_e8af1e'	=>	'Wybierz możliwość wstawienia wielu adresów email w pojedyńczym zaproszeniu - adresy oddzielone przecinkami (np. email1@domena.pl, email2@domena.pl, email3@domena.pl). Ustawienie nie dotyczy Moderatorów.',
'MULTIPLE_INVITES_b8f6fd'	=>	'Hurtowe Zaproszenia',
'SELECT_USAGE_OF_DUPLICATE_INVITES_TO_THE_SAME_ADDR_96bfd8'	=>	'Wybierz użycie podwójnych zaproszeń tego samego adresu. Ustawienie nie dotyczy Moderatorów.',
'DUPLICATE_INVITES_9b3f9e'	=>	'Podwójne Zaproszenia',
'SELECT_CONNECTION_METHOD_FROM_INVITER_TO_INVITEE_32692b'	=>	'Wybierz rodzaj znajomości / połaczenia pomiędzy zapraszającym, a zaproszonym.',
'CONNECTION_c2cc70'	=>	'Znajomi',
'PENDING_CONNECTION_30ec76'	=>	'Oczekujące Zaproszenie do Znajomych',
'AUTO_CONNECTION_5f8c15'	=>	'Automatyczne Znajomości',
'SELECT_INVITE_EMAIL_BODY_EDITOR_d8f5e1'	=>	'Wybierz edytor treści wiadomości email.',
'PLAIN_TEXT_e44b14'	=>	'Zwykły Tekst',
'HTML_TEXT_503c11'	=>	'Tekst HTML',
'WYSIWYG_fcf0d4'	=>	'WYSIWYG',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_INVITES_REQU_65e72e'	=>	'Włącz lub wyłącz użycie zabezpieczeń antyspamowych captcha w zaproszeniach. Wymaga zainstalowania aktualnej wytczki CB AntiSpam. Ustawienie nie dotyczy Moderatorów.',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_f0ef2c'	=>	'Wprowadź nazwę Nadawcy zaproszenia (np. Moja Wspaniała Strona CB!) - wspierane są zmienne. W przypadku pustej wartości zostanie użyta nazwa użytkownika. Jeśli natomiast wypełnionno nazawa Odpowiedz-Do, to zostanie wstawiona zamiast nazwy użytkownika.',
'FROM_NAME_4a4a8f'	=>	'Nazwa Nadawcy',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_ef3c3b'	=>	'Podaj email Nadawcy zaproszenia (np. ogolny@domena.com) - wspierane są zmienne. Przy pustej wartości, zostanie użyty domyśny adres email użytkownika. Jeśli natomiast podano adres w Odpowiedz-Do, to zostanie użyty zamiast adresu użytkownika.',
'FROM_ADDRESS_a5ab7d'	=>	'Adres Nadawcy',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Podaj adres kopii CC - wspierane są zmienne i ciągi pliku języka (np. [email]); kilka adresów należy oddzielić przecinkami (np. email1@domena.pl, email2@domena.pl, email3@domena.pl).',
'CC_ADDRESS_b6327b'	=>	'CC Adres',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Podaj adres ukrytej kopii BCC - wspierane są zmienne i ciągi pliku języka (np. [email]); kilka adresów należy oddzielić przecinkami (np. email1@domena.pl, email2@domena.pl, email3@domena.pl).',
'BCC_ADDRESS_33b728'	=>	'BCC Adres',
'INPUT_SUBSTITUTION_SUPPORTED_PREFIX_OF_INVITE_EMAI_91cd80'	=>	'Wprowadź przedrostek tematu wiadomości email zaproszenia - zmienne są wspierane. Dodatkowe dostępne zmienne: [site], [sitename], [path], [itemid], [register], [profile], [code], oraz [to].',
'SUBJECT_PREFIX_175911'	=>	'Przedrostek Tematu',
'SITENAME_6b68ee'	=>	'[sitename] - ',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_OF_INVITE_EMAI_058012'	=>	'Wprowadź nagłówek wiadomości email zaproszenia - zmienne są wspierane. Dodatkowe dostępne zmienne: [site], [sitename], [path], [itemid], [register], [profile], [code], oraz [to].',
'BODY_HEADER_67622c'	=>	'Nagłówek Wiadomości',
'YOU_HAVE_BEEN_INVITED_BY_USERNAME_TO_JOIN_SITENAME_e90186'	=>	'<p>Otrzymałaś/eś zaproszenie od użytkownika  [username] by dołączyć do strony [sitename]!</p><br>',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_OF_INVITE_EMAI_f5fa57'	=>	'Wprowadź stopkę wiadomości email zaproszenia - zmienne są wspierane. Dodatkowe dostępne zmienne: [site], [sitename], [path], [itemid], [register], [profile], [code], oraz [to].',
'BODY_FOOTER_6046e1'	=>	'Stopka Wiadomości',
'INVITE_CODE_CODESITENAME_SITEREGISTRATION_REGISTER_eec8c6'	=>	'<br><p>Kod Zaproszenia - [code]<br>[sitename] - [site]<br>Rejestracja - [register]<br>[username] - [profile]</p>',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__523216'	=>	'Podaj adres załącznika - wspierane są zmienne i ciągi pliku języka (np. [cb_myfile]); kilka pozycji należy oddzielić przecinkami (np. /home/username/public_html/images/file1.zip,[path]/file2.zip, http://www.domain.com/file3.zip). Dodatkowe wspierane zmienne to: [site], [sitename], [path], [itemid], [register], [profile], [code] oraz [to].',
'ATTACHMENT_e9cb21'	=>	'Załącznik',
// 22 language strings from file plug_cbinvites/component.cbinvites.php
'INPUT_INVITE_EMAIL_TO_ADDRESS_SEPARATE_MULTIPLE_EM_2028c1'	=>	'Podaj emal Adresata. Kilka adresów oddziel przecinkami.',
'INPUT_INVITE_EMAIL_TO_ADDRESS_54551f'	=>	'Podaj email Adresata.',
'INPUT_INVITE_EMAIL_SUBJECT_IF_LEFT_BLANK_A_SUBJECT_0974fc'	=>	'Podaj temat zaproszenia email; przy pustej wartości zostanie użyty temat domyślny.',
'OPTIONALLY_INPUT_PRIVATE_MESSAGE_TO_INCLUDE_WITH_I_e1d750'	=>	'Opcjonalnie wprowadź prywatną wiadomość, która ma zostać dołączna do zaproszenia email.',
'INPUT_OWNER_OF_INVITE_AS_SINGLE_INTEGER_USERID_THI_998c63'	=>	'Podaj właściciela zaproszenia w postaci pojedyńczej liczby user_id. Oznacza to użytkownika, który wysyła zaproszenie.',
'OPTIONALLY_INPUT_USER_OF_INVITE_AS_SINGLE_INTEGER__329e71'	=>	'Opcjonalnie podaj zaproszonego użytkownika w postaci pojedyńczej liczby user_id. Oznacza to użytkownikal, który przyjął zaporszenie.',
'COMMA_SEPERATED_LISTS_ARE_NOT_SUPPORTED_PLEASE_USE_7676e6'	=>	'Nie jest wspierana lista oddzielona przecinkami. Proszę podaj pojedyńczego Adresata.',
'INVITE_LIMIT_REACHED_1e1e31'	=>	'Osiągnięto maksymalny limit zaproszeń!',
'TO_ADDRESS_NOT_SPECIFIED_e292c0'	=>	'Nie podano Adresata.',
'INVITE_TO_ADDRESS_INVALID'	=>	'Adresat nieprawidłowy: [to_address]',
'YOU_CAN_NOT_INVITE_YOUR_SELF_487ade'	=>	'Nie możesz zaprosić samej/samego siebie.',
'TO_ADDRESS_IS_ALREADY_A_USER_4c2f27'	=>	'Adresat jest już użytkownikiem.',
'TO_ADDRESS_IS_ALREADY_INVITED_f165f0'	=>	'Adresat został już zaproszony.',
'INVITE_FAILED_SAVE_ERROR'	=>	'Nie udało się zapisać Zaproszenia! Błąd: [error]',
'INVITE_FAILED_SEND_ERROR'	=>	'Nieudane wysłanie Zaproszenia! Błąd: [error]',
'INVITE_SENT_SUCCESSFULLY_380490'	=>	'Zaproszenie wysłane pomyślnie!',
'INVITE_SAVED_SUCCESSFULLY_14a90f'	=>	'Zaproszenie zapisane pomyślnie!',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_RESENT_528e3e'	=>	'Zaproszenie zostało już zaakceptowane i nie może być ponownie wysłane.',
'INVITE_RESEND_NOT_APPLICABLE_AT_THIS_TIME_c65f19'	=>	'Ponowne przesłanie Zaproszenia nie jest możliwe w tym momencie.',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_DELETED_cbc934'	=>	'Już zaakceptowane zaproszenie nie może być skasowane.',
'INVITE_FAILED_DELETE_ERROR'	=>	'Nie udało się usunąć Zaproszenia: Błąd: [error]',
'INVITE_DELETED_SUCCESSFULLY_9ea357'	=>	'Zaproszenie zostało usunięte!',
// 7 language strings from file plug_cbinvites/templates/default/invite_edit.php
'EDIT_INVITE_1faaed'	=>	'Edytuj Zaproszenie',
'CREATE_INVITE_1e89ce'	=>	'Utwórz Zaproszenie',
'TO_e12167'	=>	'Do',
'BODY_ac101b'	=>	'Treść',
'USER_8f9bfe'	=>	'Użytkownika',
'UPDATE_INVITE_7c2f89'	=>	'Uaktualnij Zaproszenie',
'SEND_INVITE_962943'	=>	'Wyślij Zaproszenie',
// 9 language strings from file plug_cbinvites/templates/default/tab.php
'NEW_INVITE_4093fa'	=>	'Nowe Zaproszenie',
'SENT_7f8c02'	=>	'Wysłane',
'PLEASE_RESEND_6ba908'	=>	'Proszę Prześlij Ponownie',
'ACCEPTED_382ab5'	=>	'Zaakceptowane',
'RESEND_1c0b8f'	=>	'Wyślij Ponownie',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_INVITE_405d09'	=>	'Czy na pewno chcesz usunąć to Zaproszenie?',
'NO_INVITE_SEARCH_RESULTS_FOUND_63c4e3'	=>	'Brak rezultatów wyszukiwania zaproszeń.',
'YOU_HAVE_NO_INVITES_2f8b42'	=>	'Nie posiadasz zaproszeń.',
'THIS_USER_HAS_NO_INVITES_f2d878'	=>	'Ten użytkownik nie posiada zaproszeń.',
);
