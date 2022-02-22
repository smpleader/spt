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

trait ErrorArray
{ 
    protected $errors = [];

    public function getErrors()
    {
        return $this->errors;
    }
}