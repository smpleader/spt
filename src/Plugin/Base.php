<?php
/**
 * SPT software - A Plugin
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a basic Plugin
 * 
 */

namespace SPT\Plugin;

use SPT\App\Adapter as Application;

class Base implements PluginAbstract
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register()
    {
        return [];
    }

    public function getInfo()
    {
        return [
            'title' => 'Plugin Base',
            'name' => 'plgBase',
            'version' => '0.0.1',
            'schema_version' => '0.0.1'
        ];
    }

    public function getSettings()
    {
        return [];
    }

    // this should used for install/ uninstall function, not to register in running app
    public function registerAssets()
    {
        return [];
    }

    // hook in manage
    public function install(){}
    public function upgrade(){}
    public function uninstall(){}
}