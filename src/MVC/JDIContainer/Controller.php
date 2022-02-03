<?php
/**
 * SPT software - MVC Controller with simple DI
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: MVC Controller
 * 
 */

namespace SPT\MVC\JDIContainer;

use SPT\JDIContainer\Base;
use SPT\Theme; 
use SPT\App\Adapter as Application;
use SPT\App\Instance as AppIns;

class Controller extends Base
{
    public function prepareView()
    {
        if(AppIns::path('theme') && $this->app->config->exists('theme'))
        {
            $themePath = AppIns::path('theme'). $this->app->config->theme;
            $overrideLayouts = [
                $themePath. '__.php',
                $themePath. '__/index.php',
                AppIns::path('view'). '__.php',
                AppIns::path('view'). '__/index.php'
            ];
        }
        else
        {
            $themePath = AppIns::path('view');
            $overrideLayouts = [
                AppIns::path('view'). '__.php',
                AppIns::path('view'). '__/index.php'
            ];
        }
        
        $this->view = new View();
        $this->view->init(
            $this->app->lang, 
            new Theme($themePath, $overrideLayouts)
        );
    }

    public function toHtml()
    {
        $this->prepareView();
        $layout = $this->app->get('layout', 'default');
        
        $data = $this->getAll(); 
        if(is_array($data) && count($data))
        {
            $this->view->setIndex($layout); // because we call set before render
            $this->view->set($data);
        } 

        $page = $this->app->get('page', 'index');
        $this->app->response( $this->view->createPage($layout, $page) );
    }

    public function toJson($data=null)
    {
        header('Content-Type: application/json;charset=utf-8');
        if(null === $data) $data = $this->getAll();
        $this->app->response( $data );
    }

    public function toAjax()
    {
        $this->prepareView();
        $layout = $this->app->get('layout');

        $data = $this->getAll();
        if(is_array($data) && count($data))
        {
            $this->view->setIndex($layout); // because we call set before render
            $this->view->set($data);
        }

        $this->app->response( $this->view->_render($layout) );
    }
}