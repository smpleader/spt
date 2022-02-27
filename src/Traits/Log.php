<?php
/**
 * SPT software - Array
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Log Triat
 * 
 */

namespace SPT\Traits;

trait Log
{ 
    protected $logs = [];

    public function getLog()
    {
        return $this->logs; 
    }

    public function addLog()
    {
        $arg_list = func_get_args();
        foreach($arg_list as $arg){
            $this->logs[] = $arg;
        }
    }
}