<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A web application based Symfony container
 * @version: 0.8
 * 
 */

namespace SPT\Application\Symfony;
 
//use  as Router;
use SPT\Request\Base as SPTRequest;
use SPT\Container\Symfony as Container;

use Symfony\Component\Routing;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;

class Web extends \SPT\Application\Base 
{
    protected $slim;
    protected function envLoad()
    {   
        // secrect key
        // terminal or router 
        $this->container = new Container; 
        $this->container->set('app', $this, true);
        // create request
        $this->request = new SPTRequest(); 
        $this->container->set('request', $this->request);
        // create router
        $this->router = new Router($this->config->subpath, '');
        $this->container->set('router', $this->router, true);
        // access to app config 
        $this->container->set('config', $this->config, true);
      
        //$this->container->set('router', 'SPT\Router\ArrayEndpoint')
        //    ->addArgument($this->getContainer()->get('config')->subpath);  
    } 

    private function addEndpoint($slug, $endpoint, $method = 'get')
    {
        $container = $this->getContainer();
        $params = [];

        if(is_array($endpoint))
        {
            if( is_array($endpoint['fnc']))
            {
                foreach($endpoint['fnc'] as $med => $function)
                {
                    $this->addEndpoint($slug, $function, $med);
                }
                
                return;
            }
            else
            {
                $todo =  $endpoint['fnc'];
                unset($endpoint['fnc']);
                if(count($endpoint))
                {
                    $params = $endpoint;
                }
            }
        }
        else
        {
            $todo = $endpoint;
        }

        $app = $this;

        $this->slim->$method($slug, function (Request $request, Response $response) use ($app, $todo, $params) {

            try{

                $try = explode('.', $todo);
        
                if(count($try) !== 3)
                {
                    throw new \Exception('Not correct routing');
                } 

                // support if this home - special deals
                if($app->cn('router')->get('isHome'))
                {
                    $this->plgManager->run(null, 'Routing', 'isHome'); 
                }
                
                if(count($params))
                {
                    foreach($params as $K=>$V)
                    {
                        $this->set($K, $V);
                    }
                }
    
                list($plugin, $controllerName, $func) = $try;
                $plugin = strtolower($plugin);
                $app->set('currentPlugin', $plugin);
                $app->set('controller', $controller);
                $app->set('function', $function);

                return $app->plgManager->run($plugin, 'Dispatcher', 'dispatch', true);
            }
            catch (\Exception $e) 
            {
                $response->getBody()->write(
                    '[Error] ' . $e->getMessage()
                ); 
            }

            return $response;
        });
    }

    public function execute(string $themePath = '')
    {
        // Because Symfony routing is not to distribute a dispatch 
        // we use Slim like laravel case
        //$this->routing = new Symfony\Component\Routing\RouteCollection();
        $this->slim = AppFactory::create(); 
        $this->slim->setBasePath('/'.$this->config->subpath.'/');
        $this->slim->addErrorMiddleware(true, true, true);
       
        if($themePath) $this->set('themePath', $themePath);

        $this->plgManager->run(null, 'routing', 'registerEndpoints', false, function ($endpoints) {

            foreach($endpoints as $slug => $endpoint)
            {
                $this->addEndpoint($slug, $endpoint);
            } 
        });

        $this->plgManager->run('only-master', 'Routing', 'afterRegisterEndpoints'); 

        $this->slim->run();
        return;

        // CASE route add ( no response )
        $routes = new RouteCollection();
        $this->plgManager->run(null, 'routing', 'registerEndpoints', false, function ($endpoints) use ($routes) {

            foreach($endpoints as $slug => $endpoint)
            {
                $routes->add('hello', new Route('/hello/{name}', ['name' => 'World']));

                $this->addEndpoint($slug, $endpoint);
            } 
        });
        
        // CASE 1
        $controllerResolver = new HttpKernel\Controller\ControllerResolver();
        $argumentResolver = new HttpKernel\Controller\ArgumentResolver();

        try {
            $request->attributes->add($matcher->match($request->getPathInfo()));

            $controller = $controllerResolver->getController($request);
            $arguments = $argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (Routing\Exception\ResourceNotFoundException $exception) {
            $response = new Response('Not Found', 404);
        } catch (Exception $exception) {
            $response = new Response('An error occurred', 500);
        }

        // CASE2
        $request = Request::createFromGlobals();
        $routes = include __DIR__.'/../src/app.php';

        $context = new Symfony\Component\Routing\RequestContext();
        $context->fromRequest($request);
        $matcher = new Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

        try {

            $test = $matcher->match($request->getPathInfo());
            die(
                var_dump($test)
            );
            extract($matcher->match($request->getPathInfo()), EXTR_SKIP);
            ob_start();
            include sprintf(__DIR__.'/../src/pages/%s.php', $_route);

            $response = new Response(ob_get_clean());
        } catch (Routing\Exception\ResourceNotFoundException $exception) {
            $response = new Response('Not Found', 404);
        } catch (Exception $exception) {
            $response = new Response('An error occurred', 500);
        }
    }
}