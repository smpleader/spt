<?php
/**
 * SPT software - Pure Layout
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a view layout try to not use magic methods
 * 
 */

namespace SPT\Web\Layout;

use SPT\Web\Theme;

#[\AllowDynamicProperties]
class Pure extends Base
{
    /**
    * Internal variable cache file path
    * @var string $_path
    */
    protected readonly string $__path;
    
    /**
    * Internal variable cache a token: plugin:type:path
    * @var string $_id
    */
    protected readonly string $__id;
    
    /**
     * Constructor
     * 
     * @param Theme   $theme variable theme
     * @param string   $id token, format plugin:type:path
     * @param string   $path path file
     * @param array   $data data 
     * 
     * @return void 
     */ 
    public function __construct(Theme $theme, string $id, string $path, array $data = [])
    {
        if(!file_exists($path))
        {
            throw new \Exception('Can not create a layout from path '.$path);
        }
        
        $this->theme = $theme ;
        $this->__path = $path;
        $this->__id = $id;

        foreach($data as $k=>$v)
        {
            if(!in_array($k, ['theme', '__path', '__id']))
            {
                $this->$k = $v;
            }
        }
    } 
}