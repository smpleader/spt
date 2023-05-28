<?php
/**
 * SPT software - Bootstrap register
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Register at the start of application. Example: Plugin can add a service to container here
 * @version: 0.8
 * 
 */

namespace SPT\Application\Plugin\Register;
use SPT\Application\IApp;

Class Bootstrap
{
    public static function initialize( IApp $app)
    {
        // do something to register with system
    }
}