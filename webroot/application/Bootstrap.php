<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Include some 3rd party packages.
	 *
	 * @return void
	 */
	protected function _initPackages()
	{
		// Ensure library/ is on include_path.
		set_include_path(implode(PATH_SEPARATOR, array(
			realpath(APPLICATION_PATH . '/../library/packages'),
			get_include_path(),
		)));
	}

	/**
	 * Store the config in the registy.
	 *
	 * @return void
	 */
	protected function _initRegistryVals()
	{
		// Add the loaded configuration file to the registry.
		$config = new Zend_Config($this->getOptions(), true);
	    Zend_Registry::set('config', $config);

	    // Add the session to the registry.
	    $namespace = new Zend_Session_Namespace();
	    Zend_Registry::set('session', $namespace);
	}

	/**
	 * Initialize the custom router and add default routes (for view work in bootstrap).
	 *
	 * @return void
	 */
	public function _initRouter()
	{	
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->throwExceptions(false);
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini');
		$router = $frontController->getRouter();
		$router->addConfig($config,'routes');
		$router->addDefaultRoutes();		
	}
		
	/**
	 * Set up the global view object.
	 *
	 * @return void
	 */
	protected function _initGlobalView()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->addScriptPath(APPLICATION_PATH . '/views/scripts');
		$view->doctype('XHTML1_STRICT');

		$view->headLink()->prependStylesheet('/css/layout.css')
			->prependStylesheet('/css/reset-fonts-grids.css');	
		$view->headScript()->prependFile('/js/jquery-1.7.1.min.js');

		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial(
		    'modules/paginator.phtml'
		);
	}

	/**
	 * Initialize any placeholders (navigation, etc)
	 *
	 * @return void
	 */
	protected function _initPlaceholders()
	{
		// Grab the main view.
		$view = $this->getResource('view');

		// Set up the placeholders.
		$view->placeholder("configSelector");

		// Check to see if there is a cartID registered.
		if (Zend_Registry::get('session')->isConfigLoaded) {
			$view->hasConfig = true;
			$view->loadedConfig = str_replace(
						"-",
						"",
						Zend_Registry::get('session')->loadedConfig
					);
		} else {
			$view->hasConfig = false;
		}


		$view->render('modules/configSelector.phtml');
	}
}

function dar($data, $label = null)
{
	if (!is_null($label)) {
		echo "<b>$label :</b>";
	}
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

