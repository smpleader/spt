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

class Controller extends Client
{
    /**
     * Internal variable to check if we apply MVVM or not
     * This is important for View
     * 
     * @var bool $supportMVVM
     */
    protected $supportMVVM = false;

    protected function getTheme()
    {
        // mainPlugin | childPlugin -> currentPlugin
        $this->setCurrentPlugin();
            
        /**
         * NOTICE those values are available after setCurrentPlugin() or plugin/registers/Dispatcher process
         */
        $pluginPath = $this->app->get('pluginPath');
        $plugin = $this->app->get('currentPlugin');
        $themePath = $this->app->any('themePath', 'theme.path', '');
        $theme = $this->app->any('theme', 'theme.default', '');
        $listPlg = $this->app->plugin(true);
        $paths = [];
        foreach($listPlg as $plgName => $d)
        {
            $paths[$plgName] = $d['path'];
        }

        if( $theme )
        {
            if(file_exists($theme))
            {
                $_themePath = $theme;
            }
            elseif(file_exists($themePath. '/'. $theme))
            {
                $_themePath = $themePath. '/'. $theme;
            }
            else
            {
                throw new \Exception('Invalid theme '.$theme. ' or theme path '. $themePath);
            }

            $_themePath .= '/';

            $_overrides = [
                'layout' => [
                    $_themePath. '_layouts/'. $plugin. '/',
                    $pluginPath. 'views/layouts/'
                ],
                'widget' => [
                    $_themePath.'_widgets/__PLG__/',
                    '__PLG_PATH__/views/widgets/'
                ],
                'vcom' => [
                    $_themePath.'_vcoms/__PLG__/',
                    '__PLG_PATH__/views/vcoms/'
                ],
                '_path' => $paths
            ];

        }
        else
        {
            $_themePath = $pluginPath. 'views/';
            $_overrides = [
                'layout' => [$pluginPath. 'views/layouts/'],
                'widget' => ['__PLG_PATH__/views/widgets/'],
                'vcom' => ['__PLG_PATH__/views/vcoms/'],
                '_path' => $paths
            ];
        }

        return new Theme($_themePath, $_overrides);
    }

    /**
     * Return an View Instance based current override paths, theme, ViewComponent instance, mvvm mode
     * 
     * @return View new View instance
     */ 
    protected function getView()
    {
        return new View(
            $this->getTheme(),
            new ViewComponent($this->app->getRouter()),
            $this->supportMVVM
        );
    }

    /**
     * Return HTML format after a process
     * 
     * @return string HTML content body
     */ 
    public function toHtml()
    {
        $data = (array) $this->getAll();
        $layout = $this->app->get('layout', 'default');
        $page = $this->app->get('page', 'index');
        $view = $this->getView();

        return $view->renderPage( $page, $layout, $data );
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
        $layout = $this->app->get('layout', 'default');
        $view = $this->getView();

        return $view->renderLayout($layout, $data);
    }

    /**
     * Set plugin information, after Dispatcher found and dispatch the process
     *
     * @param string   $name  plugin name
     * @param bool    $silent through exception if a plugin is aleardy set
     * 
     * @throws Exception if plugin not found or plugin is already set 
     * @return bool 
     */ 
    public function setCurrentPlugin(string $name = '', $silent=true)
    {
        $current = $this->app->get('currentPlugin', true);
        if(true === $current)
        {
            // set plugin info
            $plugin = $this->app->plugin($name);
            if(false === $plugin)
            {
                throw new \Exception('Can not set current plugin with '.$name);
            }
            $this->app->set('currentPlugin', $plugin['name']);
            $this->app->set('namespace', $plugin['namespace']);
            $this->app->set('pluginPath', $plugin['path']);
        }
        else
        {
            if(!$silent)
            {
                throw new \Exception('Can not set current plugin twice');
            }
            return false;
        }
        return true;
    }
}