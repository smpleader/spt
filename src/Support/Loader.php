<?php
/**
 * SPT software - Loader
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: support to load a class
 * 
 */

namespace SPT\Support;

class Loader
{
    public static function findClass($dir, $namespace='')
    {
        $tmp = [];
        if( is_dir($dir) )
        {
            $objects = scandir($dir);
            foreach ($objects as $x) 
            { 
                if ($x != '.' && $x != '..')
                {
                    if( is_dir($dir. '/'. $x) )
                    {
                        $tmp = array_merge( $tmp, static::findClass($dir. '/'. $x, $x));
                    }
                    elseif(!is_link($dir. '/'. $x) && '.php' == substr($x, -4))
                    {
                        $x = substr($x, 0, (strlen($x) - 4));
                        $tmp[] = empty( $namespace ) ? $x : $namespace. '\\'.$x;
                    }
                }
            }
        }
        
        return $tmp;
    }
}
