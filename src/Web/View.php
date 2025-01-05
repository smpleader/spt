<?php
/**
 * SPT software - View
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Class view to display content
 * 
 */

namespace SPT\Web;

use SPT\Web\Theme;
use SPT\Web\ViewLayout;

class View
{
    public function __construct()
    {
        1- override layouts
        2- theme paths
        3- solve paths
        4- call viewmodel on every render
    }

    public function getPath(string $key)
    {
        $tmp = explode(':', $key);
        $count = count($tmp);
        switch($count) 
        {
            case 1:
                $plg = $currentPlugin;
                $type = 'layout';
                $path = $key;
                break;
            case 2:
                $plg = $currentPlugin;
                list($type, $path) = $tmp;
                break;
            case 3:
                list($plg, $type, $path) = $tmp;
                break;
            default:
                throw new \Exception('Invalid path '. $key);
            break;
        }
        
        plugin/id:layout:a.b.c
    }

    public function render()
    {

    }
}