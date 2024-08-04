<?php
/**
 * SPT software - Router
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to route the site based URL from an array of endpoints
 * 
 */

namespace SPT\Router;

use SPT\Support\FncArray;
use SPT\Support\Filter;
use SPT\Application\IRouter;

class ArrayEndpoint extends Base implements IRouter
{
    protected $nodes;
    protected array $_slugs;

    public function __construct(string $siteSubpath = '', string $protocol = '')
    {
        $p =  ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 ) ) ? 'https' : 'http';

        if( empty($protocol) )
        {
            $protocol = $p;
        
        } else{
            
            // force protocol
            if($protocol != $p){
                header('Location: '.$protocol. '://'. $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI']);
                exit();
            }
        }

        $protocol .= '://';

        $current = $protocol. $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
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

        $this->set( 'root', $protocol. $_SERVER['HTTP_HOST']. $subPath );

        $this->set( 'actualPath', $actualPath);

        $this->set( 'isHome', ($actualPath == '/' || empty($actualPath)) );

    }

    public function pathFinding( $default = false, $callback = null)
    {
        $sitemap = $this->get('sitemap', []);
        $path = rtrim($this->get('actualPath'), '/'); // because of preg_match
        $isHome = $this->get('isHome');
        $this->set('sitenode', '');
        if(empty($default) && isset($sitemap[0]))
        {
            $default = $sitemap[0];
        }
        
        if($isHome){
            $found = $this->get('home', '');
            if( $found === '')
            {
                $found = $default;
            }
            else
            {
                $this->set('sitenode', '/');
            }
            return $found;
        }

        if( isset($sitemap[$path]) )
        {
            $this->set('sitenode', $path);
            return $sitemap[$path];
        }
        
        $found = false;

        if( is_callable($callback))
        {
            $found = $callback($sitemap, $path);
        } 
        elseif(FncArray::isReady($sitemap)) 
        {
            foreach( $sitemap as $reg=>$value )
            {
                if (preg_match ('#^'. $reg. '#i', $path, $matches))
                {
                    $found = $value;
                    $this->set('sitenode', $reg);
                    break;
                }
            }
        }

        return $found;
        return ( $found === false ) ? $default : $found;
    }

    public function parseUrl(array $parameters)
    {
        $slugs = $this->get('actualPath', '');
        $sitenote = $this->get('sitenode', '');

        if( $slugs > $sitenote )
        {
            $slugs = trim(substr($slugs, strlen($sitenote)), '/');
            $values = explode('/', $slugs);
        }
        else
        {
            $values = [];
        }
        
        $vars = [];
        foreach($parameters as $index => $name)
        {
            $vars[$name] = isset($values[$index]) ? $values[$index] : null;
        }

        return $vars;
    }

    public function parse($request)
    {
        $intruction = $this->pathFinding();
        $fnc = '';
        $parameters = [];

        if( is_array($intruction) )
        {
            $fnc = $intruction['fnc'];
            unset($intruction['fnc']); 

            if(isset($intruction['parameters']))
            {
                $this->_slugs = $this->parseUrl($intruction['parameters']);
                $request->set('urlVars', $this->_slugs); // support old version
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
            return false;
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

    public function slug($name, $format=null)
    {
        $value = isset($this->_slugs[$name]) ? $this->_slugs[$name] : null;
        return null === $format ? $value : Filter::{$format}($value);
    }
}
