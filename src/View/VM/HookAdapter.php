<?php
/**
 * SPT software - View Hook Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: View Hook Adapter
 * 
 */

namespace SPT\View\VM;

interface HookAdapter
{
    public function trigger( string $layout, string $hook = '');
}
