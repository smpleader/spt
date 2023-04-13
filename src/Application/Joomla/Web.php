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
        $this->cfgLoad($configPath); 
        $this->prepareEnvironment();
        $this->plgLoad('bootstrap', 'initialize');
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

    public function cfgLoad(string $configPath = '')
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

        $this->plgLoad('routing', 'registerEndpoints', function ($endpoints) use ( $router ){
            $router->import($endpoints);
        }); 

        $try = $router->parse($this->request);
        if(false === $try)
        {
            $this->raiseError('Invalid request', 500);
        }

        list($todo, $params) = $try;
        $try = explode('.', $todo);
        
        if(count($try) !== 3)
        {
            $this->raiseError('Not correct routing', 500);
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
            $this->raiseError('[Error] ' . $e->getMessage(), 500);
        }
    }
}