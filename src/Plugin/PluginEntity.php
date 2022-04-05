<?php
/**
 * SPT software - PHP Session
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: PHP Session
 * 
 */

namespace SPT\Plugin;

use  SPT\Storage\DB\Entity;

class PluginEntity extends Entity
{ 
    protected $table = '#__plugin';
    protected $pk = 'id';

    public function getFields()
    {
        return [
            $this->pk => [
                'type' => 'int', 
                'pk' => 1,
                'extra' => 'auto_increment',
                'option' => 'unsigned',
            ],
            'name' => [
                'type' => 'varchar',
                'limit' => 255,
            ],
            'title' => [
                'type' => 'varchar',
                'limit' => 255,
            ],
            'version' => [
                'type' => 'varchar',
                'limit' => 15,
            ],
            'schema_version' => [
                'type' => 'varchar',
                'limit' => 15,
            ],
            'active' => [
                'type' => 'tinyint'
            ],
            'settings' => [
                'type' => 'text',
                'null' => 'YES',
            ],
        ];
    }

}