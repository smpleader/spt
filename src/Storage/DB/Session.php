<?php
/**
 * SPT software - PHP Session
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: PHP Session
 * 
 */

namespace SPT\Storage\DB;

use SPT\Query;

class Session extends Entity
{ 
    protected string $tableName = '#__spt_sessions';
    protected string $pk = 'session_id';
    protected bool $skipPkWhenInsert=true; 

    public function __construct(Query $query, array $options = [])
    {
        $this->query = $query;

        if(isset($options['tableName']))
        {
            $this->tableName = $options['tableName'];
        }

        if(isset($options['pk']))
        {
            $this->pk = $options['pk'];
        }

        $this->table = $this->query->getActiveRecord($this->tableName);
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