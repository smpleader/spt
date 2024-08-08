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
use SPT\Query;
use SPT\Extend\Pdo;
use SPT\Container\IContainer;
use SPT\Application\Plugin\Manager;

class Base extends ACore implements IApp
{
    protected $plgManager;
    protected $packages;

    public function __construct(IContainer $container, Configuration $config, string $namespace = 'App')
    {
        $config->exists('packages') or  die('System path not exists');
        
        $_pkg = $config->packages instanceof \SPT\MagicObj ?  $config->packages->toArray() : (array) $config->packages;
        foreach($_pkg as $path=>$namespace)
        {
            if(!file_exists($path)) die ('Invalid package '. $namespace);
        }

        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;
        $this->packages = $_pkg;
        $this->container = $container;
        $this->config = $config;

        $this->envLoad();

        return $this;
    }

    protected function envLoad()
    {
        $this->plgManager = new Manager( $this, $this->packages );
        $this->plgManager->call('all')->run('Bootstrap', 'initialize');
        $this->plgManager->call('all')->run('Bootstrap', 'afterInitialize');
    }

    public function useDatabase($db='database.mysql')
    {
        $container = $this->getContainer();

        if( !$this->container->exists('query') )
        {
            
            $pdo = new Pdo( $this->config->of($db) );
            if(!$pdo->connected)
            {
                $this->raiseError('Connection failed.', 'DatabaseConnectFailed'); 
            }

            $prefix = $this->config->exists($db.'.prefix') ? ['#__' => $this->config->of($db.'.prefix') ] : [];

            $query = new Query( $pdo, $prefix);
            $this->container->set('query', $query);
        }
    }

    public function useSPTCore()
    {
        // use SPT request
        $this->request = Request::instance(); 
        if( !$this->container->exists('request') )
        {
            $this->container->set('request', $this->request);
        }

        // use SPT router
        $subPath = $this->config->exists('subpath') ? $this->config->subpath : '';
        $this->router = new Router($subPath, '');
        if( !$this->container->exists('router') )
        {
            $this->container->set('router', $this->router);
        }

        // use SPT config 
        if( !$this->container->exists('config') )
        {
            $this->container->set('config', $this->config);
        }

        // use token
        if( !$this->container->exists('token') )
        {
            $this->container->set('token', new Token($this->config, $this->request));
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

        return $this->plgManager->call($plugin['name'], 'children')->run($event, $function, false, $callback, $getResult);
    }

    public function familyLoad(string $event, string $function, $callback = null, bool $getResult = false)
    {
        $plugin = $this->get('mainPlugin', false);
        if(false === $plugin)
        {
            throw new \Exception('Method familyLoad can not be called before Routing.'); 
        }

        return $this->plgManager->call($plugin['name'], 'family')->run($event, $function, false, $callback, $getResult);
    }

    public function plugin($name = '')
    {
        return '' == $name ? $this->get('mainPlugin') : 
                ( true === $name ? 
                    $this->plgManager->getList() : 
                    $this->plgManager->getDetail($name) 
                );
    }

    /**
     * 
     *  SUPPORT MVVM ENGINE
     * 
     */

    protected array $vmClasses;
    public function getVMList(string $plgName)
    {
        return isset($this->vmClasses[$plgName]) ? $this->vmClasses[$plgName] : [];
    }

    public function addVM(string $plgName, string $name, string $fullName)
    {
        $this->vmClasses[$plgName][] = [$name, $fullName];
    }
}