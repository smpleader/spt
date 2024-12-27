<?php
/**
 * SPT software - Support/Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: comfortable way to use Model
 * 
 */

namespace SPT\Support;

use SPT\Support\App;
use SPT\Support\Loader;

class Model
{
    public static function containerize(string $classname, string $fullname, ?string $alias)
    {
        $container = App::getInstance()->getContainer();
        $container->containerize(
            $classname. 'Model', 
            $fullname,
            new $fullname($container), 
            $alias
        );
    }

    public static function loadFolder(string $path, string $namespace)
    {
        var_dump($path, 
        $namespace);
        $container = App::getInstance()->getContainer();
        Loader::findClass( 
            $path, 
            $namespace, 
            function($classname, $fullname) { \SPT\Support\Model::containerize($classname, $fullname, '');}
        );
    }
}