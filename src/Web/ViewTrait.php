<?php
/**
 * SPT software - View
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core view
 * 
 */

namespace SPT\Web;

use SPT\Web\Theme;
use SPT\Web\ViewLayout;

trait ViewTrait
{
    protected Theme $theme;
    protected ViewComponent $component;
    protected $overrideLayouts = [];
    protected $paths = [];
    protected $_shares = [];
    protected $mainLayout = '';
    protected $currentPlugin = '';
    protected $noTheme = false;

    public function __construct(string $pluginName, Theme $theme, ViewComponent $component)
    {
        $this->noTheme = SPT_THEME_PATH == SPT_PLUGIN_PATH. '/'. $pluginName. '/views';
        $this->currentPlugin = $pluginName;
        $this->theme = $theme;
        $this->component = $component;
    }

    public function getVar($key, $default)
    {
        return $this->_shares[$key] ?? $default; 
    }

    public function setVar($key, $value)
    {
        $this->_shares[$key] = $value; 
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function getViewComponent(ViewLayout $layout)
    {
        return $this->component->support($layout);
    }

    private function preparePath(string $name)
    {
        $fullname = str_replace('.', '/', $name);

        $overrides =  $this->noTheme ? [
            // plugin view
            SPT_PLUGIN_PATH. '/'. $this->currentPlugin. '/views/'. $fullname. '.php',
            SPT_PLUGIN_PATH. '/'. $this->currentPlugin. '/views/'. $fullname. '/index.php',
            // default view
            SPT_PLUGIN_PATH. '/core/views/'. $fullname. '.php',
            SPT_PLUGIN_PATH. '/core/views/'. $fullname. '/index.php'

        ] : [
            // theme view for a plugin view
            SPT_THEME_PATH. '/'. $this->currentPlugin. '/'. $fullname. '.php',
            SPT_THEME_PATH. '/'. $this->currentPlugin. '/'. $fullname. '/index.php',
            // theme view for a default
            SPT_THEME_PATH. '/_'. $fullname. '.php',
            SPT_THEME_PATH. '/_'. $fullname. '/index.php',
            // plugin view
            SPT_PLUGIN_PATH. '/'. $this->currentPlugin. '/views/'. $fullname. '.php',
            SPT_PLUGIN_PATH. '/'. $this->currentPlugin. '/views/'. $fullname. '/index.php',
            // default view
            SPT_PLUGIN_PATH. '/core/views/'. $fullname. '.php',
            SPT_PLUGIN_PATH. '/core/views/'. $fullname. '/index.php'
        ];
        
        $this->overrideLayouts[$name] = $overrides;
        $this->paths[$name] = false;
        foreach($overrides as $file)
        {
            if(file_exists($file))
            {
                $this->paths[$name] = $file;
                return;
            }
        }
    }

    public function debugPath($vardump = true)
    {
        if($vardump)
        {
            var_dump( $this->overrideLayouts );
        }
        else
        {
            return $this->overrideLayouts;
        }
    }

    public function getPath(string $name, string $type = 'layout')
    {
        $safeName = 0 !== strpos($name, $type. 's.') ? $type. 's.'. $name : $name;

        if(!isset($this->paths[$safeName]))
        {
            $this->preparePath($safeName); 
        }

        return $this->paths[$safeName];
    }
}