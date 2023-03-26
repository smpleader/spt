<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Base abstract implement Container
 * 
 */

namespace SPT\Container; 

abstract class Client
{ 
	private $container;
    public function __construct(IContainer $container)
    {
        $this->setContainer($container);
    }

	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @since   1.2
	 *
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public function getContainer()
	{
		if ($this->container)
		{
			return $this->container;
		}

		throw new \UnexpectedValueException('Container not set in ' . __CLASS__);
	}

	/**
	 * Set the DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  mixed  Returns itself to support chaining.
	 *
	 * @since   1.2
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;

		return $this;
	}

    /**
     * Load automatically dependencies
     */
    public function __get($name)
    { 
        if( 'container' == $name ) return $this->container;
        
        if( $this->container->exists($name) )
        {
            return $this->container->get($name);
        }

        throw new \RuntimeException('Invalid JDIContainer '.$name, 500);
    }

    protected $_vars = []; 

    public function get($key, $default = null)
    {
        return isset( $this->_vars[$key] ) ? $this->_vars[$key] : $default; 
    }

    public function set($key, $value)
    {
        $this->_vars[$key] = $value;
    }

    public function getAll()
    {
        return $this->_vars;
    }
}