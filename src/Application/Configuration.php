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

    public function __construct($default)
    {
        defined('SPT_CONFIG_PATH') or die('Configuration path not found');

        $this->_vars = [];
        $this->_default = $default;

        // TODO: consider file name / folder name as a scope of information
        if(is_file(SPT_CONFIG_PATH))
        {
            $this->import(SPT_CONFIG_PATH, $this);
        }
        elseif(is_dir(SPT_CONFIG_PATH))
        {
            foreach(new \DirectoryIterator(SPT_CONFIG_PATH) as $item) 
            {
                if (!$item->isDot())
                { 
                    if($item->isFile() && 'php' == $item->getExtension())
                    {
                        $this->import( SPT_CONFIG_PATH. '/'. $item->getBasename(), $this);
                    }
                    elseif($item->isDir())
                    {
                        $name =  $item->getBasename();
                        $this->{$name} = new MagicObj($default);
                        foreach(new \DirectoryIterator(SPT_CONFIG_PATH. '/'. $name) as $inner) 
                        {
                            if (!$inner->isDot() && $inner->isFile() && 'php' == $inner->getExtension())
                            {
                                $this->import(SPT_CONFIG_PATH. '/'. $name. '/'. $inner->getBasename(), $this->{$name});
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
        return isset($this->_envs[$key]) ? $this->_envs[$key] : $this->_default;
    }
}