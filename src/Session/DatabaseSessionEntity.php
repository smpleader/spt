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

use SPT\Entity;
use SPT\Query; 
use SPT\User\Instance UserInstance;
use SPT\Session\Adapter SessionAdapter;

class DatabaseSessionEntity extends Entity
{ 
    protected $table = 'spt_sessions';
    protected $pk = 'session_id';

    public function __construct(Query $query, UserInstance $user, array $options = [])
    {
        $this->db = $query;
        $this->user = $user;

        if(isset($options['table']))
        {
            $this->table = $options['table'];
        }

        if(isset($options['pk']))
        {
            $this->pk = $options['pk'];
        }

        $this->db->checkAvailability();
    }

    public function getFields()
    {
        return [
            $this->pk => [
                'type' => 'varbinary', 
                'length' => 192,
            ],
            'time' => [
                'type' => 'int',
                'limit' => 11,
            ],
            'username' => [
                'type' => 'varchar',
                'limit' => 150,
            ],
            'user_id' => [
                'type' => 'int',
                'option' => 'unsigned',
            ],
            'data'=> [
                'type' => 'text'
            ],
        ];
    }

    public function getRow($isArray = true)
    {
        $row = $this->db->table( $this->table )->detail([ $this->pk => $this->user->id()]);
        return $isArray ? (array) $row : $row;
    }
}