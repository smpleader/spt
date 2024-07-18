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
use SPT\Request\Singleton as Request;
use SPT\Response; 

class Web extends Base
{
    protected function envLoad()
    {   
        // setup container
        $this->container->set('app', $this);

        // private properties
        parent::envLoad();

        // create request
        $this->request = Request::instance(); 
        if( !$this->container->exists('request') )
        {
            $this->container->set('request', $this->request);
        }

        // create router
        $subPath = $this->config->exists('subpath') ? $this->config->subpath : '';
        $this->router = new Router($subPath, '');
        if( !$this->container->exists('router') )
        {
            $this->container->set('router', $this->router);
        }

        // access to app config 
        if( !$this->container->exists('config') )
        {
            $this->container->set('config', $this->config);
        }

        // token
        if( !$this->container->exists('token') )
        {
            $this->container->set('token', new Token($this->config, $this->request));
        }
    }

    protected function routing()
    {
        // TODO: load cache
        // TODO: load table
        $router = $this->router;
        $this->plgManager->call('all')->run('routing', 'registerEndpoints', false, function ($endpoints) use ($router){
            $router->import($endpoints);
        });
        
        $this->plgManager->call('all')->run('routing', 'afterRouting');
    }

    public function execute( string | array $_parameters = [])
    {
        $this->routing();

        if( $this->cf('homeEndpoint') )
        {
            $this->router->import([
                '' => $this->cf('homeEndpoint')
            ]);
        }

        try{

            if( is_string($_parameters) )
            {
                $todo = $_parameters;
                $siteParams = [];
            }
            elseif(isset($_parameters['fnc']))
            {
                $todo = $_parameters['fnc'];
                unset($_parameters['fnc']);
                $siteParams = $_parameters;
            }
            else
            {
                $try = $this->router->parse($this->request);
                if(false === $try)
                {
                    if($this->config->exists('pageNotFound'))
                    {
                        $try = [$this->config->pageNotFound, []];
                    }
                    else
                    {
                        $this->raiseError('Invalid request', 500);
                    }
                }
                list($todo, $siteParams) = $try;
            }

            $try = explode('.', $todo);
            
            if(count($try) !== 3)
            {
                $this->raiseError('Incorrect routing', 500);
            } 

            list($pluginName, $controller, $function) = $try;

            $plugin = $this->plgManager->getDetail($pluginName);

            if(false === $plugin)
            {
                $this->raiseError('Invalid plugin '.$pluginName, 500);
            }
            
            if(count($siteParams))
            {
                foreach($siteParams as $key => $value)
                {
                    $this->set($key, $value);
                }
            }

            // support if this is  home and need a special deal
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

    
}