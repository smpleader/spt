<?php
/**
 * SPT software - Gui Field
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Easily display data object property
 * 
 */

namespace SPT\View\Gui;

class Column
{
    public $id;
    public $name;
    public $label; 
    public $sort;
    public $search;
    public $filter; 
    public $listClass; 

    public function __construct( $id, $params )
    {
        $this->id = $id;
        
        $this->name = isset( $params['name']) ? $params['name'] : $this->id;
        $this->label = isset( $params['label']) ? $params['label'] : str_replace('_', ' ', ucfirst($this->id)); 
        //$this->isPK = isset( $params['isPK']) ? $params['isPK'] : false;
        // listing
        $this->listClass = isset( $params['listClass']) ? $params['listClass'] : '';
        $this->sort = isset( $params['sort']) ? $params['sort'] : false;
        $this->search = isset( $params['search']) ? $params['search'] : false;
        $this->filter = isset( $params['filter']) ? $params['filter'] : false; 
        $this->link = isset( $params['link']) ? $params['link'] : false;
        $this->alias = isset( $params['alias']) ? $params['alias'] : false; 
    } 

    // use for listing
    public function generateCell( $row )
    {
        $_output = '';
        if($this->link)
        {
            $_output .= '<a href="'. $this->link. '" >';
        }

        if( $this->alias )
        {
            if( is_string($this->alias))
            {
                $key = $this->alias;
                $_output .= $row->{$key}; 
            }
            /*elseif( is_array($this->alias))
            {
                list($name, $fnc) = $this->alias;
                $model = $this->getModel( $name ); // TODO consider use function from field
                $_output .= $model->$fnc( $row, $this->id );
            }*/
        }
        else
        {
            $key = $this->id;
            $_output .= $row->{$key};  
        }

        if($this->link && $link)
        {
            $_output .= '</a>';
        }

        return $_output;
    }
}