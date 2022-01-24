<?php
/**
 * SPT software - Request Cookie
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by Post
 * 
 */

namespace SPT\Request;

class Post extends Base
{
    public function __construct(array $source = null)
    {
        $this->data = & $_POST;
    }
}