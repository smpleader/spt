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
        $this->host = isset($config['host']) ? $config['host'] : 'localhost';
        $this->username = isset($config['username']) ? $config['username'] : 'root';
        $this->password = isset($config['password']) ? $config['password'] : '';
        $this->database = isset($config['database']) ? $config['database'] : '';
        $this->port = isset($config['port']) ? $config['port'] : '5432';

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