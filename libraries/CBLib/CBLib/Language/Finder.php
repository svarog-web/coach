<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 09.06.13 01:29 $
* @package ${NAMESPACE}
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Language;

defined('CBLIB') or die();

/**
 * CBLib\Language\Finder (L like Language) Class implementation
 *
 */
class Finder
{

	/**
	 * Outputs an XML field type for searching language keys and text
	 *
	 * @param string $language
	 * @return string
	 */
	public static function input( $language = 'default' )
	{
		global $_CB_framework;

		static $JS_LOADED	=	0;

		if ( ! $JS_LOADED++ ) {
			$js				=	"var cbLangFinderRequest = null;"
							.	"var cbLangFinderPrevious = null;"
							.	"var cbLangFinderHandler = function() {"
							.		"var finder = $( this ).closest( '.cbLanguageFinder' );"
							.		"var search = finder.find( '.cbLanguageFinderSearch' ).val();"
							.		"var results = finder.find( '.cbLanguageFinderResults' );"
							.		"if ( ( cbLangFinderRequest == null ) && search && ( cbLangFinderPrevious != search ) ) {"
							.			"cbLangFinderPrevious = search;"
							.			"cbLangFinderRequest = $.ajax({"
							.				"url: '" . addslashes( $_CB_framework->backendViewUrl( 'languagefinder', false, array( 'language' => $language ), 'raw' ) ) . "',"
							.				"type: 'GET',"
							.				"dataType: 'html',"
							.				"cache: false,"
							.				"data: {"
							.					"search: search"
							.				"},"
							.				"beforeSend: function( jqXHR, textStatus, errorThrown ) {"
							.					"finder.find( '.cbLanguageFinderLoading' ).removeClass( 'hidden' );"
							.					"results.hide();"
							.				"}"
							.			"}).done( function( data, textStatus, jqXHR ) {"
							.				"results.html( data );"
							.				"results.fadeIn( 'slow' );"
							.				"results.find( '.cbMoreLess' ).cbmoreless();"
							.				"results.find( '.cbLanguageFinderResult' ).on( 'click', function() {"
							.					"var result = $( this );"
							.					"var resultKey = result.find( '.cbLanguageFinderResultKey' ).html();"
							.					"var resultText = result.find( '.cbLanguageFinderResultText' ).html();"
							.					"var resultFound = 0;" // No Empty or Existing found
							.					"$( 'input.cbLanguageOverrideKey' ).each( function() {"
							.						"if ( $( this ).val() == '' ) {"
							.							"$( this ).val( resultKey );"
							.							"$( this ).closest( '.cbLanguageOverride' ).find( 'textarea.cbLanguageOverrideText' ).val( resultText ).focus();"
							.							"resultFound = 1;" // Empty Found
							.						"} else if ( $( this ).val() == resultKey ) {"
							.							"resultFound = 2;" // Existing Found
							.						"}"
							.					"});"
							.					"if ( resultFound === 0 ) {" // Add new row then populate it
							.						"$( '.cbLanguageOverrides' ).find( '.cbRepeat' ).cbrepeat( 'add' );"
							.						"$( 'input.cbLanguageOverrideKey' ).each( function() {"
							.							"if ( $( this ).val() == '' ) {"
							.								"$( this ).val( resultKey );"
							.								"$( this ).closest( '.cbLanguageOverride' ).find( 'textarea.cbLanguageOverrideText' ).val( resultText ).focus();"
							.							"}"
							.						"});"
							.					"}"
							.				"});"
							.			"}).always( function( data, textStatus, jqXHR ) {"
							.				"cbLangFinderRequest = null;"
							.				"finder.find( '.cbLanguageFinderLoading' ).addClass( 'hidden' );"
							.			"});"
							.		"}"
							.	"};"
							.	"$( '.cbLanguageFinderSearch' ).on( 'keypress', function( e ) {"
							.		"if ( e.which == 13 ) {"
							.			"cbLangFinderHandler.call( this );"
							.		"}"
							.	"});"
							.	"$( '.cbLanguageFinderButton' ).on( 'click', cbLangFinderHandler );";

			$_CB_framework->outputCbJQuery( $js, 'cbmoreless' );
		}

		$return				=	'<div class="cbLanguageFinder row">'
							.		'<div class="col-sm-4">'
							.			'<div class="form-group input-group">'
							.				'<input type="text" class="cbLanguageFinderSearch form-control input-block" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Language Keys and Text...' ) ) . '" />'
							.				'<span class="input-group-btn">'
							.					'<button class="cbLanguageFinderButton btn btn-primary" type="button">' . CBTxt::T( 'Find' ) . '</button>'
							.				'</span>'
							.			'</div>'
							.		'</div>'
							.		'<div class="col-sm-8">'
							.			'<span class="cbLanguageFinderLoading fa fa-spinner fa-pulse hidden"></span>'
							.			'<div class="cbLanguageFinderResults"></div>'
							.		'</div>'
							.	'</div>';

		return $return;
	}

	/**
	 * Searches available language strings for a matching key or text
	 *
	 * @param string $language
	 * @param string $search
	 * @return string
	 */
	public static function find( $language, $search )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ( ! $language ) || ( $language == 'default' ) || ( $language == 'default_language' ) ) {
			$language				=	null;
		}

		if ( ! $search ) {
			return CBTxt::T( 'Nothing to search for.' );
		}

		// Set language to default:
		$langCache					=	CBTxt::setLanguage( null );

		// Load core language files:
		cbimport( 'language.all' );

		// Load plugin language files:
		$_PLUGINS->loadPluginGroup( 'users' );

		// Grab all the default language keys and text:
		$languageStrings			=	CBTxt::getStrings();

		if ( $language ) {
			$languagePath			=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/language';

			if ( file_exists( $languagePath . '/' . $language ) ) {
				// Import the language plugins main language strings:
				CBTxt::import( $languagePath, $language, 'language.php', false );
				CBTxt::import( $languagePath, $language, 'admin_language.php', false );
				CBTxt::import( $languagePath, $language, 'override.php', false, true );

				// Import the plugin language files for this language:
				foreach ( $_PLUGINS->getLoadedPluginGroup( null ) as $plugin ) {
					CBTxt::import( $languagePath, $language, 'cbplugin/' . $plugin->element . '-language.php', false );
					CBTxt::import( $languagePath, $language, 'cbplugin/' . $plugin->element . '-admin_language.php', false );
				}

				// Grab all the specific language keys and text then merge with default:
				$languageStrings	=	array_merge( $languageStrings, CBTxt::getStrings( $language ) );
			}
		}

		// Reset language back to original:
		CBTxt::setLanguage( $langCache );

		$return						=	null;

		foreach ( $languageStrings as $key => $text ) {
			if ( ( stripos( $key, $search ) !== false ) || ( stripos( $text, $search ) !== false ) ) {
				$return				.=	'<div class="cbLanguageFinderResult panel panel-default" style="cursor: pointer;">'
									.		'<div class="cbLanguageFinderResultKey panel-heading text-wrapall">' . htmlspecialchars( $key ) . '</div>'
									.		'<div class="cbLanguageFinderResultText panel-body text-wrap">' . htmlspecialchars( $text ) . '</div>'
									.	'</div>';
			}
		}

		if ( $return ) {
			$return					=	'<div class="cbMoreLess" data-cbmoreless-stepped="true" data-cbmoreless-height="400">'
									.		'<div class="cbMoreLessContent">'
									.			$return
									.		'</div>'
									.		'<div class="cbMoreLessOpen fade-edge hidden">'
									.			'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
									.		'</div>'
									.	'</div>';

			return $return;
		}

		return CBTxt::T( 'No language key or string matches found.' );
	}
}