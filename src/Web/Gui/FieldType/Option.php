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

class Option extends Input
{
    public $options;
    public $optionSrc;
    public $emptyOption;
    
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