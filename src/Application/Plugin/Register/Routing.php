<?php
/**
 * SPT software - Routing register
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Register endpoints and events relate to routing
 * @version: 0.8
 * 
 */

namespace SPT\Application\Plugin\Register;
use SPT\Application\IApp;

Class Routing
{
    public static function registerEndpoints()
    {
        return [];
    }

    // make something if it's home or index.php
    public static function isHome(IApp $app)
    {
        // ..
    }
}