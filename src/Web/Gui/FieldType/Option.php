<?php
/**
 * SPT software - Input type which is a collection ( of options )
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An input for list, radio, checkbox
 * 
 */

namespace SPT\Web\Gui\FieldType;

class Option extends Input
{
    /**
     * List of input options
     * 
     * @var array $options
     */
    public $options;

    /**
     * Support get options from a query or class
     * 
     * @var mixed $optionSrc
     */    
    public $optionSrc;

    /**
     * How it be when option not set
     * 
     * @var mixed $emptyOption
     */    
    public $emptyOption;
    
    /**
     * Constructor
     * 
     * @return void 
     */ 
    public function __construct( $id, $params )
    {
        parent::__construct( $id, $params );

        // minor correct
        if('option' == $this->type) $this->type = 'select';

        $this->options = isset( $params['options']) ? $params['options'] : [];
        $this->optionSrc = isset( $params['optionSrc']) ? $params['optionSrc'] : false;
        $this->emptyOption = isset( $params['emptyOption']) ? $params['emptyOption'] : false;
    }
}