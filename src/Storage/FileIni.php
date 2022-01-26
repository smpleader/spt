<?php
/**
 * SPT software - Storage File
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Storage File
 * 
 */

namespace SPT\Storage;

use SPT\Support\Filter;

class FileIni extends File
{
    public function __construct(string $path)
    {
        $this->path = $path;
        $raw = file_get_contents($path);

        $lines = explode("\n", $raw);
        foreach($lines as $line)
        {
            $tmp = explode('=', $line);
            $key = array_shift($tmp);
            $key = Filter::cmd($key);
            $this->data[$key] = implode('=', $tmp);
        }
    }

    public function toFile()
    {
        $str = [];

        foreach($this->data as $key => $value)
        {
            $str[] = $key. '='. $value;
        }

        file_put_contents($this->path, implode("\n", $str));
    }
}