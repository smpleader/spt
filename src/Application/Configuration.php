<?php
/**
 * SPT software - Application configuration
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A class to manage configuration 
 * @version: 0.8
 * 
 */

namespace SPT\Application;

use SPT\MagicObj;

class Configuration extends MagicObj
{
    /**
     * Internal array for environments
     * @var array $_envs
     */
    protected $_envs = [];

    /**
     * Internal path for environments
     * @var array $_envs
     */
    protected $_path = '';

    public function __construct(string $pathConfig)
    {
        file_exists($pathConfig) or die('Configuration path not found');

        $this->_vars = [];
        $this->_path = $pathConfig;

        // TODO: consider file name / folder name as a scope of information
        if(is_file($this->_path))
        {
            $this->import($this->_path, $this);
        }
        elseif(is_dir($this->_path))
        {
            foreach(new \DirectoryIterator($this->_path) as $item) 
            {
                if (!$item->isDot())
                { 
                    if($item->isFile() && 'php' == $item->getExtension())
                    {
                        $this->import( $this->_path. '/'. $item->getBasename(), $this);
                    }
                    elseif($item->isDir())
                    {
                        $name =  $item->getBasename();
                        $this->{$name} = new MagicObj();
                        foreach(new \DirectoryIterator($this->_path. '/'. $name) as $inner) 
                        {
                            if (!$inner->isDot() && $inner->isFile() && 'php' == $inner->getExtension())
                            {
                                $this->import($this->_path. '/'. $name. '/'. $inner->getBasename(), $this->{$name});
                            }
                        }
                    }
                }
            }
        }
    }

    private function import(string $path, &$b)
    {
        $try = require_once $path;
        if(is_array($try) || is_object($try))
        {
            foreach ($try as $key => $value) {
                if(!is_numeric($key))
                {
                    $b->{$key} = $value;
                }
            } 
        }
    }

    public function exists($key)
    {
        return isset($this->_vars[$key]);
    }

    public function empty($key)
    {
        return empty($this->_vars[$key]);
    }
    
    public function setEnv(string $path)
    {
        $configs = parse_ini_file($path); 
        if( count($configs) && is_array($configs))
        {
            $this->_envs = $configs;
        }
    }

    public function env($key)
    {
        return isset($this->_envs[$key]) ? $this->_envs[$key] : null;
    }
}