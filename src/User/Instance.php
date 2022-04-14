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

use SPT\User\Base as UserAdapter;
use SPT\Instance as Ins;

class Instance extends Ins
{
    public function __construct(UserAdapter $adapter)
    {
        $this->adapter = $adapter;
    }
}