<?php
/**
 * SPT software - Number
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Number to simplify the jobs
 * 
 */

namespace SPT\Support;

class FncNumber
{
    public static function radomize($type, $absolute = false, $min = 0, $max = 1000000000)
    {
        switch ($type)
        {
            default:
            case 'decimal';
            case 'float';
            case 'double';
            case 'int':
                if ($absolute)
                {
                    $min = 0;
                    $max = 4294967295;
                }
                else
                {
                    $min = -2147483648;
                    $max = 2147483648;
                }
                break;
            case 'tinyint':
                if ($absolute)
                {
                    $min = 0;
                    $max = 255;
                }
                else
                {
                    $min = -128;
                    $max = 127;
                }
                break;
            case 'smailint':
                if ($absolute)
                {
                    $min = 0;
                    $max = 65535;
                }
                else
                {
                    $min = -32768;
                    $max = 32767;
                }
                break;
        }
        return mt_rand($min, $max);
    }
}
