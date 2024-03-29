<?php
/**
 * SPT software - String
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with String to simplify the jobs
 * 
 */

namespace SPT\Support;

class FncString
{
    public static function uc( $word )
    {
        return ucfirst( strtolower($word) );
    }

    public static function radomize(int $limit = 50)
    {
        $random = '';
        $arr = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 

        $length = rand(0, $limit);

        for($i=0; $i < $length; $i++) 
        {
            $random .= $arr[mt_rand(0, 61)];
        }

        return $random;
    }
 
}
