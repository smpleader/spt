<?php
/**
 * SPT software - Application Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Adapter
 * 
 */

namespace SPT\App\DI;

class WebApp extends Application
{
    public function redirect($url = null)
    {
        $msg = $this->get('message', '');
        if( !empty($msg) )
        {
            $this->session->set('flashMsg', $msg);
        }
        
        parent::redirect($url);
        exit(0);
    }

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
                $this->request->set('urlVars', $this->router->praseUrl($intruction['parameters']));
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
        
        if(count($try) == 2)
        {
            list($name, $function) = $try;
        }
        else
        {
            throw new \Exception('Not a controller', 500);
        }

        $controller = $this->getController($name);

        $controller->$function();

        if( 'display' !== $function )
        {
            $controller->display();
        }
    }

    protected function getController($name)
    {
        throw new \Exception('You did not create a controller', 500);
    }

    protected function processRequest()
    {
        try{

            // TODO1: check token security timeout
            // TODO2: support i18n
            // TODO3: support plugins
            $this->routing(); 

        }
        catch (\Exception $e) 
        {
            $this->response('[Error] ' . $e->getMessage(), 500);
        }
    } 
}
