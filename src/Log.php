<?php
/**
 * SPT software - Log class
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Class support to log information for a debug
 * 
 */

namespace SPT;

class Log extends StaticObj
{
    /**
     * Internal array
     * @var array $_vars
     */
    static protected $_vars = array();

    /**
     * Pass any parameters into internal _vars
     *
     * @param mixed   Hidden parameters, add anything we want to log
     * 
     * @return void
     */ 
    public static function add(){
        $arg_list = func_get_args();
        foreach($arg_list as $arg){
            static::$_vars[] = $arg;
        }
    }

    /**
     * Print all items from _vars
     * 
     * @return void
     */ 
    public static function show(){
        foreach( static::$_vars as $item ){
            print_r( $item );
            echo "\n";
        }
    }

    /**
     * Write all items from _vars into a log file
     * 
     * @return void
     */ 
    public static function toFile($name = null, $append = true){

        ob_start();
        static::show();
        $content = ob_get_clean();

        if( $content ){
            $content = 
                ">> START LOG ". date('Y-m-d H:i:s') . " << \n". 
                $content . 
                "\n--- LOGGED AT ".date('Y-m-d H:i:s')." ---\n";
    
            if( $name === null ) $name = date('Y-m-d_His').'.log';
    
            if( $append ) {
                file_put_contents($name, $content, FILE_APPEND);
            } else {
                file_put_contents($name, $content);
            }
        }
    }
}
