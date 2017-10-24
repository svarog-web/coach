<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'deleteItems', '\CB\Plugin\Gallery\Trigger\UserTrigger' );

$_PLUGINS->registerFunction( 'mod_onCBAdminMenu', 'adminMenu', '\CB\Plugin\Gallery\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'activity_onQueryActivity', 'activityQuery', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onLoadActivity', 'activityLoad', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayActivity', 'activityDisplay', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'gallery' => '\CB\Plugin\Gallery\Field\GalleryField' ) );

class cbgalleryTab extends cbTabHandler
{

	/**
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params	=	new Registry( $tab->params );
		}

		$gallery			=	new Gallery( null, $user );

		$gallery->set( 'tab', $tab->get( 'tabid', 0, GetterInterface::INT ) );

		$gallery->parse( $tab->params, 'gallery_' );

		if ( ( ! Application::Config()->get( 'showEmptyTabs', 1, GetterInterface::INT ) ) && ( ! $gallery->folders( true ) ) && ( ! $gallery->items( true ) ) && ( ! CBGallery::canCreateFolders( $gallery ) ) && ( ! CBGallery::canCreateItems( 'all', 'both', $gallery ) ) ) {
			return null;
		}

		return $gallery->gallery();
	}
}