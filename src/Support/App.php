<?php
/**
 * SPT software - Env
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: support to check environment information
 * 
 */

namespace SPT\Support;

use SPT\Application\IApp;
use SPT\Container\IContainer;
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Singleton as Request;

class App
{
    private IApp $_instance;
    private static function checkInstance() 
    {
        if(null === self::$_instance)
        {
            die('SPT Application doesn\'t exists.');
        }
    }

    public static function getInstance() 
    {
        self::checkInstance();
        return self::$_instance;
    }

    public static function createInstance(IContainer $container, string $configPath = '') 
    {
        if(null === self::$_instance)
        {
            $config = new Configuration( $configPath );
            $className =  $config->of('system.application.type', '\SPT\Application\Web');
            try
            {
                if(!class_exists($className))
                {
                    throw('Invalid application type '. $className);
                }
                
                $instance = new $className($container, $config);
            
                if( !($instance instanceof IApp) )
                {
                    throw('Invalid SPT Application');
                }
            }
            catch (\Exception $e) 
            {
                die('Caught Exception: '.  $e->getMessage());
            }

            $instance->initialize(() => {
                
            });
            
            self::$_instance = $instance; 
        }

        return self::$_instance;
    }

    public static function useRequest(IApp $app, ) {
        
    }
}
