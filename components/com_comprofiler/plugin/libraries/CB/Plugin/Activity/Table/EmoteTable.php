<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Table;

use CBLib\Database\Table\OrderedTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Application\Application;
use CB\Plugin\Activity\CBActivity;

defined('CBLIB') or die();

class EmoteTable extends OrderedTable
{
	/** @var int  */
	public $id				=	null;
	/** @var string  */
	public $value			=	null;
	/** @var string  */
	public $icon			=	null;
	/** @var string  */
	public $class			=	null;
	/** @var int  */
	public $published		=	null;
	/** @var int  */
	public $ordering		=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_activity_emotes';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * Ordering keys and for each their ordering groups.
	 * E.g.; array( 'ordering' => array( 'tab' ), 'ordering_registration' => array() )
	 * @var array
	 */
	protected $_orderings	=	array( 'ordering' => array() );

	/**
	 * @return bool
	 */
	public function check()
	{
		$emote		=	cbutf8_strtolower( preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'value' ) ) );

		if ( $emote == '' ) {
			$this->setError( CBTxt::T( 'Emote not specified!' ) );

			return false;
		} elseif ( ( $this->get( 'icon' ) == '' ) && ( $this->get( 'class' ) == '' ) ) {
			$this->setError( CBTxt::T( 'Icon not specified!' ) );

			return false;
		} else {
			$row	=	new EmoteTable();

			$row->load( array( 'value' => $emote ) );

			if ( $row->get( 'id' ) && ( $this->get( 'id' ) != $row->get( 'id' ) ) ) {
				$this->setError( CBTxt::T( 'Emote already exists!' ) );

				return false;
			}
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		$this->set( 'value', cbutf8_strtolower( preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'value' ) ) ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'activity_onBeforeUpdateEmote', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateEmote', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateEmote', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateEmote', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'activity_onBeforeDeleteEmote', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteEmote', array( $this ) );

		return true;
	}

	/**
	 * @return Registry
	 */
	public function params()
	{
		if ( ! ( $this->get( '_params' ) instanceof Registry ) ) {
			$this->set( '_params', new Registry( $this->get( 'params' ) ) );
		}

		return $this->get( '_params' );
	}

	/**
	 * @return string
	 */
	public function icon()
	{
		global $_CB_framework;

		if ( Application::Cms()->getClientId() ) {
			CBActivity::getTemplate( 'twemoji', false, true, false );
		}

		$icon			=	null;
		$emoteClass		=	'streamIconEmote' . ucfirst( strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $this->get( 'value' ) ) ) );

		if ( $this->get( 'icon' ) ) {
			$icon		=	'<img src="' . $_CB_framework->getCfg( 'live_site' ) . '/images/' . htmlspecialchars( $this->get( 'icon' ) ) . '" class="streamIconEmote ' . $emoteClass . ' img-responsive-inline" />';
		} elseif ( $this->get( 'class' ) ) {
			$icon		=	'<span class="streamIconEmote ' . $emoteClass . ' ' . htmlspecialchars( $this->get( 'class' ) ) . '"></span>';
		}

		return $icon;
	}
}