<?php
/**
 * SPT software - Application Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Instance
 * 
 */

namespace SPT\App;

use SPT\App\Adapter as ApplicationAdapter;

class Instance
{
    private static $app;
    private static $path;

    public static function bootstrap(ApplicationAdapter $app, array $path)
    {
        static::$app = $app;
        static::$path = $path;
    }

    public static function factory(string $key)
    {
        if( static::$app->has($key) )
        {
            return static::$app->{$key};
        }

        return null;
    }

    public static function has(string $key)
    {
        return static::$app->has($key);
    }
    
    public static function main()
    {
        return static::$app;
    }

    public static function path($name)
    {
        return isset(static::$path[$name]) ? static::$path[$name] : '';
    }
}