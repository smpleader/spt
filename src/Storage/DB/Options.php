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

class Options extends Entity
{ 
    protected $tableName = '#__spt_options';
    protected $pk = 'id';
    protected $cache;

    public function getFields()
    {
        return [
            $this->pk => [
                'pk' => 1,
                'type' => 'bigint', 
                'option' => 'unsigned',
                'limit' => 20
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

    public function __set(string $name, mixed $value): void
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

        $this->table->updateIfExist(['data'=>$data, 'serialized'=>$serialized], ['name'=>$name]);

        $this->cache[$name] = $value;
    }

    public function __get($name)
	{
        if(isset($this->cache[$name])) return $this->cache[$name];

		if ($try = $this->table->findOne(['name'=>$name], 'data, datatype'))
		{
            $this->cache[$name] = $try->serialized ? unserialize($try->data) : $try->data;
            return $this->cache[$name];
		}

        throw new \Exception('Unknown Storage Property '.$name);
	}
}