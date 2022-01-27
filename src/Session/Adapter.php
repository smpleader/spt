<?php
/**
 * SPT software - Session Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Session Adapter
 * 
 */

namespace SPT\Session;

interface Adapter
{
    public function get(string $key, $default = null);
    public function set(string $key, $value);
}
