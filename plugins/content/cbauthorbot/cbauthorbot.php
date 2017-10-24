<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class plgContentcbauthorbot extends JPlugin
{

	/**
	 * @param string $context
	 * @param object $article
	 */
	public function onContentBeforeDisplay( $context, &$article )
	{
		if ( ( strpos( $context, 'com_content' ) !== false ) && ( isset( $article->created_by ) || isset( $article->modified_by ) ) ) {
			static $CB_loaded				=	0;

			if ( ! $CB_loaded++ ) {
				if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
					echo 'CB not installed'; return;
				}

				include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );
			}

			if ( ( isset( $article->created_by ) ) && $article->created_by ) {
				$cbUserCreate				=	CBuser::getInstance( (int) $article->created_by, false );

				if ( isset( $article->author ) ) {
					$article->author		=	$cbUserCreate->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
				}

				$article->created_by_alias	=	$cbUserCreate->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
			}

			if ( ( isset( $article->modified_by ) ) && $article->modified_by ) {
				$cbUserModify				=	CBuser::getInstance( (int) $article->modified_by, false );

				$article->modified_by_name	=	$cbUserModify->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
			}

			if ( ( isset( $article->contactid ) ) && $article->contactid ) {
				$article->contactid			=	null;
			}
		}
	}
}