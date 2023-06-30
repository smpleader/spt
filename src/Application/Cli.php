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
use SPT\Request\Base as Request;

class Cli extends Web
{
    public function envLoad()
    {
        // setup container
        $this->container->set('app', $this);
        // create request
        $this->request = new Request(); 
        $this->container->set('request', $this->request);
        // access to app config 
        $this->container->set('config', $this->config);
    }

    public function execute(string $themePath = '')
    {
        if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			exit(0);
		}

        $commands = [];
        // load CommandLine to start the work
        $this->plgManager->call('all')->run('cli', 'registerCommands', false, function ($items) use (&$commands){
            $commands = array_merge($commands, $items);
        });

        $args = $this->request->cli->getArg();
        if (!$args)
        {
            $this->raiseError('Invalid Command Line');
        }

        $exec = $args[0];
        $todo = $commands[$exec];
        if (!$todo)
        {
            $this->raiseError('Invalid Command Line');
        }

        $try = explode('.', $todo);
            
        if(count($try) !== 3)
        {
            $this->raiseError('Not correct routing');
        } 
        
        list($plugin, $controller, $function) = $try;
        $plugin = strtolower($plugin);
        $this->set('currentPlugin', $plugin);
        $this->set('controller', $controller);
        $this->set('function', $function);

        return $this->plgManager->call($plugin)->run('Dispatcher', 'dispatch', true);
    }
}