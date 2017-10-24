<?php
/**
* Community Builder (TM) cbconnect Danish (Denmark) language file Frontend
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
// 5 language strings from file plug_cbconnect/cbconnect.php
'UNLINK_PROVIDER_ACCOUNT'	=>	'Unlink din [provider] konto',
'PROVIDER_PROFILE'	=>	'[provider] profil',
'PROVIDER_PROFILE_ID'	=>	'[provider] profil id [provider_id]',
'PROVIDER_PROFILE_LINKED_TO_ACCOUNT'	=>	'Din [provider_profile] bliver linket til denne konto',
'VIEW_PROVIDER_PROFILE'	=>	'Se [provider] profil',
// 111 language strings from file plug_cbconnect/cbconnect.xml
'ENABLE_OR_DISABLE_THIS_PROVIDER_THIS_ALLOWS_QUICK__e09ec3'	=>	'Aktiver eller deaktiver denne leverandør. Dette tillader hurtig de- aller aktivering uden at skylle rydde parametrene',
'INPUT_APPLICATION_ID_OR_APPLICATION_KEY_2c2860'	=>	'Indtast applikations id eller applikations nøgle',
'APP_ID_81ee9e'	=>	'App ID',
'INPUT_APPLICATION_SECRET_ac0f26'	=>	'Indtast applikations hemmelighed.',
'APP_SECRET_61be04'	=>	'App hemmelighed',
'OPTIONALLY_SELECT_ADDITIONAL_PERMISSIONS_TO_REQUES_102a4f'	=>	'Vælg eventuelt yderligere rettigheder der skal forespørges. Bemærk at de nødvendige rettigheder for log på og registrering er allerede forespurgt. Forespørg ikke yderligere rettigheder med mindre det er absolut nødvendigt.',
'PERMISSIONS_d08ccf'	=>	'Tilladelser',
'SELECT_PERMISSIONS_721073'	=>	'- Vælg rettigheder -',
'THE_PROVIDER_CALLBACK_URL_THIS_URL_SHOULD_BE_SUPPL_71c608'	=>	'Leverandør tilbagekalds URL\'en. Denne URL skal angives til leverandør konfigurationen som behøvet. Dette er nogle gange også kaldet omdirigerings URL\'en. Bemærk at ikke alle applikationer behøver dette og behøver måske kun domænet.',
'CALLBACK_URL_971aa9'	=>	'Callback URL',
'ENABLE_OR_DISABLE_DEBUGGING_OF_THIS_PROVIDER_THIS__9e0efb'	=>	'Aktiver eller deaktiver fejlsøgning for denne leverandør. Dette vil returnere et var_dump for leverandør http forespørgsels responser (under godkendelse og API kald).',
'DEBUG_a60390'	=>	'Fejlsøg',
'ENABLE_OR_DISABLE_ACCOUNT_REGISTRATION_REGISTER_AL_9767ca'	=>	'Aktiver eller deaktiver konto registrering. Registrering tillader ikke-eksisterende Community Builder brugere at registrere sig med deres leverandør konto detaljer.',
'REGISTER_0ba758'	=>	'Registrer',
'SELECT_HOW_REGISTRATION_BUTTON_IS_DISPLAYED_b21e54'	=>	'Vælg hvordan registreringsknappen vises.',
'BUTTON_STYLE_190f98'	=>	'Knap stil',
'SELECT_IF_USERS_SHOULD_BE_REGISTERED_IMMEDIATELY_W_823dfb'	=>	'Vælg om brugere skal registreres straks med Enkelt Log-på eller om standard CB registreringsformularen skal for-udfyldes med deres profiledata fra det sociale websted. Bemærk at at for for-udfyldt tilstand, skal dette leverandør cb-felt - fx fb_userid - sættes til vis ved registrering.',
'OPTIONALLY_INPUT_SUBSTITUTION_SUPPORTED_USERNAME_F_619999'	=>	'Indtast eventuelt substitutionsunderstøttet brugernavns formaterings overskrivning.',
'USERNAME_FORMAT_08bb89'	=>	'Brugernavnsformat',
'THE_ADDITIONAL_SUPPORTED_SUBSTITUTIONS_FOR_USERNAM_7dcc39'	=>	'Yderligere understøttede substitutioner til brugernavnsformat.',
'USERNAME_SUBSTITUTIONS_380e8d'	=>	'Brugernavnssubstitutioner',
'OPTIONALLY_SELECT_REGISTRATION_USERGROUPS_OF_USERS_19373c'	=>	'Vælg eventuelt registrerings brugergrupper for brugere.',
'USERGROUPS_6ad0aa'	=>	'Brugergrupper',
'SELECT_REGISTRATION_TO_REQUIRE_ADMIN_APPROVAL_807f79'	=>	'Vælg om registrering kræver admin godkendelse.',
'SELECT_REGISTRATION_TO_REQUIRE_EMAIL_CONFIRMATION_1e7a18'	=>	'Vælg om registrering kræver e-mail godkendelse.',
'CONFIRMATION_f4d1ea'	=>	'Bekræftelse',
'SELECT_AVATAR_TO_REQUIRE_ADMIN_APPROVAL_7215ef'	=>	'Vælg om brugerbillede kræver admin godkendelse',
'AVATAR_APPROVAL_30980b'	=>	'Brugerbillede godkendelse',
'SELECT_CANVAS_TO_REQUIRE_ADMIN_APPROVAL_887bbf'	=>	'Vælg om kanvas billede kræver admin godkendelse.',
'CANVAS_APPROVAL_18e724'	=>	'Kanvas godkendelse',
'FIELDS_a4ca5e'	=>	'Felter',
'SELECT_A_FIELD_TO_SYNCHRONIZE_FROM_ON_REGISTRATION_f0034f'	=>	'Vælg et felt der skal synkroniseres fra ved registrering. Bemærk at felt værdi formatet er ikke garanteret og kan kræve yderligere rettigheder. Værdier bliver leveret som de er.',
'FROM_FIELD_b7c383'	=>	'Fra felt',
'SELECT_PROVIDER_FIELD_fa0518'	=>	'- Vælg leverandør felt -',
'SELECT_A_FIELD_TO_SYNCHRONIZE_TO_ON_REGISTRATION_N_2ec5a1'	=>	'Vælg et felt der skal synkroniseres til ved registrering. Bemærk at kernefelter som brugernavn, navn, fornavn, mellemnavn, efternavn, brugerbilleder, kanvas, og e-mail er allerede synkroniserede.',
'TO_FIELD_eeb0a6'	=>	'Til felt',
'ENABLE_OR_DISABLE_ACCOUNT_LINKING_LINKING_ALLOWS_E_c9b748'	=>	'Aktiver eller deaktiver kontolinkning. Linkning tillader at eksisterende Community Builder brugere der er logget på, kan linke deres leverandør konto til deres eksisterende Community Builder konto.',
'LINKING_4f0929'	=>	'Linkning',
'SELECT_HOW_THE_LINK_BUTTON_IS_DISPLAYED_5b29a3'	=>	'Vælg hvordan link knappen vises.',
'ENABLE_OR_DISABLE_RESYNCHRONIZING_OF_PROVIDER_PROF_da4ff2'	=>	'Aktiver eller deaktiver resynkronisering af leverandør profil data ved linkning.',
'RESYNCHRONIZE_1b0f89'	=>	'Resynkroniser',
'SELECT_HOW_THE_LOGIN_BUTTON_IS_DISPLAYED_f7ced5'	=>	'Vælg hvordan log på knappen vises.',
'INPUT_OPTIONAL_FIRST_TIME_LOGIN_REDIRECT_URL_EG_IN_372d5b'	=>	'Indtast valgfri førstegangs log på omdirigerings URL (fx index.php?option=com_comprofiler).',
'FIRST_REDIRECT_2cc28b'	=>	'Først omdiriger',
'INPUT_OPTIONAL_LOGIN_REDIRECT_URL_EG_INDEXPHPOPTIO_a16181'	=>	'Indtast valgfri log på omdirigerings URL (fx index.php?option=com_comprofiler).',
'REDIRECT_4202ef'	=>	'Omdiriger',
'ENABLE_OR_DISABLE_RESYNCHRONIZING_OF_PROVIDER_PROF_a7617c'	=>	'Aktiver eller deaktiver resynkronisering af leverandør profil data ved hvert log på.',
'INPUT_APPLICATION_CONSUMER_KEY_191627'	=>	'Indtast applikations forbruger nøgle.',
'CONSUMER_KEY_ce7a07'	=>	'Forbruger nøgle',
'INPUT_APPLICATION_CONSUMER_SECRET_5fe0d8'	=>	'Indtast applikations forbruger hemmelighed.',
'CONSUMER_SECRET_2ce81a'	=>	'Forbruger hemmelighed',
'SELECT_HOW_THE_REGISTRATION_BUTTON_IS_DISPLAYED_10fb21'	=>	'Vælg hvordan registreringsknappen vises.',
'INPUT_APPLICATION_CLIENT_ID_81b124'	=>	'Indtast applikations klient id.',
'CLIENT_ID_76525f'	=>	'Klient ID',
'INPUT_APPLICATION_CLIENT_SECRET_3dea79'	=>	'Indtast applikations klient hemmelighed.',
'CLIENT_SECRET_734082'	=>	'Klient hemmelighed',
'INPUT_APPLICATION_ID_a1250d'	=>	'Indtast applikations id.',
'APPLICATION_ID_e500e9'	=>	'Applikations ID',
'INPUT_APPLICATION_SECRET_KEY_98c733'	=>	'Indtast applikations hemmelig nøgle.',
'SELECT_HOW_THIS_REGISTRATION_BUTTON_IS_DISPLAYED_f61498'	=>	'Vælg hvordan denne registreringsknap vises.',
'ENABLE_OR_DISABLE_THIS_THIS_ALLOWS_QUICK_ENABLE_OR_f0a61b'	=>	'Aktiver eller deaktiver dette. Dette tillader hurtig de- aller aktivering uden at skulle rydde parametrene.',
'INPUT_WEB_API_KEY_26de4a'	=>	'Indtast web api nøgle.',
'API_KEY_d876ff'	=>	'API nøgle',
'INPUT_APPLICATION_KEY_0d07cc'	=>	'Indtast applikations nøgle.',
'KEY_897356'	=>	'Nøgle',
'ENABLE_OR_DISABLE_SANDBOX_USAGE_SANDBOX_URL_ENDPOI_4ff8cb'	=>	'Aktiver eller deaktiver sandkasse anvendelse. Sandkasse URL slutpunkter er forskellige fra live. Hvis du tester med sandkasse detaljer, så skal dette være aktiveret.',
'SANDBOX_2652ee'	=>	'Sandkasse',
'INPUT_APPLICATION_API_KEY_258382'	=>	'Indtast applikations api nøgle.',
'API_ID_17d66f'	=>	'API ID',
'INPUT_APPLICATION_API_SECRET_e15606'	=>	'Indtast applikations api hemmelighed',
'API_SECRET_1ddec0'	=>	'API hemmelighed',
'YOUR_FACEBOOK_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZE_c1c65b'	=>	'Dit Facebook ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'FACEBOOK_ID_4336c5'	=>	'Facebook ID',
'YOUR_TWITTER_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_21c14d'	=>	'Dit Twitter ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'TWITTER_ID_b80446'	=>	'Twitter ID',
'YOUR_GOOGLE_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__8d7e0a'	=>	'Dit Google ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'GOOGLE_ID_81d3e3'	=>	'Google ID',
'YOUR_LINKEDIN_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZE_06e793'	=>	'Dit Linkedin ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'LINKEDIN_ID_1a58fc'	=>	'LinkedIn ID',
'YOUR_WINDOWS_LIVE_ID_ALLOWING_API_CALLS_IF_UNAUTHO_d6d424'	=>	'Dit Windows Live ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'WINDOWS_LIVE_ID_8b94b8'	=>	'Windows Live ID',
'YOUR_INSTAGRAM_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_c8826b'	=>	'Dit Instagram ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'INSTAGRAM_ID_c3820e'	=>	'Instagram ID',
'YOUR_FOURSQUARE_ID_ALLOWING_API_CALLS_IF_UNAUTHORI_178cb0'	=>	'Dit Foursquare ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'FOURSQUARE_ID_b0f614'	=>	'Foursquare ID',
'YOUR_GITHUB_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__4c4eba'	=>	'Dit Github ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'GITHUB_ID_2b7b73'	=>	'GitHub ID',
'YOUR_VKONTAKTE_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_757fdb'	=>	'Dit VKontakte ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'VKONTAKTE_ID_a9b255'	=>	'VKontakte ID',
'YOUR_STEAM_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_O_d8e5a6'	=>	'Dit Steam ID der tillader API kald; Hvis ikke-autoriseret, så vil kun offentlige kald validere.',
'STEAM_ID_00d10b'	=>	'Steam ID',
'YOUR_REDDIT_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__4b32d6'	=>	'Dit Reddit ID der tillader API kald; hvis ikke autoriseret vil kun offentlige kald valideres.',
'REDDIT_ID_811839'	=>	'Reddit ID',
'YOUR_TWITCH_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__6b312b'	=>	'Dit Twitch ID der tillader API kald; hvis ikke autoriseret, så vil kun offentlige kald blive valideret.',
'TWITCH_ID_9e5063'	=>	'Twitch ID',
'YOUR_STACK_EXCHANGE_ID_ALLOWING_API_CALLS_IF_UNAUT_480ac2'	=>	'DIt Stack Exchange ID der tillader API kald; hvis ikke autoriseret, så vil kun offentlige kald blive valideret.',
'STACK_EXCHANGE_ID_c1859a'	=>	'Stack Exchange ID',
'YOUR_PINTEREST_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_f4af23'	=>	'Dit Pinterest ID der tillader API kald; hvis ikke autoriseret, så vil kun offentlige kald blive valideret.',
'PINTEREST_ID_cffb54'	=>	'Pinterest ID',
'YOUR_AMAZON_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__a3a895'	=>	'Dit Amazon ID der tillader API kald; hvis ikke autoriseret, så vil kun offentlige kald blive valideret.',
'AMAZON_ID_d5694c'	=>	'Amazon ID',
'YOUR_YAHOO_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED_O_0f4702'	=>	'Dit Yahoo ID der tillader API kald; hvis ikke autoriseret, så vil kun offentlige kald blive valideret.',
'YAHOO_ID_59fc7f'	=>	'Yahoo ID',
'YOUR_PAYPAL_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__bacd9d'	=>	'Dit PayPal ID der tillader API kald; hvis ikke autoriseret, så vil kun offentlige kald blive valideret.',
'PAYPAL_ID_4a3287'	=>	'PayPal ID',
'YOUR_DISQUS_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__2954fe'	=>	'Dit Disqus ID der tillader API kald; hvis ikke autoriseret, så vil kun offentlige kald blive valideret.',
'DISQUS_ID_614174'	=>	'Disqus ID',
'YOUR_WORDPRESS_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZ_4776cc'	=>	'Dit WordPress ID der tillader API kald; hvis ikke autoriseret, så vil kun offentlige kald blive valideret.',
'WORDPRESS_ID_9d3a7b'	=>	'WordPress ID',
'YOUR_MEETUP_ID_ALLOWING_API_CALLS_IF_UNAUTHORIZED__29b49f'	=>	'Dit Meetup ID der tillader API kald; hvis ikke autoriseret, så vil kun offentlige kald blive valideret.',
'MEETUP_ID_629f66'	=>	'Meetup ID',
// 12 language strings from file plug_cbconnect/component.cbconnect.php
'PROVIDER_NOT_AVAILABLE'	=>	'[provider] er ikke tilgængelig.',
'PROVIDER_FAILED_TO_AUTHENTICATE'	=>	'[provider] kunne ikke godkende. Fejl: [error]',
'PROVIDER_PROFILE_MISSING'	=>	'[provider] profil kunne ikke findes.',
'LINKING_FOR_PROVIDER_NOT_PERMITTED'	=>	'Linkning for [provider] er ikke tilladt.',
'PROVIDER_ALREADY_LINKED'	=>	'[provider] konto allerede linket til en anden bruger.',
'PROVIDER_FAILED_TO_LINK'	=>	'[provider] konto kunne ikke linkes. Fejl: [error]',
'PROVIDER_LINKED_SUCCESSFULLY'	=>	'[provider] konto linket!',
'ALREADY_LINKED_TO_PROVIDER'	=>	'Du er allerede linket til en [provider] konto.',
'SIGN_UP_WITH_PROVIDER_NOT_PERMITTED'	=>	'Registrering med [provider] er ikke tilladt.',
'PROVIDER_SIGN_UP_INCOMPLETE'	=>	'Din [provider] registrering er ufuldstændig. Udfyld venligst følgende.',
'SIGN_UP_WITH_PROVIDER_FAILED'	=>	'Registrering med [provider] fejlede. Fejl: [error]',
'SIGN_UP_INCOMPLETE_c37e7a'	=>	'Registrering ufuldstændig',
// 17 language strings from file plug_cbconnect/library/CBConnect.php
'GOOGLE_8b36e9'	=>	'Google',
'WINDOWS_LIVE_37160d'	=>	'Windows Live',
'INSTAGRAM_55f015'	=>	'Instagram',
'FOURSQUARE_938a83'	=>	'Foursquare',
'GITHUB_d3b7c9'	=>	'GitHub',
'VKONTAKTE_c39fa7'	=>	'VKontakte',
'STEAM_4db456'	=>	'Steam',
'REDDIT_b632c5'	=>	'Reddit',
'TWITCH_6a057e'	=>	'Twitch',
'STACK_EXCHANGE_28d479'	=>	'Stack Exchange',
'PINTEREST_86709a'	=>	'Pinterest',
'AMAZON_b3b3a6'	=>	'Amazon',
'YAHOO_1334b6'	=>	'Yahoo',
'PAYPAL_ad69e7'	=>	'PayPal',
'DISQUS_2df055'	=>	'Disqus',
'WORDPRESS_fde316'	=>	'WordPress',
'MEETUP_b30887'	=>	'Meetup',
// 6 language strings from file plug_cbconnect/library/Connect.php
'LINK_YOUR_PROVIDER_ACCOUNT'	=>	'Link din [provider] konto',
'LINK_WITH_PROVIDER'	=>	'Link med [provider]',
'SIGN_UP_WITH_PROVIDER'	=>	'Registrer med [provider]',
'SIGN_UP_WITH_YOUR_PROVIDER_ACCOUNT'	=>	'Registrer med din [provider] konto',
'SIGN_IN_WITH_PROVIDER'	=>	'Log på med [provider]',
'SIGN_IN_WITH_YOUR_PROVIDER_ACCOUNT'	=>	'Log på med din [provider] konto',
// 3 language strings from file plug_cbconnect/library/Provider/AmazonProvider.php
'FAILED_EXCHANGE_CODE_ERROR'	=>	'Kunne ikke udveksle kode. Fejl: [error]',
'FAILED_TO_RETRIEVE_ACCESS_TOKEN_275f75'	=>	'Kunne ikke hente adgangs token.',
'FAILED_API_REQUEST_ERROR'	=>	'Fejlet API forespørgsel [api]. Fejl: [error]',
// 1 language strings from file plug_cbconnect/library/Provider/SteamProvider.php
'FAILED_TO_AUTHENTICATE_IDENTITY_d02393'	=>	'Kunne ikke godkende identitet.',
// 4 language strings from file plug_cbconnect/library/Provider/TwitterProvider.php
'FAILED_EXCHANGE_TOKEN_ERROR'	=>	'Kunne ikke udveksle token. Fejl: [error]',
'FAILED_REQUEST_TOKEN_ERROR'	=>	'Kunne ikke forespørge token. Fejl: [error]',
'CALLBACK_FAILED_TO_CONFIRM_bb8d55'	=>	'Tilbagekald kunne ikke bekræfte.',
'FAILED_TO_REQUEST_CALLBACK_2a0309'	=>	'Kunne ikke forespørge tilbagekald.',
);
