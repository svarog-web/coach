<?php
/**
 * @package     Falang Driver
 * @subpackage  Add Falang Driver
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

//Global definitions use for front
if( !defined('DS') ) {
    define( 'DS', DIRECTORY_SEPARATOR );
}


jimport('joomla.plugin.plugin');

/**
 * Falang Driver Plugin
 */
class plgSystemFalangdriver extends JPlugin
{

	public function __construct(&$subject, $config = array())
	{


		parent::__construct($subject, $config);

//        // This plugin is only relevant for use within the frontend!
		if (JFactory::getApplication()->isAdmin())
		{
			return;
		}

		//@since 2.9.0
		//add this setup in the constuctor due to system plugin who use $this->db (constucted by reflexion of JPlugin)
		//and no more in the onAfterInitialise
		if (!$this->isFalangDriverActive())
		{
			$this->setupDatabaseDriverOverride();
		}

	}

    /**
     * System Event: onAfterInitialise
     *
     * @return	string
     */
    function onAfterInitialise()
    {
        // This plugin is only relevant for use within the frontend!
        if (JFactory::getApplication()->isAdmin())
        {
            return;
        }

        //fix for joomla > 3.4.0
        $app = JFactory::getApplication();
        if ($app->isSite()) {
            $router = $app->getRouter();

            // attach build rules for translation on SEF
            $router->attachBuildRule(array($this, 'buildRule'));

            // attach build rules for translation on SEF
            $router->attachParseRule(array($this, 'parseRule'));
        }
        //end fix
    }

