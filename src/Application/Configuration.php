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
        $this->import(SPT_CONFIG_PATH, $this);
    }

    private function import(string $path, &$_var)
    {
        if(is_file($path))
        {
            $try = require_once $path;
            if(is_array($try))
            {
                foreach ($try as $key => $value) {
                    if(!is_numeric($key))
                    {
                        $_var->{$key} = $value;
                    }
                } 
            }
        }
        elseif(is_dir($path))
        {
            foreach(new \DirectoryIterator($path) as $item) 
            {
                if (!$item->isDot())
                {
                    if($item->isFile() && 'php' == $item->getExtension())
                    {
                        $name =  $item->getBasename('.php');
                        $_var->{$name} = new MagicObj($this->_default);
                        $this->import( $path. '/'. $item->getBasename(), $_var->{$name});
                    }
                    elseif($item->isDir())
                    {
                        $name =  $item->getBasename();
                        $_var->{$name} = new MagicObj($this->_default);
                        $this->import( $path. '/'. $item->getBasename(), $_var->{$name});
                    }
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