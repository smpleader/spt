<?php
/**
 * SPT software - Router
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to route the site based URL
 * 
 */

namespace SPT;

use SPT\StaticObj;

class Router extends StaticObj
{
    static protected $_vars = array();

    /**
     * singleton
     */
    private static $instance;
    public static function _( $sitemap = [], $subpath = '' ){

        if( static::$instance === null )
        {
            static::$instance = new Router();
            static::set('sitemap', array());
            static::$instance->parse($subpath);
        }

        if( is_array($sitemap) && count($sitemap) ) 
        {
            $arr = static::get('sitemap');
            $arr = array_merge($arr, static::flatNodes($sitemap));
            static::set('sitemap', $arr);
        }

        return static::$instance;
    }

    // support nested keys
    private static function flatNodes($sitemap, $parentSlug='')
    {
        $arr = [];
        foreach($sitemap as $key=>$inside)
        {
            if($key == '/' || empty($key))
            {
                if( $parentSlug == '' )
                {
                    static::set('home', $inside);
                }
                else
                {
                    $arr[$parentSlug. $key] = $inside;
                }
            }
            elseif(strpos($key, '/') === 0)
            {
                $arr = array_merge($arr, static::flatNodes($inside, substr($key, 1)));
            }
            else
            {
                if($parentSlug != '')
                {
                    $key = $parentSlug. '/'. $key;
                }
                $arr[$key] = $inside;
            }
        }
        return $arr;
    }
 
    public static function url($asset = ''){
        return static::get('root'). $asset;
    }

    private $nodes;
    //public function __construct(){}

    public function parse( $siteSubpath = '', $protocol = '')
    {
 
        $p =  ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ) ? 'https' : 'http';

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
        static::set('current', $current);

        $more = parse_url( $current );
        foreach( $more as $key => $value)
        {
            static::set( $key, $value);
        }

        $subPath = trim( $siteSubpath, '/');

        $actualPath = '/'; 
        
        $actualPath = empty($subPath) ? $more['path'] : substr($more['path'], strlen($subPath)+1);
        
        $subPath = empty($subPath) ? '/' : '/'. $subPath .'/';

        static::set( 'root', $protocol. $_SERVER['HTTP_HOST']. $subPath );

        static::set( 'actualPath', $actualPath);

        static::set( 'isHome', ($actualPath == '/' || empty($actualPath)) );

        return;
    }

    public function pathFinding( $default, $callback = null)
    {
        $sitemap = static::get('sitemap');
        $path = static::get('actualPath');
        $isHome = static::get('isHome');
        static::set('sitenode', '');
        
        if($isHome){
            $found = static::get('home', '');
            if( $found === '')
            {
                $found = $default;
            }
            else
            {
                static::set('sitenode', '/');
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
        else 
        {
            foreach( $sitemap as $reg=>$value )
            {
                //$reg = str_replace( ['-'], ['\-'], $reg) ;
                if (preg_match ('#'. $reg. '#i', $path, $matches))
                {
                    if( !is_array($value) || isset($value['fnc']))
                    {
                        $found = $value;
                        static::set('sitenode', $reg);
                        break;
                    }
                }
            }
        }

        return ( $found === false ) ? $default : $found;
    }

    public function praseUrl(array $parameters)
    {
        $slugs = trim(static::get('actualPath', ''), '/');
        $sitenote = static::get('sitenode', '');
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
