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

    public function execute()
    {
        defined('APP_PATH') || die('App did not get setup constants');

        try{
            // create config
            $this->config = new SPT\Storage\FileArray(APP_PATH_CONFIG);
            // create request
            $this->request = new SPT\Request\Base();
            // create router
            $this->router = new SPT\Route($this->config->endpoints, $this->config->sitepath);
            // create query
            $this->query = new SPT\Query(
                new SPT\Extend\Pdo(
                    $this->config->db['host'],
                    $this->config->db['username'],
                    $this->config->db['passwd'],
                    $this->config->db['database'],
                    [],
                    $this->config->db['debug']
                ), ['#__'=>  $this->config->db['prefix']]
            );
            // create session
            $this->session = new SPT\Session\PhpSession();
            // process app
            $this->MVC();
        }
        catch (Exception $e) 
        {
            $this->set('system-warning', 'Caught exception: ',  $e->getMessage(), "\n");
            return $this->warning();
        }

        return $this;
    }

    public function MVC()
    {
        
    }
    
}
