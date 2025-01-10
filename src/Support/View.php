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

class View
{
    public static function render(string|array $key, array $data = [], $isString = true)
    {
        $container = App::getInstance()->getContainer();
        if($container->exists('view'))
        {
            $view = $container->get('view');
            return $view->render( $key, $data, $isString);
        }

        return ''; // TODO: show warnign if DEBUG ON
    }
}