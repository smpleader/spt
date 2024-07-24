<?php
/**
 * SPT software - Magic Object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Magic Object to simplify the jobs
 * 
 */

namespace SPT\Support;

use SPT\MagicObj;

class FncMagicObj
{
    public static function import(string $path, &$_var = null)
    { 
        if( is_dir($path) )
        {
            foreach(new \DirectoryIterator($path) as $item) 
            {
                if($item->isDot()) continue;
                
                if($item->isDir())
                {
                    $name =  $item->getBasename();
                    $_var->{$name} = new MagicObj();
                    self::import($path. '/'. $name, $_var->{$name});
                }
                elseif($item->isFile() && 'php' == $item->getExtension())
                {
                    $name =  $item->getBasename('.php');
                    $_var->{$name} = new MagicObj();
                    self::import( $path. '/'. $item->getBasename(), $_var->{$name});
                }
            }

            return;
        }

        $try = require $path;
        if(is_array($try) || is_object($try))
        {
            foreach ($try as $key => $value) {
                if(!is_numeric($key))
                {
                    $_var->{$key} = $value; 
                }
            } 
        }
    }
}
