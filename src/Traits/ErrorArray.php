<?php
/**
 * SPT software - Error Array Traits
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: how we trace the error
 * 
 */

namespace SPT\Traits;

trait ErrorArray
{ 
    /**
     * Array of error lis
     * 
     * @var array errors
     */ 
    protected $errors = [];

    /**
     * Return current error list
     * 
     * @return array
     */ 
    public function getErrors()
    {
        return $this->errors;
    }
}