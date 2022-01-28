<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a dump view
 * 
 */

namespace SPT\MVC\DI; 

use SPT\BaseObj;

class View
{ 
    protected $theme;
    protected $lang;

    public function __construct($language, $theme)
    {
        $this->theme = $theme; 
        $this->lang = $language;
    }

    public function __get($name)
    { 
        if( 'theme' == $name ) return $this->theme;
        if( isset( $this->_vars[$this->_layout][$name] ) ) return $this->_vars[$this->_layout][$name];
        if( isset( $this->_share[$name] ) ) return $this->_share[$name];

        return NULL;
    }

    public function set($sth, $value = '', $shareable = false)
    {
        if(is_string($sth))
        {
            if( is_null($value) && is_null($shareable))
            {
                // set all variables for current layout
                $this->_layout = $sth;
                if(!isset($this->_vars[$sth])) $this->_vars[$sth] = [];
            }
            elseif( $shareable )
            {
                if(isset($this->_share[$sth]) && is_array($this->_share[$sth]))
                {
                    $value = array_merge($this->_share[$sth], $value);
                }
                $this->_share[$sth] = $value;
            }
            else
            {
                if(isset($this->_vars[$this->_layout][$sth]) && is_array($this->_vars[$this->_layout][$sth]))
                {
                    $value = array_merge($this->_vars[$this->_layout][$sth], $value);
                }
                $this->_vars[$this->_layout][$sth] = $value;
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
        if( isset( $this->_vars[$this->_layout][$key] ) ) return true;
        if( isset( $this->_share[$key] ) ) return true;
        return false;
    }

    public function render($layout)
    {
        echo $this->_render($layout);
    }

    public function _render($layout)
    {
        if( 0 !== strpos($layout, 'layouts.') &&
            0 !== strpos($layout, 'widgets.') )
        {
            $layout = 'layouts.'. $layout;
        }

        if( $file_layout = $this->theme->getPath($layout) )
        {
            $_keep_layout = $this->_layout;
            $this->set($layout, null, null);
            
            $content = $this->include($file_layout);
            $this->set($_keep_layout, null, null);

            return $content;
        }

        return 'Layout '.$layout. '  not found' ;
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

    protected function include($file)
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
        if($this->theme->getThemePath())
        {
            $this->theme->setBody(
                $this->_render($layout)
            );

            $file = $this->theme->getThemePath(). $page. '.php';
            if( false === $file )
            {
                throw new \Exception('Invalid theme');
            }
    
            return $this->include($file);
        }
        else
        { 
            return $this->_render($layout);
        }
    }
}