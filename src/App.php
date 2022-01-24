<?php
/**
 * SPT software - Application Object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: An initialized application, support global data and session, token
 * 
 */

namespace SPT;

class App extends StaticObj
{
    protected static $_vars = array();
    protected static $_data = array();
    protected static $sessionTimeout = 15;

    public static function data($key = null, $value = null, $format = 0)
    {
        switch($key)
        {
            case null: return static::$_data;
            default:

                if( true === $format )
                {
                    static::$_data[$key] = $value;
                }
                elseif( is_string($format) )
                {
                    return Util::get($key, $format, static::$_data);
                }

                return isset(static::$_data[$key]) ? static::$_data[$key] : $value;
        }
    }

    /**
     * TODO: load Session from database
     */
    public static function session($key = null, $value = null, $format = 0)
    {
        if(session_id() == '' || !isset($_SESSION)) {
            // session isn't started
            @session_start();
        }
        
        switch($key)
        {
            case null: return $_SESSION;
            default:

                if( true === $format )
                {
                    $_SESSION[$key] = $value;
                }
                elseif( is_string($format) )
                {
                    return Util::get($key, $format, $_SESSION);
                }

                return isset($_SESSION[$key]) ? $_SESSION[$key] : $value;
        }
    }

    public static function token($param = null){

        if( is_null($param)){

            return static::session('token', null);

        } elseif ( $param == 'validPost' || $param == 'validGet'){

            $tmp = ( $param == 'validPost' ) ? $_POST : $_GET;
            $token = static::token(); 
            $passed = isset($tmp[$token])  && $tmp[$token] == 1;
            if( $passed ){
               return static::token('isAlive');
            }
            return false;
        
        } elseif ( $param == 'isAlive'){

            $expire = static::session('token_timeout', 0);
            $now = strtotime("now");
            return $expire > $now;

        } elseif ( is_array($param)){

            static::session('token', $param[0], true);
            static::token( (int)$param[1] );

        } elseif ( is_int($param)){

            $param += 60 * static::$sessionTimeout;
            static::session('token_timeout', $param, true);

        }
    }

    public static function execute($router){
        // placeholder
    }

    public static function setTimeout($int){
        static::$sessionTimeout = (int) $int;
    }
}
