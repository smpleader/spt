<?php
/**
 * SPT software - PDO wrapper
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: simpler way to work with PDO
 * 
 */

namespace SPT\Extend;

use SPT\Traits\Log as LogTrait;
use SPT\Extend\PdoDrivers\Connector;

class Pdo
{
	use LogTrait;

	protected $connection;
	public $connected = false; 

	function __construct(array $config, array $parameters=array()){


		$driver = isset($config['driver']) ? $config['driver'] : 'mysql';

		/* TODO: those code is redundancy
		if(!count($parameters))
		{
			if(isset($config['options']))
			{
				$parameters = $config['options'];
			}
			else if(isset($config['parameters']))
			{
				$parameters = $config['parameters'];
			}	
		}

		if(!is_array($parameters))
		{
			throw new \Exception('Invalid database parameters');	
		}*/

		$this->connection = Connector::load($driver, $config, $parameters);
		$this->connected = is_object($this->connection);
	}

	function __destruct()
	{
		$this->connected = false;
		$this->connection = null;
	}

	public function error($error, $sql, $input)
	{
		$this->addLog('** Error !!', $error);
		$this->log($sql, $input);
		return false;
	}

	public function log($sql, $input)
	{
		$this->addLog('>> Run SQL:', $sql);
		$this->addLog('>> Inputed value:', $input);
	}

	public function fetch($query, $parameters = array()){
		if($this->connected === true)
		{
			try{
				$query = $this->connection->prepare($query);
				$query->execute($parameters);
				$this->log($query, $parameters);
				return $query->fetch();
			}
			catch(\PDOException $e)
			{
				return $this->error($e->getMessage(), $query, $parameters);
			}
		}
		
		return false;
	}

	public function fetchColumn($query, $parameters = array()){
		if($this->connected === true)
		{
			try{
				$query = $this->connection->prepare($query);
				$query->execute($parameters);
				$this->log($query, $parameters);
				return $query->fetchColumn();
			}
			catch(\PDOException $e)
			{
				return $this->error($e->getMessage(), $query, $parameters);
			}
		}

		return false;
	}

	public function fetchAll($query, $parameters = array()){
		if($this->connected === true)
		{
			try{
				$query = $this->connection->prepare($query);
				$query->execute($parameters);
				$this->log($query, $parameters);
				return $query->fetchAll();
			}
			catch(\PDOException $e)
			{
				return $this->error($e->getMessage(), $query, $parameters);
			}
		}

		return false;
	}

	public function count($query, $parameters = array())
	{
		if($this->connected === true)
		{
			try{
				$query = $this->connection->prepare($query);
				$query->execute($parameters);
				$this->log($query, $parameters);
				return $query->rowCount();
			}
			catch(\PDOException $e)
			{
				return $this->error($e->getMessage(), $query, $parameters);
			}
		}

		return false;
	}

	public function exec($query)
	{
		if($this->connected === true)
		{
			try
			{
				$this->log($query, '--');
				return $this->connection->exec($query);
			}
			catch(\PDOException $e)
			{
				return $this->error($e->getMessage(), $query, '--');
			}
		}
		
		return false;
	}

	public function insert($query, $parameters = array())
	{
		if($this->connected === true)
		{
			try
			{
				$query = $this->connection->prepare($query);
				$query->execute($parameters);
				$this->log($query, $parameters);
				return $this->connection->lastInsertId();
			}
			catch(\PDOException $e)
			{
				return $this->error($e->getMessage(), $query, $parameters);
			}
		}
		
		return false;
	}

	public function update($query, $parameters = array())
	{
		return $this->query($query, $parameters);
	}

	public function delete($query, $parameters = array())
	{
		return $this->query($query, $parameters);
	}

	public function query($query, $parameters = array())
	{
		$result = false;

		if($this->connected === true)
		{
			try
			{
				$query = $this->connection->prepare($query);
				$result = $query->execute($parameters); 
				$this->log($query, $parameters);
			}
			catch(\PDOException $e)
			{
				return $this->error($e->getMessage(), $query, $parameters);
			}
		}
		
		return $result;
	}

	public function tableExists($table){
		if($this->connected === true)
		{
			try{
				$query = $this->count("SHOW TABLES LIKE '$table'");
				$this->log($query, '-');
				return ($query > 0) ? true : false;
			}
			catch(\PDOException $e)
			{
				return $this->error($e->getMessage(), "SHOW TABLES LIKE '$table'", $table);
			}
		}
		
		return false;
	}
}

/*

$db = new db("localhost", "root", "password", "database");

$id = 1;

/*
    Calling a query which will return only ONE row. Usage: (query, array with data)
    Returns an array.
*
$db->fetch("SELECT * FROM `table` WHERE `id` = ?", array($id));

/*
    Calling a query which will return multiple rows. Usage: (query, array with data)
    Returns an array.
*
$db->fetchAll("SELECT * FROM `table` ORDER BY `id` ASC");

/*
    Calling a query which will return the total count of rows. Usage: (query, array with data)
    Returns an integer.
*
$db->count("SELECT `id` FROM `table`");

/*
    Calling a query which will insert a row in to a table. This can also create a table. Usage: (query, array with data)
*
$db->insert("INSERT INTO `table` (`id`) VALUES (?)", array($id));

/*
    Calling a query which will update a row in the table. Usage: (query, array with data)
*
$db->update("UPDATE `table` SET `id` = ? WHERE `id` = ?", array(69, $id));

/*
    Calling a query which will delete a row in the table. Usage: (query, array with data)
*
$db->delete("DELETE FROM `table` WHERE `id` = ?", array($id));

/*
    Calling a query which will determine if a table exists in the database. Usage: (table name)
    Returns true or false.
*
echo ($db->tableExists("table") === true) ? "Table exists." : "Table does NOT exist.";
*/