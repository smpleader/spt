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
        $this->getThemePath();
        $plgName = $this->app->get('currentPlugin', '');
        $this->loadVMFolder(
            SPT_PLUGIN_PATH. '/'. $plgName. '/viewmodels',
            $this->app->getNamespace(). '\\plugins\\'. $plgName
        );
    }

    protected function getThemePath()
    {
        if(!defined('SPT_THEME_PATH'))
        {
            $themePath = $this->app->get('themePath', '');
            $theme = $this->app->get('theme', '');
            if( $themePath && $theme )
            {
                $themePath .= '/'. $theme; 
            }
            else
            {
                $themePath = SPT_PLUGIN_PATH. '/'. $this->app->get('currentPlugin', ''). '/views';
            }
    
            define('SPT_THEME_PATH', $themePath);

            // Load VMs for theme
            if( is_file(SPT_THEME_PATH.'/_vms.php'))
            { 
                $vmlist = (array) require_once SPT_THEME_PATH.'/_vms.php';
                foreach($vmlist as $key => $item)
                {
                    $path = ''; $namespace = ''; $onlyWidget = true;
                    if(is_string($item))
                    { 
                        $path = SPT_PLUGIN_PATH. '/'. $item. '/viewmodels';
                        $namespace = $this->app->getNamespace(). '\\plugins\\'. $item;
                    }
                    elseif(is_array($item))
                    { 
                        list($path, $namespace, $onlyWidget) = $item; 
                    }

                    $this->loadVMFolder($path, $namespace, $onlyWidget);
                }
            }
        }

        return SPT_THEME_PATH;
    }

    protected function loadVMFolder($path, $namespace, $onlyWidget = false)
    {
        if(!is_dir($path)) return;

        foreach(new \DirectoryIterator($path) as $file) 
        {
            if(!$file->isDot() && $file->isDir())
            {
                $inner = $file->getBasename();
                $this->loadVMFolder($path. '/'.$inner, $namespace. '\\'. $inner, $onlyWidget);
            }
            elseif (!$file->isDot() && $file->isFile()) 
            {
                $filename = $file->getBasename(); 
                $vmName = substr($filename, 0, strlen($filename) - 4) ;
                $vmName = ucfirst($vmName);
                $vmName = $namespace. '\\viewmodels\\'. $vmName;

                if(class_exists($vmName))
                {
                    $registers = $vmName::register();
                    if($onlyWidget)
                    {
                        foreach($registers as $i => $line)
                        {
                            if(strpos($line, 'widgets.') !== 0)
                            {
                                unset($registers[$i]);
                            }
                        }
                    } 

                    ViewModelHelper::prepareVM(
                        $vmName, 
                        $registers, 
                        $this->getContainer()
                    );
                }
            }
        }
    }
}