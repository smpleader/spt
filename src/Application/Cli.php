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

class Cli extends Web
{
    private $_commands;

    public function envLoad()
    {
        // setup container
        $this->container->set('app', $this);
        // create request
        $this->request = Request::instance(); 
        $this->container->set('request', $this->request);
        // access to app config 
        $this->config = new Configuration(null);
        $this->container->set('config', $this->config);
        // load packages
        $this->plgManager = new Manager(
            $this,
            $this->packages
        );
    }

    public function execute(string $themePath = '')
    {
        if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			exit(0);
		}

        $commands = [];
        // load CommandLine to start the work
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

        $args = $this->request->cli->getArgs();
        if (!$args)
        {
            $this->raiseError('Invalid Command Line');
        }

        $exec = $args[0];
        if ($exec == '--help' || $exec == '-h' )
        {
            echo $this->getCommandHelp();
            exit(0);
        }

        $todo = isset($this->commands[$exec]) ? $this->commands[$exec] : '';
       
        if (!$todo)
        {
            $this->raiseError('Invalid Command Line');
        }

        if(is_array($todo))
        {
            $try = explode('.', $todo['fnc']);
        }
        else
        {
            $try = explode('.', $todo);
        }

        if(count($try) !== 3)
        {
            $this->raiseError('Not correct routing');
        } 
        
        list($plugin, $controller, $function) = $try;
        $plugin = strtolower($plugin);
        $this->set('currentPlugin', $plugin);
        $this->set('controller', $controller);
        $this->set('function', $function);

        return $this->plgManager->call($plugin)->run('Dispatcher', 'terminal', true);
    }

    public function getCommandHelp()
    {
        $commands = $this->commands;

        $arr = ["All the commands:\n"];
        foreach($this->commands as $key=>$cmd)
        {
            if(isset($cmd['description']))
            {
                $arr[] = "\t" . $key ."\t". "\t". $cmd['description'] ."\n";
            }
        }

        $arr[] = "\t --help\t". "\t List all commands\n";

        return $arr;
    }

    public function raiseError(string $msg, $code = 500)
    {
        echo $msg ."\n";
        exit(0);
    }
}
