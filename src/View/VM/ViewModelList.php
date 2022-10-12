<?php
/**
 * SPT software - Application
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Since JDI change some functions, we need a place to store list of VM
 * 
 */

namespace SPT\View\VM; 

use SPT\JDIContainer\Base;
use SPT\Support\Filter; 
use SPT\App\Adapter as Application;
use SPT\View\Adapter as View; 
use SPT\View\VM\ViewModelAdapter;
use SPT\Traits\ViewModel as ViewModelTrait;

class ViewModelList
{   
    private static $_list = [];
    public static function add(string $name)
    {
        static::$_list[] = $name;
    }

    public static function exists(string $name)
    {
        return isset(static::$_list[$name]);
    }

    public static function data()
    {
        return static::$_list;
    }
}