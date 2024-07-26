<?php
/**
 * SPT software - PDO Connection String
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Define connection string Sqlite
 *               https://www.php.net/manual/en/ref.pdo-pgsql.php
 *               https://www.php.net/manual/en/ref.pdo-pgsql.connection.php
 * 
 */

namespace SPT\Extend\PdoDrivers;

class Sqlite extends Connection
{
    public function __construct(array $config)
    {
        $arr = [
            'host' => 'localhost',
            //'username' => 'root',
            //'password' => '',
            'database' => ''
        ];

        foreach($arr as $key => $default)
        {
            $this->{$key} = isset($config[$key]) ? $config[$key] : $default;
        }

        if(empty($this->database))
        {
            if(empty($this->host))
            {
                throw new \Exception('Invalid database information');
            }
            $this->database = $this->host;
        }
    }

    public function toString()
    {
        return 'sqlite:'. $this->database;
    }
}