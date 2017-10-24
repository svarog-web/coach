<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'start' ); ?>
<?php if ( $preText ) { ?>
	<div class="pretext">
		<p><?php echo $preText; ?></p>
	</div>
<?php } ?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'beforeUsers' ); ?>
<?php if ( modCBOnlineHelper::getPlugins( $params, 'beforeLinks' ) || $cbUsers || modCBOnlineHelper::getPlugins( $params, 'afterUsers' ) ) { ?>
<div class="cbOnlineUsers <?php echo htmlspecialchars( $templateClass ); ?>">
	<?php echo modCBOnlineHelper::getPlugins( $params, 'beforeLinks' ); ?>
	<?php foreach ( $cbUsers as $cbUser ) { ?>
		<div class="cbOnlineUser cbCanvasBox cbCanvasBoxSm img-thumbnail">
			<div class="cbCanvasBoxTop bg-muted">
				<div class="cbCanvasBoxBackground">
					<?php echo $cbUser->getField( 'canvas', null, 'html', 'none', 'list', 0, true ); ?>
				</div>
				<div class="cbCanvasBoxPhoto cbCanvasBoxPhotoLeft text-left">
					<?php echo $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true ); ?>
				</div>
			</div>
			<div class="cbCanvasBoxBottom bg-default">
				<div class="cbCanvasBoxRow text-nowrap text-overflow">
					<?php
					if ( $params->get( 'usertext' ) ) {
						echo $cbUser->replaceUserVars( $params->get( 'usertext' ) );
					} else {
						echo $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
					}
					?>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php echo modCBOnlineHelper::getPlugins( $params, 'afterUsers' ); ?>
</div>
<?php } ?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'almostEnd' ); ?>
<?php if ( $postText ) { ?>
	<div class="posttext">
		<p><?php echo $postText; ?></p>
	</div>
<?php } ?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'end' ); ?>