<?php
/**
 * SPT software - View Model Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: View Model Adapter
 * 
 */

namespace SPT\View\VM;

interface ViewModelAdapter
{
    public function autorun(string $layout);
    public function set($key, $value='', $shareable=false);
}
