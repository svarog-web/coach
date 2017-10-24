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

class cbautoactionsActionBlog extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		if ( ! $this->installed() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_BLOGS_NOT_INSTALLED', ':: Action [action] :: CB Blogs is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		foreach ( $trigger->getParams()->subTree( 'blog' ) as $row ) {
			/** @var ParamsInterface $row */
			$blog			=	new cbblogsBlogTable();

			$owner			=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner		=	(int) $user->get( 'id' );
			} else {
				$owner		=	(int) $trigger->getSubstituteString( $owner );
			}

			if ( ! $owner ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_BLOGS_NO_OWNER', ':: Action [action] :: CB Blogs skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			$blogData		=	array(	'user' => $owner,
										'title' => $trigger->getSubstituteString( $row->get( 'title', null, GetterInterface::STRING ) ),
										'blog_intro' => $trigger->getSubstituteString( $row->get( 'intro', null, GetterInterface::RAW ), false ),
										'blog_full' => $trigger->getSubstituteString( $row->get( 'full', null, GetterInterface::RAW ), false ),
										'category' => $row->get( 'category', null, GetterInterface::STRING ),
										'published' => (int) $row->get( 'published', 1, GetterInterface::INT ),
										'access' => (int) $row->get( 'access', 1, GetterInterface::INT )
									);

			if ( ! $blog->bind( $blogData ) ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_BLOGS_BIND_FAILED', ':: Action [action] :: CB Blogs failed to bind. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $blog->getError() ) ) );
				}

				continue;
			}

			if ( ! $blog->store() ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_BLOGS_FAILED', ':: Action [action] :: CB Blogs failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $blog->getError() ) ) );
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function categories()
	{
		$options		=	array();

		if ( $this->installed() ) {
			$options	=	cbblogsModel::getCategoriesList();
		}

		return $options;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbblogs' ) ) {
			return true;
		}

		return false;
	}
}