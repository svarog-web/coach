<?php
/**
* Community Builder (TM) cbinvites Russian (Russia) language file Frontend
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
'JOIN_ME_de0c95'	=>	'Присоединяйтесь ко мне!',
'MAILER_FAILED_TO_SEND_46f543'	=>	'Отправить почту не удалось!',
'TO_ADDRESS_MISSING_225871'	=>	'Отсутствует адрес получателя!',
'SUBJECT_MISSING_8e0db8'	=>	'Отсутствует тема!',
'BODY_MISSING_b6f835'	=>	'Отсутствует текст письма!',
'SEARCH_INVITES_9e5f33'	=>	'Поиск приглашений...',
'INVITE_CODE_NOT_VALID_7cd6f7'	=>	'Код приглашения недействителен!',
'INVITE_CODE_ALREADY_USED_cd715c'	=>	'Данный код приглашения уже используется в этой системе!',
'INVITE_CODE_IS_VALID_96aad3'	=>	'Код приглашения действителен!',
// 44 language strings from file plug_cbinvites/cbinvites.xml
'YOUR_REGISTRATION_INVITE_CODE_324b31'	=>	'Ваш код пригласительной регистрации',
'INVITE_CODE_0a2eb0'	=>	'Код приглашения',
'INVITES_213b86'	=>	'Приглашения',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_ON_TAB_INVITES_b76bc2'	=>	'Включить или выключить использование постраничной разбивки на вкладке приглашений.',
'INPUT_PAGE_LIMIT_ON_TAB_INVITES_PAGE_LIMIT_DETERMI_b17959'	=>	'Ограничение приглашений на странице. Он определяет сколько приглашений будет показано на одной веб-странице.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_TAB_INVITES_dc5e0c'	=>	'Включить или выключить использование поля поиска на вкладке приглашений.',
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_INVITES_I_e3fe43'	=>	'Выберите шаблон, который будет использоваться для всех приглашений компонента СВ. Если шаблон не завершен, то отсутствующие файлы будут взяты из шаблона по умолчанию. Файлы шаблона могут быть сохранены по следующему пути: components/com_comprofiler/plugin/user/plug_cbinvites/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_7cf91e'	=>	'На Ваш выбор, т.е. совсем не обязательно, добавьте для тега DIV всех приглашений СВ суффикс класса CSS, ',
'SELECT_INVITE_CREATE_ACCESS_ACCESS_DETERMINES_WHO__c4a59a'	=>	'Выберите уровень доступа к созданию приглашений. Он будет определять кто сможет отправлять приглашения. Выбранная группа, а также группы над ней (например, в случае выбора группы \'Registered\' находящаяся над ней группа \'Author\'), будут обладать таким правом.',
'INPUT_NUMBER_OF_INVITES_EACH_INDIVIDUAL_USER_IS_LI_0d3553'	=>	'Введите число, определяющее сколько активных приглашений может иметь каждый отдельный пользователь (уже принятые приглашения пользователя в это число не входят). Если это поле оставить пустым, то число приглашений будет не ограниченно. Модераторы не подчиняются данному ограничению.',
'INPUT_NUMBER_OF_DAYS_AFTER_INVITE_SENT_TO_ALLOW_RE_12681b'	=>	'Введите через сколько дней после отправки приглашения пользователю будет разрешено отправлять приглашение повторно (принятые приглашения отправлять повторно не разрешается). Если это поле оставить пустым, то число приглашений будет не ограниченно. Модераторы не подчиняются данному ограничению.',
'RESEND_DELAY_4f8afe'	=>	'Задержка перед повторной отправкой',
'SELECT_USAGE_OF_MULTIPLE_EMAILS_IN_A_SINGLE_INVITE_e8af1e'	=>	'Настройте использование нескольких отделенных друг от друга знаком запятой адресов электронной почты в одном приглашении (например, email1@domain.com, email2@domain.com, email3@domain.com). Модераторы во включении для них этой настройки не нуждаются.',
'MULTIPLE_INVITES_b8f6fd'	=>	'Множественные приглашения',
'SELECT_USAGE_OF_DUPLICATE_INVITES_TO_THE_SAME_ADDR_96bfd8'	=>	'Настройте использование нескольких дубликатов приглашений, отправленных на один и тот же адрес электронной почты. Модераторы во включении для них этой настройки не нуждаются.',
'DUPLICATE_INVITES_9b3f9e'	=>	'Дублировать приглашения',
'SELECT_CONNECTION_METHOD_FROM_INVITER_TO_INVITEE_32692b'	=>	'Выберите метод соединения приглашающего с приглашаемым.',
'CONNECTION_c2cc70'	=>	'Соединение',
'PENDING_CONNECTION_30ec76'	=>	'В процессе соединения',
'AUTO_CONNECTION_5f8c15'	=>	'Автоматическое соединение',
'SELECT_INVITE_EMAIL_BODY_EDITOR_d8f5e1'	=>	'Выберите редактор текста электронного письма приглашения',
'PLAIN_TEXT_e44b14'	=>	'Обычный текст',
'HTML_TEXT_503c11'	=>	'Текст с кодом HTML',
'WYSIWYG_fcf0d4'	=>	'WYSIWYG',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_INVITES_REQU_65e72e'	=>	'Enable or disable usage of captcha on invites. Requires latest CB AntiSpam to be installed and published. Moderators are exempt from this configuration.',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_f0ef2c'	=>	'Введите текст-замену названия отправителя, которое будет добавлено во всех приглашениях (например, \'Мой потрясающий СВ веб-сайт!\'). Если это поле оставить незаполненным, то по умолчанию для названия отправителя будет использовано имя отправляющего приглашение пользователя. Если указано, то название \'Кому ответить\' будет добавлено как имя пользователя.???',
'FROM_NAME_4a4a8f'	=>	'От кого',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_ef3c3b'	=>	'Введите текст-замену адреса электронной почты отправителя, который будет добавлен во всех приглашениях (например, general@domain.com). Если это поле оставить незаполненным, то по умолчанию для адреса электронной почты отправителя будет использован адрес электронной почты пользователя. Если указано, то адрес электронной почты \'Кому ответить\' будет добавлено как адрес электронной почты пользователя.???',
'FROM_ADDRESS_a5ab7d'	=>	'Адрес электронной почты \'От кого\'',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Введите текст-замену адреса электронной почты получателя копии письма (например, [email]); поддерживается использование нескольких отделенных друг от друга адресов электронной почты (например, email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'Адрес для отправки копии',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Введите текст-замену адреса электронной почты скрытного получателя копии письма (например, [email]); поддерживается использование нескольких отделенных друг от друга адресов электронной почты (например, email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'Адрес скрытного получателя копии',
'INPUT_SUBSTITUTION_SUPPORTED_PREFIX_OF_INVITE_EMAI_91cd80'	=>	'Введите замену для приставки темы электронного письма приглашения. Поддерживаются дополнительные заменители: [site], [sitename], [path], [itemid], [register], [profile], [code], и [to].',
'SUBJECT_PREFIX_175911'	=>	'Приставка темы',
'SITENAME_6b68ee'	=>	'[sitename] - ',
'INPUT_SUBSTITUTION_SUPPORTED_HEADER_OF_INVITE_EMAI_058012'	=>	'Введите замену для верхнего колонтитула текста электронного письма приглашения. Поддерживаются дополнительные заменители: [site], [sitename], [path], [itemid], [register], [profile], [code] и [to].',
'BODY_HEADER_67622c'	=>	'Верхний колонтитул текста',
'YOU_HAVE_BEEN_INVITED_BY_USERNAME_TO_JOIN_SITENAME_e90186'	=>	'<p>You have been invited by [username] to join [sitename]!</p><br>',
'INPUT_SUBSTITUTION_SUPPORTED_FOOTER_OF_INVITE_EMAI_f5fa57'	=>	'Введите замену для нижнего колонтитула текста электронного письма приглашения. Поддерживаются дополнительные заменители: [site], [sitename], [path], [itemid], [register], [profile], [code] и [to].',
'BODY_FOOTER_6046e1'	=>	'Нижний колонтитул текста',
'INVITE_CODE_CODESITENAME_SITEREGISTRATION_REGISTER_eec8c6'	=>	'<br><p>Invite Code - [code]<br>[sitename] - [site]<br>Registration - [register]<br>[username] - [profile]</p>',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__523216'	=>	'Введите замену для адреса файла приложения (например, [cb_myfile]); поддерживается введение через запятую нескольких адресов (например, /home/username/public_html/images/file1.zip,[path]/file2.zip, http://www.domain.com/file3.zip). Поддерживаются дополнительные заменители: [site], [sitename], [path], [itemid], [register], [profile], [code], and [to].',
'ATTACHMENT_e9cb21'	=>	'Приложение',
// 22 language strings from file plug_cbinvites/component.cbinvites.php
'INPUT_INVITE_EMAIL_TO_ADDRESS_SEPARATE_MULTIPLE_EM_2028c1'	=>	'Введите адрес электронной почты получателя приглашения. При введении более одного адреса электронной почты отделяйте их друг от друга знаком запятой.',
'INPUT_INVITE_EMAIL_TO_ADDRESS_54551f'	=>	'Введите адрес электронной почты получателя приглашения',
'INPUT_INVITE_EMAIL_SUBJECT_IF_LEFT_BLANK_A_SUBJECT_0974fc'	=>	'Введите тему электронного письма приглашения; если это поле оставлено незаполненным, то будет применена некоторая тема.???',
'OPTIONALLY_INPUT_PRIVATE_MESSAGE_TO_INCLUDE_WITH_I_e1d750'	=>	'По своему желанию, т.е. совсем не обязательно, введите отправляемое вместе с электронным письмом приглашения личное сообщение приглашаемому.',
'INPUT_OWNER_OF_INVITE_AS_SINGLE_INTEGER_USERID_THI_998c63'	=>	'Введите как целое число ID номер пользователя владельца приглашения. Это тот пользователь, который отправил данное приглашение.',
'OPTIONALLY_INPUT_USER_OF_INVITE_AS_SINGLE_INTEGER__329e71'	=>	'По своему желанию, т.е. совсем не обязательно, введите как целое число ID номер пользователя приглашения. Это тот пользователь, который получил приглашение.',
'COMMA_SEPERATED_LISTS_ARE_NOT_SUPPORTED_PLEASE_USE_7676e6'	=>	'Списки отделенных друг от друга знаком запятой объектов не поддерживаются! Используйте, пожалуйста, единственный адрес получателя.',
'INVITE_LIMIT_REACHED_1e1e31'	=>	'Достигнут лимит приглашений!',
'TO_ADDRESS_NOT_SPECIFIED_e292c0'	=>	'Не указан адрес получателя!',
'INVITE_TO_ADDRESS_INVALID'	=>	'Недействителен адрес получателя: [to_address]',
'YOU_CAN_NOT_INVITE_YOUR_SELF_487ade'	=>	'Вы не можете отправлять приглашение самому себе!',
'TO_ADDRESS_IS_ALREADY_A_USER_4c2f27'	=>	'Данный адрес получателя уже используется!!!',
'TO_ADDRESS_IS_ALREADY_INVITED_f165f0'	=>	'На адрес этого получателя уже было выслано приглашение.',
'INVITE_FAILED_SAVE_ERROR'	=>	'Сохранить приглашение не удалось! Ошибка: [error]',
'INVITE_FAILED_SEND_ERROR'	=>	'Отправить приглашение не удалось! Ошибка: [error]',
'INVITE_SENT_SUCCESSFULLY_380490'	=>	'Приглашение было успешно отправлено!',
'INVITE_SAVED_SUCCESSFULLY_14a90f'	=>	'Приглашение было успешно сохранено!',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_RESENT_528e3e'	=>	'Приглашение уже было принято и не может быть отправлено повторно!',
'INVITE_RESEND_NOT_APPLICABLE_AT_THIS_TIME_c65f19'	=>	'Повторная отправка приглашения в данное время не применима!',
'INVITE_ALREADY_ACCEPTED_AND_CAN_NOT_BE_DELETED_cbc934'	=>	'Приглашение уже принято и не может быть удалено!',
'INVITE_FAILED_DELETE_ERROR'	=>	'Удалить приглашение не удалось! Ошибка: [error]',
'INVITE_DELETED_SUCCESSFULLY_9ea357'	=>	'Приглашение удалено успешно!',
// 7 language strings from file plug_cbinvites/templates/default/invite_edit.php
'EDIT_INVITE_1faaed'	=>	'Редактировать приглашение',
'CREATE_INVITE_1e89ce'	=>	'Создать приглашение',
'TO_e12167'	=>	'Кому',
'BODY_ac101b'	=>	'Текст письма',
'USER_8f9bfe'	=>	'Пользователь',
'UPDATE_INVITE_7c2f89'	=>	'Обновить приглашение',
'SEND_INVITE_962943'	=>	'Отправить приглашение',
// 9 language strings from file plug_cbinvites/templates/default/tab.php
'NEW_INVITE_4093fa'	=>	'Создать приглашение',
'SENT_7f8c02'	=>	'Отправить',
'PLEASE_RESEND_6ba908'	=>	'Пожалуйста, отправьте повторно',
'ACCEPTED_382ab5'	=>	'Принято',
'RESEND_1c0b8f'	=>	'Отправлено повторно',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_INVITE_405d09'	=>	'Вы действительно желаете удалить это приглашение?',
'NO_INVITE_SEARCH_RESULTS_FOUND_63c4e3'	=>	'Поиск приглашение не нашел никаких приглашений!',
'YOU_HAVE_NO_INVITES_2f8b42'	=>	'У Вас нет никаких приглашений!',
'THIS_USER_HAS_NO_INVITES_f2d878'	=>	'У данного пользователя нет никаких приглашений!',
);
