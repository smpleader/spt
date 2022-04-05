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
use SPT\Dispatcher;
use SPT\Traits\ErrorArray; 

class DipatchableEntity extends Entity
{   
    protected $prefix = '';
    protected $affix = '';

    public function add($data, $where = [])
    {
        $newId = parent::add($data);

        if ($newId)
        {
            // TODO add rollback
            Dispatcher::fire('afterNew'. $this->affix, static::class, $data, $newId);
        }

        return $newId;
    }

    public function update($data, $where = [])
    {
        $result = parent::update($data, $where);

        if ($result)
        {
            // TODO add rollback
            Dispatcher::fire('afterUpdate'. $this->affix, static::class, $data);
        }

        return $result;
    }

    public function remove($id)
    {
        $result = parent::remove($id);

        if ($result)
        {
            // TODO add rollback
            Dispatcher::fire('afterRemove'. $this->affix, static::class, $id);
        }

        return $result;
    }
}