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

class Controller extends Client
{
    protected $supportMVVM = false;
    protected $overrides;

    protected function getOverrideLayouts()
    {
        if(empty($this->overrides))
        {
            $pluginPath = $this->app->get('pluginPath');
            $plugin = $this->app->get('currentPlugin');
            $themePath = $this->app->get('themePath', '');
            $theme = $this->app->get('theme', '');
            if( $themePath && $theme )
            {
                $themePath .= '/'. $theme. '/'; 
                $this->overrides = [
                    $themePath.'_',
                    $themePath. $plugin. '/views/',
                    $pluginPath. '/views/'
                ];
            }
            else
            {
                $themePath = $pluginPath. '/views/';
                $this->overrides = [$pluginPath. '/views/'];
            }
    
            define('SPT_THEME_PATH', $themePath);
        }
        return $this->overrides;
    }

    protected function getView()
    {   
        return new View(
            $this->getOverrideLayouts(), 
            new Theme(),
            new ViewComponent($this->app->getRouter()),
            $this->supportMVVM
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