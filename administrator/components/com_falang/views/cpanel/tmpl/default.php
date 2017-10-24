<?php 
defined('_JEXEC') or die('Restricted access');

$params = JComponentHelper::getParams( 'com_falang' );
$downloadid = $params->get('downloadid');
$version = new FalangVersion();

?>
<?php if (!empty( $this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>


            <!-- Header Message DownloadId Free version -->
            <div class="row-fluid">
                <div class="span10">
                    <?php if (empty($downloadid)) { ?>
                    <div class="alert alert-info">
                        <h4 class="alert-heading"><?php echo JText::_('COM_FALANG_CPANEL_NEEDSAUTH_NOTICE'); ?></h4>
                        <p>
                            <?php echo JText::_('COM_FALANG_CPANEL_NEEDSAUTH'); ?>
                            <a target="_blank"  href="http://www.faboba.com/index.php?option=com_content&view=article&id=39"><?php echo JText::_('COM_FALANG_CPANEL_NEEDSAUTH_LINK'); ?></a>
                        </p>
                    </div>
                    <?php } ?>

                    <?php if($version->_versiontype == 'free') { ?>
                        <div class="alert alert-info">
                            <h4 class="alert-heading"><?php echo JText::_('COM_FALANG_CPANEL_FREE_MSG_TITLE');?></h4>
                            <p>
                                <?php echo JText::_('COM_FALANG_CPANEL_FREE_MSG'); ?>
                            </p>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row-fluid">

                <div class="span4">
                    <div class="box box-success">
                        <div class="box-header with-border" style="height: 150px">
                            <h3 class="box-title"><?php echo JText::_('COM_FALANG_CPANEL_VERSION');?></h3>
                            <div class="body">
                                <?php echo $this->loadTemplate('version'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="span6">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="height: 150px">
                            <h3 class="box-title"><?php echo JText::_('COM_FALANG_CPANEL_CONFIGURATION');?></h3>
                            <div class="body">
                                <?php echo $this->loadTemplate('configuration'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                </div>

            <div class="row-fluid">

            <div class="span4">
                    <div class="box box-info">
                        <div class="box-header with-border" style="height: 150px">
                            <h3 class="box-title"><?php echo JText::_('COM_FALANG_CPANEL_TOOLS');?></h3>
                            <div class="body">
                                <a class="btn btn-app" href="index.php?option=com_falang&task=export.show" alt="">
                                    <i class="fa fa-cloud-upload"></i>
                                    <?php echo JText::_('COM_FALANG_CPANEL_EXPORT');?>
                                </a>
                                <a class="btn btn-app" href="index.php?option=com_falang&task=import.show" alt="">
                                    <i class="fa fa-cloud-download"></i>
                                    <?php echo JText::_('COM_FALANG_CPANEL_IMPORT');?>
                                </a>
                                <a class="btn btn-app" href="index.php?option=com_falang&task=elements.show" alt="">
                                    <i class="fa fa-puzzle-piece"></i>
                                    <?php echo JText::_('COM_FALANG_CPANEL_CONTENT_ELEMENT');?>
                                </a>
                           </div>
                        </div>
                    </div>
                </div>

                <div class="span6">
                    <div class="box box-navy">
                        <div class="box-header with-border" >
                            <h3 class="box-title"><?php echo JText::_('COM_FALANG_CPANEL_PLUGINS');?></h3>
                            <div class="body">
                                <?php echo $this->loadTemplate('plugins'); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>





