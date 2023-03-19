<?php
/**
 * SPT software - Asset
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An application context
 * 
 */

namespace SPT\Application\Simple;
 
use SPT\Router\ArrayEndpoint as Router;
use SPT\Request\Base as Request;
use SPT\Response;

class Cli extends \SPT\Application\Core
{
    protected function prepareEnvironment()
    {
        // secrect key
        // terminal or router
        $this->request = new Request();
        // setup container
    } 

    public function execute(string $themePath = '')
    {
        if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			exit(0);
		}

        // load CommandLine to start the work
        $this->loadPlugins('cli', 'registerCommands');
    }
}