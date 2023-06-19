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
    private string $pointer = '';
    private string $calls = [];
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

    public function call($sth, string $mode = 'single')
    {
        $this->calls = [];

        switch($sth)
        {
            case 'all':
                $this->calls = $this->list;
                break;
            case 'none-master': 
                $this->calls = $this->list;
                if($this->master) unset($this->calls[$this->master]);
                break;
            case 'only-master':
                if(!$this->callPlugin($this->master))
                {
                    Log::add('PluginManager: call master failed because there is no master plugin');
                    if(!$required) return false;
                    throw new Exception('Unavailable master plugin'); 
                }
                break;
            default:
                if(FncArray::isArrayString($sth))
                {
                    foreach($sth as $plgName)
                    {
                        if(!$this->callPlugin($plgName))
                        {
                            Log::add('PluginManager: unvailable plugin '.$plgName); 
                        }
                    }
                }
                if(is_string($sth))
                {
                    if(!$this->callPlugin($sth, $mode))
                    {
                        Log::add('PluginManager: unvailable plugin '.$sth); 
                    }
                }
                else
                {
                    Log::add('PluginManager: invalid plugin call'); 
                }
                break;
        }

        return $this;
    }

    private function callPlugin($pluginName, string $mode = 'single')
    {
        if(!isset($this->list[$pluginName])) return false;

        if($mode == 'single' || $mode == 'family')
        {
            $this->calls = [$pluginName =>$this->list[$pluginName]];
        }

        if($mode == 'children' || $mode == 'family')
        {
            $test = $pluginName. '_';
            foreach($this->list as $name => $plg)
            {
                if(strpos($name, $test) === 0)
                {
                    if(! $this->callPlugin($name, $mode) )
                    {
                        Log::add('Invalid plugin '. $name);
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function run(string $event, string $function, bool $required = false, $closure = null, bool $outputResult=false)
    {
        if(empty($this->calls))
        {
            if(!$required) continue;
            throw new Exception('Invalid plugin '. $name);
        }

        list($listName, $finalList) = $this->filterName($plugin);

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
                    
                }
            }
        }

        $this->calls = [];
    }

    private function runByList(string $event, string $function, $closure = null, $outputResult=false)
    {
        $event = ucfirst(strtolower($event));
        $results = $outputResult ? [] : true;

        foreach($this->calls as $plugin)
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

    private function filterName($plugin)
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
            if(strpos($plugin, 'child-') === 0)
            {
                $segment = substr($plugin, 5). '_';
                foreach($this->list as $name => $plg)
                {
                    if(strpos($name, $segment) === 0)
                    {
                        $finalList[$name] = $plg;
                    }
                }
            }
            elseif(strpos($plugin, 'family-') === 0)
            {
                $segment = substr($plugin, 6);
                foreach($this->list as $name => $plg)
                {
                    if($name == $segment || strpos($name, $segment. '_') === 0)
                    {
                        $finalList[$name] = $plg;
                    }
                }
            }
            else
            {
                $listName = [$plugin];
            }
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

        return [$listName, $finalList];
    }
}