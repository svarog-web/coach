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
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Tags;
use CB\Plugin\Activity\CBActivity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbactivityTags
{

	/**
	 * @param TagTable[]      $rows
	 * @param Tags            $stream
	 * @param int             $output 0: Normal, 1: Raw, 2: Inline, 3: Load , 4: Save
	 * @param UserTable       $user
	 * @param UserTable       $viewer
	 * @param cbPluginHandler $plugin
	 * @return null|string
	 */
	static public function showTags( $rows, $stream, $output, $user, $viewer, $plugin )
	{
		global $_PLUGINS;

		CBActivity::loadHeaders( $output );

		$sourceClean			=	htmlspecialchars( $stream->source() );

		$tags					=	array();
		$return					=	null;

		$_PLUGINS->trigger( 'activity_onBeforeDisplayTags', array( &$return, &$rows, $stream, $output ) );

		if ( $rows ) foreach ( $rows as $row ) {
			$rowId				=	$sourceClean . 'Tag' . (int) $row->get( 'id' );

			if ( is_numeric( $row->get( 'user' ) ) ) {
				$name			=	CBuser::getInstance( (int) $row->get( 'user' ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
			} else {
				$name			=	htmlspecialchars( $row->get( 'user' ) );
			}

			if ( ! $name ) {
				continue;
			}

			$tags[]				=	'<span id="' . $rowId . '" class="streamTag">'
								.		$name
								.	'</span>';
		}

		if ( $tags ) {
			$return				.=	( ! in_array( $output, array( 1, 4 ) ) ? '<span class="' . $sourceClean . 'Tags streamTags">' : null );

			if ( count( $tags ) > 2 ) {
				$tagOne			=	array_shift( $tags );
				$tagTwo			=	array_shift( $tags );

				$moreTooltip	=	cbTooltip( null, '<div class="streamTagRow">' . implode( '</div><div class="streamTagRow">', $tags ) . '</div>', null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-open-event="click" data-cbtooltip-close-event="click unfocus" data-cbtooltip-button-hide="true"' );
				$more			=	'<a href="javascript: void(0);"' . $moreTooltip . '>' . CBTxt::T( 'TAGS_MORE', '%%COUNT%% more', array( '%%COUNT%%' => count( $tags ) ) ) . '</a>';

				$return			.=		CBTxt::T( 'TAGS_MORE_THAN_TWO', '[tag_1], [tag_2], and [more]', array( '[tag_1]' => $tagOne, '[tag_2]' => $tagTwo, '[more]' => $more ) );
			} elseif ( count( $tags ) > 1 ) {
				$return			.=		CBTxt::T( 'TAGS_TWO', '[tag_1] and [tag_2]', array( '[tag_1]' => $tags[0], '[tag_2]' => $tags[1] ) );
			} else {
				$return			.=		$tags[0];
			}

			$return				.=	( ! in_array( $output, array( 1, 4 ) ) ? '</span>' : null );
		}

		$return					.=	CBActivity::reloadHeaders( $output );

		$_PLUGINS->trigger( 'activity_onAfterDisplayTags', array( &$return, $rows, $stream, $output ) );

		return $return;
	}
}