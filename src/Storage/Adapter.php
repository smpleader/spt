<?php
/**
 * SPT software - Storage Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Storage Adapter
 * 
 */

namespace SPT\Storage;

interface Adapter
{
    public function get(string $key, $default);
    public function set(string $key, $value);
}
