<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->registerFunction( 'onAfterLoginForm', 'getDisplay', 'cbgtePlugin' );
$_PLUGINS->registerFunction( 'onAfterLogoutForm', 'getDisplay', 'cbgtePlugin' );

class cbgtePlugin extends cbPluginHandler {

	public function getDisplay( $name_lenght, $pass_lenght, $horizontal, $class_sfx, $params ) {
		global $_CB_framework;

		static $JS_loaded			=	0;

		if ( ! $JS_loaded++ ) {
			$scheme					=	( ( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) ) ? 'https' : 'http' );
			$language				=	$this->params->get( 'general_default', 'en' );
			$languages				=	$this->params->get( 'general_languages', null );
			$tracking				=	$this->params->get( 'general_tracking', null );
			$code					=	$this->params->get( 'customization_code', null );
			$mode					=	$this->params->get( 'mode_display', 1 );

			if ( $code ) {
				$_CB_framework->document->addHeadMetaData( 'google-translate-customization', $code );
			}

			switch( $mode ) {
				case 3:
					$layout			=	null;
					break;
				case 2:
					switch( $this->params->get( 'mode_tabbed_layout', 4 ) ) {
						case 1:
							$layout	=	", floatPosition: google.translate.TranslateElement.FloatPosition.BOTTOM_RIGHT";
							break;
						case 2:
							$layout	=	", floatPosition: google.translate.TranslateElement.FloatPosition.BOTTOM_LEFT";
							break;
						case 3:
							$layout	=	", floatPosition: google.translate.TranslateElement.FloatPosition.TOP_RIGHT";
							break;
						case 4:
						default:
							$layout	=	", floatPosition: google.translate.TranslateElement.FloatPosition.TOP_LEFT";
							break;
					}
					break;
				case 1:
				default:
					switch( $this->params->get( 'mode_inline_layout', 3 ) ) {
						case 1:
							$layout	=	null;
							break;
						case 2:
							$layout	=	", layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL";
							break;
						case 3:
						default:
							$layout	=	", layout: google.translate.TranslateElement.InlineLayout.SIMPLE";
							break;
					}
					break;
			}

			$API_js					=	"function googletranslate_loadapi() {"
									.		"if ( window.google ) {"
									.			"new google.translate.TranslateElement({"
									.				"pageLanguage: '$language'"
									.				( $languages ? ", includedLanguages: '" . implode( ',', explode( '|*|', $languages ) ) . "'" : null )
									.				( ( ( $language == 'auto' ) || ( ! $this->params->get( 'general_auto', 1 ) ) && ( $mode != 3 ) ) ? ", autoDisplay: false" : null )
									.				( ( $language != 'auto' ) && $this->params->get( 'general_mixed', 0 ) ? ", multilanguagePage: true" : null )
									.				( $tracking ? ", gaTrack: true, gaId: '" . addslashes( $tracking ) . "'" : null )
									.				$layout
									.			( $mode >= 2 ? "});" : "}, 'cbGoogleTranslate' );" )
									.		"}"
									.	"};";

			$_CB_framework->document->addHeadScriptUrl( $scheme . '://translate.google.com/translate_a/element.js?cb=googletranslate_loadapi', false, null, $API_js );

			if ( ! $this->params->get( 'general_tooltip', 0 ) ) {
				$TOOLIP_css			=	'.goog-tooltip {'
									.		'display: none !important;'
									.		'top: 0 !important;'
									.		'z-index: -100 !important;'
									.	'}'
									.	'.goog-tooltip:hover {'
									.		'display: none !important;'
									.		'top: 0 !important;'
									.		'z-index: -100 !important;'
									.	'}'
									.	'.goog-text-highlight {'
									.		'background-color: transparent !important;'
									.		'border: none !important; '
									.		'box-shadow: none !important;'
									.	'}';

				$_CB_framework->document->addHeadStyleInline( $TOOLIP_css );
			}
		}

		$return						=	'<div id="cbGoogleTranslate"></div>';

		if ( ! $_CB_framework->myId() ) {
			$return					=	array( 'almostEnd' => $return );
		}

		return $return;
	}
}
?>