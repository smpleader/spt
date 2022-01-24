<?php
/**
 * SPT software - Json
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Json to simplify the jobs
 * 
 */

namespace SPT\Support;

class Filter
{
    public static function __callStatic($type, $arguments)
    {
        $type = strtolower($type);
        switch($type){
            case 'int':
            case 'integer':
                return (int) $arguments;
            case 'float':
            case 'double':
                return (float) $arguments;
            case 'bool':
            case 'boolean':
                return (bool) $arguments;
            case 'email':
                return filter_var($arguments, FILTER_VALIDATE_EMAIL);
            case 'word':
                return preg_replace('/[^A-Z_]/i', '', $arguments);
            case 'alnum':
                return preg_replace('/[^A-Z0-9]/i', '', $arguments);
            case 'array':
                return (array) $arguments;
            case 'cmd': // Allow a-z, 0-9, underscore, dot, dash. Also remove leading dots from result. 
                return preg_replace('/[^A-Z0-9_\.-]/i', '', $arguments);
            case 'base64': // Allow a-z, 0-9, slash, plus, equals.
                return preg_replace('/[^A-Z0-9\/+=]/i', '', $arguments);
           default: // raw
                return $arguments;
    }
}
