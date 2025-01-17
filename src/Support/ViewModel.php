<?php
/**
 * SPT software - Support/Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: comfortable way to use Model
 * 
 */

namespace SPT\Support;

use SPT\Support\App;
use SPT\Support\Loader;

class ViewModel
{
    private static $_list;

    public static function containerize(string $classname, string $fullname, ?string $alias)
    {
        $container = App::getInstance()->getContainer();
        $container->containerize(
            $classname. 'VM', 
            $fullname,
            function($fullname, $container)
            { 
                $vm = new $fullname($container);
                $vm->registerLayouts();

                return $vm;
            }, 
            $alias
        );
    }

    public static function add(string $layoutId, string $vm, string $fnc)
    {
        if(isset(self::$_list[$layoutId]))
        {
            self::$_list[$layoutId][] = [$vm, $fnc];
        }
        else
        {
            self::$_list[$layoutId] = [[$vm, $fnc]];
        }
    }

    public static function getData(string $layoutId, array $extraData): array
    {
        $data = $extraData;
        if(isset(self::$_list[$layoutId]))
        {
            $container = App::getInstance()->getContainer();
            
            foreach(self::$_list[$layoutId] as $tmp)
            {
                list($vm, $fnc) = $tmp;
                $ViewModel = $container->get($vm);

                if(!method_exists($ViewModel, $fnc))
                {
                    throw new \Exception('Invalid function '. $fnc. ' of ViewModel '.$vm);
                }

                $try = $ViewModel->$fnc($data, $extraData);
                if(is_array($try))
                {
                    $data = array_merge($data, $try);
                }
            }
        }
        
        return $data;
    } 
}