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

class Memory
{
    $data = [];
    public function init($settings)
    {
        $this->set('id', rand(1,999));
    }

    public function id(string $scope = '')
    {
        return $this->data['id'];
    }

    public function get(string $key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    public function can(string $permission_key)
    {
        return true;
    }
}
