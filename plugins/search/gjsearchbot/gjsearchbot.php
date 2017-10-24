<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;
use CB\Database\Table\PluginTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\GroupTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.plugin.plugin' );

class plgSearchgjsearchbot extends JPlugin
{
	/** @var PluginTable  */
	public $_gjPlugin	=	null;
	/** @var Registry  */
	public $_gjParams	=	null;

	/**
	 * @param object $subject
	 * @param array  $config
	 */
	public function __construct( &$subject, $config )
	{
		global $_PLUGINS;

		parent::__construct( $subject, $config );

		static $CB_loaded		=	0;

		if ( ! $CB_loaded++ ) {
			if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
				return;
			}

			include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

			cbimport( 'cb.html' );
			cbimport( 'language.front' );

			$_PLUGINS->loadPluginGroup( 'user' );

			$this->_gjPlugin	=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );

			if ( ! $this->_gjPlugin ) {
				return;
			}

			$this->_gjParams	=	$_PLUGINS->getPluginParams( $this->_gjPlugin );
		}
	}

	/**
	 * @return array|null
	 */
	public function onContentSearchAreas()
	{
		if ( ! $this->_gjPlugin ) {
			return null;
		}

		static $areas						=	null;

		if ( ! isset( $areas ) ) {
			$areas							=	array();

			if ( $this->getCategorySearching() ) {
				$categoryArea				=	$this->params->get( 'search_category_area', 'Categories' );

				if ( ! $categoryArea ) {
					$categoryArea			=	'Categories';
				}

				$areas['gj_categories']		=	CBTxt::T( $categoryArea );
			}

			if ( $this->getGroupSearching() ) {
				$groupArea					=	$this->params->get( 'search_group_area', 'Groups' );

				if ( ! $groupArea ) {
					$groupArea				=	'Groups';
				}

				$areas['gj_groups']			=	CBTxt::T( $groupArea );
			}
		}

		return $areas;
	}

	/**
	 * @param string $text
	 * @param string $phrase
	 * @param string $ordering
	 * @param null   $areas
	 * @return array|null
	 */
	public function onContentSearch( $text, $phrase = '', $ordering = '', $areas = null )
	{
		global $_CB_database, $_CB_framework;

		if ( ( ( ! $this->getCategorySearching() ) && ( ! $this->getGroupSearching() ) ) || ( ! $text ) || ( ! $this->_gjPlugin ) ) {
			return array();
		}

		$user							=	CBuser::getMyUserDataInstance();
		$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$results						=	array();

		$excludeCategories				=	$this->params->get( 'search_category_exclude', null );

		if ( $excludeCategories ) {
			$excludeCategories			=	explode( '|*|', $excludeCategories );
		}

		$excludeGroups					=	$this->params->get( 'search_group_exclude', null );

		if ( $excludeGroups ) {
			$excludeGroups				=	explode( '|*|', $excludeGroups );
		}

		if ( $this->getCategorySearching() ) {
			$resultTitle				=	$this->params->get( 'results_category_title', '[name]' );
			$resultText					=	$this->params->get( 'results_category_text', '[description]' );
			$resultsLimit				=	(int) $this->params->get( 'results_category_limit', 50 );
			$resultsLinks				=	(int) $this->params->get( 'results_category_link', 0 );

			switch( $phrase ) {
				case 'exact':
					$where				=	"\n WHERE ( c." . $_CB_database->NameQuote( 'name' ) . " = " . $_CB_database->Quote( $text )
										.	" OR c." . $_CB_database->NameQuote( 'description' ) . " = " . $_CB_database->Quote( $text ) . " )";
					break;
				case 'any':
				case 'all':
				default:
					$words				=	explode( ' ', $text );
					$search				=	array();

					foreach ( $words as $word ) {
						$search[]		=	"( c." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $word, true ) . '%', false )
										.	" OR c." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $word, true ) . '%', false ) . " )";
					}

					$where				=	"\n WHERE ( " . implode( ( $phrase == 'any' ? " OR " : " AND " ), $search ) . " )";
					break;
			}

			switch( $ordering ) {
				case 'oldest':
					$orderBy			=	'c.' . $_CB_database->NameQuote( 'ordering' ) . ' ASC';
					break;
				case 'popular':
					$orderBy			=	$_CB_database->NameQuote( '_groups' ) . ' DESC';
					break;
				case 'alpha':
					$orderBy			=	'c.' . $_CB_database->NameQuote( 'name' ) . ' ASC';
					break;
				case 'newest':
				case 'category':
				default:
					$orderBy			=	'c.' . $_CB_database->NameQuote( 'ordering' ) . ' DESC';
					break;
			}

			$groups						=	null;

			if ( $ordering == 'popular' ) {
				$groups					=	'SELECT COUNT(*)'
										.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
										.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'user_id' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
										.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' );

				if ( ! $isModerator ) {
					$groups				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
										.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $user->get( 'id' )
										.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
										.	' ON i.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
										.	' AND i.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
										.	' AND ( ( i.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) )
										.	' AND i.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
										.	' OR ( i.' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' )
										.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )';
				}

				$groups					.=	"\n WHERE g." . $_CB_database->NameQuote( 'category' ) . " = c." . $_CB_database->NameQuote( 'id' )
										.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
										.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
										.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

				if ( ! $isModerator ) {
					$groups				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
										.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
										.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
										.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
										.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )';
				}
			}

			$query						=	'SELECT c.*'
										.	( $ordering == 'popular' ? ', ( ' . $groups . ' ) AS _groups' : null )
										.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c"
										.	$where;

			if ( ! $isModerator ) {
				$query					.=	"\n AND c." . $_CB_database->NameQuote( 'published' ) . " = 1"
										.	"\n AND c." . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( $user->get( 'id' ) ) );
			}

			$query						.=	( $excludeCategories ? "\n AND c." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeCategories ) : null )
										.	"\n ORDER BY " . $orderBy;
			if ( $resultsLimit ) {
				$_CB_database->setQuery( $query, 0, $resultsLimit );
			} else {
				$_CB_database->setQuery( $query );
			}
			$rows						=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\CategoryTable', array( $_CB_database ) );

			/** @var CategoryTable[] $rows */
			foreach ( $rows as $row ) {
				$url					=	$_CB_framework->pluginClassUrl( $this->_gjPlugin->element, true, array( 'action' => 'categories', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );
				$extras					=	array(	'[id]'			=>	$row->get( 'id' ),
													'[name]'		=>	CBTxt::T( $row->get( 'name' ) ),
													'[description]'	=>	CBTxt::T( $row->get( 'description' ) ),
													'[logo]'		=>	$row->logo( true, false, true ),
													'[canvas]'		=>	$row->canvas( true ),
													'[url]'			=>	$url
											);

				$result					=	new stdClass();
				$result->href			=	$url;
				$result->title			=	CBTxt::T( 'CATEGORY_SEARCH_TITLE', $resultTitle, $extras );
				$result->text			=	CBTxt::T( 'CATEGORY_SEARCH_TEXT', $resultText, $extras );
				$result->created		=	null;
				$result->browsernav		=	$resultsLinks;
				$result->section		=	0;

				$results[]				=	$result;
			}
		}

		if ( $this->getGroupSearching() ) {
			$resultTitle				=	$this->params->get( 'results_group_title', '[name]' );
			$resultText					=	$this->params->get( 'results_group_text', '[description]' );
			$resultsLimit				=	(int) $this->params->get( 'results_group_limit', 50 );
			$resultsLinks				=	(int) $this->params->get( 'results_group_link', 0 );

			switch( $phrase ) {
				case 'exact':
					$where				=	"\n AND ( g." . $_CB_database->NameQuote( 'name' ) . " = " . $_CB_database->Quote( $text )
										.	" OR g." . $_CB_database->NameQuote( 'description' ) . " = " . $_CB_database->Quote( $text ) . " )";
					break;
				case 'any':
				case 'all':
				default:
					$words				=	explode( ' ', $text );
					$search				=	array();

					foreach ( $words as $word ) {
						$search[]		=	"( g." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $word, true ) . '%', false )
										.	" OR g." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $word, true ) . '%', false ) . " )";
					}

					$where				=	"\n AND ( " . implode( ( $phrase == 'any' ? " OR " : " AND " ), $search ) . " )";
					break;
			}

			switch( $ordering ) {
				case 'oldest':
					$orderBy			=	'g.' . $_CB_database->NameQuote( 'date' ) . ' ASC';
					break;
				case 'popular':
					$orderBy			=	$_CB_database->NameQuote( '_users' ) . ' DESC';
					break;
				case 'alpha':
					$orderBy			=	'g.' . $_CB_database->NameQuote( 'name' ) . ' ASC';
					break;
				case 'category':
					$orderBy			=	'c.' . $_CB_database->NameQuote( 'ordering' ) . ' ASC';
					break;
				case 'newest':
				default:
					$orderBy			=	'g.' . $_CB_database->NameQuote( 'date' ) . ' DESC';
					break;
			}

			$users						=	null;

			if ( $ordering == 'popular' ) {
				$users					=	'SELECT COUNT(*)'
										.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS uc"
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS uccb"
										.	' ON uccb.' . $_CB_database->NameQuote( 'id' ) . ' = uc.' . $_CB_database->NameQuote( 'user_id' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS ucj"
										.	' ON ucj.' . $_CB_database->NameQuote( 'id' ) . ' = uccb.' . $_CB_database->NameQuote( 'id' )
										.	"\n WHERE uc." . $_CB_database->NameQuote( 'group' ) . " = g." . $_CB_database->NameQuote( 'id' )
										.	"\n AND uccb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
										.	"\n AND uccb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
										.	"\n AND ucj." . $_CB_database->NameQuote( 'block' ) . " = 0";

				if ( ! $isModerator ) {
					$users				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
										.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' >= 2'
										.		' OR uc.' . $_CB_database->NameQuote( 'status' ) . ' >= 1 )';
				}

				if ( ! $this->_gjParams->get( 'groups_users_owner', 1 ) ) {
					$users				.=	"\n AND uc." . $_CB_database->NameQuote( 'status' ) . " != 4";
				}
			}

			$query						=	'SELECT g.*'
										.	', c.' . $_CB_database->NameQuote( 'name' ) . ' AS _category_name'
										.	( $ordering == 'popular' ? ', ( ' . $users . ' ) AS _users' : null )
										.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_groups' ) . " AS g"
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
										.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'user_id' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
										.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_categories' ) . " AS c"
										.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = g.' . $_CB_database->NameQuote( 'category' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_users' ) . " AS u"
										.	' ON u.' . $_CB_database->NameQuote( 'user_id' ) . ' = ' . (int) $user->get( 'id' )
										.	' AND u.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
										.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__groupjive_invites' ) . " AS i"
										.	' ON i.' . $_CB_database->NameQuote( 'group' ) . ' = g.' . $_CB_database->NameQuote( 'id' )
										.	' AND i.' . $_CB_database->NameQuote( 'accepted' ) . ' = ' . $_CB_database->Quote( '0000-00-00 00:00:00' )
										.	' AND ( ( i.' . $_CB_database->NameQuote( 'email' ) . ' = ' . $_CB_database->Quote( $user->get( 'email' ) )
										.	' AND i.' . $_CB_database->NameQuote( 'email' ) . ' != "" )'
										.	' OR ( i.' . $_CB_database->NameQuote( 'user' ) . ' = ' . (int) $user->get( 'id' )
										.	' AND i.' . $_CB_database->NameQuote( 'user' ) . ' > 0 ) )'
										.	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
										.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
										.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

			if ( ! $isModerator ) {
				$query				.=	"\n AND ( g." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
									.		' OR ( g.' . $_CB_database->NameQuote( 'published' ) . ' = 1'
									.		' AND ( g.' . $_CB_database->NameQuote( 'type' ) . ' IN ( 1, 2 )'
									.		' OR u.' . $_CB_database->NameQuote( 'status' ) . ' IN ( 0, 1, 2, 3 )'
									.		' OR i.' . $_CB_database->NameQuote( 'id' ) . ' IS NOT NULL ) ) )'
									.	"\n AND ( ( c." . $_CB_database->NameQuote( 'published' ) . " = 1"
									.		' AND c.' . $_CB_database->NameQuote( 'access' ) . ' IN ' . $_CB_database->safeArrayOfIntegers( CBGroupJive::getAccess( (int) $user->get( 'id' ) ) ) . ' )'
									.		( $this->_gjParams->get( 'groups_uncategorized', 1 ) ? ' OR g.' . $_CB_database->NameQuote( 'category' ) . ' = 0 )' : ' )' );
			}

			$query					.=	$where
									.	( $excludeCategories ? "\n AND c." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeCategories ) : null )
									.	( $excludeGroups ? "\n AND g." . $_CB_database->NameQuote( 'id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( $excludeGroups ) : null )
									.	"\n ORDER BY " . $orderBy;
			if ( $resultsLimit ) {
				$_CB_database->setQuery( $query, 0, $resultsLimit );
			} else {
				$_CB_database->setQuery( $query );
			}
			$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJive\Table\GroupTable', array( $_CB_database ) );

			/** @var GroupTable[] $rows */
			foreach ( $rows as $row ) {
				$url					=	$_CB_framework->pluginClassUrl( $this->_gjPlugin->element, true, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );
				$extras					=	array(	'[id]'			=>	$row->get( 'id' ),
													'[name]'		=>	CBTxt::T( $row->get( 'name' ) ),
													'[description]'	=>	CBTxt::T( $row->get( 'description' ) ),
													'[logo]'		=>	$row->logo( true, false, true ),
													'[canvas]'		=>	$row->canvas( true ),
													'[url]'			=>	$url,
													'[date]'		=>	cbFormatDate( $row->get( 'date' ) )
											);

				$result					=	new stdClass();
				$result->href			=	$url;
				$result->title			=	CBTxt::T( 'GROUP_SEARCH_TITLE', $resultTitle, $extras );
				$result->text			=	CBTxt::T( 'GROUP_SEARCH_TEXT', $resultText, $extras );
				$result->created		=	$row->get( 'date' );
				$result->browsernav		=	$resultsLinks;
				$result->section		=	0;

				$results[]				=	$result;
			}
		}

		return $results;
	}

	/**
	 * @param array $areas
	 * @return bool
	 */
	private function getCategorySearching( $areas = array() )
	{
		if ( $this->params->get( 'search_category_enable', 0 ) ) {
			if ( $areas && ( ! in_array( 'gj_categories', $areas ) ) ) {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * @param array $areas
	 * @return bool
	 */
	private function getGroupSearching( $areas = array() )
	{
		if ( $this->params->get( 'search_group_enable', 1 ) ) {
			if ( $areas && ( ! in_array( 'gj_groups', $areas ) ) ) {
				return false;
			}

			return true;
		}

		return false;
	}
}