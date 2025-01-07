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

    public static function containerize(string $classname, string $fullname, ?string $alias, string $pluginId)
    {
        $container = App::getInstance()->getContainer();
        $container->containerize(
            $classname. 'VM', 
            $fullname,
            function($fullname, $container) use ($pluginId)
            { 
                $vm = new $fullname($container);
                if(method_exists($vm, 'registerLayouts'))
                {
                    $arr = $vm->registerLayouts();
                    foreach(['theme', 'layout', 'widget'] as $k)
                    {
                        if(isset($arr[$k]))
                        {
                            self::extractSettings($arr[$k], $pluginId. ':'. $k, $classname. 'VM');
                        }
                    }
                    // TODO: attach function to a layout
                    // https://www.php.net/manual/en/closure.bindto.php
                }

                return $vm;
            }, 
            $alias
        );
    }

    private static function add(string $id, string $vm, string $fnc)
    {
        if(isset(self::$_list[$id]))
        {
            self::$_list[$id][] = [$vm, $fnc];
        }
        else
        {
            self::$_list[$id] = [[$vm, $fnc]];
        }
    }

    public static function extractSettings($sth, string $token, string $vm)
    {
        if(is_string($sth))
        {
            $token .= ':'. $sth;
            self::add($token, $vm, $sth);
        }
        elseif(is_array($sth))
        {
            //if (count($array) == count($array, COUNT_RECURSIVE))
            if(is_array($sth[array_key_first($sth)])) 
            {
                foreach($sth as $tmp)
                { 
                    self::extractSettings( $tmp, $token, $vm);
                }
            }
            else
            {
                list($layout, $fnc) = $sth;
                self::add( $token. ':'. $layout, $vm, $fnc);
            }

        }
    }

    public static function getData(string $id, $extraData): array
    {
        $data = [];
        if(isset(self::$_list[$id]))
        {
            $container = App::getInstance()->getContainer();
            foreach(self::$_list[$id] as $tmp)
            {
                list($vm, $fnc) = $tmp;
                $ViewModel = $container->get($vm);

                if(!method_exists($ViewModel, $fnc))
                {
                    throw new \Exception('Invalid function '. $fnc. ' of ViewModel '.$vm);
                }

                $data = array_merge($data, $ViewModel->$fnc($data, $extraData));
            }
        }
        
        return $data;
    }
}