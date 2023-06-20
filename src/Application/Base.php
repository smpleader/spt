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
use SPT\Response;
use SPT\Container\IContainer;
use SPT\Application\Plugin\Manager;

class Base extends ACore implements IApp
{
    protected $plgManager;

    public function __construct(IContainer $container, string $publicPath, string $pluginPath, string $configPath = '', string $namespace = '')
    {
        if(!file_exists($publicPath) || !file_exists($pluginPath) || !file_exists($configPath))
        {
            die('System path not exists');
        }

        define('SPT_PUBLIC_PATH', $publicPath);
        define('SPT_PLUGIN_PATH', $pluginPath);
        define('SPT_CONFIG_PATH', $configPath);

        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;

        $this->container = $container;
        $this->config = new Configuration(null);
        $this->plgManager = new Manager($this);

        $this->envLoad();
        
        $this->plgManager->call('only-master')->run('Bootstrap', 'initialize', true);
        $this->plgManager->call('none-master')->run('Bootstrap', 'initialize');
        $this->plgManager->call('only-master')->run('Bootstrap', 'afterInitialize');

        return $this;
    }

    protected function envLoad(){}

    public function execute(string $themePath = ''){}

    public function redirect(string $url, $code = 302)
    {
        Response::redirect($url, $code );
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

    public function plgLoad(string $event, string $function, $callback = null, bool $getResult = false)
    {
        return $this->plgManager->call('all')->run($event, $function, false, $callback, $getResult);
    }

    public function childLoad(string $event, string $function, $callback = null, bool $getResult = false)
    {
        $plugin = $this->set('currentPlugin', false);
        if(false === $plugin)
        {
            throw new Exception('Method childLoad can not be called before Routing.'); 
        }

        return $this->plgManager->call($plugin, 'children')->run($event, $function, false, $callback, $getResult);
    }

    public function familyLoad(string $event, string $function, $callback = null, bool $getResult = false)
    {
        $plugin = $this->set('currentPlugin', false);
        if(false === $plugin)
        {
            throw new Exception('Method familyLoad can not be called before Routing.'); 
        }

        return $this->plgManager->call($plugin, 'family')->run($event, $function, false, $callback, $getResult);
    }
}