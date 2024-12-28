<?php
/**
 * SPT software - Request File
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by File
 * 
 */

namespace SPT\Request;

class File extends Base
{
    public function __construct(?array $source = null)
    {
        $this->data = & $_FILES;
    }
}