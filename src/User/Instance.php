<?php
/**
 * SPT software - User Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Create User Object based User Adapter
 * 
 */

namespace SPT\User;

use SPT\User\Base as UserAdapter;
use SPT\Instance as Ins;

class Instance extends Ins
{
    /**
     * A constructor
     *
     * @param UserAdapter   $adapter  Here we change user based configuration of an user
     * 
     * @return void
     */ 
    public function __construct(UserAdapter $adapter)
    {
        $this->adapter = $adapter;
    }
}