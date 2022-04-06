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

use SPT\BaseObj;
use SPT\Support\FncArray;

class Base extends BaseObj
{
    protected $nodes; 

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
    public function flatNodes($sitemap, $parentSlug='')
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
}
