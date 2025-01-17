<?php
/**
 * SPT software - Base application
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An application integrate plugin engine
 * @version: 0.8
 * 
 */

namespace SPT\Application;
 
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Singleton as Request;
use SPT\Response;
use SPT\Query;
use SPT\Extend\Pdo;
use SPT\Container\IContainer;
use SPT\Application\Plugin\Manager;
use SPT\Support\Loader;

class Base extends ACore implements IApp
{
    protected $plgManager;

    public function __construct(IContainer $container, Configuration $config, string $namespace = 'App')
    {
        $packages =  $config->of('system.packages');
        if(!is_array($packages))
        {
            die('Package is required in the configuration.');
        }
        
        foreach($packages as $path=>$_namespace)
        {
            if(!is_string($path) || !is_string($_namespace) || !file_exists($path))
            {
                die ('Invalid package '. $_namespace);
            }
        }

        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;
        $this->container = $container;
        $this->config = $config;
        $this->request = Request::instance();  
        $this->router = new Router(
            $this->config->of('router.subpath', ''),
            $this->config->of('router.ssl', '')
        );
        
        $this->plgManager = new Manager( $this, $this->config->of('system.packages') );
        $this->set('pluginPaths', $this->plgManager->getPluginPaths());

        $this->container->share('app', $this);
        $this->container->share('config', $this->config);

        return $this;
    }

    public function initialize(?callable $beforePlugin = null, ?callable $afterPlugin = null)
    {
        if( is_callable($beforePlugin))
        {
            $beforePlugin($this);
        }
        
        $this->plgManager->call('all')->run('Bootstrap', 'initialize');

        if( is_callable($afterPlugin))
        {
            $afterPlugin($this);
        }
    }

    public function execute(string | array $parameters = []){}

    public function redirect(string $url, $code = 302)
    {
        Response::redirect($url, $code );
        exit(0);
    }

    public function raiseError(string $msg, $code = 500)
    {
        Response::_($msg, is_numeric($code) ? (int)$code : 500);
        exit(0);
    }

    public function finalize($content)
    {
        Response::_200($content);
        exit(0);
    }

    /**
     * 
     *  SUPPORT PLUGIN ENGINE
     * 
     */

    public function plgLoad(string $event, string $function, $callback = null, bool $getResult = false)
    {
        return $this->plgManager->call('all')->run($event, $function, false, $callback, $getResult);
    }

    public function childLoad(string $event, string $function, $callback = null, bool $getResult = false)
    {
        $plugin = $this->get('mainPlugin', false);
        if(false === $plugin)
        {
            throw new \Exception('Method childLoad can not be called before Routing.'); 
        }

        return $this->plgManager->call($plugin->getId(), 'children')->run($event, $function, false, $callback, $getResult);
    }

    public function familyLoad(string $event, string $function, $callback = null, bool $getResult = false)
    {
        $plugin = $this->get('mainPlugin', false);
        if(false === $plugin)
        {
            throw new \Exception('Method familyLoad can not be called before Routing.'); 
        }

        return $this->plgManager->call($plugin->getId(), 'family')->run($event, $function, false, $callback, $getResult);
    }

    public function plugin($name = '')
    {
        return '' == $name ? $this->get('mainPlugin') : 
                ( true === $name ? 
                    $this->plgManager->getList() : 
                    $this->plgManager->getDetail($name) 
                );
    }

    protected function prepareDispatch(string $pluginName)
    {
        $plugin = $this->plgManager->getDetail($pluginName);

        if(false === $plugin)
        {
            $this->raiseError('Invalid plugin '. $pluginName, 500);
        }
        
        $this->set('mainPlugin', $plugin);
        $list = $plugin->getDependencies();

        // check if package is ready
        // packages must be added in Bootstrap::initialize
        if(isset($list['packages']) && is_array($list['packages']))
        {
            foreach($list['packages'] as $name)
            {
                if( !$container->exists($name) )
                {
                    $this->raiseError('Plugin '. $plugin->getId(). ' requires an instance of '. $namee);
                }
            }
        }

        $loop = [
            'models' =>  '\SPT\Support\Model::containerize',
            'entities' => '\SPT\Support\Entity::containerize',
            'viewmodels' => '\SPT\Support\ViewModel::containerize'
        ];

        foreach($loop as $obj=>$fnc)
        {
            if(isset($list[$obj]) && is_array($list[$obj]))
            {   
                foreach($list[$obj] as $configArr)
                {
                    list($path, $name, $alias) = $configArr;
                    if(file_exists($path))
                    {
                        if(is_dir($path))
                        {
                            Loader::findClass( $path, $name,
                                function($classname, $fullname) use ($fnc) { 
                                    $fnc($classname, $fullname, '');
                                }
                            );
                        }
                        elseif(class_exists($path))
                        {
                            $fnc( $name, $path, $alias );
                        }
                    }
                }
            }
            elseif(!isset($list[$obj]) || false !== $list[$obj])
            {
                Loader::findClass( $plugin->getPath($obj), $plugin->getNamespace('\\'. $obj),
                    function($classname, $fullname) use ($fnc) { 
                        $fnc($classname, $fullname, '');
                    }
                );
            }
        }
    }
}