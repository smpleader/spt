<?php
/**
 * SPT software - Storage File
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Json File
 * 
 */

namespace SPT\Storage\File;

class JsonType extends Base
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