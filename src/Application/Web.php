<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A web application based Joomla container
 * @version: 0.8
 * 
 */

namespace SPT\Application;
 
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Base as Request;
use SPT\Response; 

class Web extends \SPT\Application\Base
{
    protected function envLoad()
    {   
        // secrect key 
        // setup container
        $this->container->set('app', $this, true);
        // create request
        $this->request = new Request(); 
        $this->container->set('request', $this->request);
        // create router
        $this->router = new Router($this->config->subpath, '');
        $this->container->set('router', $this->router, true);
        // access to app config 
        $this->container->set('config', $this->config, true);
    }

    public function execute(string $themePath = '')
    {
        $router = $this->router;
        $this->plgLoad('routing', 'registerEndpoints', function ($endpoints) use ($router){
            $router->import($endpoints);
        }); 

        if($masterPlg = $this->config->master)
        {
            $this->plgRun($masterPlg, 'Routing', 'afterRegisterEndpoints');
        }

        if($themePath) $this->set('themePath', $themePath);

        try{

            $try = $this->router->parse($this->request);
            if(false === $try)
            {
                if($this->config->pagenotfound)
                {
                    $try = [$this->config->pagenotfound, []];
                }
                else
                {
                    $this->raiseError('Invalid request', 500);
                }
            }

            list($todo, $params) = $try;
            $try = explode('.', $todo);
            
            if(count($try) !== 3)
            {
                $this->raiseError('Not correct routing', 500);
            } 

            if(count($params))
            {
                foreach($params as $key => $value)
                {
                    $this->set($key, $value);
                }
            }

            // support if this is home - special deals
            if($this->router->get('isHome'))
            {
                $this->plgLoad('routing', 'isHome'); 
            }

            list($plugin, $controller, $function) = $try;
            $plugin = strtolower($plugin);
            $this->set('currentPlugin', $plugin);
            
            return $this->plgDispatch($controller, $function);

        }
        catch (\Exception $e) 
        {
            $this->raiseError('[Error] ' . $e->getMessage(), 500);
        }
    }
}