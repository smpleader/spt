<?php
/**
 * SPT software - PHP Session
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: PHP Session
 * 
 */

namespace SPT\User\SPT;

use SPT\Storage\DB\Entity;
use SPT\Query;

class UserGroupEntity extends Entity
{
    protected $table = '#__user_usergroup_map';
    protected $pk = 'id';

    public function getFields()
    {
        return [
                'id' => [
                    'type' => 'int',
                    'pk' => 1,
                    'option' => 'unsigned',
                    'extra' => 'auto_increment',
                ],
                'user_id' => [
                    'type' => 'int',
                ],
                'group_id' => [
                    'type' => 'int',
                ],
        ];
    }
}