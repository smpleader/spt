<?php
/**
 * SPT software - PHP Session
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: PHP Session
 * 
 */

namespace SPT\Session;

use SPT\Session\Adapter as SessionAdapter;

class PhpSession implements SessionAdapter
{
    public function __construct()
    {
        if(session_id() == '' || !isset($_SESSION)) {
            // session isn't started
            @session_start();
        }
    }

    public function get(string $key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }
}