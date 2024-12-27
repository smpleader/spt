<?php
/**
 * SPT software - Container client
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Base abstract use Container as core
 * 
 */

namespace SPT\Container; 

abstract class Client
{ 
	protected $container;
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
	public function setContainer(IContainer $container)
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

        throw new \RuntimeException('Invalid Container Service '.$name, 500);
    }
}