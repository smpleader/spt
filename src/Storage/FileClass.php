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
    protected $_data;
    protected $_paths;

    public function import(string $path, string $className)
    {
        $this->_paths[] = $path;
        try
        {
            include $path;
            $sth = new $className;
            foreach($sth as $key => $value)
            {
                $this->_data->{$key} = $value;
            }
        }
        catch (Exception $e) 
        {
            $this->response('Caught \Exception: '.  $e->getMessage(), 500);
        }
    }

    public function __set(string $name, mixed $value): void
    {
        $this->_data[$name] = $value;
    }

    public function __get($name)
	{
		if (isset($this->_data[$name]))
		{
			return $this->_data[$name];
		}

        throw new \Exception('Unknown Storage Property '.$name);
	}

    public function print($data = null, $deep = 0)
    {
        if( null === $data ) $data = $this->_data;

        if(count($data))
        {
            $tabParent =  str_repeat("\t", $deep);

            $str = "$tabParent\{\n";

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

            $str .= "$tabParent\}";

            return $str;
        }
        else
        {
            return $deep > 0 ? '' : '[]';
        }
    }

    public function toFile(string $path)
    {
        file_put_contents($path, $this->print());
    }

    public function getPaths()
    {
        return $this->_paths;
    }
}