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
        if('ui' == $name) return $this->_view->getViewComponent($this);
        // try local 
        if( isset( $this->_vars[$name] ) ) return $this->_vars[$name];
        // try global
        return $this->_view->getVar($name, NULL);
    }

    public function render($layout, array $data=[], $type='layout')
    {
        if( 0 !== strpos($layout, $type. 's.' ))
        {
            $layout = $type. 's.'. $layout;
        }

        return $this->_view->renderLayout($layout, $data, $type);
    }

    public function renderWidget(string $layout, array $data=[])
    {
        return $this->render($layout, $data, 'widget');
    }

    public function _render()
    {
        ob_start();
        include $this->_file;
        $content = ob_get_clean();
        return $content;
    }

    public function txt()
    {
        $arg_list = func_get_args();
        $label = array_shift($arg_list);
        if($label)
        {
            if(count($arg_list))
            {
                return call_user_func_array('sprintf', array_unshift($arg_list, $this->ui->translate($label)));
            }
            else
            {
                return $this->ui->translate($label);
            }
        }
        
        return '';
    }

    public function url($alias='')
    {
        return $this->ui->createUrl($alias);
    }
}