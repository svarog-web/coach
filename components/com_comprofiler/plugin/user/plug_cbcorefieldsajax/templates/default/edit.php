<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_framework;

static $loaded	=	0;

if ( ! $loaded++ ) {
	cbValidator::loadValidation();
	initToolTip();
}

$return			=	'<div class="cbAjaxContainer cbAjaxContainerEdit cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
				.		'<form action="' . $_CB_framework->viewUrl( 'fieldclass', true, array( 'field' => $field->get( 'name' ), 'function' => 'ajax_save', 'user' => (int) $user->get( 'id' ), 'reason' => $reason ), 'raw' ) .'" name="cbAjaxForm" enctype="multipart/form-data" method="post" class="cbAjaxForm cbValidation cb_form form-auto">'
				.			'<div class="cbAjaxInput form-group cb_form_line clearfix">'
				.				'<div class="cb_field">'
				.					$formatted
				.				'</div>'
				.			'</div>'
				.			'<div class="cbAjaxButtons form-group cb_form_line clearfix">'
				.				'<input type="submit" class="cbAjaxSubmit btn btn-primary" value="' . htmlspecialchars( CBTxt::T( 'Update' ) ) . '" />'
				.				' <input type="button" class="cbAjaxCancel btn btn-default" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" />'
				.			'</div>'
				.			cbGetSpoofInputTag( 'fieldclass' )
				.			cbGetRegAntiSpamInputTag()
				.			$headers
				.		'</form>'
				.	'</div>';

return $return;