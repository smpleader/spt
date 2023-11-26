<?php
/**
 * SPT software - User Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: User just once time
 * Write this to easily imagine how the user should be
 * 
 */

namespace SPT\User;

use SPT\ConfigurableDI;

class Base extends ConfigurableDI
{
    /**
     * Attached Session
     * @var object $session
     */
    protected $session;

    /**
     * Attached data
     * @var mixed $data
     */
    protected $data;

    /**
     * Load user based options, this helps us change user instance according to user type ( joomla, laravel, symfony )
     *
     * @param array   $options  Add properties
     * 
     * @return void
     */ 
    public function init(array $options)
    {
        parent::init($options);

        $storage = $this->getContext();
        $this->data = (array) $this->session->get( $storage );
        
        if( empty($this->data) )
        {
            $this->data = $this->getDefault();
        }
    }

    /**
     * Get array mutable fields
     * 
     * @return array
     */
    public function getMutableFields(): array
    {
        return [
            'session' => '\SPT\Session\Instance'
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
            'groups' => ['guest'],
            'permission' => [],
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Check if user belong to a group
     * 
     * @return bool
     */
    public function is(string $group)
    {
        return is_array($this->data['groups']) ? in_array($group, $this->data['groups']) : false;
    }

    /**
     * Check if user has a permission
     * 
     * @return bool
     */
    public function can(string $permission)
    {
        return is_array($this->data['permission']) ? in_array($permission, $this->data['permission']) : false;
    }

    /**
     * Set value to private data data
     *
     * @param string   $key  Data key
     * @param mixed   $value  Data value
     * 
     * @return void
     */ 
    public function set(string $key, $value)
    {
        $this->data[$key] = $value;
        $storage = $this->getContext();
        $this->session->set($storage, $this->data);
    }

    /**
     * Get value by a data key
     *
     * @param string   $key  Data key
     * @param mixed   $default  Data default if not found
     * 
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * Reset data session
     * 
     * @return void
     */
    public function reset()
    {
        $this->data = $this->getDefault();
        $storage = $this->getContext();
        $this->session->set($storage, $this->data);
        $this->session->reload('guest', 0);
    }
}
