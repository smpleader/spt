<?php
/**
 * SPT software - Controller
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core controller
 * 
 */

namespace SPT\Web\MVC;

use SPT\Application\IApp;

use SPT\BaseObj;  
use SPT\Container\ContainerClient;   
 
trait ControllerTrait
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
 
        $view = new View($layoutPath, $themePath, $theme);

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
        $layout = $this->app->get('layout', 'default');
        $page = $this->app->get('page', 'index');
        $themePath = $this->app->get('themePath', '');
        $theme = $this->app->get('theme', '');
        $data = (array) $this->getAll();

        $layoutPath = $this->app->getCurrentPluginPath(). '/views/';
 
        $view = new View($layoutPath, $themePath, $theme);

        return $view->renderLayout($layout, $data);
    }
}