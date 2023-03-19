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

class Controller  extends BaseObj
{
    protected $app;

    public function __construct(IApp $app)
    {
        $this->app = $app; 
    }

    public function toHtml()
    { 
        $layout = $this->app->get('layout', 'default');
        $page = $this->app->get('page', 'index');
        $themePath = $this->app->get('themePath', '');
        $theme = $this->app->get('theme', '');
        $data = (array) $this->getAll();

        $layoutPath = $this->app->getCurrentPluginPath(). '/views/';

        $this->registerViewModels();
 
        $view = new View($layoutPath, $themePath, $theme);

        if( 0 !== strpos($layout, 'layouts.') )
        {
            $layout = 'layouts.'. $layout;
        }

        ViewModelHelper::deployVM($layout, $data);

        Response::_200( $view->renderPage( $page, $layout, $data ) );
    }

    public function toJson($data=null)
    {
        header('Content-Type: application/json;charset=utf-8');
        if(null === $data) $data = $this->getAll();
        Response::_200( $data );
    }

    public function toAjax()
    {
        $layout = $this->app->get('layout', 'default');
        $page = $this->app->get('page', 'index');
        $themePath = $this->app->get('themePath', '');
        $theme = $this->app->get('theme', '');
        $data = (array) $this->getAll();

        $layoutPath = $this->app->getCurrentPluginPath(). '/views/';

        $this->registerViewModels();
 
        $view = new View($layoutPath, $themePath, $theme);

        if( 0 !== strpos($layout, 'layouts.') )
        {
            $layout = 'layouts.'. $layout;
        }

        ViewModelHelper::deployVM($layout, $data);

        Response::_200( $view->renderLayout($layout, $data) );
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