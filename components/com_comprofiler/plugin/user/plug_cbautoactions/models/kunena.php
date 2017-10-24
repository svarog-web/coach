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

class cbautoactionsActionKunena extends cbPluginHandler
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
				var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NOT_INSTALLED', ':: Action [action] :: Kunena is not installed', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
			}

			return;
		}

		foreach ( $trigger->getParams()->subTree( 'kunena' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner								=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner							=	(int) $user->get( 'id' );
			} else {
				$owner							=	(int) $trigger->getSubstituteString( $owner );
			}

			switch ( $row->get( 'mode', 'category', GetterInterface::STRING ) ) {
				case 'sync':
					if ( ! $user->get( 'id' ) ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_USER', ':: Action [action] :: Kunena skipped due to no user', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$kunenaUser					=	KunenaUserHelper::get( (int) $user->get( 'id' ) );

					$kunenaUser->set( 'name', $user->get( 'name' ) );
					$kunenaUser->set( 'username', $user->get( 'username' ) );
					$kunenaUser->set( 'email', $user->get( 'email' ) );

					foreach ( $row->subTree( 'fields' ) as $r ) {
						/** @var ParamsInterface $r */
						$field					=	$r->get( 'field', null, GetterInterface::STRING );

						if ( $field ) {
							$kunenaUser->set( $field, $trigger->getSubstituteString( $r->get( 'value', null, GetterInterface::RAW ), false, $r->get( 'translate', false, GetterInterface::BOOLEAN ) ) );
						}
					}

					$kunenaUser->save();
					break;
				case 'reply':
					if ( ! $owner ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_OWNER', ':: Action [action] :: Kunena skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$message					=	$trigger->getSubstituteString( $row->get( 'message', null, GetterInterface::RAW ), false );

					if ( ! $message ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_MSG', ':: Action [action] :: Kunena skipped due to missing message', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$topicId					=	(int) $row->get( 'topic', 0, GetterInterface::INT );

					if ( ! $topicId ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_TOPIC', ':: Action [action] :: Kunena skipped due to missing topic', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$subject					=	$trigger->getSubstituteString( $row->get( 'subject', null, GetterInterface::STRING ) );

					$topic						=	KunenaForumMessageHelper::get( $topicId );

					$fields						=	array( 'message' => $message );

					if ( $subject ) {
						$fields['subject']		=	$subject;
					}

					$topic->newReply( $fields, $owner );
					break;
				case 'topic':
					if ( ! $owner ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_OWNER', ':: Action [action] :: Kunena skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$subject					=	$trigger->getSubstituteString( $row->get( 'subject', null, GetterInterface::STRING ) );

					if ( ! $subject ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_SUBJ', ':: Action [action] :: Kunena skipped due to missing subject', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$message					=	$trigger->getSubstituteString( $row->get( 'message', null, GetterInterface::RAW ), false );

					if ( ! $message ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_MSG', ':: Action [action] :: Kunena skipped due to missing message', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$categoryId					=	(int) $row->get( 'category', 0, GetterInterface::INT );

					if ( ! $categoryId ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_CAT', ':: Action [action] :: Kunena skipped due to missing category', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$category					=	KunenaForumCategoryHelper::get( $categoryId );

					$fields						=	array(	'catid' => $categoryId,
															'subject' => $subject,
															'message' => $message
														);

					$category->newTopic( $fields, $owner );
					break;
				case 'category':
				default:
					$name						=	$trigger->getSubstituteString( $row->get( 'name', null, GetterInterface::STRING ) );

					if ( ! $name ) {
						if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
							var_dump( CBTxt::T( 'AUTO_ACTION_KUNENA_NO_NAME', ':: Action [action] :: Kunena skipped due to missing name', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
						}

						continue;
					}

					$query						=	'SELECT ' . $_CB_database->NameQuote( 'id' )
												.	"\n FROM " . $_CB_database->NameQuote( '#__kunena_categories' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'name' ) . " = " . $_CB_database->Quote( $name );
					$_CB_database->setQuery( $query );
					if ( ! $_CB_database->loadResult() ) {
						$category				=	KunenaForumCategoryHelper::get();

						$category->set( 'parent_id', (int) $row->get( 'parent', 0, GetterInterface::INT ) );
						$category->set( 'name', $name );
						$category->set( 'alias', KunenaRoute::stringURLSafe( $name ) );
						$category->set( 'accesstype', 'joomla.group' );
						$category->set( 'access', (int) $row->get( 'access', 1, GetterInterface::INT ) );
						$category->set( 'published', (int) $row->get( 'published', 1, GetterInterface::INT ) );
						$category->set( 'description', $trigger->getSubstituteString( $row->get( 'description', null, GetterInterface::STRING ) ) );

						if ( $category->save() && $owner ) {
							$category->addModerator( (int) $owner );
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
		$options			=	array();

		if ( $this->installed() ) {
			$rows			=	KunenaForumCategoryHelper::getChildren( 0, 10 );

			if ( $rows ) foreach ( $rows as $row ) {
				$options[]	=	moscomprofilerHTML::makeOption( (string) $row->id, str_repeat( '- ', $row->level + 1  ) . ' ' . $row->name );
			}
		}

		return $options;
	}

	/**
	 * @return array
	 */
	public function topics()
	{
		$options			=	array();

		if ( $this->installed() ) {
			$rows			=	KunenaForumTopicHelper::getLatestTopics();

			if ( $rows[1] ) foreach ( $rows[1] as $row ) {
				$options[]	=	moscomprofilerHTML::makeOption( (string) $row->id, $row->subject );
			}
		}

		return $options;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_CB_framework;

		$api	=	$_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_kunena/api.php';

		if ( file_exists( $api ) ) {
			require_once( $api );

			if ( class_exists( 'KunenaForum' ) && class_exists( 'KunenaForumCategoryHelper' ) && class_exists( 'KunenaForumTopicHelper' ) && class_exists( 'KunenaUserHelper' ) ) {
				return true;
			}
		}

		return false;
	}
}