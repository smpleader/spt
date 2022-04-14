<?php
/**
 * SPT software - User Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: User just once time
 * Write this to easily imagine how the user should be
 * 
 */

namespace SPT\User;

use SPT\ConfigurableDI;

class Base extends ConfigurableDI
{
    protected $session;
    protected $data;
    protected $context;

    public function init($options)
    {
        parent::init($options);

        $storage = empty($this->context) ? '_user' : $this->context;
        $this->data = $this->session->get( $storage );
        
        if( empty($this->data) )
        {
            $this->data = $this->getDefault();
        }
    }

    public function getMutableFields(): array
    {
        return [
            'session' => '\SPT\Session\Instance'
        ];
    }

    public function getDefault()
    {
        return [
            'id' => 0,
            'username' => 'guest',
            'fullname' => 'A Guest',
            'email' => '',
            'groups' => ['guest'],
            'permission' => [],
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    public function is(string $group)
    {
        return is_array($this->_vars['groups']) ? in_array($group, $this->_vars['groups']) : false;
    }

    public function can(string $permission)
    {
        return is_array($this->_vars['permission']) ? in_array($permission, $this->_vars['permission']) : false;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        $storage = empty($this->context) ? '_user' : $this->context;
        $this->session->update($storage, $this->data);
    }

    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
}
