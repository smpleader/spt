<?php
/**
 * SPT software - PDO Driver Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Instance to fast pick a driver
 * 
 */

namespace SPT\Extend\PdoDrivers;

class Connector
{
    public static $connections = [];
    public static function load(string $type, array $config, array $attributes = [])
    {
        $id = md5(json_encode($config));
        if(isset(static::$connections[$id]))
        {
            return static::$connections[$id];
        }

        $type = strtolower($type);
        switch($type)
        {
            default:
                $drv = new CustomizedString($type, $config); break;
            case 'mysql':
                $drv = new Mysql($config); break;
            case 'postgresql':
                $drv = new Postgresql($config); break;
            case 'mssql':
                $drv = new Mssql($config); break;
            case 'sqlite':
                $drv = new Sqlite($config); break;
        }

        static::$connections[$id] = $drv->connect($attributes);

        return static::$connections[$id];
    }
}