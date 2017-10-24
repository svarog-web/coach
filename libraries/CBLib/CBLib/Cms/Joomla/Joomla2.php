<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 09.06.13 01:15 $
* @package CBLib\Cms
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Cms\Joomla;

defined('CBLIB') or die();

/**
 * CBLib\Cms\Joomla\Joomla2 Class implementation
 * 
 */
class Joomla2 extends Joomla3
{
	/**
	 * Registers a handler to filter the final output
	 *
	 * @param  callable  $handler  A function( $body ) { return $bodyChanged; }
	 * @return self                To allow chaining.
	 */
	public function registerOnAfterRenderBodyFilter( $handler )
	{
		$this->registerEvent(
			'onAfterRender',
			function() use ( $handler ) {
				\JResponse::setBody( $handler( \JResponse::getBody() ) );
			}
		);
		return $this;
	}

	/**
	 * Registers a handler to a particular CMS event
	 *
	 * Joomla 2.5 does not accept a callable as handler, but only an object or a function name
	 * Thus we need to use a handler-mirror class for that.
	 *
	 * @param  string    $event    The event name:
	 * @param  callable  $handler  The handler, a function or an instance of a event object.
	 * @return self                To allow chaining.
	 */
	public function registerEvent( $event, $handler )
	{
		$observer		=	array( 'event' => $event, 'handler' => $handler );

		/** @noinspection PhpParamsInspection */
		\JDispatcher::getInstance()
			->attach( $observer );

		return $this;
	}

	/**
	 * Prepares the HTML $htmlText with triggering CMS Content Plugins
	 *
	 * @param  string $htmlText
	 * @param  string $context
	 * @param  int    $userId
	 * @return string
	 */
	public function prepareHtmlContentPlugins( $htmlText, $context = 'text', $userId = 0 ) {
		$previousDocType		=	\JFactory::getDocument()->getType();

		\JFactory::getDocument()->setType( 'html' );

		$content				=	new \stdClass();
		$content->text			=	$htmlText;
		$content->created_by	=	(int) $userId;

		$params					=	new \JRegistry();

		try {
			\JPluginHelper::importPlugin( 'content' );

			\JDispatcher::getInstance()->trigger( 'onContentPrepare', array( 'com_comprofiler' . ( $context ? '.' . $context : null ), &$content, &$params, 0 ) );
		} catch ( \Exception $e ) {}

		\JFactory::getDocument()->setType( $previousDocType );

		return $content->text;
	}
}
