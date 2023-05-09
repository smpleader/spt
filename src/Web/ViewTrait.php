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

    private function preparePath(string $name, string $type)
    {
        if( 0 !== strpos($name, $type. 's.' ))
        {
            $fullname = $type. 's.'. $name;
        }

        $fullname = str_replace('.', '/', $fullname);

        $overrides =  $this->noTheme ? [
            SPT_PLUGIN_PATH. '/'. $this->currentPlugin. '/views/'. $fullname. '.php',
            SPT_PLUGIN_PATH. '/'. $this->currentPlugin. '/views/'. $fullname. '/index.php',
            SPT_PLUGIN_PATH. '/core/'. $fullname. '.php',
            SPT_PLUGIN_PATH. '/core/'. $fullname. '/index.php'

        ] : [
            SPT_THEME_PATH. '/'. $this->currentPlugin. '/'. $fullname. '.php',
            SPT_THEME_PATH. '/'. $this->currentPlugin. '/'. $fullname. '/index.php',
            SPT_THEME_PATH. '/_'. $type. 's/'. $fullname. '.php',
            SPT_THEME_PATH. '/_'. $type. 's/'. $fullname. '/index.php',
            SPT_PLUGIN_PATH. '/'. $this->currentPlugin. '/views/'. $fullname. '.php',
            SPT_PLUGIN_PATH. '/'. $this->currentPlugin. '/views/'. $fullname. '/index.php',
            SPT_PLUGIN_PATH. '/core/'. $fullname. '.php',
            SPT_PLUGIN_PATH. '/core/'. $fullname. '/index.php'
        ];
        
        $this->overrideLayouts[$name] = false;
        foreach($overrides as $file)
        {
            if(file_exists($file))
            {
                $this->overrideLayouts[$name] = $file;
                return;
            }
        }
    }

    public function debugPath($vardump = true)
    {
        $vardump ? var_dump( $this->overrideLayouts ) : return $this->overrideLayouts;
    }

    public function getPath(string $name, string $type = 'layout')
    {
        if(!isset($this->overrideLayouts[$name]))
        {
            $this->preparePath($name, $type); 
        }

        return $this->overrideLayouts[$name];
    }
}