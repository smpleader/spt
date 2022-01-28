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

class WebApp extends Application
{
    public function redirect($url = null)
    {
        $msg = $this->get('message', '');
        if( !empty($msg) )
        {
            $this->session->set('flashMsg', $msg);
        }
        
        parent::redirect($url);
        exit(0);
    }

    public function MVC()
    {

    }
}
