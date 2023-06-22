<?php
/**
 * SPT software - Controller
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core controller
 * 
 */

namespace SPT\Web;

use SPT\Application\IApp;
use SPT\Container\Client;   

class ControllerMVVM extends Controller
{
    protected $supportMVVM = true;

    public function toHtml()
    {
        $this->registerViewModels();
        return parent::toHtml();
    }

    public function toAjax()
    {
        $this->registerViewModels();
        return parent::toAjax();
    }

    public function registerViewModels()
    {
        $plgName = $this->app->get('currentPlugin', '');
        $vmFolder = SPT_PLUGIN_PATH. '/plugins/'. $plgName. '/viewmodels';
                
        if( is_dir( $vmFolder))
        {
            foreach(new \DirectoryIterator($vmFolder) as $file) 
            {
                if (!$file->isDot() && $file->isFile()) 
                {
                    $filename = $file->getBasename(); 
                    $vmName = substr($filename, 0, strlen($filename) - 4) ;
                    $vmName = ucfirst($vmName);
                    $vmName = $this->app->getNamespace(). '\\plugins\\'. $plgName. '\\viewmodels\\'. $vmName;

                    if(class_exists($vmName))
                    {
                        ViewModelHelper::prepareVM(
                            $vmName, 
                            $vmName::register(), 
                            $this->getContainer()
                        );
                    }
                }
            }
        }
        /* STOP to load VM from all plugins
        foreach(new \DirectoryIterator(SPT_PLUGIN_PATH) as $plg) 
        {
            if (!$plg->isDot() && $plg->isDir()) 
            { 
                $plgName = $plg->getBasename();
            }
        }*/
    }
}