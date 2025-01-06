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

#[\AllowDynamicProperties]
class Pure extends Base
{
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
        $this->_plugin = $plugin;
        $this->_path = $path;
        $this->_type = $type;

        foreach($data as $k=>$v)
        {
            if(!in_array($k, ['theme', '__path', '__id']))
            {
                $this->$k = $v;
            }
        }
    } 
}