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

class FileClass
{
    protected $data;
    protected $path;

    public function __construct(string $path, string $className)
    {
        $this->path = $path;
        include $path;
        $this->data = new $className;
    }

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

    public function print($data = null, $deep = 0)
    {
        if( null === $data ) $data = $this->data;

        if(count($data))
        {
            $tabParent =  str_repeat("\t", $deep);

            $str = "$tabParent\[\n";

            $tab =  str_repeat("\t", $deep+1);
            
            foreach($data as $key=>$value)
            {
                $str .= $tab;

                if(!is_numeric($key))
                {
                    $str .= "$tab'". $key. "' => ";
                }

                if(is_array($value))
                {
                    $str .= $this->print($value, $deep+1);
                }
                else
                {
                    $str .= "$tab'". str_replace("'", "\\'", $value);
                }

                $str .= "', \n";
            }

            $str .= "$tabParent\]";

            return $str;
        }
        else
        {
            return $deep > 0 ? '' : '[]';
        }
    }

    public function toFile()
    {
        file_put_contents($this->path, $this->print());
    }

    public function getPath()
    {
        return $this->path;
    }
}