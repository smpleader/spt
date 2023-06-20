<?php
/**
 * SPT software - Enitity has status
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: how we toggle the status by id
 * 
 */

namespace SPT\Traits;

trait EntityHasStatus
{
    public function toggleStatus( $id, $action = null)
    {
        if(null === $action)
        {
            $item = $this->findByPK($id);
            $status = !$item['status'];
        }
        else
        {
            $status = $action;
        }

        return $this->db->table( $this->table )->update([
            'status' => $status,
        ], ['id' => $id ]);
    }
}