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
use SPT\User\Instance as UserInstance; 

class Database extends Entity
{ 
    protected $table = 'spt_options';
    protected $pk = 'id';

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
                'pk' => 1
                'type' => 'int', 
                'option' => 'unsigned',
            ],
            'name' => [
                'type' => 'varchar',
                'limit' => 45,
            ], 
            'data'=> [
                'type' => 'text'
            ],
            'serialized' => [
                'type' => 'int',
                'limit' => 1,
            ]
        ];
    }

    public __set(string $name, mixed $value): void
    {
        if(is_array($value) || is_object($value))
        {
            $data = serialize($value);
            $serialized = 1;
        }
        else
        {
            $data = $value;
            $serialized = 0;
        }

        $this->db->update(['data'=>$data, 'serialized'=>$serialized], ['name'=>$name]);
    }

    public function __get($name)
	{
		if ($try = $this->db->findOne(['name'=>$name], 'data, datatype'))
		{
            return $try->serialized ? unserialize($try->data) : $try->data;
		}

        throw new Exception('Unknown Storage Property '.$name);
	}
}