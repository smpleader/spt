<?php
/**
 * SPT software - A Controller using MVC ( in default )
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core controller
 * 
 */

namespace SPT\Web;

use SPT\Application\IApp;
use SPT\Container\Client;   
use SPT\Traits\ObjectHasInternalData;

class Controller extends Client
{
    use ObjectHasInternalData;

    /**
     * Return HTML format after a process
     * 
     * @return string HTML content body
     */ 
    public function toHtml()
    {
        $data = (array) $this->getAll(); 
        $page = $this->app->get('page', 'index');
        $view = $this->getView();

        return $view->render( $page, $data );
    }

    /**
     * Return Jsom format after a process
     *
     * @param object|array   $data  Data of json to be shown in content body
     * 
     * @return string Json content body
     */ 
    public function toJson($data=null)
    {
        header('Content-Type: application/json;charset=utf-8');
        if(null === $data) $data = $this->getAll();
        
        return json_encode($data);
    }

    /**
     * Return a content body for ajax response
     * Mostly it's for a html layout or  non-json content
     * 
     * @return string A content body
     */ 
    public function toAjax()
    {
        $data = (array) $this->getAll();
        $page = $this->app->get('page', 'ajax');
        $view = $this->getView();

        return $view->render($page, $data);
    }

    /**
     * Return an View Instance based current override paths, theme, ViewComponent instance, mvvm mode
     * 
     * @return View new View instance
     */ 
    protected function getView()
    {
        $pluginPath = $this->app->get('pluginPath', '_NOT_SET_'); 
        if('_NOT_SET_' === $pluginPath)
        {
            // Carefully check SPT\Support\App::createController()
            $this->app->raiseError('Invalid current plugin');
        }

        $currentPlugin = $this->app->get('currentPlugin');
        $pluginList = $this->app->plugin(true);

        $themePath = $this->app->any('themePath', 'theme.path', '');
        $theme = $this->app->any('theme', 'theme.default', '');

        if($themePath)
        {
            if(substr($themePath, -1) !== '/')
            {
                $themePath .= '/';
            }

            $themePath .= $theme;
            $themeConfigFile = $themePath. '/'. $this->app->any('themeConfigFile', 'theme.config', '_assets.php');
        }
        else
        {
            $themeConfigFile = '';
        }

        //$pluginPaths = $this->app->get('pluginPaths'); ?? 
        return new View(
            $pluginList, $currentPlugin, $themePath, $themeConfigFile
        );
    }

    public function execute()
    {
        $fName = $this->app->get('function', '', 'cmd');

        if(!method_exists($this, $fName))
        {
            throw new \Exception('Invalid function '. $fName);
        }

        $agurments = func_get_args();

        call_user_func_array([$this, $fName], $agurments); 

        $format = $app->get('format', 'html', 'cmd');
        $fName = 'to'. ucfirst($format);

        if(!method_exists($this, $format))
        {
            throw new \Exception('Invalid page format '. $format);
        }

        $this->app->finalize(
            call_user_func([$this, $fName]) 
        );
    }
}