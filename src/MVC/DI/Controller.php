<?php
/**
 * SPT software - MVC Controller with simple DI
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: MVC Controller
 * 
 */

namespace SPT\MVC\DI;

use SPT\App\Adapter as Application;
use SPT\BaseObj;

class Controller extends BaseObj
{
    protected $app;
    protected $view;

    public function __construct(Application $app)
    {
        $this->app = $app; 
        $this->view = new View($app->lang);
    }

    public function display()
    {
        $layout = $this->app->get('layout', 'default');
        
        $data = $this->getAll();
        if(is_array($data) && count($data))
        {
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
        $layout = $this->app->get('layout');

        $data = $this->getAll();
        if(is_array($data) && count($data))
        {
            $this->view->set($data);
        }

        $this->app->response( $this->view->_render($layout) );
    }
}