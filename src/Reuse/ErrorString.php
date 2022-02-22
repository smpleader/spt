<?php
/**
 * SPT software - Array
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: how we trace the error
 * 
 */

namespace SPT\Reuse;

trait ErrorString
{ 
    protected $error = ''; 

    public function getError()
    {
        return $this->error; 
    }
}