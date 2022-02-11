<?php
/**
 * SPT software - PHP Session
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: PHP Session
 * 
 */

namespace SPT\Session;

use SPT\Session\Adapter as SessionAdapter;
use SPT\Util;
use SPT\Query;
use SPT\Request\Base as Request;

class DatabaseSession implements SessionAdapter
{
    private $session = array();
    private $session_id = '';
    private $table;

    public function __construct(DatabaseSessionEntity $table)
    {
        $this->table = $table;
        $request = new Request();
        $browser = $request->server->get('HTTP_USER_AGENT', '');

        $cli = $request->server->get('argv', '') ? true : false;
        $cookie = $request->cookie->get('sid', '');
        if (!$cookie)
        {
            $cookie = rand();
            $request->cookie->set('sid', $cookie, $cli);
        }

        $ip = Util::getClientIp();
        
        $this->session_id = md5($ip . $browser . $cookie);
        $this->user = (object) [];
        $this->user->id = $this->session_id;
        $this->reload();
    }

    public function reload()
    {
        $data = $this->table->getRow( ['session_id' =>  $this->session_id]);
        $this->session = $data ? (array) json_decode($data['data']) : [];
        
        if(empty($this->session))
        {
            $this->table->add( [
                'session_id' =>  $this->session_id,
                'time' => strtotime("now"),
                'user_id' => 1,
                'username' => '',
                'data' => '',
            ]);
        }
    }

    public function get(string $key, $default = null)
    {
        if('_logs' == $key) return $this->table->logs();
        return isset($this->session[$key]) ? $this->session[$key] : $default;
    }
    
    public function set(string $key, $value)
    {
        $this->session[$key] = $value;

        $try = $this->table->update([
            'time' => strtotime("now"),
            'user_id' => 1,
            'username' => '',
            'data' => json_encode($this->session),
        ], ['session_id' =>  $this->session_id]);
    }

}