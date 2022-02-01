<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to manage assets
 * 
 */

namespace SPT;

class Asset extends BaseObj
{
    public function __construct(string $link, array $dependencies = array(), string $group = '')
    {
        $this->set('url', $link);

        $x = parse_url($link);
        $y = explode('/', $x['path']);
        $x = array_pop($y);

        $this->set('isMin', strpos($x, '.min.'));

        $y = explode('.', $x);

        $this->set('type', array_pop($y));
        //$this->set('id', implode('.', $y));
        $this->set('parents', $dependencies);
        $this->set('group', $group);
    }
}
