<?php
/**
 * SPT software - Gui
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Easily display data object
 * 
 */

namespace SPT\Web\Gui;

class Listing
{ 
    use \SPT\Traits\Index;
    
    protected $items;
    protected $total;
    protected $totalPage;
    protected $limit;  
    protected $columns;
    protected $row;

    public function __construct(array $items, int $total, int $limit, array $columns )
    {
        $this->items = $items;
        $this->total = $total;
        $this->limit = $limit;
        $this->index = 0;

        foreach($columns as $id => $field)
        {
            if(is_string($field)) $field = ['label' => $field];   
            $this->columns[$id] = new Column($id, $field);
        }
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getTotalPage()
    {
        if( null === $this->totalPage)
        {
            if( 0 == $this->total ) $this->totalPage = 1;
            else
            {
                $remain = $this->total % $this->limit;
                $this->totalPage = ( $this->total - $remain ) / $this->limit;
                
                if( $remain > 0 ) $this->totalPage++;
            } 

        }     
        return $this->totalPage;
    }

    public function hasRow()
    {
        return isset($this->items[$this->index]);
    }

    public function getRow()
    {
        $row = $this->items[$this->index];
        $this->index++;
        return $row;
    }
}