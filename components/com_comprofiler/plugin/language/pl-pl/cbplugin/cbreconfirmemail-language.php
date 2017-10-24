<?php
/**
* Community Builder (TM) cbreconfirmemail Polish (Poland) language file Frontend
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
'CHANGED_820dbd'	=>	'Zmieniony',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Podaj wiadomość (możliwe użycie zmiennych), która bedzie wyświetlana po zmianie adresu email. Pozostaw puste, by nie wyświetlać żadnego komunikatu.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'Twój adres email uległ zmianie i wymaga powtórnego potwierdzenia. Proszę sprawdź w swojej nowej skrzynce email, czy otrzymałeś wiadomość z prośbą potwierdzenia zmiany.',
'NOTIFICATION_96d008'	=>	'Powiadomienie',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Podaj nazwę nadawcy (możliwe użycie zmiennych), która zostanie użyta we wszystkich wysyłanych wiadomościach z prośbą o potwierdzenie adresu email (np. Moja Wyjątkowa Strona CB!). W przypadku pustej wartości, domyślnie zostanie wstawiona nazwa użytkownika. Jeśli podano wartość \'odpowiedz do\', to zostanie powtórzona ta wartość.',
'FROM_NAME_4a4a8f'	=>	'Nazwa Nadawcy',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Podaj adres nadawcy wiadomości potwierdzenia email (np. ogolny@domena.pl) - wspierane są zmienne i ciągi pliku języka. W przypadku pozostawienia pustej wartości zostanie użyty adres email użytkownika. Jeśli został ustawiony adres odpowiedz-do, to zostanie wstawiony w tym miejscu.',
'FROM_ADDRESS_a5ab7d'	=>	'Adres Nadawcy',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Wprowadź temat wiadomości potwierdzenia adresu email - wspierane są zmienne i ciągi pliku języka.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Twój adres email uległ zmianie',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Wprowadź treść wiadomości potwierdzenia adresu email - wspierane są html, zmienne i ciągi pliku języka. Użyj [reconfirm] by wstawić link potwierdzający. Dodatkowo można użyć [old_email] do wstawienia starego adresu lub [new_email] dla nowego adresu email.',
'BODY_ac101b'	=>	'Treść',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'Adres email powiązany z Twoim kontem [username] został zmieniony na [new_email] i wymaga potwierdzenia.<br><br>Możesz potwierdzić swój adres email klikając poniższy link:<br><a href="[reconfirm]">[reconfirm]</a><br><br>Jeśli zmiana adresu email została wprowadzona przez pomyłkę, proszę skontaktuj się z administratorem lub anuluj zmianę klikając <a href="[cancel]">Anuluj Zmianę</a>.',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Podaj adres kopii CC - wspierane są zmienne i ciągi pliku języka (np. [email]); kilka adresów należy oddzielić przecinkami (np. email1@domena.pl, email2@domena.pl, email3@domena.pl).',
'CC_ADDRESS_b6327b'	=>	'Pole CC',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Podaj adres ukrytej kopii BCC - wspierane są zmienne i ciągi pliku języka (np. [email]); kilka adresów należy oddzielić przecinkami (np. email1@domena.pl, email2@domena.pl, email3@domena.pl).',
'BCC_ADDRESS_33b728'	=>	'Pole BCC',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Podaj adres załącznika - wspierane są zmienne i ciągi pliku języka (np. [cb_myfile]); kilka pozycji należy oddzielić przecinkami (np. /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip).',
'ATTACHMENT_e9cb21'	=>	'Załącznik',
'RECONFIRMED_e748a2'	=>	'Potwierdzony',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Podaj adres URL przekierowania w przypadku udanego potwierdzenia adresu email - wspierane są zmienne i ciągi pliku języka. W przypadku pustej wartości, nie nastąpi przekierowanie.',
'REDIRECT_4202ef'	=>	'Przekieruj',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Podaj treść komunikatu wyświetlanego po udanym potwierdzeniu adresu email - wspierane są zmienne i ciągi pliku języka.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'Nowy adres email został prawidłowo potwiedzony!',
'CANCELLED_a149e8'	=>	'Odwołano',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Podaj adres URL przekierowania w przypadku udanego anulowania zmiany adresu email - wspierane są zmienne i ciągi pliku języka. W przypadku pustej wartości, nie nastąpi przekierowanie.',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Podaj treść komunikatu wyświetlanego po udanym anulowaniu zmiany adresu email - wspierane są zmienne i ciągi pliku języka.',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'Odwołanie zmiany adresu przeprowadzona prawidłowo!',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Brak kodu potwierdzającego.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'Użytkownik nie jest zgodny z kodem potwierdzającym.',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Kod potwierdzający jest nieprawidłowy.',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Nie udało się anulować zmiany adresu email! Błąd: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Nie udało się potwierdzić nowego adresu email! Błąd: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'Adres mail został już potwierdzony.',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'Adresy Email',
);
