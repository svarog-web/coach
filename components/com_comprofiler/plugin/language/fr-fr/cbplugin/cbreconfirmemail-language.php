<?php
/**
* Community Builder (TM) cbreconfirmemail French (France) language file Frontend
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
'CHANGED_820dbd'	=>	'Modifié',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_a4c750'	=>	'Saisir le message de substitution pris en charge qui s\'affichera après avoir changé l\'adresse de courriel. Laissez ce champ vide pour n\'afficher aucun message.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_AND_REQUIRES_RECONF_498289'	=>	'Votre adresse courriel a changé et nécessite une re-confirmation. Veuillez vérifier votre nouvelle adresse de courriel pour votre courriel de confirmation.',
'NOTIFICATION_96d008'	=>	'Notification',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_NAME_TO_BE_SEN_941d81'	=>	'Saisir le message de substitution pris en charge pour le nom à envoyer avec tous les courriels de re-confirmation (par exemple Mon Génial CB Site !). Si laissé vide ce sera par défaut le nom d\'utilisateurs. Si spécifié un Nom replyto sera ajoutée comme nom des utilisateurs.',
'FROM_NAME_4a4a8f'	=>	'à partir du Nom',
'INPUT_A_SUBSTITUTION_SUPPORTED_FROM_ADDRESS_TO_SEN_f8bd17'	=>	'Saisir le message de substitution pris en charge pour l\'adresse qui enverra tous les courriels de confirmation (par exemple general@domain.com). Si laissé vide ce sera par défaut le nom d\'utilisateurs. Si spécifié un Nom replyto sera ajoutée comme nom des utilisateurs.',
'FROM_ADDRESS_a5ab7d'	=>	'à partir de l\'adresse',
'INPUT_SUBSTITUTION_SUPPORTED_RECONFIRM_EMAIL_SUBJE_65c12d'	=>	'Saisir le message de substitution pris en charge pour l"objet du courriel de re-confirmation.',
'YOUR_EMAIL_ADDRESS_HAS_CHANGED_e5b542'	=>	'Votre adresse de courriel a changé',
'INPUT_HTML_AND_SUBSTITUTION_SUPPORTED_RECONFIRM_EM_ca445d'	=>	'Mettez du HTML et le message de substitution prise en charge pour le corps du lien de confirmation par courriel.
Supporte la substitution [reconfirm] pour afficher le lien de confirmation.
De plus [old_email] peut-être utilisé pour afficher l\'ancienne adresse de courriel ou [new_email] pour afficher la nouvelle adresse. ',
'BODY_ac101b'	=>	'Corps',
'THE_EMAIL_ADDRESS_ATTACHED_TO_YOUR_ACCOUNT_USERNAM_5c3f69'	=>	'L\'adresse de courriel attaché à votre compte [username] a été changée pour celle-ci [new_email] et demande votre confirmation.<br><br>Vous pouvez confirmer cette adresse en cliquant sur le lien suivant:<br><a href="[reconfirm]">[reconfirm]</a><br><br>Si ce changement a été fait par erreur veuillez contacter l\'administrateur ou annulez en  <a href="[cancel]">cliquant ici</a>.',
'INPUT_A_SUBSTITUTION_SUPPORTED_CC_ADDRESS_EG_EMAIL_e48bb8'	=>	'Saisir le message de substitution pris en charge pour les adresses en Copie Cachée Intégrale (par exemple [email]); plusieurs adresses sont supportées en liste si séparée par des virgules (Par exemple email1@domain.com, email2@domain.com, email3@domain.com).',
'CC_ADDRESS_b6327b'	=>	'Adresse CC',
'INPUT_A_SUBSTITUTION_SUPPORTED_BCC_ADDRESS_EG_EMAI_417251'	=>	'Saisir le message de substitution pris en charge pour les adresses en Copie Cachée (par exemple [email]); plusieurs adresses sont supportées en liste si séparée par des virgules (Par exemple email1@domain.com, email2@domain.com, email3@domain.com).',
'BCC_ADDRESS_33b728'	=>	'Adresse CCI',
'INPUT_A_SUBSTITUTION_SUPPORTED_ATTACHMENT_ADDRESS__14d21b'	=>	'Saisir le message de substitution pris en charge pour les adresses de pièce jointe (par exemple [cb_myfilel]); plusieurs adresses sont supportées en liste si séparée par des virgules (Par exemple /home/username/public_html/images/file1.zip, http://www.domain.com/file3.zip).',
'ATTACHMENT_e9cb21'	=>	'Pièce jointe',
'RECONFIRMED_e748a2'	=>	'Reconfirmé',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_76c4af'	=>	'Saisir le message de substitution pris en charge pour les URLs pour rediriger après chaque re-confirmation faite avec succès. Si laissé vide aucune redirection ne sera effectuée.',
'REDIRECT_4202ef'	=>	'Redirection',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_96a926'	=>	'Saisir le message de substitution pris en charge qui s\'affichera après le succès de la confirmation.',
'NEW_EMAIL_ADDRESS_CONFIRMED_SUCCESSFULLY_1a901d'	=>	'Nouvelle adresse de courriel confirmée avec succès !',
'CANCELLED_a149e8'	=>	'Annulé',
'INPUT_SUBSTITUTION_SUPPORTED_URL_TO_REDIRECT_TO_AF_e82b77'	=>	'Saisir le message de substitution pris en charge pour les URL de redirection après l\'annulation réussie du changement d\'adresse de courriel. Si laissé vide aucune redirection ne sera faite.',
'INPUT_SUBSTITUTION_SUPPORTED_MESSAGE_DISPLAYED_AFT_73710e'	=>	'Saisir le message de substitution pris en charge pour le message affiché après l\'annulation réussie du changement d\'adresse de courriel. ',
'EMAIL_ADDRESS_CHANGE_CANCELLED_SUCCESSFULLY_167e65'	=>	'Changement d\'adresse courriel annulé avec succès !',
// 6 language strings from file plug_cbreconfirmemail/cbreconfirmemail.php
'CONFIRM_CODE_MISSING_761a29'	=>	'Code de confirmation manquant.',
'USER_NOT_ASSOCIATED_WITH_CONFIRM_CODE_220850'	=>	'L\'utilisateur n\'est pas associé au code de confirmation.',
'CONFIRM_CODE_IS_NOT_VALID_b7f5f7'	=>	'Code de confirmation non valide.',
'FAILED_CANCEL_EMAIL_CHANGE'	=>	'Impossible d\'annuler le changement d\'adresse de courriel! Erreur: [error]',
'FAILED_RECONFIRM_EMAIL'	=>	'Impossible de confirmer la nouvelle adresse de courriel ! Erreur: [error]',
'EMAIL_ADDRESS_HAS_ALREADY_BEEN_CONFIRMED_42a2cf'	=>	'L\'adresse de courriel a déjà été confirmée.',
// 1 language strings from file plug_cbreconfirmemail/xml/controllers/frontcontroller.xml
'EMAILS_9790b7'	=>	'Courriels',
);
