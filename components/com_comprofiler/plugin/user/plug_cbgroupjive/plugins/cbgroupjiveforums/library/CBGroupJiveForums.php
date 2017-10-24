<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveForums;

use CB\Plugin\GroupJiveForums\Forum\ForumInterface;

defined('CBLIB') or die();

class CBGroupJiveForums
{

	/**
	 * Returns the forum instance
	 *
	 * @return null|ForumInterface
	 */
	static public function getForum()
	{
		global $_CB_framework, $_PLUGINS;

		static $forum				=	null;

		if ( ! $forum ) {
			static $params			=	null;

			if ( ! $params ) {
				$plugin				=	$_PLUGINS->getLoadedPlugin( 'user/plug_cbgroupjive/plugins', 'cbgroupjiveforums' );
				$params				=	$_PLUGINS->getPluginParams( $plugin );
			}

			switch( $params->get( 'groups_forums_model', 'kunena' ) ) {
				case 'kunena':
					$api			=	$_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_kunena/api.php';

					if ( file_exists( $api ) ) {
						require_once( $api );

						if ( class_exists( 'KunenaForum' ) && \KunenaForum::installed() ) {
							\KunenaForum::setup();

							$forum	=	new Forum\Kunena\KunenaForum();
						}
					}
					break;
			}
		}

		return $forum;
	}

	/**
	 * Returns select options list of forum categories
	 *
	 * @return array
	 */
	static public function getCategoryOptions()
	{
		$forum	=	self::getForum();

		if ( ! $forum ) {
			return array();
		}

		return $forum->getCategories();
	}
}