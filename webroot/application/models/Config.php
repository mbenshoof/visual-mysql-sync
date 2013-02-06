<?php

/**
 * Store the base functions to read/write config files, etc.
 *
 * @author Mike Benshoof
 */
class Application_Model_Config 
{
	/**
	 * Main path for saved config INI files.
	 *
	 * @var string
	 */
	protected $_configPath;

	/**
	 * The list of the configs available in the directory.
	 *
	 * @var array
	 */
	protected $_availableConfigs = array();

	/**
	 * Set up the config path and try to fetch all the config files.  Also, 
	 * try to verify which is currently loaded.
	 *
	 */
	public function __construct()
	{
		$this->_configPath = Zend_Registry::get('config')->configPath;
		$this->getAllConfigs();
	}

	/**
	 * Fetch the current config path.
	 *
	 * @return string
	 */
	public function getConfigPath()
	{
		return $this->_configPath;
	}

	/**
	 * Loop through the config directory and try to load all the options.
	 *
	 * @return void
	 */
	public function getAllConfigs()
	{
		$files = new DirectoryIterator($this->_configPath);

		// Iterate through all the files in the directory.
		foreach ($files as $file) {

			if ($file->isFile()) {
				
				// Parse the filename and type to verify we are using an INI file.
				$fileName = $file->getFilename();
				$parts = explode(".", $fileName);
				$ext = array_pop($parts);
				
				if ($ext == "ini") {
					
					// Set up the config array object.
					$conf = array (
							"sessionKey"  => $parts[0],
							"displayName" => str_replace("-", " ", $parts[0]),
							"fullPath"    => $file->getPath(),
							"fullName"    => $file->getPath() . "/" . $fileName,
						);

					$this->_availableConfigs[$parts[0]] = $conf;
				}				
			}
		}
	}

	/**
	 * Get the list of all the available configs.
	 *
	 * @return array
	 */
	public function getAvailableConfigs()
	{
		return $this->_availableConfigs;
	}

	/**
	 * Check to see if a configuration is valid.
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function configExists($key)
	{
		return array_key_exists($key, $this->_availableConfigs);
	}

	/**
	 * Get the database details for the selected configuration.
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	public function getDetails($key)
	{
		if (!array_key_exists($key, $this->_availableConfigs)) {
			throw new Exception("Invalid configuration selected.");
		}

		$iniFile = $this->_availableConfigs[$key]['fullName'];
		$config = new Zend_Config_Ini($iniFile);

		$configList = array();

		foreach ($config->dbs->toArray() as $key=>$dbConf) {
			$configList[$key] = $dbConf;
		}

		// Sort by the array keys.
		ksort($configList);

		$result = array(
				"dbList"         => $configList,
				"diffSource"     => $config->diffSource,
				"checksumSource" => $config->checksumSource,
				"diffSchema"     => $config->diffSchema,
				"diffPrefix"     => $config->diffPrefix,
			);

		return $result;
	}

	/**
	 * Use the diff source schema and prefix to generate a list of tables 
	 * that have been compared.
	 *
	 * @param string  $key           The config to list tables
	 * @param boolean $includeCounts Include the counts of rows?
	 *
	 * @return array
	 */
	public function listTables($key, $includeCounts = false)
	{
		// Get the connection details for a particular config.
		$details = $this->getDetails($key);

		// Get a database adapter for the diff source
		$diffSeverKey = $details['diffSource'];
		$diffSchema = $details['diffSchema'];
		$dbConf = $details['dbList'][$diffSeverKey];
		$dbConf['params']['dbname'] = $diffSchema;
		$db = Zend_Db::factory($dbConf['adapter'], $dbConf['params']);

		$sql = "SHOW TABLES LIKE '" . $details['diffPrefix'] . "%'";
		$results = $db->query($sql)->fetchAll();
		$tableList = array();

		// Loop through all the tables and format them.
		foreach ($results as $row) {
			$tableData = array(
					"name" => str_replace($details['diffPrefix'], "", current($row))
				);

			if ($includeCounts) {

				// Todo:  Add caching layer.
				$perTableSql = "SELECT count(1) as total FROM " . current($row);
				$perTableResult = $db->query($perTableSql)->fetchAll();
				$tableData['count'] = $perTableResult[0]['total'];
			}

			$tableList[] = $tableData;
		}

		return $tableList;
	}
}


