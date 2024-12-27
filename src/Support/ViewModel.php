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

class ViewModel
{
    public static function containerize(string $classname, string $fullname, ?string $alias)
    {
        $container = App::getInstance()->getContainer();
        $container->containerize(
            $classname. 'VM', 
            $fullname,
            function($fullname, $container) { return new $fullname($container);}, 
            $alias
        );
    }

    public static function loadFolder(string $path, string $namespace)
    {
        $container = App::getInstance()->getContainer();
        Loader::findClass( 
            $path, 
            $namespace, 
            function($classname, $fullname) { \SPT\Support\ViewModel::containerize($classname, $fullname, '');}
        );
    }
}