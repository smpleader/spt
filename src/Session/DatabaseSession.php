<?php
/**
 * SPT software - PHP Session
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Database Session
 * 
 */

namespace SPT\Session;

use SPT\Session\Adapter as SessionAdapter;
use SPT\Query;
use SPT\Storage\DB\Session as SessionEntity;

class DatabaseSession implements SessionAdapter
{
    private $session = array();
    private $session_id = false;
    private SessionEntity $entity;

    public function __construct(SessionEntity $entity, string $session_id)
    {
        $this->entity = $entity;
        $this->session_id = $session_id;
        $this->reload();
    }

    public function reload($username = false, $userid = 0)
    {
        $data = $this->entity->findOne( ['session_id' =>  $this->session_id]);
        $this->session = $data ? json_decode($data['data'], true) : [];
        
        if(empty($data))
        {
            $this->entity->add( [
                'session_id' =>  $this->session_id,
                'created_at' => strtotime("now"),
                'modified_at' => strtotime("now"),
                'data' => '',
                'username' => $username,
                'user_id' => $userid 
            ]);
        }
        elseif( false !== $username )
        {
            $this->entity->update([
                'modified_at' => strtotime("now"),
                'username' => $username,
                'user_id' => $userid 
            ], ['session_id' =>  $this->session_id]);
        }
    }

    public function get(string $key, $default = null)
    {
        if('_logs' == $key) return $this->entity->query->getLog();
        return isset($this->session[$key]) ? $this->session[$key] : $default;
    }
    
    public function set(string $key, $value)
    {
        $this->session[$key] = $value;
        $try = $this->entity->update([
            'modified_at' => strtotime("now"),
            'data' => json_encode($this->session),
        ], ['session_id' =>  $this->session_id]);
    }

    public function id()
    {
        return $this->session_id;
    }
}