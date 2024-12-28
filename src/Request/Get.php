<?php
/**
 * SPT software - Request Cookie
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by Get
 * 
 */

namespace SPT\Request;

class Get extends Base
{
    public function __construct(?array $source = null)
    {
        $this->data = & $_GET;
    }
}