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

class UserEntity extends Entity
{
    /**
     * Table name
     * @var string $table
     */
    protected $table = '#__users';
    
    /**
     * PK name
     * @var string $pk
     */
    protected $pk = 'id';

    /**
     * Get array of fields
     * 
     * @return array
     */
    public function getFields()
    {
        return [
                'id' => [
                    'type' => 'int',
                    'pk' => 1,
                    'option' => 'unsigned',
                    'extra' => 'auto_increment',
                ],
                'name' => [
                    'type' => 'varchar',
                    'limit' => 100,
                ],
                'username' => [
                    'type' => 'varchar',
                    'limit' => 100,
                ],
                'password' => [
                    // 'validate' => ['md5'],
                    'type' => 'varchar',
                    'limit' => 255,
                ],
                'email' => [
                    'type' => 'varchar',
                    'limit' => 255,
                ],
                'status' => [
                    'type' => 'tinyint',
                ],
                'avatar' => [
                    'type' => 'text'
                ],
                'created_at' => [
                    'type' => 'datetime',
                    'default_value' => '0000-00-00 00:00:00',
                ],
                'created_by' => [
                    'type' => 'int',
                    'option' => 'unsigned',
                ],
                'modified_at' => [
                    'type' => 'datetime',
                    'default_value' => '0000-00-00 00:00:00',
                ],
                'modified_by' => [
                    'type' => 'int',
                    'option' => 'unsigned',
                ],
        ];
    }

    public function getGroupInfo(int $user_id)
    {
        $list = $this->db->select( 'usermap.user_id, usergroup.name as group_name, usergroup.id as group_id, usergroup.access' )
                        ->table( '#__user_usergroup_map as usermap' )
                        ->join( 'LEFT JOIN #__user_groups as usergroup ON usergroup.id = usermap.group_id ')
                        ->where(['usermap.user_id = ' .$user_id]);

        $arr = $list->list(0, 0);
        $groups = [];
        $permission = [];
        foreach($arr as $row)
        {
            $groups[ $row['group_id'] ] = $row['group_name'];
            $permission = array_merge($permission, json_decode($row['access'], true));
        }

        return [$groups, $permission];
    }
}