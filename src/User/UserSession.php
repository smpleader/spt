<?php
/**
 * SPT software - User Based Session
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: User use session
 * 
 */

namespace SPT\User;

use SPT\Session\Adapter as Session;

class UserSession extends Base
{
    protected $session;
    public function init(Session $session)
    {
        $this->session = $session;
    }
}
