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
<?php if ( modCBOnlineHelper::getPlugins( $params, 'beforeLinks' ) || $cbUsers || modCBOnlineHelper::getPlugins( $params, 'almostEnd' ) ) { ?>
<ul class="unstyled cbOnlineUsers">
	<?php echo modCBOnlineHelper::getPlugins( $params, 'beforeLinks' ); ?>
	<?php foreach ( $cbUsers as $cbUser ) { ?>
		<li class="cbOnlineUser">
			<?php echo $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true ); ?>
		</li>
	<?php } ?>
	<?php echo modCBOnlineHelper::getPlugins( $params, 'afterUsers' ); ?>
</ul>
<?php } ?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'almostEnd' ); ?>
<?php if ( $postText ) { ?>
	<div class="posttext">
		<p><?php echo $postText; ?></p>
	</div>
<?php } ?>
<?php echo modCBOnlineHelper::getPlugins( $params, 'end' ); ?>