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

    public function __construct(string $pathConfig)
    {
        file_exists($pathConfig) or die('Configuration path not found');

        $this->_vars = [];
        $this->_path = $pathConfig;
        $this->import($pathConfig, $this);
    }

    public function import(string $path)
    {
        FncMagicObj::import($path, $this);
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

    public function getConfigPath()
    {
        return $this->_path;
    }
}