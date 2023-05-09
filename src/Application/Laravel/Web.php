<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A web application based Laravel container
 * 
 */

namespace SPT\Application\Laravel;
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use SPT\Storage\File\ArrayType as FileArray;
use SPT\Container\Laravel as Container;
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Base as SPTRequest;

class Web extends \SPT\Application\Core 
{
    private $container;
    protected $slim;

    public function __construct(string $publicPath, string $pluginPath, string $configPath = '', string $namespace = '')
    {
        define('SPT_PUBLIC_PATH', $publicPath);
        define('SPT_PLUGIN_PATH', $pluginPath);

        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;
        $this->psr11 = true; 

        // Create new IoC Container instance
        $this->container = new Container;
        
        $this->cfgLoad($configPath); 
        $this->prepareEnvironment();
        $this->plgLoad('bootstrap', 'initialize');
        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }
    
    public function getRouter()
    {
        return $this->container->get('router');
    }

    public function getRequest()
    {
        return $this->container->get('request');
    }
    
    protected function prepareEnvironment()
    {   
        // secrect key
        // terminal or router 
        $this->container->instance('router', new Router($this->container->config->subpath, ''), true);
        // setup container
        //$container = $this->getContainer();
        $this->container->instance('app', $this, true);
        // create request 
        $this->container->set('request', new SPTRequest());
    }

    public function cfgLoad(string $configPath = '')
    {
        $config = new FileArray();
        if( file_exists($configPath) )
        {
            $config->import($configPath);
        }
        $this->container->instance('config', $config);
    }

    private function addEndpoint($slug, $endpoint, $method = 'get')
    {
        $container = $this->getContainer();
        $params = [];

        if(is_array($endpoint))
        {
            if( is_array($endpoint['fnc']))
            {
                foreach($endpoint['fnc'] as $med => $function)
                {
                    $this->addEndpoint($slug, $function, $med);
                }
                
                return;
            }
            else
            {
                $todo =  $endpoint['fnc'];
                unset($endpoint['fnc']);
                if(count($endpoint))
                {
                    $params = $endpoint;
                }
            }
        }
        else
        {
            $todo = $endpoint;
        }

        $app = $this;

        $this->slim->$method($slug, function (Request $request, Response $response) use ($app, $todo, $params) {

            try{

                $try = explode('.', $todo);
        
                if(count($try) !== 3)
                {
                    throw new \Exception('Not correct routing');
                } 

                // support if this home - special deals
                if($app->getContainer()->get('router')->get('isHome'))
                {
                    $this->plgLoad('routing', 'isHome'); 
                }
                
                if(count($params))
                {
                    foreach($params as $K=>$V)
                    {
                        $this->set($K, $V);
                    }
                }
    
                list($plugin, $controllerName, $func) = $try;
                $plugin = strtolower($plugin);
                $app->set('currentPlugin', $plugin);
    
                $plgRegister = $app->getNamespace(). '\\plugins\\'. $plugin. '\\registers\\Dispatcher';
                if(!class_exists($plgRegister))
                {
                    throw new \Exception('Invalid plugin '. $plugin);
                }
                if(!method_exists($plgRegister, 'dispatch'))
                {
                    throw new \Exception('Invalid dispatcher of plugin '. $plugin);
                }

                $plgRegister::dispatch($app, $controllerName, $func);
               // $response->getBody()->write(  $sth  );
    
            }
            catch (\Exception $e) 
            {
                $response->getBody()->write(
                    '[Error] ' . $e->getMessage()
                ); 
            }

            return $response;
        });
    }

    public function execute(string $themePath = '')
    {
        $container = $this->getContainer();
        $config = $container->get('config');
        $this->slim = AppFactory::create(); 
        $this->slim->setBasePath('/'.$config->subpath.'/');
        $this->slim->addErrorMiddleware(true, true, true);

        if($themePath)
        {
            $this->set('themePath', $themePath);
        }

        $this->plgLoad('routing', 'registerEndpoints', function ($endpoints) {

            foreach($endpoints as $slug => $endpoint)
            {
                $this->addEndpoint($slug, $endpoint);
            } 
        }); 

        $this->slim->run();
    }
}