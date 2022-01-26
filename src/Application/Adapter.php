<?php
/**
 * SPT software - Application Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Adapter
 * 
 */

namespace SPT\Application;

interface Adapter
{
    public function get(string $key);
    public function set(string $key, $value);
    public function factory(string $key);
    public function execute();
}
