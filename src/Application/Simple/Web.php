<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An application context
 * 
 */

namespace SPT\Application\Simple;
 
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Base as Request;
use SPT\Response;
use \Exception;

class Web extends \SPT\Application\Core
{
    protected $request;
    public function getRequest()
    {
        return $this->request;
    }

    protected function prepareEnvironment()
    {
        // secrect key
        // terminal or router
        $this->request = new Request();
        // setup container
    }

    public function execute(string $themePath = '')
    {
        $router = new Router($this->get('subpath', ''));

        $this->plgLoad('routing', 'registerEndpoints', function ($endpoints) use ( $router ){
            $router->import($endpoints);
        }); 

        try{

            $try = $router->parse($this->request);
            if(false === $try)
            {
                throw new Exception('Invalid request', 500);
            }
    
            list($todo, $params) = $try;
            $try = explode('.', $todo);
            
            if(count($try) !== 3)
            {
                throw new Exception('Not correct routing', 500);
            } 
    
            if(count($params))
            {
                foreach($params as $key => $value)
                {
                    $this->set($key, $value);
                }
            }

            $this->router = $router;

            if($themePath)
            {
                $this->set('themePath', $themePath);
            }

            // support if this home - special deals
            if($router->get('isHome'))
            {
                $this->plgLoad('routing', 'isHome'); 
            }

            list($plugin, $controllerName, $func) = $try;
            $plugin = strtolower($plugin);
            $this->set('currentPlugin', $plugin);

            $plgRegister = $this->namespace. '\\plugins\\'. $plugin. '\\registers\\Dispatcher';
            if(!class_exists($plgRegister))
            {
                throw new Exception('Invalid plugin '. $plugin);
            }
            if(!method_exists($plgRegister, 'dispatch'))
            {
                throw new Exception('Invalid dispatcher of plugin '. $plugin);
            }
            
            return $plgRegister::dispatch($this, $controllerName, $func);

        }
        catch (Exception $e) 
        {
            $this->raiseError('[Error] ' . $e->getMessage(), 500);
        }
    }
}