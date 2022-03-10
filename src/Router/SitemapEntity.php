<?php
/**
 * SPT software - PHP Session
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: PHP Session
 * 
 */

namespace SPT\Router;

use SPT\Query; 
use SPT\Storage\DB\Entity;

class SitemapEntity extends Entity
{ 
    protected $table = 'spt_sitemap';
    protected $pk = 'id';
    protected $cache;

    public function __construct(Query $query, array $options = [])
    {
        $this->db = $query;

        if(isset($options['table']))
        {
            $this->table = $options['table'];
        }

        $this->db->checkAvailability();
    }

    public function getFields()
    {
        return [
            'id' => [
                'pk' => 1,
                'type' => 'bigint', 
                'option' => 'unsigned',
                'limit' => 20
            ],
            'slug' => [
                'type' => 'varchar',
                'limit' => 245,
            ], 
            'title' => [
                'type' => 'varchar',
                'limit' => 245,
            ], 
            'plugin' => [
                'type' => 'varchar',
                'limit' => 245,
            ], 
            'func' => [
                'type' => 'varchar',
                'limit' => 245,
            ],
            'table' => [
                'type' => 'varchar',
                'limit' => 145,
            ], 
            'tablepk' => [
                'type' => 'varchar',
                'limit' => 145,
            ], 
            'oid' => [
                'type' => 'bigint', 
                'option' => 'unsigned',
                'limit' => 20
            ], 
            'settings' => [
                'type' => 'text'
            ],
            'published' => [
                'type' => 'int',
                'limit' => 1,
            ]
        ];
    }
}