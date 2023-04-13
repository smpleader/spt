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
    protected IRouter $router;
    protected $pluginPath;
	protected $psr11;

    public function __construct(string $publicPath, string $pluginPath, string $configPath = '', string $namespace = '')
    {
        define('SPT_PUBLIC_PATH', $publicPath);
        define('SPT_PLUGIN_PATH', $pluginPath);

        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;
        $this->pluginPath = $pluginPath;
        $this->psr11 = false;

        $this->cfgLoad($configPath); 
        $this->prepareEnvironment();
        $this->plgLoad('bootstrap', 'initialize');

        return $this;
    }

    public function supportContainer()
    {
        return $this->psr11;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getPluginPath()
    {
        return $this->pluginPath. '/';
    }

    protected function prepareEnvironment(){ }

    protected $config;
    public function cfgLoad(string $configPath = '')
    {
        if( file_exists( $configPath) )
        {
            $try = require_once($configPath);
            if(is_array($try) || is_object($try))
            {
                foreach($try as $K=>$V) $this->set($K, $V);
            }
        }
    }

    public function plgLoad(string $event, string $execute, $closure = null)
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

    public function redirect(string $url, $redirectStatus = 302)
    {
        Response::redirect($url, $redirectStatus );
        exit(0);
    }

    public function raiseError(string $msg, $code = 500)
    {
        Response::_($msg, $code);
        exit(0);
    }

    public function finalize(string $content)
    {
        Response::_200($content);
        exit(0);
    }
}