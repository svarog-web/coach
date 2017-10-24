<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( strtolower( $_SERVER['HTTPS'] ) != 'off' ) ) {
	$url		=	'https://';
} else {
	$url		=	'http://';
}

if ( ( ! empty( $_SERVER['PHP_SELF'] ) ) && ( ! empty( $_SERVER['REQUEST_URI'] ) ) ) {
	$url		.=	$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
} else {
	$url		.=	$_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

	if ( isset( $_SERVER['QUERY_STRING'] ) && ( ! empty( $_SERVER['QUERY_STRING'] ) ) ) {
		$url	.=	'?' . $_SERVER['QUERY_STRING'];
	}
}

$hasQuery		=	( strpos( $url, '?' ) !== false );
$urlFrom		=	'components/com_comprofiler/plugin/user/plug_cbconnect/endpoint.php' . ( $hasQuery ? '?' : '' );
$urlTo			=	'index.php?option=com_comprofiler&task=pluginclass&plugin=cbconnect' . ( $hasQuery ? '&' : '' );
$url			=	str_replace( $urlFrom, $urlTo, $url );

header( 'HTTP/1.1 303 See other' );
header( 'Location: ' . $url );
header( 'Content-Type: text/html; charset=UTF-8' );
exit();
