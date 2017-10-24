<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

JFormHelper::loadFieldClass( 'list' );

class JFormFieldgjmodes extends JFormFieldList
{
	/** @var string  */
	protected $type	=	'gjmodes';

	/**
	 * @return array
	 */
	protected function getOptions()
	{
		global $_PLUGINS;

		static $loaded		=	0;

		if ( ! $loaded++ ) {
			include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

			cbimport( 'cb.html' );
			cbimport( 'language.all' );

			$_PLUGINS->loadPluginGroup( 'user' );
		}

		$options			=	parent::getOptions();

		$_PLUGINS->trigger( 'gj_onModuleMode', array( &$options, $this ) );

		return $options;
	}
}