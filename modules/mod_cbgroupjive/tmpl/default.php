<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2016 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

?>
<div class="cb_template cb_template_<?php echo selectTemplate( 'dir' ); ?>">
	<div class="cbGroupJive<?php echo ( $class ? ' ' . htmlspecialchars( $class ) : null ); ?>">
		<div class="cbGroupJiveInner">
			<?php echo $return; ?>
		</div>
	</div>
</div>