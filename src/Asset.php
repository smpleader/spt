<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to manage assets for frontend
 * 
 */

namespace SPT;

class Asset extends BaseObj
{
    /**
     * A constructor
     *
     * @param string   $link  URL to assets
     * @param array    $dependencies array IDs of assets which this assets require before run
     * @param string   $group  put asset into group so we can generate tag in a certain purpose
     * 
     * @return Asset
     */ 
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
