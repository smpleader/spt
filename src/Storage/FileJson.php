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

class FileArray extends File
{
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->data = json_decode(file_get_contents($path));
    }

    public function toFile()
    {
        file_put_contents($this->path, json_encode($this->data));
    }
}