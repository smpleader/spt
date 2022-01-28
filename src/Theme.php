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

use SPT\Support\FncString;
use SPT\Support\FncArray;

class Theme extends BaseObj
{
    protected $themePath = '';
    protected $overrideLayouts = [];
    protected $_body = '';
    protected $_assets = [];

    public function __construct(string $themePath, array $overrideLayouts)
    {
        $this->themePath = $themePath;
        $this->overrideLayouts = $overrideLayouts;


        //defined('THEME_PATH') || throw new Exception('Invalid Theme Constant');

        //$this->setContainer($container);
        //$config = $container->get('config');
        //$theme = $config->theme;

        //define('THEME_PATH', APP_PATH. 'views/themes/'. $theme. '/' );
        $this->registerAssets();
    }

    public function getPath()
    {
        return $this->themePath;
    }

    public function registerAssets(string $profile = '', array $list = [])
    {
        if( '' === $profile )
        {
            $arr = require_once $this->themePath. 'assets.php';
        }
        else
        {
            $arr = [$profile => $list];
        }

        $this->_assets = array_merge($this->_assets, $arr);
    }

    public function prepareAssets(array $profiles)
    {
        foreach($profiles as $profile)
        {
            if(!isset($this->_assets[$profile]))
            {
                echo 'Profile '. $profile. ' not found';
                continue;
            }

            foreach($this->_assets[$profile] as $asset)
            {
                $this->add($asset);
            }
        }
    }

    public function getPath( $name )
    {
        $name = str_replace('.', '/', $name);
    
        /*$try = [
            THEME_PATH. $name. '.php',
            THEME_PATH. $name. '/index.php',
            APP_PATH. 'views/'. $name. '.php',
            APP_PATH. 'views/'. $name. '/index.php'
        ];*/

        foreach($this->overrideLayouts as $file)
        {
            $file = str_replace('__', $name, $file);
            if(file_exists($file)) return $file;
        }

        return false;
    }

    public function setBody($body)
    {
        $this->_body = $body;
    }

    public function getBody()
    {
        return $this->_body;
    }

    public function echo($type)
    {
        echo implode("\n", $this->generate($type));
    }

    public function generate($type)
    {
        $output = []; 

        if( 0 === strpos($type, 'inline') )
        {
            $tag = '';
            $output = $this->get($type);

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
            $assets = $this->get($type);
            if( is_array($assets) && count($assets) )
            {
                foreach($assets as $id => $asset)
                {
                    $this->createLink($output, $asset->get('type'), $id, $assets);
                }
            }
        }

        return $output;
    }

    public function add(string $link, $dependencies = array(), $id = '', $group = '')
    {
        if(empty($dependencies)) $dependencies = array();
        else  $dependencies = (array) $dependencies;
        $asset = new Asset($link, $dependencies, $group);
        $type = $asset->get('type');

        if(!empty($id))
        {
            $asset->set('id', $id );
        }
        $id = $asset->get('id');

        $key = $group ? $group. FncString::uc($type) : $type;

        FncArray::merge($this->_vars[$key], [
            $key => [ $id => $asset ]
        ]);
    }

    public function addInline(string $type, string $lines)
    {
        $key = 'inline'. FncString::uc($type);
        $this->_vars[$key][] = $lines;
    }

    private function createLink(&$result, $type, $id, &$assets)
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
                        $this->createLink($result, $type, $pid, $assets);
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
}
