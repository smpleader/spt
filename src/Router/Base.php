<?php
/**
 * SPT software - Router
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to route the site based URL
 * 
 */

namespace SPT\Router;

use SPT\Support\FncArray;

class Base extends BaseObj
{
    protected $nodes;

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

    public function import(array $sitemap = [])
    {
        if( count($sitemap) ) 
        {
            $arr = $this->get('sitemap', []);
            $arr = array_merge($arr, $this->flatNodes($sitemap));
            $this->set('sitemap', $arr);
        }
    }

    // support nested keys
    protected function flatNodes($sitemap, $parentSlug='')
    {
        $arr = [];
        foreach($sitemap as $key => $inside)
        {
            if(empty($key)) $key = '/';
            elseif (strpos($key, '/') !== 0 && empty($parentSlug)) 
            {
                $key = '/'. $key;
            }

            if($key == '/' )
            {
                if( $parentSlug == '' )
                {
                    $this->set('home', $inside);
                }
                else
                {
                    $arr[$parentSlug. $key] = $inside;
                }
            }
            else
            {
                if(!empty($parentSlug)) $key = $parentSlug. '/'. $key;
                if(is_array($inside))
                { 
                    if(isset($inside['fnc']))
                    {
                        $arr[$key] = $inside;
                    }
                    else
                    {
                        $arr = array_merge($arr, $this->flatNodes($inside, $key ));
                    }
                }
                else
                {
                    $arr[$key] = $inside;
                }
            }
        }
        krsort($arr);


        return $arr;
    }
 
    public function url($asset = '')
    {
        return $this->get('root'). $asset;
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
}
