<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a dump view
 * 
 */

namespace SPT\View\VM; 

use SPT\Support\Filter; 
use SPT\App\Adapter as Application;
use SPT\View\Adapter as View; 
use SPT\ViewModel\VM\ViewModelAdapter;

class ViewModel implements ViewModelAdapter
{   
    protected $alias = __CLASS__;
    protected $layouts = [];
    protected $functions = [];
    protected $view;

    public function setView(View $view)
    {
        $this->view = $view;
    }

    public function alias()
    {
        return $this->alias;
    }

    protected function parsePath($string, $root)
    {
        if(false === strpos($string, '|'))
        {
            $layout = $root. '.'. $string;
            $func = end( explode('.', $layout) );
        }
        else
        {
            list( $last, $func) = explode('|', $string);
            $layout = $root. '.'. $last;
        }

        return [$layout, $func];
    }

    public function parse()
    {
        $map = [];
        foreach($this->layouts as $root => $lay)
        {
            list($layout, $func) = $this->parsePath($lay, $root);
            if(!in_array($func, ['alias', 'parse', 'parsePath', 'autorun', 'set', 'state']))
            {
                $this->functions[$layout] = $func;
                $map[] = $layout;
            }
        }
        return $map;
    }

    public function autorun($layout)
    {
        if(isset($this->functions[$layout]))
        {
            $fnc = $this->functions[$layout];
            $this->{$fnc}();
        }
    }

    public function set($key, $value='', $shareable=false)
    {
        $this->view->set($key, $value, $shareable);
    }

    public function state($key, $default='', $format='', $request='post', $sessionName='')
    {
        if(empty($sessionName)) $sessionName = $key;

        $old = $this->session->get($sessionName, $default);

        if( !is_object( $this->request->{$request} ) )
        {
            $var = null;
        }
        else
        {
            $var = $this->request->{$request}->get($key, $old);
            if($format)
            {
                $var = Filter::{$format}($var);
            }
            $this->session->set($sessionName, $var);
        }

        return $var;
    }
}