<?php
/**
 * SPT software - Gui Field
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Easily display data object property
 * 
 */

namespace SPT\View\Gui\FieldType;

class Input
{
    public $id;
    public $name;
    public $type;
    public $label;
    public $required;
    
    public $default; 
    public $placeholder; 
    public $value; 
    public $formClass; 
    public $autocompleteOff;
    public $showLabel;

    public $validates;
    public $autocomplete;
    public $link;
    public $alias;
    public $layout;

    public function __construct( $id, $params )
    {
        $this->id = $id;
        
        $this->type = isset( $params['type']) ? $params['type'] : 'text';
        $this->name = isset( $params['name']) ? $params['name'] : $this->id;
        $this->label = isset( $params['label']) ? $params['label'] : str_replace('_', ' ', ucfirst($this->id));
        $this->required = isset( $params['required']) ? $params['required'] : ''; 
        //$this->isPK = isset( $params['isPK']) ? $params['isPK'] : false;
        $this->value = isset( $params['value']) ? $params['value'] : '';
        $this->validates = isset( $params['validates']) ? $params['validates'] : '';
        $this->formClass = isset( $params['formClass']) ? $params['formClass'] : '';
        $this->autocomplete = isset( $params['autocomplete']) ? 'autocomplete="'. $params['autocomplete']. '"' : '';
        $this->link = isset( $params['link']) ? $params['link'] : false;
        $this->alias = isset( $params['alias']) ? $params['alias'] : false;
        $this->placeholder = isset( $params['placeholder']) ? 'placeholder="'. $params['placeholder']. '"' : '';
        $this->showLabel = isset( $params['showLabel']) ? $params['showLabel'] : true;
        $this->layout = isset( $params['layout']) ? $params['layout'] : false;
    }
}