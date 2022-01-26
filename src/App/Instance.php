<?php
/**
 * SPT software - Application Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Instance
 * 
 */

namespace SPT\Application;

use SPT\Application\Adapter as ApplicationAdapter;

class Instance
{
    private static $app;

    public static function bootstrap(ApplicationAdapter $app)
    {
        static::$app = $app;
    }

    public static function get(string $key)
    {
        return static::$app->get($key);
    }

    public static function set(string $key, $value)
    {
        return static::$app->set($key, $value);
    }

    public static function factory(string $key)
    {
        return static::$app->factory($key);
    }
}