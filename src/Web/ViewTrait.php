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

    public function __construct($overrideLayouts, $themePath)
    {
        $this->overrideLayouts = $overrideLayouts;
        $this->theme = new Theme($themePath);
        $this->component = new ViewComponent();
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
}