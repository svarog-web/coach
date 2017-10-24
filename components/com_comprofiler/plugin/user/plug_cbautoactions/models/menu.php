<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionMenu extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		foreach ( $trigger->getParams()->subTree( 'menu' ) as $row ) {
			/** @var ParamsInterface $row */
			$menuTitle					=	$trigger->getSubstituteString( $row->get( 'title', null, GetterInterface::STRING ) );

			if ( ! $menuTitle ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_MENU_NO_TITLE', ':: Action [action] :: CB Menu skipped due to missing title', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$menuType					=	$trigger->getSubstituteString( $row->get( 'type', null, GetterInterface::STRING ), true, false );
			$menuClass					=	$trigger->getSubstituteString( $row->get( 'class', null, GetterInterface::STRING ), true, false );
			$menuTarget					=	$row->get( 'target', null, GetterInterface::STRING );
			$menuImg					=	$trigger->getSubstituteString( $row->get( 'image', null, GetterInterface::STRING ), false );

			$menuItem					=	array();

			if ( ! $menuType )  {
				$menuItem['arrayPos']	=	array( $menuClass => null );
			} else {
				$menuItem['arrayPos']	=	array( $menuType => array( $menuClass => null ) );
			}

			$menuItem['position']		=	$row->get( 'position', 'menuBar', GetterInterface::STRING );
			$menuItem['caption']		=	htmlspecialchars( $menuTitle );
			$menuItem['url']			=	$trigger->getSubstituteString( $row->get( 'url', null, GetterInterface::STRING ), false );
			$menuItem['target']			=	( $menuTarget ? htmlspecialchars( $menuTarget ) : null );
			$menuItem['img']			=	( $menuImg ? ( $menuImg[0] == '<' ? $menuImg : '<img src="' . htmlspecialchars( $menuImg ) . '" />' ) : null );
			$menuItem['tooltip']		=	htmlspecialchars( $trigger->getSubstituteString( $row->get( 'tooltip', null, GetterInterface::STRING ), false ) );

			$this->addMenu( $menuItem );
		}
	}
}