<?php
/**
 * SPT software - Gui Field
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Easily display data object property
 * 
 */

namespace SPT\Web\Gui\FieldType;

class Collection extends Input
{
    public function __construct( $id, $params )
    {
        parent::__construct( $id, $params );

        // minor correct
        if('collection' == $this->type) $this->type = 'text-array';

        $this->separator = isset( $params['separator']) ? $params['separator'] : '|'; 
    }

}