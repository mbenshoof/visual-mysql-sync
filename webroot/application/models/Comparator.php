<?php

/**
 * Store the base functions to read/write config files, etc.
 *
 * @author Mike Benshoof
 */
class Application_Model_Comparator
{
	/**
	 * List of database sources to compare.
	 *
	 * @var array
	 */
	protected $_dbConfigs;

	/**
	 * The table to compare.
	 *
	 * @var string
	 */
	protected $_table;

	/**
	 * Array of the keys to check on this run.
	 *
	 * @var array
	 */
	protected $_diffKeys;

	/**
	 * The total number of keys.
	 *
	 * @param integer
	 */
	protected $_totalKeys;

	/**
	 * The resultset object.
	 *
	 * @var Application_Model_Comparator_Resultset
	 */
	protected $_resultset;

	/**
	 * Set up the base db configs and the table base name.
	 *
	 * @
	 */
	public function __construct($confs, $table)
	{
		$this->_dbConfigs = $confs;
		$this->_table = $table;
	}

	/**
	 * Fetch the set of primary keys to investigate on each server.
	 *
	 * @param array $opts
	 *
	 * @return Application_Model_Comparator
	 */
	public function fetchKeys($opts = array())
	{
		// Get a database adapter for the diff source
		$diffSeverKey = $this->_dbConfigs['diffSource'];
		$diffSchema = $this->_dbConfigs['diffSchema'];
		$dbConf = $this->_dbConfigs['dbList'][$diffSeverKey];
		$dbConf['params']['dbname'] = $diffSchema;
		$db = Zend_Db::factory($dbConf['adapter'], $dbConf['params']);

		// Set up the Zend_Db_Table based on the prefix and specified table.
		$tblName = $this->_dbConfigs['diffPrefix'] . $this->_table;

		$tableConf = array(
				"db"   => $db,
				"name" => $tblName,
			);

		$tbl = new Zend_Db_Table($tableConf);

		if (array_key_exists('page', $opts) && array_key_exists('num', $opts)) {
			$select = $tbl->select()->limitPage($opts['page'], $opts['num']);
		} else {
			$select = $tbl->select();
		}		

		$keyResults = $db->query($select)->fetchAll();
		$this->_diffKeys = array();

		foreach ($keyResults as $row) {

			if (count($row) == 1) {
				$this->_diffKeys[] = current(array_values($row));
			} else {
				$this->_diffKeys[] = array_values($row);
			}
		}

		// If the count isn't cached, then get the total.
		// Todo:  Add caching layer.
		$select = $tbl->select();
		$select->from($tbl, array('count(1) as total'));
		$countRes = $db->query($select)->fetchAll();
		$this->_totalKeys = $countRes[0]['total'];

		return $this;
	}

	/**
	 * Fetch the primary key list for this comparison set.
	 *
	 * @return array
	 */
	public function getKeys()
	{
		return $this->_diffKeys;
	}

	/**
	 * Fetch the total number of primary keys for paging.
	 *
	 * @return integer
	 */
	public function getNumKeys()
	{
		return (int) $this->_totalKeys;
	}

	/**
	 * Count the number of underlying data sources.
	 *
	 * @return integer
	 */
	public function getNumSources()
	{
		return count($this->_dbConfigs['dbList']);
	}

	/**
	 * Get a list of the server names.
	 *
	 * @return array
	 */
	public function getServerAliases()
	{
		return array_keys($this->_dbConfigs['dbList']);
	}

	/**
	 *
	 *
	 *
	 */
	public function validateTable()
	{
		$conf = current($this->_dbConfigs['dbList']);

		// Set up the Zend_Db_Table with the the given db adapter.
		$db = Zend_Db::factory($conf['adapter'], $conf['params']);

		$tableConf = array(
				"db"   => $db,
				"name" => $this->_table,
			);

		$tbl = new Zend_Db_Table($tableConf);

		try {
			$tbl->info();
		} catch (Exception $e) {
			throw new Zend_Exception("Invalid table.");
		}

		return true;
	}

	/**
	 * Connect to each of the remote db servers and fetch the rowsets for the 
	 * given primary key list.
	 *
	 * @return Application_Model_Comparator
	 */
	public function loadDiffs()
	{
		$dbKeys = array_keys($this->_dbConfigs['dbList']);
		$this->_resultset = new Application_Model_Comparator_Resultset(
				$dbKeys,
				$this->_diffKeys
			);

		foreach ($this->_dbConfigs['dbList'] as $key => $conf) {

			// Set up the Zend_Db_Table with the the given db adapter.
			$db = Zend_Db::factory($conf['adapter'], $conf['params']);

			$tableConf = array(
					"db"   => $db,
					"name" => $this->_table,
				);

			$tbl = new Zend_Db_Table($tableConf);
			$this->_resultset->setTableInfo($tbl->info());

			// Do the raw fetch against the table.
			$rowset = $tbl->find($this->_diffKeys);
			
			foreach ($rowset as $row) {

				$this->_resultset->storeRow($key, $row->toArray());
			}
		}

		return $this;
	}

	/**
	 * Just a wrapper to prepare the resultsets.
	 *
	 * @return array
	 */
	public function prepareResults()
	{
		return $this->_resultset->prepareResults();
	}
}


