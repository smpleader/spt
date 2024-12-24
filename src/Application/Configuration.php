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
use SPT\Support\FncMagicObj;

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

    public function __construct(string $pathConfig = '')
    {
        '' == $pathConfig || file_exists($pathConfig) || die('Invalid configuration path '.$pathConfig);

        $this->_vars = [];
        $this->_path = $pathConfig;
        if('' != $pathConfig) $this->import($pathConfig, $this);
    }

    public function import(string $path)
    {
        FncMagicObj::import($path, $this);
    }

    // Check key exists in the configuraton by token format a.b.c
    public function exists(string $key)
    {
        $tmp = explode('.', $key);

        $var = $this;

        foreach($tmp as $k)
        {
            if($var instanceof MagicObj)
            {
                if(!$var->isset($k)) return false;
                $var = $var->{$k};
            }
            elseif( is_array($var))
            {
                if(!isset($var[$k]))  return false;
                $var = $var[$k];
            }
            elseif( is_object($var))
            {
                if( !isset($var->{$k}) ) return false;
                $var = $var->{$k};
            }
            else return false;
        }
        
        return  true;
    } 

    // Get value of configuraton by key token format a.b.c
    public function of(string $key)
    {
        $tmp = explode('.', $key);

        $var = $this;

        foreach($tmp as $k)
        {
            if($var instanceof MagicObj)
            {
                if(!$var->isset($k)) return NULL;
                $var = $var->{$k};
            }
            elseif( is_array($var))
            {
                if(!isset($var[$k]))  return NULL;
                $var = $var[$k];
            }
            elseif( is_object($var))
            {
                if( !isset($var->{$k}) ) return NULL;
                $var = $var->{$k};
            }
            else return NULL;
        }

        return $var;
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

    public function getConfigPath()
    {
        return $this->_path;
    }
}