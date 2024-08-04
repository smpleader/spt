<?php
/**
 * SPT software - GUI form
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Class support to generate and work with a form 
 * 
 */

namespace SPT\Web\Gui;

class Form
{
    use \SPT\Traits\Index;

    /**
     * Current data to bind into the form
     * 
     * @var array|object $record
     */
    protected $record;

    /**
     * Array list of form field
     * 
     * @var array $fields
     */
    protected array $fields;

    /**
     * Array list of field name
     * 
     * @var array $fieldIds
     */
    protected array $fieldIds;

    /**
     * Token for field name
     * 
     * @var string $prefix
     */
    protected string $prefix;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct(array $fields, array $record = [], string $prefix = '')
    {
        $parent = '';
        $this->prefix = $prefix;
        if($prefix && strpos($prefix, '[') === 0)
        {
            $parent = substr($prefix, 1, -1);
            $prefix = '';
        } 

        foreach($fields as $id => $field)
        {
            $className = '\SPT\Web\Gui\FieldType\Input';
            $type = 'text';
            
            if(isset($field[0]))
            {
                if(false === strpos($field[0], '\\'))
                {
                    $type = $field[0];
                    $tmp = '\SPT\Web\Gui\FieldType\\'. ucfirst($field[0]);
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
            $field_id = $id;

            if($parent)
            {
                $field_id = $parent. '_'. $id;
                $field['name'] = $parent. '['. $id. ']';
            }
            elseif($prefix)
            {
                $field_id = $prefix. '_'. $id;
                $field['name'] = $prefix. '_'. $id;
            }

            $this->fields[$id] = new $className($field_id, $field);
            $this->fieldIds[] = $id;
        }
        $this->record = $record;
        $this->index = 0;
    }

    /**
     * Get data which attached to form 
     * 
     * @return array|object
     */
    public function getData()
    {
        return $this->record;
    }

    /**
     * Get list of fields
     * 
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get list of field id
     * 
     * @return array
     */
    public function getfieldIds()
    {
        return $this->fieldIds;
    }

    /**
     * Check if field exists in a loop call
     * 
     * @return bool
     */
    public function hasField()
    {
        return isset( $this->fieldIds[$this->index] );
    }

    /**
     * Get field object by an id, return false if not found
     * 
     * @return bool|object
     */
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