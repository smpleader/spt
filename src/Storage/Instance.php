<?php
/**
 * SPT software - Storage Instance
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Storage Instance
 * 
 */

namespace SPT\Storage;

use SPT\Storage\Adapter as StorageAdapter;

class Instance
{
    private $adapter;
    public function init(StorageAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function get(string $key)
    {
        return $this->adapter->get($key);
    }

    public function set(string $key, $value)
    {
        return $this->adapter->set($key, $value);
    }
}