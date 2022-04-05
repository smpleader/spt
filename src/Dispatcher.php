<?php
/**
 * SPT software - Dispatcher
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Dispatcher
 * 
 */

namespace SPT;

final class Dispatcher
{
    static private $dispatches;
    static private $log;
    static private $current;

    public static function register($event, $object, $func)
    {
        if(!isset(static::$dispatches[$event])) static::$dispatches[$event] = [];
        static::$dispatches[$event][] = [ $object, $func ];
    }

    public static function fire()
    {
        $params = func_get_args();
        $name = array_shift($params);
        if(isset( static::$dispatches[$name]) || static::$current != $name )
        {
            static::$current = $name;
            foreach(static::$dispatches[$name] as $register)
            {
                list($object, $nameFnc) = $register;
                $try = \SPT\App\Instance::factory($object);

                if( $try && method_exists( $try, $nameFnc))
                {
                    $result = call_user_func_array([$try, $nameFnc],  $params);
                    if( true !== $result )
                    {
                        static::$log = $result;
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