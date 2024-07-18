<?php
/**
 * SPT software - CLI application
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An application for CLI
 * @version 0.8
 * 
 */

namespace SPT\Application;

use SPT\Application\Plugin\Manager;
use SPT\Request\Singleton as Request;

class Cli extends Base
{
    private $_commands;

    public function envLoad()
    {
        // setup container
        $this->container->set('app', $this);

        // private properties
        parent::envLoad();

        // create request
        $this->request = Request::instance(); 
        if( !$this->container->exists('request') )
        {
            $this->container->set('request', $this->request);
        }
        
        // access to app config 
        if( !$this->container->exists('config') )
        {
            $this->container->set('config', $this->config);
        }

        // token
        if( !$this->container->exists('token') )
        {
            $this->container->set('token', new Token($this->config, $this->request));
        }
    }

    private function getCLICommands()
    {
        if(null === $this->_commands)
        {
            $commands = [];
            
            $this->plgManager->call('all')->run('cli', 'registerCommands', false, function (array $items) use (&$commands){
                foreach( $items as $key=>$item)
                {
                    if(!array_key_exists($key, $commands))
                    {
                        $commands[$key] = $item;
                    }
                }
            });

            $this->_commands = $commands;
        }

        return $this->_commands;
    }

    public function execute(string | array $_parameters = [])
    {
        if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			exit(0);
		}

        $this->getCLICommands();

        if( is_string($_parameters) )
        {
            $todo = explode('.', $_parameters);
            $siteParams = [];
        }
        elseif(isset($_parameters['fnc']))
        {
            $todo = $_parameters['fnc'];
            unset($_parameters['fnc']);
            $siteParams = $_parameters;
        }
        else
        {
            $args = $this->request->cli->getArgs();
            if (!$args)
            {
                $this->raiseError('Invalid Parameter');
            }

            $exec = $args[0];
            if ($exec == 'help' || $exec == 'h' )
            {
                echo $this->getCommandHelp();
                exit(0);
            }
       
            if (!isset($this->_commands[$exec]))
            {
                $this->raiseError('Invalid Command');
            }
    
            $cmd = $this->_commands[$exec];

            if( is_array($cmd) )
            {
                $todo = $cmd['fnc'];
                unset($cmd['fnc']);
                unset($cmd['description']);
                $siteParams = $cmd;
            }
            elseif( is_string($cmd) )
            {
                $todo = $cmd;
                $siteParams = [];
            }
        }

        $try = explode('.', $todo);

        if(count($try) !== 3)
        {
            $this->raiseError('Not correct routing');
        } 
        
        list($pluginName, $controller, $function) = $try;

        $plugin = $this->plgManager->getDetail($pluginName);

        if(false === $plugin)
        {
            $this->raiseError('Invalid plugin '.$pluginName, 500);
        }
            
        if(count($siteParams))
        {
            foreach($siteParams as $key => $value)
            {
                $this->set($key, $value);
            }
        }

        $this->set('mainPlugin', $plugin);
        $this->set('controller', $controller);
        $this->set('function', $function);

        return $this->plgManager->call($pluginName)->run('Dispatcher', 'terminal', true);
    }

    public function getCommandHelp($asString=true)
    {
        $arr = ["All the commands:\n"];
        $count = 1;
        foreach($this->_commands as $key=>$cmd)
        {
            if(isset($cmd['description']))
            {
                $arr[] = $count. " - " . $key .": ". $cmd['description'] ."\n";
                $count++;
            }
        }

        $arr[] = $count." - help: List all commands\n";

        return $asString ? implode($arr) : $arr;
    }

    public function raiseError(string $msg, $code = 500)
    {
        echo $msg ."\n";
        exit(0);
    }
}
