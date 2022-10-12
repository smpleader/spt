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

use SPT\Dispatcher;
use SPT\Support\Loader;
use SPT\Support\FncArray;
use SPT\Storage\File\IniType as FileIni;
use SPT\App\Instance as AppIns;
use SPT\User\Instance as User;
use SPT\User\SPT\User as UserAdapter;
use SPT\User\SPT\UserEntity;

class CMSApp extends WebApp
{
    protected function routing()
    {
        list($todo, $params) = $this->router->parse($this->config, $this->request);

        if(count($params))
        {
            foreach($params as $key => $value)
            {
                $this->set($key, $value);
            }
        }

        $try = Dispatcher::fire('permission', $todo);
        if( !$try )
        {
            throw new \Exception('You are not allowed.', 403);
        }

        $try = explode('.', $todo);
        
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
                    $plgObject = new $class($this);
                    $list = $plgObject->register();

                    $this->beforeLoadServiceProviderClass($plgObject, $list, $path);

                    if(FncArray::isReady($list))
                    {
                        foreach($list as $type => $settings)
                        {
                            $this->loadServiceProviderClass($plgObject, $namespace, $type, $settings, $path);
                        }
                    }

                    $this->afterLoadServiceProviderClass($plgObject, $list, $path);

                    $enqueue->{$plugin} = $plgObject;
                }
            }
        }

        $container->set('plugin', $enqueue);
    }

    protected function loadServiceProviderClass($plgObject, $namespace, $type, $settings, $path)
    {
        $container = $this->getContainer();
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

    protected function beforeLoadServiceProviderClass($plgObject, &$list, $path)
    {
        if(is_bool($list)) // auto load
        {
            $list = [
                'libraries' => [],
                'models' => [],
                'entities' => [],
                'viewmodels' => []
            ];
        }
    }

    protected function afterLoadServiceProviderClass($plgObject, &$list, $path)
    {
        $routing_file = $path. 'routing.php';
        if(file_exists($routing_file))
        {
            $routing = (array) require_once $routing_file;
            $this->router->import($routing);
        }
    }

    protected function loadClass(string $path, string $namespace, array $aliasList)
    {
        $inners = Loader::findClass($path, $namespace);
        $container = $this->getContainer();
        foreach($inners as $class)
        {
            if(class_exists($class) && !$container->exists($class))
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
        $languageName = $this->session->get('language', 'en');
        $try = $this->request->get->get('lang', '', 'cmd');
        $acceptLanguages = $this->config->exists('acceptLanguages') ? $this->config->acceptLanguages : ['en']; 
        if(in_array($try, $acceptLanguages) && $try != $lang)
        {
            $languageName = $try;
            $this->session->set('language', $lang);
        }

        $lang =  new FileIni();

        $path = AppIns::path('plugin'). 'core/'.  $languageName. '.ini';
        if(file_exists($path))
        {
            $lang->import($path);
        }

        if($this->get('plugin', 'core') != 'core')
        {
            $path = AppIns::path('plugin'). $this->get('plugin'). '/'. $languageName. '.ini';
            if(file_exists($path))
            {
                $lang->import($path);
            }
        }
        
        $this->getContainer()->set('lang', $lang);
    }

    public function prepareUser()
    {
        $user = new User( new UserAdapter() );
        $user->init([
            'session' => $this->session,
            'entity' => new  UserEntity($this->query)
        ]);
        $this->getContainer()->share('user', $user, true);
    }
    
}
