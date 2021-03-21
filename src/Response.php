<?php
/**
 * SPT software - Response
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A way to response the package
 * 
 */

namespace SPT;

class Response extends StaticObj
{
    static protected $_vars = array();

    public static function _($content=false, $http_code='200')
    {
        http_response_code($http_code);

        if(is_array($content) || is_object($content))
        {
            header('Content-Type: application/json');
            echo json_encode($content);
        }
        elseif($content!==false)
        {
            echo $content;
        }
        
        exit(0);
    }

    // Okie
    public static function _200($msg='')
    {
        self::_($msg);
    }

    // Accepted
    public static function _202($msg='')
    {
        self::_($msg, '202');
    }

    // No Content
    public static function _204($msg='')
    {
        self::_($msg, '204');
    }

    // Moved Permanently
    public static function _301($msg='')
    {
        self::_($msg, '301');
    }

    // Found
    public static function _302($msg='')
    {
        self::_($msg, '302');
    }

    // See Other (since HTTP/1.1)
    public static function _303($msg='')
    {
        self::_($msg, '303');
    }

    // Temporary Redirect (since HTTP/1.1)
    public static function _307($msg='')
    {
        self::_($msg, '307');
    }

    // Permanent Redirect (RFC 7538)
    public static function _308($msg='')
    {
        self::_($msg, '308');
    }

    // Bad Request
    public static function _400($msg='')
    {
        self::_($msg, '400');
    }

    // unauthorised
    public static function _401($msg='')
    {
        self::_($msg, '401');
    }

    // Payment Required
    public static function _402($msg='')
    {
        self::_($msg, '402');
    }

    // Forbidden
    public static function _403($msg='')
    {
        self::_($msg, '403');
    }

    // Not found
    public static function _404($msg='')
    {
        self::_($msg, '404');
    }

    // Method Not Allowed
    public static function _405($msg='')
    {
        self::_($msg, '405');
    }

    // Not Acceptable
    public static function _406($msg='')
    {
        self::_($msg, '406');
    }

    // Proxy Authentication Required (RFC 7235)
    public static function _407($msg='')
    {
        self::_($msg, '407');
    }

    // Request Timeout
    public static function _408($msg='')
    {
        self::_($msg, '408');
    }

    //  Conflict
    public static function _409($msg='')
    {
        self::_($msg, '409');
    }

    // internal error
    public static function _500($msg='')
    {
        self::_($msg, '500');
    }

    // Not Implemented
    public static function _501($msg='')
    {
        self::_($msg, '501');
    }

    // Bad Gateway
    public static function _502($msg='')
    {
        self::_($msg, '502');
    }

    // Service Unavailable
    public static function _503($msg='')
    {
        self::_($msg, '503');
    }

    // Gateway Timeout
    public static function _504($msg='')
    {
        self::_($msg, '504');
    }

    public static function redirect($url)
    {
        if(headers_sent())
        {
            echo '<script>document.location.href="'. $url .'"</script>';
        }
        else
        {
            header('Location: '.$url);
        }
        exit(0);
    }
}
