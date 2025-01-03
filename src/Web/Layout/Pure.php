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
     * @param string   $filePath layout path
     * @param array   $dataView data from View
     * @param array   $dataViewModel data from ViewModel
     * 
     * @return void 
     */ 
    public function __construct(string $path, array $dataView, array $dataViewModel)
    {
        foreach($dataView as $k=>$v)
        {
            $this->$k = $v;
        }

        foreach($dataViewModel as $k=>$v)
        {
            $this->$k = $v;
        } 
    } 
}