<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A web application based Joomla container
 * 
 */

namespace SPT\Application\Laravel;
 
use Illuminate\Container\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

class Web extends \SPT\Application\Core 
{
    private $container;
    public function getContainer()
    {
        return $container;
    }
    public function __construct(string $pluginPath, string $configPath = '', string $namespace = '')
    {
        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;
        $this->pluginPath = $pluginPath;       
        $this->psr11 = true; 

        // Create new IoC Container instance
        $this->container = new Illuminate\Container\Container;
        
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
        $container->bind('app', $this, true);
        // create request 
    }

    public function loadConfig(string $configPath = '')
    {
        $config = new FileArray();
        if( file_exists($configPath) )
        {
            $config->import($configPath);
        }
        $this->getContainer()->set('config', $config);
    }

    private function addEndpoint($slug, $endpoint, $method = 'get')
    {
        $app = AppFactory::create(); 
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

        if(count($params))
        {
            foreach($params as $key => $value)
            {
                $this->set($key, $value);
            }
        }

        $app->$method($slug, function (Request $request, Response $response) use ($container, $todo) {

            try{

                $try = explode('.', $todo);
        
                if(count($try) !== 3)
                {
                    Response::_500('Not correct routing');
                } 

                if($themePath)
                {
                    $this->set('themePath', $themePath);
                }
    
                list($plugin, $controllerName, $func) = $try;
                $plugin = strtolower($plugin);
                $this->set('currentPlugin', $plugin);
    
                $plgRegister = $this->namespace. '\\plugins\\'. $plugin. '\\registers\\Dispatcher';
                if(!class_exists($plgRegister))
                {
                    throw new \Exception('Invalid plugin '. $plugin);
                }
                if(!method_exists($plgRegister, 'dispatch'))
                {
                    throw new \Exception('Invalid dispatcher of plugin '. $plugin);
                }
                
                $plgRegister::dispatch($this, $controllerName, $func);
    
            }
            catch (\Exception $e) 
            {
                Response::_500('[Error] ' . $e->getMessage());
            }
            
            //$response->getBody()->write('..');

            return $response;
        });
    }

    public function execute(string $themePath = '')
    {
        $container = $this->getContainer();
        $config = $container->get('config');

        $this->loadPlugins('routing', 'registerEndpoints', function ($endpoints) use ( $router ){

            foreach($endpoints as $slug => $endpoint)
            {
                $this->addEndpoint($slug, $endpoint);
            } 
        }); 

        $app->run();
    }

    public function url(string $subpath = '')
    {
        // TODO
        // https://www.slimframework.com/docs/v4/cookbook/retrieving-current-route.html
        return  $this->getContainer()->get('router')->url($subpath);
    }
}