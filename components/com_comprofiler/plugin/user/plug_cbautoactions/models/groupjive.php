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
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\GroupTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class cbautoactionsActionGroupJive extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_database;

		if ( ! $this->installed() ) {
			if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NOT_INSTALLED', ':: Action [action] :: CB GroupJive is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		foreach ( $trigger->getParams()->subTree( 'groupjive' ) as $row ) {
			/** @var ParamsInterface $row */
			switch( (int) $row->get( 'mode', 1, GetterInterface::INT ) ) {
				case 3:
					$owner							=	$row->get( 'owner', null, GetterInterface::STRING );

					if ( ! $owner ) {
						$owner						=	(int) $user->get( 'id' );
					} else {
						$owner						=	(int) $trigger->getSubstituteString( $owner );
					}

					if ( ! $owner ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_OWNER', ':: Action [action] :: CB GroupJive skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$name							=	$trigger->getSubstituteString( $row->get( 'name', null, GetterInterface::STRING ) );

					if ( ! $name ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_NAME', ':: Action [action] :: CB GroupJive skipped due to missing name', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$category						=	new CategoryTable();

					$category->load( array( 'name' => $name ) );

					if ( $category->get( 'id' ) ) {
						continue;
					}

					$category->set( 'published', 1 );
					$category->set( 'name', $name );
					$category->set( 'description', $trigger->getSubstituteString( $row->get( 'description', null, GetterInterface::STRING ) ) );
					$category->set( 'access', 1 );
					$category->set( 'create_access', 0 );
					$category->set( 'types', $row->get( 'types', '1|*|2|*|3', GetterInterface::STRING ) );
					$category->set( 'ordering', 1 );

					if ( ! $category->store() ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $category->getError() ) ) );

							continue;
						}
					}
					break;
				case 2:
					$owner							=	$row->get( 'owner', null, GetterInterface::STRING );

					if ( ! $owner ) {
						$owner						=	(int) $user->get( 'id' );
					} else {
						$owner						=	(int) $trigger->getSubstituteString( $owner );
					}

					if ( ! $owner ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_OWNER', ':: Action [action] :: CB GroupJive skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$categoryId						=	(int) $row->get( 'category', -1, GetterInterface::INT );

					$category						=	new CategoryTable();

					if ( $categoryId == -1 ) {
						$name						=	$trigger->getSubstituteString( $row->get( 'category_name', null, GetterInterface::STRING ) );

						if ( ! $name ) {
							if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
								var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_CAT_NAME', ':: Action [action] :: CB GroupJive skipped due to missing category name', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
							}

							continue;
						}

						$category->load( array( 'name' => $name ) );

						if ( ! $category->get( 'id' ) ) {
							$category->set( 'published', 1 );
							$category->set( 'name', $name );
							$category->set( 'description', $trigger->getSubstituteString( $row->get( 'category_description', null, GetterInterface::STRING ) ) );
							$category->set( 'access', 1 );
							$category->set( 'create_access', 0 );
							$category->set( 'types', $row->get( 'category_types', '1|*|2|*|3', GetterInterface::STRING ) );
							$category->set( 'ordering', 1 );

							if ( ! $category->store() ) {
								if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
									var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $category->getError() ) ) );
								}

								continue;
							}
						}
					} else {
						$category->load( (int) $categoryId );
					}

					if ( ! $category->get( 'id' ) ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_CATEGORY', ':: Action [action] :: CB GroupJive skipped due to missing category', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$name							=	$trigger->getSubstituteString( $row->get( 'name', null, GetterInterface::STRING ) );

					if ( ! $name ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_NO_NAME', ':: Action [action] :: CB GroupJive skipped due to missing name', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$group							=	new GroupTable();
					$join							=	false;

					if ( $row->get( 'unique', 1, GetterInterface::BOOLEAN ) ) {
						$group->load( array( 'category' => (int) $category->get( 'id' ), 'user_id' => (int) $owner, 'name' => $name ) );
					} else {
						$group->load( array( 'category' => (int) $category->get( 'id' ), 'name' => $name ) );

						if ( $row->get( 'autojoin', 1, GetterInterface::BOOLEAN ) ) {
							$join					=	true;
						}
					}

					if ( ! $group->get( 'id' ) ) {
						$group->set( 'published', 1 );
						$group->set( 'category', (int) $category->get( 'id' ) );
						$group->set( 'user_id', $owner );
						$group->set( 'name', $name );
						$group->set( 'description', $trigger->getSubstituteString( $row->get( 'description', null, GetterInterface::STRING ) ) );
						$group->set( 'type', (int) $row->get( 'type', 1, GetterInterface::INT ) );
						$group->set( 'ordering', 1 );

						if ( ! $group->store() ) {
							if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
								var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $group->getError() ) ) );
							}

							continue;
						}
					} elseif ( $join ) {
						$groupUser					=	new \CB\Plugin\GroupJive\Table\UserTable( $_CB_database );

						$groupUser->load( array( 'group' => (int) $group->get( 'id' ), 'user_id' => (int) $user->get( 'id' ) ) );

						if ( $groupUser->get( 'id' ) ) {
							continue;
						}

						$groupUser->set( 'user_id', (int) $user->get( 'id' ) );
						$groupUser->set( 'group', (int) $group->get( 'id' ) );
						$groupUser->set( 'status', (int) $row->get( 'group_status', 1, GetterInterface::INT ) );

						if ( ! $groupUser->store() ) {
							if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
								var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $groupUser->getError() ) ) );
							}

							continue;
						}
					}
					break;
				case 4:
					$groups							=	$row->get( 'groups', null, GetterInterface::STRING );

					if ( ! $groups ) {
						continue;
					}

					$groups							=	explode( '|*|', $groups );

					cbArrayToInts( $groups );

					foreach ( $groups as $groupId ) {
						$group						=	new GroupTable();

						$group->load( (int) $groupId );

						if ( ! $group->get( 'id' ) ) {
							continue;
						}

						$groupUser					=	new \CB\Plugin\GroupJive\Table\UserTable( $_CB_database );

						$groupUser->load( array( 'group' => (int) $group->get( 'id' ), 'user_id' => (int) $user->get( 'id' ) ) );

						if ( ( ! $groupUser->get( 'id' ) ) || ( $groupUser->get( 'status' ) == 4 ) ) {
							continue;
						}

						$groupUser->delete();
					}
					break;
				case 1:
				default:
					$groups							=	$row->get( 'groups', null, GetterInterface::STRING );

					if ( ! $groups ) {
						continue;
					}

					$groups							=	explode( '|*|', $groups );

					cbArrayToInts( $groups );

					foreach ( $groups as $groupId ) {
						$group						=	new GroupTable();

						$group->load( (int) $groupId );

						if ( ! $group->get( 'id' ) ) {
							continue;
						}

						$groupUser					=	new \CB\Plugin\GroupJive\Table\UserTable( $_CB_database );

						$groupUser->load( array( 'group' => (int) $group->get( 'id' ), 'user_id' => (int) $user->get( 'id' ) ) );

						if ( $groupUser->get( 'id' ) ) {
							continue;
						}

						$groupUser->set( 'user_id', (int) $user->get( 'id' ) );
						$groupUser->set( 'group', (int) $group->get( 'id' ) );
						$groupUser->set( 'status', (int) $row->get( 'status', 1, GetterInterface::INT ) );

						if ( ! $groupUser->store() ) {
							if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
								var_dump( CBTxt::T( 'AUTO_ACTION_GROUPJIVE_FAILED', ':: Action [action] :: CB GroupJive failed to save. Error: [error]', array( '[action]' => (int) $trigger->get( 'id' ), '[error]' => $groupUser->getError() ) ) );
							}

							continue;
						}
					}
					break;
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
			$options	=	CBGroupJive::getCategoryOptions();
		}

		return $options;
	}

	/**
	 * @return array
	 */
	public function groups()
	{
		$options		=	array();

		if ( $this->installed() ) {
			$options	=	CBGroupJive::getGroupOptions();
		}

		return $options;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_CB_framework, $_PLUGINS;

		static $installed			=	null;

		if ( $installed === null ) {
			if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' ) ) {
				if ( file_exists( $_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler/plugin/user/plug_cbgroupjive/cbgroupjive.class.php' ) ) {
					$installed		=	false;
				} else {
					$installed		=	true;
				}
			} else {
				$installed			=	false;
			}
		}

		return $installed;
	}
}