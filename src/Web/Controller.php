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
                $themePath = SPT_PLUGIN_PATH. '/'. $pluginName. '/views';
            }
    
            define('SPT_THEME_PATH', $themePath);
        }

        return SPT_THEME_PATH;
    }

    protected function getView()
    {
        $pluginName = $this->app->get('currentPlugin', '');

        if(empty($pluginName))
        {
            throw new \Exception('Invalid plugin, can not create content page');
        }

        $this->getThemePath();
        
        return new View(
            $pluginName, 
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