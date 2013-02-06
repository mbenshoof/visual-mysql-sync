<?php

/**
 * Store the raw diff rows in an easily accessible object.
 *
 * @author Mike Benshoof
 */
class Application_Model_Comparator_Resultset
{
	/**
	 * List of database keys.
	 *
	 * @var array
	 */
	protected $_dbKeys;

	/**
	 * The table to compare.
	 *
	 * @var string
	 */
	protected $_numSources;

	/**
	 * Array of the keys to check on this run.
	 *
	 * @var array
	 */
	protected $_diffKeys;

	/**
	 * The table info.
	 *
	 * @var array
	 */
	protected $_tableInfo;

	/**
	 * The primary key info.
	 *
	 * @var array
	 */
	protected $_pkInfo;

	/**
	 * The field name for the primary key.
	 *
	 * @var string
	 */
	protected $_pkField;

	/**
	 * Is this a multi-field primary key?
	 *
	 * @var boolean
	 */
	protected $_multiKey;

	/**
	 * The raw resultsets for each primary key.
	 *
	 * @var array
	 */
	protected $_resultsets;

	/**
	 * The prepared difference set.
	 *
	 * @var array
	 */
	protected $_prepared;

	/**
	 * Set up the base db configs and the table base name.
	 *
	 * @param array $dbKeys
	 * @param array $diffKeys
	 *
	 * @return void
	 */
	public function __construct($dbKeys, $diffKeys)
	{
		$this->_dbKeys = $dbKeys;
		$this->_diffKeys = $diffKeys;
		$this->_numSources = count($dbKeys);

		// Initialize some other variables.
		$this->_tableInfo = null;
		$this->_pkInfo = null;
		$this->_multiKey = null;
		$this->_resultsets = array();
	}

	/**
	 * Store the table info and parse the primary key data.
	 *
	 * @param array $info
	 *
	 * @return Application_Model_Comparator_Resultset
	 */
	public function setTableInfo($info)
	{
		if (!is_null($this->_tableInfo)) {
			return $this;
		}

		$this->_tableInfo = $info;
		$this->_pkInfo = $info['primary'];
		$numKeys = count($this->_pkInfo);

		switch ($numKeys) {

			case 0:
				throw new Exception("Row has no primary key for comparison");
				break;

			case 1:
				$this->_pkField = $this->_pkInfo[1];
				$this->_multiKey = false;
				break;

			case 2:
				$this->_multiKey = true;
				break;
		}

		return $this;
	}

	/**
	 * Store this row for a particular server.
	 *
	 * @param string $serverKey The key for the server
	 * @param array  $row       The raw db row as an assoc
	 *
	 * @return Application_Model_Comparator_Resultset
	 */
	public function storeRow($serverKey, $row)
	{
		if ($this->_multiKey) {
			return $this->_storeMultiKeyRow($serverKey, $row);
		} else {
			return $this->_storeSingleKeyRow($serverKey, $row);
		}
	}

	/**
	 * Store a row for a server given a single field primary key.
	 *
	 * @param string $serverKey The key for the server
	 * @param array  $row       The raw db row as an assoc
	 *
	 * @return Application_Model_Comparator_Resultset
	 */
	protected function _storeSingleKeyRow($serverKey, $row)
	{
		$rowPk = $row[$this->_pkField];

		// Initialize the result set if the key isn't already loaded.
		if (!array_key_exists($rowPk, $this->_resultsets)) {
			$this->_resultsets[$rowPk] = array();
		}

		$this->_resultsets[$rowPk][$serverKey] = $row;
		return $this;
	}

	/**
	 * Store a row for a server given a multi-field primary key.
	 *
	 * @param string $serverKey The key for the server
	 * @param array  $row       The raw db row as an assoc
	 *
	 * @return Application_Model_Comparator_Resultset
	 */
	protected function _storeMultiKeyRow($serverKey, $row)
	{
		// TODO
		return $this;
	}

	/**
	 * Find the fields that are out of sync for each row and store the raw data.
	 *
	 * @return array
	 */
	public function prepareResults()
	{
		$this->_prepared = array();

    	foreach ($this->_resultsets as $pk => $sources) {

    		// Find diffs or mark all as missing.
    		if (count($sources) < $this->_numSources) {
    			$this->_fillGap($sources);
    			$diffs = array_keys(current($sources));
    		} else {
    			$diffs = $this->_findDiffs($sources);
    		}

    		// Make sure that the source array is sorted by keys.
    		ksort($sources);    		
    		$this->_prepared[$pk] = array(
    				"diffs" => $diffs,
    				"raw"   => $sources,
    			);
    	}

    	// Keep the final array sorted in PK order.
    	ksort($this->_prepared);
    	return $this->_prepared;
	}

	/**
	 * Put an empty entry in for this row if not found on a server.
	 *
	 * @param array $sources
	 *
	 * @return void
	 */
	protected function _fillGap(&$sources)
	{
		foreach ($this->_dbKeys as $serverKey) {
			if (!array_key_exists($serverKey, $sources)) {
				$sources[$serverKey] = array();
			}			
		}
	}

	/**
	 * Manually check for rows not the same in all sources.
	 *
	 * @param array $sources
	 *
	 * @return array
	 */
	protected function _findDiffs($sources)
	{
		$base = array_shift($sources);
		$keyList = array_keys($base);
		$diffs = array();

		foreach ($keyList as $key) {

			foreach ($sources as $source) {

				if ($source[$key] !== $base[$key]) {
					$diffs[] = $key;
					break;
				}
			}
		}

		return $diffs;
	}
}