    public function buildRule(&$router, &$uri)
    {
        $lang = $uri->getVar('lang');
        $default_lang	= JComponentHelper::getParams('com_languages')->get('site', 'en-GB');

        //we build the route for category list article
        if ($lang != $default_lang && $uri->getVar('id') != null && $uri->getVar('catid') != null) {

            $fManager = FalangManager::getInstance();
            $id_lang = $fManager->getLanguageID($lang);

            // Make sure we have the id and the alias
            if (strpos($uri->getVar('id'), ':') > 0)
            {
                list($tmp, $id) = explode(':', $uri->getVar('id'), 2);
                $db = JFactory::getDbo();
                $dbQuery = $db->getQuery(true)
                    ->select('fc.value')
                    ->from('#__falang_content fc')
                    ->where('fc.reference_id = '.(int)$tmp)
                    ->where('fc.language_id = '.(int) $id_lang )
                    ->where('fc.reference_field = \'alias\'')
                    ->where('fc.published = 1')
                    ->where('fc.reference_table = \'content\'');

                $db->setQuery($dbQuery);
                $alias = $db->loadResult();
                if (isset($alias)) {
                    $uri->setVar('id',$tmp. ':' . $alias);
                }
            }
            // Make sure we have the id and the alias
            if (strpos($uri->getVar('catid'), ':') > 0)
            {
                list($tmp2, $catid) = explode(':', $uri->getVar('catid'), 2);

                $db = JFactory::getDbo();
                $dbQuery = $db->getQuery(true)
                    ->select('fc.value')
                    ->from('#__falang_content fc')
                    ->where('fc.reference_id = '.(int)$tmp2)
                    ->where('fc.language_id = '.(int) $id_lang )
                    ->where('fc.reference_field = \'alias\'')
                    ->where('fc.published = 1')
                    ->where('fc.reference_table = \'categories\'');

                $db->setQuery($dbQuery);
                $alias = $db->loadResult();
                if (isset($alias)) {
                    $uri->setVar('catid',$tmp2. ':' . $alias);
                }
            }
        }

        //fix canonical if sef plugin is enabled
        $sef_plugin = JPluginHelper::getPlugin('system', 'sef');
        if (!empty($sef_plugin)) {
            if ($lang != $default_lang && $uri->getVar('id') != null && $uri->getVar('catid') != null) {
                $fManager = FalangManager::getInstance();
                $id_lang = $fManager->getLanguageID($lang);

                // Make sure we have the id and the alias
                if (strpos($uri->getVar('id'), ':') === false)
                {
                    //we use id in the query to be translated.
                    $db = JFactory::getDbo();
                    $dbQuery = $db->getQuery(true)
                        ->select('alias,id')
                        ->from('#__content')
                        ->where('id=' . (int) $uri->getVar('id'));
                    $db->setQuery($dbQuery);
                    $alias = $db->loadResult();
                    if (isset($alias)) {
                        $uri->setVar('id',$uri->getVar('id') . ':' . $alias);
                    }
                }
            }
        }

        //build route for hikashop product
        if ( $uri->getVar('option') == 'com_hikashop' &&  $uri->getVar('ctrl') == 'product' &&  $uri->getVar('task')== 'show' ) {
            // on native language look in falang table
            if ($default_lang != $lang ){
                $fManager = FalangManager::getInstance();
                $id_lang = $fManager->getLanguageID($lang);
                $id = $uri->getVar('cid');
                $db = JFactory::getDbo();
                $dbQuery = $db->getQuery(true)
                    ->select('fc.value')
                    ->from('#__falang_content fc')
                    ->where('fc.reference_id = '.(int)$id)
                    ->where('fc.language_id = '.(int) $id_lang )
                    ->where('fc.reference_field = \'product_alias\'')
                    ->where('fc.published = 1')
                    ->where('fc.reference_table = \'hikashop_product\'');

                $db->setQuery($dbQuery);
                $alias = $db->loadResult();
                if (isset($alias)) {
                    $uri->setVar('name', $alias);
                }

            } else {
                // translated languague look in native table
                $id = $uri->getVar('cid');
                $db = JFactory::getDbo();
                $dbQuery = $db->getQuery(true)
                    ->select('product_alias')
                    ->from('#__hikashop_product')
                    ->where('product_id = '.(int)$id);
                $db->setQuery($dbQuery);
                $alias = $db->loadResult();
                if (isset($alias)) {
                    $uri->setVar('name', $alias);
                }
            }
            //
        }
        //build route for hikahsop category list
        if ( $uri->getVar('option') == 'com_hikashop' &&  $uri->getVar('ctrl') == 'category' &&  $uri->getVar('task')== 'listing' ) {
            // on native language look in falang table
            if ($default_lang != $lang) {
                $fManager = FalangManager::getInstance();
                $id_lang = $fManager->getLanguageID($lang);
                $id = $uri->getVar('cid');
                $db = JFactory::getDbo();
                $dbQuery = $db->getQuery(true)
                    ->select('fc.value')
                    ->from('#__falang_content fc')
                    ->where('fc.reference_id = ' . (int)$id)
                    ->where('fc.language_id = ' . (int)$id_lang)
                    ->where('fc.reference_field = \'category_alias\'')
                    ->where('fc.published = 1')
                    ->where('fc.reference_table = \'hikashop_category\'');

                $db->setQuery($dbQuery);
                $alias = $db->loadResult();
                if (isset($alias)) {
                    $uri->setVar('name', $alias);
                }

            } else {
                // translated languague look in native table
                $id = $uri->getVar('cid');
                $db = JFactory::getDbo();
                $dbQuery = $db->getQuery(true)
                    ->select('category_alias')
                    ->from('#__hikashop_category')
                    ->where('category_id = ' . (int)$id);
                $db->setQuery($dbQuery);
                $alias = $db->loadResult();
                if (isset($alias)) {
                    $uri->setVar('name', $alias);
                }
            }
        }
        //build route for k2 category list
        //v2.2.2 add download test due to download link bug in other case.
        if ( $uri->getVar('option') == 'com_k2' &&  $uri->getVar('view') == 'item' && $uri->getVar('task') != 'download' ) {
            // on native language look in falang table
            if ($default_lang != $lang ) {
                $fManager = FalangManager::getInstance();
                $id_lang = $fManager->getLanguageID($lang);

                // Make sure we have the id and the alias
                if (strpos($uri->getVar('id'), ':') > 0) {
                    list($tmp, $id) = explode(':', $uri->getVar('id'), 2);
                    $db = JFactory::getDbo();
                    $dbQuery = $db->getQuery(true)
                        ->select('fc.value')
                        ->from('#__falang_content fc')
                        ->where('fc.reference_id = ' . (int)$tmp)
                        ->where('fc.language_id = ' . (int)$id_lang)
                        ->where('fc.reference_field = \'alias\'')
                        ->where('fc.published = 1')
                        ->where('fc.reference_table = \'k2_items\'');

                    $db->setQuery($dbQuery);
                    $alias = $db->loadResult();
                    if (isset($alias)) {
                        $uri->setVar('id', $tmp . ':' . $alias);
                    }
                }
            } else {
                // translated languague look in native table
	            $tmp = $uri->getVar('id');
	            // Make sure we have the id and the alias
	            if (strpos($tmp, ':') > 0) {
		            list($tmp, $id) = explode(':', $tmp, 2);
	            }

                $db = JFactory::getDbo();
                $dbQuery = $db->getQuery(true)
                    ->select('alias')
                    ->from('#__k2_items')
                    ->where('id = '.(int)$tmp);
                $db->setQuery($dbQuery);
                $alias = $db->loadResult();
                if (isset($alias)) {
                    $uri->setVar('id', $tmp . ':' . $alias);
                }
            }
        }

        return array();
    }

