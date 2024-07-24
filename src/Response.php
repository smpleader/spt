<?php
/**
 * SPT software - Response class
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Response content with singal http code
 * 
 */

namespace SPT;

class Response extends StaticObj
{
    /**
     * Internal array
     * @var array $_vars
     */
    static protected $_vars = array();

    /**
     * A general function to response a content with http code signal
     *
     * @param mixed   $content  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * @param integer $http_code Http code 
     * 
     * @return string content of response body
     */ 
    public static function _($content=false, int $http_code=200)
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
    }

    /**
     * Okie
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 200
     */ 
    public static function _200($msg='')
    {
        static::_($msg);
    }

    /**
     * Accepted
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 202
     */ 
    public static function _202($msg='')
    {
        static::_($msg, 202);
    }

    /**
     * No Content
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 204
     */ 
    public static function _204($msg='')
    {
        static::_($msg, 204);
    }

    /**
     * Moved Permanently
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 301
     */ 
    public static function _301($msg='')
    {
        static::_($msg, 301);
    }

    /**
     * Found
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 302
     */ 
    public static function _302($msg='')
    {
        static::_($msg, 302);
    }

    /**
     * See Other (since HTTP/1.1)
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 303
     */ 
    public static function _303($msg='')
    {
        static::_($msg, 303);
    }

    /**
     * Temporary Redirect (since HTTP/1.1)
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 307
     */ 
    public static function _307($msg='')
    {
        static::_($msg, 307);
    }

    /**
     * Permanent Redirect (RFC 7538)
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 308
     */ 
    public static function _308($msg='')
    {
        static::_($msg, 308);
    }

    /**
     * Bad Request
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 400
     */ 
    public static function _400($msg='')
    {
        static::_($msg, 400);
    }

    /**
     * unauthorised
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 401
     */ 
    public static function _401($msg='')
    {
        static::_($msg, 401);
    }

    /**
     * Payment Required
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 402
     */ 
    public static function _402($msg='')
    {
        static::_($msg, 402);
    }

    /**
     * Forbidden
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 403
     */ 
    public static function _403($msg='')
    {
        static::_($msg, 403);
    }

    /**
     * Not found
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 404
     */ 
    public static function _404($msg='')
    {
        static::_($msg, 404);
    }

    /**
     * Method Not Allowed
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 405
     */ 
    public static function _405($msg='')
    {
        static::_($msg, 405);
    }

    /**
     * Not Acceptable
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 406
     */ 
    public static function _406($msg='')
    {
        static::_($msg, 406);
    }

    /**
     * Proxy Authentication Required (RFC 7235)
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 407
     */ 
    public static function _407($msg='')
    {
        static::_($msg, 407);
    }

    /**
     * Request Timeout
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 408
     */ 
    public static function _408($msg='')
    {
        static::_($msg, 408);
    }

    /**
     * Conflict
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 409
     */ 
    public static function _409($msg='')
    {
        static::_($msg, 409);
    }

    /**
     * internal error
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 500
     */ 
    public static function _500($msg='')
    {
        static::_($msg, 500);
    }

    /**
     * Not Implemented
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 501
     */ 
    public static function _501($msg='')
    {
        static::_($msg, 501);
    }

    /**
     * Bad Gateway
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 502
     */ 
    public static function _502($msg='')
    {
        static::_($msg, 502);
    }

    /**
     * Service Unavailable
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 503
     */ 
    public static function _503($msg='')
    {
        static::_($msg, 503);
    }

    /**
     * Gateway Timeout
     *
     * @param mixed   $msg  Content could be array, object, or string. Others like bool, null, number will be treated wiht PHP default "echo"
     * 
     * @return string content of response body with http code 504
     */ 
    public static function _504($msg='')
    {
        static::_($msg, 504);
    }

    /**
     * Redirect response to an URL with http code
     *
     * @param string   $url  URL of redrection
     * 
     * @return void|string when header already set, use javascript for a redirection
     */ 
    public static function redirect(string $url, $redirect_status = 302)
    {
        if(headers_sent())
        {
            echo '<script>document.location.href="'. $url .'"</script>';
        }
        else
        {
            header('Location: '.$url, true, $redirect_status);
        }
    }
}
