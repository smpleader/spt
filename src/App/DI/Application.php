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

use SPT\BaseObj;
use SPT\Response;
use SPT\MagicObj;
use SPT\Query;
use SPT\Route as Router;
use SPT\Extend\Pdo as PdoWrapper;
use SPT\Storage\FileArray;
use SPT\Storage\FileIni;
use SPT\Session\PhpSession;
use SPT\Session\DatabaseSession;
use SPT\Session\DatabaseSessionEntity;
use SPT\Session\Instance as Session;
use SPT\App\Instance as AppIns;
use SPT\App\Adapter;
use SPT\Request\Base as Request;

class Application extends BaseObj implements Adapter
{
    public $config;
    public $router;
    public $query;
    public $request;
    public $user;
    public $session; 
    public $lang;

    public function factory(string $key)
    {
        $key = strtolower($key);
        if(in_array($key, ['config', 'router', 'query', 'request', 'user', 'session', 'theme', 'lange']))
        {
            if(!is_object($this->{$key}))
            {
                // didn't setup properly
                throw new \Exception('Invalid Factory Object '.$key);
            } 

            return $this->{$key};
        }
        return false;
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
        AppIns::path('app') || die('App did not setup properly');

        try{

            // create request
            $this->request = new Request();

            // create config
            if(AppIns::path('config'))
            {
                $this->config = new FileArray(AppIns::path('config'));
                
                // create router based config
                if(AppIns::path('config') && $this->config->exists('endpoints'))
                {
                    $sitePath = $this->config->exists('sitepath') ? $this->config->sitepath : '';
                    $this->router = new Router();
                    $this->router->init($this->config->endpoints, $sitePath);
                }

                // create query
                if( $this->config->exists('db') )
                {
                    $this->query = new Query(
                        new PdoWrapper(
                            $this->config->db['host'],
                            $this->config->db['username'],
                            $this->config->db['passwd'],
                            $this->config->db['database'],
                            $this->config->db['options'],
                            $this->config->db['debug']
                        ), ['#__'=>  $this->config->db['prefix']]
                    );
                }
            }

            // create session
            $this->prepareSession();

            // process app
            $this->processRequest();
        }
        catch (Exception $e) 
        {
            $this->response('Caught \Exception: '.  $e->getMessage(), 500);
        }

        return $this;
    }

    public function prepareLanguage()
    {
        $this->lang = AppIns::path('language') ? new FileIni(AppIns::path('language')) : new MagicObj('--');
    }

    public function prepareSession()
    {
        if(empty($this->query))
        {
            $this->session =  new PhpSession();
        }
        else
        {
            // TODO set request ID
            $this->session = new Session(  new DatabaseSession( new DatabaseSessionEntity($this->query) ) ); 
        }
    }

    protected function processRequest()
    {
        
    }

    protected function getController(string $name)
    {

    }

}
