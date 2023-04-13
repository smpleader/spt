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

    public function __construct(array $overrideLayouts,Theme $theme, ViewComponent $component)
    {
        $this->overrideLayouts = $overrideLayouts;
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

    public function getPath(string $name, string $type = 'layout')
    {
        if( 0 !== strpos($name, $type. 's.' ))
        {
            $name = $type. 's.'. $name;
        }

        $name = str_replace('.', '/', $name);

        $overrideLayouts = $this->overrideLayouts;
        if($type !== 'layout')
        {
            $overrideLayouts[] = SPT_PLUGIN_PATH.'/core/views/'. $name.'.php';
            $overrideLayouts[] = SPT_PLUGIN_PATH.'/core/views/'. $name.'/index.php';
        }

        foreach($overrideLayouts as $file)
        {
            $file = str_replace('__', $name, $file);
            if(file_exists($file)) return $file;
        }

        return false;
    }

 /*   public function renderWidget(string $widgetPath, array $data = [])
    {
        return $this->renderLayout($widgetPath, $data, 'widget');
    }

    public function renderViewComponent(string $viewcomPath, array $data = [])
    {
        return $this->renderLayout($viewcomPath, $data, 'vcom');
    }*/
}