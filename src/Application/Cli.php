<?php
/**
 * SPT software - SPT application for CLI
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A CLI application based SPT framework
 * @version 0.8
 * 
 */

namespace SPT\Application;

use SPT\Application\Plugin\Manager;
use SPT\Request\Singleton as Request;

class Cli extends Base
{
    private $_commands;

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
            $todo = $_parameters;
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
                $this->raiseError('No Command Found');
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
                /**
                 * TODO: filter commands based script name $_SERVER['SCRIPT_FILENAME'] | bypass key | group
                 */
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

        if(count($siteParams))
        {
            foreach($siteParams as $key => $value)
            {
                $this->set($key, $value);
            }
        }
        
        list($pluginName, $controller, $function) = $try;

        $this->set('controller', $controller);
        $this->set('function', $function);
        
        $this->prepareDispatch($pluginName);

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
        $this->set('error', $msg);
        $this->set('errorCode', $code);
        $this->set('env', 'cli');

        $this->plgManager->call('all')->run('Error', 'catch', false);

        // if no plugin handle this error, just stop
        echo $msg ."\n";
        exit(0);
    }
}
