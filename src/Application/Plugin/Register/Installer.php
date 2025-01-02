<?php
/**
 * SPT software - Installer register
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Supply plugin info, and an event when install, uninstall, active, deactive
 * @version: 0.8
 * 
 */

namespace SPT\Application\Plugin\Register;
use SPT\Application\IApp;

Class Installer
{
    public static function info()
    {   
        return [];
    }

    public static function install( IApp $app)
    {
        // run sth to prepare the install
    }
    public static function uninstall( IApp $app)
    {
        // run sth to uninstall
    }
    public static function active( IApp $app)
    {
        // run sth to prepare the install
    }
    public static function deactive( IApp $app)
    {
        // run sth to uninstall
    } 
}