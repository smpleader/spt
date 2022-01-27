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

    public function factory(string $key)
    {
        $key = strtolower($key);
        if(in_array($key, ['config', 'router', 'query', 'request', 'user', 'session']))
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
}
