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
use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Activity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityActivity
{

	/**
	 * @param ActivityTable[] $rows
	 * @param Activity        $stream
	 * @param int             $output 0: Normal, 1: Raw, 2: Inline, 3: Load, 4: Save
	 * @param UserTable       $user
	 * @param UserTable       $viewer
	 * @param cbPluginHandler $plugin
	 * @return null|string
	 */
	static public function showActivity( $rows, $stream, $output, $user, $viewer, $plugin )
	{
		global $_CB_framework, $_PLUGINS;

		$showActions					=	(int) $stream->get( 'actions', 1 );
		$showLocations					=	(int) $stream->get( 'locations', 1 );
		$showLinks						=	(int) $stream->get( 'links', 1 );
		$showTags						=	(int) $stream->get( 'tags', 1 );

		CBActivity::loadHeaders( $output );

		static $loaded					=	0;

		if ( ! $loaded++ ) {
			$_CB_framework->outputCbJQuery( "$( '.activityStream' ).cbactivity();" );
		}

		$canCreate						=	CBActivity::canCreate( $user, $viewer, $stream );
		$cbModerator					=	CBActivity::isModerator( (int) $viewer->get( 'id' ) );
		$sourceClean					=	htmlspecialchars( $stream->source() );
		$newForm						=	null;
		$moreButton						=	null;

		if ( ( $output != 4 ) && $stream->get( 'paging' ) && $stream->get( 'limit' ) && $rows ) {
			$moreButton					=	'<a href="' . $stream->endpoint( 'show', array( 'limitstart' => ( $stream->get( 'limitstart' ) + $stream->get( 'limit' ) ), 'limit' => $stream->get( 'limit' ) ) ) . '" class="activityButton activityButtonMore streamMore btn btn-primary btn-sm btn-block">' . CBTxt::T( 'More' ) . '</a>';
		}

		$return							=	null;

		$_PLUGINS->trigger( 'activity_onBeforeDisplayActivity', array( &$return, &$rows, $stream, $output ) );

		if ( ! in_array( $output, array( 1, 4 ) ) ) {
			$return						.=	'<div class="' . $sourceClean . 'Activity activityStream streamContainer ' . ( $stream->direction() ? 'streamContainerUp' : 'streamContainerDown' ) . '" data-cbactivity-direction="' . (int) $stream->direction() . '">';

			if ( ( $stream->source() != 'hidden' ) && ( ! $stream->get( 'id' ) ) && ( ! $stream->get( 'filter' ) ) ) {
				$newForm				=	self::showNew( $stream, $output, $user, $viewer, $plugin );
			}
		}

		$return							.=		( $stream->direction() ? $moreButton : $newForm )
										.		( ! in_array( $output, array( 1, 4 ) ) ? '<div class="' . $sourceClean . 'ActivityItems activityStreamItems streamItems">' : null );

		if ( $rows ) foreach ( $rows as $row ) {
			$rowId						=	$stream->id() . '_' . (int) $row->get( 'id' );
			$rowOwner					=	( $viewer->get( 'id' ) == $row->get( 'user_id' ) );
			$typeClass					=	( $row->get( 'type' ) ? ucfirst( strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $row->get( 'type' ) ) ) ) : null );
			$subTypeClass				=	( $row->get( 'subtype' ) ? ucfirst( strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $row->get( 'subtype' ) ) ) ) : null );
			$isStatus					=	( ( $row->get( 'type' ) == 'status' ) || ( $row->get( 'subtype' ) == 'status' ) );

			$cbUser						=	CBuser::getInstance( (int) $row->get( 'user_id' ), false );
			$title						=	( $row->get( 'title' ) ? ( $isStatus ? htmlspecialchars( $row->get( 'title' ) ) : CBTxt::T( $row->get( 'title' ) ) ) : null );
			$message					=	( $row->get( 'message' ) ? ( $isStatus ? htmlspecialchars( $row->get( 'message' ) ) : CBTxt::T( $row->get( 'message' ) ) ) : null );
			$date						=	null;
			$insert						=	null;
			$footer						=	null;
			$menu						=	array();
			$extras						=	array();

			$_PLUGINS->trigger( 'activity_onDisplayActivity', array( &$row, &$title, &$date, &$message, &$insert, &$footer, &$menu, &$extras, $stream, $output ) );

			$title						=	$stream->parser( $title )->parse( array( 'linebreaks' ) );
			$message					=	$stream->parser( $message )->parse();

			if ( $isStatus ) {
				if ( ( in_array( $row->get( 'type' ), array( 'status', 'field' ) ) ) && $row->get( 'parent' ) && ( $row->get( 'parent' ) != $_CB_framework->displayedUser() ) && ( $row->get( 'parent' ) != $row->get( 'user_id' ) ) ) {
					$targetUser			=	CBuser::getInstance( (int) $row->get( 'parent' ) );

					if ( $targetUser !== null ) {
						$title			=	' <span class="fa fa-caret-right"></span> <strong>' . $targetUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong>';
					}
				}

				$action					=	( $showActions ? $stream->parser( $row->action() )->parse( array( 'linebreaks' ) ) : null );
				$location				=	( $showLocations ? $row->location() : null );
				$tags					=	null;

				if ( $showTags && $row->get( '_tags' ) ) {
					$tagsStream			=	$row->tags( $stream->source() );

					if ( $tagsStream && $tagsStream->data( true ) ) {
						$tags			=	trim( CBTxt::T( 'ACTIVITY_STATUS_TAGS', 'with [tags]', array( '[tags]' => $tagsStream->stream( true ) ) ) );
					}
				}

				if ( $action || $location || $tags ) {
					$subContent			=	( $action ? $action : null )
										.	( $location ? ( $action ? ' ' : null ) . $location : null )
										.	( $tags ? ( $action || $location ? ' ' : null ) . $tags : null );

					if ( $title ) {
						$message		.=	'<div class="streamItemSubContent">&mdash; ' . $subContent . '</div>';
					} else {
						$title			=	$subContent;
					}
				}
			} elseif ( $row->get( 'type' ) == 'activity' ) {
				if ( ! $row->get( 'item' ) ) {
					continue;
				}

				$row->set( '_comments', false );
				$row->set( '_tags', false );
				$row->set( '_links', false );

				$title					=	CBTxt::T( 'ACTIVITY_OF_ACTIVITY_TITLE', '[title] [date]', array( '[title]' => $title, '[date]' => cbFormatDate( $row->get( 'date' ), true, 'timeago' ) ) );
				$message				=	null;
				$footer					=	null;

				$subActivity			=	new Activity( 'activity', $cbUser->getUserData() );

				CBActivity::loadStreamDefaults( $subActivity, $stream );

				$subActivity->set( 'id', $row->get( 'item' ) );

				if ( $row->get( 'subtype' ) == 'comment' ) {
					$subActivity->set( 'comments', 1 );
				} elseif ( $row->get( 'subtype' ) == 'tag' ) {
					$subActivity->set( 'tags', 1 );
				}

				$insert					=	$subActivity->stream( true );

				if ( ! $insert ) {
					continue;
				}
			} else {
				$title					=	( $title ? $cbUser->replaceUserVars( $title, false, false, $extras, false ) : null );
				$message				=	( $message ? $cbUser->replaceUserVars( $message, false, false, $extras, false ) : null );
			}

			$links						=	array();

			if ( ( ( $isStatus && $showLinks ) || ( ! $isStatus ) ) && ( $row->get( '_links' ) !== false ) ) {
				$links					=	$row->attachments();
			}

			if ( ( $stream->source() != 'hidden' ) && $stream->get( 'comments' ) && ( $row->get( '_comments' ) !== false ) ) {
				$comments				=	$row->comments( 'activity', $cbUser->getUserData() );

				if ( $comments ) {
					CBActivity::loadStreamDefaults( $comments, $stream, 'comments_' );

					$footer				.=	$comments->stream( true, ( $row->get( '_comments' ) ? true : false ) );
				}
			}

			$return						.=		'<div id="' . $rowId . '" class="streamItem streamPanel activityContainer' . ( $typeClass ? ' activityContainer' . $typeClass : null ) . ( $subTypeClass ? ' activityContainer' . $typeClass . $subTypeClass : null ) . ' panel panel-default" data-cbactivity-id="' . (int) $row->get( 'id' ) . '">'
										.			'<div class="streamItemInner">'
										.				'<div class="streamMedia streamPanelHeading activityContainerHeader media panel-heading clearfix">'
										.					'<div class="streamMediaLeft activityContainerLogo media-left">'
										.						$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
										.					'</div>'
										.					'<div class="streamMediaBody activityContainerTitle media-body">'
										.						'<div class="activityContainerTitleTop text-muted">'
										.							'<strong>' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</strong>'
										.							( $title ? ' ' . $title : null )
										.						'</div>'
										.						'<div class="activityContainerTitleBottom text-muted small">'
										.							cbFormatDate( $row->get( 'date' ), true, 'timeago' )
										.							( $row->params()->get( 'modified' ) ? ' <span class="streamIconEdited fa fa-edit" title="' . htmlspecialchars( CBTxt::T( 'Edited' ) ) . '"></span>' : null )
										.							( $date ? ' ' . $date : null )
										.						'</div>'
										.					'</div>'
										.				'</div>';

			if ( $message ) {
				$return					.=				'<div class="streamPanelBody streamItemDisplay activityContainerContent panel-body">'
										.					'<div class="activityContainerContentInner cbMoreLess">'
										.						'<div class="streamItemContent cbMoreLessContent">'
										.							$message
										.						'</div>'
										.						'<div class="cbMoreLessOpen fade-edge hidden">'
										.							'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
										.						'</div>'
										.					'</div>'
										.				'</div>';
			}

			$return						.=				( $insert ? '<div class="streamItemDisplay streamItemDivider activityContainerInsert border-default">' . $insert . '</div>' : null );

			if ( $links ) {
				$return					.=				'<div class="streamPanelBody streamItemDisplay streamItemDivider activityContainerAttachments panel-body border-default">'
										.					'<div class="activityContainerAttachmentsInner">'
										.						self::showAttachments( $row, $stream, $output, $user, $viewer, $plugin )
										.					'</div>'
										.				'</div>';
			}

			$return						.=				( $footer ? '<div class="streamPanelFooter streamItemDisplay activityContainerFooter panel-footer">' . $footer . '</div>' : null );

			if ( $isStatus && ( $cbModerator || $rowOwner ) && $canCreate ) {
				$return					.=				self::showEdit( $row, $stream, $output, $user, $viewer, $plugin );
			}

			if ( $cbModerator || $rowOwner || ( $viewer->get( 'id' ) && ( ! $rowOwner ) ) || $menu ) {
				$menuItems				=	'<ul class="streamItemMenuItems activityMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">';

				if ( $isStatus && ( $cbModerator || $rowOwner ) && $canCreate ) {
					$menuItems			.=		'<li class="streamItemMenuItem activityMenuItem"><a href="javascript: void(0);" class="activityMenuItemEdit streamItemEditDisplay" data-cbactivity-container="#' . $rowId . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';
				}

				if ( $viewer->get( 'id' ) && ( ! $rowOwner ) ) {
					if ( $stream->source() == 'hidden' ) {
						$menuItems		.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $stream->endpoint( 'unhide', array( 'id' => (int) $row->get( 'id' ) ) ) . '" class="activityMenuItemUnhide streamItemAction" data-cbactivity-container="#' . $rowId . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Unhide' ) . '</a></li>';
					} else {
						$menuItems		.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $stream->endpoint( 'hide', array( 'id' => (int) $row->get( 'id' ) ) ) . '" class="activityMenuItemHide streamItemAction" data-cbactivity-container="#' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to hide this Activity?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Hide Activity' ) ) . '"><span class="fa fa-times"></span> ' . CBTxt::T( 'Hide' ) . '</a></li>';
					}
				}

				if ( $cbModerator || $rowOwner ) {
					$menuItems			.=		'<li class="streamItemMenuItem activityMenuItem"><a href="' . $stream->endpoint( 'delete', array( 'id' => (int) $row->get( 'id' ) ) ) . '" class="activityMenuItemDelete streamItemAction" data-cbactivity-container="#' . $rowId . '" data-cbactivity-confirm="' . htmlspecialchars( CBTxt::T( 'Are you sure you want to delete this Activity?' ) ) . '" data-cbactivity-confirm-button="' . htmlspecialchars( CBTxt::T( 'Delete Activity' ) ) . '"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>';
				}

				if ( $menu ) {
					$menuItems			.=		'<li class="streamItemMenuItem activityMenuItem">' . implode( '</li><li class="streamItemMenuItem activityMenuItem">', $menu ) . '</li>';
				}

				$menuItems				.=	'</ul>';

				$menuAttr				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="fa fa-chevron-down text-muted" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle" data-cbtooltip-open-classes="open"' );

				$return					.=				'<div class="streamItemMenu activityContainerMenu">'
										.					'<span ' . trim( $menuAttr ) . '></span>'
										.				'</div>';
			}

			$return						.=			'</div>'
										.		'</div>';
		} elseif ( $output != 2 ) {
			$return						.=		'<div class="streamItemEmpty text-center text-muted small">';

			if ( $output == 1 ) {
				$return					.=			CBTxt::T( 'No more activity to display.' );
			} else {
				$return					.=			CBTxt::T( 'No activity to display.' );
			}

			$return						.=		'</div>';
		} elseif ( ( $output == 2 ) && ( ! $newForm ) ) {
			return null;
		}

		$return							.=		( ! in_array( $output, array( 1, 4 ) ) ? '</div>' : null )
										.		( ! $stream->direction() ? $moreButton : $newForm )
										.	( ! in_array( $output, array( 1, 4 ) ) ? '</div>' : null )
										.	CBActivity::reloadHeaders( $output );

		$_PLUGINS->trigger( 'activity_onAfterDisplayActivity', array( &$return, $rows, $stream, $output ) );

		return $return;
	}

	/**
	 * @param Activity        $stream
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

		$cbModerator		=	CBActivity::isModerator( (int) $viewer->get( 'id' ) );
		$messageLimit		=	( $cbModerator ? 0 : (int) $stream->get( 'message_limit', 400 ) );
		$showActions		=	(int) $stream->get( 'actions', 1 );
		$actionLimit		=	( $cbModerator ? 0 : (int) $stream->get( 'actions_message_limit', 100 ) );
		$showLocations		=	(int) $stream->get( 'locations', 1 );
		$locationLimit		=	( $cbModerator ? 0 : (int) $stream->get( 'locations_address_limit', 200 ) );
		$showLinks			=	(int) $stream->get( 'links', 1 );
		$linkLimit			=	( $cbModerator ? 0 : (int) $stream->get( 'links_link_limit', 5 ) );
		$showTags			=	(int) $stream->get( 'tags', 1 );

		$actionTooltip		=	cbTooltip( null, CBTxt::T( 'What are you doing or feeling?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$locationTooltip	=	cbTooltip( null, CBTxt::T( 'Share your location.' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$tagTooltip			=	cbTooltip( null, CBTxt::T( 'Are you with anyone?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$linkTooltip		=	cbTooltip( null, CBTxt::T( 'Have a link to share?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$actionOptions		=	( $showActions ? CBActivity::loadActionOptions() : array() );
		$locationOptions	=	( $showLocations ? CBActivity::loadLocationOptions() : array() );

		$rowId				=	$stream->id() . '_new';

		$newBody			=	null;
		$newFooter			=	null;

		$_PLUGINS->trigger( 'activity_onDisplayActivityCreate', array( &$newBody, &$newFooter, $stream, $output ) );

		$return				=	'<div id="' . $rowId . '" class="streamItem streamPanel activityContainer activityContainerNew panel panel-default">'
							.		'<div class="streamItemInner">'
							.			'<form action="' . $stream->endpoint( 'new' ) . '" method="post" enctype="multipart/form-data" name="' . $rowId . 'Form" id="' . $rowId . 'Form" class="cb_form streamItemForm form">'
							.				'<div class="streamItemNew">'
							.					'<textarea id="' . $stream->id() . '_message_new" name="message" rows="1" class="streamInput streamInputAutosize streamInputMessage form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What's on your mind?" ) ) . '" data-cbactivity-input-size="3"' . ( $messageLimit ? ' data-cbactivity-input-limit="' . (int) $messageLimit . '" maxlength="' . (int) $messageLimit . '"' : null ) . '></textarea>';

		if ( $showLinks ) {
			if ( ( ! $linkLimit ) || ( $linkLimit > 1 ) ) {
				$return		.=					'<div class="streamItemInputGroup streamInputLinkContainer cbRepeat border-default clearfix hidden" data-cbrepeat-sortable="false"' . ( $linkLimit ? ' data-cbrepeat-max="' . (int) $linkLimit . '"' : null ) . '>'
							.						'<div class="streamItemInputGroupRow cbRepeatRow border-default">'
							.							'<span class="streamItemInputGroupLabel form-control">'
							.								'<button type="button" class="cbRepeatRowAdd btn btn-xs btn-success"><span class="fa fa-plus "></span></button>'
							.								'<button type="button" class="cbRepeatRowRemove btn btn-xs btn-danger"><span class="fa fa-minus"></span></button>'
							.							'</span>'
							.							'<div class="streamItemInputGroupInput border-default">'
							.								'<input type="text" id="' . $stream->id() . '_links__0__url_new" name="links[0][url]" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '" disabled="disabled" />'
							.							'</div>'
							.						'</div>'
							.					'</div>';
			} else {
				$return		.=					'<div class="streamItemInputGroup streamInputLinkContainer border-default clearfix hidden">'
							.						'<input type="text" id="' . $stream->id() . '_links__0__url_new" name="links[0][url]" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '" disabled="disabled" />'
							.					'</div>';
			}
		}

		if ( $actionOptions ) {
			$emoteOptions	=	CBActivity::loadEmoteOptions();

			$return			.=					'<div class="streamItemInputGroup streamInputActionContainer border-default clearfix hidden">'
							.						'<span class="streamItemInputGroupLabel streamInputSelectToggleLabel form-control"></span>'
							.						'<div class="streamItemInputGroupInput border-default">'
							.							'<input type="text" id="' . $stream->id() . '_actions_message_new" name="actions[message]" class="streamInput streamInputActionMessage streamInputSelectTogglePlaceholder form-control no-border"' . ( $actionLimit ? ' maxlength="' . (int) $actionLimit . '"' : null ) . ' disabled="disabled" />'
							.							( $emoteOptions ? str_replace( 'actions__emote', $stream->id() . '_actions_emote_new', moscomprofilerHTML::selectList( $emoteOptions, 'actions[emote]', 'class="streamInputSelect streamInputEmote" data-cbselect-width="auto" data-cbselect-height="100%" data-cbselect-dropdown-css-class="streamEmoteOptions" disabled="disabled"', 'value', 'text', null, 0, false, false, false ) ) : null )
							.						'</div>'
							.					'</div>';
		}

		if ( $locationOptions ) {
			$return			.=					'<div class="streamItemInputGroup streamInputLocationContainer border-default clearfix hidden">'
							.						'<span class="streamItemInputGroupLabel streamInputSelectToggleLabel form-control"></span>'
							.						'<div class="streamItemInputGroupInput border-default">'
							.							'<input type="text" id="' . $stream->id() . '_location_place_new" name="location[place]" class="streamInput streamInputLocationPlace form-control no-border" placeholder="' . CBTxt::T( 'Where are you?' ) . '"' . ( $locationLimit ? ' maxlength="' . (int) $locationLimit . '"' : null ) . ' disabled="disabled" />'
							.							'<input type="text" id="' . $stream->id() . '_location_address_new" name="location[address]" class="streamInput streamInputLocationAddress form-control no-border" placeholder="' . CBTxt::T( 'Have the address to share?' ) . '"' . ( $locationLimit ? ' maxlength="' . (int) $locationLimit . '"' : null ) . ' disabled="disabled" />'
							.							'<div class="streamFindLocation fa fa-map-marker fa-lg" data-cbactivity-location-target=".streamInputLocationAddress"></div>'
							.						'</div>'
							.					'</div>';
		}

		if ( $showTags ) {
			$tagOptions		=	CBActivity::loadTagOptions( null, (int) $viewer->get( 'id' ) );

			$return			.=					'<div class="streamItemInputGroup streamInputTagContainer border-default clearfix hidden">'
							.						str_replace( 'tags__', $stream->id() . '_tags_new', moscomprofilerHTML::selectList( $tagOptions, 'tags[]', 'multiple="multiple" class="streamInputSelect streamInputTags form-control no-border" data-cbselect-placeholder="' . htmlspecialchars( CBTxt::T( 'Who are you with?' ) ) . '" data-cbselect-tags="true" data-cbselect-width="100%" data-cbselect-height="100%" data-cbselect-dropdown-css-class="streamTagsOptions" disabled="disabled"', 'value', 'text', null, 0, true, false, false ) )
							.					'</div>';
		}

		$return				.=					$newBody
							.				'</div>'
							.				'<div class="streamPanelFooter streamItemDisplay activityContainerFooter panel-footer hidden">'
							.					'<div class="activityContainerFooterRow clearfix">'
							.						'<div class="activityContainerFooterRowLeft pull-left">'
							.							( $actionOptions ? str_replace( 'actions__id', $stream->id() . '_actions_id_new', moscomprofilerHTML::selectList( $actionOptions, 'actions[id]', 'class="streamInputSelect streamInputSelectToggle streamInputAction btn btn-xs btn-default" data-cbactivity-toggle-target=".streamInputActionContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default" data-cbactivity-toggle-icon="fa fa-smile-o" data-cbselect-dropdown-css-class="streamSelectOptions"' . $actionTooltip, 'value', 'text', null, 0, false, false, false ) ) : null )
							.							( $locationOptions ? ( $actionOptions ? ' ' : null ) . str_replace( 'location__id', $stream->id() . '_location_id_new', moscomprofilerHTML::selectList( $locationOptions, 'location[id]', 'class="streamInputSelect streamInputSelectToggle streamInputLocation btn btn-xs btn-default" data-cbactivity-toggle-target=".streamInputLocationContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default" data-cbactivity-toggle-icon="fa fa-map-marker" data-cbselect-dropdown-css-class="streamSelectOptions"' . $locationTooltip, 'value', 'text', null, 0, false, false, false ) ) : null )
							.							( $showTags ? ( $actionOptions || $locationOptions ? ' ' : null ) . '<button type="button" id="' . $stream->id() . '_tags_new" class="streamToggle streamInputTag btn btn-default btn-xs" data-cbactivity-toggle-target=".streamInputTagContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default"' . $tagTooltip . '><span class="fa fa-user"></span></button>' : null )
							.							( $showLinks ? ( $actionOptions || $locationOptions || $showTags ? ' ' : null ) . '<button type="button" id="' . $stream->id() . '_links_new" class="streamToggle streamInputLink btn btn-default btn-xs" data-cbactivity-toggle-target=".streamInputLinkContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default"' . $linkTooltip . '><span class="fa fa-link"></span></button>' : null )
							.							$newFooter
							.						'</div>'
							.						'<div class="activityContainerFooterRowRight pull-right text-right">'
							.							'<button type="submit" class="activityButton activityButtonNewSave streamItemNewSave btn btn-primary btn-xs disabled" disabled="disabled">' . CBTxt::T( 'Post' ) . '</button>'
							.							' <button type="button" class="activityButton activityButtonNewCancel streamItemNewCancel btn btn-default btn-xs">' . CBTxt::T( 'Cancel' ) . '</button>'
							.						'</div>'
							.					'</div>'
							.				'</div>'
							.			'</form>'
							.		'</div>'
							.	'</div>';

		return $return;
	}

	/**
	 * @param ActivityTable   $row
	 * @param Activity        $stream
	 * @param int             $output 0: Normal, 1: Raw, 2: Inline, 3: Load, 4: Save
	 * @param UserTable       $user
	 * @param UserTable       $viewer
	 * @param cbPluginHandler $plugin
	 * @return null|string
	 */
	static public function showEdit( $row, $stream, $output, $user, $viewer, $plugin )
	{
		global $_PLUGINS;

		$cbModerator				=	CBActivity::isModerator( (int) $viewer->get( 'id' ) );
		$messageLimit				=	( $cbModerator ? 0 : (int) $stream->get( 'message_limit', 400 ) );
		$showActions				=	(int) $stream->get( 'actions', 1 );
		$actionLimit				=	( $cbModerator ? 0 : (int) $stream->get( 'actions_message_limit', 100 ) );
		$showLocations				=	(int) $stream->get( 'locations', 1 );
		$locationLimit				=	( $cbModerator ? 0 : (int) $stream->get( 'locations_address_limit', 200 ) );
		$showLinks					=	(int) $stream->get( 'links', 1 );
		$linkLimit					=	( $cbModerator ? 0 : (int) $stream->get( 'links_link_limit', 5 ) );
		$showTags					=	(int) $stream->get( 'tags', 1 );

		$actionTooltip				=	cbTooltip( null, CBTxt::T( 'What are you doing or feeling?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$locationTooltip			=	cbTooltip( null, CBTxt::T( 'Share your location.' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$tagTooltip					=	cbTooltip( null, CBTxt::T( 'Are you with anyone?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$linkTooltip				=	cbTooltip( null, CBTxt::T( 'Have a link to share?' ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
		$actionOptions				=	( $showActions ? CBActivity::loadActionOptions() : array() );
		$locationOptions			=	( $showLocations ? CBActivity::loadLocationOptions() : array() );

		$rowId						=	$stream->id() . '_edit_' . (int) $row->get( 'id' );
		$actionId					=	null;
		$locationId					=	null;
		$tags						=	array();
		$links						=	array();

		$editBody					=	null;
		$editFooter					=	null;

		$_PLUGINS->trigger( 'activity_onDisplayActivityEdit', array( &$row, &$editBody, &$editFooter, $stream, $output ) );

		$return						=	'<div class="streamItemEdit activityContainerContentEdit border-default hidden">'
									.		'<form action="' . $stream->endpoint( 'save', array( 'id' => (int) $row->get( 'id' ) ) ) . '" method="post" enctype="multipart/form-data" name="' . $rowId . 'Form" id="' . $rowId . 'Form" class="cb_form streamItemForm form">'
									.			'<textarea id="' . $stream->id() . '_message_edit_' . (int) $row->get( 'id' ) . '" name="message" rows="3" class="streamInput streamInputAutosize streamInputMessage form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What's on your mind?" ) ) . '" data-cbactivity-input-size="3"' . ( $messageLimit ? ' data-cbactivity-input-limit="' . (int) $messageLimit . '" maxlength="' . (int) $messageLimit . '"' : null ) . '>' . htmlspecialchars( $row->get( 'message' ) ) . '</textarea>';

		if ( $showLinks ) {
			$links					=	$row->attachments();

			if ( $links ) {
				$return				.=			'<div class="streamPanelBody streamItemInputGroup streamInputAttachmentsContainer panel-body border-default clearfix">'
									.				self::showAttachments( $row, $stream, $output, $user, $viewer, $plugin )
									.			'</div>';
			}

			if ( ( ! $linkLimit ) || ( $linkLimit > 1 ) ) {
				$return				.=			'<div class="streamItemInputGroup streamInputLinkContainer cbRepeat border-default clearfix' . ( ! $links ? ' hidden' : null ) . '" data-cbrepeat-sortable="false"' . ( $linkLimit ? ' data-cbrepeat-max="' . (int) $linkLimit . '"' : null ) . '>';

				if ( $links ) {
					foreach ( $links as $i => $link ) {
						$return		.=				'<div class="streamItemInputGroupRow cbRepeatRow border-default">'
									.					'<span class="streamItemInputGroupLabel form-control">'
									.						'<button type="button" class="cbRepeatRowAdd btn btn-xs btn-success"><span class="fa fa-plus "></span></button>'
									.						'<button type="button" class="cbRepeatRowRemove btn btn-xs btn-danger"><span class="fa fa-minus"></span></button>'
									.					'</span>'
									.					'<div class="streamItemInputGroupInput border-default">'
									.						'<input type="text" id="' . $stream->id() . '_links__' . $i . '__url_edit_' . (int) $row->get( 'id' ) . '" name="links[' . $i . '][url]" value="' . htmlspecialchars( $link['url'] ) . '" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '" />'
									.					'</div>'
									.				'</div>';
					}
				} else {
					$return			.=				'<div class="streamItemInputGroupRow cbRepeatRow border-default">'
									.					'<span class="streamItemInputGroupLabel form-control">'
									.						'<button type="button" class="cbRepeatRowAdd btn btn-xs btn-success"><span class="fa fa-plus "></span></button>'
									.						'<button type="button" class="cbRepeatRowRemove btn btn-xs btn-danger"><span class="fa fa-minus"></span></button>'
									.					'</span>'
									.					'<div class="streamItemInputGroupInput border-default">'
									.						'<input type="text" id="' . $stream->id() . '_links__0__url_edit_' . (int) $row->get( 'id' ) . '" name="links[0][url]" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '" disabled="disabled" />'
									.					'</div>'
									.				'</div>';
				}

				$return				.=			'</div>';
			} else {
				$return				.=			'<div class="streamItemInputGroup streamInputLinkContainer border-default clearfix' . ( ! $links ? ' hidden' : null ) . '">'
									.				'<input type="text" id="' . $stream->id() . '_links__0__url_edit_' . (int) $row->get( 'id' ) . '" name="links[0][url]" value="' . htmlspecialchars( ( $links ? $links[0]['url'] : null ) ) . '" class="streamInput streamInputLinkURL form-control no-border" placeholder="' . htmlspecialchars( CBTxt::T( "What link would you like to share?" ) ) . '"' . ( ! $links ? ' disabled="disabled"' : null ) . ' />'
									.			'</div>';
			}
		}

		if ( $actionOptions ) {
			$action					=	$row->params()->subTree( 'action' );
			$actionId				=	(int) $action->get( 'id' );
			$emoteOptions			=	CBActivity::loadEmoteOptions();

			$return					.=			'<div class="streamItemInputGroup streamInputActionContainer border-default clearfix' . ( ! $actionId ? ' hidden' : null ) . '">'
									.				'<span class="streamItemInputGroupLabel streamInputSelectToggleLabel form-control"></span>'
									.				'<div class="streamItemInputGroupInput border-default">'
									.					'<input type="text" id="' . $stream->id() . '_actions_message_edit_' . (int) $row->get( 'id' ) . '" name="actions[message]" value="' . htmlspecialchars( $action->get( 'message' ) ) . '" class="streamInput streamInputActionMessage streamInputSelectTogglePlaceholder form-control no-border"' . ( $actionLimit ? ' maxlength="' . (int) $actionLimit . '"' : null ) . ( ! $actionId ? ' disabled="disabled"' : null ) . ' />'
									.					( $emoteOptions ? str_replace( 'action__emote', $stream->id() . '_actions_emote_edit_' . (int) $row->get( 'id' ), moscomprofilerHTML::selectList( $emoteOptions, 'actions[emote]', 'class="streamInputSelect streamInputEmote" data-cbselect-width="auto" data-cbselect-height="100%" data-cbselect-dropdown-css-class="streamEmoteOptions"' . ( ! $actionId ? ' disabled="disabled"' : null ), 'value', 'text', $action->get( 'emote' ), 0, false, false, false ) ) : null )
									.				'</div>'
									.			'</div>';
		}

		if ( $locationOptions ) {
			$location				=	$row->params()->subTree( 'location' );
			$locationId				=	(int) $location->get( 'id' );

			$return					.=			'<div class="streamItemInputGroup streamInputLocationContainer border-default clearfix' . ( ! $locationId ? ' hidden' : null ) . '">'
									.				'<span class="streamItemInputGroupLabel streamInputSelectToggleLabel form-control"></span>'
									.				'<div class="streamItemInputGroupInput border-default">'
									.					'<input type="text" id="' . $stream->id() . '_location_place_edit_' . (int) $row->get( 'id' ) . '" name="location[place]" value="' . htmlspecialchars( $location->get( 'place' ) ) . '" class="streamInput streamInputLocationPlace form-control no-border" placeholder="' . CBTxt::T( 'Where are you?' ) . '"' . ( $locationLimit ? ' maxlength="' . (int) $locationLimit . '"' : null ) . ( ! $locationId ? ' disabled="disabled"' : null ) . ' />'
									.					'<input type="text" id="' . $stream->id() . '_location_address_edit_' . (int) $row->get( 'id' ) . '" name="location[address]" value="' . htmlspecialchars( $location->get( 'address' ) ) . '" class="streamInput streamInputLocationAddress form-control no-border" placeholder="' . CBTxt::T( 'Have the address to share?' ) . '"' . ( $locationLimit ? ' maxlength="' . (int) $locationLimit . '"' : null ) . ( ! $locationId ? ' disabled="disabled"' : null ) . ' />'
							.							'<div class="streamFindLocation fa fa-map-marker fa-lg" data-cbactivity-location-target=".streamInputLocationAddress"></div>'
									.				'</div>'
									.			'</div>';
		}

		if ( $showTags ) {
			if ( $row->get( '_tags' ) ) {
				$tagsStream			=	$row->tags( $stream->source() );

				if ( $tagsStream ) {
					foreach ( $tagsStream->data() as $tag ) {
						/** @var TagTable $tag */
						$tags[]		=	(string) $tag->get( 'user' );
					}
				}
			}

			$tagOptions				=	CBActivity::loadTagOptions( (int) $row->get( 'id' ), (int) $row->get( 'user_id' ) );

			$return					.=			'<div class="streamItemInputGroup streamInputTagContainer border-default clearfix' . ( ! $tags ? ' hidden' : null ) . '">'
									.				str_replace( 'tags__', $stream->id() . '_tags_edit_' . (int) $row->get( 'id' ), moscomprofilerHTML::selectList( $tagOptions, 'tags[]', 'multiple="multiple" class="streamInputSelect streamInputTags form-control no-border" data-cbselect-placeholder="' . htmlspecialchars( CBTxt::T( 'Who are you with?' ) ) . '" data-cbselect-tags="true" data-cbselect-width="100%" data-cbselect-height="100%" data-cbselect-dropdown-css-class="streamTagsOptions"' . ( ! $tags ? ' disabled="disabled"' : null ), 'value', 'text', $tags, 0, true, false, false ) )
									.			'</div>';
		}

		$return						.=			$editBody
									.			'<div class="streamPanelFooter activityContainerFooter panel-footer">'
									.				'<div class="activityContainerFooterRow clearfix">'
									.					'<div class="activityContainerFooterRowLeft pull-left">'
									.						( $actionOptions ? str_replace( 'actions__id', $stream->id() . '_actions_id_edit_' . (int) $row->get( 'id' ), moscomprofilerHTML::selectList( $actionOptions, 'actions[id]', 'class="streamInputSelect streamInputSelectToggle streamInputAction btn btn-xs ' . ( $actionId ? 'btn-primary' : 'btn-default' ) . '" data-cbactivity-toggle-target=".streamInputActionContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default" data-cbactivity-toggle-icon="fa fa-smile-o" data-cbselect-dropdown-css-class="streamSelectOptions"' . $actionTooltip, 'value', 'text', $actionId, 0, false, false, false ) ) : null )
									.						( $locationOptions ? ( $actionOptions ? ' ' : null ) . str_replace( 'location__id', $stream->id() . '_location_id_edit_' . (int) $row->get( 'id' ), moscomprofilerHTML::selectList( $locationOptions, 'location[id]', 'class="streamInputSelect streamInputSelectToggle streamInputLocation btn btn-xs ' . ( $locationId ? 'btn-primary' : 'btn-default' ) . '" data-cbactivity-toggle-target=".streamInputLocationContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default" data-cbactivity-toggle-icon="fa fa-map-marker" data-cbselect-dropdown-css-class="streamSelectOptions"' . $locationTooltip, 'value', 'text', $locationId, 0, false, false, false ) ) : null )
									.						( $showTags ? ( $actionOptions || $locationOptions ? ' ' : null ) . '<button type="button" id="' . $stream->id() . '_tags_edit_' . (int) $row->get( 'id' ) . '" class="streamToggle streamInputTag btn btn-xs' . ( $tags ? ' btn-primary streamToggleOpen' : ' btn-default' ) . '" data-cbactivity-toggle-target=".streamInputTagContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default"' . $tagTooltip . '><span class="fa fa-user"></span></button>' : null )
									.						( $showLinks ? ( $actionOptions || $locationOptions || $showTags ? ' ' : null ) . '<button type="button" id="' . $stream->id() . '_links_edit_' . (int) $row->get( 'id' ) . '" class="streamToggle streamInputLink btn btn-xs' . ( $links ? ' btn-primary streamToggleOpen' : ' btn-default' ) . '" data-cbactivity-toggle-target=".streamInputLinkContainer" data-cbactivity-toggle-active-classes="btn-primary" data-cbactivity-toggle-inactive-classes="btn-default"' . $linkTooltip . '><span class="fa fa-link"></span></button>' : null )
									.						$editFooter
									.					'</div>'
									.					'<div class="activityContainerFooterRowRight pull-right text-right">'
									.						'<button type="submit" class="activityButton activityButtonEditSave streamItemEditSave btn btn-primary btn-xs">' . CBTxt::T( 'Done Editing' ) . '</button>'
									.						' <button type="button" class="activityButton activityButtonEditCancel streamItemEditCancel btn btn-default btn-xs">' . CBTxt::T( 'Cancel' ) . '</button>'
									.					'</div>'
									.				'</div>'
									.			'</div>'
									.		'</form>'
									.	'</div>';

		return $return;
	}

	/**
	 * @param ActivityTable   $row
	 * @param Activity        $stream
	 * @param int             $output 0: Normal, 1: Raw, 2: Inline, 3: Load, 4: Save
	 * @param UserTable       $user
	 * @param UserTable       $viewer
	 * @param cbPluginHandler $plugin
	 * @return null|string
	 */
	static public function showAttachments( $row, $stream, $output, $user, $viewer, $plugin )
	{
		global $_PLUGINS;

		if ( ! $stream->get( 'links', 1 ) ) {
			return null;
		}

		$links								=	$row->attachments();

		$_PLUGINS->trigger( 'activity_onDisplayActivityAttachments', array( &$row, &$links, $stream, $output ) );

		if ( ! $links ) {
			return null;
		}

		$cbModerator						=	CBActivity::isModerator( (int) $viewer->get( 'id' ) );
		$cbUser								=	CBuser::getInstance( (int) $row->get( 'user_id' ), false );

		$isStatus							=	( ( $row->get( 'type' ) == 'status' ) || ( $row->get( 'subtype' ) == 'status' ) );
		$rowOwner							=	( $viewer->get( 'id' ) == $row->get( 'user_id' ) );

		$count								=	count( $links );

		$return								=	'<div class="streamItemScroll">'
											.		'<div class="streamItemScrollLeft' . ( $count > 1 ? null : ' hidden' ) . '">'
											.			'<table>'
											.				'<tr>'
											.					'<td>'
											.						'<span class="streamItemScrollLeftIcon fa fa-chevron-left"></span>'
											.					'</td>'
											.				'</tr>'
											.			'</table>'
											.		'</div>';

		foreach ( $links as $i => $link ) {
			if ( ! $isStatus ) {
				$link['title']				=	$cbUser->replaceUserVars( $link['title'], false, false, null, false );
				$link['description']		=	$cbUser->replaceUserVars( $link['description'], false, false, null, false );
				$link['text']				=	$cbUser->replaceUserVars( $link['text'], false, false, null, false );
				$link['url']				=	$cbUser->replaceUserVars( $link['url'], false, false, null, false );
				$link['media']['url']		=	$cbUser->replaceUserVars( $link['media']['url'], false, false, null, false );
				$link['media']['custom']	=	$cbUser->replaceUserVars( $link['media']['custom'], false, false, null, false );
			}

			$hasMedia						=	( ( $link['type'] == 'custom' ) || ( ( $link['type'] != 'url' ) && $link['media']['url'] ) || ( ( $link['type'] == 'url' ) && $link['media']['url'] && $link['thumbnail'] ) );

			$return							.=		'<div class="activityContainerAttachment streamItemScrollContent ' . ( $link['type'] == 'url' ? 'streamMedia media' : 'streamPanel panel' ) . ( $i != 0 ? ' hidden' : null ) . '">';

			if ( $hasMedia ) {
				$return						.=			'<div class="activityContainerAttachmentMedia ' . ( $link['type'] == 'url' ? 'streamMediaLeft media-left' : 'streamPanelBody panel-body' ) . ' text-center">';

				switch ( $link['type'] ) {
					case 'custom':
						$return				.=				$link['media']['custom'];
						break;
					case 'video':
						if ( $link['media']['mimetype'] == 'video/youtube' ) {
							if ( preg_match( '%(?:(?:watch\?v=)|(?:embed/)|(?:be/))([A-Za-z0-9_-]+)%', $link['media']['url'], $matches ) ) {
								$return		.=				'<iframe width="100%" height="360" src="https://www.youtube.com/embed/' . htmlspecialchars( $matches[1] ) . '" frameborder="0" allowfullscreen class="streamItemVideo"></iframe>';
							}
						} else {
							$return			.=				'<video width="100%" height="100%" style="width: 100%; height: 100%;" src="' . htmlspecialchars( $link['media']['url'] ) . '" type="' . htmlspecialchars( $link['media']['mimetype'] ) . '" controls="controls" preload="auto" class="streamItemVideo"></video>';
						}
						break;
					case 'audio':
						$return				.=				'<audio style="width: 100%;" src="' . htmlspecialchars( $link['media']['url'] )  . '" type="' . htmlspecialchars( $link['media']['mimetype'] ) . '" controls="controls" preload="auto" class="streamItemAudio"></audio>';
						break;
					case 'image':
						$return				.=				'<a href="' . htmlspecialchars( $link['url'] ) . '" rel="nofollow" target="_blank">'
											.					'<img src="' . htmlspecialchars( $link['media']['url'] ) . '" class="img-responsive streamItemImage" />'
											.				'</a>';
						break;
					case 'url':
					default:
						$return				.=				'<a href="' . htmlspecialchars( $link['url'] ) . '" rel="nofollow" target="_blank">'
											.					'<img src="' . htmlspecialchars( $link['media']['url'] ) . '" class="img-responsive streamItemImage" />'
											.				'</a>';
						break;
				}

				$return						.=			'</div>';
			}

			if ( $link['title'] || $link['description'] || ( ( ! $link['internal'] ) && ( ( ! $link['title'] ) || $link['text'] ) ) || ( $count > 1 ) ) {
				$hypertext					=	( $link['text'] ? CBTxt::T( $link['text'] ) : $link['url'] );

				$return						.=			'<div class="streamPanelFooter streamItemDisplay activityContainerAttachmentInfo panel-footer' . ( $link['type'] == 'url' ? ' streamMediaBody media-body' : null ) . '">'
											.				'<div class="cbMoreLess">'
											.					'<div class="cbMoreLessContent">'
											.						( $link['title'] ? '<div><strong><a href="' . htmlspecialchars( $link['url'] ) . '" rel="nofollow" target="_blank">' . ( $isStatus ? htmlspecialchars( CBTxt::T( $link['title'] ) ) : CBTxt::T( $link['title'] ) ) . '</a></strong></div>' : null )
											.						( $isStatus ? htmlspecialchars( CBTxt::T( $link['description'] ) ) : CBTxt::T( $link['description'] ) )
											.					'</div>'
											.					'<div class="activityContainerAttachmentUrl small">'
											.						( ( ! $link['internal'] ) && ( ( ! $link['title'] ) || $link['text'] ) ? '<a href="' . htmlspecialchars( $link['url'] ) . '" rel="nofollow" target="_blank">' . ( $isStatus ? htmlspecialchars( $hypertext ) : $hypertext ) . '</a>' : null )
											.						( $count > 1 ? '<div class="activityContainerAttachmentCount text-muted">' . ( $i + 1 ) . ' - ' . $count . '</div>' : null )
											.					'</div>'
											.					'<div class="cbMoreLessOpen fade-edge hidden">'
											.						'<a href="javascript: void(0);" class="cbMoreLessButton">' . CBTxt::T( 'See More' ) . '</a>'
											.					'</div>'
											.				'</div>'
											.			'</div>';
			}

			if ( $isStatus && ( $cbModerator || $rowOwner ) ) {
				$return						.=			'<div class="streamPanelFooter streamItemEdit activityContainerAttachmentInfo panel-footer' . ( $link['type'] == 'url' ? ' streamMediaBody media-body' : null ) . ' hidden">'
											.				'<input type="text" id="' . $stream->id() . '_links_title_edit_' . (int) $row->get( 'id' ) . '_' . ( $i + 1 ) . '" name="links[' . $i . '][title]" value="' . htmlspecialchars( $link['title'] ) . '" class="streamInput streamInputLinkTitle form-control" placeholder="' . htmlspecialchars( CBTxt::T( 'Title' ) ) . '" />'
											.				'<textarea id="' . $stream->id() . '_links_description_edit_' . (int) $row->get( 'id' ) . '_' . ( $i + 1 ) . '" name="links[' . $i . '][description]" rows="1" class="streamInput streamInputAutosize streamInputLinkDescription form-control" placeholder="' . htmlspecialchars( CBTxt::T( 'Description' ) ) . '">' . htmlspecialchars( $link['description'] ) . '</textarea>';

				if ( $link['type'] == 'url' ) {
					$return					.=				'<div class="streamInput">'
											.					'<label class="checkbox-inline">'
											.						'<input type="checkbox" id="' . $stream->id() . '_links_thumbnail_edit_' . (int) $row->get( 'id' ) . '_' . ( $i + 1 ) . '" name="links[' . $i . '][thumbnail]" value="0"' . ( ! $link['thumbnail'] ? ' checked="checked"' : null ) . '> ' . CBTxt::T( 'Do not display thumbnail' )
											.					'</label>'
											.				'</div>';
				}

				$return						.=			'</div>';
			}

			$return							.=		'</div>';
		}

		$return								.=		'<div class="streamItemScrollRight' . ( $count > 1 ? null : ' hidden' ) . '">'
											.			'<table>'
											.				'<tr>'
											.					'<td>'
											.						'<span class="streamItemScrollRightIcon fa fa-chevron-right"></span>'
											.					'</td>'
											.				'</tr>'
											.			'</table>'
											.		'</div>'
											.	'</div>';

		return $return;
	}
}