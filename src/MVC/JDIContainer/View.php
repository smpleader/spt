<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a dump view
 * 
 */

namespace SPT; 

use SPT\Lang;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareTrait;
use Joomla\DI\ContainerAwareInterface;

class View
{
    public function __construct(Container $container)
    {
        $this->user = $container->get('user');
        $this->theme = $container->get('theme');
        $this->helper = $container->get('helper');
        $this->hook = $container->get('viewhook');
    }

    protected $_vars = []; 
    protected $_layout = '_'; 
    protected $_share = []; 

    public function __get($name)
    { 
        if( isset( $this->_vars[$this->_layout][$name] ) ) return $this->_vars[$this->_layout][$name];
        if( isset( $this->_share[$name] ) ) return $this->_share[$name];

        return NULL;
        // silent this
        throw new \RuntimeException('View: Invalid variable '.$name, 500);
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

    public function render($layout, $hook = '')
    {
        echo $this->_render($layout, $hook);
    }

    public function _render($layout, $hook = '')
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
            $this->hook->trigger($layout, $hook);
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
        return Lang::_($words);
    }

    public function echo($words)
    {
        echo $this->txt($words);
    }
}