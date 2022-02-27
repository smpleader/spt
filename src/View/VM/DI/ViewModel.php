<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Implement ViewModel
 * 
 */

namespace SPT\View\VM\DI; 

use SPT\Support\Filter; 
use SPT\App\Adapter as Application;
use SPT\View\Adapter as View; 
use SPT\View\VM\ViewModelAdapter;
use SPT\Traits\ViewModel as ViewModelTrait;

class ViewModel implements ViewModelAdapter
{
    use ViewModelTrait;
    protected $app;
    protected $view;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function state($key, $default='', $format='cmd', $request='post', $sessionName='')
    {
        if(empty($sessionName)) $sessionName = $key;

        $old = $this->app->session->get($sessionName, $default);

        if( !is_object( $this->app->request->{$request} ) )
        {
            $var = null;
        }
        else
        {
            $var = $this->app->request->{$request}->get($key, $old, $format);
            $this->app->session->set($sessionName, $var);
        }

        return $var;
    }
}
