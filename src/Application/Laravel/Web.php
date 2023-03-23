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

class Web extends \SPT\Application\Core 
{
    private $container;
    protected $slim;
    public function getContainer()
    {
        return $this->container;
    }

    public function __construct(string $pluginPath, string $configPath = '', string $namespace = '')
    {
        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;
        $this->pluginPath = $pluginPath;       
        $this->psr11 = true; 

        // Create new IoC Container instance
        $this->container = new Container;
        
        $this->loadConfig($configPath); 
        $this->prepareEnvironment();
        $this->loadPlugins('bootstrap', 'initialize');
        return $this;
    }
    
    protected function prepareEnvironment()
    {   
        // secrect key
        // terminal or router 
        // setup container
        $container = $this->getContainer();
        $container->instance('app', $this, true);
        // create request 
    }

    public function loadConfig(string $configPath = '')
    {
        $config = new FileArray();
        if( file_exists($configPath) )
        {
            $config->import($configPath);
        }
        $this->getContainer()->instance('config', $config);
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
                    $this->parseRouter($slug, $function, $med);
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

        /*if(count($params))
        {
            $this->set( 'params', $params);
        }*/

        $app = $this;

        $this->slim->$method($slug, function (Request $request, Response $response) use ($app, $todo) {

            try{

                $try = explode('.', $todo);
        
                if(count($try) !== 3)
                {
                    throw new \Exception('Not correct routing');
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
                
                $sth = $plgRegister::dispatch($app, $controllerName, $func);
                //var_dump($sth, $plgRegister, $controllerName, $func);
                $response->getBody()->write(
                    $sth
                );
    
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

        $this->loadPlugins('routing', 'registerEndpoints', function ($endpoints) {

            foreach($endpoints as $slug => $endpoint)
            {
                $this->addEndpoint($slug, $endpoint);
            } 
        }); 

        $this->slim->run();
    }

    public function url(string $subpath = '')
    {
        // TODO
        // https://www.slimframework.com/docs/v4/cookbook/retrieving-current-route.html
        return  '';//$this->getContainer()->get('router')->url($subpath);
    }
}