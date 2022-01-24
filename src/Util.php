<?php
/**
 * SPT software - Array
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Array to simplify the jobs
 * 
 */

namespace SPT;

class Util
{
    public static function get($var, $type='', $from='get'){

        if(is_string($from)){
            switch($from){
                case 'post':
                case 'POST':
                    $from = $_POST;
                    break;
                case 'get':
                case 'GET':
                    $from = $_GET;
                    break;
                case 'server':
                case 'SERVER':
                    $from = $_SERVER;
                    break;
                case 'session':
                case 'SESSION':
                    $from = $_SESSION;
                    break;
                default:
                    $find = static::get($var, $type, 'post');
                    return $find === null ? static::get($var, $type, 'get') : $find;
                    break;
            }
        }
        elseif(is_object($from))
        {
            $from = (array)$from;
        }

        if( !isset($from[$var]) ) return null;

        $type = strtolower($type);
        switch($type){
            case 'int':
            case 'integer':
                return (int) $from[$var];
            case 'float':
            case 'double':
                return (float) $from[$var];
            case 'bool':
            case 'boolean':
                return (bool) $from[$var];
            case 'email':
                return filter_var($from[$var], FILTER_VALIDATE_EMAIL);
            case 'word':
                return preg_replace('/[^A-Z_]/i', '', $from[$var]);
            case 'alnum':
                return preg_replace('/[^A-Z0-9]/i', '', $from[$var]);
            case 'array':
                return (array) $from[$var];
            case 'cmd': // Allow a-z, 0-9, underscore, dot, dash. Also remove leading dots from result. 
                return preg_replace('/[^A-Z0-9_\.-]/i', '', $from[$var]);
            case 'base64': // Allow a-z, 0-9, slash, plus, equals.
                return preg_replace('/[^A-Z0-9\/+=]/i', '', $from[$var]);
           default: // raw
                return $from[$var];
        }
    }

    public static function getClientIp() {
        $ipaddress = '';
        if (getenv('X-Real-IP'))
            $ipaddress = getenv('X-Real-IP');
        else if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public static function input($key, $default = '', $filter = '')
    {
        $type = '';
        $storage = 'get';

        if($filter)
        {
            foreach([
                'type' => ['int', 'integer', 'float', 'double', 'bool', 'boolean', 'email','word', 'alnum', 'array', 'cmd', 'base64'],
                'storage' => ['get','post', 'any']
            ] as $var => $val )
            {
                foreach($val as $v)
                {
                    if(false !== stripos( $filter, $v ))
                    {
                        ${$var} = $v;
                        break;
                    }
                }
            }
        }

        $try = static::get( $key, $type, $storage );
        return null === $try ? $default : $try;
    }
}
