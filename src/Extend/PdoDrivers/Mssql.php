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
        $arr = [
            'host' => 'localhost',
            'username' => 'sa',
            'password' => '',
            'database' => '',
            'port' => '1433',
        ];

        foreach($arr as $key => $default)
        {
            $this->{$key} = isset($config[$key]) ? $config[$key] : $default;
        }

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