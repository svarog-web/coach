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

class ExportController extends JControllerLegacy
{

    function __construct( )
    {
        parent::__construct();
        $this->registerDefaultTask('show');
    }

    public function process(){
        // Set output format to raw
        JFactory::getApplication()->input->set('format', 'raw');

        $model = $this->getModel('export', 'exportModel');
        $model->process();
        $this->setRedirect( 'index.php?option=com_falang&task=export.show' );


    }

    function cancel()
    {
        $this->setRedirect( 'index.php?option=com_falang' );
    }

}