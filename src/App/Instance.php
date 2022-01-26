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

    public static function bootstrap(ApplicationAdapter $app)
    {
        static::$app = $app;
    }

    public static function factory(string $key)
    {
        return static::$app->factory($key);
    }
    
    public static function main()
    {
        return static::$app;
    }
}