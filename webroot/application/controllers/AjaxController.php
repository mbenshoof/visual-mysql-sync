<?php

class AjaxController extends Zend_Controller_Action
{
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

        $data = array(
                "status" => "VALID",
                "output" => "$output",
            );



        $this->_helper->json($data);
    }    
}

