<?php
/**
 * SPT software - Memory Session
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Memory Session
 * 
 */

namespace SPT\Session;

use SPT\Session\Adapter SessionAdapter;

class MemorySession implements SessionAdapter
{
    private $session = array();

    public function get(string $key)
    {
        return isset($this->session[$key]) ? $this->session[$key] : null;
    }
    
    public function set(string $key, $value)
    {
        $this->session[$key] = $value;
    }
}