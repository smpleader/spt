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
use SPT\Response;

class Core implements IApp
{
    protected $namespace;
    protected $router;
    protected $pluginPath;

    public function __construct(string $pluginPath, string $configPath = '', string $namespace = '')
    {
        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;
        $this->pluginPath = $pluginPath;

        $this->loadConfig($configPath); 
        $this->prepareEnvironment();
        $this->loadPlugins('bootstrap', 'initialize');
        return $this;
    }

    public function getPluginPath()
    {
        return $this->pluginPath. '/';
    }

    public function getCurrentPluginPath()
    {
        return $this->pluginPath. '/'. $this->get('currentPlugin', '');
    }

    protected function prepareEnvironment(){ }

    protected $config;
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
                $plgRegister = $this->namespace. '\\plugins\\'. $item->getBasename(). '\\registers\\'. $event; // $item->getFilename();
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
    protected array $_vars = [];
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

    public function execute(string $themePath = ''){ }
    public function url(string $subpath = ''){ return '-- current url --'; }
}