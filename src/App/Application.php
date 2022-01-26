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
        $key = strtolower($key);
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
        defined('APP_PATH') || die('App did not get setup constants');
        // create config
        $this->config = new SPT\Storage\FileArray(APP_PATH_CONFIG);
        // create request
        $this->request = new SPT\Request\Base();
        // create router
        $this->router = ..
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
        )
        // process app
    }
}
