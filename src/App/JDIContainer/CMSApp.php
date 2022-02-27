<?php
/**
 * SPT software - Application Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Adapter
 * 
 */

namespace SPT\App\JDIContainer;

use SPT\Support\Loader;
use SPT\Support\FncArray;
use SPT\Storage\File\IniType as FileIni;
use SPT\App\Instance as AppIns;

class CMSApp extends WebApp
{
    protected function routing()
    {
        $defaultEndpoint = $this->config->exists('defaultEndpoint') ? $this->config->defaultEndpoint : '';
        $intruction = $this->router->pathFinding($defaultEndpoint);
        $fnc = '';

        if( is_array($intruction) )
        {
            $fnc = $intruction['fnc'];
            unset($intruction['fnc']);
            foreach($intruction as $key => $value)
            {
                $this->set($key, $value);
            }

            if(isset($intruction['parameters']))
            {
                $this->request->set('urlVars', $this->router->parseUrl($intruction['parameters']));
                unset($intruction['parameters']);
            }
        } 
        elseif( is_string($intruction) ) 
        {
            $fnc = $intruction;
        } 
        else 
        {
            throw new \Exception('Invalid request', 500);
        }

        $method = $this->request->header->getRequestMethod();
        if(is_array($fnc))
        {
            if(isset($fnc[$method]))
            {
                $fnc = $fnc[$method];
                $this->set('method', $method);
            }
            elseif(isset($fnc['any']))
            {
                $fnc = $fnc['any'];
                $this->set('method', 'any');
            }
            else
            {
                throw new \Exception('Not a function', 500);
            }
        }

        $try = explode('.', $fnc);
        
        if(count($try) == 3)
        {
            return $try;
        }
        else
        {
            throw new \Exception('Not a controller', 500);
        }   
    }

    protected function processRequest()
    {
        try{

            // TODO1: check token security timeout  
            list($plugin, $controllerName, $func) = $this->routing(); 
            $this->set('plugin', strtolower($plugin));

            // create language
            $this->prepareLanguage();

            $controller = $this->getController($controllerName);

            $controller->$func();

            switch($this->get('format', ''))
            {
                case 'html': $controller->toHtml(); break;
                case 'ajax': $controller->toAjax(); break;
                case 'json': $controller->toJson(); break;
            }

        }
        catch (\Exception $e) 
        {
            $this->response('[Error] ' . $e->getMessage(), 500);
        }
    }

    protected function prepareServiceProvider()
    {
        $container = $this->getContainer();

        $enqueue = new \StdClass;

        if(AppIns::path('plugin'))
        {
            $plugins = ['core'];
            if($this->config->exists('plugins'))
            {
                $plugins = array_merge($plugins, $this->config->plugins);
            }

            foreach($plugins as $plugin)
            {
                $plugin = strtolower($plugin);
                $namespace = $this->getName('plugins\\'. $plugin);
                $class =  $namespace. '\plugin';

                if(class_exists($class))
                {
                    $path = AppIns::path('plugin'). $plugin. '/';
                    $plgObject = new $class;
                    $list = $plgObject->register();

                    if(is_bool($list)) // auto load
                    {
                        $list = [
                            'libraries' => [],
                            'models' => [],
                            'entities' => [],
                            'viewmodels' => []
                        ];
                    }

                    if(FncArray::isReady($list))
                    {
                        foreach($list as $type => $settings)
                        {
                            $fnc = 'load'.ucfirst($type);
                            if(method_exists($plgObject, $fnc))
                            {
                                $plgObject->{$fnc}($container);
                            }
                            else
                            {
                                $fullPath = isset($settings['path']) ? $settings['path'] : $path. $type;
                                $aliasList = isset($settings['alias']) ? $settings['alias'] : [];
                                $class_namespace = isset($settings['namespace']) ? $settings['namespace'] : $namespace. '\\'. $type;
                                $this->loadClass($fullPath, $class_namespace, $aliasList);
                            }
                        }
                    }
                    
                    $routing_file = $path. 'routing.php';
                    if(file_exists($routing_file))
                    {
                        $routing = (array) require_once $routing_file;
                        $this->router->import($routing);
                    }

                    $enqueue->{$plugin} = $plgObject;
                }
            }
        }

        $container->set('plugin', $enqueue);
    }

    protected function loadClass(string $path, string $namespace, array $aliasList)
    {
        $inners = Loader::findClass($path, $namespace);
        $container = $this->getContainer();
        foreach($inners as $class)
        {
            if(class_exists($class) && !$container->has($class))
            {
                $container->share( $class, new $class($container), true);
                if(isset($aliasList[$class]))
                {
                    $container->alias( $aliasList[$class], $class);
                }
            }
            // else { debug this }
        }
    }

    public function prepareLanguage()
    {
        // multi language
        $lang = $this->session->get('language', 'en');
        $try = $this->request->get->get('lang', '', 'cmd');
        $acceptLanguages = $this->config->exists('acceptLanguages') ? $this->config->acceptLanguages : ['en']; 
        if(in_array($try, $acceptLanguages) && $try != $lang)
        {
            $lang = $try;
            $this->session->set('language', $lang);
        }

        $this->lang =  new FileIni();

        $path = AppIns::path('plugin'). 'core/'.  $lang. '.ini';
        if(file_exists($path))
        {
            $this->lang->import($path);
        }

        if($this->get('plugin', 'core') != 'core')
        {
            $path = AppIns::path('plugin'). $this->get('plugin'). '/'. $lang. '.ini';
            if(file_exists($path))
            {
                $this->lang->import($path);
            }
        }
        
        $this->getContainer()->set('lang', $lang);
    }
    
}
