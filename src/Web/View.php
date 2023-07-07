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
    protected $logs = [];
    protected $paths = [];
    protected $_shares = [];
    protected $mainLayout = '';
    protected $currentPlugin = '';
    protected $overrides = [];

    public function __construct(array $overrides, Theme $theme, ViewComponent $component, $supportMVVM = true)
    {
        $this->overrides = $overrides;
        $this->theme = $theme;
        $this->component = $component;
        $this->isMVVM = $supportMVVM;
    }

    public function getVar($key, $default = null)
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

    protected function preparePath(string $name, string $type)
    {
        $overrides = [];
        $plugin = '';
        $layout = '';
        if(in_array($type, ['widget', 'vcom']))
        {
            @list($plugin, $layout) = explode('::', $name);
            if(empty($plugin) || empty($layout))
            {
                throw new \Exception($type.' needs plugin name or path ( input: '. $name.')');
            } 
            
            $realPath = $this->overrides['_path'][$plugin] ?? '';

            if(empty($realPath))
            {
                throw new \Exception($type.' does not exists in '. $plugin);
            }

            $layout = str_replace('.', '/', $layout);
        }
        else
        {
            $layout = str_replace('.', '/', $name);
        }

        foreach($this->overrides[$type] as $line)
        {
            if($plugin)
            {
                $line = str_replace('__PLG__', $plugin, $line);
                $line = str_replace('__PLG_PATH__', $realPath, $line);
            }

            $overrides[] = $line. $layout. '.php';
            $overrides[] = $line. $layout. '/index.php';
        }
        
        $this->logs[$name] = $overrides;
        $this->paths[$type. '_'. $name] = false;
        foreach($overrides as $file)
        {
            if(file_exists($file))
            {
                $this->paths[$type. '_'.$name] = $file;
                return;
            }
        }
    }

    public function debugPath($vardump = true)
    {
        if($vardump)
        {
            var_dump( $this->logs );
        }
        else
        {
            return $this->logs;
        }
    }

    public function getPath(string $name, string $type = 'layout')
    {
        // absolute path, nothing to worry
        if(file_exists($name)) return $name;

        if(!isset($this->paths[$type. '_'. $name]))
        {
            $this->preparePath($name, $type); 
        }

        return $this->paths[$type. '_'. $name];
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
        
        
        $file = SPT_THEME_PATH. '/'. $page. '/index.php';
        if( !file_exists($file) )
        {
            $file = SPT_THEME_PATH. '/'. $page. '.php';
        }
        
        if( !file_exists($file) )
        {
            throw new \Exception('Invalid theme page '. $page);
        }

        $this->setVar('mainLayout', $layout);

        if($this->isMVVM)
        {
            ViewModelHelper::deployVM('layout', $layout, $data, []);
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
            // $this->debugPath();
            throw new \Exception('Invalid layout '. $layoutPath);
        }

        if($this->isMVVM && $layoutPath != $this->mainLayout)
        {
            ViewModelHelper::deployVM($type, $layoutPath, $data, $this->_shares);
        }

        $layout = new ViewLayout(
            $file, 
            $this,
            $data
        );
        
        return $layout->_render();
    }
}