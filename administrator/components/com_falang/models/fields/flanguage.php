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

class JFormFieldFlanguage extends JFormFieldList
{
    public $type = 'flanguage';

    protected function getOptions()
    {

        $client = 'administrator';

        // Make sure the languages are sorted base on locale instead of random sorting
        $languages = JLanguageHelper::createLanguageList($this->value, constant('JPATH_' . strtoupper($client)), true, false);
        if (count($languages) > 1)
        {
            usort(
                $languages,
                function ($a, $b)
                {
                    return strcmp($a["value"], $b["value"]);
                }
            );
        }

        //remove default language
        $defaultLanguage = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');

        foreach ($languages as $key=>$language){
            if ($language['value'] == $defaultLanguage ){
                unset($languages[$key]);
            }
        }
        // Merge any additional options in the XML definition.
        $options = array_merge(
            parent::getOptions(),
            $languages
        );

        return $options;
    }

}