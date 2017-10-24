<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

abstract class Stream extends Registry implements StreamInterface
{
	/** @var string $id */
	protected $id			=	null;
	/** @var string $source */
	protected $source		=	'stream';
	/** @var UserTable $user */
	protected $user			=	null;
	/** @var string $endpoint */
	protected $endpoint		=	'stream';

	/** @var bool $resetCount */
	protected $resetCount	=	false;
	/** @var bool $resetSelect */
	protected $resetSelect	=	false;

	/**
	 * Constructor for stream object
	 *
	 * @param null|string    $source
	 * @param null|UserTable $user
	 */
	public function __construct( $source = null, $user = null )
	{
		global $_PLUGINS;

		parent::__construct();

		$_PLUGINS->loadPluginGroup( 'user' );

		if ( $source === null ) {
			$source			=	'stream';
		}

		if ( $user === null ) {
			$user			=	\CBuser::getMyUserDataInstance();
		}

		$this->source		=	$source;
		$this->user			=	$user;

		if ( ! $this->id ) {
			$this->id		=	uniqid();
		}
	}

	/**
	 * Gets the stream id
	 *
	 * @return string
	 */
	public function id()
	{
		if ( ! $this->id ) {
			$this->id		=	uniqid();
		}

		return $this->id;
	}

	/**
	 * Gets or sets the stream source
	 *
	 * @param string|null $source
	 * @return string
	 */
	public function source( $source = null )
	{
		if ( $source ) {
			$this->source	=	$source;
		}

		return $this->source;
	}

	/**
	 * Gets or sets the stream target user (owner)
	 *
	 * @param UserTable|null $user
	 * @return UserTable|null
	 */
	public function user( $user = null )
	{
		if ( $user ) {
			$this->user	=	$user;
		}

		return $this->user;
	}

	/**
	 * Resets the data cache for this stream (forces data to requery)
	 */
	public function resetData()
	{
		$this->resetCount	=	true;
		$this->resetSelect	=	true;
	}

	/**
	 * Outputs stream HTML
	 *
	 * @param bool $inline
	 * @param bool $data
	 * @return string
	 */
	public function stream( $inline = false, $data = true )
	{
		global $_CB_framework, $_PLUGINS;

		static $plugin		=	null;

		if ( ! $plugin ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );
		}

		if ( ! $plugin ) {
			return null;
		}

		if ( ! class_exists( 'CBplug_cbactivity' ) ) {
			$component		=	$_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbactivity/component.cbactivity.php';

			if ( file_exists( $component ) ) {
				include_once( $component );
			}
		}

		ob_start();
		$pluginTab			=	null;
		$pluginData			=	array( 'stream' => $this, 'inline' => $inline, 'data' => $data );
		$pluginArguements	=	array( &$pluginTab, &$this->user, ( Application::Cms()->getClientId() ? 2 : 1 ), &$pluginData );

		$_PLUGINS->call( $plugin->id, 'getCBpluginComponent', 'CBplug_cbactivity', $pluginArguements, $pluginTab );
		$return				=	ob_get_contents();
		ob_end_clean();

		return $return;
	}

	/**
	 * Returns the stream validation token
	 *
	 * @param array $data
	 * @return string
	 */
	public function token( $data = array() )
	{
		return md5( json_encode( array_merge( $this->asArray(), $data ) ) );
	}

	/**
	 * Outputs stream URL endpoint
	 *
	 * @param string $view
	 * @param array  $data
	 * @return string
	 */
	public function endpoint( $view = null, $data = array() )
	{
		global $_CB_framework;

		$stream					=	array_merge( $this->asArray(), $data );

		$path['action']			=	$this->endpoint;
		$path['func']			=	$view;
		$path['stream']			=	base64_encode( json_encode( $stream ) );
		$path['token']			=	$this->token( $stream );

		return $_CB_framework->pluginClassUrl( 'cbactivity', true, $path, 'raw', 0, true );
	}

	/**
	 * Returns a parser object for parsing stream content
	 *
	 * @param string $string
	 * @return Parser
	 */
	public function parser( $string = '' )
	{
		$parser		=	new Parser( $string );

		return $parser;
	}

	/**
	 * Returns an array of all current params
	 *
	 * @return array
	 */
	public function asArray()
	{
		$params					=	parent::asArray();

		$params['source']		=	$this->source;
		$params['user']			=	(int) $this->user->get( 'id' );

		return $params;
	}
}