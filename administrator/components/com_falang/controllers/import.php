<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.falang
 *
 * @copyright   Copyright (C) 2016 Faboba. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Stéphane Bouey <stephane.bouey@faboba.com>
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class ImportController extends JControllerLegacy
{

    function __construct( )
    {
        parent::__construct();
        $this->registerDefaultTask('show');
    }

    public function process(){
        // Set output format to raw
        $input = JFactory::getApplication()->input;
        $data = $input->get('jform', null, 'array');

        $files = new JInput($_FILES, array());
        $file = $files->get('jform', null, 'array');

        if (empty($file) or empty($file['name']['translationFile'])) {
            $this->setMessage(JText::_("COM_FALANG_IMPORT_FILE_MISSING"));
            $this->setRedirect( 'index.php?option=com_falang&task=import.show' );
            return false;
        }


        $model = $this->getModel('import', 'importModel');
        $model->process();
        $this->setRedirect( 'index.php?option=com_falang&task=import.show' );

    }

    function cancel()
    {
        $this->setRedirect( 'index.php?option=com_falang' );
    }

}