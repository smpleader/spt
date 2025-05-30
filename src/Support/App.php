<?php
/**
 * SPT software - Support/App
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: comfortable way to use Application
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
use SPT\StaticObj;
use SPT\DynamicObj;
use SPT\Support\Loader;
use SPT\Extend\Pdo;
use SPT\Session\Instance as Session;
use SPT\Session\PhpSession;
use SPT\Session\DatabaseSession;
use SPT\Storage\DB\Session as SessionEntity;
use SPT\Application\Token as AppToken;
use SPT\Web\Controller;

class App extends StaticObj
{
    private static IApp $_instance;
    private static array $_vars;
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

    public static function createInstance(IContainer $container, string $configPath = '', string $applicationType = 'system.application.web', $beforeInit = 1, $afterInit = null) 
    {
        if( !isset( self::$_instance) )
        {
            $config = new Configuration( $configPath );
            $className =  $config->of($applicationType, '\SPT\Application\Web');
            try
            {
                if(class_exists($className))
                {
                    $reflected = new \ReflectionClass( $className );
                    if(!$reflected->isSubclassOf( '\SPT\Application\Base' ))
                    {
                        throw($className.' must be extended from \SPT\Application\Base');
                    }
                }
                else
                {
                    throw('Invalid application type '. $className);
                }
                
                $instance = new $className($container, $config);
                
                // assign here to support function intialize() called instance itself
                self::$_instance = &$instance; 
            
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
            
        }

        return self::$_instance;
    }

    public static function defaultInit(IApp $ins) 
    { 
        $container = $ins->getContainer();
        $appConfig = $ins->getConfig();
        $foundConfig = false;
        $config = $appConfig->of('system.boot');

        if($config instanceof DynamicObj)
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

        $controller = new $className($app->getContainer());

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
        
        $app->set('currentPlugin', $plugin->getId());
        $app->set('namespace', $plugin->getNamespace());
        $app->set('pluginPath', $plugin->getPath());

        return $controller;
    }
}
