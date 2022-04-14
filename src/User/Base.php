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

use SPT\BaseObj;

abstract class Base
{
    abstract function init()   
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
}