	public function parseRule(&$router, &$uri) {
		static $done = false;
		if (!$done)
		{
			$done = true;
			$conf = JFactory::getConfig();
			$lang                       = JFactory::getLanguage();
			$default_lang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');

			// Workaround for Joomla > 3.7.0, we need to set the correct language for the cache handler because the menu get already cached with the
			// language defined in JApplicationSite::initialiseApp(), but this language is the wrong if we change the language because language detection
			// is done in the Joomla system plugin languagefilter. The solution is to load the com_menus cache and set the correct language and reload
			// the menu, this is already done for Jooma 3.4.0, it seams that the cache works since 3.7.0
			if ($conf->get('caching',0) > 0)
			{
				$cache                      = JFactory::getCache('com_menus', 'callback');
				$cache->options['language'] = $lang->getTag();
				if ($lang->getTag() != $default_lang)
				{
					$cache->options['caching'] = false;
				}
			}

			//reload menu
			JFactory::getApplication()->getMenu()->__construct();
			//rewrite Menu route with translated alias
			$app = JFactory::getApplication();
			$menu = $app->getMenu()->getMenu();

			//workaround for Joomla > 3.7.0 continue.
			if ($conf->get('caching',0) > 0)
			{
				foreach($menu as &$item) {
					$item->route = '';
					if ($item->level > 1) {
						if (array_key_exists($item->parent_id, $menu)) {
							$item->route = $menu[$item->parent_id]->route.'/';
						}
					}
					$item->route .= $item->alias;
				}
			}

		}
		return array();
	}

    public function isFalangDriverActive()
    {
        $db = JFactory::getDBO();

        return is_a($db, 'JFalangDatabase');
    }


    function onAfterDispatch()
    {
        if (JFactory::getApplication()->isSite() && $this->isFalangDriverActive()) {
            include_once( JPATH_ADMINISTRATOR . '/components/com_falang/version.php');
            $version = new FalangVersion();
            if ($version->_versiontype == 'free'  ) {
                FalangManager::setBuffer();
            }
            return true;
        }
    }


    function setupDatabaseDriverOverride()
    {
        //override only the override file exist
        if (file_exists(dirname(__FILE__) . '/falang_database.php'))
        {

            require_once( dirname(__FILE__) . '/falang_database.php');

            $conf = JFactory::getConfig();

            $host = $conf->get('host');
            $user = $conf->get('user');
            $password = $conf->get('password');
            $db = $conf->get('db');
            $dbprefix = $conf->get('dbprefix');
            $driver = $conf->get('dbtype');
            $debug = $conf->get('debug');

            $options = array('driver' => $driver,"host" => $host, "user" => $user, "password" => $password, "database" => $db, "prefix" => $dbprefix, "select" => true);
            $db = new JFalangDatabase($options);
            $db->setDebug($debug);


            if ($db->getErrorNum() > 2)
            {
                JError::raiseError('joomla.library:' . $db->getErrorNum(), 'JDatabase::getInstance: Could not connect to database <br/>' . $db->getErrorMsg());
            }

            // replace the database handle in the factory
            JFactory::$database = null;
            JFactory::$database = $db;

            $test = JFactory::getDBO();

        }

    }

    private function setBuffer()
    {
        $doc = JFactory::getDocument();
        $cacheBuf = $doc->getBuffer('component');

        $cacheBuf2 =
            '<div><a title="Faboba : Cr&eacute;ation de composant'.
            'Joomla" style="font-size: 9px;; visibility: visible;'.
            'display:inline;" href="http://www.faboba'.
            '.com" target="_blank">FaLang tra'.
            'nslation syste'.
            'm by Faboba</a></div>';

        if ($doc->_type == 'html')
            $doc->setBuffer($cacheBuf . $cacheBuf2,'component');

    }


    /*
     * Use trigger to activate the language selection in the template
     */
    function onContentPrepareForm($form, $data)
    {
        if (JFactory::getApplication()->isSite()){return;}

	    $this->enabledTplTranslation($form,$data);

	    $custom_fields = JPluginHelper::isEnabled('system', 'fields');
	    if ($custom_fields){
		    $this->loadCustomFields($form, $data);
	    }
    }

