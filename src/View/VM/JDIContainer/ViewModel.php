<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a dump view
 * 
 */

namespace SPT\View\VM\JDIContainer; 

use SPT\JDIContainer\Base;
use SPT\Support\Filter; 
use SPT\App\Adapter as Application;
use SPT\View\Adapter as View; 
use SPT\View\VM\ViewModelAdapter;

class ViewModel extends Base implements ViewModelAdapter
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

    protected function parsePath(string $string, $root = '')
    {
        if(false === strpos($string, '|'))
        {
            $layout = $root ? $root. '.'. $string :  $string;
            $func = end( explode('.', $layout) );
        }
        else
        {
            list( $last, $func) = explode('|', $string);
            $layout = $root ? $root. '.'. $last : $last;
        }

        if(!in_array($func, ['alias', 'parse', 'parsePath', 'autorun', 'set', 'state']))
        {
            $this->functions[$layout] = $func;
            return $layout;
        }

        return false;
    }

    public function parse(array $sth = [], $root = '')
    {
        $map = [];
        if(!count($sth)) $sth = $this->layouts;
        foreach($sth as $inner => $lay)
        {
            if(is_numeric($inner))
            {
                if($found = $this->parsePath($lay, $root))
                {
                    $map[] = $found;
                }
            }
            elseif(is_array($lay)) // need to flat the array
            {
                $inner = $root ? $root. '.'. $inner : $inner;
                $map = array_merge($map, $this->parse($lay, $inner));
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
            $var = $this->request->{$request}->get($key, $old, $format);
            $this->session->set($sessionName, $var);
        }

        return $var;
    }
}