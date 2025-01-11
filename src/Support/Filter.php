<?php
/**
 * SPT software - Filter
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Alias to filter a variable type
 * 
 */

namespace SPT\Support;

class Filter
{
    public static function __callStatic($type, $arguments)
    {
        if(count($arguments) == 1)
        {
            $value = $arguments[0];
            $type = strtolower($type);
            switch($type){
                case 'int':
                case 'integer':
                    return (int) $value;
                case 'float':
                case 'double':
                    return (float) $value;
                case 'bool':
                case 'boolean':
                    return (bool) $value;
                case 'email':
                    return filter_var($value, FILTER_VALIDATE_EMAIL);
                case 'word':
                    return preg_replace('/[^A-Z_]/i', '', $value);
                case 'alnum':
                    return preg_replace('/[^A-Z0-9]/i', '', $value);
                case 'array':
                    return (array) $value;
                case 'cmd': // Allow a-z, 0-9, underscore, dot, dash. Also remove leading dots from result. 
                    return preg_replace('/[^A-Z0-9_\.-]/i', '', $value);
                case 'alias': // Allow a-z, 0-9, underscore, dot, dash. 
                    return preg_replace('/[^\w\-_\.]/i', '', $value);
                case 'title': // Allow a-z, 0-9, underscore, dot, dash, space. 
                    return preg_replace('/[^\w\-_\.\s]/i', '', $value);
                case 'base64': // Allow a-z, 0-9, slash, plus, equals.
                    return preg_replace('/[^A-Z0-9\/+=]/i', '', $value);
            default: // raw
                    return $value;
            }
        }
        elseif(count($arguments) > 1)
        {
            $result = [];
            foreach($arguments as $value)
            {
                $result[] = static::{$type}($value);
            }
            return $result;
        }
        
        return null;
    }
}
