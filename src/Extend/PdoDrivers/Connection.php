<?php
/**
 * SPT software - PDO Connection String
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Abstract to define connection string
 * 
 */

namespace SPT\Extend\PdoDrivers;
use SPT\Traits\Log as LogTrait;

class Connection
{
	use LogTrait;

    protected string $host;
    protected string $port;
    protected string $username;
    protected string $password;
    protected string $database;
    protected \PDO $pdo;

    public function toString()
    {
        return '';
    }

    public function setup(array $attributes = [])
    {
        // setup some attribute of this connection
    }

    public function connect(array $attributes = [])
    {
        try
        { 
            $this->pdo = new \PDO($this->toString(), $this->username, $this->password);
            $this->setup($attributes);
        }
		catch(\PDOException $e)
		{
            $this->addLog('** Error Connection '. __CLASS__ , $e->getMessage() );
            return false;
		}

        return $this->pdo;
    }
}