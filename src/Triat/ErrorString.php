<?php
/**
 * SPT software - Array
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: how we trace the error
 * 
 */

namespace SPT\Triat;

trait ErrorString
{ 
    protected $error = ''; 

    public function getError()
    {
        return $this->error; 
    }
}