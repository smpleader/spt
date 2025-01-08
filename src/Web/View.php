<?php
/**
 * SPT software - View
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Class view to display content
 * 
 */

namespace SPT\Web;

use SPT\Web\Theme;
use SPT\Web\Layout\Base as Layout; 
use SPT\Support\ViewModel;

class View
{
    private array $_layouts;
    private Theme $_theme;
    private array $_plugins;
    private array $_closures;
    private string $_current;
    private string $_themePath;

    /**
     * Constructor
     * 
     * @param array   $pluginList list all information of plugins
     * @param string   $currentPlugin id of current plugin
     * @param string   $themePath path to theme path
     * @param string   $themeConfigFile path to theme configuration path
     * 
     * @return void 
     */ 
    public function __construct(array $pluginList, string $currentPlugin, array $closures, string $themePath = '', string $themeConfigFile = '' )
    {
        $this->_plugins = $pluginList;
        $this->_current = $currentPlugin;
        $this->_closures = $closures;

        if( $themePath && substr($themePath, -1) !== '/') $themePath .= '/';
        $this->_themePath = $themePath;

        $this->_theme = new Theme;
        if($themeConfigFile)
        {
            $this->_theme->registerAssets($themeConfigFile);
        }
    }

    /**
     * Key format  plugin/id:layout:a.b.c
     * 
     * @param string   $key a short token to layout path
     * 
     * @return Layout 
     */
    public function getLayout(string|array $key): Layout
    {
        $tmp = is_array($key) ? $key : explode(':', $key);
        $count = count($tmp); 
        switch($count) 
        {
            case 1:
                $plg = $this->_current;
                $type = 'layout';
                $path = $key;
                break;
            case 2:
                $plg = $this->_current;
                list($type, $path) = $tmp;
                break;
            case 3:
                list($plg, $type, $path) = $tmp;
                if(!isset($this->_plugins[$plg]))
                {
                    throw new \Exception('Invalid plugin '. $plg. ' of path '. $key);
                }
                break;
            default:
                throw new \Exception('Invalid path '. $key);
            break;
        }

        $id = $plg. ':'. $type. ':'. $path;
        if(!isset($this->_layouts[$id]))
        {
            $realPath = $this->getRealPath($plg, $type, $path);
            $this->_layouts[$id] = new  \SPT\Web\Layout\Pure($this->_theme, $id, $realPath, $this->_closures);
        }

        return $this->_layouts[$id];
    }

    public function render(string|array $key, array $data = [], $isString = true)
    {
        $layout = $this->getLayout($key);
        
        $data = ViewModel::getData($layout->getId(), $data);
        $layout->update($data);

        // TODO: VALIDATE  before render
        // $layout->validate()

        return $isString ? $layout->_render() : $layout->render();
    }

    public function getRealPath(string $plgId, string $type, string $token)
    {
        if(!isset($this->_plugins[$plgId]))
        {
            throw new \Exception('Invalid Plugin '. $plgId);
        }

        $type = strtolower($type);

        if(!in_array($type, ['theme', 'layout', 'widget']))
        {
            throw new \Exception('Invalid Path type '. $type);
        }

        $token = str_replace('.', '/', $token);

        if($this->_themePath)
        {
            $path = 'theme' == $type ? $this->_themePath. $token : $this->_themePath. $plgId. '/'. $type. 's/'. $token;
            if( $path = $this->fileExists($path) ) return $path;
        }

        $path = $this->_plugins[$plgId]->getPath( 'views/'. $type. 's/'. $token);
        if( $path = $this->fileExists($path) ) return $path;

        throw new \Exception('Invalid path '. $token. ' <!-- plugin '. $plgId. ':'. $type. '-->' );
    }

    private function fileExists(string $path)
    {
        if(file_exists($path. '.php')) return $path. '.php';
        if(file_exists($path) && is_file($path)) return $path;
        if(file_exists($path. '/index.php')) return $path. '/index.php';
        return false;
    }
}