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
    /**
    * Internal variable to see MVVM is applied or not
    * @var bool $isMVVM
    */
    protected $isMVVM;

    /**
    * Internal variable for Theme instance
    * @var Theme $theme
    */
    protected Theme $theme;

    /**
    * Internal variable for ViewComponent instance
    * @var ViewComponent $isMVVM
    */
    protected ViewComponent $component;

    /**
    * Internal variable for logs
    * @var array $logs
    */
    protected $logs = [];

    /**
    * Internal variable for paths
    * @var array $paths
    */
    protected $paths = [];

    /**
    * Internal variable for shared varialbel accros layouts
    * @var array $_shares
    */
    protected $_shares = [];

    /**
    * Internal variable for main layout
    * @var string $mainLayout
    */
    protected $mainLayout = '';

    /**
    * Internal variable for current plugin name, this is important for a view
    * @var string $currentPlugin
    */    
    protected $currentPlugin = '';

    /**
     * Constructor
     * 
     * @return void 
     */ 
    public function __construct(Theme $theme, ViewComponent $component, $supportMVVM = true)
    {
        $this->theme = $theme;
        $this->component = $component;
        $this->isMVVM = $supportMVVM;
    }
    
    /**
     * Get golbal variable by key
     *
     * @param string   $key  key of array data
     * @param mixed   $default default if null / not found
     * 
     * @return mixed 
     */
    public function getVar(string $key, $default = null)
    {
        return $this->_shares[$key] ?? $default; 
    }

    /**
     * Set golbal variable by key
     *
     * @param string   $key  key of array data
     * @param mixed   $value 
     * 
     * @return void 
     */
    public function setVar($key, $value)
    {
        $this->_shares[$key] = $value; 
    }

    /**
     * Get current theme instance
     * 
     * @return Theme 
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Get View Component
     *
     * @param ViewLayout   $layout  layou which sticked into View Component
     * 
     * @return ViewComponent 
     */
    public function getViewComponent(ViewLayout $layout)
    {
        return $this->component->support($layout);
    }

    /**
     * Prepare available path based overrides info 
     *
     * @param string   $name  layou path
     * @param string   $type  layou type
     * 
     * @throw Exception If path not ready with defined plugin
     * 
     * @return void 
     */
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
            
            $realPath = $this->theme->overrides['_path'][$plugin] ?? '';

            if(empty($realPath))
            {
                $realPath = $this->theme->overrides['_path'][$type.'_'.$plugin] ?? '';
            }

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

        foreach($this->theme->overrides[$type] as $line)
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

    /**
     * Find path logs
     *
     * @param bool   $vardump echo content directly or return array log
     * 
     * @return void|array 
     */
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

    /**
     * Get real path based path name
     *
     * @param string   $name layout name
     * @param string   $type layout type
     * 
     * @return string
     */
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

    /**
     * Generate a main content based path name, with fully structured body content
     *
     * @param string   $page theme layout file name
     * @param string   $layout layout name
     * @param array   $data array of data, keep as global variable
     * 
     * @throw Exception   If renderPage called twice or theme is not exist
     * 
     * @return string
     */
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
        
        
        $file = $this->theme->path. '/'. $page. '/index.php';
        if( !file_exists($file) )
        {
            $file = $this->theme->path. '/'. $page. '.php';
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

    /**
     * Generate a layout based layout path
     *
     * @param string   $layoutPath  layout file apath
     * @param array   $data array of data, keep as global variable
     * @param string   $type layout type
     * 
     * @throw Exception   if layout is not ready
     * 
     * @return string
     */    
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