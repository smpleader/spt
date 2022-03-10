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
    public function __construct(UserAdapter $adapter, array $middleWares = [])
    {
        $this->adapter = $adapter;

        if(count($middleWares))
        {
            foreach($middleWares as $type => $mw)
            {
                if('adapter' != $type)
                {
                    $key = strtolower($key). 'MW';
                    $this->{$key} = $mv;
                }
            }
        }
    }

    public function init($settings)
    {
        return $this->adapter->init($settings);
    }

    public function get(string $key)
    {
        return $this->adapter->get($key);
    }

    public function can(string $key)
    {
        if(property_exists($this, 'permissionMW'))
        {
            return $this->permissionMW->allow($key, $this->adapter);
        }

        return $this->adapter->can($key);
    }
}