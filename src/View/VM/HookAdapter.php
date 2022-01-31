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

use SPT\View\Adapter as View;

interface HookAdapter
{
    public function trigger(View $view, string $layout, string $hook = '');
}
