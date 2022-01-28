<?php
/**
 * SPT software - Application Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Adapter
 * 
 */

namespace SPT\App;

use SPT\BaseObj;
use SPT\Response;

class Application extends BaseObj implements Adapter
{
    protected $config;
    protected $router;
    protected $query;
    protected $request;
    protected $user;
    protected $session;
    protected $theme;
    protected $lang;

    public function factory(string $key)
    {
        $key = strtolower($key);
        if(in_array($key, ['config', 'router', 'query', 'request', 'user', 'session', 'theme', 'lange']))
        {
            if(!is_object($this->{$key}))
            {
                // didn't setup properly
                throw new Exception('Invalid Factory Object '.$key);
            } 

            return $this->{$key};
        }
        return false;
    }

    public function execute()
    {
        defined('APP_PATH') || die('App did not get setup constants');
        // create config, request, router, query, session
        // process app 
    }

    public function warning()
    {
        die($this->get('system-warning', 'Huh, something goes wrong..'));
    }

    public function turnDebug($turnOn = false)
    {
        if( $turnOn )
        {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
    }

    public function redirect($url = null)
    {
        $redirect = null === $url ? $this->get('redirect', '/') : $url;
        $redirect_status = $this->get('redirectStatus', '302');

        Response::redirect($redirect, $redirect_status);
    }

    public function response($content, $code='200')
    {
        Response::_($content, $code);
    }

    public function execute()
    {
        defined('APP_PATH') || die('App did not get setup constants');

        try{
            // create config
            $this->config = new SPT\Storage\FileArray(APP_PATH_CONFIG);
            // create config
            $this->lang = new SPT\Storage\FileIni(APP_PATH_LANGUAGE);
            // create request
            $this->request = new SPT\Request\Base();
            // create router
            $this->router = new SPT\Route($this->config->endpoints, $this->config->sitepath);
            // create query
            $this->query = new SPT\Query(
                new SPT\Extend\Pdo(
                    $this->config->db['host'],
                    $this->config->db['username'],
                    $this->config->db['passwd'],
                    $this->config->db['database'],
                    [],
                    $this->config->db['debug']
                ), ['#__'=>  $this->config->db['prefix']]
            );
            // create session
            $this->session = new SPT\Session\PhpSession();
            // process app
            $this->MVC();
        }
        catch (Exception $e) 
        {
            $this->set('system-warning', 'Caught exception: ',  $e->getMessage(), "\n");
            return $this->warning();
        }

        return $this;
    }

    public function MVC()
    {
        //try{

            $intruction = $this->router->pathFinding($this->config->defaultEndpoint);
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
                    $this->set('urlVars', $this->router->praseUrl($intruction['parameters']));
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

            $method = $this->router->getRequestMethod();
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
                    $this->response(['msg'=>'Not a function'], 404);
                }
            }

            $try = explode('.', $fnc);
            
            if(count($try) == 2 || $fnc == '')
            {
                list($controller, $function) = $try;

                if( false === strpos($controller, '-'))
                {
                    $controller = ucfirst($controller). 'Controller';
                }
                else
                {
                    $c = explode('-', $controller);
                    $controller = '\App\controllers\\'. $c[0]. '\\'. ucfirst($c[1]). 'Controller';
                }
            }
            else
            {
                $function = $fnc;
                $controller = 'HomeController';
            }
            
            if( $this->getContainer()->has($controller) )
            {
                $controller = $this->{$controller};
            }
            else
            {
                // TODO: create a default controller
                throw new \RuntimeException('Invalid Class '.$controller, 500);
            }

            
            // Language
            $this->loadCurrentLanguage(); 

            // Support Token
            App::setTimeout(30);
            if( null === App::token() || !App::token('isAlive'))
            {
                App::token([ Util::genToken(), strtotime('now') ]);
            } 
            else 
            {
                App::token(strtotime('now'));
            }

            $controller->$function();

            if( 'display' !== $function )
            {
                $controller->display();
            }

        /*}
        catch (\Exception $e) 
        {
            die('[Error] ' . $e->getMessage());
        }*/
    } 
}
