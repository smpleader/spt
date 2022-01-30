<?php
/**
 * SPT software - Session Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Session Adapter
 * 
 */

namespace SPT\Session;

use SPT\Session\Adapter as SessionAdapter;

class Instance
{
    private $adapter;
    public function init(SessionAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function get(string $key, $default = null)
    {
        return $this->adapter->get($key, $default);
    }

    public function set(string $key, $value)
    {
        $this->adapter->set($key, $value);
    }
}