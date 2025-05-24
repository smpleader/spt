<?php
/**
 * SPT software - Support/Plugin
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: comfortable way to use Plugin
 * 
 */

namespace SPT\Support;

use SPT\Support\App;
use SPT\Support\Loader;

class Plugin
{
    public static function path(string $name, string $path)
    {
        return App::getInstance()->plugin($name)->getPath($path);
    }

    public static function id(string $idOrAlias)
    {
        return App::getInstance()->plugin($idOrAlias)->getId();
    }
}