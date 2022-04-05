<?php
/**
 * SPT software - Router Sitemap
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to manage routers 
 * 
 */

namespace SPT\Router;

use SPT\BaseObj;
use SPT\Request\Base as Request;
use SPT\Support\FncArray;

class Sitemap extends BaseObj
{
    private $nodes; 
    private $table;

    public function __construct(SitemapEntity $entity, $config, Request $request)
    {
        $this->table = $entity;

        $siteSubpath = $config->exists('sitepath') ? $config->sitepath : '';
        $protocol = $config->exists('protocol') ? $config->protocol : '';

        $p =  ( (!empty($request->server->get('HTTPS', '', 'string')) && $request->server->get('HTTPS', '', 'string') !== 'off') || 
        (!empty($request->server->get('SERVER_PORT', '', 'string')) && $request->server->get('SERVER_PORT', '', 'string') == 443)) ? 'https' : 'http';

        if( empty($protocol) )
        {
            $protocol = $p;
        
        } else{
            
            // force protocol
            if($protocol != $p){
                header('Location: '.$protocol. '://'. $request->server->get('HTTP_HOST', '', 'string') .$request->server->get('REQUEST_URI', '', 'string'));
                exit();
            }
        }

        $protocol .= '://';

        $current = $protocol. $request->server->get('HTTP_HOST', '', 'string') .$request->server->get('REQUEST_URI', '', 'string');
        $this->set('current', $current);

        $more = parse_url( $current );
        foreach( $more as $key => $value)
        {
            $this->set( $key, $value);
        }

        $subPath = trim( $siteSubpath, '/');

        $actualPath = '/'; 
        
        $actualPath = empty($subPath) ? $more['path'] : substr($more['path'], strlen($subPath)+1);
        
        $subPath = empty($subPath) ? '/' : '/'. $subPath .'/';

        $this->set( 'root', $protocol. $request->server->get('HTTP_HOST', '', 'string'). $subPath );

        $this->set( 'actualPath', $actualPath);

        $this->set( 'isHome', ($actualPath == '/' || empty($actualPath)) );
        
    }

    public function pathFinding($default = false, $method)
    {
        $default = $default ? $default : false;

        $sitemap = $this->get('sitemap', []);
        $path = trim($this->get('actualPath'), '/'); 
        $isHome = $this->get('isHome');
        
        $found = $isHome ? $this->table->findOne(['page' => 'home', 'published' => 1])
                     : $this->table->findOne(['slug' => $path, 'published' => 1, 'method' => $method]);

        $this->set('sitenode', $found);
        return ( $found === false ) ? $default : $found;
    }

    public function url($asset = '')
    {
        return $this->get('root'). $asset;
    }

    public function parse($config, $request)
    {
        $defaultEndpoint = $config->exists('defaultEndpoint') ? $config->defaultEndpoint : '';
        $intruction = $this->pathFinding($defaultEndpoint);
        $fnc = '';
        $parameters = [];

        if( is_array($intruction) )
        {
            $fnc = $intruction['fnc'];
            unset($intruction['fnc']); 

            if(isset($intruction['parameters']))
            {
                $request->set('urlVars', $this->parseUrl($intruction['parameters']));
                unset($intruction['parameters']);
            }

            if(count($intruction))
            {
                $parameters = $intruction;
            }
        } 
        elseif( is_string($intruction) ) 
        {
            $fnc = $intruction;
        } 
        else 
        {
            throw new \Exception('Invalid request', 500);
        }

        if(is_array($fnc))
        {
            $method = $request->header->getRequestMethod();
            if(isset($fnc[$method]))
            {
                $fnc = $fnc[$method];
                $parameters['method'] = $method;
            }
            elseif(isset($fnc['any']))
            {
                $fnc = $fnc['any'];
                $parameters['method'] = 'any';
            }
            else
            {
                throw new \Exception('Not a function', 500);
            }
        }

        return [$fnc, $parameters];
    }
}
