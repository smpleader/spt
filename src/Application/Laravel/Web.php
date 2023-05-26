<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A web application based Laravel container
 * @version: 0.8
 * 
 */

namespace SPT\Application\Laravel;
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
 
use SPT\Container\Laravel as Container;
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Base as SPTRequest;

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
    }
    
    private function addEndpoint($slug, $endpoint, $method = 'get')
    {
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
                    $this->plgLoad('routing', 'isHome'); 
                }
                
                if(count($params))
                {
                    foreach($params as $K=>$V)
                    {
                        $this->set($K, $V);
                    }
                }
    
                list($plugin, $controller, $function) = $try;
                $plugin = strtolower($plugin);
                $app->set('currentPlugin', $plugin);

                return $app->plgDispatch($controller, $function);
     
               // $response->getBody()->write(  $sth  );
    
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
        $this->slim = AppFactory::create(); 
        $this->slim->setBasePath('/'.$this->config->subpath.'/');
        $this->slim->addErrorMiddleware(true, true, true);

        if($themePath) $this->set('themePath', $themePath);

        $this->plgLoad('routing', 'registerEndpoints', function ($endpoints) {

            foreach($endpoints as $slug => $endpoint)
            {
                $this->addEndpoint($slug, $endpoint);
            } 
        }); 

        if($masterPlg = $this->config->master)
        {
            $this->plgRun($masterPlg, 'Routing', 'afterRegisterEndpoints');
        }

        $this->slim->run();
    }
}