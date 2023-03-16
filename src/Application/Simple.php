<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An application context
 * 
 */

namespace SPT\Application;
 
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Base as Request;
use SPT\Response;

class Simple implements IApp
{
    private $namespace;
    private $request;
    private $router;
    private $pluginPath;

    public function __construct(string $pluginPath, string $configPath = '', string $namespace = '')
    {
        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;
        $this->pluginPath = $pluginPath;

        $this->loadConfig($configPath); 
        $this->prepareEnvironment();
        $this->loadPlugins('bootstrap', 'initialize');
        return $this;
    }

    private function prepareEnvironment()
    {
        // secrect key
        // terminal or router
        $this->request = new Request();
        // setup container
    }

    private $endpoints = [];
    public function registerEndpoints(array $endpoints)
    {
        $this->endpoints = array_merge($this->endpoints, $endpoints);
    }

    private $config;
    public function loadConfig(string $configPath = '')
    {
        if( !empty( $configPath) )
        {
            $try = require_once($configPath);
            foreach($try as $K=>$V) $this->set($K, $V);
        }
    }

    public function loadPlugins(string $event, string $execute, $closure = null)
    {
        $event = ucfirst(strtolower($event));
        foreach(new \DirectoryIterator($this->pluginPath) as $item) 
        {
            if (!$item->isDot() && $item->isDir()) 
            { 
                $plgRegister = $this->namespace. '\\'. $item->getBasename(). '\\registeres\\'. $event; // $item->getFilename();
                if(class_exists($plgRegister) && method_exists($plgRegister, $execute))
                {
                    $result = $plgRegister::$execute($this);
                    if(null !== $closure && is_callable($closure))
                    {
                        $ok = $closure( $result );
                        if(false === $ok)
                        {
                            die('Got an issue with plugin '. $item->getBasename(). ' when call '. $event .'.' . $execute);
                        }
                    }
                }
            }
        }
    }

    // loval variables
    private array $_vars = [];
    public function get($key, $default = null)
    {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : $default;
    }

    // Just set once
    public function set($key, $value)
    {
        if(!isset($this->_vars[$key]))
        {
            $this->_vars[$key] = $value;
        }
    }

    public function executeCommandLine(string $themePath = '')
    {
        if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			exit(0);
		}

        // load CommandLine to start the work
        $this->loadPlugins('cli', 'registerCommands');
    }

    public function runWebApp(string $themePath = '')
    {
        $router = new Router($this->get('subpath', ''));

        $this->loadPlugins('routing', 'registerEndpoints', function ($endpoints) use ( $router ){
            $router->import($endpoints);
        });

        list($todo, $params) = $router->parse($this->get('defaultEndpoint', false), $this->request);

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

            $this->router = $router;

            if(!$themePath)
            {
                $this->set('themePath', $themePath);
            }

            list($plugin, $controllerName, $func) = $try;
            $plugin = strtolower($plugin);

            $plgRegister = $this->namespace. '\\'. $plugin. '\\registers\\Dispatcher';
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
}