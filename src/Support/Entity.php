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

class Entity
{
    public static function containerize(string $classname, string $fullname, ?string $alias)
    {
        $container = App::getInstance()->getContainer();
        $container->containerize(
            $classname. 'Entity', 
            $fullname,
            new $fullname($container->get('query')), 
            $alias
        );
    }

    public static function loadFolder(string $path, string $namespace)
    {
        $container = App::getInstance()->getContainer();
        Loader::findClass( 
            $path, 
            $namespace, 
            function($classname, $fullname) { \SPT\Support\Entity::containerize($classname, $fullname, '');}
        );
    }
}