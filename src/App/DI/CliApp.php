<?php
/**
 * SPT software - Application Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Application Adapter
 * 
 */

namespace SPT\App;

use SPT\BaseObj;

class CliApp extends Application
{
    public function redirect($url = null)
    {
        // debug $this->get('redirect', '/');
        // debug $this->get('redirectStatus', '302');
    }

    public function execute()
    {
        if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			exit(0);
		}

        parent::execute();
    }
}
