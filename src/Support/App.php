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
use SPT\Application\Token as AppToken;
use SPT\Web\Controller;

class App
{
    private static IApp $_instance;
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

    public static function createInstance(IContainer $container, string $configPath = '', $beforeInit = 1, $afterInit = null) 
    {
        if( !isset( self::$_instance) )
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

                if(1 === $beforeInit)
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
        $foundConfig = false;
        $config = $appConfig->of('system.boot');

        if($config instanceof MagicObj)
        {
            $foundConfig = true;
            $useRequest = $config->get('request', true);
            $useRouter = $config->get('router', true);
            $useToken = $config->get('token', true);
            $useQuery = $config->get('query', false);
            $useSession = $config->get('session', false);
            $database = $config->get('database', false);
        }
        elseif( is_array($config) )
        {
            $foundConfig = true;
            $useRequest = $config['request'] ?? true;
            $useRouter = $config['router'] ?? true;
            $useToken = $config['token'] ?? true;
            $useQuery = $config['query'] ?? false;
            $useSession = $config['session'] ?? false;
            $database = $config['database'] ?? false;
        }

        if($foundConfig)
        {
            if( $useRequest && !$container->exists('request') )
            {
                $container->set('request', $ins->getRequest());
            }
    
            if( $useRouter &&  !$container->exists('router') )
            {
                $container->set('router', $ins->getRouter());
            }
    
            if( $useToken &&  !$container->exists('token') )
            {
                $container->set('token', new AppToken($appConfig, $ins->getRequest()));
            }
    
            if( $useQuery && is_array($database) &&  !$container->exists('query') )
            {
                $pdo = new Pdo( $database );
                if(!$pdo->connected)
                {
                    die('Connection failed.'); 
                }

                $prefix = $database['prefix'] ?? [];
                if(is_string($prefix)) $prefix = ['#__' => $prefix];
    
                $query = new Query( $pdo, $prefix);
                $container->set('query', $query);
    
                if( $useSession && !$container->exists('session') )
                {
                    if( $useToken )
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
                    else
                    {
                        $container->set('session', new Session( new PhpSession()));
                    }
                }
            }
            else
            {
                if( $useSession  &&  !$container->exists('session') )
                {
                    $container->set('session', new Session( new PhpSession()));
                }
            }
        }
    }

    public static function createController(string $className, $pluginName = ''): Controller
    {
        $app = self::getInstance();
        if(!class_exists($className))
        {
            $app->raiseError('Invalid controller '. $className);
        }

        $controller = new $controller($app->getContainer());

        if(!($controller instanceof Controller))
        {
            $app->raiseError('Prohibited controller '. $className);
        }

        $plugin = $app->plugin($pluginName);
        if(false === $plugin)
        {
            $app->raiseError(
                empty($pluginName) ? 'Invalid main plugin' : 'Invalid plugin '.$pluginName
            );
        }
        
        $app->set('currentPlugin', $plugin['name']);
        $app->set('namespace', $plugin['namespace']);
        $app->set('pluginPath', $plugin['path']);

        return $controller;
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
