<?php
/**
 * SPT software - Plugin loader
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A plugin supporter
 * @version: 0.8
 * 
 */

namespace SPT\Application\Plugin;

use SPT\Application\IApp;
use SPT\Log;
use SPT\Support\FncArray;
use \Exception;

class Manager
{
    private array $list = [];
    private string $master = '';
    private IApp $app;

    public function __construct(IApp $app)
    {
        $this->app = $app;

        $arr = [];
        if(is_array($app->cf('activePlugins')))
        {
            $arr = $app->cf('activePlugins');
        }

        if($app->cf('master')) $this->master = $app->cf('master');

        foreach(new \DirectoryIterator(SPT_PLUGIN_PATH) as $item) 
        {
            if (!$item->isDot() && $item->isDir())
            {
                $namespace = $app->getNamespace(). '\\plugins\\'. $item->getBasename(). '\\registers';
                if(empty($this->master))
                {
                    $installer = $namespace. '\Installer';
                    if(true === $installer::info('master'))
                    {
                        $this->master = $item->getBasename();
                    }
                }

                if(empty($arr) || in_array($item->getBasename(), $arr) || $this->master == $item->getBasename())
                {
                    $this->list[$item->getBasename()] = $namespace;
                }
            }
        }
    }

    public function run($plugin, string $event, string $function, bool $required = false, $closure = null, bool $outputResult=false)
    {
        $listName = [];
        $finalList = [];
        if(is_null($plugin))
        {
            $finalList = $this->list;
        }
        elseif('none-master' === $plugin)
        {
            $finalList = $this->list;
            if($this->master) unset($finalList[$this->master]);
        }
        elseif('only-master' === $plugin)
        {
            if($this->master)
            {
                $finalList = [$this->master =>$this->list[$this->master]];
            }
            else
            {
                if(!$required) return false;
                throw new Exception('Unavailable master plugin'); 
            }
        }
        elseif(is_string($plugin))
        {
            $listName = [$plugin];
        }
        elseif(FncArray::isArrayString($plugin))
        {
            $listName = $plugin;
        }
        else
        {
            if(!$required) return false;
            throw new Exception('Invalid plugin array'); 
        }

        if(count($listName))
        {
            foreach($listName as $name)
            {
                if(isset($this->list[$name]))
                {
                    $finalList[$name] = $this->list[$name];
                }
                else
                {
                    if(!$required) continue;
                    throw new Exception('Invalid plugin '. $name);
                }
            }
        }

        $event = ucfirst(strtolower($event));
        $results = $outputResult ? [] : true;

        foreach($finalList as $plugin)
        {
            $class = $plugin. '\\'. $event;
            if(!method_exists($class, $function))
            {
                if(!$required) continue;
                throw new Exception('Invalid plugin '. $plugin. ' with '. $event. '.'. $function);
            }

            $result = $class::$function($this->app);
            if(null !== $closure && is_callable($closure))
            {
                $ok = $closure( $result );
                if(false === $ok && $required)
                {
                    throw new Exception('Callback failed with plugin '. $plugin. ' when call '. $event .'.' . $function);
                }

                if( $outputResult )
                {
                    $results[$plugin] = ['result'=>$result, 'afterCallback' =>$ok];
                }
            }
            else
            {   
                if( $outputResult )
                {
                    $results[$plugin] = ['result'=>$result];
                }
            }
        }

        return $results;
    }
}