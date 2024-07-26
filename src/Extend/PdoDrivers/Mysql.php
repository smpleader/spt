<?php
/**
 * SPT software - PDO Connection String
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Define connection string Mysql
 *               https://www.php.net/manual/en/ref.pdo-mysql.php
 *               https://www.php.net/manual/en/ref.pdo-mysql.connection.php
 * 
 */

namespace SPT\Extend\PdoDrivers;

class Mysql extends Connection
{
    protected string $charset;

    public function __construct(array $config)
    {
        $arr = [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => '',
            'port' => '3306',
            'charset' => 'utf8mb4'
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
        return 'mysql:host='. $this->host. ';'.
                'port='. $this->port.';'.
                'dbname='. $this->database. ';'.
                'charset='. $this->charset;
    }

    public function setup(array $attributes = [])
    {
        // setup some attribute of this connection
        if( isset($attributes['fetch_mode']))
        {
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $parameters['fetch_mode']);
        }
        else
        {
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        }

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }
}