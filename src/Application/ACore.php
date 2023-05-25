<?php
/**
 * SPT software - Abstract application
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All basic elements of an application
 * @version: 0.8
 * 
 */

namespace SPT\Application;
 
use SPT\Router\ArrayEndpoint as Router;
use SPT\Response;
use SPT\Container\IContainer;
use SPT\Request\Base as Request;
use SPT\MagicObj;

abstract class ACore
{
    protected $namespace;
    public function getNamespace()
    {
        return $this->namespace;
    }

    // -- Router --
    protected IRouter $router; 
    public function getRouter()
    {
        return $this->router;
    }

    // -- Container --
	protected IContainer $container; 
    public function getContainer()
    {
        return $this->container;
    }

    // -- Request --
	protected Request $request; 
    public function getRequest()
    {
        return $this->request;
    }

    // -- Config --
	protected MagicObj $config; 
    public function getConfig()
    {
        return $this->config;
    }

    // -- Variables --
    protected array $_vars = [];
    public function get($key, $default = null)
    {
        return isset($this->_vars[$key]) ? $this->_vars[$key] : $default;
    }

    // -- Variables :: Just set once
    public function set($key, $value)
    {
        if(!isset($this->_vars[$key]))
        {
            $this->_vars[$key] = $value;
        }
    }

    // -- Shortcut :: no factory pattern
    public function rt(string $path = '////')
    {
        return $path !== '////' ? $this->router->url($path) : $this->router;
    }

    public function cn(string $name)
    {
        return $this->container->get($name);
    }

    public function cf(string $name = '')
    {
        return $name == '' ? $this->config : $this->config->{$name};
    }

    public function rq(string $method = '')
    {
        return  $method == '' ? $this->request : $this->request->{$method};
    }
}