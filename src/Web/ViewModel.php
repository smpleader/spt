<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core view model, is a type of container
 * 
 */

namespace SPT\Web;

use SPT\Container\Client;
use SPT\Traits\ObjectHasInternalData;
use SPT\Support\ViewModel as VMHub;
use SPT\Support\App;

class ViewModel extends Client
{
    use ObjectHasInternalData;

    /**
     * Get array of support layout
     */
    public function registerLayouts() {}

    /**
     * Get a state from a session
     * 
     * @param string   $key value name
     * @param mixed   $default default value if not set
     * @param string   $format value format filter
     * @param string   $request_type method type POST|GET|PUT|DELETE
     * @param string   $sessionName alias name in the session, in the case of field name is different to session name
     * 
     * @return mixed 
     */ 
    public function state(string $key, $default='', string $format='cmd', string $request_type='post', string $sessionName='')
    {
        if(empty($sessionName)) $sessionName = $key;

        $old = $this->session->get($sessionName, $default);
        $var = null;

        if( is_object( $this->request->{$request_type} ) )
        {
            $var = $this->request->{$request_type}->get($key, $old, $format);
            $this->session->set($sessionName, $var);
        }

        return $var;
    }
}