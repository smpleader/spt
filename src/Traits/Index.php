<?php
/**
 * SPT software - Triat Index
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Index 
 * 
 */

namespace SPT\Traits; 

trait Index
{
    /**
    * Internal variable to cache current data array index
    *
    * @var int $index
    */
    protected $index;

    /**
     * Reset the index to the top
     * 
     * @return void 
     */
    public function resetIndex()
    {
        $this->index = 0;
    }

    /**
     * Get index of an array
     * 
     * @return int 
     */
    public function getIndex()
    {
        return $this->index;
    }
}