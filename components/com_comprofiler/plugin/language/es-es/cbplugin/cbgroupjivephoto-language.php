<?php
/**
* Community Builder (TM) cbgroupjivephoto Spanish (Spain) language file Frontend
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
'INPUT_NUMBER_OF_PHOTOS_EACH_INDIVIDUAL_USER_IS_LIM_11b810'	=>	'Input number of photos each individual user is limited to creating per group. If blank allow unlimited photos. Moderators and group owners are exempt from this configuration.',
'ENABLE_OR_DISABLE_USAGE_OF_CAPTCHA_ON_GROUP_PHOTOS_0ea9f4'	=>	'Enable or disable usage of captcha on group photos. Requires latest CB AntiSpam to be installed and published. Moderators are exempt from this configuration.',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_ALWAYS_BE_RESAMPL_b7b0e2'	=>	'Elija si las imágenes subidas deben ser siempre remuestreadas . Añade seguridad adicional, pero las animaciones sólo se mantienen cuando se utiliza ImageMagick .',
'INPUT_THE_MAXIMUM_HEIGHT_IN_PIXELS_THAT_THE_IMAGE__e0ce78'	=>	'Escriba el maximo alto en pixeles para que la imagen sea redimensionada.',
'INPUT_THE_MAXIMUM_WIDTH_IN_PIXELS_THAT_THE_IMAGE_W_75174f'	=>	'Escriba el maximo ancho en pixeles para que la imagen sea redimensionada.',
'INPUT_THE_MAXIMUM_THUMBNAIL_HEIGHT_IN_PIXELS_THAT__9d2b57'	=>	'Entrada de la altura máxima miniatura en píxeles en que la imagen será redimensionada',
'INPUT_THE_MAXIMUM_THUMBNAIL_WIDTH_IN_PIXELS_THAT_T_d159f4'	=>	'Escriba el ancho máxima de la miniatura en píxeles que la imagen será redimensionada',
'CHOOSE_IF_IMAGES_UPLOADED_SHOULD_MAINTAIN_THEIR_AS_d23fff'	=>	'Elija si las imágenes subidas deben mantener su relación de aspecto al cambiar el tamaño . Si se establece en No la imagen siempre será redimensionado a la anchura máxima especificada y altura. Si se establece en Sí, la relación de aspecto se mantendrá en lo posible dentro de la anchura y la altura máxima . Si se establece en Sí con Recorte de la imagen siempre cambiar el tamaño a la anchura y la altura máxima especificada dentro de la relación de aspecto y recortar cualquier desbordamiento ; esto es útil para mantener imágenes al cuadrado.',
'INPUT_THE_MINIMUM_IMAGE_FILE_SIZE_IN_KBS_30eae6'	=>	'Input the minimum image file size in KBs.',
'INPUT_THE_MAXIMUM_IMAGE_FILE_SIZE_IN_KBS_SET_TO_0__f73680'	=>	'Input the maximum image file size in KBs. Set to 0 for no limit.',
'UPLOAD_OF_NEW_PHOTO_1831ae'	=>	'Upload of new photo',
'NEW_PHOTO_REQUIRES_APPROVAL_d212d4'	=>	'New photo requires approval',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Activar o desactivar el uso de paginación.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_RO_61ece3'	=>	'Indique el límite por página. El límite por página determina cuantas filas son mostradas por página. Si la paginación está desactivada esto aún puede ser usado para limitar el número de filas mostradas.',
'ENABLE_OR_DISABLE_USAGE_OF_SEARCH_ON_ROWS_cf0975'	=>	'Activar o desactivar el uso de la búsqueda en las filas.',
'SELECT_THE_ROW_ORDERING_30243c'	=>	'Seleccione el orden de las filas.',
'DATE_ASC_a5871f'	=>	'Fecha ASC',
'DATE_DESC_bcfc6d'	=>	'Fecha DESC',
'FILENAME_ASC_44f721'	=>	'Filename ASC',
'FILENAME_DESC_13d728'	=>	'Filename DESC',
// 31 language strings from file cbgroupjivephoto/component.cbgroupjivephoto.php
'GROUP_DOES_NOT_EXIST_df7d25'	=>	'Group does not exist.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_UPLOAD_A_c06972'	=>	'You do not have sufficient permissions to upload a photo in this group.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_EDIT_THI_6eccd3'	=>	'You do not have sufficient permissions to edit this photo.',
'SELECT_PUBLISH_STATE_OF_THIS_PHOTO_UNPUBLISHED_PHO_75ff3b'	=>	'Select publish state of this photo. Unpublished photos will not be visible to the public.',
'OPTIONALLY_INPUT_A_PHOTO_TITLE_TO_DISPLAY_INSTEAD__323e09'	=>	'Optionally input a photo title to display instead of filename.',
'SELECT_THE_PHOTO_TO_UPLOAD_8e29df'	=>	'Select the photo to upload.',
'GROUP_PHOTO_LIMITS_EXT'	=>	'Your photo must be of [ext] type.',
'GROUP_PHOTO_LIMITS_MIN'	=>	'Your photo should exceed [size].',
'GROUP_PHOTO_LIMITS_MAX'	=>	'Your photo should not exceed [size].',
'OPTIONALLY_INPUT_A_PHOTO_DESCRIPTION_d4c183'	=>	'Optionally input a photo description.',
'INPUT_THE_PHOTO_OWNER_ID_PHOTO_OWNER_DETERMINES_TH_eb7b03'	=>	'Input the photo owner id. Photo owner determines the creator of the photo specified as User ID.',
'GROUP_PHOTO_FAILED_TO_SAVE'	=>	'Photo failed to save! Error: [error]',
'GROUP_PHOTO_DATE_FORMAT'	=>	'M j, Y',
'NEW_GROUP_PHOTO_9ba416'	=>	'New group photo',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_429420'	=>	'[user] has uploaded the photo [photo] in the group [group]!',
'NEW_GROUP_PHOTO_AWAITING_APPROVAL_221466'	=>	'New group photo awaiting approval',
'USER_HAS_UPLOADED_THE_PHOTO_PHOTO_IN_THE_GROUP_GRO_e90ac6'	=>	'[user] has uploaded the photo [photo] in the group [group] and is awaiting approval!',
'PHOTO_UPLOADED_SUCCESSFULLY_AND_AWAITING_APPROVAL_492ec3'	=>	'Photo uploaded successfully and awaiting approval!',
'PHOTO_UPLOADED_SUCCESSFULLY_0b02f3'	=>	'Photo uploaded successfully!',
'PHOTO_SAVED_SUCCESSFULLY_726f86'	=>	'Photo saved successfully!',
'YOUR_PHOTO_IS_AWAITING_APPROVAL_d5f199'	=>	'Your photo is awaiting approval.',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_PUBLISH__dce5c6'	=>	'You do not have sufficient permissions to publish or unpublish this photo.',
'PHOTO_DOES_NOT_EXIST_cd6623'	=>	'Photo does not exist.',
'GROUP_PHOTO_STATE_FAILED_TO_SAVE'	=>	'Photo state failed to saved. Error: [error]',
'PHOTO_UPLOAD_REQUEST_ACCEPTED_bf6572'	=>	'Photo upload request accepted',
'YOUR_PHOTO_PHOTO_UPLOAD_REQUEST_IN_THE_GROUP_GROUP_9728ad'	=>	'Your photo [photo] upload request in the group [group] has been accepted!',
'PHOTO_STATE_SAVED_SUCCESSFULLY_2f8a03'	=>	'Photo state saved successfully!',
'YOU_DO_NOT_HAVE_SUFFICIENT_PERMISSIONS_TO_DELETE_T_8777b2'	=>	'You do not have sufficient permissions to delete this photo.',
'GROUP_PHOTO_FAILED_TO_DELETE'	=>	'Photo failed to delete. Error: [error]',
'PHOTO_DELETED_SUCCESSFULLY_a9f27f'	=>	'Photo deleted successfully!',
'YOU_DO_NOT_HAVE_ACCESS_TO_THIS_PHOTO_5ca855'	=>	'You do not have access to this photo.',
// 7 language strings from file cbgroupjivephoto/library/Table/PhotoTable.php
'OWNER_NOT_SPECIFIED_4e1454'	=>	'Owner not specified!',
'GROUP_NOT_SPECIFIED_70267b'	=>	'Group not specified!',
'GROUP_DOES_NOT_EXIST_adf2fd'	=>	'Group does not exist!',
'GROUP_PHOTO_UPLOAD_INVALID_EXT'	=>	'Invalid photo extension [ext]. Please upload only [exts]!',
'GROUP_PHOTO_UPLOAD_TOO_SMALL'	=>	'The photo is too small, the minimum is [size]!',
'GROUP_PHOTO_UPLOAD_TOO_LARGE'	=>	'The photo size exceeds the maximum of [size]!',
'PHOTO_NOT_SPECIFIED_dd1bfc'	=>	'Photo not specified!',
// 3 language strings from file cbgroupjivephoto/library/Trigger/AdminTrigger.php
'PHOTOS_5daaf2'	=>	'Fotos',
'ADD_NEW_PHOTO_TO_GROUP_2df00d'	=>	'Add New Photo to Group',
'CONFIGURATION_254f64'	=>	'Configuración',
// 6 language strings from file cbgroupjivephoto/library/Trigger/PhotoTrigger.php
'DISABLE_bcfacc'	=>	'Desactivar',
'ENABLE_2faec1'	=>	'Enable',
'ENABLE_WITH_APPROVAL_575b45'	=>	'Enable, with Approval',
'OPTIONALLY_ENABLE_OR_DISABLE_USAGE_OF_PHOTOS_GROUP_44b054'	=>	'Optionally enable or disable usage of photos. Group owner and group administrators are exempt from this configuration and can always upload photos. Note existing photos will still be accessible.',
'DONT_NOTIFY_3ea23f'	=>	'No Notificar',
'SEARCH_PHOTOS_e11345'	=>	'Buscar Fotos...',
// 2 language strings from file cbgroupjivephoto/templates/default/activity.php
'GROUP_PHOTO_ACTIVITY_TITLE'	=>	'uploaded a photo in [group]',
'ORIGINAL_0a52da'	=>	'Original',
// 6 language strings from file cbgroupjivephoto/templates/default/photo_edit.php
'EDIT_PHOTO_68ffc9'	=>	'Editar Foto',
'NEW_PHOTO_50a153'	=>	'Foto Nueva',
'PHOTO_c03d53'	=>	'Foto',
'DESCRIPTION_b5a7ad'	=>	'Descripción',
'UPDATE_PHOTO_89bc50'	=>	'Actualizar Foto',
'UPLOAD_PHOTO_05e477'	=>	'Subir Foto',
// 7 language strings from file cbgroupjivephoto/templates/default/photos.php
'GROUP_PHOTOS_COUNT'	=>	'%%COUNT%% Photo|%%COUNT%% Photos',
'AWAITING_APPROVAL_af6558'	=>	'Awaiting Approval',
'APPROVE_6f7351'	=>	'Aprobar',
'ARE_YOU_SURE_YOU_WANT_TO_UNPUBLISH_THIS_PHOTO_31f072'	=>	'Are you sure you want to unpublish this Photo?',
'ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PHOTO_4d3e2d'	=>	'Are you sure you want to delete this Photo?',
'NO_GROUP_PHOTO_SEARCH_RESULTS_FOUND_64adc0'	=>	'No group photo search results found.',
'THIS_GROUP_CURRENTLY_HAS_NO_PHOTOS_8939ef'	=>	'This group currently has no photos.',
);
