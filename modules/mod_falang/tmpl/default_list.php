<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_falang
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>



<ul class="<?php echo $params->get('inline', 1) ? 'lang-inline' : 'lang-block';?>">
    <?php foreach($list as $language):?>

        
        <!-- >>> [PAID] >>> -->
        <?php if ($params->get('show_active', 0) || !$language->active):?>
            <li class="<?php echo $language->active ? 'lang-active' : '';?>" dir="<?php echo JLanguage::getInstance($language->lang_code)->isRTL() ? 'rtl' : 'ltr' ?>">
                <?php if ($language->display) { ?>
                    <a href="<?php echo $language->link;?>">
                        <?php if ($params->get('image', 1)):?>
                            <?php echo JHtml::_('image', $imagesPath.$language->image.'.'.$imagesType, $language->title_native, array('title'=>$language->title_native), $relativePath);?>
                        <?php endif; ?>
                        <?php if ($params->get('show_name', 1)):?>
                            <?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);?>
                        <?php endif; ?>
                    </a>
                <?php } else { ?>
                    <?php if ($params->get('image', 1)):?>
                        <?php echo JHtml::_('image', $imagesPath.$language->image.'.'.$imagesType, $language->title_native, array('title'=>$language->title_native,'style'=>'opacity:0.5'), $relativePath);?>
                    <?php endif; ?>
                    <?php if ($params->get('show_name', 1)):?>
                        <?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);?>
                    <?php endif; ?>
                <?php } ?>
            </li>
        <?php endif;?>
        <!-- <<< [PAID] <<< -->
    <?php endforeach;?>
</ul>
