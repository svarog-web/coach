<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionAction extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 * @return mixed
	 */
	public function execute( $trigger, $user )
	{
		$params					=	$trigger->getParams()->subTree( 'action' );
		$actions				=	$params->get( 'actions' );
		$return					=	null;

		if ( $actions ) {
			$actions			=	explode( '|*|', $actions );

			cbArrayToInts( $actions );

			foreach ( $actions as $actionId ) {
				$action			=	new cbautoactionsActionTable();

				if ( ! $action->load( $actionId ) ) {
					continue;
				}

				if ( ( ! $action->get( 'id', 0 , GetterInterface::INT ) ) || ( ! $action->get( 'published', 1 , GetterInterface::INT ) ) ) {
					continue;
				}

				$return			.=	cbautoactionsClass::triggerAction( $action, $user, $trigger->get( '_password' ), $trigger->get( '_extras' ), $trigger->get( '_vars' ) );
			}
		}

		return $return;
	}
}