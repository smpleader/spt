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
    protected $_data = [];
    protected $_paths = [];

    public function import(string $path)
    {
        if(!in_array($path, $this->_paths))
        {
            $this->_paths[] = $path;
        }

        $this->parse($path);
    }

    protected function parse(string $path)
    {
        $this->_data[] = file_get_contents($path);
    }

    public function toFile(string $path)
    {
        file_put_contents($path, $this->_data);
    }

    public function __set(string $name, $value): void
    {
        $this->_data[$name] = $value;
    }

    public function __get($name)
	{
		if (isset($this->_data[$name]))
		{
			return $this->_data[$name];
		}

        throw new \Exception('Unknown Storage '. get_called_class(). ' Property: '.$name);
	}

    public function exists($name)
    {
        return isset($this->_data[$name]);
    }

    public function getPaths()
    {
        return $this->_paths;
    }
}