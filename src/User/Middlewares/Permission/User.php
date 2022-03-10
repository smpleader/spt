<?php
/**
 * SPT software - Application Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Instance
 * 
 */

namespace SPT\User\Middlewares\Permission;

use SPT\Middleware;
use SPT\User\Adapter as UserAdapter;

class User extends Middleware
{
    public function allow(string $key, UserAdapter $user)
    {
        // true if this is owner
        // example return TableEntity::find( ['creator' => $user->get('id')] );
    }
}