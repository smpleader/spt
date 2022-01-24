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

class String
{
    public static function uc( $word )
    {
        return ucfirst( strtolower($word) );
    }
}
