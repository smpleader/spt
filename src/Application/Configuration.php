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
 
use SPT\DynamicObj;

class Configuration
{
    protected DynamicObj $_vars;

    public function __construct(string $pathConfig = '')
    {
        '' == $pathConfig || file_exists($pathConfig) || die('Invalid configuration path '.$pathConfig);

        $this->_vars = new DynamicObj;
        
        if('' != $pathConfig) $this->importFile($pathConfig, $this->_vars);
    }

    private function importFile(string $path, &$_var)
    { 
        if( is_dir($path) )
        {
            foreach(new \DirectoryIterator($path) as $item) 
            {
                if($item->isDot()) continue;
                
                if($item->isDir())
                {
                    $name =  $item->getBasename();
                    $_var->{$name} = new DynamicObj();
                    $this->importFile($path. '/'. $name, $_var->{$name});
                }
                elseif($item->isFile())
                {
                    $name =  $item->getBasename('.php');
                    $_var->{$name} = new DynamicObj();
                    $this->import($item->getExtension(), $_var->{$name}, $path. '/'. $item->getBasename());
                }
            } 
        }
        elseif( file_exists($path) && is_file($path) )
        {
            $p = pathinfo($path);
            $this->import($p['extension'], $_var, $path);
        }
    }

    private function import(string $ext, DynamicObj &$_obj, string $path)
    {
        switch($ext)
        {
            case 'json':
                $try = file_get_contents($path);
                $try = json_decode($try);
                if(is_array($try) || is_object($try))
                {
                    foreach ($try as $key => $value) 
                    {
                        $_obj->{$key} = $value;
                    }
                }
                break;
            case 'ini':
                $try = parse_ini_file($path);
                foreach ($try as $key => $value) 
                {
                    $_obj->{$key} = $value;
                }
                break;
            case 'php':
                $try = require $path;
                if(is_array($try) || is_object($try))
                {
                    foreach ($try as $key => $value) {
                        if(!is_numeric($key))
                        {
                            $_obj->{$key} = $value; 
                        }
                    } 
                }
                break;
        }
    } 

    // Check key exists in the configuraton by token format a.b.c
    public function exists(string $key)
    {
        $tmp = explode('.', $key);

        $var = $this->_vars;

        foreach($tmp as $k)
        { 
            if( is_array($var))
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
    public function of(string $key, $default = NULL)
    {
        $tmp = explode('.', $key);

        $var = $this->_vars;

        foreach($tmp as $k)
        {
            if( is_array($var))
            {
                if(!isset($var[$k]))  return $default;
                $var = $var[$k];
            }
            elseif( is_object($var))
            {
                if( !isset($var->{$k}) ) return $default;
                $var = $var->{$k};
            }
            else return $default;
        }

        return $var;
    }
}