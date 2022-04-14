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
use SPT\Support\Env;
use SPT\Support\Token;
use SPT\User\Instance as User;
use SPT\User\SPT as UserAdapter;

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

    public function prepareUser()
    {
        $user = new User( new UserAdapter() );
        $user->init(['session' => $this->session]);
        $this->getContainer()->share('user', $user, true);
    }
}
