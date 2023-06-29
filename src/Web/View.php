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

class View
{
    protected $isMVVM;
    protected Theme $theme;
    protected ViewComponent $component;
    protected $overrideLayouts = [];
    protected $paths = [];
    protected $_shares = [];
    protected $mainLayout = '';
    protected $currentPlugin = '';
    protected $noTheme = false;

    public function __construct(string $pluginName, Theme $theme, ViewComponent $component, $supportMVVM = true)
    {
        $this->noTheme = SPT_THEME_PATH == SPT_PLUGIN_PATH. '/'. $pluginName. '/views';
        $this->currentPlugin = $pluginName;
        $this->theme = $theme;
        $this->component = $component;
        $this->isMVVM = $supportMVVM;
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

    protected function preparePath(string $name)
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

    public function renderPage(string $page, string $layout, array $data = [])
    {
        if( 0 !== strpos($layout, 'layouts.') )
        {
            $layout = 'layouts.'. $layout;
        }

        if($this->mainLayout)
        {
            throw new \Exception('Generate page twice is not supported ');
        }
        else
        {
            $this->mainLayout = $layout;
        }
        
        $file = SPT_THEME_PATH. '/'. $page. '.php';
        if( !file_exists($file) )
        {
            throw new \Exception('Invalid theme page '. $page);
        }

        $this->setVar('mainLayout', $layout);

        if($this->isMVVM)
        {
            ViewModelHelper::deployVM($layout, $data, []);
        }

        if(is_array($data) || is_object($data))
        {
            foreach($data as $key => $value)
            {
                $this->setVar($key, $value);
            }
        }

        $layout = new ViewLayout($file, $this);
        return $layout->_render();
    }
    
    public function renderLayout(string $layoutPath, array $data = [], string $type = 'layout')
    {
        $file = $this->getPath($layoutPath, $type);
        if( false === $file )
        {
            // $this->debugPath()
            throw new \Exception('Invalid layout '. $layoutPath);
        }

        if($this->isMVVM && $layoutPath != $this->mainLayout)
        {
            ViewModelHelper::deployVM($layoutPath, $data, $this->_shares);
        }

        $layout = new ViewLayout(
            $file, 
            $this,
            $data
        );
        
        return $layout->_render();
    }
}