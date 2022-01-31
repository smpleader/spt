<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a view, used in MVC or ViewModel
 * 
 */

namespace SPT\View;

use SPT\View\Adapter as ViewAdapter;

class Base implements ViewAdapter
{ 
    protected $theme = null;
    protected $lang = null;
    protected $_share = [];
    protected $_vars = [];
    protected $_index = '_root_';

    /* It's better to stop this for easier dependency injection init()
    public function __construct($language, $theme)
    {
        $this->theme = $theme; 
        $this->lang = $language;
    }*/

    public function __get($name)
    { 
        if( 'theme' == $name ) return $this->theme;
        if( isset( $this->_vars[$this->_index][$name] ) ) return $this->_vars[$this->_index][$name];
        if( isset( $this->_share[$name] ) ) return $this->_share[$name];

        return NULL;
    }

    public function set($sth, $value = '', $shareable = false)
    {
        if(is_string($sth))
        {
            if( $shareable )
            {
                if(isset($this->_share[$sth]) && is_array($this->_share[$sth]))
                {
                    $value = array_merge($this->_share[$sth], $value);
                }
                $this->_share[$sth] = $value;
            }
            else
            {
                if(isset($this->_vars[$this->_index][$sth]) && is_array($this->_vars[$this->_index][$sth]))
                {
                    $value = array_merge($this->_vars[$this->_index][$sth], $value);
                }

                $this->_vars[$this->_index][$sth] = $value;
            }
        }
        elseif( is_array($sth) || is_object($sth) )
        {
            foreach($sth as $key=>$value)
            {
                $this->set($key, $value, $shareable);
            }
        }
    }

    public function exists($key)
    {
        if( isset( $this->_vars[$this->_index][$key] ) ) return true;
        if( isset( $this->_share[$key] ) ) return true;
        return false;
    }

    public function render($layout)
    {
        echo $this->_render($layout);
    }

    public function _render($layout)
    {
        $layout = $this->safeName($layout);

        if( $file_layout = $this->theme->getPath($layout) )
        {
            $_keep_layout = $this->_index;
            $this->setIndex($layout);
            
            $content = $this->include($file_layout);
            $this->setIndex($_keep_layout);

            return $content;
        }

        return 'Layout '.$layout. '  not found' ;
    }

    public function safeName($layout)
    {
        if( 0 !== strpos($layout, 'layouts.') && 0 !== strpos($layout, 'widgets.') )
        {
            $layout = 'layouts.'. $layout;
        }
        return $layout;
    }

    public function setIndex($name)
    {
        $this->_index = $this->safeName($namw);
        if(!isset($this->_vars[$this->_index]))
        {
            $this->_vars[$this->_index] = [];
        }
    }

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

        if($layout && $file_layout = $this->theme->getPath($layout) )
        {
            return $this->include($file_layout);
        }

        return '<!-- Invalid field '. $name. ' in form '. $formName .' -->';
    }

    public function include($file)
    {
        ob_start();
        include $file;
        $content = ob_get_clean();
        return $content;
    }

    public function txt($words)
    {
        return null == $this->lang ? $words  : ( $this->lang->exists($words) ? $this->lang->{$words} : $word );
    }

    public function echo($words)
    {
        echo $this->txt($words);
    }

    public function createPage($layout, $page = 'index')
    {
        $file = $this->theme->getThemePath(). $page. '.php';
        if( !file_exists($file) )
        {
            throw new \Exception('Invalid theme '.$file);
        }

        $this->theme->setBody(
            $this->_render($layout)
        );

        return $this->include($file); 
    }
}