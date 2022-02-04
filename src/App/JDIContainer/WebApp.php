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
            // This is for single app
            list($controllerName, $func) = $this->routing(); 

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
}
