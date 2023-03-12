<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An application context
 * 
 */

namespace SPT;

class Application
{
    private $namespace;
    public function __construct(string $pluginPath, string $configPath = '', string $namespace = '')
    {
        $this->namespace = empty($namespace) ? __NAMESPACE__ : $namespace;

        $this->prepareEnvironment();
        $this->loadConfig($configPath); 
        $this->loadPlugins($pluginPath);
        return $this;
    }

    private function prepareEnvironment()
    {
        // secrect key
        // terminal or router
        // setup container 
    }

    private function loadConfig(string $configPath = '')
    {
        if( !empty( $configPath) )
        {
            $try = require_once($configPath);
            foreach($try as $K=>$V) $this->set($K, $V);
        }
    }

    private function loadPlugins(string $pluginPath)
    {
        // TODO cache this + load from db
        foreach(new DirectoryIterator($pluginPath) as $item) {
            if (!$item->isDot() && $item->isDir()) { 
                // echo $item->getFilename();
                $plgRegister = $this->namespace. '\\'. $item->getBasename(). '\\Register';
                if(class_exists($plgRegister))
                {
                    $plgRegister::bootstrap($this);
                }
            }
        }
    }

    // loval variables
    private array $_vars = [];
    public function get($key)
    {
        return isset($_vars[$key]) ? $_vars[$key] : null; // ah ha, just avoid warning
    }

    // Just set once
    public function set($key, $value)
    {
        if(!isset($_vars[$key]))
        {
            $_vars[$key] = $value;
        }
    }

    public function executeCommandLine(string $templatePath = '')
    {
        if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			exit(0);
		}

        // load CommandLine to start the work
    }

    public function runWebApp(string $templatePath)
    {
        return 'It works';
    }
}