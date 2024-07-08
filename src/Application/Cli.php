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
use SPT\Request\Singleton as Request;

class Cli extends Web
{
    private $commands;

    public function envLoad()
    {
        $this->config = new Configuration(null);
        $this->plgManager = new Manager(
            $this,
            $this->packages
        );
        
        // setup container
        $this->container->set('app', $this);
        // create request
        $this->request = Request::instance(); 
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

        $this->commands = $commands;

        $args = $this->request->cli->getArgs();
        if (!$args)
        {
            $this->raiseError('Invalid Command Line');
        }

        $exec = $args[0];
        if ($exec == '--help')
        {
            return $this->commandHelp();
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

    public function commandHelp()
    {
        $commands = $this->commands;
        $commands['--help'] = [
            'description' => 'Information commands',
        ];

        echo "Command Helper:\n";
        foreach($commands as $key => $command)
        {
            $description = is_array($command) ? $command['description'] : '';
            
            echo "\t" . $key ."\t". "\t". $description ."\n";
        }
    }

    public function raiseError(string $msg, $code = 500)
    {
        echo $msg ."\n";
        exit(0);
    }
}
