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
    public static function findClass($dir, $namespace='', $callback = null, $nodes = [])
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
                        $nodes[] = $x;
                        $_namespace = empty( $namespace ) ? $x : $namespace. '\\'.$x;
                        $tmp = array_merge( $tmp, static::findClass($dir. '/'. $x, $_namespace, null, $nodes)); // we only callback after get all
                    }
                    elseif(!is_link($dir. '/'. $x) && '.php' == substr($x, -4))
                    {
                        $x = substr($x, 0, (strlen($x) - 4));
                        $name = empty( $namespace ) ? $x : $namespace. '\\'.$x;
                        $tmp[$name] = [$x, $nodes];
                    }
                }
            }
        }

        if($callback !== null && is_callable($callback))
        {
            foreach($tmp as $fullname=>$detail)
            {
                list($classname, $deep) = $detail;
                if(class_exists($fullname))
                {
                    $callback($classname, $fullname, $deep);
                }
            }
        }

        return $tmp;
    }
}
