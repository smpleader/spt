<?php
/**
 * SPT software - Theme
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to create theme engine
 * 
 */

namespace SPT;

class Theme extends StaticObj
{
    static protected $_vars = array();

    public static function add(string $link, $dependencies = array(), $id = '', $group = '')
    {
        if(empty($dependencies)) $dependencies = array();
        else  $dependencies = (array) $dependencies;
        $asset = new Asset($link, $dependencies);
        $type = $asset->get('type');

        if(!empty($id))
        {
            $asset->set('id', $id );
        }
        $id = $asset->get('id');

        $key = $group ? $group. ucfirst($type) : $type;

        static::importArr([
            $key => [ $id => $asset ]
        ]);
    }

    public static function addInline(string $type, string $lines)
    {   
        $key = 'inline'. Util::uc($type);
        static::$_vars[$key][] = $lines;
    }

    public static function echo($type)
    {
        echo implode("\n", static::generate($type));
    }

    public static function generate($type)
    {
        $output = []; 

        if( 0 === strpos($type, 'inline') )
        {
            $tag = '';
            $output = static::get($type);

            if(!is_array($output))
            {
                return [];
            }
            
            switch($type)
            {
                case 'inlineCss':
                case 'inlineStyle':
                    $tag = 'style'; 
                break;
                case 'inlineJs': 
                case 'inlineJavascript':
                    $tag = 'script';
                break;
            }

            array_unshift($output, '<'.$tag .'>');
            array_push($output, '</'.$tag.'>');
        }
        else
        {
            $assets = static::get($type);
            if( is_array($assets) && count($assets) )
            {
                foreach($assets as $id => $asset)
                {
                    static::createLink($output, $asset->get('type'), $id, $assets);
                }
            }
        }

        return $output; 

    }

    protected static function createLink(&$result, $type, $id, &$assets)
    {
        if(!isset($assets[$id]))
        {
            $result[] = '<!-- '.$type. ' '. $id.' not found -->';
        }
        else
        {
            $asset = $assets[$id];

            if( !$assets[$id]->get('added', 0) )
            {
                if( count($asset->get('parents') ) )
                {
                    foreach($asset->get('parents') as $pid)
                    {
                        static::createLink($result, $type, $pid, $assets);
                    }
                }
    
                switch($type)
                {
                    case 'css':
                        $result[] = '<link rel="stylesheet" type="text/css" href="'. $asset->get('url'). '" >';
                    break;
                    case 'js':
                        $result[] = '<script src="'. $asset->get('url'). '" ></script>';
                    break;
                }
    
                $assets[$id]->set('added', 1); 
            }
        }
    }

    public static function createPage($page='index', $data = array())
    {
        include $page. '.php';

        /**
         *  TODO: use structure define as default.html to generate a page
         *  $tags = [ 'content', 'css', 'js', 'widget' ]
         */
    }

    public static function echoWidget($name, $data = array())
    {
        if(file_exists($name. '.php'))
        {
            ob_start();
            include $name. '.php';
            echo  ob_get_clean();
        }
    }

}
