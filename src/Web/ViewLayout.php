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
    
    /**
     * SUPPORT form + field
     */
    public function form($formName = null)
    {
        $sth = $this->form;
        if(is_array($sth))
        {
            if(!count($sth)) return false;
            if(isset($sth[$formName])) return $sth[$formName];

            reset($sth);
            return current($sth);
        }

        return $sth;
    }

    public function field($name = null, $formName = null)
    {
        echo $this->_field($name, $formName);
    }

    public function _field($name = null, $formName = null)
    {
        $form = $this->form($formName);
        if(!$form) return;
        
        $layout = false;
        if(null === $name)
        {
            if($form->hasField())
            {
                $this->field = $form->getField();
                $layout = $this->field->layout ? $this->field->layout : 'fields.'. $this->field->type;
            }
        }
        else
        {
            $this->field = $form->getField($name);
            $layout = $this->field->layout ? $this->field->layout : 'fields.'. $this->field->type;
        }

        if($layout && $file_layout = $this->_view->getPath($layout) )
        {
            ob_start();
            include $file_layout;
            $content = ob_get_clean();
            return $content;
        }

        return '<!-- Invalid field '. $name. ' in form '. $formName .' -->';
    }
}