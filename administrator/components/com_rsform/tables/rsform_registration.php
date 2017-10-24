<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_Registration extends JTable
{
	public $form_id 				= null;
	public $action					= 1;
	public $action_field			= null;
	public $groups 					= 2;
	public $vars 					= '';
	public $activation 				= 1;
	public $cbactivation 			= 1;
	public $defer_admin_email 		= 0;
	public $user_activation_action 	= null;
	public $admin_activation_action = null;
	public $user_activation_url 	= null;
	public $admin_activation_url 	= null;
	public $user_activation_text 	= null;
	public $admin_activation_text 	= null;
	public $itemid					= null;
	public $published 				= 0;
	
	public function __construct(& $db) {
		parent::__construct('#__rsform_registration', 'form_id', $db);
	}
	
	public function load($keys = null, $reset = true) {
		$result = parent::load($keys, $reset);
		
		if ($result) {
			$this->groups = explode(',', $this->groups);
			
			$this->vars = unserialize($this->vars);
			if ($this->vars === false || !is_array($this->vars)) {
				$this->vars = array();
			}
			
			if (!isset($this->vars['password'])) {
				$this->vars['password'] = isset($this->vars['password1']) ? $this->vars['password1'] : '';
			}
			
			if (!isset($this->vars['email'])) {
				$this->vars['email'] = isset($this->vars['email1']) ? $this->vars['email1'] : '';
			}
		}
		
		return $result;
	}
	
	// Validate data before save
	public function check() {
		if (is_array($this->groups)) {
			$this->groups = implode(',', $this->groups);
		}
		
		if (is_array($this->vars)) {
			$this->vars	= serialize($this->vars);
		}
		
		// Check if we need to add the empty record to the database
		$row = self::getInstance('RSForm_Registration', 'Table');
		if (!$row->load($this->form_id)) {
			$db 	= JFactory::getDbo();
			$query	= $db->getQuery(true)
						 ->insert($db->qn($this->getTableName()))
						 ->set($db->qn('form_id').'='.$db->q($this->form_id));
			$db->setQuery($query)->execute();
		}
		
		return true;
	}
}