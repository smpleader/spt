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
    protected $table = '#__spt_sitemap';
    protected $pk = 'id';
    protected $cache;

    public function __construct(Query $query, array $options = [])
    {
        $this->db = $query;

        if(isset($options['table']))
        {
            $this->table = $options['table'];
        }

        $this->checkAvailability();
    }

    public function getFields()
    {
        return [
            'id' => [
                'pk' => 1,
                'type' => 'bigint', 
                'extra' => 'auto_increment',
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
            'fnc' => [
                'type' => 'varchar',
                'limit' => 245,
            ],
            'page' => [
                'type' => 'varchar',
                'limit' => 45,
            ],
            'method' => [
                'type' => 'varchar',
                'limit' => 15,
            ],
            'object' => [
                'type' => 'varchar',
                'limit' => 145,
                // format: abc.id = table abc, column id
                // format: xyz.cat.12.id = table xyz, cat = 12, column id
            ],
            'object_id' => [
                'type' => 'bigint', 
                'option' => 'unsigned',
                'limit' => 20
            ], 
            'settings' => [
                'type' => 'text'
            ],
            'permission' => [
                'type' => 'text'
            ],
            'published' => [
                'type' => 'int',
                'limit' => 1,
            ]
        ];
    }

    public function endpointsFromArray(array $data)
    {
        $return = [];
        $endpoint = [
            'slug' => $this->getUniqueSlug($data['slug']),
            'title' => $data['title'],
            'plugin' => $data['plugin'],
            'settings' => '[]',
            'object_id' => 0,
            'object' => $data['object'],
            'permission' => '[]',
            'method' => 'GET',
            'page' => '',
            'published' => 1
        ];

        if(isset($data['page'])) $endpoint['page'] = $data['page'];
        if(isset($data['settings'])) $endpoint['settings'] = json_encode($data['settings']);
        if(isset($data['permission'])) $endpoint['permission'] = json_encode($data['permission']);

        if(is_array($data['fnc']))
        {
            foreach($data['fnc'] as $method => $fnc)
            {
                $endpoint['fnc'] = $fnc;
                $endpoint['method'] = strtolower($method);
                $return[] = $endpoint;
            }
        }
        else
        {
            $endpoint['fnc'] = $data['fnc'];
            $return[] = $endpoint;
        }

        return $return;
    }

    public function getUniqueSlug( string $slug, int $counter = 0)
    {
        $try = $this->findOne(['slug'=>$slug]);
        if(empty($try)) return $slug;

        return $this->getUniqueSlug($slug. '_'. $counter, $counter++);
    }
}