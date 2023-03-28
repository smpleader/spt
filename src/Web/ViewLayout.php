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

class ViewLayout extends BaseObj
{ 
    protected $_file = '';
    protected $_view;

    public function __construct($filePath, $view, $data = null)
    {
        $this->_file = $filePath;
        $this->_view = $view;
        
        if(is_array($data) && count($data))
        {
            foreach($data as $key => $value)
            {
                $this->set($key, $value);
            }
        }
    }

    public function __get(string $name)
    { 
        if('theme' == $name) return $this->_view->getTheme();
        // try local 
        if( isset( $this->_vars[$name] ) ) return $this->_vars[$name];
        // try global
        return $this->_view->getVar($name, NULL);
    }

    public function render($filePath, $data)
    {
        return $this->_view->renderLayout($filePath, $data);
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
        if( isset( $this->_vars[$key] ) ) return true;
        $try = $this->_view->getVar($name, '__NOT__FOUND__');
        return '__NOT__FOUND__' !== $try ;
    }
}