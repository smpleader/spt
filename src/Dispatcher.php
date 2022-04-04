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

    public static function register($event, $object, $func)
    {
        if(!isset(static::$dispatches[$event])) static::$dispatches[$event] = [];
        static::$dispatches[$event][] = [ $object, $func ];
    }

    public static function fire()
    {
        $params = func_get_args();
        $name = array_shift($params);
        if(isset( static::$dispatches[$name]))
        {
            foreach(static::$dispatches[$name] as $register)
            {
                list($object, $nameFnc) = $register;
                $try = \SPT\App\Instance::main()->factory($object);

                if( $try && method_exists( $try, $nameFnc))
                {
                    if( true !== call_user_func_array([$try, $nameFnc],  $params) )
                    {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}