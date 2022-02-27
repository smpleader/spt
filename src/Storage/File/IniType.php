<?php
/**
 * SPT software - Storage File
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: File Ini
 * 
 */

namespace SPT\Storage\File;

use SPT\Support\Filter;

class IniType extends Base
{
    public function parse(string $path)
    {
        $raw = file_get_contents($path);

        $lines = explode("\n", $raw);
        foreach($lines as $line)
        {
            $tmp = explode('=', $line);
            $key = array_shift($tmp);
            $key = Filter::cmd($key);
            $this->_data[$key] = implode('=', $tmp);
        }
    }

    public function toFile(string $path)
    {
        $str = [];

        foreach($this->_data as $key => $value)
        {
            $str[] = $key. '='. $value;
        }

        file_put_contents($path, implode("\n", $str));
    }
}