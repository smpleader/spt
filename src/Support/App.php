<?php
/**
 * SPT software - Env
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: support to check environment information
 * 
 * Configuration keys:
 * - system.application.type: instance type of IApp
 * - system.boot: components of application be loaded
 * 
 */

namespace SPT\Support;

use SPT\Application\Configuration;
use SPT\Application\IApp;
use SPT\Container\IContainer;
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Singleton as Request;
use SPT\Response;
use SPT\Query;
use SPT\Support\Loader;
use SPT\Extend\Pdo;
use SPT\Session\Instance as Session;
use SPT\Session\PhpSession;
use SPT\Session\DatabaseSession;
use SPT\Storage\DB\Session as SessionEntity;

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

    public static function createInstance(IContainer $container, string $configPath = '', $beforeInit = null, $afterInit = null) 
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
                    throw new \Exception('Invalid SPT Application');
                }

                if(null === $beforeInit && null === $afterInit)
                {
                    $beforeInit = ['\SPT\Support\App', 'defaultInit'];
                }
                elseif(1 === $afterInit)
                {
                    $afterInit = ['\SPT\Support\App', 'defaultInit'];
                }
                    
                $instance->initialize( $beforeInit, $afterInit );
            }
            catch (\Exception $e) 
            {
                die('Caught Exception: '.  $e->getMessage());
            }
            
            self::$_instance = $instance; 
        }

        return self::$_instance;
    }

    public static function defaultInit(IApp $ins) 
    { 
        $container = $ins->getContainer();
        $appConfig = $ins->getConfig();
        $config = $appConfig->of('system.boot');

        if($config instanceof MagicObj)
        {
            if( $config->get('request', true) && !$container->exists('request') )
            {
                $container->set('request', $ins->getRequest());
            }
    
            if( $config->get('router', true) &&  !$container->exists('router') )
            {
                $container->set('router', $ins->getRouter());
            }
    
            if( !$container->exists('config') )
            {
                $container->set('config', $appConfig);
            }
    
            if( $config->get('token', true) &&  !$container->exists('token') )
            {
                $container->set('token', new Token($config, $ins->getRequest()));
            }
    
            if( $config->get('query', false) &&
                $config->get('database', false) &&  
                !$container->exists('query') )
            {
                $database = $config->get('database');
                $pdo = new Pdo(  );
                if(!$pdo->connected)
                {
                    die('Connection failed.'); 
                }

                $prefix = isset($database['prefix']) ? $database['prefix'] : [];
    
                $query = new Query( $pdo, $prefix);
                $this->container->set('query', $query);
    
                if( $config->get('session', false) &&
                    !$container->exists('session') )
                {
                    $container->set('session', 
                        new Session( 
                            new DatabaseSession( 
                                new SessionEntity($container->get('query')),
                                $container->get('token')->value()
                            )
                        )
                    );
                }
            }
            else
            {
                if( $config->get('session', false) &&
                    !$container->exists('session') )
                {
                    $container->set('session', new Session( new PhpSession()));
                }
            }
        }
    }

    public static function addModel(string $path, string $namespace)
    {
        $app = self::getInstance();
        $container = $app->getContainer();
        Loader::findClass( 
            $path, 
            $namespace, 
            function($classname, $fullname) use ($container)
            { 
                if( !$container->exists($classname) && class_exists($fullname) )
                {
                    $container->share( $classname. 'Model', new $fullname($container), true);
                    //$container->alias( $alias, $fullname);
                }
            }
        );
    }

    public static function addEntity(string $path, string $namespace)
    {
        $app = self::getInstance();
        $container = $app->getContainer();
        Loader::findClass( 
            $path, 
            $namespace, 
            function($classname, $fullname) use ($container)
            { 
                if( !$container->exists($classname) && class_exists($fullname) )
                {
                    $container->share( $classname. 'Entity', new $fullname($container->get('query')), true);
                }
            }
        );
    }

    public static function addViewModel(string $path, string $namespace): array
    {
        // Todo: add widget by a config instead of autoload
        $app = self::getInstance();
        $vmList = [];
        $container = $app->getContainer();
        Loader::findClass( 
            $path, 
            $namespace, 
            function($classname, $fullname) use ($container, &$vmList)
            { 
                if( !$container->exists($classname) && class_exists($fullname) )
                {
                    $container->share( $classname. 'Entity', new $fullname($container->get('query')), true);
                }
            }
        );

        return $vmList;
        // Usage:
        $controller->registerViewModels($vmList); 
    }
}
