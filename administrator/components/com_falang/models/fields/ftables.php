<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_falang
 *
 * @author      Stéphane Bouey
 * @copyright	Copyright (C) 2014 Faboba
 * @license		GNU/GPL, see LICENSE.php
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldFtables extends JFormFieldList
{
    public $type = 'ftables';

    public function getOptions() {
        $options = array();

        $options = array();
        // Add our options to the array
        // value=> [table name] , text => [Name to Display] //name to display content element name ?
        $options[] = array("value" => 'content', "text" => "Articles");
        $options[] = array("value" => "menu", "text" => "Menu");

        return $options;
//        $items = array('article'=>'Article','menu'=>'Menu');
//
//        // Build the field options.
//        if (!empty($items))
//        {
//            foreach ($items as $key =>$item)
//            {
//                $options[] = JHtml::_('select.option', $key, $item);
//            }
//        }
//
//        // Merge any additional options in the XML definition.
//        $options = array_merge(parent::getOptions(), $options);
//
//        return $options;


    }

}