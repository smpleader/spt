<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A web application based Joomla container
 * 
 */

namespace SPT\Application\Joomla;
 
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Base as Request;
use SPT\Response;
use SPT\Storage\File\ArrayType as FileArray;
use Joomla\DI\ContainerAwareTrait;
use Joomla\DI\ContainerAwareInterface;
use SPT\Container\Joomla as Container;

class Web extends \SPT\Application\Core implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(string $pluginPath, string $configPath = '', string $namespace = '')
    {
        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;
        $this->pluginPath = $pluginPath;       
        $this->psr11 = true; 

        $this->setContainer(new Container);
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
        $container->share('app', $this, true);
        // create request
        $container->set('request', new Request());
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

    public function execute(string $themePath = '')
    {
        $container = $this->getContainer();
        $config = $container->get('config');
        $request = $container->get('request');

        $router = new Router($config->subpath, '');

        $this->loadPlugins('routing', 'registerEndpoints', function ($endpoints) use ( $router ){
            $router->import($endpoints);
        }); 

        list($todo, $params) = $router->parse($config->defaultEndpoint, $request);
        $try = explode('.', $todo);
        
        if(count($try) !== 3)
        {
            Response::_500('Not correct routing');
        } 

        if(count($params))
        {
            foreach($params as $key => $value)
            {
                $this->set($key, $value);
            }
        }

        try{

            $container->share( 'router', $router, true);
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
            
            return $plgRegister::dispatch($this, $controllerName, $func);

        }
        catch (\Exception $e) 
        {
            Response::_500('[Error] ' . $e->getMessage());
        }
    }

    public function url(string $subpath = '')
    {
        return  $this->getContainer()->get('router')->url($subpath);
    }
}