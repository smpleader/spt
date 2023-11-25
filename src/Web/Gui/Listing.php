<?php
/**
 * SPT software - GUI listing
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Class support to generate and work with a listing 
 * 
 */

namespace SPT\Web\Gui;

class Listing
{ 
    use \SPT\Traits\Index;

    /**
    * Internal variable to cache the list of data array
    *
    * @var array $items
    */
    protected $items;

    /**
    * Internal variable to cache the total of query result
    *
    * @var int $total
    */
    protected $total;

    /**
    * Internal variable to cache the total of page
    *
    * @var int $totalPage
    */
    protected $totalPage;

    /**
    * Internal variable to cache the limit of current query
    *
    * @var int $totalPage
    */
    protected $limit;  

    /**
    * Internal variable information of columns, support to generate a column
    *   this be supported by ViewComponent ( ViewLayout->ui )
    *   with support of: link / sortable/ icons
    *   
    * @var array $columns
    */
    protected $columns;

    /**
    * Internal variable to cache current data row
    *
    * @var object|array $row
    */
    protected $row;
    
    /**
     * Constructor
     * 
     * @return void 
     */ 
    public function __construct(array $items, int $total, int $limit, array $columns )
    {
        $this->items = $items;
        $this->total = $total;
        $this->limit = $limit;
        $this->index = 0;

        foreach($columns as $id => $field)
        {
            $tmp = is_string($field) ? ['label' => $field] : $field;
            $this->columns[$id] = $tmp;
        }
    }

    /**
     * Get columns information
     * 
     * @return array 
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get total of a query 
     * 
     * @return int 
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get limit of a query  
     * 
     * @return int 
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get total page 
     * 
     * @return int 
     */
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

    /**
     * Check if next record exists
     * 
     * @return bool 
     */
    public function hasRow()
    {
        return isset($this->items[$this->index]);
    }

    /**
     * Get row
     * 
     * @return object|array 
     */
    public function getRow()
    {
        $row = $this->items[$this->index];
        $this->index++;
        return $row;
    }
}