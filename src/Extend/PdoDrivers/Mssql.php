<?php
/**
 * SPT software - PDO Connection String
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Define connection string for Mssql
 *               https://www.php.net/manual/en/ref.pdo-sqlsrv.php
 *               https://www.php.net/manual/en/ref.pdo-sqlsrv.connection.php
 * 
 */

namespace SPT\Extend\PdoDrivers;

class Mssql extends Connection
{
    public function __construct(array $config)
    {
        $this->host = isset($config['host']) ? $config['host'] : 'localhost';
        $this->username = isset($config['username']) ? $config['username'] : 'sa';
        $this->password = isset($config['password']) ? $config['password'] : '';
        $this->database = isset($config['database']) ? $config['database'] : '';
        $this->port = isset($config['port']) ? $config['port'] : '1433';

        if(empty($this->database) || empty($this->host))
        {
            throw new \Exception('Invalid database information');
        }
    }

    public function toString()
    {
        return 'sqlsrv:Server='. $this->host. ','. $this->port. ';'.
                'Database='. $this->database;
    }
}