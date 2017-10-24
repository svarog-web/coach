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

class cbautoactionsActionContent extends cbPluginHandler
{

	/**
	 * @param cbautoactionsActionTable $trigger
	 * @param UserTable $user
	 */
	public function execute( $trigger, $user )
	{
		global $_CB_framework;

		foreach ( $trigger->getParams()->subTree( 'content' ) as $row ) {
			/** @var ParamsInterface $row */
			$mode					=	(int) $row->get( 'mode', 1, GetterInterface::INT );
			$title					=	$trigger->getSubstituteString( $row->get( 'title', null, GetterInterface::STRING ) );
			$alias					=	$row->get( 'alias', null, GetterInterface::STRING );

			if ( ! $alias ) {
				$alias				=	$title;
			} else {
				$alias				=	$trigger->getSubstituteString( $alias );
			}

			$alias					=	$this->cleanAlias( $alias );
			$introText				=	$trigger->getSubstituteString( $row->get( 'introtext', null, GetterInterface::RAW ), false );
			$fullText				=	$trigger->getSubstituteString( $row->get( 'fulltext', null, GetterInterface::RAW ), false );
			$metaDesc				=	$trigger->getSubstituteString( $row->get( 'metadesc', null, GetterInterface::RAW ), false );
			$metaKey				=	$trigger->getSubstituteString( $row->get( 'metakey', null, GetterInterface::RAW ), false );
			$access					=	(int) $row->get( 'access', 1, GetterInterface::INT );
			$published				=	(int) $row->get( 'published', 1, GetterInterface::INT );
			$featured				=	(int) $row->get( 'featured', 0, GetterInterface::INT );
			$language				=	$row->get( 'language', '*', GetterInterface::STRING );
			$owner					=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner				=	(int) $user->get( 'id' );
			} else {
				$owner				=	(int) $trigger->getSubstituteString( $owner );
			}

			if ( ! $owner ) {
				if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
					var_dump( CBTxt::T( 'AUTO_ACTION_CONTENT_NO_OWNER', ':: Action [action] :: Content skipped due to missing owner', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
				}

				continue;
			}

			if ( $mode == 1 ) {
				JTable::addIncludePath( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_content/tables' );

				$category			=	(int) $row->get( 'category_j', null, GetterInterface::INT );
				$table				=	JTable::getInstance( 'content' );

				while ( $table->load( array( 'alias' => $alias, 'catid' => $category ) ) ) {
					$matches		=	null;

					if ( preg_match( '#-(\d+)$#', $alias, $matches ) ) {
						$alias		=	preg_replace( '#-(\d+)$#', '-' . ( $matches[1] + 1 ) . '', $alias );
					} else {
						$alias		.=	'-2';
					}
				}

				/** @var JTableContent $article */
				$article			=	JTable::getInstance( 'content' );

				$article->set( 'created_by', $owner );
				$article->set( 'title', $title );
				$article->set( 'alias', $alias );
				$article->set( 'introtext', $introText );
				$article->set( 'fulltext', $fullText );
				$article->set( 'metadesc', $metaDesc );
				$article->set( 'metakey', $metaKey );
				$article->set( 'catid', $category );
				$article->set( 'access', $access );
				$article->set( 'state', $published );
				$article->set( 'featured', $featured );
				$article->set( 'ordering', 1 );
				$article->set( 'created', $_CB_framework->getUTCDate() );
				$article->set( 'language', $language );

				$article->set( 'images', '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}' );
				$article->set( 'urls', '{"urla":null,"urlatext":"","targeta":"","urlb":null,"urlbtext":"","targetb":"","urlc":null,"urlctext":"","targetc":""}' );
				$article->set( 'attribs', '{"show_title":"","link_titles":"","show_tags":"","show_intro":"","info_block_position":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}' );
				$article->set( 'metadata', '{"robots":"","author":"","rights":"","xreference":"","tags":""}' );

				if ( ! $article->store() ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_CONTENT_FAILED', ':: Action [action] :: Content failed to save', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
					}

					continue;
				}

				$article->reorder( '`catid` = ' . $category );

				if ( $article->get( 'featured' ) ) {
					$feature	=	JTable::getInstance( 'Featured', 'ContentTable' );

					$feature->set( 'content_id', (int) $article->get( 'id' ) );
					$feature->set( 'ordering', 0 );

					if ( $feature->store() ) {
						$feature->reorder();
					}
				}
			} elseif ( ( $mode == 2 ) && $this->isK2Installed() ) {
				$category			=	(int) $row->get( 'category_k', null, GetterInterface::INT );

				/** @var TableK2Item $article */
				$article			=	JTable::getInstance( 'K2Item', 'Table' );

				$article->set( 'created_by', $owner );
				$article->set( 'title', $title );
				$article->set( 'alias', $alias );
				$article->set( 'introtext', $introText );
				$article->set( 'fulltext', $fullText );
				$article->set( 'metadesc', $metaDesc );
				$article->set( 'metakey', $metaKey );
				$article->set( 'catid', $category );
				$article->set( 'access', $access );
				$article->set( 'published', $published );
				$article->set( 'featured', $featured );
				$article->set( 'ordering', 1 );
				$article->set( 'created', $_CB_framework->getUTCDate() );
				$article->set( 'language', $language );

				if ( ! $article->store() ) {
					if ( $trigger->getParams()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
						var_dump( CBTxt::T( 'AUTO_ACTION_CONTENT_FAILED', ':: Action [action] :: Content failed to save', array( '[action]' => (int) $trigger->get( 'id' ) ) ) );
					}

					continue;
				}

				$article->reorder( '`catid` = ' . $category );
			}
		}
	}

	/**
	 * @return array
	 */
	public function k2Categories()
	{
		global $_CB_framework;

		$listCategories				=	array();

		if ( $this->isK2Installed() ) {
			require_once( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_k2/models/categories.php' );

			$categoryModel			=	new K2ModelCategories();

			$categories				=	$categoryModel->categoriesTree( null, true, true );

			if ( $categories ) foreach ( $categories as $category ) {
				$listCategories[]	=	moscomprofilerHTML::makeOption( (string) $category->value, $category->text );
			}
		}

		return $listCategories;
	}

	/**
	 * @return bool
	 */
	private function isK2Installed()
	{
		global $_CB_framework;

		if ( is_dir( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_k2' ) && class_exists( 'K2Model' ) ) {
			JTable::addIncludePath( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_k2/tables' );

			return true;
		}

		return false;
	}

	/**
	 * @param string $title
	 * @return string
	 */
	private function cleanAlias( $title )
	{
		$alias	=	str_replace( '-', ' ', $title );
		$alias	=	trim( cbIsoUtf_strtolower( $alias ) );
		$alias	=	preg_replace( '/(\s|[^A-Za-z0-9\-])+/', '-', $alias );
		$alias	=	trim( $alias, '-' );

		return $alias;
	}
}