<?php

/**
 * Manage different database configurations.
 *
 * @author Mike Benshoof
 */
class ConfigController extends Zend_Controller_Action
{
    /**
     * The configuration key stored in the session.
     *
     * @param string
     */
    protected $_configKey;

    /**
     * The table configuration object.
     *
     * @var Application_Model_Config
     */
    protected $_tableConfig;

    /**
     * Make sure there is a session variable for the config file.
     *
     *
     */
    public function init()
    {
        $this->_tableConfig = new Application_Model_Config();

        // Check to see if there is a cartID registered.
        if (!Zend_Registry::get('session')->isConfigLoaded) {
            //echo "No config really loaded in session!!";
            $this->_configKey = null;
        } else {
            //echo "Loading config key from from session...";
            $this->_configKey = Zend_Registry::get('session')->loadedConfig;
        }
    }

    /**
     * Main entry point - list saved configs, show selected, allow user to add new.
     *
     * @return void
     */
    public function indexAction()
    {
        // action body
        $this->view->headTitle("Configuration Selector");
        $configList = $this->_tableConfig->getAvailableConfigs();
        
        foreach ($configList as $key => &$config) {
            $tables = $this->_tableConfig->listTables($key);
            $config['numTables'] = count($tables);
        }

        $this->view->configList = $configList;
    }

    /**
     * View the details for a particular Config
     *
     * @return void
     */
    public function detailsAction()
    {
    	if (is_null($this->_getParam("cid"))) {
    		throw new Exception("No config parameter specified.");
    	} 

    	$config = new Application_Model_Config();
    	$details = $config->getDetails($this->_getParam("cid"));
    	
    	dar($details);
    	$this->_view->details = $details;
    }

    /**
     * Load a configuration into the session.
     *
     * @return void
     */
    public function loadAction()
    {
        // No config key is specified.
        if (is_null($this->_getParam("key"))) {
            throw new Exception("No config key specified.");
        }

        // This isn't a valid configuration file.
        if (!$this->_tableConfig->configExists($this->_getParam("key"))) {
            throw new Exception("Invalid config key specified.");
        }

        // Set up the session variables and redirect to the comparison base.
        Zend_Registry::get('session')->loadedConfig = $this->_getParam("key");
        Zend_Registry::get('session')->isConfigLoaded = true;
        $this->_redirect("/compare");
    }
}

