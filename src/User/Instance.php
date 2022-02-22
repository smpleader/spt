<?php
/**
 * SPT software - User Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: User Adapter
 * 
 */

namespace SPT\User;

use SPT\User\Adapter as UserAdapter;

class Instance
{
    private $adapter;
    public function __construct(UserAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function init($settings)
    {
        return $this->adapter->init($settings);
    }

    public function id(string $scope = '')
    {
        return $this->adapter->id($scope);
    }

    public function get(string $key)
    {
        return $this->adapter->get($key);
    }

    public function can(string $key)
    {
        return $this->adapter->can($key);
    }
}