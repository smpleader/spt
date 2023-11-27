<?php
/**
 * SPT software - Basic User 
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Basic User for application using SPT
 * 
 */

namespace SPT\User\SPT;

use SPT\App\Instance as AppIns;
use SPT\User\Base;

class User extends Base
{
    /**
     * Attached Entity to support work with db
     * @var \SPT\Storage\DB\Entity $entity
     */
    protected $entity;

    /**
     * Get mutaable fields
     * 
     * @return array
     */ 
    public function getMutableFields(): array
    {
        return [
            'session' => '\SPT\Session\Instance',
            'entity' => '\SPT\Storage\DB\Entity'
        ];
    }

    /**
     * Get array of default value
     * 
     * @return array
     */
    public function getDefault()
    {
        return [
            'id' => 0,
            'username' => 'guest',
            'fullname' => 'A Guest',
            'email' => '',
            'groups' => [],
            'permission' => [],
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Login with supplied username and password
     * TODO apply middleware or authentication
     *
     * @param string   $username 
     * @param string   $password 
     * 
     * @return bool|User
     */ 
    public function login(string $username, string $password)
    {
        $user = $this->entity->findOne(['username' => $username, 'password' => md5($password)]);
        if ($user)
        {
            unset($user['password']);
            $this->session->reload($user['username'], $user['id']);
            $this->data = $user;

            $storage = $this->getContext();
            $this->session->set($storage, $this->data); 
            return $user;
        }
            
        return false;
    }

    /**
     * Logout by a reset
     * 
     * @return bool
     */ 
    public function logout()
    {
        if ( $this->get('id') )
        {
            $this->reset();
        }

        return true;
    }

    /**
     * Get User group by user id
     *
     * @param int   $user_id 
     * 
     * @return array
     */ 
    public function getGroups(int $user_id = 0)
    {
        if(empty($user_id))
        {
            if(is_array($this->data['group']))
            {
                return $this->data['groups'];
            }

            $uid = $this->get('id');
        }
        else
        {
            $uid = $user_id;
        }

        list($groups, $permissions) = $this->entity->getGroupInfo($uid);
        if(empty($user_id))
        {
            $this->set('groups', $groups);
            $this->set('permissions', $permissions);
        }

        return $groups;
    }

    /**
     * Get User permmission by user id
     *
     * @param int   $user_id 
     * 
     * @return array
     */
    public function getPermissions(int $user_id = 0)
    {
        if(empty($user_id))
        {
            if(is_array($this->data['permissions']))
            {
                return $this->data['permissions'];
            }

            $uid = $this->get('id');
        }
        else
        {
            $uid = $user_id;
        }

        list($groups, $permissions) = $this->entity->getGroupInfo($uid);
        if(empty($user_id))
        {
            $this->set('groups', $groups);
            $this->set('permissions', $permissions);
        }

        return $permissions;
    }
}
