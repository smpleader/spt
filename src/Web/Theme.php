<?php
/**
 * SPT software - Theme
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to create theme engine
 * 
 */

namespace SPT\Web;

use SPT\Asset;
use SPT\BaseObj;
use SPT\Support\FncString;
use SPT\Support\FncArray;

class Theme extends BaseObj
{
    /**
     * Internal variable to store array of asset
     * @var array $_assets
     */
    protected array $_assets = [];

    /**
     * Internal variable to store array of value
     * @var array $_assets
     */
    protected array $_vars = []; 
    /**
     * Readonlye theme override layouts (since PHP 8.1)
     * @var array $overrides
     */
    //public readonly array $overrides; 

    /**
     * Register Asset array
     *
     * @param array   $arr array of asset links
     * 
     * @return void 
     */ 
    public function registerAsset(array $arr)
    {
        $this->_assets = array_merge($this->_assets, $arr);
    }

    /**
     * Register Asset links based theme file
     *
     * @param string   $path string of asset file
     * 
     * @return void 
     */ 
    public function registerAssets(string $path)
    {
        if(file_exists($path))
        {
            $info = pathinfo($path);
            if('php' == $path['extension'])
            {
                $arr = require_once $this->path;
            }

            if( is_array($arr) )
            {
                $this->registerAsset($arr);
            }
        }
    }

    /**
     * Add Asset links based array of profile name
     *
     * @param array   $profiles array of profile name
     * 
     * @return void 
     */ 
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
                call_user_func_array([$this, 'add'], $asset);
            }
        }
    }

    /**
     * Generate assets string, replace __domain__ with domain for a absolute link
     *
     * @param string   $type type of asset, could be inlineJs, inlineJavscript, inlinceCss, inlineStylesheet, js, css
     * @param string   $url url of the assets, it could be subpath
     * 
     * @return void 
     */ 
    public function echo(string $type, string $url = '')
    {
        $generate = $this->generate($type);
        if ($url)
        {
            $generate = str_replace('__domain__', $url, $generate);
        }
        echo implode("\n", $generate);
    }

    /**
     * Generate resource with tags or inline script, inline stylesheet with content
     *
     * @param string   $type type of asset
     * 
     * @return string 
     */ 
    public function generate(string $type)
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
            $output = $this->createTagByType($type);
        }

        return $output;
    }

    /**
     * Add a link into assets array
     *
     * @param string   $link asset link
     * @param array|string   $dependencies id of asset which current asset link depend on ( required before add into HTML document )
     * @param string   $id ID of a asset 
     * @param string   $group asset group, if not set asset type is assigned
     * 
     * @return string 
     */ 
    public function add(string $link, $dependencies = array(), string $id = '', string $group = '')
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

    /**
     * Add a stylesheet or javascript into assets
     *
     * @param string   $type type of a asset 
     * @param string   $lines content need be added
     * 
     * @return void 
     */ 
    public function addInline(string $type, string $lines)
    {
        $key = 'inline'. FncString::uc($type);
        $this->_vars[$key][] = $lines;
    }

    /**
     * Return array of asset links based a type
     *
     * @param string   $type type of a asset  
     * 
     * @return array 
     */ 
    public function createTagByType(string $type)
    {
        $result = [];
        $assets = $this->get($type);
        if( FncArray::isReady($assets) )
        {
            foreach($assets as $asset)
            {
                $result = array_merge($result, $this->createTag($asset));
            }
        }
        return $result;
    }

    /**
     * Add asset object into array of asset based a type
     *
     * @param Asset   $sth object of asset
     * 
     * @return array 
     */ 
    protected function createTag(Asset $sth)
    {
        $result = [];

        if(!$sth->get('added', false))
        {
            if( count($sth->get('parents') ) )
            {
                $assets = $this->get($sth->get('type'));
                foreach($sth->get('parents') as $pid)
                {
                    if(isset($assets[$pid]))
                    {
                        $result = array_merge($result, $this->createTag($assets[$pid]));
                    }
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
