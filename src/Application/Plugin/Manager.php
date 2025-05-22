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
    protected array $list = [];
    protected array $alias = [];
    protected array $paths = [];
    protected string $master = '';
    protected string $message = '';
    protected array $calls = [];
    protected IApp $app;

    public function __construct(IApp $app, array $packages)
    {
        $this->app = $app;

        foreach($packages as $path=>$sth)
        {
            $id = '';
            $alias = [];
            if(is_array($sth))
            {
                if(!isset($sth['namespace']))
                {
                    $app->raiseError('Invalid package namespace '.$path);
                }

                $namespace = $sth['namespace'];

                if(isset($sth['id']))
                {
                    $id = $sth['id'];
                }

                if(isset($sth['alias']))
                {
                    $alias = $sth['alias'];
                }
            }
            elseif(is_string($sth))
            {
                $namespace = $sth;
            }
            else
            {
                $app->raiseError('Invalid package namespace '.$path);
            }
            
            if(!file_exists($path)) 
            {
                $app->raiseError('Invalid package '.$path);
            }

            if(count($alias))
            {
                foreach($alias as $v)
                {
                    if(isset($this->alias[$v]))
                    {
                        $app->raiseError('Duplicate alias "'. $v. '" of package '.$path);
                    }
                }
            }

            $this->add($id, $alias, $path, $namespace);
        }
    }

    protected function add(string $id, array $alias, string $path, string $namespace)
    {
        if('/' !== substr($path, -1))
        {
            $path .= '/';
        }
        
        // a solution
        if( file_exists($path. 'about.php') ) 
        {
            $parent = basename($path);
            foreach(new \DirectoryIterator($path) as $item) 
            {
                if (!$item->isDot() && $item->isDir())
                {
                    $name = $item->getBasename(); 
                    $id = empty($id) ? $parent. '/'. $name : $id. '/'. $name;
                    $this->add($id, [], $path. $name. '/', $namespace. '\\'. $name);
                }
            }
        }
        // a plugin
        elseif( file_exists($path. 'registers') && is_dir($path. 'registers') ) 
        {
            if(empty($id)) $id = basename($path);
            if(isset($this->list[$id]))
            {
                echo 'Warning: Plugin '.$id. ' already exists.';
            }
            else
            {
                $this->list[$id] = new Plugin($id, $path, $namespace);
                $this->paths[$id] = $path;
                if(count($alias))
                {
                    foreach($alias as $v)
                    {
                        $this->alias[$v] = $id;
                    }
                }
            }
        } 
    }

    public function call($sth, string $mode = 'single')
    {
        $this->calls = [];
        $this->message = '';

        switch($sth)
        {
            case 'all':
                $this->calls = $this->list;
                break;
            default:
                if(FncArray::isArrayString($sth))
                {
                    if($mode == 'tag')
                    {
                        //TODO: call plugin by tags
                        // sth == tag
                    }
                    else
                    {
                        foreach($sth as $plgName)
                        {
                            if(!$this->callPlugin($plgName))
                            {
                                Log::add('PluginManager: unvailable plugin '.$plgName); 
                            }
                        }
                    }
                }
                elseif(is_string($sth))
                {
                    if($mode == 'tag')
                    {
                        //TODO: call plugin by tags
                        // sth == tag
                    }
                    elseif(!$this->callPlugin($sth, $mode))
                    {
                        Log::add('PluginManager: unvailable plugin '. $sth. ' in mode '. $mode); 
                    }
                }
                else
                {
                    $this->message = 'PluginManager: invalid call ';
                }
                break;
        }

        return $this;
    }

    protected function hasTag(array $matchTags, string $pluginId)
    {
        // TODO: call plugin by tags 
        return false;
    }

    protected function callPlugin($pluginId, string $mode = 'single')
    {
        if(!isset($this->list[$pluginId]))
        {
            $this->message = 'Invalid plugin '. $pluginId;
            return false;
        }

        if($mode == 'single' || $mode == 'family')
        {
            $this->calls[$pluginId] = $this->list[$pluginId];
        }

        if($mode == 'children' || $mode == 'family')
        {
            $test = $pluginId. '_';
            foreach($this->list as $name => $plg)
            {
                if(strpos($name, $test) === 0)
                {
                    if(! $this->callPlugin($name) )
                    {
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
            return;
        }

        if($this->message && $required)
        {
            throw new Exception($this->message);
        }
 
        $event = ucfirst(strtolower($event));
        $results = $outputResult ? [] : true;

        foreach($this->calls as $id => $plugin)
        {
            $class = $plugin->getNamespace(). '\\registers\\'. $event;
            if(!method_exists($class, $function))
            {
                if(!$required) continue;
                throw new Exception('Invalid plugin '. $id. ' with '. $event. '.'. $function);
            }

            $result = $class::$function($this->app);
            if(null !== $closure && is_callable($closure))
            {
                $ok = $closure( $result );
                if(false === $ok && $required)
                {
                    throw new Exception('Callback failed with plugin '. $id. ' when call '. $event .'.' . $function);
                }

                if( $outputResult )
                {
                    $results[$id] = ['result'=>$result, 'afterCallback' =>$ok];
                }
            }
            else
            {   
                if( $outputResult )
                {
                    $results[$id] = ['result'=>$result];
                }
            }
        }

        return $results;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function getDetail(string $name)
    {
        if(isset($this->alias[$name]))
        {
            return $this->getDetail( $this->alias[$name]);
        }

        return isset($this->list[$name]) ? $this->list[$name] : false;
    }

    public function getPluginPaths(): array
    {
        return $this->paths;
    }
}