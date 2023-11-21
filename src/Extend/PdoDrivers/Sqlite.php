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
        $this->host = isset($config['host']) ? $config['host'] : '';
        $this->database = isset($config['database']) ? $config['database'] : '';
        if(empty($this->database))
        {
            if(empty($this->host))
            {
                throw new \Exception('Invalid database information');
            }
            $this->database = $this->host;
        }

        $this->username = isset($config['username']) ? $config['username'] : 'root';
        $this->password = isset($config['password']) ? $config['password'] : '';
        $this->port = '';
    }

    public function toString()
    {
        return 'sqlite:'. $this->database;
    }
}