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
use SPT\Router\ArrayEndpoint as Router;
use SPT\Extend\Pdo as PdoWrapper;
use SPT\Storage\File\ArrayType as FileArray;
use SPT\Storage\File\IniType as FileIni;
use SPT\Session\PhpSession;
use SPT\Session\DatabaseSession;
use SPT\Session\DatabaseSessionEntity;
use SPT\Session\Instance as Session;
use SPT\App\Instance as AppIns;
use SPT\App\Adapter;
use SPT\Request\Base as Request;
use SPT\Traits\Application as ApplicationTrait;

class Application extends BaseObj implements Adapter
{
    use ApplicationTrait;

    public $config;
    public $router;
    public $query;
    public $request;
    public $user;
    public $session; 
    public $lang;

    public function has(string $name)
    {
        return in_array($name, ['config', 'router', 'query', 'user', 'session', 'lang']);
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
                $this->config = new FileArray();
                $this->config->import(AppIns::path('config'));
                
                // create router based config
                if(AppIns::path('config') && $this->config->exists('endpoints'))
                {
                    $sitePath = $this->config->exists('sitepath') ? $this->config->sitepath : '';
                    $this->router = new Router($sitePath);
                    $this->router->import($this->config->endpoints) ;
                }

                // create query
                if( $this->config->exists('db') )
                {
                    try{
                        $pdo = new PdoWrapper( $this->config->db );

                        if(!$pdo->connected)
                        {
                            $tmp = $pdo->getLog();
                            throw new \Exception('Connection failed. '. $tmp[1], 500); 
                        }

                        $this->query = new Query( $pdo, ['#__'=>  $this->config->db['prefix']] );
                    } 
                    catch(\Exception $e) 
                    {
                        die( $e->getMessage() );
                    }
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
        if(AppIns::path('language'))
        {
            $lang = new FileIni();
            $lang->import(AppIns::path('language'));
        }
        else
        {
            $lang = new MagicObj('--');
        }
        
        $this->lang = $lang;
    }

    public function prepareSession()
    {
        $this->session =  new Session(
            empty($this->query) ? new PhpSession()
                : new DatabaseSession( new DatabaseSessionEntity($this->query), $this->getToken() )
        );
    }

    protected function processRequest()
    {
        
    }

    public function getController(string $name)
    {
        $controllerName = $this->getName('controllers\\'.$name);
        if(!class_exists($controllerName))
        {
            throw new \Exception('Controller '. $name. ' not found', 500);
        }
        
        return new $controllerName($this);
    }

    public function getToken(string $context = '_app_')
    {
        return rand(0, 9999);
    }
}
