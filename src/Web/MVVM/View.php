<?php
/**
 * SPT software - View
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core view
 * 
 */

namespace SPT\Web\MVVM;

use SPT\Web\Theme;
use SPT\Web\ViewLayout;

class View
{
    protected Theme $theme;
    protected $overrideLayouts = [];
    protected $_shares = [];
    protected $vm = [];
    protected $mainLayout = '';

    public function __construct($layoutPath, $themePath, $theme)
    {
        if(empty($themePath) || empty($theme))
        {
            $themePath = $layoutPath;
        }
        else
        {
            $themePath .= '/'. $theme;
        }

        if( '/' != substr($themePath, -1))
        {
            $themePath .= '/';
        }

        if($themePath == $layoutPath)
        {
            $this->overrideLayouts = [
                $layoutPath. '__.php',
                $layoutPath. '__/index.php'
            ];
        }
        else
        {   
            $this->overrideLayouts = [
                $themePath. '__.php',
                $themePath. '__/index.php',
                $layoutPath. '__.php',
                $layoutPath. '__/index.php'
            ];
        }
        
        $this->theme = new Theme($themePath);
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

    public function renderPage(string $page, string $layout, array $data = [])
    {
        if($this->mainLayout)
        {
            throw new \Exception('Generate page twice is not supported ');
        }
        else
        {
            $this->mainLayout = $layout;
        }
        
        $file = $this->theme->getThemePath(). '/'. $page. '.php';
        if( !file_exists($file) )
        {
            throw new \Exception('Invalid theme page '. $page);
        }

        ViewModelHelper::deployVM($layout, $data, []);

        if(is_array($data) || is_object($data))
        {
            foreach($data as $key => $value)
            {
                $this->setVar($key, $value);
            }
        }

        ob_start();
        include $file;
        $content = ob_get_clean();

        return $content; 
    }
    
    public function renderLayout(string $layoutPath, array $data = [])
    {
        if( 0 !== strpos($layoutPath, 'layouts.') )
        {
            $layoutPath = 'layouts.'. $layoutPath;
        }
        $file = $this->getPath($layoutPath);
        if( false === $file )
        {
            throw new \Exception('Invalid layout '. $layoutPath);
        }

        if($layoutPath != $this->mainLayout)
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

    public function renderWidget(string $widgetPath, array $data = [])
    {
        if( 0 !== strpos($widgetPath, 'widgets.') )
        {
            $widgetPath = 'widgets.'. $widgetPath;
        }

        $file = $this->getPath($widgetPath);
        if( false === $file )
        {
            throw new \Exception('Invalid widget '. $widgetPath);
        }

        ViewModelHelper::deployVM($widgetPath, $data, $this->_shares);

        $layout = new ViewLayout(
            $file, 
            $this,
            $data
        );
        
        return $layout->_render();
    }
}