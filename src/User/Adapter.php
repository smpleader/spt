<?php
/**
 * SPT software - User Adapter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: User Adapter
 * 
 */

namespace SPT\User;
use SPT\Session\Instance as Session;

interface Adapter
{
    public function init(Session $session);
    public function id(string $scope);
    public function get(string $key);
    public function can(string $permission_key);
}
