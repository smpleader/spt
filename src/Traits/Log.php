<?php
/**
 * SPT software - Log
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Log Triat
 * 
 */

namespace SPT\Traits;

trait Log
{ 
    /**
     * Internal var to cache  logs
     * 
     * @var  array $logs
     */ 
    protected $logs = [];

    /**
     * Return current log list
     * 
     * @return array
     */ 
    public function getLog()
    {
        return $this->logs; 
    }

    /**
     * Add log 
     * 
     * @return void
     */ 
    public function addLog()
    {
        $arg_list = func_get_args();
        foreach($arg_list as $arg){
            $this->logs[] = $arg;
        }
    }
}