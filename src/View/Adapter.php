<?php
/**
 * SPT software - View Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: View Adapter
 * 
 */

namespace SPT\View;

interface Adapter
{
    public function render($layout);
    public function set($sth, $value = '', $shareable = false);
    public function createPage($layout, $page = 'index');
    public function include($file);
}
