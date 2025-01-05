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
            function($fullname, $container) { 
                $vm = new $fullname($container);
                // collect registers 
                TODO: load VM and prepare collections
                App::getInstance()->addVM($classname. 'VM')
                return $vm;
            }, 
            $alias
        );
    }

    public static function getData(string $path)
    {
        TODO: get data for layout
    }
}