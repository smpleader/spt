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

use SPT\Session\Adapter as SessionAdapter;

class MemorySession implements SessionAdapter
{
    private $session = array();

    public function get(string $key, $default = null)
    {
        return isset($this->session[$key]) ? $this->session[$key] : $default;
    }
    
    public function set(string $key, $value)
    {
        $this->session[$key] = $value;
    }
}