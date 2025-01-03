<?php
/**
 * SPT software - Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just base class for   layouts
 * 
 */

namespace SPT\Web\Layout;

use SPT\Web\Theme;

class Base
{ 
    /**
    * Internal variable cache file path
    * @var string $_path
    */
    protected string $_path = '';

    /**
    * Theme info
    * @var string $_path
    */
    protected Theme $theme;

    /**
     * After calling renderLayout from View instance, 
     * this function will be called to keep variables attached into ViewLayout
     * 
     * @return string 
     */ 
    public function _render(): string
    {
        ob_start();
        include $this->_path;
        $content = ob_get_clean();
        return $content;
    }
}