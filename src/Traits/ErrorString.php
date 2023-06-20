<?php
/**
 * SPT software - Error string Triat
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: how we trace the error
 * 
 */

namespace SPT\Traits;

trait ErrorString
{ 
    protected $error = ''; 

    public function getError()
    {
        return $this->error; 
    }
}