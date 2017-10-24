<?php 
/*
*/
defined('_JEXEC') or die('Restricted access');
?>

<table class="adminlist  table table-striped">
    <tr class="row0">
            <th width="180" align="left"><?php echo JText::_('Joomla Content search'); ?></th>
        <td><?php
            $joomla_content_search = JPluginHelper::getPlugin('search', 'falangContent');
            if (!empty($joomla_content_search)) {?>
                <i class="fa fa-check fa-success"></i>
            <?php } else { ?>
                <i class="fa fa-times fa-danger"></i> - <a href="http://www.faboba.com/index.php?option=com_ars&view=release&id=41" target="_blank" alt="download"><?php echo JText::_('COM_FALANG_DOWNLOAD_PLUGIN_LINK');?></a>
            <?php } ?>
        </td>
    </tr>
    <tr class="row1">
        <th width="180" align="left"><?php echo JText::_('K2 Content search'); ?></th>
        <td><?php
            $k2_content_search = JPluginHelper::getPlugin('search', 'falangK2');
            if (!empty($k2_content_search)) {?>
                <i class="fa fa-check fa-success"></i>
            <?php } else { ?>
                <i class="fa fa-times fa-danger"></i> - <a href="http://www.faboba.com/index.php?option=com_ars&view=release&id=42" target="_blank" alt="download"><?php echo JText::_('COM_FALANG_DOWNLOAD_PLUGIN_LINK');?></a>
            <?php } ?>
        </td>
    </tr>
    <tr class="row0">
        <th width="180" align="left"><?php echo JText::_('Missing Translation'); ?></th>
        <td><?php
            $falang_missing = JPluginHelper::getPlugin('system', 'falangmissing');
            if (!empty($falang_missing)) {?>
                <i class="fa fa-check fa-success"></i>
            <?php } else { ?>
                <?php if ($this->versionType != 'free') { ?>
                    <i class="fa fa-times fa-danger"></i> - <a href="http://www.faboba.com/index.php?option=com_ars&view=release&id=46" target="_blank" alt="download"><?php echo JText::_('COM_FALANG_DOWNLOAD_PLUGIN_LINK');?></a>
                <?php } else { ?>
                    <?php echo JText::_('COM_FALANG_ONLY_PAID') ?>
                <?php } ?>
            <?php } ?>
        </td>
    </tr>
    <tr class="row1">
        <th width="180" align="left"><?php echo JText::_('Falang Extra Params'); ?></th>
        <td><?php
            $extraparams = JPluginHelper::getPlugin('system', 'falangextraparams');
            if (!empty($extraparams)) {?>
                <i class="fa fa-check fa-success"></i>
            <?php } else { ?>
                <?php if ($this->versionType != 'free') { ?>
                    <i class="fa fa-times fa-danger"></i> - <a href="http://www.faboba.com/index.php?option=com_ars&view=release&id=47" target="_blank" alt="download"><?php echo JText::_('COM_FALANG_DOWNLOAD_PLUGIN_LINK');?></a>
                <?php } else { ?>
                    <?php echo JText::_('COM_FALANG_ONLY_PAID') ?>
                <?php } ?>
            <?php } ?>
        </td>
    </tr>
    <tr class="row0">
        <th width="180" align="left"><?php echo JText::_('K2 extra field'); ?></th>
        <td><?php
            $k2_extra = JPluginHelper::getPlugin('system', 'falangk2');
            if (!empty($k2_extra)) {?>
                <i class="fa fa-check fa-success"></i>
            <?php } else { ?>
                <?php if ($this->versionType != 'free') { ?>
                    <i class="fa fa-times fa-danger"></i> - <a href="http://www.faboba.com/index.php?option=com_ars&view=release&id=43" target="_blank" alt="download"><?php echo JText::_('COM_FALANG_DOWNLOAD_PLUGIN_LINK');?></a>
                <?php } else { ?>
                    <?php echo JText::_('COM_FALANG_ONLY_PAID') ?>
                <?php } ?>
            <?php } ?>
        </td>
    </tr>
    <tr class="row0">
        <th width="180" align="left"><?php echo JText::_('Smart Search - Joomla Content'); ?></th>
        <td><?php
            $k2_extra = JPluginHelper::getPlugin('finder', 'falangcontent');
            if (!empty($k2_extra)) {?>
                <i class="fa fa-check fa-success"></i>
            <?php } else { ?>
                <?php if ($this->versionType != 'free') { ?>
                    <i class="fa fa-times fa-danger"></i> - <a href="http://www.faboba.com/index.php?option=com_ars&view=release&id=109" target="_blank" alt="download"><?php echo JText::_('COM_FALANG_DOWNLOAD_PLUGIN_LINK');?></a>
                <?php } else { ?>
                    <?php echo JText::_('COM_FALANG_ONLY_PAID') ?>
                <?php } ?>
            <?php } ?>
        </td>
    </tr>


</table>