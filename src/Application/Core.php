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
	protected $container;

    public function __construct(string $publicPath, string $pluginPath, string $configPath = '', string $namespace = '')
    {
        define('SPT_PUBLIC_PATH', $publicPath);
        define('SPT_PLUGIN_PATH', $pluginPath);

        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;

        $this->cfgLoad($configPath); 
        $this->prepareEnvironment();
        $this->pluginsBootstrap();

        return $this;
    }

    protected function pluginsBootstrap()
    {
        $config = $this->getContainer() ? $this->getContainer()->get('config') : $this->getConfig();
        if($config->master)
        {
            $this->pluginBackbone($config->master, 'Bootstrap', 'initialize', true);
        }

        foreach(new \DirectoryIterator(SPT_PLUGIN_PATH) as $item) 
        {
            if (!$item->isDot() && $item->isDir() && ($item->getBasename() !== $config->master))
            { 
                $this->pluginBackbone($item->getBasename(), 'Bootstrap', 'initialize');
            }
        }

        if($config->master)
        {
            $this->pluginBackbone($config->master, 'Bootstrap', 'afterInitialize');
        }
    }

    protected function pluginBackbone(string $plgName, string $event, string $fnc, bool $required=false)
    {
        $event = ucfirst(strtolower($event));
        $plgRegister = $this->namespace. '\\plugins\\'. $plgName. '\\registers\\'.$event;
        if(!class_exists($plgRegister) || !method_exists($plgRegister, $fnc))
        {
            if(!$required) return;
            $this->raiseError('Invalid Plugin '. $plgName. ' with '. $event. '.'. $fnc);
        }

        if(false === $plgRegister::$fnc($this))
        {
            $this->raiseError($plgRegister::getMessage());
        }
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getRouter()
    {
        return $this->router;
    }

    protected function prepareEnvironment(){ }

    public function cfgLoad(string $configPath = ''){}

    public function plgLoad(string $event, string $execute, $closure = null)
    {
        $event = ucfirst(strtolower($event));
        foreach(new \DirectoryIterator(SPT_PLUGIN_PATH) as $item) 
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
                            $this->raiseError('Got an issue with plugin '. $item->getBasename(). ' when call '. $event .'.' . $execute);
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

    public function finalize($content)
    {
        Response::_200($content);
        exit(0);
    }
}