<?php
/**
 * SPT software - Triat
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Index 
 * 
 */

namespace SPT\Reuse; 

trait Index
{ 
    protected $index;
    public function reset()
    {
        $this->index = 0;
    }

    public function getIndex()
    {
        return $this->index;
    }
}