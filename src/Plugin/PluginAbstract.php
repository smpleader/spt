<?php
/**
 * SPT software - Controller
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a basic controller
 * 
 */

namespace App\libraries\Core;

abstract class PluginAbstract
{
    // declare in service provider
    abstract public function register();
    abstract public function info();
    abstract public function config();
    abstract public function assets();

    // hook in manage
    abstract public function install();
    abstract public function upgrade();
    abstract public function uninstall();
}