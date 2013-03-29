<?php

class AjaxController extends Zend_Controller_Action
{

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
			$this->_configKey = "Sample-Config";
		} else {
			$this->_configKey = Zend_Registry::get('session')->loadedConfig;
		}
	}

	/**
	 * Just a default landing page.
	 *
	 * @return void
	 */
    public function indexAction()
    {
        echo "No action specified.";
    }

    /**
     * Run the CLI commands to do the sync and capture the output.
     *
     * @return void
     */
    public function runcliAction()
    {
    	$binDir = realpath(APPLICATION_PATH . '/../../cli/bin');
    	$runCmd = "$binDir/run-all.sh";

    	$output = `$runCmd`;
    	$output = str_replace("\n", "\n<br>", $output);

		$this->view->tableList = $this->_tableConfig->listTables($this->_configKey, true);        

        $data = array(
                "status"  => "VALID",
                "output"  => "$output",
                "refresh" => $this->view->render('compare/tableList.phtml'),
            );

        $this->_helper->json($data);
    }    
}

