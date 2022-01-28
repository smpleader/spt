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

class File
{
    protected $data;
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->data = file_get_contents($path);
    }

    public function toFile()
    {
        file_put_contents($this->path, $this->data);
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
	{
		if (isset($this->data[$name]))
		{
			return $this->data[$name];
		}

        throw new \Exception('Unknown Storage '. get_called_class(). ' Property: '.$name);
	}

    public function exists($name)
    {
        return isset($this->data[$name]);
    }

    public function getPath()
    {
        return $this->path;
    }
}