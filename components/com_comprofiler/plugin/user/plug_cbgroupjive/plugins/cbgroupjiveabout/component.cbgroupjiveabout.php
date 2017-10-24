<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\PluginTable;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\GroupJive\CBGroupJive;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

class CBplug_cbgroupjiveabout extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_PLUGINS;

		$format					=	$this->input( 'format', null, GetterInterface::STRING );

		if ( $format != 'raw' ) {
			outputCbJs();
			outputCbTemplate();
		}

		$action					=	$this->input( 'action', null, GetterInterface::STRING );
		$function				=	$this->input( 'func', null, GetterInterface::STRING );
		$id						=	$this->input( 'id', 0, GetterInterface::INT );
		$user					=	CBuser::getMyUserDataInstance();

		if ( $format != 'raw' ) {
			ob_start();
		}

		switch ( $action ) {
			case 'about':
				switch ( $function ) {
					case 'edit':
						$this->showAboutEdit( $id, $user );
						break;
					case 'save':
						$this->saveAboutEdit( $id, $user );
						break;
				}
				break;
		}

		if ( $format != 'raw' ) {
			$html				=	ob_get_contents();
			ob_end_clean();

			static $gjClass		=	null;

			if ( $gjClass === null ) {
				$gjPlugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgroupjive' );
				$gjParams		=	$_PLUGINS->getPluginParams( $gjPlugin );
				$gjClass		=	$gjParams->get( 'general_class', '', GetterInterface::STRING );
			}

			$return				=	'<div class="cbGroupJive' . ( $gjClass ? ' ' . htmlspecialchars( $gjClass ) : null ) . '">'
								.		'<div class="cbGroupJiveInner">'
								.			$html
								.		'</div>'
								.	'</div>';

			echo $return;
		}
	}

	/**
	 * prepare frontend about edit render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showAboutEdit( $id, $user )
	{
		global $_CB_framework;

		$row					=	CBGroupJive::getGroup( $id );
		$returnUrl				=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );

		if ( CBGroupJive::canAccessGroup( $row, $user ) ) {
			if ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( CBGroupJive::getGroupStatus( $user, $row ) < 3 ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit about in this group.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		}

		CBGroupJive::getTemplate( 'about_edit', true, true, $this->element );

		$input					=	array();

		$about					=	$_CB_framework->displayCmsEditor( 'about', $this->input( 'post/about', $row->params()->get( 'about_content' ), GetterInterface::HTML ), '100%', null, 40, 15 );

		$input['about']			=	cbTooltip( null, CBTxt::T( 'Optionally input a detailed description about this group.' ), null, null, null, $about, null, 'style="display:block;"' );

		HTML_groupjiveAboutEdit::showAboutEdit( $row, $input, $user, $this );
	}

	/**
	 * save about
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveAboutEdit( $id, $user )
	{
		global $_CB_framework;

		$row			=	CBGroupJive::getGroup( $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'id' ) ) );

		if ( CBGroupJive::canAccessGroup( $row, $user ) ) {
			if ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( CBGroupJive::getGroupStatus( $user, $row ) < 3 ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit about in this group.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		}

		$row->params()->set( 'about_content', trim( $this->input( 'post/about', $row->params()->get( 'about_content' ), GetterInterface::HTML ) ) );

		$row->set( 'params', $row->params()->asJson() );

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_FAILED_TO_SAVE', 'Group failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showAboutEdit( $id, $user );
			return;
		}

		cbRedirect( $returnUrl, CBTxt::T( 'Group about saved successfully!' ) );
	}
}