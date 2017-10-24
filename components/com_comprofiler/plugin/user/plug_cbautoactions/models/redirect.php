<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionRedirect extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_framework;

		$params						=	$trigger->getParams()->subTree( 'redirect' );
		$redirect					=	$trigger->getSubstituteString( $params->get( 'url', null, GetterInterface::STRING ), ( preg_match( '/^\[[a-zA-Z0-9-_]+\]$/', $params->get( 'url', null, GetterInterface::STRING ) ) ? false : array( 'cbautoactionsClass', 'escapeURL' ) ) );

		if ( ! $redirect ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_REDIRECT_NO_URL', ':: Action [action] :: Redirect skipped due to missing url', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		$message					=	$trigger->getSubstituteString( CBTxt::T( $params->get( 'message', null, GetterInterface::RAW ) ), false );
		$messageType				=	$params->get( 'type', 'message', GetterInterface::STRING );

		if ( $messageType == 'custom' ) {
			$messageType			=	$trigger->getSubstituteString( $params->get( 'custom_type', null, GetterInterface::STRING ) );
		}

		if ( substr( strtolower( $redirect ), 0, 6 ) == 'goback' ) {
			$back					=	(int) substr( strtolower( $redirect ), 6 );

			if ( $message ) {
				$_CB_framework->enqueueMessage( $message, ( $messageType ? $messageType : null ) );
			}

			$_CB_framework->document->addHeadScriptDeclaration( ( $back && ( $back > 0 ) ? "window.history.go( -$back );" : "window.history.back();" ) );
		} elseif ( strtolower( $redirect ) == 'reload' ) {
			if ( $message ) {
				$_CB_framework->enqueueMessage( $message, ( $messageType ? $messageType : null ) );
			}

			$_CB_framework->document->addHeadScriptDeclaration( "window.location.reload();" );
		} else {
			if ( strtolower( $redirect ) == 'return' ) {
				$isHttps			=	( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) );
				$redirect			=	'http' . ( $isHttps ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'];

				if ( ( ! empty( $_SERVER['PHP_SELF'] ) ) && ( ! empty( $_SERVER['REQUEST_URI'] ) ) ) {
					$redirect		.=	$_SERVER['REQUEST_URI'];
				} else {
					$redirect		.=	$_SERVER['SCRIPT_NAME'];

					if ( isset( $_SERVER['QUERY_STRING'] ) && ( ! empty( $_SERVER['QUERY_STRING'] ) ) ) {
						$redirect	.=	'?' . $_SERVER['QUERY_STRING'];
					}
				}

				$redirect			=	cbUnHtmlspecialchars( preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', preg_replace( '/eval\((.*)\)/', '', htmlspecialchars( urldecode( $redirect ) ) ) ) );

				if ( preg_match( '/index.php\?option=com_comprofiler&task=confirm&confirmCode=|index.php\?option=com_comprofiler&view=confirm&confirmCode=|index.php\?option=com_comprofiler&task=login|index.php\?option=com_comprofiler&view=login/', $redirect ) ) {
					$redirect		=	'index.php';
				}
			}

			cbRedirect( $redirect, $message, ( $message ? ( $messageType ? $messageType : null ) : null ) );
		}
	}
}