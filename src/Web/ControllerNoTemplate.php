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
    protected function getTheme()
    {
        $this->setCurrentPlugin();
        
        /**
         * NOTICE those values are available after setCurrentPlugin() or plugin/registers/Dispatcher process
         */
        $pluginPath = $this->app->get('pluginPath'); 
        $themePath = $this->app->any('themePath', 'theme.path' '');
        $theme = $this->app->any('theme', 'theme.default', '');
        $listPlg = $this->app->plugin(true);
        $paths = [];
        foreach($listPlg as $plgName => $d)
        {
            $paths[$plgName] = $d['path'];
        } 

        $_themePath = $pluginPath. 'views/';
        $_overrides = [
            'layout' => [$pluginPath. 'views/layouts/'],
            'widget' => ['__PLG_PATH__/views/widgets/'],
            'vcom' => ['__PLG_PATH__/views/vcoms/'],
            '_path' => $paths
        ]; 

        return new Theme($_themePath, $_overrides);
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