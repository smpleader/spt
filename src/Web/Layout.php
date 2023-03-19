<?php
/**
 * SPT software - Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core layout
 * 
 */

namespace SPT\Web;

use SPT\BaseObj;

class Layout extends BaseObj
{ 
    protected $_file = '';
    protected $_theme;

    public function __construct($filePath, $theme)
    {
        $this->_file = $filePath;
        $this->_theme = $theme;
    }

    public function __get(string $name)
    { 
        if('theme' == $name) return $this->_theme;
        // try local 
        if( isset( $this->_vars[$name] ) ) return $this->_vars[$name];
        // try global
        return $this->theme->getVar($name, NULL);
    }

    public function render($filePath, $data)
    {
        return $this->theme->renderLayout($filePath, $data);
    }

    public function _render()
    {
        ob_start();
        include $this->_file;
        $content = ob_get_clean();
        return $content;
    }

    public function exists($key)
    {
        if( isset( $this->_vars[$this->_index][$key] ) ) return true;
        $try = $this->theme->getVar($name, '__NOT__FOUND__');
        return '__NOT__FOUND__' !== $try ;
    }
}