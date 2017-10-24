<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_framework;

static $loaded	=	0;

if ( ! $loaded++ ) {
	cbValidator::loadValidation();
	initToolTip();

	$_CB_framework->addJQueryPlugin( 'cbajaxfield', '/components/com_comprofiler/plugin/user/plug_cbcorefieldsajax/js/cbcorefieldsajax.js' );

	$_CB_framework->outputCbJQuery( "$( '.cbAjaxContainer' ).cbajaxfield();", array( 'cbajaxfield', 'form' ) );
}

$ajaxOutput		=	( $field->params->get( ( $reason == 'list' ? 'ajax_list_output' : 'ajax_profile_output' ), 1 ) == 2 ? ' data-cbajaxfield-tooltip="true"' : null );
$editUrl		=	$_CB_framework->viewUrl( 'fieldclass', true, array( 'field' => $field->get( 'name' ), 'function' => 'ajax_edit', 'user' => (int) $user->get( 'id' ), 'reason' => $reason ), 'raw' );

$return			=	'<div class="cbAjaxContainer cbAjaxContainerDisplay' . ( in_array( $formatting, array( 'span', 'none' ) ) ? ' cbAjaxContainerInline' : null ) . ' cbClicksInside" data-cbajaxfield-edit-url="' . $editUrl . '"' . $ajaxOutput . '>'
				.		'<div class="cbAjaxValue fa-before fa-pencil">'
				.			$formatted
				.		'</div>'
				.	'</div>';

return $return;