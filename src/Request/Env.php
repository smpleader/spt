<?php
/**
 * SPT software - Request Env variable
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by Env variable
 * 
 */

namespace SPT\Request;

class Env extends Base
{
    public function __construct(?array $source = null)
    {
        $this->data = & $_ENV;
    }
}