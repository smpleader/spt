<?php
/**
 * SPT software - Gui
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Easily display data object
 * 
 */

namespace SPT\View\Gui;

class Form
{
    use \SPT\Trait\Index;

    protected $record; 
    protected $fields;
    protected $fieldIds;

    public function __construct(array $fields, array $record = [] )
    {
        foreach($fields as $id => $field)
        {
            $className = '\SPT\View\Gui\FieldType\Input';
            $type = 'text';
            
            if(isset($field[0]))
            {
                if(false === strpos($field[0], '\\'))
                {
                    $type = $field[0];
                    $tmp = '\SPT\View\Gui\FieldType\\'. ucfirst($field[0]);
                }
                else
                {
                    $tmp = $field[0];
                }
                if(class_exists($tmp)) $className = $tmp;
            }

            $default = isset($field['default']) ? $field['default'] : NULL;
            $field['value'] = isset($record[$id]) ? $record[$id] : $default;
            $field['type'] = isset($field['type']) ? $field['type'] : $type;

            $this->fields[$id] = new $className($id, $field);
            $this->fieldIds[] = $id;
        }
        $this->record = $record;
        $this->index = 0;
    }

    public function getData()
    {
        return $this->record;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getfieldIds()
    {
        return $this->fieldIds;
    }

    public function hasField()
    {
        return isset( $this->fieldIds[$this->index] );
    }

    public function getField($key = null)
    {
        if( null === $key )
        {
            $key = $this->fieldIds[$this->index];
            $this->index++;
        }

        return isset($this->fields[$key]) ? $this->fields[$key] : false;
    }
}