<?php
/**
 * SPT software - User Middleware for Authorisation
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Support login by Google account
 * 
 */

namespace SPT\User\Middlewares\Authorization;

use SPT\Middleware; 

class Google extends Middleware
{
    public function loggin(string $username, string $password)
    {
        // true if this is allowed group 
    }
}