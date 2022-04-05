<?php
/**
 * SPT software - Controller
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a basic controller
 * 
 */

namespace SPT\Plugin;

abstract class PluginAbstract
{
    // declare in service provider
    abstract public function register();
    abstract public function getInfo();
    abstract public function getSettings();
    abstract public function registerAssets();

    // hook in manage
    abstract public function install();
    abstract public function upgrade();
    abstract public function uninstall();
}