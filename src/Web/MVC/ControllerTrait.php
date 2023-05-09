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

        $themePath = $this->app->get('themePath', '');
        $theme = $this->app->get('theme', '');
        if( $themePath && $theme )
        {
            $themePath .= '/'. $theme; 
        }
        else
        {
            $themePath = SPT_PLUGIN_PATH. '/'. $pluginName. '/views';
        }
        
        return new View(
            $pluginName, 
            new Theme($themePath),
            new ViewComponent($this->app->getRouter())
        );
    }

    public function toHtml()
    {
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
        $data = (array) $this->getAll();
        $layout = $this->app->get('layout', 'default');
        $view = $this->getView();

        return $view->renderLayout($layout, $data);
    }
}