<?php
/**
 * SPT software - PDO Connection String
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Define connection string PostgreSql
 *               https://www.php.net/manual/en/ref.pdo-pgsql.php
 *               https://www.php.net/manual/en/ref.pdo-pgsql.connection.php
 * 
 */

namespace SPT\Extend\PdoDrivers;

class Postgresql extends Connection
{
    public function __construct(array $config)
    {
        $arr = [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => '',
            'port' => '5432',
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
        return 'pgsql:host='. $this->host. ';'.
                'port='. $this->port.';' .
                'dbname='. $this->database. ';';
                // add username password here ??
    }
}