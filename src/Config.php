<?php
/**
 * SPT software - Configuration
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Handle configuration for safer and unified configurations
 * 
 */

namespace SPT;

use StaticObj;

class Config extends StaticObj
{
    static protected $_vars = array();

    static function init( $vars ){
        // run once
        if( is_array($vars) && count(self::$_vars) == 0 ){
            foreach($vars as $key => $val){ 
                if( !is_numeric($key) ){
                    self::set($key, $val);
                }
            }
        }
    }
}
