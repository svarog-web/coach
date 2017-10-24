<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Comments;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityComments
{

	/**
	 * @param CommentTable[]  $rows
	 * @param Comments        $stream
	 * @param int             $output 0: Normal, 1: Raw, 2: Inline, 3: Load, 4: Save
	 * @param UserTable       $user
	 * @param UserTable       $viewer
	 * @param cbPluginHandler $plugin
	 * @return null|string
	 */
	static public function showComments( $rows, $stream, $output, $user, $viewer, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		CBActivity::loadHeaders( $output );

		static $loaded					=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.commentsStream' ).cbactivity();" );
		}

		$canCreate						=	CBActivity::canCreate( $user, $viewer, $stream );
		$cbModerator					=	CBActivity::isModerator( (int) $viewer->get( 'id' ) );
		$sourceClean					=	htmlspecialchars( $stream->source() );
		$newForm						=	null;
		$moreButton						=	null;

		if ( ( $output != 4 ) && $stream->get( 'paging' ) && $stream->get( 'limit' ) && $rows ) {
			$moreButton					=	'<a href="' . $stream->endpoint( 'show', array( 'limitstart' => ( $stream->get( 'limitstart' ) + $stream->get( 'limit' ) ), 'limit' => $stream->get( 'limit' ) ) ) . '" class="commentButton commentButtonMore streamMore">' . ( $stream->get( 'type' ) == 'comment' ? CBTxt::T( 'Show more replies' ) : CBTxt::T( 'Show more comments' ) ) . '</a>';
		}

		$return							=	null;

		$_PLUGINS->trigger( 'activity_onBeforeDisplayComments', array( &$return, &$rows, $stream, $output ) );

		if ( ! in_array( $output, array( 1, 4 ) ) ) {
			$return						.=	'<div class="' . $sourceClean . 'Comments commentsStream streamContainer ' . ( $stream->direction() ? 'streamContainerUp' : 'streamContainerDown' ) . '" data-cbactivity-stream="' . base64_encode( (string) $stream ) . '" data-cbactivity-direction="' . (int) $stream->direction() . '">';

			if ( ( $stream->source() != 'hidden' ) && ( ! $stream->get( 'id' ) ) && ( ! $stream->get( 'filter' ) ) ) {
				$newForm				=	self::showNew( $stream, $output, $user, $viewer, $plugin );
			}
		}

		$return							.=		( $stream->direction() ? $moreButton : $newForm )
										.		( ! in_array( $output, array( 1, 4 ) ) ? '<div class="' . $sourceClean . 'CommentsItems commentsStreamItems streamItems">' : null );

		if ( $rows ) foreach ( $rows as $row ) {
			$rowId						=	$stream->id() . '_' . (int) $row->get( 'id' );
			$rowOwner					=	( $viewer->get( 'id' ) == $row->get( 'user_id' ) );
			$typeClass					=	( $row->get( 'type' ) ? ucfirst( strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $row->get( 'type' ) ) ) ) : null );
			$subTypeClass				=	( $row->get( 'subtype' ) ? ucfirst( strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $row->get( 'subtype' ) ) ) ) : null );

			$cbUser						=	CBuser::getInstance( (int) $row->get( 'user_id' ), false );
			$message					=	( $row->get( 'message' ) ? htmlspecialchars( $row->get( 'message' ) ) : null );
			$date						=	null;
			$insert						=	null;
			$footer						=	null;
			$menu						=	array();
			$extras						=	array();

			$_PLUGINS->trigger( 'activity_onDisplayComment', array( &$row, &$message, &$insert, &$date, &$footer, &$menu, &$extras, $stream, $output ) );

			$message					=	$stream->parser( $message )->parse( array( 'linebreaks' ) );

			if ( ( $stream->source() != 'hidden' ) && $stream->get( 'replies' ) && ( $row->get( '_comments' ) !== false ) ) {
				if ( $newForm ) {
					$date				.=	( $date ? ' ' : null ) . '<span class="streamToggle streamToggleReplies" data-cbactivity-toggle-target=".commentContainerNew" data-cbactivity-toggle-close="false" data-cbactivity-toggle-filter="false" data-cbactivity-toggle-active-classes="hidden">- <a href="javascript: void(0);">' . CBTxt::T( 'Reply' ) . '</a></span>';
				}

				$replies				=	$row->replies( $stream->source(), $stream->user() );

				if ( $replies ) {
					CBActivity::loadStreamDefaults( $replies, $stream );

					$replies->set( 'replies', 0 );

					$footer				.=	$replies->stream( true, ( $row->get( '_comments' ) ? true : false ) );
				}
			}

			$return						.=		'<div id="' . $rowId . '" class="streamItem streamItemInline commentContainer' . ( $typeClass ? ' commentContainer' . $typeClass : null ) . ( $subTypeClass ? ' commentContainer' . $typeClass . $subTypeClass : null ) . '" data-cbactivity-id="' . (int) $row->get( 'id' ) . '">'
										.			'<div class="streamItemInner streamMedia media clearfix">'
										.				'<div class="streamMediaLeft commentContainerLogo media-left">'
										.					$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
										.				'</div>'
										.				'<div class="streamMediaBody streamItemDisplay commentContainerContent media-body">'
										.					'<div class="commentContainerContentInner cbMoreLess text-small" data-cbmoreless-height="50">'
										.						'<div class="streamItemContent cbMoreLessContent">'
										.							'<strong>' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong>'
										.							( $message ? ' ' . $message : null )
										.						'</div>'
										.						'<div class="cbMoreLessOpen fade-edge hidden">'
										.							'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
										.						'</div>'
										.					'</div>'
										.					( $insert ? '<div class="commentContainerContentInsert">' . $insert . '</div>' : null )
										.					'<div class="commentContainerContentDate text-muted text-small">'
										.						cbFormatDate( $row->get( 'date' ), true, 'timeago' )
										.						( $row->params()->get( 'modified' ) ? ' <span class="streamIconEdited fa fa-edit" title="' . htmlspecialchars( CBTxt::T( 'Edited' ) ) . '"></span>' : null )
										.						( $date ? ' ' . $date : null )
										.					'</div>'
										.					( $footer ? '<div class="commentContainerContentFooter">' . $footer . '</div>' : null )
										.				'</div>';

			if ( ( $cbModerator || $rowOwner ) && $canCreate ) {
				$return					.=				self::showEdit( $row, $stream, $output, $user, $viewer, $plugin );
			}

			if ( $cbModerator || $rowOwner || ( $viewer->get( 'id' ) && ( ! $rowOwner ) ) || $menu ) {
				$menuItems				=	'<ul class="streamItemMenuItems commentMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

				if ( ( $cbModerator || $rowOwner ) && $canCreate ) {
					$menuItems			.=		'<li class="streamItemMenuItem commentMenuItem"><a href="javascript: void(0);" class="commentMenuItemEdit streamItemEditDisplay" data-cbactivity-container="#' . $rowId . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';
				}

				if ( $viewer->get( 'id' ) && ( ! $rowOwner ) ) {
					if ( $stream->source() == 'hidden' ) {
						$menuItems		.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $stream->endpoint( 'unhide', array( 'id' => (int) $row->get( 'id' ) ) ) . '" class="commentMenuItemUnhide streamItemAction" data-cbactivity-container="#' . $rowId . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Unhide' ) . '</a></li>';
					} else {
						$menuItems		.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $stream->endpoint( 'hide', array( 'id' => (int) $row->get( 'id' ) ) ) . '" class="commentMenuItemHide streamItemAction" data-cbactivity-container="#' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to hide this Comment?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Hide Comment' ) ) . '"><span class="fa fa-times"></span> ' . CBTxt::T( 'Hide' ) . '</a></li>';
					}
				}

				if ( $cbModerator || $rowOwner ) {
					$menuItems			.=		'<li class="streamItemMenuItem commentMenuItem"><a href="' . $stream->endpoint( 'delete', array( 'id' => (int) $row->get( 'id' ) ) ) . '" class="commentMenuItemDelete streamItemAction" data-cbactivity-container="#' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to delete this Comment?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Delete Comment' ) ) . '"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
				}

				if ( $menu ) {
					$menuItems			.=		'<li class="streamItemMenuItem commentMenuItem">' . implode( '</li><li class="streamItemMenuItem commentMenuItem">', $menu ) . '</li>';
				}

				$menuItems				.=	'</ul>';

				$menuAttr				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="fa fa-chevron-down text-muted" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle" data-cbtooltip-open-classes="open"' );

				$return					.=				'<div class="streamItemMenu commentContainerMenu small">'
										.					'<span ' . trim( $menuAttr ) . '></span>'
										.				'</div>';
			}

			$return						.=			'</div>'
										.		'</div>';
		} elseif ( $output != 2 ) {
			$return						.=		'<div class="streamItemEmpty text-center text-muted small">';

			if ( $output == 1 ) {
				$return					.=			CBTxt::T( 'No more comments to display.' );
			} else {
				$return					.=			CBTxt::T( 'No comments to display.' );
			}

			$return						.=		'</div>';
		} elseif ( ( $output == 2 ) && ( ! $newForm ) ) {
			return null;
		}

		$return							.=		( ! in_array( $output, array( 1, 4 ) ) ? '</div>' : null )
										.		( ! $stream->direction() ? $moreButton : $newForm )
										.	( ! in_array( $output, array( 1, 4 ) ) ? '</div>' : null )
										.	CBActivity::reloadHeaders( $output );

		$_PLUGINS->trigger( 'activity_onAfterDisplayComments', array( &$return, $rows, $stream, $output ) );

		return $return;
	}

	/**
	 * @param Comments        $stream
	 * @param int             $output 0: Normal, 1: Raw, 2: Inline, 3: Load, 4: Save
	 * @param UserTable       $user
	 * @param UserTable       $viewer
	 * @param cbPluginHandler $plugin
	 * @return null|string
	 */
	static public function showNew( $stream, $output, $user, $viewer, $plugin )
	{
		global $_PLUGINS;

		if ( ! CBActivity::canCreate( $user, $viewer, $stream ) ) {
			return null;
		}

		$cbModerator	=	CBActivity::isModerator( (int) $viewer->get( 'id' ) );
		$messageLimit	=	( $cbModerator ? 0 : (int) $stream->get( 'message_limit', 400 ) );

		$rowId			=	$stream->id() . '_new';
		$newBody		=	null;
		$newFooter		=	null;

		$_PLUGINS->trigger( 'activity_onDisplayCommentCreate', array( &$newBody, &$newFooter, $stream, $output ) );

		$return			=	'<div id="' . $rowId . '" class="streamItem streamItemInline commentContainer commentContainerNew' . ( $stream->get( 'type' ) == 'comment' ? ' hidden' : null ) . '">'
						.		'<div class="streamItemInner streamMedia media clearfix">'
						.			'<form action="' . $stream->endpoint( 'new' ) . '" method="post" enctype="multipart/form-data" name="' . $rowId . 'Form" id="' . $rowId . 'Form" class="cb_form streamItemForm form">'
						.				'<div class="streamMediaLeft commentContainerLogo media-left">'
						.					CBuser::getInstance( (int) $viewer->get( 'user_id' ), false )->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
						.				'</div>'
						.				'<div class="streamMediaBody streamItemNew commentContainerContent media-body text-small">'
						.					'<textarea id="' . $stream->id() . '_message_new" name="message" rows="1" class="streamInput streamInputAutosize streamInputMessage form-control" placeholder="' . htmlspecialchars( ( $stream->get( 'type' ) == 'comment' ? CBTxt::T( 'Write a reply...' ) : CBTxt::T( 'Write a comment...' ) ) ) . '"' . ( $messageLimit ? ' data-cbactivity-input-limit="' . (int) $messageLimit . '" maxlength="' . (int) $messageLimit . '"' : null ) . '></textarea>'
						.					$newBody
						.					'<div class="streamItemDisplay commentContainerFooter hidden">'
						.						'<div class="commentContainerFooterRow clearfix">'
						.							'<div class="commentContainerFooterRowLeft pull-left">'
						.								$newFooter
						.							'</div>'
						.							'<div class="commentContainerFooterRowRight pull-right text-right">'
						.								'<button type="submit" class="commentButton commentButtonNewSave streamItemNewSave btn btn-primary btn-xs disabled" disabled="disabled">' . CBTxt::T( 'Post' ) . '</button>'
						.								' <button type="button" class="commentButton commentButtonNewCancel streamItemNewCancel btn btn-default btn-xs">' . CBTxt::T( 'Cancel' ) . '</button>'
						.							'</div>'
						.						'</div>'
						.					'</div>'
						.				'</div>'
						.			'</form>'
						.		'</div>'
						.	'</div>';

		return $return;
	}

	/**
	 * @param CommentTable    $row
	 * @param Comments        $stream
	 * @param int             $output 0: Normal, 1: Raw, 2: Inline, 3: Load, 4: Save
	 * @param UserTable       $user
	 * @param UserTable       $viewer
	 * @param cbPluginHandler $plugin
	 * @return null|string
	 */
	static public function showEdit( $row, $stream, $output, $user, $viewer, $plugin )
	{
		global $_PLUGINS;

		$cbModerator	=	CBActivity::isModerator( (int) $viewer->get( 'id' ) );
		$messageLimit	=	( $cbModerator ? 0 : (int) $stream->get( 'message_limit', 400 ) );

		$rowId			=	$stream->id() . '_edit_' . (int) $row->get( 'id' );

		$editBody		=	null;
		$editFooter		=	null;

		$_PLUGINS->trigger( 'activity_onDisplayCommentEdit', array( &$row, &$editBody, &$editFooter, $stream, $output ) );

		$return			=	'<div class="streamMediaBody streamItemEdit commentContainerContentEdit media-body text-small hidden">'
						.		'<form action="' . $stream->endpoint( 'save', array( 'id' => (int) $row->get( 'id' ) ) ) . '" method="post" enctype="multipart/form-data" name="' . $rowId . 'Form" id="' . $rowId . 'Form" class="cb_form streamItemForm form">'
						.			'<textarea id="' . $stream->id() . '_message_edit_' . (int) $row->get( 'id' ) . '" name="message" rows="1" class="streamInput streamInputAutosize streamInputMessage form-control" placeholder="' . htmlspecialchars( ( $stream->get( 'type' ) == 'comment' ? CBTxt::T( 'Write a reply...' ) : CBTxt::T( 'Write a comment...' ) ) ) . '"' . ( $messageLimit ? ' data-cbactivity-input-limit="' . (int) $messageLimit . '" maxlength="' . (int) $messageLimit . '"' : null ) . '>' . htmlspecialchars( $row->get( 'message' ) ) . '</textarea>'
						.			$editBody
						.			'<div class="commentContainerFooter">'
						.				'<div class="commentContainerFooterRow clearfix">'
						.					'<div class="commentContainerFooterRowLeft pull-left">'
						.						$editFooter
						.					'</div>'
						.					'<div class="commentContainerFooterRowRight pull-right text-right">'
						.						'<button type="submit" class="commentButton commentButtonEditSave streamItemEditSave btn btn-primary btn-xs">' . CBTxt::T( 'Done Editing' ) . '</button>'
						.						' <button type="button" class="commentButton commentButtonEditCancel streamItemEditCancel btn btn-default btn-xs">' . CBTxt::T( 'Cancel' ) . '</button>'
						.					'</div>'
						.				'</div>'
						.			'</div>'
						.		'</form>'
						.	'</div>';

		return $return;
	}
}