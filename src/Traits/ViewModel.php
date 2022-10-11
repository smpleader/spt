<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Create a dump view
 * 
 */

namespace SPT\Traits; 

use SPT\Support\Filter; 
use SPT\App\Adapter as Application;
use SPT\View\Adapter as View; 
use SPT\View\VM\ViewModelAdapter;

trait ViewModel
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
            $tmp = explode('.', $layout);
            $func = end( $tmp );
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
}