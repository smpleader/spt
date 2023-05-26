<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core view model
 * 
 */

namespace SPT\Web;

use SPT\Container\Client;

class ViewModel extends Client
{
    public function state(string $key, $default='', string $format='cmd', string $request_type='post', string $sessionName='')
    {
        if(empty($sessionName)) $sessionName = $key;

        $old = $this->session->get($sessionName, $default);
        $var = null;

        if( is_object( $this->request->{$request_type} ) )
        {
            $var = $this->request->{$request_type}->get($key, $old, $format);
            $session->set($sessionName, $var);
        }

        return $var;
    }
}