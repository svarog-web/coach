<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveForums\Forum\Kunena;

use CBLib\Registry\Registry;
use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJiveForums\Forum\ForumInterface;
use CB\Plugin\GroupJiveForums\Table\Kunena\CategoryTable;
use CB\Plugin\GroupJiveForums\Table\Kunena\PostTable;
use CBLib\Registry\GetterInterface;

defined('CBLIB') or die();

class KunenaForum implements ForumInterface
{
	/** @var string  */
	public $type		=	'kunena';
	/** @var Registry  */
	public $params		=	null;

	public function __construct()
	{
		global $_PLUGINS;

		if ( $this->params === null ) {
			$plugin			=	$_PLUGINS->getLoadedPlugin( 'user/plug_cbgroupjive/plugins', 'cbgroupjiveforums' );

			$this->params	=	$_PLUGINS->getPluginParams( $plugin );
		}
	}

	/**
	 * Returns an array of available categories
	 *
	 * @return array
	 */
	public function getCategories()
	{
		$rows			=	\KunenaForumCategoryHelper::getChildren( 0, 10, array( 'action' => 'admin', 'unpublished' => true ) );
		$options		=	array();

		foreach ( $rows as $row ) {
			$options[]	=	\moscomprofilerHTML::makeOption( (string) $row->id, str_repeat( '- ', $row->level + 1  ) . ' ' . $row->name );
		}

		return $options;
	}

	/**
	 * Returns a forum category object
	 *
	 * @param int $id
	 * @return CategoryTable
	 */
	public function getCategory( $id )
	{
		if ( ! $id ) {
			return new CategoryTable();
		}

		static $cache		=	array();

		if ( ! isset( $cache[$id] ) ) {
			$row			=	new CategoryTable();

			$row->load( (int) $id );

			$cache[$id]		=	$row;
		}

		return $cache[$id];
	}

	/**
	 * Returns a display array of kunena topics for a group
	 *
	 * @param UserTable  $user
	 * @param GroupTable $group
	 * @param array      $counters
	 * @return array|null
	 */
	public function getTopics( $user, &$group, &$counters )
	{
		global $_CB_framework, $_CB_database;

		$categoryId					=	$group->params()->get( 'forum_id', 0, GetterInterface::INT );

		if ( ( ! $categoryId ) || ( ! $group->params()->get( 'forums', true, GetterInterface::BOOLEAN ) ) || ( $group->category()->get( 'id', 0, GetterInterface::INT ) && ( ! $group->category()->params()->get( 'forums', true, GetterInterface::BOOLEAN ) ) ) ) {
			return null;
		}

		CBGroupJive::getTemplate( 'forums', true, true, 'cbgroupjiveforums' );

		$prefix						=	'gj_group_' . $group->get( 'id', 0, GetterInterface::INT ) . '_forums_';
		$limit						=	$this->params->get( 'groups_forums_limit', 15, GetterInterface::INT );
		$limitstart					=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );
		$search						=	$_CB_framework->getUserStateFromRequest( $prefix . 'search{com_comprofiler}', $prefix . 'search' );
		$where						=	null;

		if ( $search && $this->params->get( 'groups_forums_search', true, GetterInterface::BOOLEAN ) ) {
			$where					.=	'( m.' . $_CB_database->NameQuote( 'subject' ) . ' LIKE ' . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
									.	' OR t.' . $_CB_database->NameQuote( 'message' ) . ' LIKE ' . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . ' )';
		}

		$searching					=	( $where ? true : false );

		$params						=	array(	'starttime'	=>	-1,
												'where'		=>	$where
											);

		$posts						=	\KunenaForumMessageHelper::getLatestMessages( $categoryId, 0, 0, $params );
		$total						=	array_shift( $posts );

		if ( ( ! $total ) && ( ! $searching ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'forums' ) ) ) {
			return null;
		}

		$pageNav					=	new \cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $prefix );

		switch( $this->params->get( 'groups_forums_orderby', 2, GetterInterface::INT ) ) {
			case 1:
				$params['orderby']	=	'm.' . $_CB_database->NameQuote( 'time' ) . ' ASC';
				break;
		}

		if ( $this->params->get( 'groups_forums_paging', true, GetterInterface::BOOLEAN ) ) {
			$posts					=	\KunenaForumMessageHelper::getLatestMessages( $categoryId, (int) $pageNav->limitstart, (int) $pageNav->limit, $params );
			$posts					=	array_pop( $posts );
		} else {
			$posts					=	array_pop( $posts );
		}

		$rows						=	array();

		/** @var \KunenaForumMessage[] $posts */
		foreach ( $posts as $post ) {
			$row					=	new PostTable();

			$row->post( $post );

			$rows[]					=	$row;
		}

		$input						=	array();

		$input['search']			=	'<input type="text" name="' . htmlspecialchars( $prefix ) . 'search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjGroupForumsForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Posts...' ) ) . '" class="form-control" />';

		CBGroupJive::preFetchUsers( $rows );

		$group->set( '_forums', $pageNav->total );

		return array(	'id'		=>	'forums',
						'title'		=>	CBTxt::T( 'Forums' ),
						'content'	=>	\HTML_groupjiveForums::showForums( $rows, $pageNav, $searching, $input, $counters, $group, $user, $this )
					);
	}
}