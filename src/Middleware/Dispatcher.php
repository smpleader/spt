<?php
/**
 * SPT software - Dispatcher for Middleware
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Dispatcher for Middleware
 * 
 */

namespace SPT\Middleware;

final class Dispatcher
{
    static private $dispatchers; 
    static private $log;
    static private $current;

    public static function register(string $type, Loader $middlewareLoader)
    {
        if(!isset(static::$dispatchers[$type])) static::$dispatchers[$type] = [];
        static::$dispatchers[$type][] = $middlewareLoader;
    }

    /**
     * @parameters:
     *      type: required | string
     *      loader: required | string
     *      param1 , param2 , param3 ..: optional
     * 
     * @return: 
     *      null => nothing to do
     *      true => processed successfully
     *      false => something goes wrong
     */
    public static function fire()
    {
        $params = func_get_args();
        if( count($params) < 2 ) return null;

        $type = array_shift($params);
        $loader = array_shift($params);
        if(is_array( static::$dispatchers[$type] ) && static::$current != $type )
        {
            static::$current = $type;
            foreach(static::$dispatchers[$type] as $mw)
            {
                $mw->prepare($loader);
                if( $mw->ready() )
                {
                    $result = $mw->execute($params);
                    if(false === $result)
                    {
                        static::$log = $result;
                        static::$current = null;
                        return false;
                    }
                }
            }
            static::$current = null;
        }

        return true;
    }

    public static function log()
    {
        return static::$log;
    } 
}