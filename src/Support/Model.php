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
use SPT\Web\Model as WebModel;

class Model
{
    public static function containerize(string $classname, string $fullname, ?string $alias)
    {
        if($fullname instanceof WebModel)
        {
            $container = App::getInstance()->getContainer();
            $container->containerize(
                $classname. 'Model', 
                $fullname,
                new $fullname($container),
                $alias
            );
        }
    }
}