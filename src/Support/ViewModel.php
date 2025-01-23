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
    private static array $_list;
    private static array $_vms;

    public static function containerize(string $classname, string $fullname, string $alias = '')
    {
        $container = App::getInstance()->getContainer();
        $container->containerize(
            $classname. 'VM', 
            $fullname,
            function($fullname, $container) use ($classname)
            { 
                $vm = new $fullname($container);
                \SPT\Support\ViewModel::addVM($classname.'VM');
                return $vm;
            }, 
            $alias
        );
    }

    public static function addVM(string $name)
    {
        self::$_vms[] = $name;
    }

    public static function registerLayouts()
    {
        self::$_vms = array_unique(self::$_vms);
        $container = App::getInstance()->getContainer();
        foreach(self::$_vms as $vm)
        {
            $container->get($vm)->registerLayouts();
        }
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