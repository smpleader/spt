<?php
/**
 * SPT software - Request Server variable
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by Server variable
 * 
 */

namespace SPT\Request;

class Server extends Base
{
    public function __construct(?array $source = null)
    {
        $this->data = & $_SERVER;
    }
}