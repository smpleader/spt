<?php
/**
 * SPT software - Storage Memory
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Storage Memory
 * 
 */

namespace SPT\Storage; 

class Memory
{
    private $data;

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
	{
		if (isset($this->data[$name]))
		{
			return $this->data[$name];
		}

        throw new \Exception('Unknown Storage Property '.$name);
	}
}