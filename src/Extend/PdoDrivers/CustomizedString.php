<?php
/**
 * SPT software - PDO Connection String
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Define connection string for Mssql
 * 
 */

namespace SPT\Extend\PdoDrivers;

class CustomizedString extends ConnectionString
{
    public function __construct(string $str, array $config)
    {
        $this->host = $str;
        $this->username = isset($config['username']) ? $config['username'] : 'root';
        $this->password = isset($config['password']) ? $config['password'] : '';
    }

    public function toString()
    {
        return $this->host;
    }
}