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

    public function factory(string $key)
    {
        if(in_array($key, ['config', 'router', 'query', 'request', 'user']))
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
        // create config
        // create router
        // create query
        // create request
        // process app
    }
}
