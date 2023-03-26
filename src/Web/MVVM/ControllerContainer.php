<?php
/**
 * SPT software - Controller
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a core controller
 * 
 */

namespace SPT\Web\MVVM;

use SPT\Application\IApp;
use SPT\Container\Client;   

class ControllerContainer extends Client
{
    use ControllerTrait; 

    public function __construct(IApp $app)
    {
        $this->app = $app; 
        $this->setContainer($app->getContainer());
    }
}