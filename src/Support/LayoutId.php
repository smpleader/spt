<?php
/**
 * SPT software - Layout ID
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Layout ID
 * 
 */

namespace SPT\Support;

class LayoutId
{ 
    // used in view
    public static function validateArray(array $input, string $plugin, string $theme): array
    {
        @list($ext, $type, $path) = $tmp;
        if(empty($path)) throw new \Exception('Invalid layout Id from an array');
        self::validateType($type);
        if(empty($ext)) $ext = 'theme' == $type ? $theme : $plugin;

        return [$ext, $type, $path];
    }

    // used in view
    public static function toArray(string $token, string $plugin, string $theme): array
    {
        $tmp = explode(':', $token);
        $count = count($tmp); 
        switch($count) 
        {
            case 1:
                $ext = $plugin;
                $type = 'layout';
                $path = $token;
                break;
            case 2:
                list($type, $path) = $tmp;
                self::validateType($type);
                $ext = 'theme' == $type ? $theme : $plugin;
                break;
            case 3:
                list($ext, $type, $path) = $tmp;
                self::validateType($type);
                if(empty($ext)) $ext = 'theme' == $type ? $theme : $plugin;
                break;
            default:
                throw new \Exception('Invalid path '. $token);
            break;
        }

        return [$ext, $type, $path];
    }

    // used in view
    private static function validateType(?string &$type): void
    {
        switch($type)
        {
            case null: 
            case '': 
            case 'l': $type = 'layout'; break;
            case 'w': $type = 'widget'; break;
            case 'v': $type = 'viewcom'; break;
            case 't': $type = 'theme'; break;
            case 'widget':
            case 'layout':
            case 'viewcom':
            case 'theme':
                break;
            default:
                throw new \Exception('Invalid layout type '.$type);
                break;
        }
    }

    // used in viewmodel
    public static function implode(array|string $sth, string $layout, string $plugin ='', string $theme=''): string
    {
        return is_array($sth) ? implode(':', $sth). ':'. $layout : $sth. ':'. $layout;
        /**
         * Important: we can't smart create a token because currentPlugin does not set while this VM get bootstrap
         * Other solution: let call registerLayouts when set View instance
         */

        $tmp = is_array($sth) ? $sth : explode(':', $sth);
        $counter = count($tmp);
        switch($counter)
        {
            case 0:
                $ext = $plugin;
                $type = 'layout';
                break;
            case 1:
                $type = $tmp[0];
                self::validateType($type);
                $ext = 'theme' == $type ? $theme : $plugin;
                break;
            case 2:
                list($ext, $type) = $tmp;
                self::validateType($type);
                if(empty($ext)) $ext = 'theme' == $type ? $theme : $plugin;
                break;
        }

        if($type == 'theme' && $ext == '*')
        {
            $ext = $theme;
        }

        return $ext. ':'. $type. ':'. $layout;
    }
}
