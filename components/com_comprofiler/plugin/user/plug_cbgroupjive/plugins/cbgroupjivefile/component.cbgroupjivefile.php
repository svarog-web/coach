<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJiveFile\CBGroupJiveFile;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

class CBplug_cbgroupjivefile extends cbPluginHandler
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
			case 'file':
				switch ( $function ) {
					case 'download':
						$this->outputFile( false, $id, $user );
						break;
					case 'publish':
						$this->stateFile( 1, $id, $user );
						break;
					case 'unpublish':
						$this->stateFile( 0, $id, $user );
						break;
					case 'delete':
						$this->deleteFile( $id, $user );
						break;
					case 'new':
						$this->showFileEdit( null, $user );
						break;
					case 'edit':
						$this->showFileEdit( $id, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveFileEdit( $id, $user );
						break;
					case 'preview':
					case 'show':
					default:
						$this->outputFile( true, $id, $user );
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
	 * prepare frontend file edit render
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function showFileEdit( $id, $user )
	{
		global $_CB_framework;

		$row							=	CBGroupJiveFile::getFile( (int) $id );
		$isModerator					=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$groupId						=	$this->input( 'group', null, GetterInterface::INT );

		if ( $groupId === null ) {
			$group						=	$row->group();
		} else {
			$group						=	CBGroupJive::getGroup( $groupId );
		}

		$returnUrl						=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		} elseif ( ! $isModerator ) {
			if ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'file' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to upload a file in this group.' ), 'error' );
			} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $group ) < 2 ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this file.' ), 'error' );
			}
		}

		CBGroupJive::getTemplate( 'file_edit', true, true, $this->element );

		$minFileSize					=	$this->params->get( 'groups_file_min_size', 0 );
		$maxFileSize					=	$this->params->get( 'groups_file_max_size', 1024 );
		$extensions						=	explode( ',', $this->params->get( 'groups_file_extensions', 'zip,rar,doc,pdf,txt,xls' ) );

		$fileValidation					=	array();

		if ( $minFileSize || $maxFileSize ) {
			$fileValidation[]			=	cbValidator::getRuleHtmlAttributes( 'filesize', array( $minFileSize, $maxFileSize, 'KB' ) );
		}

		if ( $extensions ) {
			$fileValidation[]			=	cbValidator::getRuleHtmlAttributes( 'extension', implode( ',', $extensions ) );
		}

		$input							=	array();

		$publishedTooltip				=	cbTooltip( null, CBTxt::T( 'Select publish state of this file. Unpublished files will not be visible to the public.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['published']				=	moscomprofilerHTML::yesnoSelectList( 'published', 'class="form-control"' . $publishedTooltip, (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) );

		$titleTooltup					=	cbTooltip( null, CBTxt::T( 'Optionally input a file title to display instead of filename.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['title']					=	'<input type="text" id="title" name="title" value="' . htmlspecialchars( $this->input( 'post/title', $row->get( 'title' ), GetterInterface::STRING ) ) . '" class="form-control" size="35"' . $titleTooltup . ' />';

		$fileTooltip					=	cbTooltip( null, CBTxt::T( 'Select the file to upload.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['file']					=	'<input type="file" id="file" name="file" value="" class="form-control' . ( ! $row->get( 'id' ) ? ' required' : null ) . '"' . ( $fileTooltip ? ' ' . $fileTooltip : null ) . ( $fileValidation ? implode( ' ', $fileValidation ) : null ) . ' />';

		$input['file_limits']			=	array();

		if ( $extensions ) {
			$input['file_limits'][]		=	CBTxt::T( 'GROUP_FILE_LIMITS_EXT', 'Your file must be of [ext] type.', array( '[ext]' => implode( ', ', $extensions ) ) );
		}

		if ( $minFileSize ) {
			$input['file_limits'][]		=	CBTxt::T( 'GROUP_FILE_LIMITS_MIN', 'Your file should exceed [size].', array( '[size]' => CBGroupJive::getFormattedFileSize( $minFileSize * 1024 ) ) );
		}

		if ( $maxFileSize ) {
			$input['file_limits'][]		=	CBTxt::T( 'GROUP_FILE_LIMITS_MAX', 'Your file should not exceed [size].', array( '[size]' => CBGroupJive::getFormattedFileSize( $maxFileSize * 1024 ) ) );
		}

		$descriptionTooltip				=	cbTooltip( null, CBTxt::T( 'Optionally input a file description.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['description']			=	'<textarea id="description" name="description" class="form-control" cols="40" rows="5"' . ( $descriptionTooltip ? ' ' . $descriptionTooltip : null ) . '>' . htmlspecialchars( $this->input( 'post/description', $row->get( 'description' ), GetterInterface::STRING ) ) . '</textarea>';

		$ownerTooltip					=	cbTooltip( null, CBTxt::T( 'Input the file owner id. File owner determines the creator of the file specified as User ID.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['user_id']				=	'<input type="text" id="user_id" name="user_id" value="' . (int) $this->input( 'post/user_id', $this->input( 'user', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ), GetterInterface::INT ) . '" class="digits required form-control" size="6"' . $ownerTooltip . ' />';

		HTML_groupjiveFileEdit::showFileEdit( $row, $input, $group, $user, $this );
	}

	/**
	 * save file
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveFileEdit( $id, $user )
	{
		global $_CB_framework, $_PLUGINS;

		$row					=	CBGroupJiveFile::getFile( (int) $id );
		$isModerator			=	CBGroupJive::isModerator( $user->get( 'id' ) );
		$groupId				=	$this->input( 'group', null, GetterInterface::INT );

		if ( $groupId === null ) {
			$group				=	$row->group();
		} else {
			$group				=	CBGroupJive::getGroup( $groupId );
		}

		$returnUrl				=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $group->get( 'id' ) ) );

		if ( ! CBGroupJive::canAccessGroup( $group, $user ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
		} elseif ( ! $isModerator ) {
			if ( ( ! $row->get( 'id' ) ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'file' ) ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to upload a file in this group.' ), 'error' );
			} elseif ( $row->get( 'id' ) && ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $group ) < 2 ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to edit this file.' ), 'error' );
			}
		}

		if ( $isModerator ) {
			$row->set( 'user_id', (int) $this->input( 'post/user_id', $row->get( 'user_id', $user->get( 'id' ) ), GetterInterface::INT ) );
		} else {
			$row->set( 'user_id', (int) $row->get( 'user_id', $user->get( 'id' ) ) );
		}

		$canModerate			=	( CBGroupJive::getGroupStatus( $user, $group ) >= 2 );

		$row->set( 'published', ( $isModerator || $canModerate || ( $row->get( 'id' ) && ( $row->get( 'published' ) != -1 ) ) || ( $group->params()->get( 'file', 1 ) != 2 ) ? (int) $this->input( 'post/published', $row->get( 'published', 1 ), GetterInterface::INT ) : -1 ) );
		$row->set( 'group', (int) $group->get( 'id' ) );
		$row->set( 'title', $this->input( 'post/title', $row->get( 'title' ), GetterInterface::STRING ) );
		$row->set( 'description', $this->input( 'post/description', $row->get( 'description' ), GetterInterface::STRING ) );

		if ( ( ! $isModerator ) && $this->params->get( 'groups_file_captcha', 0 ) ) {
			$_PLUGINS->loadPluginGroup( 'user' );

			$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

			if ( $_PLUGINS->is_errors() ) {
				$row->setError( $_PLUGINS->getErrorMSG() );
			}
		}

		$new					=	( $row->get( 'id' ) ? false : true );

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_FILE_FAILED_TO_SAVE', 'File failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showFileEdit( $id, $user );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'GROUP_FILE_FAILED_TO_SAVE', 'File failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showFileEdit( $id, $user );
			return;
		}

		if ( $new ) {
			$extras				=	array(	'file_id'			=>	(int) $row->get( 'id' ),
											'file_title'		=>	htmlspecialchars( ( $row->get( 'title' ) ? $row->get( 'title' ) : $row->name() ) ),
											'file_description'	=>	htmlspecialchars( $row->get( 'description' ) ),
											'file_size'			=>	$row->size(),
											'file_type'			=>	$row->mimeType(),
											'file_extension'	=>	$row->extension(),
											'file_url'			=>	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'file', 'func' => 'download', 'id' => (int) $row->get( 'id' ) ), 'raw', 0, true ),
											'file_date'			=>	cbFormatDate( $row->get( 'date' ), true, false, CBTxt::T( 'GROUP_FILE_DATE_FORMAT', 'M j, Y' ) ),
											'file'				=>	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ), 'tab' => 'grouptabfile' ) ) . '">' . htmlspecialchars( ( $row->get( 'title' ) ? $row->get( 'title' ) : $row->name() ) ) . '</a>' );

			if ( $row->get( 'published' ) == 1 ) {
				CBGroupJive::sendNotifications( 'file_new', CBTxt::T( 'New group file' ), CBTxt::T( '[user] has uploaded the file [file] in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 1, $extras );
			} elseif ( ( $row->get( 'published' ) == -1 ) && ( $row->group()->params()->get( 'file', 1 ) == 2 ) ) {
				CBGroupJive::sendNotifications( 'file_approve', CBTxt::T( 'New group file awaiting approval' ), CBTxt::T( '[user] has uploaded the file [file] in the group [group] and is awaiting approval!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 2, $extras );

				cbRedirect( $returnUrl, CBTxt::T( 'File uploaded successfully and awaiting approval!' ) );
			}

			cbRedirect( $returnUrl, CBTxt::T( 'File uploaded successfully!' ) );
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'File saved successfully!' ) );
		}
	}

	/**
	 * set file publish state status
	 *
	 * @param int       $state
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function stateFile( $state, $id, $user )
	{
		global $_CB_framework;

		$row				=	CBGroupJiveFile::getFile( (int) $id );
		$returnUrl			=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 2 ) {
					if ( ( $user->get( 'id' ) == $row->get( 'user_id' ) ) && ( $row->get( 'published' ) == -1 ) && ( $row->group()->params()->get( 'file', 1 ) == 2 ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'Your file is awaiting approval.' ), 'error' );
					} elseif ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) ) {
						cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to publish or unpublish this file.' ), 'error' );
					}
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'File does not exist.' ), 'error' );
		}

		$currentState		=	(int) $row->get( 'published' );

		$row->set( 'published', (int) $state );

		if ( $row->getError() || ( ! $row->store() ) ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_FILE_STATE_FAILED_TO_SAVE', 'File state failed to saved. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $state && ( $currentState == -1 ) ) {
			$extras			=	array(	'file_id'			=>	(int) $row->get( 'id' ),
										'file_title'		=>	htmlspecialchars( ( $row->get( 'title' ) ? $row->get( 'title' ) : $row->name() ) ),
										'file_description'	=>	htmlspecialchars( $row->get( 'description' ) ),
										'file_size'			=>	$row->size(),
										'file_type'			=>	$row->mimeType(),
										'file_extension'	=>	$row->extension(),
										'file_url'			=>	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'file', 'func' => 'download', 'id' => (int) $row->get( 'id' ) ), 'raw', 0, true ),
										'file_date'			=>	cbFormatDate( $row->get( 'date' ), true, false, CBTxt::T( 'GROUP_FILE_DATE_FORMAT', 'M j, Y' ) ),
										'file'				=>	'<a href="' . $_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ), 'tab' => 'grouptabfile' ) ) . '">' . htmlspecialchars( ( $row->get( 'title' ) ? $row->get( 'title' ) : $row->name() ) ) . '</a>' );

			if ( $row->get( 'user_id' ) != $user->get( 'id' ) ) {
				CBGroupJive::sendNotification( 4, $user, (int) $row->get( 'user_id' ), CBTxt::T( 'File upload request accepted' ), CBTxt::T( 'Your file [file] upload request in the group [group] has been accepted!' ), $row->group(), $extras );
			}

			CBGroupJive::sendNotifications( 'file_new', CBTxt::T( 'New group file' ), CBTxt::T( '[user] has uploaded the file [file] in the group [group]!' ), $row->group(), (int) $row->get( 'user_id' ), null, array( $user->get( 'id' ) ), 1, $extras );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'File state saved successfully!' ) );
	}

	/**
	 * delete file
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteFile( $id, $user )
	{
		global $_CB_framework;

		$row			=	CBGroupJiveFile::getFile( (int) $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $user->get( 'id' ) != $row->get( 'user_id' ) ) && ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 2 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have sufficient permissions to delete this file.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'File does not exist.' ), 'error' );
		}

		if ( ! $row->canDelete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_FILE_FAILED_TO_DELETE', 'File failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( ! $row->delete() ) {
			cbRedirect( $returnUrl, CBTxt::T( 'GROUP_FILE_FAILED_TO_DELETE', 'File failed to delete. Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $returnUrl, CBTxt::T( 'File deleted successfully!' ) );
	}

	/**
	 * output file
	 *
	 * @param bool      $preview
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function outputFile( $preview, $id, $user )
	{
		global $_CB_framework;

		$row			=	CBGroupJiveFile::getFile( (int) $id );
		$returnUrl		=	$_CB_framework->pluginClassUrl( 'cbgroupjive', false, array( 'action' => 'groups', 'func' => 'show', 'id' => (int) $row->get( 'group' ) ) );

		if ( $row->get( 'id' ) ) {
			if ( ! CBGroupJive::canAccessGroup( $row->group(), $user ) ) {
				cbRedirect( $returnUrl, CBTxt::T( 'Group does not exist.' ), 'error' );
			} elseif ( ! CBGroupJive::isModerator( $user->get( 'id' ) ) ) {
				if ( ( $row->get( 'published' ) != 1 ) && ( CBGroupJive::getGroupStatus( $user, $row->group() ) < 2 ) ) {
					cbRedirect( $returnUrl, CBTxt::T( 'You do not have access to this file.' ), 'error' );
				}
			}
		} else {
			cbRedirect( $returnUrl, CBTxt::T( 'File does not exist.' ), 'error' );
		}

		if ( $preview ) {
			$row->preview();
		} else {
			$row->download();
		}
	}
}