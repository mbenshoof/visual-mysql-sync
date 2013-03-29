<?php

/**
 * Manage different database configurations.
 *
 * @author Mike Benshoof
 */
class CompareController extends Zend_Controller_Action
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
	 * @return void
	 */
	public function init()
	{
		$this->_tableConfig = new Application_Model_Config();

		// Check to see if there is a config registered.
		if (!Zend_Registry::get('session')->isConfigLoaded) {
			$this->_redirect("/config");
			//echo "No config really loaded in session!!";
			$this->_configKey = "Sample-Config";
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
        $this->_redirect("/compare/tables");
    }

    /**
     * Set up the initial table selection.
     *
     * @return void
     */
    public function chooseAction()
    {
    	// Table name specified, so redirect to the table view.
       	if(!is_null($this->_getParam("name"))) {
       		$this->_redirect("/compare/tables/name/" . $this->_getParam("name"));
       	}

		// Fetch a list of diff tables.
	   	$this->view->tableList = $this->_tableConfig->listTables($this->_configKey, true);
    }

    /**
     * View the details for a particular Config
     *
     * @return void
     */
    public function tablesAction()
    {
    	// No table name specified, so show a form to display it.
       	if(is_null($this->_getParam("name"))) {
       		$this->_redirect("/compare/choose");
       	}

    	// Set the table name and load the current config.
    	$tableName = $this->_getParam("name");
    	$perPage = 10;

    	// Set the first page or check the paging params.
       	if (is_null($this->_getParam("page"))) {
       		$pageNum = 1;
       	} else {
       		$pageNum = $this->_getParam("page");
       	}

       	$keyOpts = array(
       			"page" => $pageNum,
       			"num"  => $perPage,
       		);

       	// Set up the Comparator with the config.
    	$details = $this->_tableConfig->getDetails($this->_configKey);
    	$compare = new Application_Model_Comparator(
	    		$details,
	    		$tableName
    		);

    	// Make sure we are have a valid table.
    	$compare->validateTable();
    	$results = $compare->fetchKeys($keyOpts)
    						->loadDiffs()
    						->prepareResults();

    	$numSources = $compare->getNumSources();
    	$colWidth = round((float) (100 / $numSources), 1);

    	$this->view->tableName = $this->_getParam("name");
    	$this->view->numSources = $numSources;
    	$this->view->colWidth = $colWidth;
    	$this->view->serverNames = $compare->getServerAliases();
    	$this->view->results = $results;

    	$paginator = Zend_Paginator::factory($compare->getNumKeys());
    	$paginator->setCurrentPageNumber($pageNum)
    				->setItemCountPerPage($perPage);

    	$this->view->paged = $paginator;
    }
}

