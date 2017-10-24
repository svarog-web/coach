<?php
/**
* Community Builder (TM) cbactivity French (France) language file Administration
* @version $Id:$
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

/**
* WARNING:
* Do not make changes to this file as it will be over-written when you upgrade CB.
* To localize you need to create your own CB language plugin and make changes there.
*/

defined('CBLIB') or die();

return	array(
// 1 language strings from file plug_cbactivity/cbactivity.xml
'CLEANUP_8c6384'	=>	'Nettoyage',
// 2 language strings from file plug_cbactivity/xml/models/model.activity.xml
'ENABLE_2faec1'	=>	'Activer',
'DISABLE_bcfacc'	=>	'Désactiver',
// 10 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.editactivityactionrow.xml
'ACTION_004bf6'	=>	'Action',
'SELECT_PUBLISH_STATUS_OF_THIS_ACTION_UNPUBLISHED_A_cd8370'	=>	'La sélection publie le statut de cette action.  L\'action non publiée de s\'affichera pas.',
'INPUT_THE_ACTION_ACTION_WILL_DETERMINE_THE_USERS_S_0cdf85'	=>	'Entrez l\'action.  L\'action déterminera l\'intention de statut d\'utilisateur (ex sentiments, jeux, repas, ... )',
'OPTIONALLY_INPUT_THE_ACTION_TITLE_THE_TITLE_IS_DIS_7eeb57'	=>	'Saisissez facultativement le titre de l\'action.  Le titre est montrer comme le titre du statut préfixant le message d\'action de statut d\'utilisateurs (par exemple le sentiment, le jeu, le repas, joué, etc ...)',
'OPTIONALLY_INPUT_THE_ACTION_DESCRIPTION_THE_DESCRI_0503d3'	=>	'Si vous le souhaitez, mettez la description de l\'action. Cette description sera affichée dans le champ d\'entrée vide en temps que message de statut (par exemple : Comment allez-vous ? Ou bien Comment vous sentez vous ? etc... )',
'PREVIEW_31fde7'	=>	'Aperçu',
'OPTIONALLY_SELECT_THE_IMAGE_FILE_TO_BE_USED_AS_THE_a7ff9a'	=>	'Choisissez facultativement le dossier d\'image qui doit être utilisé comme source d’icône.  L’icône est affichée à droite du message de statut d\'action.  (par exemple l\'icône de message de titre).  ',
'ICON_817434'	=>	'Icône',
'OPTIONALLY_INPUT_THE_CSS_CLASS_TO_RENDER_AN_ICON_D_4875fd'	=>	'Si vous le souhaitez vous pouvez mettre une classe CSS pour modifier l\'affichage de l\'icône.',
'SELECT_THE_ORDERING_OF_THIS_ACTION_ORDERING_DETERM_6e7262'	=>	'Choisissez l\'ordre de cette action.  L\'ordre déterminera dans quel ordre l\'action sera montrée dans le défilement.',
// 10 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.editactivitycommentrow.xml
'COMMENT_0be840'	=>	'Commentaire',
'INPUT_THE_COMMENT_MESSAGE_4b34b1'	=>	'Entrez le message de commentaire',
'INPUT_THE_TYPE_OF_COMMENT_35a388'	=>	'Entrez le type de commentaire',
'OPTIONALLY_INPUT_THE_COMMENT_SUBTYPE_b9c873'	=>	'Saisissez facultativement le sous-type de commentaire',
'SUBTYPE_423366'	=>	'Sous-Type',
'OPTIONALLY_INPUT_THE_COMMENT_ITEM_ID_728c4f'	=>	'Si vous le souhaitez vous pouvez l\'ID de l\'élément de commentaire.',
'ITEM_7d74f3'	=>	'Elément',
'OPTIONALLY_INPUT_THE_COMMENT_PARENT_ID_c20362'	=>	'Si vous le souhaitez vous pouvez mettre l\'ID du commentaire parent.',
'PARENT_302690'	=>	'Parent',
'INPUT_OWNER_AS_SINGLE_INTEGER_USERID_169965'	=>	'Entrez le numéro d\'id du propriétaire.',
// 6 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.editactivityemoterow.xml
'EMOTE_4b72cd'	=>	'Sentiment',
'SELECT_PUBLISH_STATUS_OF_THIS_EMOTE_UNPUBLISHED_EM_f5fb0f'	=>	'Sélectionner le statut de publication de ce sentiment.  Le sentiment non publié ne sera pas visible.',
'INPUT_THE_EMOTE_EMOTE_IS_THE_EMOJI_CODE_USED_TO_RE_23b57a'	=>	'Mettez l\'emote. L\'emote est le code emoji qui sera utilisé pour affiché l’émoticône (par exemple triste sera utilisé pour :triste:).',
'SELECT_THE_IMAGE_FILE_TO_BE_USED_AS_THE_EMOTE_ICON_8d84cc'	=>	'Sélectionner le dossier d\'image qui dont être utilisé comme source d\'icône sentiment.',
'INPUT_THE_CSS_CLASS_TO_RENDER_AN_ICON_DISPLAY_a0bf41'	=>	'Entrez la classe CSS pour le rendu d\'affichage d\'icône.',
'SELECT_THE_ORDERING_OF_THIS_EMOTE_ORDERING_DETERMI_a5705f'	=>	'Sélectionnez l\'ordre de cette émoticône. Le classement détermine l\'ordre dans lequel les émoticônes seront affichées dans la liste déroulante.',
// 4 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.editactivitylocationrow.xml
'SELECT_PUBLISH_STATUS_OF_THIS_LOCATION_UNPUBLISHED_3f4131'	=>	'La sélection publie le statut de cette emplacement.  L\'action non publiée ne s\'affichera pas.',
'INPUT_THE_LOCATION_LOCATION_WILL_DETERMINE_THE_USE_f17257'	=>	'Saisissez l\'emplacement. L\'emplacement déterminera la relation d\'emplacement d\'utilisateurs (par exemple. À, Dans, Allant À, etc ...).',
'OPTIONALLY_INPUT_THE_LOCATION_TITLE_THE_TITLE_IS_D_7ac280'	=>	'Saisissez facultativement l\'emplacement du titre.  Le titre est montré comme préfixe à l\'emplacement (ex À, dans, allant à, arrivant, etc ...)',
'SELECT_THE_ORDERING_OF_THIS_LOCATION_ORDERING_DETE_fcb76c'	=>	'Sélectionnez le classement pour le lieu. Le classement détermine l\'ordre dans lequel les lieux seront affichées dans la liste déroulante.',
// 32 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.editactivityrow.xml
'OPTIONALLY_INPUT_THE_SUBSTITUTION_SUPPORTED_TITLE__f856ce'	=>	'Saisissez facultativement la substitution du titre supporté.  Le titre est montré à droite du nom d\'utilisateur.',
'OPTIONALLY_INPUT_THE_SUBSTITUTION_SUPPORTED_MESSAG_384409'	=>	'Saisissez facultativement la substitution du message supporté.  Le message est montré en dessous de l\'entête d\'activité',
'INPUT_THE_TYPE_OF_ACTIVITY_f171fb'	=>	'Saisissez le type d\'activité.',
'OPTIONALLY_INPUT_THE_ACTIVITY_SUBTYPE_b7b145'	=>	'Optionnellement entrez le sous type d\'activité.',
'OPTIONALLY_INPUT_THE_ACTIVITY_ITEM_ID_8eaf3f'	=>	'Si vous le souhaitez mettez l\'ID de l\'élément d\'activité.',
'OPTIONALLY_INPUT_THE_ACTIVITY_PARENT_ID_a482bf'	=>	'Si vous le souhaitez mettez l\'ID de l\'élément d\'activité parent.',
'SELECT_THE_TYPE_OF_THIS_LINK_TYPE_DETERMINES_HOW_T_c9989a'	=>	'Choisissez le type de ce lien.  Le type détermine comment le lien sera affiché.',
'INPUT_THE_LINK_URL_FOR_THIS_LINK_5ccf15'	=>	'Saisissez l\'URL pour ce lien.',
'ENABLE_OR_DISABLE_INTERNAL_LINK_INTERNAL_LINKS_WIL_8fced1'	=>	'Permettez ou interdisez les liens interne.  Les liens internes supprimeront l\'affichage de l\'URL.',
'OPTIONALLY_INPUT_TEXT_FOR_THIS_LINK_THIS_WILL_BE_D_c8c887'	=>	'Facultativement entre un texte pour ce lien.  Il sera montré à la place de L\'URL.',
'OPTIONALLY_INPUT_A_TITLE_FOR_THIS_LINK_4125ea'	=>	'Optionnellement entrez un titre pour ce lien',
'OPTIONALLY_INPUT_A_DESCRIPTION_FOR_THIS_LINK_e2a66c'	=>	'Optionnellement entrez une description pour ce lien.',
'ENABLE_OR_DISABLE_DISPLAY_OF_URL_THUMBNAIL_1901f8'	=>	'Active ou désactive l\'affichage de l\'url de la miniature',
'THUMBNAIL_b7c161'	=>	'Miniature',
'MEDIA_3b5635'	=>	'Média',
'OPTIONALLY_INPUT_A_CUSTOM_MEDIA_DISPLAY_76cfc7'	=>	'Saisissez facultativement l\'affichage personnalisé du média',
'INPUT_THE_MEDIA_URL_EG_URL_TO_AN_IMAGE_VIDEO_OR_AU_4403e1'	=>	'Mettez l\'URL du média (par exemple l\'URL vers une image, une vidéo, un fichier audio)',
'INPUT_THE_MEDIA_MIMETYPE_EG_IMAGEJPEG_VIDEOYOUTUBE_e7c636'	=>	'Mettez le type d\'extension media (par exemple image/jpeg, video/youtube). Cela est nécessaire pour les élements vidéo ou audio.',
'MIMETYPE_876791'	=>	'Type Mime',
'INPUT_THE_MEDIA_EXTENSION_EG_JPG_21cf65'	=>	'Entrez l\'extension du média (ex jpg).',
'EXTENSION_63e4e9'	=>	'Extension',
'OPTIONALLY_SELECT_AN_ACTION_FOR_THIS_STATUS_20508c'	=>	'Facultativement entrer une action pour ce statut',
'SELECT_ACTION_71cae1'	=>	'- Sélectionnez une action -',
'INPUT_A_MESSAGE_FOR_THIS_ACTION_dcb2aa'	=>	'Entrez un message pour cette action.',
'OPTIONALLY_SELECT_THE_EMOTE_FOR_THIS_ACTION_af21d0'	=>	'Saisissez facultativement l\'émotion de cette action.',
'SELECT_EMOTE_7a3957'	=>	'- Sélectionner l\'émotion -',
'OPTIONALLY_SELECT_A_LOCATION_FOR_THIS_STATUS_687450'	=>	'Choisissez facultativement un endroit pour ce statut.',
'SELECT_LOCATION_71f2c4'	=>	'- Sélectionnez l\'endroit - ',
'INPUT_THE_PLACE_FOR_THIS_LOCATION_20965b'	=>	'Sélectionnez l\'endroit pour cet emplacement.',
'PLACE_7b9cf0'	=>	'Endroit',
'OPTIONALLY_INPUT_THE_ADDRESS_FOR_THIS_LOCATION_17ee38'	=>	'Saisissez facultativement l\'adresse pour cet emplacement.',
'ADDRESS_dd7bf2'	=>	'Adresse',
// 6 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.editactivitytagrow.xml
'TAG_c10105'	=>	'Etiquette',
'INPUT_THE_TAG_USER_AS_A_SINGLE_INTEGER_USERID_OR_A_85d648'	=>	'Mettez la balise utilisateur soit en nombre entier (user_id) ou un nom',
'INPUT_THE_TAG_ITEM_TYPE_EG_ACTIVITY_OR_COMMENT_9c3cbd'	=>	'Mettez la balise du type d\'élément (par exemple activité ou commentaire).',
'OPTIONALLY_INPUT_THE_TAG_ITEM_SUBTYPE_d4be25'	=>	'Saisissez facultativement le sous type d\'étiquette de l\'article',
'INPUT_THE_TAG_ITEM_ID_TYPICALLY_THIS_IS_THE_ACTIVI_86acb5'	=>	'Mettez la balise identifiant l\'élément. Typiquement il s\'agit de l\'ID de l\'élément ou de l\'ID du comentaire.',
'OPTIONALLY_INPUT_THE_TAG_PARENT_ID_64ed0b'	=>	'Si vous le souhaitez mettez la balise ID du parent.',
// 2 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.edithiddenactivityrow.xml
'INPUT_THE_HIDDEN_ITEM_TYPE_EG_ACTIVITY_OR_COMMENT_f925fb'	=>	'Mettez le type d\'élément caché (par exemple activité ou commentaire).',
'INPUT_THE_HIDDEN_ITEM_ID_TYPICALLY_THIS_IS_THE_ACT_0e29ae'	=>	'Mettez l\'ID du type d\'élément caché. Typiquement il s\'agit de l\'ID de l\'activité ou du commentaire.',
// 3 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.showactivityactionrows.xml
'SEARCH_ACTIONS_1e5045'	=>	'Actions de recherche...',
'ORDERING_ASCENDING_ee242f'	=>	'Ordre ascendant',
'ORDERING_DESCENDING_574564'	=>	'Ordre descendant',
// 6 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.showactivitycommentrows.xml
'SEARCH_COMMENTS_8c53f9'	=>	'Commentaires de recherche...',
'SELECT_SUBTYPE_4e4129'	=>	'- Sélectionnez un sous-type -',
'FROM_5da618'	=>	'Depuis',
'TO_e12167'	=>	'Vers',
'OWNER_ASCENDING_edd599'	=>	'Propriétaire croissant',
'OWNER_DESCENDING_c27319'	=>	'Propriétaire décroissant',
// 1 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.showactivityemoterows.xml
'SEARCH_EMOTES_564024'	=>	'recherche les émotions...',
// 1 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.showactivitylocationrows.xml
'SEARCH_LOCATIONS_4114f7'	=>	'Emplacements de recherche ...',
// 1 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.showactivityrows.xml
'SEARCH_ACTIVITY_81528c'	=>	'Activités de recherches ...',
// 1 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.showactivitytagrows.xml
'SEARCH_TAGS_b11ecb'	=>	'Etiquette de recherche ...',
// 3 language strings from file plug_cbactivity/xml/views/view.com_comprofiler.showhiddenactivityrows.xml
'SEARCH_HIDDEN_0a7e78'	=>	'Recherche cachée...',
'ID_ASCENDING_ee74eb'	=>	'ID croissant',
'ID_DESCENDING_8ab4db'	=>	'ID décroissant',
);
