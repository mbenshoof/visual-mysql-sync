<?php

class AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        
    }

	/**
	 * Display the nice list of all the services that are offered.
	 *
	 * @return void
	 */
    public function indexAction()
    {
        echo "No action specified.";
    }

    /**
     * Fetch the raw MC info.
     *
     * @return void
     */
    public function fetchmcAction()
    {
        $type = $this->getRequest()->getPost('type');
        $num = $this->getRequest()->getPost('number');

        switch ($type) {
            case "MC":
                $mc_num = Application_Model_McNumber::fetchMcNumber($num);
                break;
            case "FF":
                $mc_num = Application_Model_McNumber::fetchFFNumber($num);
                break;
            default:
                $mc_num = null;
                break;                
        }

        $data = array(
                "status" => "INVALID",
                "data"   => "unknown",
            );

        if (is_array($mc_num)) {
            $data['status'] = "VALID";
            $data['data'] = $mc_num;
        }

        $this->_helper->json($data);
    }    
}

