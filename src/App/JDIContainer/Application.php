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
use SPT\Support\Token;
use SPT\Support\Env;
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

                // create query
                if( $config->exists('db') )
                {
                    $this->prepareDB($config);
                }
                
                // create router based config
                $this->prepareRouter($config);
            }

            // create session
            $this->prepareSession();

            $this->prepareUser();

            // prepare Service Provider
            $this->prepareServiceProvider();

            // process app
            $this->processRequest();
        }
        catch (Exception $e) 
        {
            $this->response('Caught \Exception: '.  $e->getMessage(), 500);
        }

        return $this;
    }

    public function has(string $name)
    {
        return $this->getContainer()->exists($name);
    }

    public function prepareRouter($config)
    {
        if($config->exists('endpoints'))
        {
            $sitePath = $config->exists('sitepath') ? $config->sitepath : '';
            $router = new Router($sitePath);
            $router->import($config->endpoints);
            $this->getContainer()->share('router', $router, true);
        }
    }

    public function prepareDB($config)
    {
        try{
            $pdo = new PdoWrapper( $config->db );
        
            if(!$pdo->connected)
            {
                $tmp = $pdo->getLog();
                throw new \Exception('Connection failed. '. $tmp[1], 500); 
            }

            $this->getContainer()->set('query', new Query( $pdo, ['#__'=>  $config->db['prefix']]));
        } 
        catch(\Exception $e) 
        {
            die( $e->getMessage() );
        }
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
        $container = $this->getContainer();
        $session = new Session(
            $container->exists('query') ? 
            new DatabaseSession( new DatabaseSessionEntity($this->query), $this->getToken() ) :
            new PhpSession()
        );
        $container->set('session', $session);
    }

    protected function processRequest(){} 

    protected function prepareServiceProvider(){}

    protected function prepareUser(){}

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

    public function getToken(string $context = '_app_')
    {
        if('_secrect_' === $context)
        {
            return empty($this->config->exists('secrect')) ? strtotime('now') : $this->config->secrect;
        }

        $container = $this->getContainer();
        $res = '';

        if( !$container->exists('secrects') )
        {
            $res = $this->createToken($context);
            $container->set('secrects', [ $context => $res]);
        } 
        else
        {
            $secrects = $container->get('secrects');
            if(!isset($secrects[$context]))
            {
                $secrects[$context] = $this->createToken($context);
            }
            $res = $secrects[$context];
        }

        return $res;
    }

    protected function createToken(string $context)
    {
        $browser = $this->request->server->get('HTTP_USER_AGENT', '');
        $secrect = $this->getToken('_secrect_');

        $cookie = $this->request->cookie->get($secrect, '_do_not_set_');
        if ('_do_not_set_' == $cookie)
        {
            $cookie = Token::md5( rand(199999, strtotime('now')), 8);
            $this->request->cookie->set($secrect, $cookie);
        }

        return Token::md5(
            Token::md5($context, 4). Env::getClientIp(). $browser. $cookie
        );
    }

    public function validateToken(string $token, string $context = '_app_')
    {
        $res = $this->getToken($context);
        return $res === $token;
    }
}
