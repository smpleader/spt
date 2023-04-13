<?php
/**
 * SPT software - Controller
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core controller
 * 
 */

namespace SPT\Web\MVVM;

use SPT\Application\IApp;

use SPT\BaseObj;
use SPT\Response;
use SPT\Web\Theme;   
use SPT\Web\ViewComponent;   

trait ControllerTrait
{
    protected $app;

    protected function getView()
    {
        $pluginName = $this->app->get('currentPlugin', '');

        if(empty($pluginName))
        {
            throw new \Exception('Invalid plugin, can not create content page');
        }

        $pluginLayout = $this->app->getPluginPath(). $pluginName.'/views/';

        $themePath = $this->app->get('themePath', '');
        $theme = $this->app->get('theme', '');
        if( $themePath && $theme )
        {
            $themePath .= '/'. $theme;
            $themeLayout = $themePath. '/'. $pluginName. '/';
            $layouts = [
                $themeLayout. '__.php',
                $themeLayout. '__/index.php',
                $pluginLayout. '__.php',
                $pluginLayout. '__/index.php',
            ];
        }
        else
        {
            $themePath = $pluginLayout;
            $layouts = [
                $pluginLayout. '__.php',
                $pluginLayout. '__/index.php'
            ];
        }

        return new View(
            $layouts, 
            new Theme($themePath),
            new ViewComponent($this->app->getRouter())
        );
    }

    public function toHtml()
    { 
        $this->registerViewModels();
        $data = (array) $this->getAll();
        $layout = $this->app->get('layout', 'default');
        $page = $this->app->get('page', 'index');
        $view = $this->getView();

        return $view->renderPage( $page, $layout, $data );
    }

    public function toJson($data=null)
    {
        header('Content-Type: application/json;charset=utf-8');
        if(null === $data) $data = $this->getAll();
        return json_encode($data);
    }

    public function toAjax()
    {
        $this->registerViewModels();
        $data = (array) $this->getAll();
        $layout = $this->app->get('layout', 'default');
        $view = $this->getView();

        return $view->renderLayout($layout, $data);
    }
    
    public function registerViewModels()
    {
        foreach(new \DirectoryIterator($this->app->getPluginPath()) as $plg) 
        {
            if (!$plg->isDot() && $plg->isDir()) 
            { 
                $plgName = $plg->getBasename();
                $vmFolder = $plg->getPath(). '/'. $plgName. '/viewmodels';
                
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
                                    $this->app->supportContainer() ? $this->app->getContainer() : NULL
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}