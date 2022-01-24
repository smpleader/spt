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

use SPT\Log;

class Json
{ 
    public static function decode($string, $asArray=false)
    {
        $body = @json_decode($string, $asArray);

        if(json_last_error() !== JSON_ERROR_NONE)
        {
            $err = '';
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $err = ' - No errors';
                break;
                case JSON_ERROR_DEPTH:
                    $err = ' - Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    $err = ' - Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    $err = ' - Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    $err = ' - Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    $err = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                default:
                    $err = ' - Unknown error';
                break;
            }
            // TODO: block bad IP
            // TODO: log this issue 
            Log::add($err);
            return '';
        }
        return $body;
    }
}
