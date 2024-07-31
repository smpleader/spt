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

use SPT\Storage\DB\Entity;
use SPT\Query; 
use SPT\Session\Adapter as SessionAdapter;

class DatabaseSessionEntity extends Entity
{ 
    protected string $tableName = '#__spt_sessions';
    protected string $pk = 'session_id';
    protected $user;

    public function __construct(Query $query, array $options = [])
    {
        $this->db = $query;

        if(isset($options['tableName']))
        {
            $this->tableName = $options['tableName'];
        }

        if(isset($options['user']))
        {
            $this->user = $options['user'];
        }

        if(isset($options['pk']))
        {
            $this->pk = $options['pk'];
        }

        $this->checkAvailability();
    }

    public function getFields()
    {
        return [
            $this->pk => [
                'type' => 'varbinary',
                'limit' => 192,
            ],
            'created_at' => [
                'type' => 'int',
                'option' => 'unsigned',
                'length' => 10,
            ],
            'modified_at' => [
                'type' => 'int',
                'option' => 'unsigned',
                'length' => 10,
            ],
            'username' => [
                'type' => 'varchar',
                'null' => 'YES',
                'limit' => 150,
            ],
            'user_id' => [
                'type' => 'int',
                'option' => 'unsigned',
                'null' => 'YES'
            ],
            'data'=> [
                'type' => 'mediumtext'
            ],
        ];
    }
}