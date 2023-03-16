<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A web application based Joomla container
 * 
 */

namespace SPT\Application;
 
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Base as Request;
use SPT\Response;

class JApp extends Simple
{
    private function prepareEnvironment()
    {
        // secrect key
        // terminal or router
        $this->request = new Request();
        // setup container
    }

    public function execute(string $themePath = '')
    {
        $router = new Router($this->get('subpath', ''));

        $this->loadPlugins('routing', 'registerEndpoints', function ($endpoints) use ( $router ){
            $router->import($endpoints);
        }); 

        list($todo, $params) = $router->parse($this->get('defaultEndpoint', false), $this->request);
        $try = explode('.', $todo);
        
        if(count($try) !== 3)
        {
            Response::_500('Not correct routing');
        } 

        if(count($params))
        {
            foreach($params as $key => $value)
            {
                $this->set($key, $value);
            }
        }

        try{

            $this->router = $router;

            if(!$themePath)
            {
                $this->set('themePath', $themePath);
            }

            list($plugin, $controllerName, $func) = $try;
            $plugin = strtolower($plugin);

            $plgRegister = $this->namespace. '\\'. $plugin. '\\registers\\Dispatcher';
            if(!class_exists($plgRegister))
            {
                throw new \Exception('Invalid plugin '. $plugin);
            }
            if(!method_exists($plgRegister, 'dispatch'))
            {
                throw new \Exception('Invalid dispatcher of plugin '. $plugin);
            }
            
            return $plgRegister::dispatch($this, $controllerName, $func);

        }
        catch (\Exception $e) 
        {
            Response::_500('[Error] ' . $e->getMessage());
        }
    }
}