<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveEvents\Table;

use CB\Plugin\GroupJive\CBGroupJive;
use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CB\Plugin\GroupJive\Table\GroupTable;

defined('CBLIB') or die();

class EventTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var int  */
	public $group			=	null;
	/** @var string  */
	public $title			=	null;
	/** @var string  */
	public $event			=	null;
	/** @var string  */
	public $location		=	null;
	/** @var string  */
	public $address			=	null;
	/** @var int  */
	public $limit			=	null;
	/** @var string  */
	public $start			=	null;
	/** @var string  */
	public $end				=	null;
	/** @var int  */
	public $published		=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__groupjive_plugin_events';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( $this->get( 'user_id' ) == '' ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		} elseif ( $this->get( 'group' ) == '' ) {
			$this->setError( CBTxt::T( 'Group not specified!' ) );

			return false;
		} elseif ( $this->get( 'title' ) == '' ) {
			$this->setError( CBTxt::T( 'Title not specified!' ) );

			return false;
		} elseif ( $this->get( 'event' ) == '' ) {
			$this->setError( CBTxt::T( 'Event not specified!' ) );

			return false;
		} elseif ( $this->get( 'start' ) == '0000-00-00 00:00:00' ) {
			$this->setError( CBTxt::T( 'Start date not specified!' ) );

			return false;
		} elseif ( ! $this->group()->get( 'id' ) ) {
			$this->setError( CBTxt::T( 'Group does not exist!' ) );

			return false;
		} elseif ( $this->get( 'end' ) != '0000-00-00 00:00:00' ) {
			if ( Application::Date( $this->get( 'start' ), 'UTC' )->diff( $this->get( 'end' ) )->days < 0 ) {
				$this->setError( CBTxt::T( 'End date can not be before the start date!' ) );

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

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'gj_onBeforeUpdateEvent', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onBeforeCreateEvent', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'gj_onAfterUpdateEvent', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'gj_onAfterCreateEvent', array( $this ) );
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

		$_PLUGINS->trigger( 'gj_onBeforeDeleteEvent', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Delete attendance to this event:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__groupjive_plugin_events_attendance' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'event' ) . " = " . (int) $this->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$users			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\GroupJiveEvents\Table\AttendanceTable', array( $this->getDbo() ) );

		/** @var AttendanceTable[] $users */
		foreach ( $users as $user ) {
			$user->delete();
		}

		$_PLUGINS->trigger( 'gj_onAfterDeleteEvent', array( $this ) );

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
	 * @return GroupTable
	 */
	public function group()
	{
		return CBGroupJive::getGroup( (int) $this->get( 'group' ) );
	}

	/**
	 * @return string
	 */
	public function date()
	{
		static $cache				=	array();

		$id							=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$date					=	cbFormatDate( $this->get( 'start' ), true, true, CBTxt::T( 'GROUP_EVENT_DATE_FORMAT', 'l, F j Y' ), CBTxt::T( 'GROUP_EVENT_TIME_FORMAT', ' g:i A' ) );

			if ( $this->get( 'end' ) && ( $this->get( 'end' ) != '0000-00-00 00:00:00' ) ) {
				if ( Application::Date( $this->get( 'start' ) )->diff( $this->get( 'end' ) )->days == 0 ) {
					$dateFormat		=	'';
				} else {
					$dateFormat		=	CBTxt::T( 'GROUP_EVENT_DATE_FORMAT', 'l, F j Y' );
				}

				$date				.=	' - ' . trim( cbFormatDate( $this->get( 'end' ), true, true, $dateFormat, CBTxt::T( 'GROUP_EVENT_TIME_FORMAT', ' g:i A' ) ) );
			}

			$cache[$id]				=	$date;
		}

		return $cache[$id];
	}

	/**
	 * @return int
	 */
	public function status()
	{
		static $cache				=	array();

		$id							=	$this->get( 'id' );

		if ( ! isset( $cache[$id] ) ) {
			$start					=	Application::Date( $this->get( 'start' ), 'UTC' )->getTimestamp();
			$now					=	Application::Date( 'now', 'UTC' )->getTimestamp();
			$status					=	0;

			if ( $start < $now )  {
				$status				=	1;

				if ( $this->get( 'end' ) && ( Application::Date( $this->get( 'end' ), 'UTC' )->getTimestamp() > $now ) ) {
					$status			=	2;
				}
			}

			$cache[$id]				=	$status;
		}

		return $cache[$id];
	}
}