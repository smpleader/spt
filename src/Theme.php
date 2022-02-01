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
        $this->registerAssets();
    }

    public function getThemePath()
    {
        return $this->themePath;
    }

    public function registerAssets(string $profile = '', array $list = [])
    {
        if( '' === $profile && file_exists($this->themePath. '_assets.php'))
        {
            $arr = require_once $this->themePath. '_assets.php';
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
            $output = $this->createLinkByType($type);
        }

        return $output;
    }

    public function add(string $link, $dependencies = array(), $id = '', $group = '')
    {
        $dependencies = empty($dependencies) ? array() : (array) $dependencies;
        $asset = new Asset($link, $dependencies, $group);
        $type = $asset->get('type');

        if(empty($id))
        {
            $id = md5($link);
        }

        $asset->set('id', $id );

        $key = $group ? $group. FncString::uc($type) : $type;

        $current = $this->get($key, []);

        FncArray::merge($current, [
            $id => $asset
        ]);

        $this->set($key, $current);
    }

    public function addInline(string $type, string $lines)
    {
        $key = 'inline'. FncString::uc($type);
        $this->_vars[$key][] = $lines;
    }

    public function createLinkByType($type)
    {
        $result = [];
        $assets = $this->get($type);
        if( FncArray::isReady($assets) )
        {
            foreach($assets as $asset)
            {
                $result = array_merge($result, $this->createLink($asset));
            }
        }
        return $result;
    }

    private function createLink(Asset $sth)
    {
        $result = [];

        if(!$sth->get('added', false))
        {
            if( count($sth->get('parents') ) )
            {
                foreach($sth->get('parents') as $pid)
                {
                    $assets = $this->get($sth->get('type'));
                    $result = array_merge($result, $this->createLink($assets[$pid]));
                }
            }
    
            switch($sth->get('type'))
            {
                case 'css':
                    $autoremove = true;
                    $result[] = '<link rel="stylesheet" type="text/css" href="'. $sth->get('url'). '" >';
                break;
                case 'js':
                    $autoremove = true;
                    $result[] = '<script src="'. $sth->get('url'). '" ></script>';
                break;
            }
    
            $sth->set('added', true);
        } 

        return $result;
    }
}
