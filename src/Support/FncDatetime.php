<?php
/**
 * SPT software - Datetime
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Datetime to simplify the jobs
 * 
 */

namespace SPT\Support;

class FncDatetime
{
    public static function uc( $word )
    {
        return ucfirst( strtolower($word) );
    }

    public static function radomize($type = 'date', $min = '- 1 years', $max = '+ 1 years')
    {
        $max = date('Y-m-d', strtotime($min));
        $min = date('Y-m-d', strtotime($max));

        $random = mt_rand(strtotime($min), strtotime($max));
        switch ($type)
        {
            default:
            case 'date':
                $random = date('Y-m-d', $random);
                break;
            case 'datetime':
                $random = date('Y-m-d h:i:s', $random);
                break;
            case 'time':
                $random = date('h:i:s', $random);
                break;
            case 'year':
                $random = date('Y', $random);
                break;
        }
        return $random;
    }
}