	//use to set the value of the custom fields to the falang translation form
	//custom fields exist only since Joomla 3.7
	private function loadCustomFields($form, $data){
		$input = JFactory::getApplication()->input;
		$option = $input->get('option');
		$task = $input->get('task');
		$catid = $input->get('catid');
		$language_id = $input->get('language_id');

		if ($option == 'com_falang' && ($task == 'translate.edit' || ($task == 'translate.apply') )) {


			JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

			$context = $form->getName();

			// When a category is edited, the context is com_categories.categorycom_content
			if (strpos($context, 'com_categories.category') === 0) {
				$context = str_replace('com_categories.category', '', $context) . '.categories';
			}

			$parts = FieldsHelper::extract($context, $form);

			if (!$parts) {
				return true;
			}

			// Getting the fields
			$fields = FieldsHelper::getFields($parts[0] . '.' . $parts[1], $data);

			$db = JFactory::getDbo();

			foreach ($fields as $field)
			{
				//get falang translation for this field published or not
				$query = $db->getQuery(true);
				$query->select($query->qn('value'))
					->from($query->qn('#__falang_content'))
					->where($query->qn('language_id') . ' = ' . $query->q($language_id))
					->where($query->qn('reference_id') . ' = ' . $query->q($field->id))
					->where($query->qn('reference_field') . ' = ' . $query->q('value'))
					->where($query->qn('published') . ' = ' . $query->q('1'))
					->where($query->qn('reference_table') . ' = ' . $query->q('fields_values'));

				$db->setQuery($query);
				$value = $db->loadResult();

				if (!empty($value)) {
					$form->setValue($field->name, 'com_fields', $value);
				} else {
					//translation value not found get joomla item value

					$form->setValue($field->name, 'com_fields', $field->default_value);
				}
			}

		}

		return true;

	}

	//use to enable template by langugage (paid version only)
	private function enabledTplTranslation($form, $data){
		jimport('joomla.application.component.helper');
		$params = JComponentHelper::getParams('com_falang');
		$show_tpl_lang = $params->get('show_tpl_lang');

		if (!isset($show_tpl_lang) || $show_tpl_lang == '0' ) {return;}


		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}
		if ((is_array($data) && array_key_exists('home', $data))
			|| ((is_object($data) && isset($data->home) ))) {
			$form->setFieldAttribute('home', 'readonly', 'false');
		}
	}

	//throw by Falang
	// use for joomla 3.7+ to save the custom fields in th custom fields table
	public function onAfterTranslationSave($post){
		/*
		$input = JFactory::getApplication()->input;
		$catid = $input->get('catid');
		$language_id = $input->get('language_id');
		$formData = new JRegistry($input->get('jform', '', 'array'));
		$context = $catid;

		//First release only content supported.
		if ($context != 'content'){return;}

		//TODO not set article here
		$fields = FieldsHelper::getFields('com_'.$context. '.' . 'article', $post);

		if (!$fields) {
			return true;
		}

		// Get the translated fields data
		$fieldsData = !empty($formData) ? (array)$formData['com_fields'] : array();

		// Loading the model
		$model = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));
		$db = JFactory::getDbo();
		$user = JFactory::getUser();


		// Loop over the fields
		foreach ($fields as $field) {
			// Determine the value if it is available from the data
			$value = key_exists($field->alias, $fieldsData) ? $fieldsData[$field->alias] : null;

			//get previous value if exit to make update or insert
			$query = $db->getQuery(true);
			$query->select($query->qn('id'))
				->from($query->qn('#__falang_content'))
				->where($query->qn('language_id') . ' = ' . $query->q($language_id))
				->where($query->qn('reference_id') . ' = ' . $query->q($field->id))
				->where($query->qn('reference_field') . ' = ' . $query->q('value'))
				->where($query->qn('reference_table') . ' = ' . $query->q('fields_values'));

			$db->setQuery($query);
			$falangId = $db->loadResult();

			//get joomla item value
			$query = $db->getQuery(true);
			$query->select($query->qn('value'))
				->from($query->qn('#__fields_values'))
				->where($query->qn('field_id') . ' = ' . $query->q($field->id));
			$db->setQuery($query);
			$joomlaValues = $db->loadObjectList();

			//store the field in Falang table
			if (isset($value)){
				$fieldContent = new falangContent($db);
				if (isset($falangId)){$fieldContent->id = $falangId;}
				$fieldContent->reference_id = $field->id ;
				$fieldContent->language_id = $language_id;
				$fieldContent->reference_table= 'fields_values';
				$fieldContent->reference_field= 'value';
				$fieldContent->value = $value;
				// original value will be already md5 encoded - based on that any encoding isn't needed!
				//$fieldContent->original_value = $originalValue;
				//$fieldContent->original_text = !is_null($originalText)?$originalText:"";

				$fieldContent->modified =  JFactory::getDate()->toSql();

				$fieldContent->modified_by = $user->id;
				$fieldContent->published= true;

				$fieldContent->store();
			}

		}

		return true;
		*/
	}

}