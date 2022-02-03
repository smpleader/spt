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

class FileJson extends File
{
    public function parse(string $path)
    {
        $this->_data = json_decode(file_get_contents($path));
    }

    public function toFile(string $path)
    {
        file_put_contents($path, json_encode($this->_data));
    }
}