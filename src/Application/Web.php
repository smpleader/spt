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
        // private properties
        parent::envLoad();

        // setup container
        $this->container->set('app', $this);
        // create request
        $this->request = new Request(); 
        $this->container->set('request', $this->request);
        // create router
        $this->router = new Router($this->config->subpath, '');
        $this->container->set('router', $this->router);
        // access to app config 
        $this->container->set('config', $this->config);
        // token
        $this->container->set('token', new Token($this->config, $this->request));
    }

    protected function routing()
    {
        // TODO: load cache
        // TODO: load table
        $router = $this->router;
        $this->plgManager->call('all')->run('routing', 'registerEndpoints', false, function ($endpoints) use ($router){
            $router->import($endpoints);
        });
    }

    public function execute(string $themePath = '')
    {
        $this->routing();
        
        if($themePath) $this->set('themePath', $themePath);

        try{

            $try = $this->router->parse($this->request);
            if(false === $try)
            {
                if($this->config->pageNotFound)
                {
                    $try = [$this->config->pageNotFound, []];
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

            list($pluginName, $controller, $function) = $try;

            $plugin = $this->plgManager->getDetail($pluginName);

            if(false === $plugin)
            {
                $this->raiseError('Invalid plugin '.$pluginName, 500);
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
                $this->plgManager->call('all')->run('Routing', 'isHome');
            }

            $this->set('mainPlugin', $plugin);
            $this->set('controller', $controller);
            $this->set('function', $function);

            return $this->plgManager->call($pluginName)->run('Dispatcher', 'dispatch', true);

        }
        catch (\Exception $e) 
        {
            $this->raiseError('[Error] ' . $e->getMessage(), 500);
        }
    }

    public function plugin($name = '')
    {
        return '' == $name ? $this->get('mainPlugin') : 
                ( true === $name ? 
                    $this->plgManager->getList() : 
                    $this->plgManager->getDetail($name) 
                );
    }

    protected array $vmClasses;
    public function getVMList(string $plgName)
    {
        return isset($this->vmClasses[$plgName]) ? $this->vmClasses[$plgName] : [];
    }
}