<?php
/**
 * SPT software - Input type which has value of array
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Support to display a list of value into one input
 * 
 */

namespace SPT\Web\Gui\FieldType;

class Collection extends Input
{
    /**
     * A character for separator
     * 
     * @var string $separator
     */
    public $separator;

    /**
     * Constructor
     * 
     * @return void 
     */ 
    public function __construct( $id, $params )
    {
        parent::__construct( $id, $params );

        // minor correct
        if('collection' == $this->type) $this->type = 'text-array';

        $this->separator = isset( $params['separator']) ? $params['separator'] : '|'; 
    }

}