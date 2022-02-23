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

use SPT\JDIContainer\Base;
use SPT\Response;
use SPT\MagicObj;
use SPT\Query;
use SPT\Support\FncString;
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
use SPT\Reuse\Application as ApplicationTrait;

class Application extends Base implements Adapter
{
    use ApplicationTrait;
    
    public function execute()
    {
        AppIns::path('app') || die('App did not setup properly');

        try{

            $container = $this->getContainer();
            $container->share( 'app', $this, true);
            
            // create request
            $container->set('request', new Request());

            // create config
            if(AppIns::path('config'))
            {
                $config = new FileArray();
                $config->import(AppIns::path('config'));
                $container->set('config', $config);
                
                // create router based config
                if(AppIns::path('config') && $config->exists('endpoints'))
                {
                    $sitePath = $config->exists('sitepath') ? $config->sitepath : '';
                    $router = new Router($sitePath);
                    $router->import($config->endpoints);
                    $container->set('router', $router);
                }

                // create query
                if( $config->exists('db') )
                {
                    $container->set('query', new Query(
                        new PdoWrapper(
                            $config->db['host'],
                            $config->db['username'],
                            $config->db['passwd'],
                            $config->db['database'],
                            $config->db['options'],
                            $config->db['debug']
                        ), ['#__'=>  $config->db['prefix']]
                    ));
                }
            }

            // prepare Service Provider
            $this->prepareServiceProvider();

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
        
        $this->getContainer()->set('lang', $lang);
    }
    
    public function prepareSession()
    {
        $session = new Session();
        $container = $this->getContainer();
        $session->init(
            $container->has('query') ? 
            new DatabaseSession( new DatabaseSessionEntity($this->query), $this->getToken() ) :
            new PhpSession()
        );
        $container->set('session', $session);
    }

    protected function processRequest()
    {
        
    } 

    protected function prepareServiceProvider()
    {

    }

    public function getController(string $name)
    {
        $name = str_replace('-', '\\', $name);
        $controllerName = empty($this->get('plugin', '')) ? 'controllers\\'. FncString::uc($name)
            : 'plugins\\'. $this->get('plugin'). '\controllers\\'. FncString::uc($name);

        $controllerName = $this->getName($controllerName);
        if(!class_exists($controllerName))
        {
            throw new \Exception('Controller '. $name. ' not found', 500);
        }
        
        return new $controllerName($this->getContainer());
    }
}
