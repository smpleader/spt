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

use Joomla\DI\Container;
use SPT\JDIContainer\Base;
use SPT\Support\Filter; 
use SPT\App\Adapter as Application;
use SPT\View\Adapter as View; 
use SPT\View\VM\ViewModelAdapter;
use SPT\View\VM\ViewModelList;
use SPT\Traits\ViewModel as ViewModelTrait;

class ViewModel extends Base implements ViewModelAdapter
{   
    use ViewModelTrait; 

    public function __construct(Container $container)
    {
        if(empty($this->alias))
        {
            $tmp = get_class($this);
            $tmp = explode('\\', $tmp);
            $this->alias = end($tmp);
        }
        ViewModelList::add($this->alias);
        parent::__construct($container);
    }

    public function state($key, $default='', $format='cmd', $request='post', $sessionName='')
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