<?php

/**
 *
 *	By Jelle Ursem
 *	
 *	Ultra-simple database abstraction class.
 *	Reads settings from a properties file and takes care of executing queries.
 *	You can easily extend this class to add new database types.	
 *
 *	@package pork
 */

/**
 * dbConnection class
 * Handles database connections and querying/inserting/removal of rows.
 * @package pork
 */
class dbConnection
{
	var $num_rows, $affected_rows, $connection, $result, $lastquery, $error, $output, $username, $password, $database, $queries, $func, $host;

		
	/**
	 * Constructor.
	 * Reads settings from the default settings file and creates the connection.
	 * @param String $useAlternative Alternative settings file
	 */
	function __construct($useAlternative = false)
	{
		$this->readSettings( (!$useAlternative) ? dirname(__FILE__).'/../settings/dbsettings.php' : $useAlternative);
		$this->connect();
	}
	
	/*
     * Read a file line-by-line to fetch the settings.
	 * @param String $file file to read settings from. 
	 */
	function readSettings($file)
	{
		$input = file_get_contents($file);
		$file = explode("\n", $input );
		for ($i=2; $i<sizeof($file) -1; $i++)
		{
			$property = explode ("=", $file[$i]);
			$prop = trim($property[0]);
			$this->$prop = trim($property[1]);
		}
	}

	/* 
	 * Singleton functionality.
	 * Creates a static instance.
	 */
	public static function getInstance()
    {
		static $instance;
		if (!isset($instance)) 
		{
		     $c = __CLASS__;
		     $instance = new $c;
        }
        return $instance;
    }

	/**
	 * Creates the actual connection
	 */
	function connect()
	{
		switch ($this->dbtype)
		{
			case 'mysql':
				$this->connection = mysql_connect($this->host, $this->username, $this->password);
				if ($this->connection)
				{
					mysql_select_db($this->database, $this->connection);
					return true;
				}
			break;
		}
		return false;
	}


	
	/**
	 * Find out the number of rows returned
	 */
	function numrows()
	{
		switch ($this->dbtype)
		{
			case 'mysql':
				return @mysql_num_rows($this->result);
			break;
		}		
	}

		
	/**
	 * Execute the passed query on the database and determine if insert_id or affected_rows or numrows has to be called.
	 * @param String $query Query to be executed.
	 * @returns mixed ID if inserted row, false on error
	 */
	function query($query)
	{
		
		$this->queries[]= $query;
		$this->insertID = 0;
		$this->lastQuery = $query;
		if ($this->dbtype == 'mysql') { mysql_query("use {$this->database}", $this->connection); }
		$this->result = mysql_query($this->lastQuery, $this->connection);
		$this->error = mysql_error($this->connection);
		$query = strtolower($this->lastQuery);
		if (empty($this->error))
		{
			if (strpos($query, 'insert') !== false)
			{
				$this->insertID = mysql_insert_id($this->connection);
				$this->num_rows = 0;
			}
			elseif (strpos($query, 'delete') !== false || strpos($query, 'replace') !== false || strpos($query, 'update') !== false)
			{
				$this->affected_rows = mysql_affected_rows($this->connection); 
				$this->num_rows = 0;
			}
			else
			{
				$this->num_rows = $this->numrows();
				$this->affected_rows = 0;
			}
			$this->queryCount++;
			if (!empty($this->insertID)) { return ($this->insertID); }
		}
		else
		{
		 die($this->error."\nWhile executing query: \n{$query}");
		 return false;
		}
		return true;
		
	}

	/**
	 * Execute the query and return result # 0.
	 * If no query is passed it will use the previous result.
	 * @param $query optional query to execute. 
	 * @returns String $output
	 */
	function fetchOne($query='')
	{
		if (!empty($query)) $okay = $this->query($query);
		if($okay !== false)
		{
			$this->output = ($this->num_rows > 0) ? mysql_result($this->result, 0) : '';
		}
		return($this->output);
	}

	/**
	 * Execute the passed query and fetch a multi-dimensional array of results using $func
	 * If no query is passed it will use the previous result.
	 * @param $query optional query to execute. 
	 * @param $func function to use. Can use mysql_fetch_array or mysql_fetch_object or mysql_fetch_assoc at will.
	 * @returns Array|Object $output multi dimensional array of output.
	 */
	function fetchAll($query='', $func=false)
	{
		$output = array();
		if (!empty($query)) $okay = $this->query($query);
		if ($okay !== false)
		{
			$func = ($func != false) ? $func : $this->func;
			while ($row = $func($this->result))
			{
				$output[] = $row;
			}
			$this->output = $output;
		}
		return($output);
	}

	/** 
	 *	Execute the passed query and fetch only one row of results using $func
	 * If no query is passed it will use the previous result.
	 * @param $query optional query to execute. 
	 * @param $func function to use. Can use mysql_fetch_array or mysql_fetch_object or mysql_fetch_assoc at will.
	 * @returns Array|Object $output multi dimensional array of output.
	 */
	function fetchRow($query='', $func=false)
	{
		$output = array();
		if (!empty($query)) $okay = $this->query($query);
		if ($okay !== false)
		{
			$func = ($func != false) ? $func : $this->func;
			if ($row = $func($this->result))
			{
				$output = $row;
			}
			$this->output = $output;

		}
		return($output);	
	}

	/**
	 * @param String $val Password to use
	 */
	function setPassword($val)
	{
		$this->password = $val;
	}

	/**
	 * @param String $val Username to use
	 */
	function setUsername($val)
	{
		$this->username = $val;
	}


	/**
	 * @param String $val Database host to use
	 */
	function setHost($val)
	{
		$this->host = $val;
	}
	
	/**
	 * @param String $val Database to set
	 */
	function setDatabase($val)
	{
		$this->database = $val;
		if ($this->connection)
		{
			mysql_select_db($val, $this->connection);
		}
	}

	/**
	 * Only used for extending with different database types
	 * @param String $val Database type to set
	 */
	function setDbType($type)
	{
		if ($type == 'mysql')
		{
			$this->dbtype = $type;
		}
	}

	function getError()
	{
		return($this->error);
	}

}


class Transaction 
{
	  static function begin()
	  {
		mysql_query("BEGIN", dbConnection::getInstance()->connection);
	  }

	  static function rollback() 
	  {
		 mysql_query("ROLLBACK", dbConnection::getInstance()->connection);
	  }

	  static function commit() 
	  {
		mysql_query("COMMIT", dbConnection::getInstance()->connection);
	  }
} 



?>