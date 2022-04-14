<?php
/**
 * SPT software - Session Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Session Adapter
 * 
 */

namespace SPT\Session;

use SPT\Session\Adapter as SessionAdapter;
use SPT\Instance as Ins;

class Instance extends Ins
{
    public function __constructor(SessionAdapter $adapter)
    {
        $this->adapter = $adapter;
    }
}