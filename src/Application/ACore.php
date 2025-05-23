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
use SPT\Traits\ObjectHasInternalData;

abstract class ACore
{
    use ObjectHasInternalData;
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
	protected Configuration $config; 
    public function getConfig()
    {
        return $this->config;
    }

    // -- Shortcut :: no factory pattern
    public function _(string $name)
    {
        return $this->container->get($name);
    }

    // TODO: consider a better way 
    public function any(string $key, string $keyInConfig = '', $default = null)
    {
        if(isset($this->_vars[$key]))
        {
            return $this->_vars[$key];
        }

        if(!$keyInConfig) return $default;

        return $this->config->of($keyInConfig, $default);
    }

    public function input($key, string $method = '')
    {
        return $method == '' ? $this->request->get($key) : $this->request->{$method}->get($key);
    }
}