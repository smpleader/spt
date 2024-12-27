<?php
/**
 * SPT software - SPT application for a website
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A web application based SPT framework
 * @version: 0.8
 * 
 */

namespace SPT\Application;
 
use SPT\Router\ArrayEndpoint as Router;
use SPT\Response; 
use SPT\Support\App;

class Web extends Base
{
    protected function routing()
    {
        // TODO: load cache
        // TODO: load table
        $router = $this->router;
        $this->plgManager->call('all')->run('routing', 'registerEndpoints', false, function ($endpoints) use ($router){
            $router->import($endpoints);
        });

        if( $this->config->exists('router.home') )
        {
            $this->router->import(['' => $this->config->of('router.home')]);
        }
        
        $this->plgManager->call('all')->run('routing', 'afterRouting');
    }

    public function execute( string | array $_parameters = [])
    {
        $this->routing();

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
                    $this->raiseError('PageNotFound', 404);
                }

                list($todo, $siteParams) = $try;
            }

            $try = explode('.', $todo);
            
            if(count($try) !== 3)
            {
                $this->raiseError('Incorrect routing', 500);
            } 

            if(count($siteParams))
            {
                foreach($siteParams as $key => $value)
                {
                    $this->set($key, $value);
                }
            }

            list($pluginName, $controller, $function) = $try;

            $this->set('controller', $controller);
            $this->set('function', $function);

            // support if this is  home and need a special deal
            if($this->router->get('isHome'))
            {
                $this->plgManager->call('all')->run('Routing', 'isHome');
            }

            $this->prepareDispatch($pluginName);

            return $this->plgManager->call($pluginName)->run('Dispatcher', 'dispatch', true);

        }
        catch (\Exception $e) 
        {
            $this->raiseError('[Error] ' . $e->getMessage(), 500);
        }
    }

    public function raiseError(string $msg, $code = 500)
    {
        $this->set('error', $msg);
        $this->set('errorCode', $code);
        $this->set('env', 'web');

        $this->plgManager->call('all')->run('Error', 'catch', false);

        // if no plugin handle this error, just stop
        parent::raiseError( $msg, $code);
    }    
}