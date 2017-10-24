<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Table;

use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

class TagTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var string  */
	public $type			=	null;
	/** @var string  */
	public $subtype			=	null;
	/** @var string  */
	public $item			=	null;
	/** @var string  */
	public $parent			=	null;
	/** @var string  */
	public $user			=	null;
	/** @var string  */
	public $date			=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_activity_tags';

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
		} elseif ( trim( $this->get( 'user' ) ) == '' ) {
			$this->setError( CBTxt::T( 'User not specified!' ) );

			return false;
		} elseif ( preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'type' ) ) == '' ) {
			$this->setError( CBTxt::T( 'Type not specified!' ) );

			return false;
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_CB_framework, $_PLUGINS;

		$new	=	( $this->get( 'id' ) ? false : true );
		$old	=	new self();

		$this->set( 'type', preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'type' ) ) );
		$this->set( 'subtype', preg_replace( '/[^-a-zA-Z0-9_.]/', '', $this->get( 'subtype' ) ) );
		$this->set( 'date', $this->get( 'date', $_CB_framework->getUTCDate() ) );

		if ( ! $new ) {
			$old->load( (int) $this->get( 'id' ) );

			$_PLUGINS->trigger( 'activity_onBeforeUpdateTag', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateTag', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateTag', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateTag', array( $this ) );
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

		$_PLUGINS->trigger( 'activity_onBeforeDeleteTag', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Deletes activity about this tag:
		$query				=	'SELECT *'
							.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity' )
							.	"\n WHERE " . $this->getDbo()->NameQuote( 'type' ) . " = " . $this->getDbo()->Quote( 'activity' )
							.	"\n AND " . $this->getDbo()->NameQuote( 'subtype' ) . " = " . $this->getDbo()->Quote( 'tag' )
							.	"\n AND " . $this->getDbo()->NameQuote( 'item' ) . " = " . (int) $this->get( 'id' );
		$this->getDbo()->setQuery( $query );
		$activities			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\ActivityTable', array( $this->getDbo() ) );

		/** @var ActivityTable[] $activities */
		foreach ( $activities as $activity ) {
			$activity->delete();
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteTag', array( $this ) );

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
}