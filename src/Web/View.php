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
use SPT\Support\Plugin;
use SPT\Application\Configuration;

class View
{
    public readonly string $_themePath;
    public Theme $_theme;
    public Configuration $_config;

    private array $_layouts;
    private array $_plugins;
    public array $_closures;
    private string $_currentPlugin;
    private string $_currentTheme;
    private array $_shares;

    /**
     * Constructor
     * 
     * @param array   $pluginList list all information of plugins
     * @param string   $currentPlugin id of current plugin
     * @param array   $closures extra methods for layout
     * @param string   $currentTheme   theme name
     * @param string   $themePath path to theme path
     * @param string   $themeConfigFile path to theme configuration path
     * 
     * @return void 
     */ 
    public function __construct(array $pluginList, string $currentPlugin, array $closures, string $currentTheme = '',  string $themePath = '', string $themeConfigFile = '' )
    {
        $this->_plugins = $pluginList;
        $this->_currentPlugin = $currentPlugin;
        $this->_currentTheme = $currentTheme;
        $this->_closures = $closures;
        $this->_shares = [];

        if( $themePath && substr($themePath, -1) !== '/') $themePath .= '/';
        $this->_themePath = $themePath;

        $this->_theme = new Theme;
        if($themeConfigFile && file_exists($themeConfigFile))
        {
            $this->_config = new Configuration($themeConfigFile); 

            if( is_array($this->_config->of('assets', null) ) )
            {
                $this->_theme->registerAsset($this->_config->of('assets'));
            } 
        }
        else
        {
            $this->_config = new Configuration();
        }

    }

    /**
     * Key format  plugin/id:a.b.c 
     * 
     * @param string   $key a short token to layout path
     * 
     * @return Layout 
     */
    public function getLayout(string $key): Layout
    {
        $try = explode(':', $key);
        if(count($try) == 1)
        {
            $layout =  $key;
            $plugin = $this->_currentPlugin;
        }
        else
        {
            list($plugin, $layout) = $try;
            $plugin = Plugin::id($plugin);
        }

        $id = $plugin. ':'. $layout;

        if(!isset($this->_layouts[$id]))
        {
            $this->_layouts[$id] = new \SPT\Web\Layout\Pure(
                $this, 
                $id, 
                $this->getRealPath($plugin, $layout)
            );
            $this->_layouts[$id]->update($this->_closures, true);
        }

        return $this->_layouts[$id];
    }

    public function render(string $key, array $data = [], $isString = true)
    {
        $layout = $this->getLayout($key);
        
        $data = ViewModel::getData($layout->getId(), $data);
        $layout->update($data);

        // TODO: VALIDATE  before render
        // $layout->validate()

        return $isString ? $layout->_render() : $layout->render();
    }

    public function getRealPath(string $plgId, string $token)
    {
        if(!isset($this->_plugins[$plgId]))
        {
            throw new \Exception('Invalid Plugin '. $plgId);
        }

        $token = str_replace('.', '/', $token);
        if($this->_currentTheme)
        {
            //  case1: theme override plugin layout
            $path = $this->_plugins[$this->_currentTheme]. 'views/_'. $plgId. '/'. $token;
            if( $path = $this->fileExists($path) ) return $path;
        }

        //  case2: plugin layout
        $path = $this->_plugins[$plgId]. 'views/'. $token;
        if( $path = $this->fileExists($path) ) return $path;
    
        if($this->_currentTheme)
        {
            //  case3: share layout from theme
            $path = $this->_plugins[$this->_currentTheme]. 'views/'. $token;
            if( $path = $this->fileExists($path) ) return $path;
        }

        throw new \Exception('Invalid path '. $plgId. ':'. $token );

    }

    public function fileExists(string $path)
    {
        if(file_exists($path. '.php')) return $path. '.php';
        if(file_exists($path) && is_file($path)) return $path;
        return false;
    }

    public function setData(array $data)
    {
        $this->_shares = array_merge($this->_shares, $data);
    }

    public function getData(string | int $key, $default = null, ?string $format = null)
    {
        if(!isset($this->_shares[$key])) return $default;
        return $format ? Filter::$format($this->_shares[$key]) : $this->_shares[$key];
    }
}