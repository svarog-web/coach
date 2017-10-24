<?php
/**
 * Joom!Fish - Multi Lingual extention and translation manager for Joomla!
 * Copyright (C) 2003 - 2011, Think Network GmbH, Munich
 *
 * All rights reserved.  The Joom!Fish project is a set of extentions for
 * the content management system Joomla!. It enables Joomla!
 * to manage multi lingual sites especially in all dynamic information
 * which are stored in the database.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * -----------------------------------------------------------------------------
 * $Id: controllerHelper.php 1551 2011-03-24 13:03:07Z akede $
 * @package joomfish
 * @subpackage controllerHelper
 *
*/


defined( '_JEXEC' ) or die( 'Restricted access' );


class  FalangControllerHelper  {

	/**
	 * Sets up ContentElement Cache - mainly used for data to determine primary key id for tablenames ( and for
	 * future use to allow tables to be dropped from translation even if contentelements are installed )
	 */
	static function _setupContentElementCache()
	{
		$db = JFactory::getDBO();
		// Make usre table exists otherwise create it.
		$db->setQuery( "CREATE TABLE IF NOT EXISTS `#__falang_tableinfo` ( `id` int(11) NOT NULL auto_increment, `joomlatablename` varchar(100) NOT NULL default '',  `tablepkID`  varchar(100) NOT NULL default '', PRIMARY KEY (`id`)) ENGINE=MyISAM");
		$db->query();
		// clear out existing data
		$db->setQuery( "DELETE FROM `#__falang_tableinfo`");
		$db->query();
		$falangManager = FalangManager::getInstance();
		$contentElements = $falangManager->getContentElements(true);
		$sql = "INSERT INTO `#__falang_tableinfo` (joomlatablename,tablepkID) VALUES ";
		$firstTime = true;
		foreach ($contentElements as $key => $jfElement){
			$tablename = $jfElement->getTableName();
			$refId = $jfElement->getReferenceID();
			$sql .= $firstTime?"":",";
			$sql .= " ('".$tablename."', '".$refId."')";
			$firstTime = false;
		}

		$db->setQuery( $sql);
		$db->query();

	}


	public static function _checkDBCacheStructure (){

        //TODO : sbou revoir la methode de cache
        return true;
/*
		JCacheStorageJfdb::setupDB();

		$db =  JFactory::getDBO();
		$sql = "SHOW COLUMNS FROM #__dbcache LIKE 'value'";
		$db->setQuery($sql);
		$data = $db->loadObject();
		if (isset($data) && strtolower($data->Type)!=="mediumblob"){
			$sql = "DROP TABLE #__dbcache";
			$db->setQuery($sql);
			$db->query();

			JCacheStorageJfdb::setupDB();
		}
*/
	}

	public static function _checkDBStructure (){
		$db =  JFactory::getDBO();
		$sql = "SHOW INDEX FROM #__falang_content";// where key_name = 'jfContent'";
		$db->setQuery($sql);
		$data = $db->loadObjectList("Key_name");

        if (isset($data['combo'])){
            $sql = "ALTER TABLE `#__falang_content` DROP INDEX `combo`" ;
            $db->setQuery($sql);
            $db->query();
        }
        if (!isset($data['idxFalang1'])){

            $sql = "ALTER TABLE `#__falang_content` ADD INDEX `idxFalang1` ( `reference_id` , `reference_field` , `reference_table` )" ;
            $db->setQuery($sql);
            $db->query();
        }

		if (!isset($data['falangContent'])){
			$sql = "ALTER TABLE `#__falang_content` ADD INDEX `falangContent` ( `language_id` , `reference_id` , `reference_table` )" ;
			$db->setQuery($sql);
			$db->query();
		}

        if (!isset($data['falangContentLanguage'])){
            $sql = "ALTER TABLE `#__falang_content` ADD INDEX `falangContentLanguage` (`reference_id`, `reference_field`, `reference_table`, `language_id`)" ;
            $db->setQuery($sql);
            $db->query();
        }

		if (!isset($data['reference_id'])){
			$sql = "ALTER TABLE `#__falang_content` ADD INDEX `reference_id` (`reference_id`)" ;
			$db->setQuery($sql);
			$db->query();
        }
        if (!isset($data['language_id'])){
            $sql = "ALTER TABLE `#__falang_content` ADD INDEX `language_id` (`language_id`)" ;
            $db->setQuery($sql);
            $db->query();
        }
        if (!isset($data['reference_table'])){
            $sql = "ALTER TABLE `#__falang_content` ADD INDEX `reference_table` (`reference_table`)" ;
            $db->setQuery($sql);
            $db->query();
        }
        if (!isset($data['reference_field'])){
            $sql = "ALTER TABLE `#__falang_content` ADD INDEX `reference_field` (`reference_field`)" ;
            $db->setQuery($sql);
            $db->query();
        }
	}

	/**
	 * Check Plugin Order since Joomla 3.6.2, language filter need to be set before FalangDatabaseDriver plgin
	 *
	 * @since version 2.7.0
	 */

	public static function _checkPlugin(){
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);

		//language filter must be before falang database driver
		$query->select('extension_id,element,ordering ');
		$query->from('#__extensions');

		$query->where($query->quoteName('type') . '=' . $query->quote('plugin'));
		$query->where($query->quoteName('folder') . '=' . $query->quote('system'));
		$query->where($query->quoteName('element') . 'IN ("languagefilter","falangdriver")');
		$query->order('ordering ASC');

		$db->setQuery($query);
		$list = $db->loadObjectList('element');

		if (isset($list['languagefilter']) and isset($list['falangdriver'])){
			if ((int)$list['languagefilter']->ordering >=  (int)$list['falangdriver']->ordering){
				//we have to fix the order
				$pks = array((int)$list['languagefilter']->extension_id,(int)$list['falangdriver']->extension_id);
				//set order to 1 and 2 - other plugin set to -1 stay at -1
				$order = array(1,2);

				jimport('joomla.application.component.model');
				JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_plugins/models');
				$pluginsModel = JModelLegacy::getInstance( 'Plugin', 'PluginsModel' );

				// Save the ordering
				$return = $pluginsModel->saveorder($pks, $order);

				$application = JFactory::getApplication();
				if ($return === false){
					JFactory::getApplication()->enqueueMessage(JText::_('COM_FALANG_PLUGINS_SYSTEM_ORDER_FAILED'), 'error');
				} else {
					JFactory::getApplication()->enqueueMessage(JText::_('COM_FALANG_PLUGINS_SYSTEM_ORDER_FIXED'), 'notice');
				}
			}
		}

	}


}
