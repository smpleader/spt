<?php
/**
 * SPT software - Dynamic Object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A basic object support dynamic properties
 * 
 */

namespace SPT;

#[\AllowDynamicProperties]
class DynamicObj 
{
    public function get($key, $default = null)
    {
        return property_exists($this, $key) ? $this->{$key} : $default;
    }
}