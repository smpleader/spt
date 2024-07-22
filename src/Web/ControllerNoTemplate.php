<?php
/**
 * SPT software - A Controller using MVC ( in default ) without template engine
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core controller without template engine, concentrate into developing functions faster
 * 
 */

namespace SPT\Web;

use SPT\Application\IApp;
use SPT\Container\Client;   

class ControllerNoTemplate extends Controller
{
    /**
     * Get current internal variable $overrides
     * 
     * @return array $overrides
     */ 
    protected function getOverrideLayouts()
    {
        if(empty($this->overrides))
        {
            // mainPlugin | childPlugin -> currentPlugin
            $this->setCurrentPlugin();
            // auto use default theme
            if(empty( $this->app->get('theme', '') ))
            {
                $this->app->set('theme', $this->app->cf('defaultTheme'));
            }
            
            $pluginPath = $this->app->get('pluginPath');
            $plugin = $this->app->get('currentPlugin');
            $themePath = $this->app->get('themePath', '');
            $theme = $this->app->get('theme', '');
            $listPlg = $this->app->plugin(true);
            $paths = [];
            foreach($listPlg as $plgName => $d)
            {
                $paths[$plgName] = $d['path'];
            } 

            $themePath = $pluginPath. 'views/';
            $this->overrides = [
                'layout' => [$pluginPath. 'views/layouts/'],
                'widget' => ['__PLG_PATH__/views/widgets/'],
                'vcom' => ['__PLG_PATH__/views/vcoms/'],
                '_path' => $paths
            ]; 
    
            define('SPT_THEME_PATH', $themePath);
        }
        return $this->overrides;
    }

    /**
     * Return HTML format after a process
     * 
     * @return string HTML content body
     */ 
    public function toHtml()
    {
        $layout = $this->app->get('layout', '___'); 
        if('___' == $layout) exit(0); // should not say anything else


        $data = (array) $this->getAll();
        $view = $this->getView();

        return $view->renderLayout( $layout, $data );
    }
}