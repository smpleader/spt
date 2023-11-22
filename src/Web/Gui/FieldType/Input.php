<?php
/**
 * SPT software - Field type input
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Very basic class of input object
 * 
 */

namespace SPT\Web\Gui\FieldType;

class Input
{
    /**
     * Id, equal to html attribute "id"
     * 
     * @var string $id
     */
    public $id;

    /**
     * Name, equal to html attribute "name"
     * 
     * @var string $name
     */
    public $name;

    /**
     * Input type, equal to html attribute "type"
     * 
     * @var string $type
     */
    public $type;

    /**
     * Label, equal to html attribute "label"
     * 
     * @var string $label
     */
    public $label;

    /**
     * Input required or not, equal to html attribute "required"
     * 
     * @var string $required
     */
    public $required;

    /**
     * Default value if value not set
     * 
     * @var string $default
     */
    public $default; 

    /**
     * Placeholder, equal to html attribute "placeholder"
     * 
     * @var string $placeholder
     */
    public $placeholder; 

    /**
     * Value equal to html attribute "value"
     * 
     * @var string $value
     */
    public $value; 

    /**
     * Form class, equal to html attribute "class"
     * 
     * @var string $formClass
     */
    public $formClass; 

    /**
     * Autocomplete or not, equal to html attribute "autocomplete"
     * 
     * @var string $name
     */
    public $autocomplete;

    /**
     * Show label or not
     * 
     * @var string $showLabel
     */
    public $showLabel;

    /**
     * Add validators to auto check at frontend
     * 
     * @var string $validates
     */

    public $validates;

    /**
     * Support attached link
     * 
     * @var string $link
     */
    public $link;

    /**
     * Input could be an allias of other input
     * 
     * @var string $alias
     */
    public $alias;

    /**
     * Layout path or Layout name of this input
     * 
     * @var string $layout
     */
    public $layout;
    
    /**
     * Constructor
     * 
     * @return void 
     */ 
    public function __construct( $id, $params )
    {
        $this->id = $id;
        
        $this->type = isset( $params['type']) ? $params['type'] : 'text';
        $this->name = isset( $params['name']) ? $params['name'] : $this->id;
        $this->label = isset( $params['label']) ? $params['label'] : str_replace('_', ' ', ucfirst($this->id));
        $this->required = isset( $params['required']) ? $params['required'] : ''; 
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